<?php
	lt_include( PLOG_CLASS_PATH."class/config/properties.class.php" );
	lt_include( PLOG_CLASS_PATH."class/data/textfilter.class.php" );

    /**
     * \ingroup Net
     *
     * Represents a request in our system. Doing so we can in the future
     * change the format requests are recognized since all the dirty
     * stuff would be done here. After that, using an interface of the type
     * getValue( "param" ) would be enough to access those values, regardless
     * if the request was /index.php?op=Default&articleId=10 or
     * /index.php/op/Default/articleId/10.
     */
    class Request extends Properties 
	{
        /**
         * Initializes the request object
         *
         * @param values An associative array that will be
         *               used to initialize the object
         */
    	function Request( $values = null )
        {
            if(get_magic_quotes_gpc()){
                $values = Textfilter::recursiveStripSlashes($values);
            }
            
            $this->Properties( $values );
        }
    }
?>
