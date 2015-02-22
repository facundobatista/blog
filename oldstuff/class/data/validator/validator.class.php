<?php

    /**
     * \defgroup Validator
     *
     * Validator in pLog is an implementation of the 'Strategy' pattern as it can be seen
     * http://www.phppatterns.com/index.php/article/articleview/13/1/1/.
     *
     * Validator classes allow to reuse validation logic wherever needed, and in a uniform
     * way since class users can safely assume that all validators implement the
     * Validator::validate() method. 
     *
     * Validator classes in pLog can be built as a set of Rules, as a set of chained Validator
     * classes or they can also bring their own non-reusable logic (non-reusable in the sense that 
     * validation logic does not necessarily need to be part of a Rule class) 
     *
     * This the preferred way to validate data using a Validator class in pLog:
     *
     * <pre>
     *  $val = new UsernameValidator();
     *  if( !$val->validate( $newUsername ))
     *    print( "the username $newUsername is not correct!" );
     *  else
     *    print( "the username is correct" );
     * </pre>
     *
     * It is also possible to implemlent our own custom validators by extending the Validator base class
     * and adding a few rules. Keep in mind that if we overwrite the Validator::validate() method, 
     * we will lose some logic regarding rules. If our Validator class does not use rules, it is safe
     * to overwrite such method and return 'true' if successful or false otherwise.
     *
     * In order to implement our own validator, the preferred way of doing it is by adding the necessary
     * Validator and Rule objects in the constructor:
     *
     * <pre>
     *  class NewValidator extend Validator {
     *    function NewValidator() 
     *    {
     *     $this->addRule( new NonEmptyRule());
     *     $this->addRule( new NumericRule()); 
     *    }
     *  }
     * </pre>
     */

	lt_include( PLOG_CLASS_PATH."class/data/validator/validation.class.php" );

    /**
     * \ingroup Validator
     *
     * Base class that all other validators extend. See the documentation of the Validator module for more
     * information.
     */
    class Validator extends Validation 
    {
    
        var $_rules;

    	/**
         * Initializes the constructor
         */
    	function Validator()
        {
            $this->Validation();
            
            $this->_rules = array();
        }
        
        /**
         * Adds a rule to the Validator
         *
         * @param rule A valid Rule object
         */
        function addRule(&$rule)
        {
            $this->_rules[] = &$rule;
        }
        
        /**
         * Our validators can also be built based on other validators. It is also possible
         * to use the ChainedValidator if we don't wish to create a completely new Validator
         *
         * @param validator A Validator object or a class extending from it
         */ 
        function addValidator(&$validator)
        {
            foreach ($validator->_rules as $rule)
            {
                $this->addRule($rule);
            }
        }                

        /**
         * This is the main method that takes care of processing all the rules. It is not necessary
         * to reimplement this method in our custom Validator classes if we have already added some rules, 
         * as this method already provides some logic for going through the rules and setting some extended
         * error codes if necessary.
         *
         * If your custom validator does not use rules, it is safe to reimplement this method.
         *
         * @param value The value that we're going to validate.
         */
        function validate($value)
        {
            foreach ($this->_rules as $rule)
            {
                if (!$rule->validate($value))
                {
                    $this->_setError($rule->getError());
                    return false;
                }
            }

            return true;
        }
    }
?>