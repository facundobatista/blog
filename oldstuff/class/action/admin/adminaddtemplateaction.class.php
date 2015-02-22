<?php

    lt_include( PLOG_CLASS_PATH.'class/action/admin/siteadminaction.class.php' );
    lt_include( PLOG_CLASS_PATH.'class/view/admin/adminsitetemplateslistview.class.php' );
    lt_include( PLOG_CLASS_PATH.'class/view/admin/admintemplatedview.class.php' );
    lt_include( PLOG_CLASS_PATH.'class/file/unpacker/unpacker.class.php' );
    lt_include( PLOG_CLASS_PATH.'class/template/templatesandbox.class.php' );
    lt_include( PLOG_CLASS_PATH.'class/file/fileuploads.class.php' );
	lt_include( PLOG_CLASS_PATH.'class/template/templatesets/templatesetstorage.class.php' );
	lt_include( PLOG_CLASS_PATH.'class/template/templatesets/templatefinder.class.php' );

    /**
     * \ingroup Action
     * @private
     *
     * Shows a form to add a new template file
     */
    class AdminAddTemplateAction extends AdminAction
    {

        function AdminAddTemplateAction( $actionInfo, $request )
        {
            $this->AdminAction( $actionInfo, $request );

            // decide what to do based on which submit button was pressed
            if( $this->_request->getValue( "addTemplateUpload" ) != "" )
            	$this->_op = "addTemplateUpload";
            else
            	$this->_op = "scanTemplates";

			$this->requireAdminPermission( "add_template" );
        }

		/**
		 * @private
		 */
        function _checkTemplateSandboxResult( $result )
        {
            switch( $result ) {
                case ERROR_TEMPLATE_NOT_INSIDE_FOLDER:
                    $errorMessage = $this->_locale->tr('error_template_not_inside_folder');
                    break;
                case ERROR_MISSING_BASE_FILES:
                    $errorMessage =  $this->_locale->tr('error_missing_base_files');
                    break;
                case TEMPLATE_SANDBOX_ERROR_UNPACKING:
                    $errorMessage =  $this->_locale->tr('error_unpacking');
                    break;
                case TEMPLATE_SANDBOX_ERROR_FORBIDDEN_EXTENSIONS:
                    $errorMessage =  $this->_locale->tr('error_forbidden_extensions');
                    break;
                case TEMPLATE_SANDBOX_ERROR_CREATING_WORKING_FOLDER:
                    $errorMessage = $this->_locale->tr('error_creating_working_folder');
                    break;
                default:
                    $errorMessage = $this->_locale->pr('error_checking_template', $result);
                    break;
            }

            return $errorMessage;
        }

		/**
		 * installs an uploaded template
		 */
        function _performUploadTemplate()
        {
            // handle the uploaded file
            $files    = HttpVars::getFiles();
            $uploads  = new FileUploads( $files );

            if( count($files) == 0 || $files["templateFile"]["name"] == "") {
				$this->_view = new AdminTemplatedView( $this->_blogInfo, "newglobaltemplate" );
				$this->_view->setValue( "templateFolder", TemplateSetStorage::getBaseTemplateFolder());
                $this->_view->setErrorMessage( $this->_locale->tr("error_must_upload_file"));
                $this->setCommonData();
                return false;
			}

            $config =& Config::getConfig();

            $tmpFolder = $config->getValue( 'temp_folder' );

            // move it to the temporary folder
            $result = $uploads->process( $tmpFolder );

            // and from there, unpack it
            $upload   = new FileUpload( $files['templateFile'] );
			$templateName = TemplateSandbox::toTemplateSetName( $upload->getFileName() );            

			// Check the template set exist or not
			if( TemplateSets::isTemplate( $templateName ) ) {
				$this->_view = new AdminSiteTemplatesListView( $this->_blogInfo );
                $this->_view->setErrorMessage( $this->_locale->pr("error_template_exist", $templateName));
                $this->setCommonData();

                return false;
            }

            $templateSandbox = new TemplateSandbox();
            $valid = $templateSandbox->checkTemplateSet( $upload->getFileName(), $tmpFolder.'/');

            if( $valid < 0 ) {
                $this->_view = new AdminSiteTemplatesListView( $this->_blogInfo );
                $this->_view->setErrorMessage( $this->_checkTemplateSandboxResult( $valid ));
                $this->setCommonData();
                return false;
            }

            // the template was ok, so then we can proceed and move it to the main
            // template folder, add it to our array of templates

            //
            // :KLUDGE:
            //
            // maybe we should simply move the files rather than unpacking the whole
            // thing again, but this indeed makes things easier! ;)
            $unpacker = new Unpacker();
            $templateFolder = $config->getValue( 'template_folder' );
			$fileToUnpack = $tmpFolder.'/'.$upload->getFileName();
            if( !$unpacker->unpack( $fileToUnpack, $templateFolder )) {
                $this->_view = new AdminSiteTemplatesListView( $this->_blogInfo );
				$tf = new Textfilter();
                $this->_view->setErrorMessage( $this->_locale->pr('error_installing_template', $tf->filterAllHtml($upload->getFileName())));
                $this->setCommonData();
                return false;
            }

            // if the template set was installed ok in the template folder, we can record
            // it as a valid set
            $ts = new TemplateSetStorage();
            $ts->addTemplate( $templateName );

            $this->_view = new AdminSiteTemplatesListView( $this->_blogInfo );
            $this->_view->setSuccessMessage( $this->_locale->pr('template_installed_ok', $templateName));
            $this->setCommonData();

            return true;
        }


        //
        // adds a template manually
        //
        function _addTemplateCode( $templateName )
        {

            $ts = new TemplateSetStorage();

            // make sure that the template is valid
            $templateSandbox = new TemplateSandbox();
            $valid = $templateSandbox->checkTemplateFolder( $templateName, $ts->getBaseTemplateFolder());
            if( $valid < 0 ) {
				$this->_errorMessage .= $this->_locale->pr( 'error_installing_template', $templateName ).': '.$this->_checkTemplateSandboxResult( $valid ).'<br/>';
				$result = false;
			}
            else {
				// otherwise, we can add it without problems
				$ts->addTemplate( $templateName );
            	$this->_successMessage .= $this->_locale->pr( 'template_installed_ok', $templateName).'<br/>';
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
			// set up the view
            $this->_view = new AdminSiteTemplatesListView( $this->_blogInfo );

			// and tell the template finder to find any new template file...
			$tf = new TemplateFinder();
			$newTemplates = $tf->find();

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

			return true;
		}

		/**
		 * perform the action
		 */
        function perform()
        {
            if( $this->_op == 'addTemplateUpload' )
                $result = $this->_performUploadTemplate();
            elseif( $this->_op == 'scanTemplates' )
                $result = $this->_performScanTemplateFolder();
            else {
                throw( new Exception( 'You shouldn\'t be seeing this!!! :)' ));
                die();
            }

            $this->setCommonData();

            return $result;
        }
    }
?>
