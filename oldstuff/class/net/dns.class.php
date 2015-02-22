<?php

	
    lt_include( PLOG_CLASS_PATH."class/misc/osdetect.class.php" );

    /**
     * \ingroup Net
     *
     * Implementation of an alternative version of the checkdnsrr and getmxrr functions which
     * are not available in the windows version of the php. The class detects wether we're
     * running windows or linux and then depending on the result, we will use the faster and native
     * version or the alternative one.
     */
    class Dns  
    {

    	/**
         * Static function that acts as a wrapper for the native checkdnsrr function. It first detects
         * wether we're running in Windows or not and then uses the native version or the alternative one.
         *
         * For more information:          http://hk2.php.net/checkdnsrr
         *
         * @param host The we would like to check.
         * @param type It defaults to MX, but could be one of A, MX, NS, SOA, PTR, CNAME, AAAA, or ANY.
         * @return Returns TRUE if any records are found; returns FALSE if no records were found or if an error occurred.
         * @static
         */
    	function checkdnsrr( $host, $type = "MX" )
        {
        	if( OsDetect::isWindows()) {
            	// call the alternative version
                return Dns::checkdnsrr_windows( $host, $type );
            }
            else {
            	// call the native version
                return checkdnsrr( $host, $type );
            }
        }

        /**
         * Function shamelessly copied from a comment made by an anonymous poster, that implements
         * an alternative version of checkdnsrr for windows platforms (at least, it works for
         * windows nt, 2000 and xp) I will never work in windows 98 because a) I think it's stupid
         * to run this in a windows 98 machine and b) because windows 98 is outdated anyway.
         *
         * Original function: http://hk2.php.net/checkdnsrr
         *
         * This function should behave in exactly the same way as the native checkdnsrr.
         *
         * @param host The we would like to check.
         * @param type It defaults to MX, but could be one of A, MX, NS, SOA, PTR, CNAME, AAAA, or ANY.
         * @return Returns TRUE if any records are found; returns FALSE if no records were found or if an error occurred.
         * @static
         * @private
         */
        function checkdnsrr_windows( $host, $type = "MX" )
        {
        	if( !empty( $host ) ) {
            	@exec( "nslookup -type=$type $host", $output );

                while( list( $k, $line ) = each( $output ) ) {
                	// Valid records begin with host name:
                    if( eregi( "^$host", $line ) ) {
                    	// record found:
                        return true;
                    }
                }

                return false;
            }
        }

        /**
         * Static function that detects wether we're running windows or not and then either uses the native version of
         * getmxrr or the alternative one. See getmxrr_windows below for more information.
         *
         * @param hostname The host for which we want to get the mx records.
         * @param mxhosts The array we are going to fill with the mx records.
         * @return Returns either true or false.
         * @static
         */
        function getmxrr( $hostname, &$mxhosts )
        {
        	if( OsDetect::isWindows()) {
            	// call the alternative version
                return Dns::getmxrr_windows( $hostname, $mxhosts );
            }
            else {
            	// use the native version
                return getmxrr( $hostname, $mxhosts );
            }
        }

        /**
         * Another function shamelessly copied from the same place which implements an alternative version
         * of getmxrr.
         *
         * See http://hk2.php.net/manual/en/function.getmxrr.php for more details.
         *
         * @param hostname The host for which we want to get the mx records.
         * @param mxhosts The array we are going to fill with the mx records.
         * @return Returns either true or false.
         * @static
         * @private
         */
        function getmxrr_windows( $hostname, &$mxhosts )
        {
        	if( !is_array( $mxhosts )) $mxhosts = array();

            if( !empty( $hostname ) ) {
            	@exec( "nslookup -type=MX $hostname", $output, $ret );

                while( list( $k, $line ) = each( $output ) ) {
                	// Valid records begin with hostname:
                    if( ereg( "^$hostname\tMX preference = ([0-9]+), mail exchanger = (.*)$", $line, $parts )) {
                    	$mxhosts[ $parts[1] ] = $parts[2];
                    }
                }

                if( count( $mxhosts ) ) {
                	reset( $mxhosts );
                    ksort( $mxhosts );

                    $i = 0;

                    while( list( $pref, $host ) = each( $mxhosts ) ) {
                    	$mxhosts2[$i] = $host;
                        $i++;
                    }

                    $mxhosts = $mxhosts2;

                    return true;
                }
                else {
                	return false;
                }
            }
        }
    }
?>
