<?php
namespace Enforcer\Test\TestCase\Model\Table;

use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;
use Enforcer\Model\Table\EnforcerUsersGroupsTable;

/**
 * Enforcer\Model\Table\EnforcerUsersGroupsTable Test Case
 */
class EnforcerUsersGroupsTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \Enforcer\Model\Table\EnforcerUsersGroupsTable
     */
    public $EnforcerUsersGroups;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'plugin.Enforcer.EnforcerUsersGroups',
        'plugin.Enforcer.Groups',
        'plugin.Enforcer.Users',
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $config = TableRegistry::getTableLocator()->exists('EnforcerUsersGroups') ? [] : ['className' => EnforcerUsersGroupsTable::class];
        $this->EnforcerUsersGroups = TableRegistry::getTableLocator()->get('EnforcerUsersGroups', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->EnforcerUsersGroups);

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
