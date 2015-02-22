<?php

lt_include(PLOG_CLASS_PATH.'class/mail/emailmessage.class.php');
lt_include(PLOG_CLASS_PATH.'class/locale/locales.class.php');
lt_include(PLOG_CLASS_PATH.'class/template/templateservice.class.php');

define( "CONFIRM_MAIL_TEMPLATE", "email_confirm" );

/**
 * email message that represent confirm email message mailed to user
 * @package mail
 */
class ConfirmEmailMessage extends EmailMessage{

    var $username;
    var $activeCode;
    var $activeLink;

    function ConfirmEmailMessage(){
        $this->EmailMessage();        
    }

    function setUsername($username){
        $this->username = $username;
    }

    function setActiveCode($activeCode){
        $this->activeCode = $activeCode;
    }

    function setActiveLink($activeLink){
        $this->activeLink = $activeLink;
    }

    /**
     * create the message body
     */
    function createBody(){
        $body = $this->renderBodyTemplate(CONFIRM_MAIL_TEMPLATE,"summary");
        $this->setBody($body) ;
    }

    function renderBodyTemplate($templateid,$templateFolder){
        // create a new template service
        $ts = new TemplateService();
        $messageTemplate = $ts->Template( $templateid,$templateFolder );
		$messageTemplate->forceDisableTrimWhitespace = true;
        $messageTemplate->assign("username",$this->username);
        $messageTemplate->assign("activeCode",$this->activeCode);
        $messageTemplate->assign("activeLink",$this->activeLink);

        // FIXME: use which locale?
        $locale = &Locales::getLocale();
        $messageTemplate->assign("locale",$locale);
        // render and return the contents
        return $messageTemplate->fetch();
    }
}

?>