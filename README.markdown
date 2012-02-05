Monitorix is released under the New BSD License.

The current version is Monitorix 1.2.

Travis CI has run all unit tests and says: [![Build Status](https://secure.travis-ci.org/markushausammann/monitorix.png)](http://travis-ci.org/markushausammann/monitorix)

## What's nice about monitorix

* integrated logging of php errors, exceptions, javascript errors, slow queries and more
* log entries of all your apps in one filterable, sortable, queryable place (database)
* very easy to set up
* doesn't interfere with your default error reporting settings

## Features
Monitorix is meant to offer a **light but free** alternative to the commercial monitoring solution integrated in Zend Server.

### Current Features
* easy setup and integration with your existing Zend Framework applications
* minimal bootstrapping and configuration
* simple message logging
* automated logging of php errors (optional)
* **NEW**: automated logging of fatal php errors (optional)
* automated logging of uncaught exceptions (optional)
* automated logging of slow database queries (optional)
* **NEW**: automated logging of javascript errors (optional)
* fully unit tested

## Prerequisites
* Zend Framework 1.10 or newer
* PHP 5.3 or newer

Needed for javascript error logging:

* ZendX_JQuery or your own implementation of jQuery (more specifially, the jQuery Ajax plugin is needed.)

Needed for running the unit tests:

* phpUnit
* [Mockery](https://github.com/padraic/mockery)

## Installation and usage
### Location
We advise you to use the bundle as provided as an additional library somewhere on your php include path, for example:

    docs
    |_LICENSE
    |_monitorix.sql 

    library
    |_Monitorix
      |_Controller
        |_Plugin
          |_MonitorExceptions.php
          |_MonitorJavascript.php
          |_MonitorSlowQueries.php
      |_Monitor.php
      |_Version.php
    |_Zend <- your Zend Framework library

### Setup steps
1. Add the 'Monitorix' folder to your library folder or use a Symlink
2. Add  'Monitorix_' to your namespaces.
3. Create the database, the table and the user with the help of docs/monitorix.sql
4. Add the following connection block to your application.ini

        resources.monitor.db.adapter = "Pdo_Mysql"
        resources.monitor.db.params.username = "monitorix"
        resources.monitor.db.params.password = "yourmonitorixpassword"
        resources.monitor.db.params.dbname = "monitorix"

5. Add the following lines of code to your Bootstrap.php

        protected function _initMonitor()
        {
            $config = Zend_Registry::get('config');
            $monitorDb = Zend_Db::factory($config->resources->monitor->db->adapter, $config->resources->monitor->db->params);
    
            $monitor = new Monitorix_Monitor(new Zend_Log_Writer_Db($monitorDb, 'logentries'), "yourProjectName");
            
            //if you want to monitor php errors
            $monitor->registerErrorHandler();
    
            //if you want to monitor fatal errors and syntax errors
            $monitor->logFatalErrors();
    
            //if you want to log exceptions
            $monitor->logExceptions();
    
            //if you want to monitor javascript errors
            $monitor->logJavascriptErrors();
    
            //if you want to log slow database queries
            $monitor->logSlowQueries(array($dbAdapter));
        }

    monitorix provides a fluid interface, so you could also write:

        $monitor->registerErrorHandler()->logFatalErrors()->logExceptions()->logSlowQueries(array($dbAdapter));

### Usage
#### General
monitorix will attempt to set the 'environment' field automatically by using the APPLICATION_ENV constant. If this constant is not defined (should be) it will log everything with the default value of "undefined". You can also set the value in your bootstrap or elsewhere with:

    $monitor->setEnvironment('development');

Also, if you don't pass a project name when you setup the monitorix instance, you can later set it with:

    $monitor->setProjectName('myProjectName');

#### Logging PHP errors
monitorix automatically logs PHP errors for you, if you use:

    $monitor->registerErrorHandler();

PHP errors are logged according to the 8 priorities documented in Zend_Log. monitorix has a field 'logType'. For logged PHP errors this field will contain the string "php error".

#### Logging fatal PHP errors
monitorix automatically logs the last PHP error before shutdown/exit, if  you use:

    $monitor->logFatalErrors();

Such errors are a subclass of the "php error" type with a special context information "Last error before shutdown. Fatal or syntax.".

#### Logging Exceptions
monitorix automagically logs all Exceptions that bubble up to the surface (ancaught Exceptions) if you set:

    $monitor->logExceptions();

Exceptions are logged with priority '2 = CRIT'. The field 'logType' will contain the string "exception".

#### Logging Javascript Errors
monitorix automagically logs all javascript errors that are not caught during execution, if you set:

    $monitor->logJavascriptErrors();

monitorix will attempt to automatically:

1. init the view, if it can't be retrieved from the viewRenderer
2. register the jQuery view helper, if not registered already
3. enable jQuery, if it is not enabled yet

If monitorix should fail to enable jQuery, it will throw a Monitorix_Exception. If you have a custom implementation of jQuery, you can suppress step 2 and 3 of this list by passing FALSE as second parameter. 

    $monitor->logJavascriptErrors(TRUE, FALSE);
    
The view has to be available though, so that monitorix can prepend its 'window.onerror' script.

#### Logging Slow Database Queries
monitorix automagically logs all database queries that take longer than 1 second or any other value you pass, if you set:

    $monitor->logSlowQueries(array($myDbAdapter, $myOtherDbAdapter), 0.5);

As first parameter you can pass an array of as many Zend_Db_Adapter instances as you wish. Normally you'll probably have one.

As second argument, you can pass a limit in seconds. Every query which takes longer than that, will be logged.

#### Logging other events
With monitorix you can log whaterver you want, wherever you want.
Here a few examples:

    //get the monitorix instance from the registry
    $monitor = Zend_Registry::get('monitor');

    //log a simple message, it will be logged with the default priority 7 = DEBUG and the 'logType' "simpleLog"
    $monitor->writeLog('A simple message');

    //log a message with a custom 'logType' and a custom priority
    $monitor->writeLog('A special message', 5, 'myCustomLogType');

    //set your own default log type
    $monitor->setDefaultLogType('myDefault');

    //and then log to this type simply with
    $monitor->writeLog('this will be logged with my new default logType');


## Contributions
This project is developed by the team of www.cloud-solutions.net.

Additional contributions have been made by:

* Ritesh Kumar Sahu - Fatal error logging through use of register_shutdown_function

You're welcome to contribute to the component! Just contact us, if you're interested!