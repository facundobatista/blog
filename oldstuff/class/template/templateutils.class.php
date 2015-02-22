<?php

	
    lt_include( PLOG_CLASS_PATH."class/dao/article.class.php" );
    lt_include( PLOG_CLASS_PATH."class/net/requestgenerator.class.php" );
    lt_include( PLOG_CLASS_PATH."class/dao/bloginfo.class.php" );
    lt_include( PLOG_CLASS_PATH."class/dao/articlecategory.class.php" );
    lt_include( PLOG_CLASS_PATH."class/dao/userinfo.class.php" );
    lt_include( PLOG_CLASS_PATH."class/net/http/httpvars.class.php" );

    /**
     * @deprecated
     * @see StringUtils
     *
     * \ingroup Template
     */
    class TemplateUtils  
	{

		var $_blogInfo;
        var $_rg;
        var $_mode;

    	/**
         * Constructor. We need the blogInfo object to get some information
         * about the current blog and so on
         */
    	function TemplateUtils( $blogInfo )
        {
        	
        	$this->_blogInfo = $blogInfo;
        }

        /**
         * Transforms some characters into their HTML entities
         *
         * @param string The string we'd like to translate
         * @return Returns the translated string.
         */
        function htmlTranslate( $string )
        {
        	return htmlspecialchars( $string );
        }

        /**
         * Manually adds the "show more" link in a post.
         *
         * @param post The post we are going to cut.
         * @param maxWords Amount of words we'd like to allow.
         * @param linkText Text we are going to show.
         * @return The modified link.
         */
        function addShowMoreLink( $post, $maxWords, $linkText )
        {
        	$textFilter = new TextFilter();
            $result = $textFilter->cutText( $post->getText(), $maxWords );
            $config =& Config::getConfig();
            if( $result != $post->getText()) {
            	$rg =& RequestGenerator::getRequestGenerator();
                $rg->addParameter( "op", "ViewArticle" );
                $rg->addParameter( "articleId", $post->getId());
                $rg->addParameter( "blogId", $this->_blogInfo->getId());
            	$indexPage = $config->getValue( "script_name", "index.php" );                
                $showMoreLink = " <a href=\"$indexPage".$rg->getRequest()."\">".$linkText."</a>";
                $result .= $showMoreLink;
            }

            return $result;
        }

        /**
         * Cuts a string after 'n' characters.
         *
         * @param string The string.
         * @return The string cut to 'n' characters.
         */
        function cutString( $string, $n )
        {
        	return substr( $string, 0, $n );
        }

        /**
         * Returns an array with all the links in a string.
         *
         * @param string The string
         * @return An array with the links in the string.
         */
        function getLinks( $string )
        {
        	$regexp = "|<a href=\"(.+)\">(.+)</a>|U";
            $result = Array();

            if( preg_match_all( $regexp, $string, $out, PREG_PATTERN_ORDER )) {
            	foreach( $out[1] as $link ) {
             		array_push( $result, $link );
            	}
            }

            return $result;
        }
    }
?>