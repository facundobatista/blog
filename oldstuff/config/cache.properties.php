<?php
# 
# Settings for the data cache. If you're unsure what this means,
# do not modify these settings. If you are planning to use 
# memcached as your cache system, please take a look below. The only two
# possible values are cache_lite and memcached
#

$config["cache_method"] = "cache_lite";

#
# cache settings for Cache_Lite
#
$config["cache_lite_cache_dir"] = "./tmp/";
$config["cache_lite_life_time"] = 604800;
$config["cache_lite_read_control"] = false;
$config["cache_lite_automatic_serialization"] = true;
$config["cache_lite_hashed_directory_level"] = 2;

#
# cache settings for Memcached
#
$config["memcached_servers"] = array( "127.0.0.1:11211" );
$config["memcached_life_time"] = 604800;
$config["memcached_debug"] = false;
$config["memcached_compress_threshold"] = 10240;
$config["memcached_persistant"] = true;

?>