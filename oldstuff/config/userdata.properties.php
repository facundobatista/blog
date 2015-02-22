<?php
####
# Please do not edit these configuration settings unless you have read
# and fully understood the documentation for user data providers:
# http://wiki.lifetype.net/index.php/User_data_providers
####

#
# Default user data provider, LifeType's own one
#
$config = Array( 
  "provider" => "LifeTypeUserDataProvider",
);

#
# PHPBB2 user data provider
#
/*$config = Array( 
  "provider" => "PhpBB2UserDataProvider",
  "createBlogIfNotExisting" => true,
  "database" => "phpbb2",
  "user" => "root",
  "password" => "",
  "prefix" => "phpbb_"
);*/

#
# Simple PostNuke user data provider
#
/*$config = Array( 
  // general
  "provider" => "SimplePostNukeUserDataProvider",
  "createBlogIfNotExisting" => true,
  
  // PostNuke db connection
  "host" => "localhost",
  "database" => "postnuke76",
  "user" => "root",
  "password" => "",
  "prefix" => "pn_",
  
  // This string gets appended to the username and results
  // in "Username's Weblog"
  "blogtitle_postfix" => "'s Weblog"
);*/

# 
# VBB3 user data provider 
# 
/*$config = Array( 
  "provider" => "vbb3UserDataProvider", 
  "createBlogIfNotExisting" => false, 
  "database" => "bbs",            //vbb database name 
  "user" => "bbs",               //vbb data base user name 
  "password" => "",         //vbb data base user password 
  "prefix" => "",                 //vbb data base prefix 
  "usesalt" => true,             //use password salt or not , if vbb3 ,plz set to true 
  "allowgroup" => Array(2,5,6,7), //default set to 2,5,6,7 
  "denygroup" => Array(3,4),       //default set to 3,4 
  "admingroup"  => Array(6),      //default set to 6 
  "adminuser" => Array(1)        //default set to 1 
);*/

# 
# WBB2 user data provider 
# 
/*$config = Array( 
  // general 
  "provider" => "WBBUserDataProvider", 
  "createBlogIfNotExisting" => true, 
  
  // WBB2 connection 
  "host" => "localhost",                       //database server default: localhost 
  "database" => "",                            //database name 
  "user" => "",                                //database user 
  "password" => "",                            //user-password 
  "prefix" => "bb1_",                          //wbb2-prefix default: bb1_ 
  "admingroup"  => Array('1','11','42'),       //groupcombinationIDs for Admin default: 1,11,42 
  "blogtitle_postfix" => "'s Blog"              // This string gets appended to the username in blogtitle: "Username's Blog" 
);
*/

#
# JOOMLA user data provider
#
#
# Please run the following SQL query in your LifeType database,
# as it is required by the provider
#
# CREATE TABLE `lt_joomla_users` (
#  `id` int(10) unsigned NOT NULL auto_increment,
#  `joomla_id` int(10) unsigned NOT NULL,
#  `about` text,
#  `properties` text NOT NULL,
#  `resource_picture_id` int(10) NOT NULL default '0',
#  `blog_site_admin` int(10) NOT NULL default '0',
#  PRIMARY KEY  (`id`),
#  UNIQUE KEY `joomla_id` (`joomla_id`)
# ) ENGINE=MyISAM AUTO_INCREMENT=1 ;
/*
$config = Array( 
  // general
  "provider" => "JoomlaUserDataProvider",
  "createBlogIfNotExisting" => true,

  // Joomla db connection
  "host" => "mysql_host",
  "database" => "joomla_db",
  "user" => "joomla_db_user",
  "password" => "joomla_db_pass",
  "prefix" => "jos_",

  // This string gets appended to the username and results
  // in "Username's Weblog"
  "blogtitle_postfix" => "'s Weblog"
);
*/

?>
