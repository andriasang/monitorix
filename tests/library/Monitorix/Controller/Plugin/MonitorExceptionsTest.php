<?php

/**
 * cloud solutions monitorix 
 * 
 * This source file is part of the cloud solutions monitorix package
 * 
 * @category   Monitorix
 * @package    Tests
 * @license    New BSD License {@link /docs/LICENSE}
 * @copyright  Copyright (c) 2011, cloud solutions O�
 * @version    $Id: MonitorExceptionsTest.php 51 2011-09-20 00:07:57Z markushausammann@gmail.com $
 */

use Mockery\Mock;

require_once 'Zend/Controller/Plugin/Abstract.php';
require_once 'Zend/Registry.php';
require_once 'Monitorix/Controller/Plugin/MonitorExceptions.php';

use \Mockery as m;

/**
 * Monitorix_Controller_Plugin_MonitorExceptions test case.
 */
class Monitorix_Controller_Plugin_MonitorExceptionsTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var monitorExceptions
     */
    private $monitorExceptions;
    
    /**
     * Prepares the environment before running a test.
     */
    protected function setUp ()
    {
    	$response = m::mock('Zend_Controller_Response_Abstract');
    	$response->shouldReceive('isException')->andReturn(TRUE);
        
        $this->monitorExceptions = new Monitorix_Controller_Plugin_MonitorExceptions();
        $this->monitorExceptions->setResponse($response);
        
        $monitor = m::mock('monitor');
    	$monitor->shouldReceive('writeLog')->once();
        Zend_Registry::set('monitor', $monitor);
    }
    
    /**
     * Cleans up the environment after running a test.
     */
    protected function tearDown()
    {
        $this->monitorExceptions = null;
        Zend_Registry::_unsetInstance();
        
        m::close();
    }

    /**
     * Tests MonitorExceptions->dispatchLoopShutdown()
     * Doesn't test the actual writeLog method, that is covered by {@see Monitorix_MonitorTest}
     */
    public function testDispatchLoopShutdown()
    {
    	try {
        	$this->monitorExceptions->dispatchLoopShutdown();
    	}
    	catch (Exception $exception)
    	{
    		$this->fail('An exception was thrown: ' . $exception->getMessage());
    	}
    }
}

