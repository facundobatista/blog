<?php

    /**
     * @package action
     */
    lt_include( PLOG_CLASS_PATH."class/action/blogaction.class.php" );
    lt_include( PLOG_CLASS_PATH."class/dao/searchengine.class.php" );
	lt_include( PLOG_CLASS_PATH."class/view/blogtemplatedview.class.php" );
    lt_include( PLOG_CLASS_PATH."class/data/validator/stringvalidator.class.php" );
    lt_include( PLOG_CLASS_PATH."class/data/filter/htmlfilter.class.php" );
    lt_include( PLOG_CLASS_PATH."class/net/http/httpvars.class.php" );
	lt_include( PLOG_CLASS_PATH."class/view/errorview.class.php" );
	lt_include( PLOG_CLASS_PATH."class/data/pager/pager.class.php" );

	define( "VIEW_SEARCH_TEMPLATE", "searchresults" );

    class SearchAction extends BlogAction
    {
        var $_searchTerms;
    
        function SearchAction( $actionInfo, $request )
        {
            $this->BlogAction( $actionInfo, $request );
			
			// data validation
			$this->registerFieldValidator( "searchTerms", new StringValidator( true ));
			$this->setValidationErrorView( new ErrorView( $this->_blogInfo, "error_incorrect_search_terms" ));
        }
		
        function perform()
        {
			// get the search terms that have already been validated...
            $this->_searchTerms = $this->_request->getFilteredValue( "searchTerms", new HtmlFilter());

            if(trim($this->_searchTerms) == ""){
                $this->_view = new ErrorView( $this->_blogInfo, "error_incorrect_search_terms" );
                $this->setCommonData();
                return false;
            }
            
			// check if the search feature is disabled in this site...
			$config =& Config::getConfig();
			if( !$config->getValue( "search_engine_enabled" )) {
				$this->_view = new ErrorView( $this->_blogInfo, "error_search_engine_disabled" );
				$this->setCommonData();
				
				return false;
			}
			
			// create the view and make sure that it hasn't been cached
            $this->_view = new BlogTemplatedView( $this->_blogInfo, VIEW_SEARCH_TEMPLATE, Array( "searchTerms" => $this->_searchTerms, "page" => $this->_page ));
			if( $this->_view->isCached()) {
                $this->setCommonData();
				return true;
			}
			
			// calculate how many results per page
			$blogSettings = $this->_blogInfo->getSettings();
            $itemsPerPage = $blogSettings->getValue( "show_posts_max" );
			
			// get the array with the results
            $searchEngine = new SearchEngine();
            $searchResults = $searchEngine->search( $this->_blogInfo->getId(), 
                                                    $this->_searchTerms,
													POST_STATUS_PUBLISHED,
													false,
													$this->_page,             // page
													$itemsPerPage   // items per page
												   );
			// and the total number of items
			$numSearchResults = $searchEngine->getNumSearchResults( $this->_blogInfo->getId(),
			                                                        $this->_searchTerms,
			                                                        POST_STATUS_PUBLISHED,
			                                                        false );
																		
            // if no search results, return an error message
            if( count($searchResults) == 0 ) {
                $this->_view = new ErrorView( $this->_blogInfo, "error_no_search_results" );
                $this->setCommonData();
                
                return true;
            }

            // if only one search result, we can see it straight away 
            if( count($searchResults) == 1 && $numSearchResults == 1 ) {
				// only one search result, we can redirect the view via the URL,
				// so that the right permalink appears in the address bar
                $searchResult = array_pop( $searchResults );
                $article = $searchResult->getResult();
				$url = $this->_blogInfo->getBlogRequestGenerator();
				// we need to deactivate the XHTML mode of the request generator or else
				// we'll get things escaped twice!
				$url->setXHTML( false );
				$permalink = $url->postPermalink( $article );
				
				// load the view and redirect the flow
				lt_include( PLOG_CLASS_PATH."class/view/redirectview.class.php" );
				$this->_view = new RedirectView( $permalink );
				
				return( true );
            }
            
            // or else, show a list with all the posts that match the requested
            // search terms
            $this->_view->setValue( "searchresults", $searchResults );
            // MARKWU: Now, I can use the searchterms to get the keyword
            $this->_view->setValue( "searchterms", $this->_searchTerms );
            // MARKWU:
			$config =& Config::getConfig();
            $urlmode = $config->getValue( "request_format_mode" );			
            $this->_view->setValue( "urlmode", $urlmode );
                // set the page title
            $this->_view->setPageTitle( $this->_blogInfo->getBlog()." | ".$this->_locale->tr("search_results"));

			// build the pager
	        $url = $this->_blogInfo->getBlogRequestGenerator();
			$basePageUrl = $url->getIndexUrl()."?op=Search&amp;searchTerms=".$this->_searchTerms."&amp;page=";
			$pager = new Pager( $basePageUrl,            // url to the next page
	                            $this->_page,            // current page
	                            $numSearchResults,            // total number of search results
	                            $itemsPerPage );
	
	        $this->_view->setValue( 'pager', $pager );

            $this->setCommonData();
            
            return true;
        }
    }
?>