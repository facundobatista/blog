<?php	
    /**
     * \ingroup XML
     *
     * <p>This parser is a wrapper around the functionality provided by the MagpieRSS parser, which
     * can be found at http://magpierss.sourceforge.net/. The RSS parser is compatible with
     * RSS 0.9, 1.0 and almost all the modules of the 1.0 specification.</p>
     * <p>This object is exported to all templates so that we can easily incorporate the headlines
     * and/or the content of other pages in our journal. To do so, this object is exported with
     * "rss" as its identifier, so a simple operation to fetch the headlines from Slashdot would
     * look like:</p>
     *
     * <pre>
     * {if $rss->parse("http://slashdot.org/slashdot.rdf")}
     *  {foreach from=$rss->getItems() item=rssItem}
     *    <a href="{$rssItem->getLink()}">{$rssItem->getTitle()}</a>
     *  {/foreach}
     * {/if}
     * </pre>
     *
     * <p>The parse() method takes a url as a parameter, and it will return true if the url
     * was correctly fetched. If so, we can then ask the parser to gives us an array of
     * RSSItem objects which contain information about all the different &lt;item&gt; tags that were
     * found in the RSS feed. So, the only thing we have to do now is iterate through the array using
     * Smarty's <i>{foreach ...}</i> construction and call the appropiate methods on the RSSItem object.</p>
     * <p>To get more information about the channel (whatever was found between the &lt;channel&gt; opening
     * and closing tag) we can call the getChannel() function and we will get a nice and ready RSSChannel
     * information that we need to know.</p>
     * <p>If the RSS parser has been disabled via the configuration file, then the constructor will not do
     * anything and the parse method will return false.</p>
     */
	class RSSParser  
	{

		var $_items;
		var $_channel;

        /***
         * The constructor checks if the RSS parser is enabled in the configuration file. If it is
         * not, it simply quits.
         */
		function RSSParser()
		{
			$this->_channel = "";
			
			$config =& Config::getConfig();
			define( 'MAGPIE_CACHE_DIR', $config->getTempFolder());
		}

        /**
         * Parses an RSS feed
         *
         * @param rssFeed The URL of the RSS feed.
         * @return Returns true if the parsing was successful.
         */
		function parse( $rssFeed )
		{
	        lt_include( PLOG_CLASS_PATH."class/xml/rssparser/rsschannel.class.php" );
	        lt_include( PLOG_CLASS_PATH."class/xml/rssparser/magpierss/rss_fetch.inc" );	
            lt_include( PLOG_CLASS_PATH."class/xml/rssparser/magpierss/rss_cache.inc" );
            lt_include( PLOG_CLASS_PATH."class/xml/rssparser/magpierss/rss_parse.inc" );
            lt_include( PLOG_CLASS_PATH."class/xml/rssparser/magpierss/rss_utils.inc" );
            lt_include( PLOG_CLASS_PATH."class/xml/rssparser/rssitem.class.php" );

			$rss = fetch_rss( $rssFeed );

			if( !$rss )
				return false;

			$this->_channel = new RSSChannel( $rss->channel );

			$this->_items = Array();
			foreach ($rss->items as $item ) {
				$itemObject = new RSSItem( $item );
				array_push( $this->_items, $itemObject );
			}

			return true;
		}

        /**
         * Returns the items obtained from parsing the last RSS source specified
         *
         * @return An array of RSSItem objects
         */
		function getItems()
		{
			return $this->_items;
		}

        /**
         * Returns information about the channel parsed.
         *
         * @return An RSSChannel object containing information about the last RSS
         * source parsed.
         */
		function getChannel()
		{
			return $this->_channel;
		}
	}
?>
