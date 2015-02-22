<?php

	lt_include( PLOG_CLASS_PATH."class/action/admin/adminaction.class.php" );
    lt_include( PLOG_CLASS_PATH."class/view/admin/adminsiteblogslistview.class.php" );
    lt_include( PLOG_CLASS_PATH."class/view/admin/admineditsiteblogview.class.php" );
    lt_include( PLOG_CLASS_PATH."class/data/validator/integervalidator.class.php" );
    lt_include( PLOG_CLASS_PATH."class/dao/blogs.class.php" );

    /**
     * \ingroup Action
     * @private
     *
     * Action that shows a form to change the settings of a blog.
     */
    class AdminResendConfirmationAction extends AdminAction 
	{

    	var $_editBlogId;

    	/**
         * Constructor. If nothing else, it also has to call the constructor of the parent
         * class, BlogAction with the same parameters
         */
        function AdminResendConfirmationAction( $actionInfo, $request )
        {
        	$this->AdminAction( $actionInfo, $request );
			
			// data validation
			$this->registerFieldValidator( "blogId", new IntegerValidator());
			$view = new AdminSiteBlogsListView( $this->_blogInfo );
			$view->setErrorMessage( $this->_locale->tr("error_incorrect_blog_id" ));
			$this->setValidationErrorView( $view );
        }

        /**
         * Carries out the specified action
         */
        function perform()
        {
        	// get the blog and its settings
        	$this->_editBlogId = $this->_request->getValue( "blogId" );			
            $blogs = new Blogs();
            $blogInfo = $blogs->getBlogInfo( $this->_editBlogId);

            if( !$blogInfo ) {
            	$this->_view = new AdminSiteBlogsListView( $this->_blogInfo );
                $this->_view->setErrorMessage( $this->_locale->tr("error_incorrect_blog_id" ));
                $this->setCommonData();
                return false;
            }

			$this->notifyEvent( EVENT_BLOG_LOADED, Array( "blog" => &$blogInfo ));

			// now that we have the right blog loaded, regenerate the confirmation email
			lt_include( PLOG_CLASS_PATH."class/summary/mail/summarymailer.class.php" );
			
			// load the blog owner
			$blogOwner = $blogInfo->getOwnerInfo();
			// generate and send the right link
			SummaryMailer::sendConfirmationEmail( $blogOwner->getUsername());
			// print a confirmation message
			$this->_view = new AdminSiteBlogsListView( $this->_blogInfo );			
			$this->_view->setSuccessMessage( $this->_locale->tr( "confirmation_message_resent_ok" ));
			$this->setCommonData();

            // better to return true if everything fine
            return true;
        }
    }
?>
