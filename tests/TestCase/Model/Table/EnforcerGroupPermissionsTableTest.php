<?php
namespace Enforcer\Test\TestCase\Model\Table;

use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;
use Enforcer\Model\Table\EnforcerGroupPermissionsTable;

/**
 * Enforcer\Model\Table\EnforcerGroupPermissionsTable Test Case
 */
class EnforcerGroupPermissionsTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \Enforcer\Model\Table\EnforcerGroupPermissionsTable
     */
    public $EnforcerGroupPermissions;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'plugin.Enforcer.EnforcerGroupPermissions',
        'plugin.Enforcer.Groups',
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $config = TableRegistry::getTableLocator()->exists('EnforcerGroupPermissions') ? [] : ['className' => EnforcerGroupPermissionsTable::class];
        $this->EnforcerGroupPermissions = TableRegistry::getTableLocator()->get('EnforcerGroupPermissions', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->EnforcerGroupPermissions);

        parent::tearDown();
    }

    /**
     * Test initialize method
     *
     * @return void
     */
    public function testInitialize()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    /**
     * Test validationDefault method
     *
     * @return void
     */
    public function testValidationDefault()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    /**
     * Test buildRules method
     *
     * @return void
     */
    public function testBuildRules()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }
}
