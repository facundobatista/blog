<?php

    lt_include( PLOG_CLASS_PATH."class/action/admin/adminaction.class.php" );
    lt_include( PLOG_CLASS_PATH."class/action/admin/adminaddtemplateaction.class.php" );
    lt_include( PLOG_CLASS_PATH."class/file/unpacker/unpacker.class.php" );
	lt_include( PLOG_CLASS_PATH."class/view/admin/admintemplatedview.class.php" );
	lt_include( PLOG_CLASS_PATH."class/view/admin/adminblogtemplatesetslistview.class.php" );
	lt_include( PLOG_CLASS_PATH."class/template/templatesets/templatesetstorage.class.php" );
    lt_include( PLOG_CLASS_PATH."class/template/templatesandbox.class.php" );
    lt_include( PLOG_CLASS_PATH."class/file/fileuploads.class.php" );

    /**
     * \ingroup Action
     * @private
     *
     * Shows a form to add a new locale file
     */
    class AdminAddBlogTemplateAction extends AdminAction
	{

		var $_op;

        function AdminAddBlogTemplateAction( $actionInfo, $request )
        {
            $this->AdminAction( $actionInfo, $request );

            // decide what to do based on which submit button was pressed
            if( $this->_request->getValue( "addBlogTemplate" ) != "" )
            	$this->_op = "addBlogTemplate";
            else
            	$this->_op = "scanBlogTemplates";
            	
			$this->requirePermission( "add_blog_template" );            	
        }

        function validate()
        {
            //
            // first of all, let's make sure that users are allowed to
            // add new templates
            //
            $config =& Config::getConfig();
            if( !$config->getValue( "users_can_add_templates" )) {
                $this->_view = new AdminBlogTemplateSetsListView( $this->_blogInfo );
                $this->_view->setErrorMessage( $this->_locale->tr("error_add_template_disabled"));
                $this->setCommonData();

                return false;
            }

			return parent::validate();
		}

        function _performUploadTemplate()
        {
            // get the temporary folder
            $config =& Config::getConfig();
            $tmpFolder = $config->getValue( "temp_folder" );

            // move it to the temporary folder
            $files    = HttpVars::getFiles();

            if( count($files) == 0 || $files["templateFile"]["name"] == "") {
				$this->_view = new AdminTemplatedView( $this->_blogInfo, "newblogtemplate" );
				$this->_view->setValue( "templateFolder", TemplateSetStorage::getBlogBaseTemplateFolder( $this->_blogInfo->getId()));
                $this->_view->setErrorMessage( $this->_locale->tr("error_must_upload_file"));
                $this->setCommonData();
                return false;
			}

            $uploads  = new FileUploads( $files );
            
            if( $this->userHasPermission( "view_blog_templates" ))
                $this->_view = new AdminBlogTemplateSetsListView( $this->_blogInfo );
            else
            	$this->_view = new AdminTemplatedView( $this->_blogInfo, "newblogtemplate" );

            $result = $uploads->process( $tmpFolder );
            if( $result < 0 ) {
                $this->_view->setErrorMessage( $this->_locale->tr("error_uploads_disabled"));
                $this->setCommonData();

                return false;
            }

            $upload   = new FileUpload( $files["templateFile"] );
			$templateName = TemplateSandbox::toTemplateSetName( $upload->getFileName() );            

			// Check the template set exist or not
			if( TemplateSets::isTemplate( $templateName ) || TemplateSets::isBlogTemplate( $templateName, $this->_blogInfo->getId() ) ) {
                $this->_view->setErrorMessage( $this->_locale->pr("error_template_exist", $templateName));
                $this->setCommonData();

                return false;
            }

            // and make it go through the template sandbox to check if
            // we're dealing with a 'healthy' file
            $templateSandbox = new TemplateSandbox();
            $valid = $templateSandbox->checkTemplateSet( $upload->getFileName(), $tmpFolder."/");

            if( $valid < 0 ) {
                $this->_view->setErrorMessage( AdminAddTemplateAction::_checkTemplateSandboxResult( $valid ));
                $this->setCommonData();

                return false;
            }

            //
            // :KLUDGE:
            //
            // maybe we should simply move the files rather than unpacking the whole
            // thing again, but this indeed makes things easier! ;)
            //

            // since it is a local template, the path has to be $template_folder/blog_x/$templateName
            $ts = new TemplateSetStorage();
            $blogTemplateFolder = $ts->createBlogTemplateFolder( $this->_blogInfo->getId());

            // it should be there now... we can continue
            $destFolder = $blogTemplateFolder."/";

            $unpacker = new Unpacker();
            if( !$unpacker->unpack( $tmpFolder."/".$upload->getFileName(), $destFolder )) {
                $this->_view->setErrorMessage( $this->_locale->tr("error_installing_template"));
                $this->setCommonData();

                // remove the file before returning!
                File::delete( $tmpFolder."/".$upload->getFileName());

                return false;
            }

            // if the template set was installed ok in the template folder, we can record
            // it as a valid set
            $ts->addTemplate( $templateName, $this->_blogInfo->getId());

            // remove the file
            File::delete( $tmpFolder."/".$upload->getFileName());

            $this->_view->setSuccessMessage( $this->_locale->pr("template_installed_ok", $templateName));
            $this->setCommonData();

            return true;
        }


		/**
		 * @private
		 */
		function _addTemplateCode( $templateName )
        {
            $config =& Config::getConfig();
            $templateFolder = $config->getValue( "template_folder" );

            $ts = new TemplateSetStorage();

            $this->_view = new AdminBlogTemplateSetsListView( $this->_blogInfo );

            // make sure that the template is valid
            $templateSandbox = new TemplateSandbox();
            $valid = $templateSandbox->checkTemplateFolder( $templateName, $ts->getBlogBaseTemplateFolder( $this->_blogInfo->getId()));
			if( $valid < 0 ) {
				$this->_errorMessage .= $this->_locale->pr( 'error_installing_template', $templateName ).': '.AdminAddTemplateAction::_checkTemplateSandboxResult( $valid ).'<br/>';
				$result = false;
			}
            else {
            	// otherwise, we can add it without problems
            	$ts->addTemplate( $templateName, $this->_blogInfo->getId());
	            $this->_successMessage .=  $this->_locale->pr( "template_installed_ok", $templateName )."<br/>";
	            $result = true;
            }

			$this->setCommonData();
            return $result;
        }
		
		/**
		 * scans the templates folder looking for new files
		 *
		 * @private
		 */
		function _performScanTemplateFolder()
		{
			$this->_errorMessage = "";
			$this->_successMessage = "";		
		
			// set up the view
			if( $this->userHasPermission( "view_blog_templates" ))
            	$this->_view = new AdminBlogTemplateSetsListView( $this->_blogInfo );		
            else
            	$this->_view = new AdminTemplatedView( $this->_blogInfo, "newblogtemplate" );
		
			// and tell the template finder to find any new template file...
			$tf = new TemplateFinder( TemplateSetStorage::getBlogBaseTemplateFolder( $this->_blogInfo->getId()));
			$newTemplates = $tf->find( TemplateSets::getBlogTemplates( $this->_blogInfo->getId()));
			
			$this->_errorMessage = "";
			$this->_successMessage = "";
			
			if( count($newTemplates) == 0 ) {
				// no new templates found
				$this->_errorMessage = $this->_locale->tr( 'error_no_new_templates_found' );
			}
			else {
				// now add each one of the new ones
				foreach( $newTemplates as $newTemplate ) {
					$this->_addTemplateCode( $newTemplate );
				}
			}
			
			// set the success and error messages, if any
			if( $this->_errorMessage != '' ) $this->_view->setErrorMessage( $this->_errorMessage );
			if( $this->_successMessage != '' ) $this->_view->setSuccessMessage( $this->_successMessage );
			
			$this->setCommonData();
			
			return true;
		}		

        function perform()
        {
            if( $this->_op == "addBlogTemplate" ) {
                $result = $this->_performUploadTemplate();
			}
            elseif( $this->_op == "scanBlogTemplates" ) {
                $result = $this->_performScanTemplateFolder();
			}
            else {
                throw( new Exception( "You shouldn't be seeing this!!! :)" ));
                die();
            }

            // We should update the session too, or we will get data dirty
            lt_include( PLOG_CLASS_PATH."class/dao/blogs.class.php" );
            $blogs = new Blogs();
            $blogInfo = $blogs->getBlogInfo( $this->_blogInfo->getId() );
            $this->_session->setValue( "blogInfo", $blogInfo );
            $this->saveSession();
            
            return $result;
        }
    }
?>