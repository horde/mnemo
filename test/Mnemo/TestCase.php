<?php

/**
 * Basic Mnemo test case.
 *
 * PHP version 5
 *
 * @category   Horde
 * @package    Mnemo
 * @subpackage UnitTests
 * @author     Gunnar Wrobel <wrobel@pardus.de>
 * @license    http://www.horde.org/licenses/apache
 * @link       http://www.horde.org/apps/mnemo
 */

/**
 * Basic Mnemo test case.
 *
 * Copyright 2011-2026 Horde LLC (http://www.horde.org/)
 *
 * See the enclosed file LICENSE for license information (ASL). If you
 * did not receive this file, see http://www.horde.org/licenses/apache.
 *
 * @category   Horde
 * @package    Mnemo
 * @subpackage UnitTests
 * @author     Gunnar Wrobel <wrobel@pardus.de>
 * @license    http://www.horde.org/licenses/apache
 * @link       http://www.horde.org/apps/mnemo
 */
use PHPUnit\Framework\TestCase;

/**
 * @coversNothing
 */
class Mnemo_TestCase extends TestCase
{
    public function setUp(): void
    {
        $this->markTestIncomplete('Needs some love');
    }

    protected function getInjector()
    {
        return new Horde_Injector(new Horde_Injector_TopLevel());
    }

    protected static function createSqlPdoSqlite(Horde_Test_Setup $setup)
    {
        return;

        $setup->setup(
            [
                'Horde_Db_Adapter' => [
                    'factory' => 'Db',
                    'params' => [
                        'migrations' => [
                            'migrationsPath' => __DIR__ . '/../../migration',
                            'schemaTableName' => 'mnemo_test_schema',
                        ],
                    ],
                ],
            ]
        );
    }

    protected function getKolabDriver()
    {
        return;

        self::createKolabSetup();
        [$share, $this->other_share] = self::_createDefaultShares();
        return $GLOBALS['injector']->getInstance('Mnemo_Factory_Driver')->create(
            $share->getName()
        );
    }

    protected static function createKolabSetup()
    {
        $setup = new Horde_Test_Setup();
        self::createBasicMnemoSetup($setup);
        self::createKolabShares($setup);
        return $setup;
    }

    protected static function createBasicMnemoSetup(Horde_Test_Setup $setup)
    {
        $setup->setup(
            [
                '_PARAMS' => [
                    'user' => 'test@example.com',
                    'app' => 'mnemo',
                ],
                'Horde_Prefs' => 'Prefs',
                'Horde_Perms' => 'Perms',
                'Horde_Group' => 'Group',
                'Horde_History' => 'History',
                'Horde_Registry' => 'Registry',
            ]
        );
        $setup->makeGlobal(
            [
                'prefs' => 'Horde_Prefs',
                'registry' => 'Horde_Registry',
                'injector' => 'Horde_Injector',
            ]
        );

        $GLOBALS['conf']['prefs']['driver'] = 'Null';
    }

    protected static function createSqlShares(Horde_Test_Setup $setup)
    {
        return;

        $setup->setup(
            [
                'Horde_Share_Base' => 'Share',
            ]
        );
        $setup->makeGlobal(
            [
                'mnemo_shares' => 'Horde_Share_Base',
            ]
        );
        $setup->getInjector()->setInstance(
            'Horde_Core_Factory_Share',
            new Horde_Test_Stub_Factory(
                $setup->getInjector()->getInstance('Horde_Share_Base')
            )
        );
        $GLOBALS['conf']['storage']['driver'] = 'sql';
        $GLOBALS['conf']['notepads']['driver'] = 'default';
    }

    protected static function createKolabShares(Horde_Test_Setup $setup)
    {
        $setup->setup(
            [
                'Horde_Kolab_Storage' => [
                    'factory' => 'KolabStorage',
                    'params' => [
                        'imapuser' => 'test',
                    ],
                ],
                'Horde_Share_Base' => [
                    'factory' => 'Share',
                    'method' => 'Kolab',
                ],
            ]
        );
        $setup->makeGlobal(
            [
                'mnemo_shares' => 'Horde_Share_Base',
            ]
        );
        $setup->getInjector()->setInstance(
            'Horde_Core_Factory_Share',
            new Horde_Test_Stub_Factory(
                $setup->getInjector()->getInstance('Horde_Share_Base')
            )
        );
        $GLOBALS['conf']['storage']['driver'] = 'kolab';
        $GLOBALS['conf']['notepads']['driver'] = 'kolab';
    }

    protected static function _createDefaultShares()
    {
        $share = self::_createShare(
            'Notepad of Tester',
            'test@example.com'
        );
        $other_share = self::_createShare(
            'Other notepad of Tester',
            'test@example.com'
        );
        return [$share, $other_share];
    }

    private static function _createShare($name, $owner)
    {
        $share = $GLOBALS['mnemo_shares']->newShare(
            $owner,
            strval(new Horde_Support_Randomid()),
            $name
        );
        $GLOBALS['mnemo_shares']->addShare($share);
        return $share;
    }
}
