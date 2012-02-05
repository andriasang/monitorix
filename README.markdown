__NOTOC__

Monitorix is released under the New BSD License.

The current version is Monitorix 1.2.

Travis CI has run all unit tests and says: [![Build Status](https://secure.travis-ci.org/markushausammann/monitorix.png)](http://travis-ci.org/markushausammann/monitorix)

## What's nice about monitorix

* integrated logging of php errors, exceptions, javascript errors, slow queries and more
* log entries of all your apps in one filterable, sortable, queryable place (database)
* very easy to set up
* doesn't interfere with your default error reporting settings

## Features
Monitorix is meant to offer a *light but free* alternative to the commercial monitoring solution integrated in Zend Server.

### Current Features
* easy setup and integration with your existing Zend Framework applications
* minimal bootstrapping and configuration
* simple message logging
* automated logging of php errors (optional)
* '''NEW''': automated logging of fatal php errors (optional)
* automated logging of uncaught exceptions (optional)
* automated logging of slow database queries (optional)
* '''NEW''': automated logging of javascript errors (optional)
* fully unit tested

## Prerequisites
* Zend Framework 1.10 or newer
* PHP 5.3 or newer

Needed for javascript error logging:
* ZendX_JQuery or your own implementation of jQuery (more specifially, the jQuery Ajax plugin is needed.)

Needed for running the unit tests:
* phpUnit
* [https://github.com/padraic/mockery Mockery]

## Installation and usage

### Installation

### Usage
Find instructions [https://github.com/markushausammann/monitorix/blob/master/HOW-TO-USE.mediawiki here].

## Contributions
This project is developed by the team of www.cloud-solutions.net.

Additional contributions have been made by:
* Ritesh Kumar Sahu - Fatal error logging through use of register_shutdown_function

You're welcome to contribute to the component! Just contact us, if you're interested!