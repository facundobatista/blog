<?php

lt_include( PLOG_CLASS_PATH."class/summary/action/summaryaction.class.php" );
lt_include( PLOG_CLASS_PATH."class/data/textfilter.class.php" );
lt_include( PLOG_CLASS_PATH."class/summary/view/summarycachedview.class.php" );
lt_include( PLOG_CLASS_PATH."class/config/config.class.php" );
lt_include( PLOG_CLASS_PATH."class/data/validator/stringvalidator.class.php" );
lt_include( PLOG_CLASS_PATH."class/data/validator/usernamevalidator.class.php" );
lt_include( PLOG_CLASS_PATH."class/data/validator/passwordvalidator.class.php" );
lt_include( PLOG_CLASS_PATH."class/data/validator/integervalidator.class.php" );
lt_include( PLOG_CLASS_PATH."class/data/validator/emailvalidator.class.php" );

/**
 * Base action that all register actions should extend
 * @package summary
 * @subpackage action
 */
class RegisterAction extends SummaryAction
{
    var $userName;
    var $userPassword;
    var $userFullName;
    var $userEmail;
    var $blogName;
    var $blogCategoryId;
    var $blogLocale;
    var $templateId;
    var $blogDomain;

    function RegisterAction( $actionInfo, $request )
    {
        $this->SummaryAction( $actionInfo, $request );

		// there has to be a better place to check this, but I can't think of it now... Killing
		// the script without returning to the controller probably isn't a good idea, but it's
		// the quickest right now!
		$config =& Config::getConfig();
		if( $config->getValue( "summary_disable_registration" )) {
			lt_include( PLOG_CLASS_PATH."class/summary/view/summarymessageview.class.php" );
			$this->_view = new SummaryMessageView();
			$this->_view->setErrorMessage( $this->_locale->tr("error_registration_disabled"));			
			die($this->_view->render());
		}	
    }
}
?>
