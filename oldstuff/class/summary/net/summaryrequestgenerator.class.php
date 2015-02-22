<?php

    lt_include( PLOG_CLASS_PATH."class/net/baserequestgenerator.class.php" );

    /**
     * Very basic request generator for the summary. It is not meant to be used as a 
     * request generator but only to generate a few URLs needed by the summary.
     *
     * @see RequestGenerator
     * @see BaseRequestGenerator
     */
    class SummaryRequestGenerator extends BaseRequestGenerator 
    {

    	/**
         * Constructor.
         *
         * @param blogInfo A BlogInfo object
         */
    	function SummaryRequestGenerator()
        {
        	$this->BaseRequestGenerator( null );
        }
    }
?>
