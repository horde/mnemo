<?php

declare(strict_types=1);

/**
 * Copyright 2011-2026 Horde LLC (http://www.horde.org/)
 *
 * See the enclosed file LICENSE for license information (ASL). If you
 * did not receive this file, see http://www.horde.org/licenses/apache.
 *
 * @author Michael J. Rubinsky <mrubinsk@horde.org>
 * @package Mnemo
 */

namespace Horde\Mnemo\Factory;

use Horde;
use Horde\Injector\Injector;
use Horde\Mnemo\Exception;
use Mnemo_Driver;

/**
 * Injector factory to create Mnemo driver instances.
 */
class Driver
{
    /**
     * Cached instances.
     *
     * @var array<string, Mnemo_Driver>
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
     * Return a Mnemo_Driver instance.
     *
     * @throws Exception
     */
    public function create(string $name = ''): Mnemo_Driver
    {
        if (!isset($this->_instances[$name])) {
            $driver = $GLOBALS['conf']['storage']['driver'];
            $params = Horde::getDriverConfig('storage', $driver);
            $class = 'Mnemo_Driver_' . ucfirst(basename($driver));
            if (!class_exists($class)) {
                throw new Exception(sprintf('Unable to load the definition of %s.', $class));
            }

            switch ($class) {
            case 'Mnemo_Driver_Sql':
                if ($params['driverconfig'] != 'horde') {
                    $customParams = $params;
                    unset($customParams['driverconfig'], $customParams['table']);
                    $params['db'] = $this->_injector->getInstance('Horde_Core_Factory_Db')->create('mnemo', $customParams);
                } else {
                    $params['db'] = $this->_injector->getInstance('Horde_Db_Adapter');
                }
                break;

            case 'Mnemo_Driver_Kolab':
                $params = [
                    'storage' => $this->_injector->getInstance('Horde_Kolab_Storage'),
                ];
                break;
            }
            $driver = new $class($name, $params);
            $this->_instances[$name] = $driver;
        }

        return $this->_instances[$name];
    }
}
