<?php

	lt_include( PLOG_CLASS_PATH."class/dao/customfields/customfields.class.php" );
	lt_include( PLOG_CLASS_PATH."class/dao/customfields/customfieldsvalues.class.php" );
	lt_include( PLOG_CLASS_PATH."class/view/admin/admincustomfieldslistview.class.php" );
	lt_include( PLOG_CLASS_PATH."class/data/validator/arrayvalidator.class.php" );
	lt_include( PLOG_CLASS_PATH."class/action/admin/adminaction.class.php" );
	lt_include( PLOG_CLASS_PATH."class/data/validator/integervalidator.class.php" );
	
    /**
     * \ingroup Action
     * @private
     *
	 * Removes a custom field, and all the values that have been given
	 * to that field in all posts
	 */
	class AdminDeleteCustomFieldsAction extends AdminAction
	{
		
		var $_fieldIds;
		
		function AdminDeleteCustomFieldsAction( $actionInfo, $request )
        {
        	$this->AdminAction( $actionInfo, $request );
			
			// data validation, which may vary depending on the parameter
			$this->_op = $actionInfo->getActionParamValue();
			if( $this->_op == "deleteCustomField" )
				$this->registerFieldValidator( "fieldId", new IntegerValidator());
			else
				$this->registerFieldValidator( "fieldIds", new ArrayValidator( new IntegerValidator()));
			$view = new AdminCustomFieldsListView( $this->_blogInfo );
			$view->setErrorMessage( $this->_locale->tr("error_incorrect_field_id"));
			$this->setValidationErrorView( $view );			
			
			$this->requirePermission( "update_custom_field" );
        }
        
		/**
		 * prepares the data for AdminDeleteCustomFieldsAction::_deleteFields
		 */
		function perform()
		{
			if( $this->_op == "deleteCustomField" ) {
				$this->_fieldId = $this->_request->getValue( "fieldId" );
				$this->_fieldIds = Array();
				$this->_fieldIds[] = $this->_fieldId;
			}
			else
				$this->_fieldIds = $this->_request->getValue( "fieldIds" );
				
			$this->_deleteFields();
		}
		
		/**
		 * @private
		 */
		function _deleteFields()
		{
			// otherwise, go through all the selected fields and remove them one by one, 
			// also removing the rows that contain the values
			$customFields = new CustomFields();
			$errorMessage = "";
			$successMessage = "";
			$totalOk = 0;
			
			$this->_view = new AdminCustomFieldsListView( $this->_blogInfo );
			
			foreach( $this->_fieldIds as $fieldId ) {
				$field = $customFields->getCustomField( $fieldId );
				
				if( $field ) {
					// fire the pre-event
					$this->notifyEvent( EVENT_PRE_CUSTOM_FIELD_DELETE, Array( "field" => &$field ));
					
					$result = $customFields->removeCustomField( $fieldId );
					if( $result ) {
						$totalOk++;
						if( $totalOk > 1 ) 
							$successMessage = $this->_locale->pr( "fields_deleted_ok", $totalOk );
						else
							$successMessage = $this->_locale->pr( "field_deleted_ok", $field->getName());
							
						// fire the post-event
						$this->notifyEvent( EVENT_POST_CUSTOM_FIELD_DELETE, Array( "field" => &$field ));
					}
					else
						$errorMessage .= $this->_locale->pr( "error_deleting_field", $field->getName())."<br/>";
				}
				else
					$errorMessage .= $this->_locale->pr( "error_deleting_field2", $fieldId )."<br/>";
			}
			
			if( $errorMessage != "" ) $this->_view->setErrorMessage( $errorMessage );
			if( $successMessage != "" ) $this->_view->setSuccessMessage( $successMessage );
			$this->setCommonData();
			
			return true;
		}
	}
?>