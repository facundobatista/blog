<?php

	lt_include( PLOG_CLASS_PATH."class/dao/customfields/customfields.class.php" );
	lt_include( PLOG_CLASS_PATH."class/action/admin/adminaction.class.php" );
	lt_include( PLOG_CLASS_PATH."class/view/admin/admincustomfieldslistview.class.php" );
	lt_include( PLOG_CLASS_PATH."class/view/admin/admintemplatedview.class.php" );
	lt_include( PLOG_CLASS_PATH."class/data/validator/integervalidator.class.php" );

    /**
     * \ingroup Action
     * @private
     *
     * Allows users to edit custom fields
     */
    class AdminEditCustomFieldAction extends AdminAction
	{

    	var $_fieldId;

        function AdminEditCustomFieldAction( $actionInfo, $request )
        {
        	$this->AdminAction( $actionInfo, $request );
			
			// validate data
			$this->registerFieldValidator( "fieldId", new IntegerValidator());
			$view = new AdminCustomFieldsListView( $this->_blogInfo );
			$view->setErrorMessage( $this->_locale->tr( "error_fetching_custom_field" ));
			$this->setValidationErrorView( $view );
			
			$this->requirePermission( "update_custom_field" );
        }
        
        /**
         * Carries out the specified action
         */
        function perform()
        {
        	// fetch the field information
			$this->_fieldId = $this->_request->getValue( "fieldId" );
			$fields = new CustomFields();
			$field = $fields->getCustomField( $this->_fieldId );
			
            // show an error if we couldn't fetch the link
            if( !$field ) {
            	$this->_view = new AdminCustomFieldsListView( $this->_blogInfo );
                $this->_view->setErrorMessage( $this->_locale->tr("error_fetching_custom_field"));
            }
			else {
				// fire the field loaded event, in case any plugin wants to do anything with it...
				$this->notifyEvent( EVENT_CUSTOM_FIELD_LOADED, Array( "field" => &$field ));
			
				$this->_view = new AdminTemplatedView( $this->_blogInfo, "editcustomfield" );
				$this->_view->setValue( "fieldName", $field->getName());
				$this->_view->setValue( "fieldDescription", $field->getDescription());
				$this->_view->setValue( "fieldId", $field->getId());
				$this->_view->setValue( "fieldType", $field->getType());
				$this->_view->setValue( "fieldSearchable", true );
				$this->_view->setValue( "fieldHidden", $field->isHidden());
				$this->_view->setValue( "fieldValues", $field->getFieldValues());
			}
			
			$this->setCommonData();

            // better to return true if everything fine
            return true;
        }
    }
?>
