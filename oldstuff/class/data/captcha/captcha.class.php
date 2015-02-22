<?php
	
	/**
	 * default expiration time for old images, 1h
	 */
	define( "CAPTCHA_DEFAULT_EXPIRATION_TIME", 3600 );
	
	/**
	 * background folder and background default image
	 */
	define( "CAPTCHA_BACKGROUND_FOLDER", PLOG_CLASS_PATH."imgs/authimage/" );
	define( "CAPTCHA_BACKGROUND_FILE", "sky.gif" );
	/**
	 * change this to your default key, used for the "encryption"
	 */	
	define( "CAPTCHA_DEFAULT_KEY", "default-key" );
	/**
	 * where you would like to store the images
	 */
	define( "CAPTCHA_CACHE_FOLDER", "./tmp" );
	/** 
	 * default length of the code
	 */
	define( "CAPTCHA_DEFAULT_CODE_LENGTH", 6 );
	
	/**
	 * \ingroup Data
	 *	
	 * Class to generate CAPTCHA images, based on Mark Wu's AuthImage plugin. Requires support
	 * for GD built-in.
	 *
	 * Usage:
	 * <pre>
	 *  $auth = new Captcha();
	 *  $auth->generate();
	 *  ...
	 *  if( $auth->validate( $_POST["authImage"] )) {
	 *     // validation ok!
	 *  }
	 *  else {
	 *    // error in validation!
	 *  }
	 * </pre>
	 */
	class Captcha
	{
		/**
		 * Constructor. It takes no parameter but there are several public attributes that
		 * can be set after the constructor has been called:
		 *
		 * - key
		 * - cacheFolder
		 * - expiredTime
		 * - length
		 */
		function Captcha()
		{
			lt_include( PLOG_CLASS_PATH."class/config/config.class.php" );
			
			$config =& Config::getConfig();
			$this->cacheFolder = $config->getValue( "temp_folder", CAPTCHA_CACHE_FOLDER );
			/**
			 * Activate the line below and comment the line above if you have moved
			 * your temporary folder outside of the web server tree
			 */
			//$this->cacheFolder = CAPTCHA_CACHE_FOLDER;
			
			$this->key = CAPTCHA_DEFAULT_KEY;
			$this->expiredTime = CAPTCHA_DEFAULT_EXPIRATION_TIME;
			$this->length = CAPTCHA_DEFAULT_CODE_LENGTH;
		}
		
        /**
         * @private
         */
        function encrypt($string, $key) {
            $plainText = $string.$key;
            $encodeText = md5($plainText);
            return $encodeText;
        }
        
        /**
         * @private
         */
        function generateCode() {
            $code = "";
            for($i=0; $i < $this->length; $i++) $code .= rand(0,9);
            return $code;
        }		
		
		/**
		 * generates a new image and returns the url
		 *
		 * @return a url to the captcha image
		 */
		function generate()
		{
            // Delete those cached authimage files that never used
            $this->purgeOld($this->expiredTime);
            
            $code = $this->generateCode();
            $encrypt = $this->encrypt($code, $this->key);
            $background = CAPTCHA_BACKGROUND_FOLDER.CAPTCHA_BACKGROUND_FILE;
            $tempFile = $this->cacheFolder."/".$encrypt.".gif";

            if(function_exists ( 'imagecreatefromgif' )){
                $image = @imagecreatefromgif($background) or die("Cannot Initialize new GD image stream");
			}
			else if(function_exists ( 'imagecreatefrompng' )){
                $image = @imagecreatefrompng($background) or die("Cannot Initialize new GD image stream"); 
            } 
            else {
			  die("Server doesn't support GIF or PNG creation. Sorry.");
            }           
            
            $textColor = imageColorAllocate($image, 0x00, 0x00, 0x00);
            ImageString($image, 5, 7, 2, $code, $textColor);

            if ( !function_exists ( 'ImageGIF' ) ) {
                ImagePNG($image, $tempFile);
            } else {            
                ImageGIF($image, $tempFile);
            }
            
            // Now chmod it so it can be deleted later by the user
            chmod($tempFile, 0666);       
            
			return( $tempFile );
		}
		
		/**
		 * checks whether the given code validates with any of the authimages
		 * previously generated
		 *
		 * @param code The code
		 * @return True if the code is valid or false otherwise
		 */
		function validate( $code )
		{
			lt_include( PLOG_CLASS_PATH."class/file/file.class.php" );
            $encrypt = $this->encrypt($code, $this->key);
            $tempFile = $this->cacheFolder."/".$encrypt.".gif";
           	$result = File::exists( $tempFile );

			return( $result );
		}
		
		/**
		 * Removes the old captcha images that are not needed anymre
		 *Ê@private
		 */
		function purgeOld( $expireTime = CAPTCHA_DEFAULT_EXPIRATION_TIME )
		{
			lt_include( PLOG_CLASS_PATH."class/misc/glob.class.php" );
			$files = Glob::myGlob( $this->cacheFolder, "*.gif" );
			if( $files ) {
				foreach( $files as $file ) {
					$diff = time() - filectime( $file );
					if ($diff > $expireTime) 
						unlink( $file );
				}
			}
		}
	}
?>
