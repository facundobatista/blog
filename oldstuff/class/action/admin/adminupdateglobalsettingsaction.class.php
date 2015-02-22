<?php

	lt_include( PLOG_CLASS_PATH."class/action/admin/adminaction.class.php" );
    lt_include( PLOG_CLASS_PATH."class/view/admin/adminglobalsettingslistview.class.php" );
	lt_include( PLOG_CLASS_PATH."class/data/validator/integervalidator.class.php" );
	lt_include( PLOG_CLASS_PATH."class/data/validator/stringvalidator.class.php" );
	lt_include( PLOG_CLASS_PATH."class/data/validator/arrayvalidator.class.php" );

    /**
     * \ingroup Action
     * @private
     *
     * Updates the settings of the site
     */
    class AdminUpdateGlobalSettingsAction extends AdminAction 
    {

    	var $_newConfigOpts;

    	function AdminUpdateGlobalSettingsAction( $actionInfo, $request )
        {
        	$this->AdminAction( $actionInfo, $request );

			$this->requireAdminPermission( "update_global_settings" );

			$this->registerFieldValidator( "blogId", new IntegerValidator(), true);
			$this->registerFieldValidator( "show", new StringValidator(), true);
                // TODO: how do we validate the data inside the array?
                // except for the allowed_comment_html tags, we could do a regular StringValidator
                // if we changed that field to be non-html, we'd be all set.  TODO in 2.0
			$this->registerFieldValidator( "config", new ArrayValidator() );

            $view = new AdminGlobalSettingsListView( $this->_blogInfo);
            $view->setErrorMessage( $this->_locale->tr("error_saving_site_config"));
            $this->setValidationErrorView( $view );
        }

        function validate()
        {
            $valid = parent::validate();

            if($this->_form->isFieldValid("show")){
                $view = new AdminGlobalSettingsListView($this->_blogInfo,
                                                        $this->_request->getValue( "show"));
                $view->setErrorMessage( $this->_locale->tr("error_saving_site_config"));
                $this->setValidationErrorView( $view );
            }

            if(!$valid){
                    // since we switched the view, we need to run the error setting again
                $this->validationErrorProcessing();
                return false;
            }

	    	// all the settings come from a very nice array from the html form
            $this->_newConfigOpts = Array();
            $this->_newConfigOpts = $this->_request->getValue( "config" );

            // the xmlrpc_ping_hosts requires special treatment, since we need to
            // split the input returned from the textbox into an array
            if( isset( $this->_newConfigOpts["xmlrpc_ping_hosts"])) {
                $array = Array();
                foreach(explode( "\r\n", $this->_newConfigOpts["xmlrpc_ping_hosts"] ) as $host ) {
                	trim($host);
                	if($host != "")
                    	array_push( $array, $host );
                }
                $this->_newConfigOpts["xmlrpc_ping_hosts"] = $array;
            }

                // the custom URL strings need some extra validation
            $customUrlFormats = array(
                "permalink_format",
                "category_link_format",
                "blog_link_format",
                "archive_link_format",
                "user_posts_link_format",
                "post_trackbacks_link_format",
                "template_link_format",
                "album_link_format",
                "resource_link_format",
                "page_suffix_format");

            if(!$this->_newConfigOpts["blog_link_format"] || $this->_newConfigOpts["blog_link_format"] == "/")
                $this->_newConfigOpts["blog_link_format"] = "/$";

            foreach($customUrlFormats as $format){
                if(isset($this->_newConfigOpts[$format])){
                    $val = $this->_newConfigOpts[$format];
                    $val = str_replace("\\", "/", $val);
                    $this->_newConfigOpts[$format] = $val;
                    if(!$val){
                        $this->_form->setFieldValidationStatus( "config[$format]", false );
                        $valid = false;
                    }
                    else{
                            // whenever we have an array validator, we need to manually set
                            // the fieldValidationStatus, otherwise, they'll all be marked
                            // as invalid, whenever any one of them is invalid
                        $this->_form->setFieldValidationStatus( "config[$format]", true );
                    }
                }
            }

			// the default_blog_id setting is coming from a chooser, so it won't be automatically picked up
            $blogId = $this->_request->getValue( "blogId" );
            if($blogId)
                $this->_newConfigOpts["default_blog_id"] = $blogId;

            if(!$valid){
                $this->validationErrorProcessing();
                return false;
            }

            return true;
        }

        function perform()
        {
                // get the global setting section
            $show = $this->_request->getValue( "show" );
            
        	// we can proceed to update the config
            foreach( $this->_newConfigOpts as $key => $value ) {
                    // TODO: we shouldn't blindly save all configuration values from the user, right?
                    // An admin could fill up the config table with all sorts of garbage.
                if(is_array($value) || is_object($value))
                    $this->_config->setValue($key, $value);
                else
                    $this->_config->setValue($key, trim($value));
            }
            // and finally save everything
            $res = $this->_config->save();

            // depending on the result, we shall show one thing or another...
            if( $res ) {
            	$this->_view = new AdminGlobalSettingsListView( $this->_blogInfo, $show );
                $this->_view->setSuccessMessage( $this->_locale->tr("site_config_saved_ok"));
                $this->setCommonData();
				// clear the contents of all the caches
				CacheControl::resetAllCaches();
            }
            else {
            	$this->_view = new AdminGlobalSettingsListView( $this->_blogInfo, $show );
                $this->_view->setErrorMessage( $this->_locale->tr("error_saving_site_config"));
                $this->setCommonData();
            }

            return $res;
        }
    }
?>
