<?php

	lt_include( PLOG_CLASS_PATH."class/data/validator/validator.class.php" );

    /**
     * \ingroup Validator
     *
     * Validator class that allows to make chained validators so that we can validate for more complex
	 * rules. The parameter for the constructor is an array of Validator objects that we are going to
	 * chain.
	 *
	 * Internally, this class is not actually going to chain the validators but their rules, one after each
	 * other using the Validator::addValidator() method.
	 *
	 * You can either create your own brand new custom Validator class and add the rules that you need
	 * or if you know that some Validator classes are already doing what you need, you can always 
	 * chain them by using the ChainedValidator class.
	 *
	 * Example:
	 *
	 * <pre>
	 *  $val = new ChainedValidator( Array( new StringValidator(),
	 *                                      new UsernameValidator());
	 *  $result = $val->validate( $value );
	 * </pre>
     */
    class ChainedValidator extends Validator 
    {
        /**
         * Constructor of the chained validator
         *
         * @param validators An array of Validator objects which implement the logic that
         * we need
         */
    	function ChainedValidator( $validators )
        {
        	$this->Validator();
			
			foreach( $validators as $validator ) {
				$this->addValidator( $validator );
			}
        }
    }
?>