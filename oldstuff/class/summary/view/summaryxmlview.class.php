<?php

	lt_include( PLOG_CLASS_PATH."class/summary/view/summaryview.class.php" );
	
    /**
     * \ingroup View
     * @private
     */	
	class SummaryXmlView extends SummaryView
	{
        function SummaryXmlView( $templateName )
        {
            $this->SummaryView( $templateName, "summary/xml" );
			$this->setContentType( TEXT_XML_CONTENT_TYPE );
        }
	}
?>