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
 * @version    $Id: MonitorJavascriptTest.php 54 2011-09-20 00:19:05Z markushausammann@gmail.com $
 */

require_once 'Zend/Registry.php';
require_once 'Monitorix/Controller/Plugin/MonitorJavascript.php';

use \Mockery as m;

/**
 * Monitorix_Controller_Plugin_MonitorSlowQueries test case.
 */
class Monitorix_Controller_Plugin_MonitorJavascriptTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var $monitorSlowQueries
     */
    private $monitorJavascriptErrors;
    
    /**
     * Prepares the environment before running a test.
     */
    protected function setUp ()
    {
    	$monitor = m::mock('monitor');
    	$monitor->shouldReceive('writeLog')->once();
    	
    	Zend_Registry::set('monitor', $monitor);
    	
    	$_POST['message']   = 'testmessage';
    	$_POST['errorUrl']  = 'noRealUrl';
    	$_POST['errorLine'] = '777';
    	
    	$this->monitorJavascriptErrors = new Monitorix_Controller_Plugin_MonitorJavascript();
    }
    
    /**
     * Cleans up the environment after running a test.
     */
    protected function tearDown ()
    {
        $this->monitorJavascriptErrors = null;
        Zend_Registry::_unsetInstance();
        unset($_POST['message']);
        unset($_POST['errorUrl']);
        unset($_POST['errorLine']);
        m::close();
    }

    /**
     * Tests MonitorJavascriptErrors->routeStartup()
     * Doesn't test the actual writeLog method, that is covered by {@see Monitorix_MonitorTest}
     */
    public function testRouteStartup()
    {
    	$request = m::mock('Zend_Controller_Request_Abstract');
    	$request->shouldReceive('__get')->with('monitori')->once()->andReturn('x');
    	$request->shouldReceive('isXmlHttpRequest')->once()->andReturn(TRUE);
    	
    	try {
        	$this->monitorJavascriptErrors->routeStartup($request);
    	}
    	catch (Exception $exception)
    	{
    		$this->fail('An exception was thrown: ' . $exception->getMessage());
    	}
    }
}

