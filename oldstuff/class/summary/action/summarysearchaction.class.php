<?php

	lt_include( PLOG_CLASS_PATH."class/summary/action/summaryaction.class.php" );
	lt_include( PLOG_CLASS_PATH."class/summary/dao/summarystatsconstants.class.php" );
	lt_include( PLOG_CLASS_PATH."class/data/validator/stringvalidator.class.php" );
	lt_include( PLOG_CLASS_PATH."class/data/validator/integervalidator.class.php" );

     class SummarySearchAction extends SummaryAction
     {
		var $_searchTerms;
		var $_searchType;

        function SummarySearchAction( $actionInfo, $request )
        {
            $this->SummaryAction( $actionInfo, $request );			

			// validation stuff
			$this->registerFieldValidator( "searchTerms", new StringValidator( true ));
			$this->registerFieldValidator( "searchType", new IntegerValidator());
			
			$view = new SummaryView( "summaryerror" );
			$view->setErrorMessage( $this->_locale->tr("error_incorrect_search_terms" ));
			$this->setValidationErrorView( $view );
        }
		
        /**
         * carry out the search and execute it
         */
        function perform()
        {
            lt_include( PLOG_CLASS_PATH."class/data/filter/htmlfilter.class.php" );
			lt_include( PLOG_CLASS_PATH."class/dao/searchengine.class.php" );	
			lt_include( PLOG_CLASS_PATH."class/data/pager/pager.class.php" );
			lt_include( PLOG_CLASS_PATH."class/data/timestamp.class.php" );
	
            $this->_searchTerms = $this->_request->getFilteredValue( "searchTerms", new HtmlFilter());

            if(trim($this->_searchTerms) == ""){
                $this->_view = new ErrorView( $this->_blogInfo, "error_incorrect_search_terms" );
                $this->setCommonData();
                return false;
            }
			$this->_searchType  = $this->_request->getValue( "searchType" );	

			// check if the search feature is disabled in this site...
			$config =& Config::getConfig();
			if( !$config->getValue( "search_engine_enabled" )) {
				$this->_view = new ErrorView( $this->_blogInfo, "error_search_engine_disabled" );
				$this->setCommonData();
				
				return false;
			}

            $search = new SearchEngine();

			// number of items per page
			$config =& Config::getConfig();
			$itemsPerPage = $config->getValue( "summary_items_per_page", SUMMARY_DEFAULT_ITEMS_PER_PAGE );
			
			// execute the search and check if there is any result
			$results = $search->siteSearch( $this->_searchTerms, 
				                            $this->_searchType, 
				                           -1, 
										   false,
				                           View::getCurrentPageFromRequest(),
				                           $itemsPerPage );
				
			// get the total number of results, for the pager
			$numResults = $search->getNumSiteSearchResults( $this->_searchTerms, $this->_searchType, -1, false );

			// no results
			if( !$results || empty($results)) {
				// if not, then quit
				$this->_view = new SummaryView( "summaryerror" );
				$this->_view->setErrorMessage( $this->_locale->tr("error_no_search_results" ));
				return false;	
			}
			
			// but if so, then continue...
			$this->_view = new SummaryView( "searchresults" );
			$this->_view->setValue( "searchresults", $results );
			$this->_view->setValue( "searchtype", $this->_searchType );
			
			// finally, set up the pager and pass it to the view
			$pager = new Pager( "?op=summarySearch&amp;searchTerms=".$this->_searchTerms."&amp;searchType=".$this->_searchType."&amp;page=",
			                    View::getCurrentPageFromRequest(),
			                    $numResults,
			                    $itemsPerPage 
				              );
			$this->_view->setValue( "pager", $pager );
			
			$this->setCommonData();			
			
			return true;
        }
     }
?>