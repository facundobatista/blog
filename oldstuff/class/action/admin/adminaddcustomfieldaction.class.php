<?php

	lt_include( PLOG_CLASS_PATH."class/action/admin/adminaction.class.php" );
	lt_include( PLOG_CLASS_PATH."class/data/validator/stringvalidator.class.php" );
	lt_include( PLOG_CLASS_PATH."class/dao/customfields/customfields.class.php" );
	lt_include( PLOG_CLASS_PATH."class/data/validator/integervalidator.class.php" );
	lt_include( PLOG_CLASS_PATH."class/data/validator/arrayvalidator.class.php" );
	lt_include( PLOG_CLASS_PATH."class/view/admin/admintemplatedview.class.php" );
	lt_include( PLOG_CLASS_PATH."class/view/admin/admincustomfieldslistview.class.php" );

    /**
     * \ingroup Action
     * @private
     *
     * adds a custom field to the blog
     */
    class AdminAddCustomFieldAction extends AdminAction 
	{
	
		var $_fieldName;
		var $_fieldDescription;
		var $_fieldType;
		var $_fieldSearchable;
		var $_fieldHidden;

        function AdminAddCustomFieldAction( $actionInfo, $request )
        {
        	$this->AdminAction( $actionInfo, $request );
			
			// data validation
			$this->registerFieldValidator( "fieldName", new StringValidator());
			$this->registerFieldValidator( "fieldDescription", new StringValidator());
			$this->registerFieldValidator( "fieldType", new IntegerValidator());
			$this->registerFieldValidator( "fieldSearchable", new IntegerValidator(), true );
			$this->registerFieldValidator( "fieldHidden", new IntegerValidator(), true );
            if( $this->_request->getValue( "fieldType" ) == CUSTOM_FIELD_LIST )
            {
                $this->registerFieldValidator( "fieldValues", new ArrayValidator( new StringValidator() ));
            }
			$view = new AdminTemplatedView( $this->_blogInfo, "newcustomfield" );
			$view->setErrorMessage( $this->_locale->tr("error_adding_custom_field"));
			$this->setValidationErrorView( $view );
			
			$this->requirePermission( "add_custom_field" );
        }
        
        /**
         * Carries out the specified action
         */
        function perform()
        {
			// fetch the data
			$this->_fieldName = Textfilter::filterAllHTML($this->_request->getValue( "fieldName" ));
			$this->_fieldDescription = Textfilter::filterAllHTML($this->_request->getValue( "fieldDescription" ));
			$this->_fieldType = $this->_request->getValue( "fieldType" );
			$this->_fieldSearchable = (int)($this->_request->getValue( "fieldSearchable" ) != "" );
			$this->_fieldHidden = (int)($this->_request->getValue( "fieldHidden" ) != "" );
						
			// get and pre-process the field values
			if( $this->_fieldType == CUSTOM_FIELD_LIST ) {
				$values = $this->_request->getValue( "fieldValues" );
				$this->_fieldValues = Array();				
				foreach( $values as $value ) {
					$this->_fieldValues[] = Textfilter::filterAllHTML( $value );
				}
			}
		
			$fields = new CustomFields();
			
			// build the new custom field
			$customField = new CustomField( $this->_fieldName, 
			                                $this->_fieldDescription, 
			                                $this->_fieldType,
			                                $this->_blogInfo->getId(), 
											$this->_fieldHidden, 
											$this->_fieldSearchable );			
			// save the values if this field is a list
			if( $this->_fieldType == CUSTOM_FIELD_LIST )
				$customField->setFieldValues( $this->_fieldValues );
				
			// throw the pre-event
			$this->notifyEvent( EVENT_PRE_CUSTOM_FIELD_ADD, Array( "field" => &$customField ));
			
			$result = $fields->addCustomField( $customField );
			
			if( $this->userHasPermission( "view_custom_fields" ))
				$this->_view = new AdminCustomFieldsListView( $this->_blogInfo );
			else
				$this->_view = new AdminTemplatedView( $this->_blogInfo, "newcustomfield" );
											   
			if( !$result ) {				
				$this->_view->setErrorMessage( $this->_locale->tr("error_adding_custom_field" ));
			}
			else {
				$this->_view->setSuccessMessage( $this->_locale->pr( "custom_field_added_ok", $customField->getName()));
				
				// throw the post-event if all went fine
				$this->notifyEvent( EVENT_POST_CUSTOM_FIELD_ADD, Array( "field" => &$customField ));
			}
			
			$this->setCommonData();		
		
            return true;
        }
    }
?>