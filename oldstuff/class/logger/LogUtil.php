<?php
/**
 * Logger helper class
 * @author Su Baochen <subaochen@126.com>
 * \ingroup logger
 */
class LogUtil{
    /**
     * format an array or object
     * for debug purpose, format the debug message
     * usage:
     * <code>
     * $this->log->debug("current value is ".LogUtil::format($var));
     * or 
     * $this->log->debug(LogUtil::format($var));
     * </code>
     *
     * NOTE:
     * parameter should be a variable, especailly the parameter is an array or 
     * object
     *
     * @param variable
     * @return string the formatted string
     * @static
     */

	/**
	 * see copyright below! 
	 * this should help in debugging purposes...
	 */ 
	///////////////////////////////////////////////////////////////
	/// tableVarDump() by James Heinrich <info@silisoftware.com> //
	//        available at http://www.silisoftware.com          ///
	///////////////////////////////////////////////////////////////
	//                                                           //
	// Requires:                                                 //
	//   PHP 3.0.7 (or higher)                                   //
	//                                                           //
	//                                                           //
	//         This code is released under the GNU GPL:          //
	//           http://www.gnu.org/copyleft/gpl.html            //
	//                                                           //
	//      +---------------------------------------------+      //
	//      | If you do use this code somewhere, send me  |      //
	//      | an email and tell me how/where you used it. |      //
	//      +---------------------------------------------+      //
	//                                                           //
	///////////////////////////////////////////////////////////////
	///                                                          //
	// v1.0.1 - September 19, 2003                               //
	//   * Bugfix: included missing string_var_dump() function   //
	//     (thanks Andrei Verovski)                              //
	//                                                           //
	// v1.0.0 - May 8, 2003                                      //
	//   * initial public release                                //
	//                                                          ///
	///////////////////////////////////////////////////////////////

	function format($variable) {
		$returnstring = '';
		switch (gettype($variable)) {
			case 'boolean':
				$returnstring .= ($variable ? 'TRUE' : 'FALSE');
				break;

			case 'integer':
			case 'double':
			case 'float':
				$returnstring .= $variable;
				break;

			case 'array':
			case 'object':
			case 'null':
		        ob_start();
                //print_r($variable);
		        var_dump($variable);
		        $returnstring .= ob_get_contents();
		        ob_end_clean();
				break;

			case 'string':
				$variable = str_replace("\x00", ' ', $variable);
				$varlen = strlen($variable);
				for ($i = 0; $i < $varlen; $i++) {
					if (ereg('['.chr(0x0A).chr(0x0D).' -;0-9A-Za-z]', $variable{$i})) {
						$returnstring .= $variable{$i};
					} else {
						$returnstring .= '&#'.str_pad(ord($variable{$i}), 3, '0', STR_PAD_LEFT).';';
					}
				}
				break;

			default:
				$returnstring .= $variable;
				break;
		}
		return $returnstring;
	}


	function string_var_dump($variable) {
		ob_start();
		var_dump($variable);
		$dumpedvariable = ob_get_contents();
		ob_end_clean();
		return $dumpedvariable;
	}
	
}
?>
