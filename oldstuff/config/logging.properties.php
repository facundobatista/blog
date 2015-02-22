<?php
#
# configuration for the default logger. If it is generating
# too much logs for your tatest, set "appender" to "null". This will
# be disabled once the final version is out anyway...
#

$config["default"] = Array( 
    "layout"   => "%d %N - [%f:%l (%c:%F)] %m%n", 
    "appender" => "null",
	"file"     => PLOG_CLASS_PATH."tmp/lifetype.log",
    "prio"     => "debug"
  );

// debug output sent to debug.log by default. 
  
$config["debug"] = Array( 
    "layout"   => "%t%n%d %N - [%f:%l (%c:%F)] %m%n", 
    "appender" => "null",
	"file"     => PLOG_CLASS_PATH."tmp/debug.log",
    "prio"     => "info"  
  );
#
# this logger is the only one enabled by default
# and it will log all sql queries that generate an error
# to the file tmp/sql_error.log
#
$config["sqlerr"] = Array( 
    "layout" => "%S%n %d %N - %m%n", 
    "appender" => "file",
	"file" => PLOG_CLASS_PATH."tmp/sql_error.log",
    "prio" => "error"
  );
  
#
# special logger for the trackback.php script, it sends the data to 
# tmp/trackback.log
#
$config["trackback"] = Array(
    "layout" => "%d %N - [%f:%l (%c:%F)] %m%n", 
    "appender" => "null",
	"file" => PLOG_CLASS_PATH."tmp/trackback.log",
    "prio" => "debug"
  );
  
#
# special logger for metrics and performance statistics. This file is a CSV file and the
# columns have the following meaning:
#
# timestamp (14-digit)
# memory usage
# total execution time
# number of included files
# number of SQL queries executed
# total number of cache queries
# total number of cache hits
# total number of cache misses
# script being executed
# URL being processed
#
$config["metricslog"] = Array(
    "layout" => "%m%n", 
    "appender" => "null",
	"file" => PLOG_CLASS_PATH."tmp/metrics.log",
    "prio" => "debug"
  );  

?>
