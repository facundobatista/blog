<?php

	lt_include( PLOG_CLASS_PATH."class/summary/action/summaryaction.class.php" );
	lt_include( PLOG_CLASS_PATH."class/summary/view/summarycachedview.class.php" );
	lt_include( PLOG_CLASS_PATH."class/summary/data/validator/customsummarypagevalidator.class.php" );

	/**
	 * displays custom pages in the summary.php, in case users would like
	 * to add something extra to the whole set of pages
	 *
	 * It will check whether the page requested is one of the default ones and in that
	 * case, it will *not* show it. It will also perform some sanity checks on the file
	 * name.
	 */
	class SummaryCustomPageAction extends SummaryAction
	{
	
		var $_page;
		var $_error;
		
		function perform()
		{
			$this->_page = $this->_request->getValue( "page" );
			$this->_error = $this->_request->getValue( "error", false );
			
			$val = new CustomSummaryPageValidator();
			if( !$val->validate( $this->_page )) {
				// instead of showing an ugly smarty error, let's forward processing
				// to the default action so that at least we can show something!
				SummaryController::setForwardAction( "Default" );
			}
			else {
				// let's cache the page... After all, we're not expecting much dynamic context in here!
				$this->_view = new SummaryCachedView( $this->_page, Array( "page" => $this->_page, "locale" => $this->_locale->getLocaleCode()));
				
				// we can also have custom pages that return 404 errors, just specify the "error" parameter in the URL
				if( $this->_error ) 
					$this->_view->addHeaderResponse( "HTTP/1.1 404 Not Found" );
				
				$this->setCommonData();
			}
			
			return( true );
		}
	}
?>