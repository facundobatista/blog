<?php

	lt_include( PLOG_CLASS_PATH."class/action/admin/adminaction.class.php" );
    lt_include( PLOG_CLASS_PATH."class/view/admin/admintemplatedview.class.php" );
    lt_include( PLOG_CLASS_PATH."class/view/admin/adminerrorview.class.php" );
    lt_include( PLOG_CLASS_PATH."class/template/templatesets/templatesetstorage.class.php" );

    /**
     * \ingroup Action
     * @private
     *
     * Shows a form to add a new locale file
     */
    class AdminNewBlogTemplateAction extends AdminAction 
    {

    	function AdminNewBlogTemplateAction( $actionInfo, $request )
        {
        	$this->AdminAction( $actionInfo, $request );
        	
        	$this->requirePermission( "add_blog_template" );
        }

        function perform()
        {
        	if( $this->_config->getValue( "users_can_add_templates" ) == true ) {	        	
        		$this->_view = new AdminTemplatedView( $this->_blogInfo, "newblogtemplate" );
        		$this->_view->setValue( "templateFolder", TemplateSetStorage::getBlogBaseTemplateFolder( $this->_blogInfo->getId()));
            	$this->setCommonData();
            }
            else {
            	$this->_view = new AdminErrorView( $this->_blogInfo );
                $this->_view->setMessage( $this->_locale->tr("error_add_template_disabled"));
                $this->setCommonData();
            }

            return true;
        }
    }
?>
