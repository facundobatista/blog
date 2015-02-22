<?php

    
	
	define( "SEARCH_RESULT_ARTICLE", 1 );
	define( "SEARCH_RESULT_CUSTOM_FIELD", 2 );
	define( "SEARCH_RESULT_COMMENT", 3 );
	define( "SEARCH_RESULT_BLOG", 4 );
	define( "SEARCH_RESULT_GALLERYRESOURCE", 5 );
    
    /**
     * represents a result from a search query.
     *
     * Includes things like the relevance and a reference to the article object
     * that matched the query
	 *
	 * \ingroup DAO
     */
    class SearchResult 
    {
        
        var $_relevance;
        var $_result;
		var $_resultType;
		var $_searchTerms;
        
		/**
		 * Constructor
		 * 
		 * @param result The DbObject that was returned by the search
		 * @param type Whether the search result is coming from an article, comment or custom field
		 * @param relevance The relevance factor		 
		 * @see getType()
		 */
        function SearchResult( $result, $type, $searchTerms = "", $relevance = 0 )
        {                        
            $this->_relevance = $relevance;
            $this->_result    = $result;
			$this->_type      = $type;
        }
        
		/**
		 * returns the Article object, if any
		 *
		 * @return an Article object
		 */
        function getResult()
        {
            return $this->_result;
        }
		
		/**
		 * Alias for getResult()
		 * @see getResult
		 */
        function getArticle()
        {
            return( $this->getResult());
        }		
        
		/**
		 * returns the relevance of the result
		 *
		 * @return an integer specfying the relevance of the result
		 */
        function getRelevance()
        {
            return $this->_relevance;
        }
		
		/**
		 * returns the type of the result. 
		 *
		 * @return The returned result is one of the following values:
		 * Possible values are:
		 * - SEARCH_RESULT_ARTICLE
		 * - SEARCH_RESULT_CUSTOM_FIELD
		 * - SEARCH_RESULT_COMMENT
		 */		 
		function getType()
		{
			return $this->_type;
		}
		
		/**
		 * Returns the search terms that generated this result
		 *
		 * @return A string
		 */
		function getSearchTerms()
		{
			return( $this->_searchTerms );
		}
    }
?>