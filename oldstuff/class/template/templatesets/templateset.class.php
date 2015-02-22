<?php

	
	lt_include( PLOG_CLASS_PATH."class/file/file.class.php" );
	
	define( "TEMPLATE_SET_GLOBAL", 1 );
	define( "TEMPLATE_SET_BLOG_SPECIFIC", 2 );

	/**
	 * \ingroup Template
	 *
	 * abstraction for a template set
	 */
	class TemplateSet 
	{
		var $_name;
		var $_type;
		var $_blogId;
	
		/**
		 * constructor
		 *
		 * @param name The name of the template set, does not need to be unique
		 * @param type TEMPLATE_SET_GLOBAL for global templates and TEMPLATE_SET_BLOG_SPECIFIC
		 * for blog-specific templates
		 * @param blogId The blog to whom this template belongs, or 0 if it's global. 
		 */
		function TemplateSet( $name, $type, $blogId = 0)
		{
			
			
			$this->_name = $name;
			$this->_type = $type;
			$this->_blogId = $blogId;
		}
		
		/**
		 * @return the name of the template set
		 */
		function getName()
		{
			return $this->_name;
		}
		
		/**
		 * returns wether the template is global or blog-specific
		 *
		 * @return returns TEMPLATE_SET_GLOBAL if the template is global or
		 * TEMPLATE_SET_BLOG_SPECIFIC is the template is blog-specific
		 */
		function getType()
		{
			return $this->_type;
		}
		
		/**
		 * @return returns true if the template is global
		 */
		function isGlobal()
		{
			return( $this->getType() == TEMPLATE_SET_GLOBAL );
		}
		
		/**
		 * @return Returns true if the template is blog-specific
		 */
		function isBlogSpecific()
		{
			return( $this->getType() == TEMPLATE_SET_BLOG_SPECIFIC );
		}
		
		/**
		 * @return Returns the blog id of the blog to whom this blog belongs, or
		 * 0 if the template is global
		 */
		function getBlogId()
		{
			return( $this->_blogId );
		}
		
		/**
		 * returns true if the template set has an screenshot available
		 *
		 * @return true if screenshot available or false otherwise
		 */
		function hasScreenshot()
		{
			// check if the file screenshot.jpg is available in disk
			$ts = new TemplateSetStorage();
			return $ts->isScreenshotAvailable( $this->getName(), $this->getBlogId());
		}
		
		/**
		 * calculates and returns the full path to the screenshot of the template set
		 * regardless of whether it is global or blog specific
		 *
		 * @return The url to the screenshot
		 */
		function getScreenshotUrl()
		{
			$ts = new TemplateSetStorage();
			return $ts->getScreenshotUrl( $this->getName(), $this->getBlogId());
		}
	}
?>