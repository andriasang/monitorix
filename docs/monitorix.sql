-- The following queries can help you setup the database for monitorix
-- Please read all comments in this file before executing anything
-- $Id: monitorix.sql 51 2011-09-20 00:07:57Z markushausammann@gmail.com $

-- Create a database called monitorix
CREATE DATABASE `monitorix` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;

-- Create a table called logentries with the needed columns
CREATE TABLE IF NOT EXISTS `logentries` (
  `entryID` int(11) NOT NULL AUTO_INCREMENT,
  `logType` varchar(50) NOT NULL DEFAULT 'default',
  `projectName` varchar(20) NOT NULL DEFAULT 'not available',
  `environment` varchar(15) NOT NULL DEFAULT 'not available',
  `priority` int(11) NOT NULL,
  `errorNumber` int(11) DEFAULT NULL,
  `message` text NOT NULL,
  `file` varchar(255) DEFAULT NULL,
  `line` int(11) DEFAULT NULL,
  `context` longtext,
  `stacktrace` longtext,
  `timestamp` varchar(30) NOT NULL,
  `priorityName` varchar(15) NOT NULL,
  PRIMARY KEY (`entryID`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 ;

-- Create a specific monitorix user and grant access to monitorix database
-- You have to replace the *** with your password
CREATE USER 'monitorix'@'localhost' IDENTIFIED BY  '***';

-- Don't grant any global privileges
-- You have to replace the *** with your password
GRANT USAGE ON * . * TO  'monitorix'@'localhost' IDENTIFIED BY  '***' WITH MAX_QUERIES_PER_HOUR 0 MAX_CONNECTIONS_PER_HOUR 0 MAX_UPDATES_PER_HOUR 0 MAX_USER_CONNECTIONS 0 ;

-- Grant all privileges on the monitorix database
GRANT ALL PRIVILEGES ON  `monitorix` . * TO  'monitorix'@'localhost';