<?php
/**
 * Copyright 2011-2026 Horde LLC (http://www.horde.org/)
 *
 * See the enclosed file LICENSE for license information (ASL). If you
 * did not receive this file, see http://www.horde.org/licenses/apache.
 *
 * @package Mnemo
 */

use Horde\Mnemo\Factory\TagBrowser;

class Mnemo_Factory_TagBrowser extends Horde_Core_Factory_Base
{
    private ?TagBrowser $_delegate = null;

    public function create()
    {
        if ($this->_delegate === null) {
            $this->_delegate = new TagBrowser($this->_injector);
        }
        return $this->_delegate->create();
    }
}
