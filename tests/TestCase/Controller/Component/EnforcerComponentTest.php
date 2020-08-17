<?php
namespace Enforcer\Test\TestCase\Controller\Component;

use Cake\Controller\ComponentRegistry;
use Cake\TestSuite\TestCase;
use Enforcer\Controller\Component\EnforcerComponent;

/**
 * Enforcer\Controller\Component\EnforcerComponent Test Case
 */
class EnforcerComponentTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \Enforcer\Controller\Component\EnforcerComponent
     */
    public $Enforcer;

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $registry = new ComponentRegistry();
        $this->Enforcer = new EnforcerComponent($registry);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->Enforcer);

        parent::tearDown();
    }

    /**
     * Test initial setup
     *
     * @return void
     */
    public function testInitialization()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }
}
