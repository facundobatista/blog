<?php

    /**
     * flags used to calculate what we can do with the image
     */
    define( "GALLERY_RESOURCE_PREVIEW_AVAILABLE", 1 );

    /**
     * These are the different resource types that we can recognize.
     * They are very broad groups, tho.
     */
	define( "GALLERY_RESOURCE_ANY", 0 );
    define( "GALLERY_RESOURCE_IMAGE", 1 );
    define( "GALLERY_RESOURCE_VIDEO", 2 );
    define( "GALLERY_RESOURCE_SOUND", 3 );
    define( "GALLERY_RESOURCE_UNKNOWN", 4 );
    define( "GALLERY_RESOURCE_DOCUMENT", 5 );
    define( "GALLERY_RESOURCE_ZIP", 6 );
	
	/**
	 * when we are not referring to any album
	 */
	define( "GALLERY_NO_ALBUM", -1 );

    /**
     * default folder where resources will be stored. Every blog
     * will have its own folder under
     * $RESOURCES_STORAGE_FOLDER/X, where X is the blog identifier.
     */
    define( "DEFAULT_RESOURCES_STORAGE_FOLDER", PLOG_CLASS_PATH."./gallery/" );
	
	/**
	 * different error codes that the GalleryResources::addResource() method
	 * can return
	 */
	define( "GALLERY_ERROR_RESOURCE_TOO_BIG", -1 );
	define( "GALLERY_ERROR_RESOURCE_FORBIDDEN_EXTENSION", -2 );
	define( "GALLERY_ERROR_QUOTA_EXCEEDED", -3 );
	define( "GALLERY_ERROR_ADDING_RESOURCE", -4 );
	define( "GALLERY_ERROR_RESOURCE_NOT_WHITELISTED_EXTENSION", -10 );
	define( "GALLERY_ERROR_UPLOADS_NOT_ENABLED", -200 );
	define( "GALLERY_NO_ERROR", true );

	/**
	 * default thumbnail sizes
	 */
	define( "GALLERY_DEFAULT_THUMBNAIL_WIDTH", 120 );
	define( "GALLERY_DEFAULT_THUMBNAIL_HEIGHT", 120 );	
	define( "GALLERY_DEFAULT_MEDIUM_SIZE_THUMBNAIL_WIDTH", 640 );
	define( "GALLERY_DEFAULT_MEDIUM_SIZE_THUMBNAIL_HEIGHT", 480 );	
	
	/**
	 * other basic constants for the resizer
	 */
	define( "THUMBNAIL_OUTPUT_FORMAT_SAME_AS_IMAGE", "same" );
	define( "THUMBNAIL_OUTPUT_FORMAT_JPG", "jpg" );
	define( "THUMBNAIL_OUTPUT_FORMAT_PNG", "png" );
	define( "THUMBNAIL_OUTPUT_FORMAT_GIF", "gif" );	
	
	/**
	 * default generator
	 */
	define( "DEFAULT_GENERATOR_METHOD", "gd" );		 
?>