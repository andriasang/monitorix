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
 * @version    $Id: bootstrap.php 51 2011-09-20 00:07:57Z markushausammann@gmail.com $
 */

// Define application environment
defined('APPLICATION_ENV')
    || define('APPLICATION_ENV', (getenv('APPLICATION_ENV') ? getenv('APPLICATION_ENV') : 'testing'));

// Ensure library/ is on include_path
set_include_path(implode(PATH_SEPARATOR, array(
    dirname(dirname( __FILE__ )) . '/library/',
    dirname(__FILE__) . '/library/',
    get_include_path()
)));

date_default_timezone_set('Europe/Zurich');

if (defined('TRAVIS'))
{
    require '../vendor/.composer/autoload.php';
}
else
{
    require_once 'Mockery/Loader.php';
    require_once 'Hamcrest/Hamcrest.php';
}

$loader = new \Mockery\Loader;
$loader->register();