<?php

	lt_include( PLOG_CLASS_PATH."class/action/blogaction.class.php" );
    lt_include( PLOG_CLASS_PATH."class/view/templateview.class.php" );
    lt_include( PLOG_CLASS_PATH."class/data/validator/templatenamevalidator.class.php" );
    lt_include( PLOG_CLASS_PATH."class/view/errorview.class.php" );

    /**
     * \ingroup Action
     * @private
     *
     * This action shows additional templates that can be created by the user.
     * This templates are not related to any pre-defined action but can defined according
     * to the user's taste, and no additional PHP code is required in order to develop new
     * templates. The templates will receive a pre-defined set of objects like any other
     * template (the calendar,
     * the recent posts, all the categories, etc) and will also receive the parameters from the
     * request in case they're necessary (they can be ignored otherwise)
     * <br>
     * This class behaves like any other Action and is handled by the controller.<br>
     * <br>
     * The advantage of using this approach is that we do not need to define an additional
     * custom class to implement an Action when what we want to do is very simple, such as showing
     * some more content or extra pages. From within the templates we can also change the content
     * type HTTP header so that we can send for instance xml content. That could be useful in case we
     * want to add things like FOAF (Friend-Of-A-Friend) which are XML-based and which have not yet
     * been included in the main distribution. The TemplateUtils class, referred as "utils" in the
     * template context, is the one which provides such features.
     */
    class TemplateAction extends BlogAction 
    {

    	/**
         * Constructor.
         *
         * @param actionInfo The same ActionInfo parameter taken by the BlogAction class constructor.
         * @param request The same as in the BlogAction class constructor.
         */
    	function TemplateAction( $actionInfo, $request )
        {
        	$this->BlogAction( $actionInfo, $request );

        	$this->registerFieldValidator( "show", new TemplateNameValidator());

            $view = new ErrorView( $this->_blogInfo );
            $view->setErrorMessage( "Bad characters in the template name." );
            $this->setValidationErrorView( $view );
        }

        /**
         * Performs the operation.
         *
         * It needs an additional parameter named "show" which gives the relative
         * path, starting from the folder where the templates are to be found, where
         * the template file is. <b>Security checks are done to ensure that no things like
         * '../../../some/other/file'</b> are accepted.
         */
        function perform()
        {
                // get the value of the template we're trying to render
        	$templateFile = $this->_request->getValue( "show" );
                // then, check if it has any extraneous character
            if( !$templateFile || strstr( $templateFile, ".." )) {
                lt_include( PLOG_CLASS_PATH."class/view/errorview.class.php" );

        	    $view = new ErrorView( $this->_blogInfo, "error_parameter_missing" );
        	    $this->setValidationErrorView( $view );

            	$this->_view = new ErrorView( $this->_blogInfo );
                $this->_view->setValue( "message", "error_incorrect_parameter" );
                $this->setCommonData();

                return false;
            }	        
        	// get the name of the template file and create the view
        	$this->_view = new TemplateView( $this->_blogInfo, $templateFile);
                // NOTE: this passes unvalidated data to the view,
                // need to validate it in the template if you are
                // going to use it.
            $this->_view->setValue( "request", $this->_request );
            // add all the common information to the view
            $this->setCommonData();

            return true;
        }
    }
?>
