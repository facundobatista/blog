<?php

    lt_include( PLOG_CLASS_PATH."class/action/action.class.php" );
    lt_include( PLOG_CLASS_PATH."class/config/config.class.php" );
    lt_include( PLOG_CLASS_PATH."class/data/validator/integervalidator.class.php" );
    lt_include( PLOG_CLASS_PATH."class/data/validator/usernamevalidator.class.php" );
    lt_include( PLOG_CLASS_PATH."class/data/validator/blognamevalidator.class.php" );
    lt_include( PLOG_CLASS_PATH."class/data/validator/domainvalidator.class.php" );

    /**
     * \ingroup Action
     *
     * Extends the BlogAction class so that some common operations for all the actions
     * can be done in one common place, for example to fetch the SessionInfo object
     * from the HTTP session.
     *
     * It is recommended that all the classes implementing actions for the public
     * side of the blog extend this one, and do not forget to call BlogView::setCommonData()
     * once done with the processing.
     */
	class BlogAction extends Action 
	{

    	var $_session;
        var $_config;
        var $_blogInfo;
        var $_locale;
        var $_pm;
        var $_articles;
		var $_userInfo;

        /**
         * Constructor. Additionally, it fetches the SessionInfo object from
         * the session information
         *
         *
         */
        function BlogAction( $actionInfo, $request )
        {
            lt_include( PLOG_CLASS_PATH."class/plugin/pluginmanager.class.php" );
            lt_include( PLOG_CLASS_PATH."class/security/pipeline.class.php" );
            lt_include( PLOG_CLASS_PATH."class/net/http/httpvars.class.php" );

            $this->Action( $actionInfo, $request );

            // we use the HttpVars package since then we can access the session object
            // independently wether we're using php ver. < 4.1.0 or not
            $session = HttpVars::getSession();
            $this->_session = $session['SessionInfo'];

            $this->_config =& Config::getConfig();
            if( !$this->_getBlogInfo() ) {
	            lt_include( PLOG_CLASS_PATH."class/view/view.class.php" );
	            lt_include( PLOG_CLASS_PATH."class/view/redirectview.class.php" );
	            
	            $this->_session->setValue( 'blogId', null );
	            $blogDoesNotExistUrl = $this->_config->getValue( "blog_does_not_exist_url" );
	            if ( empty($blogDoesNotExistUrl) )
	            	$blogDoesNotExistUrl = trim( $this->_config->getValue( "base_url" ) );
	            
				$this->_view = new RedirectView( $blogDoesNotExistUrl );
				$this->_view->render();
                die();
			}

            // save the blogid in the session
            $this->_session->setValue( 'blogId', $this->_blogInfo->getId());

			// load userinfo data if any
			$this->_userInfo = SessionManager::getUserInfoFromSession();
			
            $this->checkDateParameter();
			
            // initialize the plugin manager
            $this->_pm =& PluginManager::getPluginManager();
            $this->_pm->setBlogInfo( $this->_blogInfo );
            $this->_pm->setUserInfo( $this->_userInfo );
            
            // locale
            $this->_locale = $this->_blogInfo->getBlogLocale();

            //
            // security stuff
            //
            $pipeline = new Pipeline( $request, $this->_blogInfo );
            $result = $pipeline->process();
            //
            // if the pipeline blocked the request, then we have
            // to let the user know
            if( !$result->isValid()) {
				if( !$result->hasView()) {
					// use the default view
					lt_include( PLOG_CLASS_PATH."class/view/errorview.class.php" );
					$message = $this->_locale->tr('error_you_have_been_blocked').'<br/><br/>';
					$message .= $result->getErrorMessage();
					$this->_view = new ErrorView( $this->_blogInfo, $message );
				}
				else {
					// if the filter that forced the processing to stop provided
					// its own view, then use it				
					$this->_view = $result->getView();
				}
                $this->setCommonData();
                $this->_view->render();

                die();
            }
			
			// update the referrers, if needed
			$this->_updateReferrer();
			
			// get the "page" parameter from the request
			$this->_page = $this->_getPage();
        }
		
		/**
		 * notifies of events using the plugin manager. It also adds a couple of useful parameters!
		 *
		 * @see PluginManager
		 */
		function notifyEvent( $eventType, $params = Array())
		{
			$params[ 'from' ] = $this->_actionInfo->getActionParamValue();
			$params[ 'request' ] = $this->_request;
					
			return $this->_pm->notifyEvent( $eventType, $params );
		}

        /**
         * Saves the information from the session
         */
        function saveSession()
        {
        	//$_SESSION['SessionInfo'] = $this->_session;
            $session = HttpVars::getSession();
            $session['SessionInfo'] = $this->_session;
            HttpVars::setSession( $session );
        }

        /**
         * Sets some common information (dirty dirty...)
		 * @param copyFormValues Whether the values from fields that were registered via
		 * Action::registerFieldValidator() and Action::registerField() should be passed back to the view
		 * as variables or not. It defaults to 'false' but this parameter is useful in those cases
		 * when we would like to force an error to happen (not a validation error) and still keep the
		 * form values.
         * @see Action::setCommonData()
         */
        function setCommonData( $copyFormValues = false )
        {
            $this->_view->setValue( "Year", $this->_session->getValue( 'Year'));
            $this->_view->setValue( "Month", $this->_session->getValue( 'Month' ));
			$this->_view->setValue( "authuser", $this->_userInfo );
			$this->_view->setValue( "blog", $this->_blogInfo );
			$this->_view->setValue( "blogsettings", $this->_blogInfo->getSettings());			
            
            parent::setCommonData( $copyFormValues );
        }

        /**
         * Fetches the information for this blog from the database since we are going to need it
         * almost everywhere.
         */
        function _getBlogInfo()
        {	
            // see if we're using subdomains
            $config =& Config::getConfig();
            if( $config->getValue( "subdomains_enabled" )) {
                lt_include( PLOG_CLASS_PATH."class/net/http/subdomains.class.php" );

                $subdomainInfo = Subdomains::getSubdomainInfoFromRequest();

                if( !empty($subdomainInfo["blogdomain"]) && $this->_request->getValue( 'blogDomain' ) == "" ) {
                    $this->_request->setValue( 'blogDomain', $subdomainInfo["blogdomain"] );
                }
                if( !empty($subdomainInfo["username"]) && $this->_request->getValue( 'blogUserName' ) == "" ) {
                    $this->_request->setValue( 'blogUserName', $subdomainInfo["username"] );
                }
                if( !empty($subdomainInfo["blogname"]) && $this->_request->getValue( 'blogName' ) == "" ) {
                    $this->_request->setValue( 'blogName', $subdomainInfo["blogname"] );
                }
            }

            $val = new IntegerValidator();
    		$blogId = $this->_request->getValue( 'blogId' );
            if( !$val->validate( $blogId ))
                $blogId = "";	

            $val = new IntegerValidator();
    		$userId = $this->_request->getValue( 'userId' );
            if( !$val->validate( $userId ))
                $userId = "";	

            $val = new BlogNameValidator();
    		$blogName = $this->_request->getValue( 'blogName' );
            if( !$val->validate( $blogName ))
                $blogName = "";	

            $val = new UsernameValidator();
    		$userName = $this->_request->getValue( 'blogUserName' );
            if( !$val->validate( $userName ))
                $userName = "";	

            $val = new DomainValidator();
            $blogDomain = $this->_request->getValue( 'blogDomain' );
            if( !$val->validate( $blogDomain ))
                $blogDomain = "";	


            // if there is a "blogId" parameter, it takes precedence over the
            // "user" parameter.
            if( !$blogId && !$blogName && !$blogDomain) {
            	// check if there was a user parameter
                if( !empty($userName) ) {
                    lt_include( PLOG_CLASS_PATH."class/dao/users.class.php" );
                	// if so, check to which blogs the user belongs
                	$users = new Users();
                 	$userInfo = $users->getUserInfoFromUsername( $userName );
                    // if the user exists and is valid...
                	if( $userInfo ) {
                    	$userBlogs = $users->getUsersBlogs( $userInfo->getId(), BLOG_STATUS_ACTIVE );
						// check all the blogs and pick the first one that is owned. If none is owned, then pick a random
						// one (the last one that was processed)
                    	if( !empty($userBlogs)) {
							$i = 0;
							$found = false;
							while( $i < count($userBlogs) && !$found ) {
								$blog = $userBlogs[$i];
								if( $blog->getOwnerId() == $userInfo->getId()) $found = true;
								$i++;
							}
							$blogId = $blog->getId();
                        } 
						else {
                        	$blogId = $this->_config->getValue('default_blog_id');
                        }
                    } 
					else {
                    	$blogId = $this->_config->getValue('default_blog_id');
                    }
                }
                else {
                    // if there is no user parameter, we take the blogId from the session
                    if( $this->_session->getValue('blogId') != '' ) {
                    	$blogId = $this->_session->getValue('blogId');
                    }
                    else {
                        // get the default blog id from the database
                        $blogId = $this->_config->getValue('default_blog_id');                        
                    }
                }
            }
			
            // fetch the BlogInfo object
            lt_include( PLOG_CLASS_PATH."class/dao/blogs.class.php" );			
            $blogs = new Blogs();
            if( $blogId ) {
				lt_include( PLOG_CLASS_PATH."class/dao/blogs.class.php" );			
                $blogs = new Blogs();
                $this->_blogInfo = $blogs->getBlogInfo( $blogId );
            }
            else if($blogName) {
				lt_include( PLOG_CLASS_PATH."class/dao/blogs.class.php" );			
                $blogs = new Blogs();
                $this->_blogInfo = $blogs->getBlogInfoByName( $blogName );
            }
            else if($blogDomain) {
				lt_include( PLOG_CLASS_PATH."class/dao/blogs.class.php" );
                $blogs = new Blogs();
                $this->_blogInfo = $blogs->getBlogInfoByDomain( $blogDomain );
            }
            else {
                $this->_blogInfo = false;
            }

            $blogExists = true;
            
            // security checks...
            if( $this->_blogInfo == false ) {
				$blogExists = false;
            } else {
	            // non-active blogs shouldn't be shown either!
	            if( $this->_blogInfo->getStatus() != BLOG_STATUS_ACTIVE )
	            	$blogExists = false;
            }
            return $blogExists;
        }

        /**
         * Checks if there is at least a year and a month in the request
         */
        function checkDateParameter()
        {
        	$date = $this->_request->getValue( 'Date' );
        	$val = new IntegerValidator();
        	if( $date && $val->validate( $date ) ) {
            	$year = substr( $date, 0, 4);
                $month = substr( $date, 4,2 );
                $day = substr( $date, 6, 2);
            }
            else {
                // to much overhead for just getting the current date
                // :TODO: but we might need to read the timezone to enter a valid date.. not sure ..
            	// $t = new Timestamp();
                // $year = $t->getYear();
                $year = date('Y');
                // $month = $t->getMonth();
                $month = date('m');
                // $day = $t->getDay();
                $day = date('d');
            }

            $this->_session->setValue( 'Year', $year );
            $this->_session->setValue( 'Month', $month );
            $this->_session->setValue( 'Day', $day );
        }
		
		/**
		 * updates the referrers but only if there is no $articleId parameter in the request. If that's the case,
		 * it will be left up to the correct action to take care of updating the referrers
		 *
		 * @private
		 */
		function _updateReferrer()
		{
			if( $this->_request->getValue( "articleId" ) != "" || 
                $this->_request->getValue( "articleName" ) != "" ) 
				return true;
				
        	// update the referer statistics
            lt_include( PLOG_CLASS_PATH."class/dao/referers.class.php" );

            if( $this->_config->getValue( "referer_tracker_enabled", true )) {
	            $referers = new Referers();
	            if (array_key_exists( 'HTTP_REFERER', $_SERVER ))
	                $referers->addReferer( $_SERVER['HTTP_REFERER'], 0, $this->_blogInfo->getId());
			}
			
			return true;
		}

		/**
		 * @private
		 * Caculate the correct article date period. All the action classes that can take dates in permalinks/links
		 * need to call this method to calculate the right dates based on the URL and the current
		 * time offset settings (such as ViewArticleAction or ViewArticleTrackbacksAction)
		 */		        
        function _getCorrectedDatePeriod( $inDate )
        {
            $blogSettings = $this->_blogInfo->getSettings();
            $serverTimeOffset = $blogSettings->getValue( "time_offset" );
            
            if( strlen($inDate) == 4 ) 
        	{
        		$year = $inDate;
        		$outDate = Timestamp::getDateWithOffset( $year."0101000000", -abs($serverTimeOffset) );
        		$maxDate = Timestamp::getDateWithOffset( $year."1231235959", abs($serverTimeOffset) );
        	} 
        	elseif ( strlen($inDate) == 6 )
        	{
                $year = substr( $inDate, 0, 4 );
                $month = substr( $inDate, 4, 2 );
                $dayOfMonth = Date_Calc::daysInMonth( $month, $year );
        		$outDate = Timestamp::getDateWithOffset( $year.$month."01000000", -abs($serverTimeOffset) );
        		$maxDate = Timestamp::getDateWithOffset( $year.$month.$dayOfMonth."235959", abs($serverTimeOffset) );
        	}
        	elseif ( strlen($inDate) == 8 )
        	{
        		$year = substr( $inDate, 0, 4 );
                $month = substr( $inDate, 4, 2 );
                $day = substr( $inDate, 6, 2 );
        		$outDate  = Timestamp::getDateWithOffset( $year.$month.$day."000000", -abs($serverTimeOffset) );
                $maxDate = Timestamp::getDateWithOffset( $year.$month.$day."235959", abs($serverTimeOffset) );
        	}
        	else
        	{
        		$maxDate = -1;
        		$outDate = $inDate;
        	}
            
            $result["inDate"] = $inDate;
            $result["maxDate"] = $maxDate;
            $result["adjustedDate"] = $outDate;
            
            return( $result );
        }		
        
	    /**
	     * @private
	     */
	    function _getPage()
	    {
				// get the value from the request
				$page = HttpVars::getRequestValue( "page" );
				// but first of all, validate it
				$val = new IntegerValidator();
				if( !$val->validate( $page ))
					$page = 1;	

				return $page;	    
	    }	
    }
?>