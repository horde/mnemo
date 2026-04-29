<?php

declare(strict_types=1);

/**
 * Copyright 2011-2026 Horde LLC (http://www.horde.org/)
 *
 * See the enclosed file LICENSE for license information (ASL). If you
 * did not receive this file, see http://www.horde.org/licenses/apache.
 *
 * @package Mnemo
 */

namespace Horde\Mnemo\Factory;

use Horde\Injector\Injector;
use Mnemo_TagBrowser;
use Mnemo_Tagger;

/**
 * Factory for the tag browser.
 */
class TagBrowser
{
    /**
     * Cached instance.
     */
    private ?Mnemo_TagBrowser $_instance = null;

    /**
     * The injector.
     */
    private Injector $_injector;

    /**
     * Constructor.
     */
    public function __construct(Injector $injector)
    {
        $this->_injector = $injector;
    }

    /**
     * Return the TagBrowser instance.
     */
    public function create(): Mnemo_TagBrowser
    {
        if ($this->_instance === null) {
            $this->_instance = new Mnemo_TagBrowser(
                $this->_injector->getInstance('Mnemo_Tagger')
            );
        }

        return $this->_instance;
    }
}
