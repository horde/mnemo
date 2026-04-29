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

use BadMethodCallException;
use Horde_Core_Share_Driver;
use Horde_Prefs_Identity;
use Horde_Share_Base;

/**
 * The default notepads handler.
 */
class DefaultNotepads extends Base
{
    /**
     * The current identity.
     */
    private Horde_Prefs_Identity $_identity;

    /**
     * Constructor.
     *
     * @param Horde_Share_Base $shares The share backend.
     * @param string           $user   The current user.
     * @param array            $params Additional parameters.
     *
     * @throws BadMethodCallException
     */
    public function __construct(Horde_Share_Base|Horde_Core_Share_Driver $shares, string $user, array $params)
    {
        if (!isset($params['identity'])) {
            throw new BadMethodCallException('This notepad handler needs an "identity" parameter!');
        }
        $this->_identity = $params['identity'];
        unset($params['identity']);
        parent::__construct($shares, $user, $params);
    }

    /**
     * Return the name of the default share.
     */
    protected function _getDefaultShareName(): string
    {
        return sprintf(_("Notepad of %s"), $this->_identity->getName());
    }
}
