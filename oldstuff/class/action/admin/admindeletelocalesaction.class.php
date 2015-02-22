<?php

	lt_include( PLOG_CLASS_PATH."class/action/admin/adminaction.class.php" );
    lt_include( PLOG_CLASS_PATH."class/view/admin/adminsitelocaleslistview.class.php" );
    lt_include( PLOG_CLASS_PATH."class/locale/locales.class.php" );
    lt_include( PLOG_CLASS_PATH."class/data/validator/arrayvalidator.class.php" );
    lt_include( PLOG_CLASS_PATH."class/data/validator/stringvalidator.class.php" );
	lt_include( PLOG_CLASS_PATH."class/data/filter/htmlfilter.class.php" );	

    /**
     * \ingroup Action
     * @private
     */
    class AdminDeleteLocalesAction extends AdminAction
    {
    	var $_op;
    	var $_localeIds;

    	function AdminDeleteLocalesAction( $actionInfo, $request )
        {
        	$this->AdminAction( $actionInfo, $request );

        	$this->_op = $actionInfo->getActionParamValue();
        	if( $this->_op == "deleteLocale" )
        		$this->registerFieldValidator( "localeId", new StringValidator());
        	else
        		$this->registerFieldValidator( "localeIds", new ArrayValidator( new StringValidator()));
        	$view = new AdminSiteLocalesListView( $this->_blogInfo );
        	$view->setErrorMessage( $this->_locale->tr("error_no_locales_selected"));
        	$this->setValidationErrorView( $view );

			$this->requireAdminPermission( "update_locale" );
        }

        function perform()
        {
			if( $this->_op == "deleteLocale" ) {
				$localeId = $this->_request->getValue( "localeId" );
				$this->_localeIds = Array();
				$this->_localeIds[] = $localeId;
			}
			else
				$this->_localeIds = $this->_request->getValue( "localeIds" );

			$this->_deleteLocales();

			return true;
        }

		/**
		 * @private
		 */
        function _deleteLocales()
        {

            $locales = new Locales();

            // if there is only one locale available in the system, we should
            // not allow to remove it either
            if( count($locales->getAvailableLocales()) == 1 ) {
            	$this->_view = new AdminSiteLocalesListView( $this->_blogInfo );
                $this->_view->setErrorMessage( $this->_locale->tr("error_deleting_only_locale"));
                $this->setCommonData();

                return false;
            }

            // keep the value of the default locale because plog should not allow
            // to remove the default one
            $defaultLocale = $this->_config->getValue( "default_locale" );

            $errorMessage = "";
            $successMessage = "";
            $totalOk = 0;
			$f = new HtmlFilter();	

            foreach( $this->_localeIds as $localeId ) {
            	if( $localeId != $defaultLocale ) {
					$localeId = $f->filter( $localeId );	
            		if( $locales->removeLocale( $localeId )) {
            			$totalOk++;
            			if( $totalOk < 2 )
            				$successMessage = $this->_locale->pr( "locale_deleted_ok", $localeId );
            			else
            				$successMessage = $this->_locale->pr( "locales_deleted_ok", $totalOk );
            		}
                    else
                    	$errorMessage .= $this->_locale->pr("error_deleting_locale", $localeId)."<br/>";
                }
                else {
                    $errorMessage .= $this->_locale->pr("error_locale_is_default", $localeId)."<br/>";
                }
            }

            $this->_view = new AdminSiteLocalesListView( $this->_blogInfo );
			if( $errorMessage != "" ) $this->_view->setErrorMessage( $errorMessage );
			if( $successMessage != "" ) $this->_view->setSuccessMessage( $successMessage );
            $this->setCommonData();

            return true;
        }
    }
?>
