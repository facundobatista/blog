<?php

    lt_include( PLOG_CLASS_PATH."class/dao/model.class.php" );
    lt_include( PLOG_CLASS_PATH."class/dao/customfields/customfields.class.php" );
    lt_include( PLOG_CLASS_PATH."class/dao/customfields/customfieldvaluefactory.class.php" );        
	lt_include( PLOG_CLASS_PATH."class/dao/daocacheconstants.properties.php" );	

    /**
     * Model for the values given to certain custom fields
     *
     * \ingroup DAO
     */
    class CustomFieldsValues extends Model 
    {
    
        function CustomFieldsValues()
        {
            $this->Model();
            $this->table = $this->getPrefix()."custom_fields_values";
        }
        
        /**
         * @param id
         * @return A CustomFieldValue object
         */
        function getCustomFieldValue( $id )
        {
        	return( $this->get( "id", $id, CACHE_CUSTOMFIELDVALUES ));
        }
        
        /**
         * returns an array of CustomFieldValue objects with information about all
         * the custom fields available for an article
         *
         * @param articleId The id of the article
         * @param includeHidden Whether to return hidden fields or not
         * @return An array of CustomFieldValue objects, or false if error
         */
        function getArticleCustomFieldsValues( $articleId, $includeHidden = true )
        {                   
            $prefix = $this->getPrefix();
            $query = "SELECT v.id AS id, d.id AS field_id, v.field_value AS field_value, 
                             d.field_name AS field_name, d.field_type AS field_type, 
                             d.field_description AS field_description,
							 v.article_id AS article_id, v.blog_id AS blog_id
                             FROM {$prefix}custom_fields_values v 
                             RIGHT OUTER JOIN {$prefix}custom_fields_definition d 
                             ON v.field_id = d.id
							 WHERE v.article_id = '".Db::qstr($articleId)."'";
                      
            $result = $this->Execute( $query );
            
            $fields = Array();
                        
            // return empty array if no fields
            if( !$result )
                return $fields;
            
            while( $row = $result->FetchRow()) {
                $field = $this->mapRow( $row );
                $fields[$field->getName()] = $field;
            }
            $result->Close();

            return $fields;
        }

        /**
         * adds a custom field value to the given article
         *
         * @param fieldId
         * @param fieldValue
         * @param articleId
         * @param blogId
         * @return True if successful or false otherwise
         */
        function addCustomFieldValue( $fieldId, $fieldValue, $articleId, $blogId )
        {
        	// create a bogus object, we don't really need a full CustomFieldValue object
        	// but it makes it easier for us if we'd like to use Model::add()
        	$value = new CustomFieldValue( $fieldId, 
        	                               $fieldValue,
        	                               $articleId,
        	                               $blogId );
			if( $result = $this->add( $value )) {
				$this->_cache->removeData( $articleId, CACHE_CUSTOMFIELDVALUES_ARTICLE );
			}
			
			return( $result );
        }
        
        
        /**
         * removes a value of a custom field, given its id.
         *
         * @param id
         * @return True if deleted successfully or false otherwise.
         */
        function removeCustomFieldValue( $id )
        {
        	$field = $this->getCustomFieldValue( $id );
        	if( !$field )
        		return false;
        		
        	if( $result = $this->delete( "id", $id )) {
        		$this->_cache->removeData( $field->getArticleId(), CACHE_CUSTOMFIELDVALUES_ARTICLE );
        	}
        	return( $result );
        }
        
        /**
         * Removes all the values associated to a certain custom field
         *
         * @param fieldId
         * @return True if successful or false otherwise.
         */
        function removeCustomFieldValues( $fieldId )
        {
        	$result = $this->delete( "field_id", $fieldId );

        	return( true );
        }
        
        /**
         * Removes all the values associated to an article
         *
         * @param articleId
         * @return True if deleted successfully or false otherwise.
         */
        function removeArticleCustomFields( $articleId )
        {
        	if( $result = $this->delete( "article_id", $articleId )) {
				$this->_cache->removeData( $articleId, CACHE_CUSTOMFIELDVALUES_ARTICLE );
				$result = true;
			}
			
			return( $result );
        }

		/**
		 * Returns the search string needed to find custom fields based on their values
		 *
		 * @param
		 * @return
		 */
		function getSearchConditions( $searchTerms )
		{
			lt_include( PLOG_CLASS_PATH."class/dao/searchengine.class.php" );
			
			$query = SearchEngine::adaptSearchString($searchTerms);

            $query_array = explode(' ',$query);	
			
			$db =& Db::getDb();
			if( $db->isFullTextSupported()) {			
				// fast path used when FULLTEXT searches are supported
				$where_string = "(MATCH(c.normalized_value) AGAINST ('{$query}' IN BOOLEAN MODE))";				
			}
			else {
	            $where_string = "(";
	            $where_string .= "(v.normalized_value LIKE '%{$query_array[0]}%')";
	            for ( $i = 1; $i < count($query_array); $i = $i + 1) {
	                $where_string .= " AND ((v.normalized_value LIKE '%{$query_array[$i]}%'))";
	            }
	            $where_string .= " OR (v.normalized_value LIKE '%{$query}%')";
	            $where_string .= ")";
			}

			return( $where_string );
		}
        
        /**
         * @private
         */
        function mapRow( $row )
        {
            return CustomFieldValueFactory::getCustomFieldValueObject( $row );
        }
    }
?>
