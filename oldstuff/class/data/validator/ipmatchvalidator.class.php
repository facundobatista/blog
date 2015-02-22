<?php

	lt_include( PLOG_CLASS_PATH."class/data/validator/validator.class.php" );

    /**
     * \ingroup Validator
     * 
     * Matches ip address with masks. Returns true wether
     * the given ip address matches with the given mask
     */
	class IpMatchValidator extends Validator 
	{

    	function IpValidator()
        {
        	$this->Validator();
        }
        
        function validate( $ip, $mask )
        {
        	return $this->checkip( $ip, $mask );
        }

        /**
         * @private
         */
        function checkip($ip , $csiext)
        {
        	$counter = 0;

            $range = explode("/",$csiext);
            if ($range[1] < 32) {
            	$maskbits = $range[1];
                $hostbits = 32 - $maskbits;

                $hostcount = pow(2, $hostbits)-1;
                $ipstart = ip2long($range[0]);
                $ipend = $ipstart + $hostcount;
                if (ip2long($ip) >= $ipstart) {
                	if (ip2long($ip) <= $ipend) {
                    	return (true);
                    }
                }
            }
            else {
            	if (ip2long($ip) == ip2long($range[0])) {
                	return (true);
            	}
            }

        	return(false);
    	}
    }
?>
