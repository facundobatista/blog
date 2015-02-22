<?php

	lt_include( PLOG_CLASS_PATH."class/action/admin/adminaction.class.php" );
    lt_include( PLOG_CLASS_PATH."class/locale/locales.class.php" );
	lt_include( PLOG_CLASS_PATH."class/view/admin/adminsitelocaleslistview.class.php" );
    lt_include( PLOG_CLASS_PATH."class/file/fileupload.class.php" );
    lt_include( PLOG_CLASS_PATH."class/file/fileuploads.class.php" );
	lt_include( PLOG_CLASS_PATH."class/locale/localefinder.class.php" );

    /**
     * \ingroup Action
     * @private
     *
     * Takes care of adding new locales to the system
     */
    class AdminAddLocaleAction extends AdminAction 
	{

    	function AdminAddLocaleAction( $actionInfo, $request )
        {
        	$this->AdminAction( $actionInfo, $request );

        	// decide what to do based on which submit button was clicked
        	if( $this->_request->getValue( "addLocale" ) != "" )
        		$this->_op = "uploadLocale";
        	else
        		$this->_op = "scanLocales";

			$this->requireAdminPermission( "add_locale" );
        }

        function validate()
        {
        	// first of all, we have to see if the name of the file is valid
		if( $this->_op == "uploadLocale" ) {
	            $files = HttpVars::getFiles();

        	    $upload = new FileUpload( $files["localeFile"] );

	            if( !Locales::isValidLocaleFileName( $upload->getFileName())) {
        	    	$this->_view = new AdminTemplatedView( $this->_blogInfo, "newlocale" );
                	$this->_view->setErrorMessage( $this->_locale->tr("error_invalid_locale_file"));
	                $this->setCommonData();
        	        return false;
            	}
		}

		return true;
        }

	/**
	 * scans the locale folder looking for new locales
	 *
	 * @return always true
	 * @private
	 */
        function _performScanLocales()
        {
        	$locales = new Locales();

		// find all the new locales that we have not yet stored
		$f = new LocaleFinder();
		$newLocaleCodes = $f->find();

		// success message
		$successMessage = "";

		// set up the view
		$this->_view = new AdminSiteLocalesListView( $this->_blogInfo );

		// if there are no new locales, there's no point in doing anything!
		if( count( $newLocaleCodes ) == 0 ) {
			$this->_view->setErrorMessage( $this->_locale->tr("error_no_new_locales_found" ));
			return false;
		}

		foreach( $newLocaleCodes as $newLocaleCode ) {
			// add the locale to the config settings
			$res = $locales->addLocale( $newLocaleCode );

			// and create a success message
			$successMessage .= $this->_locale->pr("locale_added_ok", $newLocaleCode)."<br/>";
		}

		if( $successMessage != "" ) $this->_view->setSuccessMessage( $successMessage );

		return true;
        }

        function _performUploadLocale()
        {
        	// since we are here, the file name was validated to be ok, so we can
            // continue with the operation
            $files = HttpVars::getFiles();
            $uploads = new FileUploads( $files );

			$this->_view = new AdminSiteLocalesListView( $this->_blogInfo );

            // we can first of all move the file to the destionation folder
            $result = $uploads->process( $this->_config->getValue( "locale_folder" ));

            // the only thing that can happen is that the file was not correctly saved
            if( $result[0]->getError() != 0 ) {
                $this->_view->setErrorMessage( $this->_locale->tr("error_saving_locale"));
                return false;
            }

            // and once it's there, we can do as if we were adding a locale code
            $upload = new FileUpload( $files["localeFile"] );
            $res = preg_match( REGEXP_VALID_LOCALE, $upload->getFileName(), $matches );
		$localeCode = $matches[1];

		// add the file to the list of locales
	    $locales = new Locales();
	    $locales->addLocale( $localeCode );

		$this->_view->setSuccessMessage( $this->_locale->pr( "locale_added_ok", $localeCode ));

		return true;
        }

        function perform()
        {
       	    if( $this->_op == "scanLocales" )
            	$result = $this->_performScanLocales();
	    else
            	$result = $this->_performUploadLocale();

		$this->setCommonData();

            return $result;
        }
    }
?>
