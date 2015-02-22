<?php

    lt_include( PLOG_CLASS_PATH."class/data/validator/validation.class.php");

    /**
     * \defgroup Validator_Rules
     *
     * The main classes that implement the Strategy pattern as described here
     * http://www.phppatterns.com/index.php/article/articleview/13/1/1/ are the classes
     * that extend the Validator interface. 
     *
     * But at a deeper level, a Validator class is nothing else than a collection of rules,
     * or classes that extend the Rule interface. Implementing Validator classes as a collection
     * of Rule classes allows for better code reusability, since sometimes most of the Validator
     * classes check for similar things and therefore we can reuse most of the Rule classes.
     *
     * In order to implement your own rule, simply create your own class extending the Rule 
     * class and make sure to implement the Rule::Validate() method with your own validation logic. This
     * method should return 'true' if the data complies with the logic of the rule or 'false' if it does
     * not. Additionally, since Rule extends the Validation interface it is also possible to set
     * certain error flags or messages via the private method Validation::_setError(). These error
     * codes can be checked by Validator classes or by users of this class via the Validation::getError()
     * method.
     *
     * See the documentation of the Validator base class on how to use Rule classes in your own validators.
     * @see Validator
     */
     
    /**
     * \ingroup Validator_Rules
     *
     * This is the main base class that all custom Rule classes should extend. Please implement the
     * Rule::validate() with your own logic and return 'true' if the validation was successful or 
     * 'false' otherwise. Please use Validation::_setError() to set additional error codes reporting
     * more information.
     */
    class Rule extends Validation
    {
        /**
         * The constructor does nothing.
         */
        function Rule()
        {
            $this->Validation();
        }

        /**
         * Validates the data. Does nothing here and it must be reimplemented by
         * every child class.
         *
         * @param value The value that we're going to validate
         * @return True if successful or false otheriwse
         */
        function validate($value)
        {
            throw(new Exception("Rule::validate: This method must be implemented by child classes."));
            die();
        }
    }
?>
