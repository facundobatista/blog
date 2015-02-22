<?php

    define("DEFAULT_PAGER_REGS_FOR_PAGE", 25);
    define("DEFAULT_PAGER_MAX_PAGES", 1);

	/**
	 * \defgroup Pager
	 *
	 * generic implementation of a pager. It doesn't take of generating SQL queries for paging or
	 * anything like that, it only takes care of generating the number of pages based on the number
	 * of registers, keeping track of the current page, etc. 
	 *
	 * There also needs to be some display logic in order to get it to work fine.
	 *
	 * At the PHP level, some code like this is necessary:
	 *
	 * <pre>
     * $pager = new Pager( "?op=editPosts&amp;showStatus={$this->_showStatus}&amp;page=",
     *                     $this->_page, 
     *                     $numPosts, 
     *                     $this->_itemsPerPage );
     * $view->setValue( "pager", $pager );
     * </pre>
     *
     * The first parameter passed to the constructor is the string that the pager class will use
     * to generate the page links. It will only append a page number, nothing else.
     *
     * At the Smarty/template level, some code like this is necessary in order to display the links
     * properly, etc:
     *
     * <pre>
     *   {if !$pager->isFirstPage() && !$pager->isEmpty()}
     *      &lt;a class="pagerLink" href="{$pager->getPrevPageLink()}"&gt;&laquo;Prev&lt;/a&gt;&nbsp;
     *   {/if}	
     *   {foreach from=$pager->getPageLinks() item=pageLink key=pageId}
     *     {if $pageId == $pager->getCurrentPage()}
     *       &lt;span class="pagerCurrent"&gt;&nbsp;{$pageId}&nbsp;&gt;/span&lt;
     *     {else}
     *       &lt;a class="pagerLink" href="{$pageLink}"&gt;&nbsp;{$pageId}&nbsp;&lt;/a&gt;&nbsp;
     *     {/if}  
     *   {/foreach}
     *   {if !$pager->isLastPage() && !$pager->isEmpty()}
     *       &lt;a class="pagerLink" href="{$pager->getNextPageLink()}"&gt;Next&raquo;&lt;/a&gt;&nbsp;
     *   {/if}
	 * </pre>     
	 *
	 * The display logic might look a bit complex but it is unavoidable...
	 */
    class Pager 
    {
        var $_baseUrl;
        var $_totalRegs;
        var $_regsForPage;
        var $_maxPages;
        var $_totalPages;
        var $_curPage;
        var $_startPage;
        var $_endPage;
		var $_pageLinks;

		/**
		 * Constructor of the pager
		 * 
		 * @param baseUrl The base url that will be used to generate the different URLs to the pages
		 * @param curPage The current page
		 * @param totalRegs The total number of registers
		 * @param regsForPage The maximum number of registers per page, defaults to 
		 * DEFAULT_PAGER_REGS_FOR_PAGE
		 */
        function Pager($baseUrl, $curPage, $totalRegs, $regsForPage = DEFAULT_PAGER_REGS_FOR_PAGE )
        {
            $this->_baseUrl     = $baseUrl;
			$this->_curPage     = $curPage;
            $this->_totalRegs   = $totalRegs;
            $this->_regsForPage = ($regsForPage < 1) ? DEFAULT_PAGER_REGS_FOR_PAGE : $regsForPage;
            $this->_maxPages    = DEFAULT_PAGER_MAX_PAGES;
			
			$this->_init();

        }

        /**
         * Sets the base url
		 *
		 * @param url The url
         */
        function setBaseUrl($url)
        {
            $this->_baseUrl = $url;
        }

		/**
		 * Sets the current page
		 *
		 * @param curPage
		 */
		function setCurPage( $curPage )
		{
			$this->_curPage = $curPage;
			$this->_init();
		}

        /**
        * @return returns the base url
        */
        function getBaseUrl()
        {
            return $this->_baseUrl;
        }

        /**
         * @return Returns the current total amount of registers
         */
        function getTotalRegs()
        {
            return $this->_totalRegs;
        }

        /**
         * @return Returns the current number of records per page
         */
        function getRegsForPage()
        {
            return $this->_regsForPage;
        }

        /**
         * @return Returns the maximum number of pages
         */
        function getMaxPages()
        {
            return $this->_maxPages;
        }

        /**
         * @return The total number of pages
         */
        function getTotalPages()
        {
            return $this->_totalPages;
        }

        /**
         * @return The current page
         */
        function getCurrentPage()
        {
            return $this->_curPage;
        }
		
		/**
		 * returns the number of the next page
		 *
		 * @return number of the next page
		 */
		function getNextPage()
		{
			$page = $this->getCurrentPage();
			if( !$this->isLastPage())
				$page++;
		
			return $page;
		}
		
		/** 
		 * returns the number of the previous page
		 *
		 * @return number of the previous page
		 */ 
		function getPrevPage()
		{
			$page = $this->getCurrentPage();
			if( !$this->isFirstPage())
				$page--;
				
			return $page;
		}

        /**
         * @return Returns the current start page, if any
         */
        function getStartPage()
        {
            return $this->_startPage;
        }

        /**
         * @return Returns the last page
         */
        function getEndPage()
        {
            return $this->_endPage;
        }

        /**
         * @return True if the current page is the first page or false otherwise
         */
        function isFirstPage()
        {
            return ($this->_curPage == 1);
        }

        /**
         * @return Returns true if the current page is the last one or false otherwise
         */
        function isLastPage()
        {
            return ($this->_curPage == $this->_totalPages);
        }
		
		/**
		 * @return Returns an array containing all the links to the different pages
		 */
		function getPageLinks()
		{
			return $this->_pageLinks;
		}
		
		/**
		 * @return Returns a link to the next page
		 */
		function getNextPageLink()
		{
			return $this->_pageLinks[$this->getNextPage()];
		}
		
		/**
		 * @return Returns a link to the previous page
		 */
		function getPrevPageLink()
		{
			return $this->_pageLinks[$this->getPrevPage()];
		}
		
		/**
		 * @return Returns a link to current page
		 */
		function getCurrentPageLink()
		{
			return $this->_pageLinks[$this->getCurrentPage()];
		}		
		
		/**
		 * @return returns the link to the first page
		 */
		function getFirstPageLink()
		{
			return $this->_pageLinks[1];
		}
		
		/**
		 * @return returns the link to the last page
		 */
		function getLastPageLink()
		{
			return $this->_pageLinks[$this->getEndPage()];
		}
		
		/**
		 * generates the links to the different pages
		 *
		 * @return An associative array
		 */
		function generateLinks()
		{
			$i = 1;
			$pages = Array();
			
			// check whether we need to perform a replacement or not...
			// if not, we'll just append the page number at the end of the string
			$replace = strpos( $this->_baseUrl, "{page}" );
						
			while( $i <= $this->_totalPages ) {
				if( $replace ) {
					$pages[$i] = str_replace( "{page}", $i, $this->_baseUrl );
				}
				else {
					$pages[$i] = $this->_baseUrl.$i;
				}
					
				$i++;
			}
			
			return $pages;
		}
		
		/**
		 * returns true if the pager is empty (has no pages or links) or false otherwise
		 *
		 * @return true if the pager is empty or false if not
		 */
		function isEmpty()
		{
			return( $this->_totalPages == 0 );
		}

        /**
         * @private
         */
        function _init()
        {
			$pages = ceil($this->_totalRegs / $this->_regsForPage);	
            $this->_totalPages       = $pages == 0 ? 1 : $pages;
            $this->_startPage        = 1;
            $this->_endPage          = $this->_totalPages;
			$this->_pageLinks        = $this->generateLinks();
        }

    }
?>
