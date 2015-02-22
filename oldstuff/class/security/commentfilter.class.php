<?php

	lt_include( PLOG_CLASS_PATH."class/security/pipelinefilter.class.php" );
    lt_include( PLOG_CLASS_PATH."class/config/config.class.php" );

    define( "COMMENT_FILTER_MAXIMUM_SIZE_EXCEEDED", 400 );

    /**
     * \ingroup Security
     *
     * Checks that the post complies with several definable conditions, such as
     * for example its size in bytes and so on. This filter will only check
     * incoming requests where there is an "op" parameter with value equal to
     * "addComment". If so, it will check some extra values to make sure that the comment
     * is for example not longer than a certain limit (set by the 'maximum_comment_size' config
     * setting)
     *
     *Ê@see PipelineFilter
     * @see Pipeline
     * @see PipelineResult
     */
    class CommentFilter extends PipelineFilter 
    {

    	function CommentFilter( $pipelineRequest )
        {
        	$this->PipelineFilter( $pipelineRequest );
        }

        /**
         * Checks whether incoming comments are not bigger than our configurable threshold, via
         * the 'maximum_comment_size' config setting
         *
         * @return A PipelineResult object
         */
        function filter()
        {
        	// check if we're posting a comment
            $request = $this->_pipelineRequest->getHttpRequest();
            if( $request->getValue( "op" ) != "AddComment" )
            	return new PipelineResult();

			// if this is already rejected, there is no reason to do anything here
			if ( $this->_pipelineRequest->getRejectedState() )
            	return new PipelineResult();
            	
        	// get the value of the maximum size of a comment, in bytes
        	$config =& Config::getConfig();
            $maxSize = $config->getValue( "maximum_comment_size" );

            // if it's 0 or negative, it can be as big
            // as needed
            if( $maxSize <= 0 )
        		return new PipelineResult();

            // otherwise, let's check
            $commentSize = strlen($request->getValue( "commentText" ));
            $topicSize   = strlen($request->getValue( "commentTopic" ));

            if( $commentSize >= $maxSize || $topicSize >= $maxSize ) {
                $plr = $this->getPipelineRequest();
                $bi = $plr->getBlogInfo();
                $locale = $bi->getLocale();
                $result = new PipelineResult( false, COMMENT_FILTER_MAXIMUM_SIZE_EXCEEDED, $locale->tr("error_comment_too_big" ) );
            	return $result;
            }

            $result = new PipelineResult();
            return $result;
        }
    }
?>
