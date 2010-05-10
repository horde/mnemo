<?php
/**
 * $Horde: mnemo/memo.php,v 1.71 2009/12/01 04:56:31 chuck Exp $
 *
 * Copyright 2001-2009 The Horde Project (http://www.horde.org/)
 *
 * See the enclosed file LICENSE for license information (ASL). If you
 * did not receive this file, see http://www.horde.org/licenses/asl.php.
 *
 * @author  Chuck Hagenbuch <chuck@horde.org>
 * @since   Mnemo 1.0
 * @package Mnemo
 */

 /**
  * Encryption tests.
  */
function showPassphrase($memo)
{
    global $notification;

    if (!is_a($memo['body'], 'PEAR_Error')) {
        return false;
    }

    /* Check for secure connection. */
    $secure_check = Horde::isConnectionSecure();

    if ($memo['body']->getCode() == MNEMO_ERR_NO_PASSPHRASE) {
        if ($secure_check) {
            $notification->push(_("This note has been encrypted, please provide the password below"), 'horde.message');
            return true;
        }
        $notification->push(_("This note has been encrypted, and cannot be decrypted without a secure web connection"), 'horde.error');
        $memo['body'] = '';
        return false;
    }

    if ($memo['body']->getCode() == MNEMO_ERR_DECRYPT) {
        if ($secure_check) {
            $notification->push(_("This note cannot be decrypted:") . ' ' . $memo['body']->getMessage(), 'horde.message');
            return true;
        }
        $notification->push(_("This note has been encrypted, and cannot be decrypted without a secure web connection"), 'horde.error');
        $memo['body'] = '';
        return false;
    }

    $notification->push($memo['body'], 'horde.error');
    $memo['body'] = '';

    return false;
}

@define('MNEMO_BASE', dirname(__FILE__));
require_once MNEMO_BASE . '/lib/Application.php';
Horde_Registry::appInit('mnemo');

/* Redirect to the notepad view if no action has been requested. */
$memo_id = Horde_Util::getFormData('memo');
$memolist_id = Horde_Util::getFormData('memolist');
$actionID = Horde_Util::getFormData('actionID');
if (is_null($actionID)) {
    header('Location: ' . Horde::applicationUrl('list.php', true));
    exit;
}

/* Load category manager. */
$cManager = new Horde_Prefs_CategoryManager();

