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
 * @version    $Id: MonitorTest.php 51 2011-09-20 00:07:57Z markushausammann@gmail.com $
 */

require_once 'Zend/Log.php';
require_once 'Zend/Controller/Plugin/Abstract.php';
require_once 'Zend/Log/Writer/Mock.php';
require_once 'Zend/Registry.php';
require_once 'Zend/Controller/Front.php';
require_once 'Monitorix/Monitor.php';
require_once 'Monitorix/Controller/Plugin\MonitorExceptions.php';
require_once 'Monitorix/Controller/Plugin\MonitorSlowQueries.php';
require_once 'Monitorix/Controller/Plugin\MonitorJavascript.php';


use \Mockery as m;

/**
 * Test class for Monitorix_Monitor.
 * Generated by PHPUnit on 2011-04-18 at 12:16:31.
 */
class Monitorix_MonitorTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Monitorix_Monitor
     */
    protected $monitor;
    protected $writer;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {	
    	$this->writer = new Zend_Log_Writer_Mock();
        $this->monitor = new Monitorix_Monitor($this->writer, 'testproject');
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
    	restore_error_handler();
    	Zend_Controller_Front::getInstance()->resetInstance();

    	m::close();
    }
    
    /**
     * testWriteLog().
     */
    public function testWriteSimpleLogMessage()
    {
    	//prepare
        $this->monitor->writeLog('test');
    	
        //assertions
        $this->assertContains('test', $this->writer->events[0]['message']);
    	$this->assertContains('simplelog', $this->writer->events[0]['logType']);
    	$this->assertContains('testing', $this->writer->events[0]['environment']);
    	$this->assertContains('testproject', $this->writer->events[0]['projectName']);
    	$this->assertEquals(7, $this->writer->events[0]['priority']);
    }
    
	/**
     * testWriteLog().
     */
    public function testWriteCustomLogMessage()
    {
    	//prepare
    	$this->monitor->setEnvironment('nextenv');
    	$this->monitor->setProjectName('monitorix');
    	$this->monitor->writeLog('nexttest', 1, 'nexttype');
    	
    	//assertions
    	$this->assertContains('nexttest', $this->writer->events[0]['message']);
    	$this->assertContains('nexttype', $this->writer->events[0]['logType']);
    	$this->assertContains('nextenv', $this->writer->events[0]['environment']);
    	$this->assertContains('monitorix', $this->writer->events[0]['projectName']);
    	$this->assertEquals(1, $this->writer->events[0]['priority']);
    }
    
	/**
     * testWriteLog().
     * 
     * @runInSeparateProcess
     */    
    public function testWriteException()
    {
    	$exception = m::mock('alias:Zend_Exception');
    	$exception->shouldReceive('getMessage')->once()->andReturn('testmessage');
    	$exception->shouldReceive('getCode')->once()->andReturn('10');
    	$exception->shouldReceive('getFile')->once()->andReturn('/testpath/');
    	$exception->shouldReceive('getLine')->once()->andReturn('0');
    	$exception->shouldReceive('getTrace')->once()->andReturn(array());
    	
    	$response = m::mock('alias:Zend_Controller_Response_Http');
    	$response->shouldReceive('getException')->once()->andReturn(array($exception));
    	$this->monitor->writeLog($response);
    	
    	//assertions
    	$this->assertContains('testmessage', $this->writer->events[0]['message']);
    	$this->assertContains('exception', $this->writer->events[0]['logType']);
    	$this->assertContains('10', $this->writer->events[0]['errorNumber']);
    	$this->assertEquals(2, $this->writer->events[0]['priority']);
    	
    }

    /**
     * testLogExceptions().
     */
    public function testRegisterMonitorExceptionsPlugin()
    {
    	$this->runTestInSeparateProcess = TRUE;
    	$frontController = Zend_Controller_Front::getInstance();
        $this->monitor->logExceptions(TRUE);
        
        $this->assertContains('Monitorix_Controller_Plugin_MonitorExceptions', get_class($frontController->getPlugin('Monitorix_Controller_Plugin_MonitorExceptions')));
        $this->assertTrue($this->monitor->loggingExceptions);
        
        return $frontController;
    }
    
    /**
     * testLogExceptions().
     * 
     * @depends testRegisterMonitorExceptionsPlugin
     */
    public function testRemoveMonitorExceptionsPlugin($frontController)
    {
    	$this->runTestInSeparateProcess = TRUE;
    	$this->runTestInSeparateProcess = TRUE;
        $this->monitor->logExceptions(FALSE);
        
		$this->assertFalse($frontController->getPlugin('Monitorix_Controller_Plugin_MonitorExceptions'));
		$this->assertFalse($this->monitor->loggingExceptions);
    }

    /**
     * testLogSlowQueries().
     */
    public function testRegisterSlowQueriesPlugin()
    {
        $frontController = Zend_Controller_Front::getInstance();
        
        $adapter = m::mock('alias:Zend_Db_Adapter_Pdo_Mysql');
        $profiler = m::mock('alias:Zend_Db_Profiler');
        $adapter->shouldReceive('getProfiler->setEnabled')->andReturn($profiler);
        $this->monitor->logSlowQueries(array($adapter));
        
        $this->assertContains('Monitorix_Controller_Plugin_MonitorSlowQueries', get_class($frontController->getPlugin('Monitorix_Controller_Plugin_MonitorSlowQueries')));
        $this->assertTrue($this->monitor->loggingSlowQueries);
        $profilers = Zend_Registry::get('profilers');
        $this->assertTrue($profilers[0] instanceof Zend_Db_Profiler);
        
        return $frontController;
    }
    
    /**
     * testLogSlowQueries().
     * 
     * @depends testRegisterSlowQueriesPlugin
     */
    public function testRemoveSlowQueriesPlugin($frontController)
    {	
        $this->monitor->logSlowQueries(array(), null, FALSE);
        
		$this->assertFalse($frontController->getPlugin('Monitorix_Controller_Plugin_MonitorSlowQueries'));
		$this->assertFalse($this->monitor->loggingSlowQueries);
    }
    
    /**
     * testLogJavascriptErrors()
     * 
     */
    public function testRegisterJavascriptPlugin()
    {
    	$frontController = Zend_Controller_Front::getInstance();
    	
    	$this->monitor->logJavascriptErrors();
    	
    	$this->assertContains('Monitorix_Controller_Plugin_MonitorJavascript', get_class($frontController->getPlugin('Monitorix_Controller_Plugin_MonitorJavascript')));
        $this->assertTrue($this->monitor->loggingJavascriptErrors);
        
        $viewRenderer = Zend_Controller_Action_HelperBroker::getStaticHelper('viewRenderer');
        $view = $viewRenderer->view;
        
        $this->assertTrue(strpos($view->headScript()->toString(), 'window.onerror') <> FALSE);
        
        return $frontController;
    }
    
	/**
     * testLogJavascriptErrors()
     * 
     * @depends testRegisterJavascriptPlugin
     */
    public function testRemoveJavascriptPlugin($frontController)
    {
    	$viewRenderer = Zend_Controller_Action_HelperBroker::getStaticHelper('viewRenderer');
        $view = $viewRenderer->view;
        
    	$this->monitor->logJavascriptErrors(FALSE);
    	
    	$this->assertFalse($frontController->getPlugin('Monitorix_Controller_Plugin_MonitorJavascript'));
		$this->assertFalse($this->monitor->loggingJavascriptErrors);
		$this->assertNull($view);
    }
   
	/**
     * Tests Monitorix_Monitor->setDefaultLogType()
     */
    public function testSetDefaultLogType ()
    {
        $this->monitor->setDefaultLogType('newdefault');
        $this->monitor->writeLog('test');
        $this->assertContains('newdefault', $this->writer->events[0]['logType']);
    }
    
    /**
     * Tests Monitorix_Monitor->getProjectName()
     */
    public function testGetProjectName ()
    {
        $projectName = $this->monitor->getProjectName();
        $this->assertContains('testproject', $projectName);
    }
    
    /**
     * Tests Monitorix_Monitor->getEnvironment()
     */
    public function testGetEnvironment ()
    {
        $environment = $this->monitor->getEnvironment();
        $this->assertContains('testing', $environment);
    }
    
 	/**
     * testErrorHandler()
     */
    public function testErrorHandlerWritesToLog()
    {
    	//prepare
        set_error_handler(array($this->monitor, 'errorHandler'));
        trigger_error('testerror');
        
        //assertions
        $this->assertContains('testerror', $this->writer->events[0]['message']);
    	$this->assertContains('php_error', $this->writer->events[0]['logType']);
    	$this->assertContains('testing', $this->writer->events[0]['environment']);
    	$this->assertContains('testproject', $this->writer->events[0]['projectName']);
    	$this->assertEquals(6, $this->writer->events[0]['priority']);
    	
    	restore_error_handler();
    }
}
?>