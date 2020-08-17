<?php
namespace Enforcer\Test\TestCase\Model\Table;

use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;
use Enforcer\Model\Table\EnforcerGroupsTable;

/**
 * Enforcer\Model\Table\EnforcerGroupsTable Test Case
 */
class EnforcerGroupsTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \Enforcer\Model\Table\EnforcerGroupsTable
     */
    public $EnforcerGroups;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'plugin.Enforcer.EnforcerGroups',
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $config = TableRegistry::getTableLocator()->exists('EnforcerGroups') ? [] : ['className' => EnforcerGroupsTable::class];
        $this->EnforcerGroups = TableRegistry::getTableLocator()->get('EnforcerGroups', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->EnforcerGroups);

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
}
