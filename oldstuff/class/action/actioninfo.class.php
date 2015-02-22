<?php

    /**
     * \ingroup Action
     *
     * This class carries some information that classes extending the Action class might need, such as
     * with which parameter they were called or which was the name of the parameter
     * in the request. 
     */
    class ActionInfo  
    {
	
		var $_actionParamName; 
		var $_actionParamValue;	

    	/**
         * Constructor.
         *
         * In the following request:
         * <i>controller.php/?action=ViewArticle&articleId=7</i>
         * the parameter name would be <i>'action'</i> and the action name would
         * be <i>'ViewArticle'</i>. The default name for the parameter name is
         * 'action' but it can be changed when constructing the controller. The name
         * of the action is specified by using the action maps when creating the controller.
         *
         * @param actionParamName The name of the parameter that was used to trigger
         * the action.
         * @param actionParamValue The value of the action parameter that triggered this
         * action.
         */
		function ActionInfo( $actionParamName, $actionParamValue )
        {        
        	$this->_actionParamName  = $actionParamName;
            $this->_actionParamValue = $actionParamValue;
        }

		/**
         * Returns the name of the parameter used to trigger this action.
         *
         * @return The name of the parameter.
         */
        function getActionParamName()
        {
        	return $this->_actionParamName;
        }

        /**
         * Returns the vlaue of the parameter that triggered this action.
         *
         * @return The value of the parameter.
         */
        function getActionParamValue()
        {
        	return $this->_actionParamValue;
        }
    }
?>