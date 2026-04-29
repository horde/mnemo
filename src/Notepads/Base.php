<?php

declare(strict_types=1);

/**
 * Copyright 2001-2026 Horde LLC (http://www.horde.org/)
 *
 * See the enclosed file LICENSE for license information (ASL). If you
 * did not receive this file, see http://www.horde.org/licenses/apache.
 *
 * @author   Jon Parise <jon@horde.org>
 * @author   Gunnar Wrobel <wrobel@pardus.de>
 * @package  Mnemo
 */

namespace Horde\Mnemo\Notepads;

use Horde_Core_Share_Driver;
use Horde_Share_Base;
use Horde_Share_Object;
use Horde_Support_Randomid;
use Mnemo;

/**
 * The base functionality of the notepads handler.
 */
abstract class Base
{
    /**
     * The share backend.
     */
    protected Horde_Share_Base|Horde_Core_Share_Driver $_shares;

    /**
     * The current user.
     */
    protected string $_user;

    /**
     * Additional parameters for the notepad handling.
     */
    protected array $_params;

    /**
     * Constructor.
     *
     * @param Horde_Share_Base|Horde_Core_Share_Driver $shares The share backend.
     * @param string           $user   The current user.
     * @param array            $params Additional parameters.
     */
    public function __construct(Horde_Share_Base|Horde_Core_Share_Driver $shares, string $user, array $params)
    {
        $this->_shares = $shares;
        $this->_user = $user;
        $this->_params = $params;
    }

    /**
     * Ensure the share system has a default notepad share for the current user
     * if the default share feature is activated.
     *
     * @return string|null The id of the new default share or null if no share
     *                     was created.
     */
    public function ensureDefaultShare(): ?string
    {
        if (!empty($this->_params['auto_create']) && $this->_user
            && !count(Mnemo::listNotepads(true))) {
            $share = $this->_shares->newShare(
                $this->_user,
                strval(new Horde_Support_Randomid()),
                $this->_getDefaultShareName()
            );
            $this->_prepareDefaultShare($share);
            $this->_shares->addShare($share);
            return $share->getName();
        }

        return null;
    }

    /**
     * Returns the default share's ID, if it can be determined from the share
     * backend.
     *
     * @return string|null The default share ID.
     */
    public function getDefaultShare(): ?string
    {
        $shares = $this->_shares->listShares(
            $this->_user,
            ['attributes' => $this->_user]
        );
        foreach ($shares as $id => $share) {
            if ($share->get('default')) {
                return $id;
            }
        }

        return null;
    }

    /**
     * Runs any actions after setting a new default notepad.
     */
    public function setDefaultShare(string $share): void {}

    /**
     * Return the name of the default share.
     */
    abstract protected function _getDefaultShareName(): string;

    /**
     * Add any modifiers required to the share in order to mark it as default.
     */
    protected function _prepareDefaultShare(Horde_Share_Object $share): void {}
}
