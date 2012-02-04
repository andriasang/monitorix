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
 * @version    $Id: MonitorSlowQueriesTest.php 50 2011-09-16 14:54:07Z markushausammann@gmail.com $
 */

require_once 'Zend/Controller/Plugin/Abstract.php';
require_once 'Zend/Controller/Request/Abstract.php';
require_once 'Zend/Controller/Response/Abstract.php';
require_once 'Zend/Registry.php';
require_once 'Monitorix/Controller/Plugin/MonitorSlowQueries.php';

use \Mockery as m;

/**
 * Monitorix_Controller_Plugin_MonitorSlowQueries test case.
 */
class Monitorix_Controller_Plugin_MonitorSlowQueriesTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var $monitorSlowQueries
     */
    private $monitorSlowQueries;
    
    /**
     * Prepares the environment before running a test.
     */
    protected function setUp ()
    {
    	$monitor = m::mock('monitor');
    	$monitor->shouldReceive('writeLog')->once();
    	$monitor->shouldReceive('getSlowQueryLimitSeconds')->andReturn(1.5);
    	
    	$queryOne = m::mock('queryone');
    	$queryOne->shouldReceive('getElapsedSecs')->andReturn(1);
    	
    	$queryTwo = m::mock('querytwo');
    	$queryTwo->shouldReceive('getElapsedSecs')->twice()->andReturn(2);
    	$queryTwo->shouldReceive('getQuery')->once();
    	$queryTwo->shouldReceive('getQueryParams')->once()->andReturn(array());
    	
    	$profiler = m::mock('profiler');
    	$profiler->shouldReceive('getTotalNumQueries')->andReturn(TRUE);
    	$profiler->shouldReceive('getQueryProfiles')->andReturn(array($queryOne, $queryTwo));
    	$profiler->shouldReceive('clear');
        
        $this->monitorSlowQueries = new Monitorix_Controller_Plugin_MonitorSlowQueries();
        
        Zend_Registry::set('monitor', $monitor);
        Zend_Registry::set('profilers', array($profiler));
    }
    
    /**
     * Cleans up the environment after running a test.
     */
    protected function tearDown ()
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
        	$this->monitorSlowQueries->dispatchLoopShutdown();
    	}
    	catch (Exception $exception)
    	{
    		$this->fail('An exception was thrown: ' . $exception->getMessage());
    	}
    }
}

