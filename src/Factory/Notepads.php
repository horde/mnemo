<?php

declare(strict_types=1);

/**
 * Copyright 2011-2026 Horde LLC (http://www.horde.org/)
 *
 * See the enclosed file LICENSE for license information (ASL). If you
 * did not receive this file, see http://www.horde.org/licenses/apache.
 *
 * @author   Gunnar Wrobel <wrobel@pardus.de>
 * @package  Mnemo
 */

namespace Horde\Mnemo\Factory;

use Horde\Injector\Injector;
use Horde\Mnemo\Exception;
use Horde\Mnemo\Notepads\Base as NotepadsBase;
use Horde_String;

/**
 * The factory for the notepads handler.
 */
class Notepads
{
    /**
     * Notepads drivers already created.
     *
     * @var array<string, NotepadsBase>
     */
    private array $_instances = [];

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
     * Return a Notepads instance.
     *
     * @throws Exception
     */
    public function create(): NotepadsBase
    {
        if (!isset($GLOBALS['conf']['notepads']['driver'])) {
            $driver = 'Default';
        } else {
            $driver = Horde_String::ucfirst($GLOBALS['conf']['notepads']['driver']);
        }
        if (empty($this->_instances[$driver])) {
            $class = 'Mnemo_Notepads_' . $driver;
            if (class_exists($class)) {
                $params = [];
                if (!empty($GLOBALS['conf']['share']['auto_create'])) {
                    $params['auto_create'] = true;
                }
                switch ($driver) {
                case 'Default':
                    $params['identity'] = $this->_injector->getInstance('Horde_Core_Factory_Identity')->create();
                    break;
                }
                $this->_instances[$driver] = new $class(
                    $GLOBALS['mnemo_shares'],
                    $GLOBALS['registry']->getAuth(),
                    $params
                );
            } else {
                throw new Exception(sprintf('Unable to load the definition of %s.', $class));
            }
        }
        return $this->_instances[$driver];
    }
}