/* Run through the action handlers. */
switch ($actionID) {
case 'add_memo':
    /* Check permissions. */
    if ($GLOBALS['injector']->getInstance('Horde_Perms')->hasAppPermission('max_notes') !== true &&
        $GLOBALS['injector']->getInstance('Horde_Perms')->hasAppPermission('max_notes') <= Mnemo::countMemos()) {
        $message = @htmlspecialchars(sprintf(_("You are not allowed to create more than %d notes."), Mnemo::hasPermission('max_notes')), ENT_COMPAT, Horde_Nls::getCharset());
        if (!empty($conf['hooks']['permsdenied'])) {
            $message = Horde::callHook('_perms_hook_denied', array('mnemo:max_notes'), 'horde', $message);
        }
        $notification->push($message, 'horde.error', array('content.raw'));
        header('Location: ' . Horde::applicationUrl('list.php', true));
        exit;
    }
    /* Set up the note attributes. */
    if (empty($memolist_id)) {
        $memolist_id = Mnemo::getDefaultNotepad();
    }
    if (is_a($memolist_id, 'PEAR_Error')) {
        $notification->push($memolist_id, 'horde.error');
    }
    $memo_id = null;
    $memo_body = '';
    $memo_category = '';
    $storage = &Mnemo_Driver::singleton();

    $title = _("New Note");
    break;

case 'modify_memo':
    /* Check if a passphrase has been sent. */
    $passphrase = Horde_Util::getFormData('memo_passphrase');

    /* Get the current note. */
    $memo = Mnemo::getMemo($memolist_id, $memo_id, $passphrase);
    if (!$memo || !isset($memo['memo_id'])) {
        $notification->push(_("Note not found."), 'horde.error');
        header('Location: ' . Horde::applicationUrl('list.php', true));
        exit;
    }
    $storage = &Mnemo_Driver::singleton($memolist_id);

    /* Encryption tests. */
    $show_passphrase = showPassphrase($memo);

    /* Set up the note attributes. */
    $memo_body = $memo['body'];
    $memo_category = $memo['category'];
    $memo_encrypted = $memo['encrypted'];
    $title = sprintf(_("Edit: %s"), $memo['desc']);
    break;

case 'save_memo':
    /* Get the form values. */
    $memo_id = Horde_Util::getFormData('memo');
    $memo_body = Horde_Util::getFormData('memo_body');
    $memo_category = Horde_Util::getFormData('memo_category');
    $memolist_original = Horde_Util::getFormData('memolist_original');
    $notepad_target = Horde_Util::getFormData('notepad_target');
    $memo_passphrase = Horde_Util::getFormData('memo_passphrase');
    $memo_passphrase2 = Horde_Util::getFormData('memo_passphrase2');

    $share = $GLOBALS['mnemo_shares']->getShare($notepad_target);
    if (is_a($share, 'PEAR_Error')) {
        $notification->push(sprintf(_("Access denied saving note: %s"), $share->getMessage()), 'horde.error');
    } elseif (!$share->hasPermission(Horde_Auth::getAuth(), Horde_Perms::EDIT)) {
        $notification->push(sprintf(_("Access denied saving note to %s."), $share->get('name')), 'horde.error');
    } elseif ($memo_passphrase != $memo_passphrase2) {
        $notification->push(_("The passwords don't match."), 'horde.error');
        if (empty($memo_id)) {
            $title = _("New Note");
        } else {
            $actionID = 'modify_memo';
            $memo = Mnemo::getMemo($memolist_original, $memo_id);
            if (!$memo || !isset($memo['memo_id'])) {
                $notification->push(_("Note not found."), 'horde.error');
                header('Location: ' . Horde::applicationUrl('list.php', true));
                exit;
            }
            $title = sprintf(_("Edit: %s"), $memo['desc']);
            $show_passphrase = showPassphrase($memo);
            $memo_encrypted = $memo['encrypted'];
            $memolist_id = $memolist_original;
        }
        $storage = &Mnemo_Driver::singleton($memolist_original);
        break;
    } else {
        if ($new_category = Horde_Util::getFormData('new_category')) {
            $new_category = $cManager->add($new_category);
            $memo_category = $new_category ? $new_category : '';
        }
        if (!strlen($memo_passphrase)) {
            $memo_passphrase = Mnemo::getPassphrase($memo_id);
        }

        /* If $memo_id is set, we're modifying an existing note.  Otherwise,
         * we're adding a new note with the provided attributes. */
        if (!empty($memo_id) &&
            !is_a(Mnemo::getMemo($memolist_original, $memo_id), 'PEAR_Error')) {
            $storage = &Mnemo_Driver::singleton($memolist_original);
            if ($memolist_original != $notepad_target) {
                /* Moving the note to another notepad. */
                $share = &$GLOBALS['mnemo_shares']->getShare($memolist_original);
                if (!is_a($share, 'PEAR_Error') &&
                    $share->hasPermission(Horde_Auth::getAuth(), Horde_Perms::DELETE)) {
                    $share = &$GLOBALS['mnemo_shares']->getShare($notepad_target);
                    if (!is_a($share, 'PEAR_Error') &&
                        $share->hasPermission(Horde_Auth::getAuth(), Horde_Perms::EDIT)) {
                        $result = $storage->move($memo_id, $notepad_target);
                        $storage = &Mnemo_Driver::singleton($notepad_target);
                    } else {
                        $notification->push(_("Access denied moving the note."), 'horde.error');
                    }
                } else {
                    $notification->push(_("Access denied moving the note."), 'horde.error');
                }
            }
            $memo_desc = $storage->getMemoDescription($memo_body);
            if (empty($memo_passphrase) &&
                Horde_Util::getFormData('memo_encrypt') == 'on') {
                $memo_passphrase = Mnemo::getPassphrase($memo_id);
            }
            $result = $storage->modify($memo_id, $memo_desc, $memo_body, $memo_category, $memo_passphrase);
        } else {
            /* Check permissions. */
            if ($GLOBALS['injector']->getInstance('Horde_Perms')->hasAppPermission('max_notes') !== true &&
                $GLOBALS['injector']->getInstance('Horde_Perms')->hasAppPermission('max_notes') <= Mnemo::countMemos()) {
                header('Location: ' . Horde::applicationUrl('list.php', true));
                exit;
            }
            /* Creating a new note. */
            $storage = Mnemo_Driver::singleton($notepad_target);
            $memo_desc = $storage->getMemoDescription($memo_body);
            $result = $memo_id = $storage->add($memo_desc, $memo_body,
                                               $memo_category, null,
                                               $memo_passphrase);
        }

        /* Check our results. */
        if (is_a($result, 'PEAR_Error')) {
            $notification->push(sprintf(_("There was an error saving the note: %s"), $result->getMessage()), 'horde.warning');
        } else {
            $notification->push(sprintf(_("Successfully saved \"%s\"."), $memo_desc), 'horde.success');
        }
    }

    /* Return to the notepad view. */
    header('Location: ' . Horde::applicationUrl('list.php', true));
    exit;

case 'delete_memos':
    /* Delete the note if we're provided with a valid note ID. */
    $memo_id = Horde_Util::getFormData('memo');
    $memolist_id = Horde_Util::getFormData('memolist');

    if (!is_null($memo_id) && Mnemo::getMemo($memolist_id, $memo_id)) {
        $share = &$GLOBALS['mnemo_shares']->getShare($memolist_id);
        if (!is_a($share, 'PEAR_Error') &&
            $share->hasPermission(Horde_Auth::getAuth(), Horde_Perms::DELETE)) {
            $storage = &Mnemo_Driver::singleton($memolist_id);
            $result = $storage->delete($memo_id);

            if (is_a($result, 'PEAR_Error')) {
                $notification->push(sprintf(_("There was an error removing the note: %s"), $result->getMessage()), 'horde.warning');
            } else {
                $notification->push(_("The note was deleted."), 'horde.success');
            }
        } else {
            $notification->push(_("Access denied deleting note."), 'horde.warning');
        }
    }

    /* Return to the notepad. */
    header('Location: ' . Horde::applicationUrl('list.php', true));
    exit;

default:
    header('Location: ' . Horde::applicationUrl('list.php', true));
    exit;
}

$notepads = Mnemo::listNotepads(false, Horde_Perms::EDIT);
require MNEMO_TEMPLATES . '/common-header.inc';
require MNEMO_TEMPLATES . '/menu.inc';
$notification->notify();
require MNEMO_TEMPLATES . '/memo/memo.inc';
require $registry->get('templates', 'horde') . '/common-footer.inc';