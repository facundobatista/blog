<?php

    lt_include( PLOG_CLASS_PATH."class/gallery/resizers/galleryabstractresizer.class.php" );
	lt_include( PLOG_CLASS_PATH."class/gallery/resizers/gddetector.class.php" );
	
	define( "GD_RESIZER_NO_SMOOTHING_MODE", 0 );
	define( "GD_RESIZER_PHP_IMAGECOPYRESAMPLED", 1 );
	define( "GD_RESIZER_BILINEAR_MODE", 2 );
	define( "GD_RESIZER_BICUBIC_MODE", 3 );	

	/**
	 * \ingroup Gallery_resizer
	 *
     * Generates thumbnails using the built-in GD library functionality.
     *
     * Based off Shiege Iseng's (shiegege@yahoo.com) Resize Class found somewhere in
     * the net, but heavily modified specially concerning error situations.
     *
     */
	class GalleryGDResizer extends GalleryAbstractResizer 
	{

    	var $img;

    	function GalleryGDResizer( $image, $outputMethod )
        {
        	$this->GalleryAbstractResizer( $image, $outputMethod );
        }

		/**
		 * @see GalleryResizer::generate
		 */
        function generate( $outFile, $width, $height )
        {
        	//
        	// generate the thubmanil but check for errors every time
            //
			
			// also, check if GD is available because otherwise we would get
			// all sorts of nasty errors...
			if( !GdDetector::detectGd())
				return false;
			
            if( !$this->thumbnail( $this->_image ))
            	return false;

			if( ($this->img["lebar"] < $width) && ($this->img["tinggi"] < $height) ) {
				$this->img["lebar_thumb"] = $this->img["lebar"];
				$this->img["tinggi_thumb"] = $this->img["tinggi"];
			}
            else {
                $this->calcThumbFormat($width, $height );
			}

            if( !$this->save( $outFile ))
            	return false;

                // depending on the default file creation settings in some hosts,
                // files created may not be readable by the web server
            File::chMod( $outFile, 0644 );

            return $outFile;
        }
        
		/** 
		 * @private
		 */
        function thumbnail($imgfile)
        {
        	//detect image format
            $this->img["format"]=ereg_replace(".*\.(.*)$","\\1",$imgfile);
            $this->img["format"]=strtoupper($this->img["format"]);
            if ($this->img["format"]=="JPG" || $this->img["format"]=="JPEG") {
                $this->img["format"]="JPEG";
                $this->img["src"] = @ImageCreateFromJPEG ($imgfile);
            }
            elseif ($this->img["format"]=="PNG") {
                $this->img["format"]="PNG";
                $this->img["src"] = @ImageCreateFromPNG ($imgfile);
            }
            elseif ($this->img["format"]=="GIF") {
                $this->img["format"]="GIF";
				if( function_exists("imagecreatefromgif")) {
                    $this->img["src"] = @ImageCreateFromGIF ($imgfile);
				}
                else {
					return false;
				}

            }
            else {
                return false;
            }

            // check for errors
            if( !$this->img["src"] )
            	return false;

            // if no errors, continue
            @$this->img["lebar"] = imagesx($this->img["src"]);
            @$this->img["tinggi"] = imagesy($this->img["src"]);
            //default quality jpeg
            $this->img["quality"]=85;

            return true;
        }

		/** 
		 * @private
		 */
        function size_height($size=100)
        {
        	//height
			if( $this->img["lebar"] > $size ) {
				$this->img["tinggi_thumb"]=$size;
				@$this->img["lebar_thumb"] = ($this->img["tinggi_thumb"]/$this->img["tinggi"])*$this->img["lebar"];
			}
			else {
				$this->img["tinggi_thumb"]=$size;
				$this->img["lebar_thumb"]=$this->img["lebar"]; 
			}

            return true;
        }

		/** 
		 * @private
		 */
        function size_width($size=100)
        {
        	//width
			if( $this->img["tinggi"] > $size ) {
				$this->img["lebar_thumb"]=$size;
				@$this->img["tinggi_thumb"] = ($this->img["lebar_thumb"]/$this->img["lebar"])*$this->img["tinggi"];
			}
			else {
				$this->img["lebar_thumb"] = $size;
				$this->img["tinggi_thumb"] = $this->img["tinggi"];
			}

            return true;
        }
		
		/** 
		 * @private
		 */
        function size_auto($size=100)
        {
        	//size
            if ($this->img["lebar"]>=$this->img["tinggi"]) {
            	$this->img["lebar_thumb"]=$size;
                @$this->img["tinggi_thumb"] = ($this->img["lebar_thumb"]/$this->img["lebar"])*$this->img["tinggi"];
            }
            else {
            	$this->img["tinggi_thumb"]=$size;
                @$this->img["lebar_thumb"] = ($this->img["tinggi_thumb"]/$this->img["tinggi"])*$this->img["lebar"];
            }

            return true;
        }

		/** 
		 * @private
		 */
        function jpeg_quality($quality=75)
        {
        	//jpeg quality
            $this->img["quality"]=$quality;

            return true;
        }
		
        /**
         * returns true if gd2 is available or false otherwise.
         * Based on a comment found in http://fi2.php.net/imagecreatetruecolor by aaron at aaron-wright dot com
         * (credit is due where it is due :)
		 * 
		 * @return true if GD2 is available or false otherwise
         */
        function isGD2Available()
        {
            // maybe the blog has been configured to use the gd1 routines no matter
            // if gd2 is detected or not... in that case, we don't go ahead and simply
            // say that gd2 is not available
            $config =& Config::getConfig();
            if( $config->getValue( "thumbnail_generator_force_use_gd1" )) {
                return false;
            }
            
            // if not, we still check in case the user made a mistake...
            $testGD = get_extension_funcs("gd"); // Grab function list
            if ( !$testGD ) { 
                return false;
            }
            if (in_array ("imagegd2",$testGD)) {
                return true;
            }
            else { 
                return false; 
            }
        }

		/**
		 * resizes an image using several different techniques:
		 *
		 * PHP's own ImageCopyResamplated
		 * Bi-linear filter (slower, but better quality than ImageCopyResampled)
		 * Bi-Cubic filter (slowest, but offers the best quality)
		 * PHP's own ImageCopyResized (fastest one, but offers no antialising or filter)
		 *
		 */
        function ImageResize($dst_img, &$src_img, $dst_x, $dst_y, $src_x, 
                                   $src_y, $dst_w, $dst_h, $src_w, $src_h, 
                                   $resample = GD_RESIZER_NO_SMOOTHING_MODE ) {
           $pxls = intval($src_w / $dst_w)-1;

		   if( $dst_w == $dst_h ) {
		   		$length = min($src_w, $src_h);
		   		$src_x = intval( $src_w / 2 ) - intval( $length / 2 );
		   		$src_y = intval( $src_h / 2 ) - intval( $length / 2 );
		   		$src_w = $length;
		   		$src_h = $length;
		   }
		   		
		   if( $resample == GD_RESIZER_PHP_IMAGECOPYRESAMPLED ) {
				imagecopyresampled($dst_img, $src_img, $dst_x, $dst_y, $src_x, $src_y, $dst_w, $dst_h, $src_w, $src_h);	
		   }
           elseif( $resample == GD_RESIZER_BILINEAR_MODE  ) { //slow but better quality
                ImageTrueColorToPalette( $src_img, false, 256 );
                ImagePaletteCopy ($dst_img, $src_img);
                $rX = $src_w / $dst_w;
                $rY = $src_h / $dst_h;
                $nY = 0;
                for ($y=$src_y; $y<$dst_h; $y++) {
                    $oY = $nY;
                    $nY = intval(($y + 1) * $rY+.5);
                    $nX = 0;
                    for ($x=$src_x; $x<$dst_w; $x++) {
                         $r = $g = $b = $a = 0;
                         $oX = $nX;
                         $nX = intval(($x + 1) * $rX+.5);
                         $c = ImageColorsForIndex ($src_img, ImageColorAt ($src_img, $nX, $nY));
                         $r += $c['red']; $g += $c['green']; $b += $c['blue']; $a++;
                         $c = ImageColorsForIndex ($src_img, ImageColorAt ($src_img, $nX-$pxls, $nY-$pxls));
                         $r += $c['red']; $g += $c['green']; $b += $c['blue']; $a++;
                         //you can add more pixels here! eg "$nX, $nY-$pxls" or "$nX-$pxls, $nY"
                         ImageSetPixel ($dst_img, ($x+$dst_x-$src_x), ($y+$dst_y-$src_y), ImageColorClosest ($dst_img, $r/$a, $g/$a, $b/$a));
                    }
                }
           } 
           elseif ( $resample == GD_RESIZER_BICUBIC_MODE ) { // veeeeeery slow but better quality
                     ImagePaletteCopy ($dst_img, $src_img);
                     $rX = $src_w / $dst_w;
                     $rY = $src_h / $dst_h;
                     $nY = 0;
                     for ($y=$src_y; $y<$dst_h; $y++) {
                       $oY = $nY;
                       $nY = intval(($y + 1) * $rY+.5);
                       $nX = 0;
                       for ($x=$src_x; $x<$dst_w; $x++) {
                         $r = $g = $b = $a = 0;
                         $oX = $nX;
                         $nX = intval(($x + 1) * $rX+.5);
                         for ($i=$nY; --$i>=$oY;) {
                           for ($j=$nX; --$j>=$oX;) {
                             $c = ImageColorsForIndex ($src_img, ImageColorAt ($src_img, $j, $i));
                             $r += $c['red'];
                             $g += $c['green'];
                             $b += $c['blue'];
                             $a++;
                           }
                         }
                         ImageSetPixel ($dst_img, ($x+$dst_x-$src_x), ($y+$dst_y-$src_y), ImageColorClosest ($dst_img, $r/$a, $g/$a, $b/$a));
                       }
                     }
           } 
           else {
             $dst_w++; $dst_h++; //->no black border
             imagecopyresized($dst_img, $src_img, $dst_x, $dst_y, $src_x, $src_y, $dst_w, $dst_h, $src_w, $src_h);
           }
        }

		/** 
		 * @private
		 */
        function save( $save = "" )
        {
			$fileParts = explode( ".", $save );
			$fileNoExt = implode( ".", $fileParts );
			$fileExt = strtolower($fileParts[count($fileParts)-1]);
		
            //if( function_exists("imagecreatetruecolor")) {
            if( $this->isGD2Available()) {
            	$this->img["des"] = @ImageCreateTrueColor($this->img["lebar_thumb"],$this->img["tinggi_thumb"]);
            }
            else {
            	$this->img["des"] = @ImageCreate($this->img["lebar_thumb"],$this->img["tinggi_thumb"]);
            }

            // check for errors and stop if any, or continue otherwise
            if( $this->img["des"] == "" )
            	return false;
				
			$config =& Config::getConfig();
			if( $this->isGD2Available())
				$resizeMode = $config->getValue( "thumbnail_generator_use_smoothing_algorithm", GD_RESIZER_NO_SMOOTHING_MODE );
			else
				$resizeMode = GD_RESIZER_NO_SMOOTHING_MODE;
			
			// resize the image using the mode chosen above
			$this->ImageResize($this->img["des"], $this->img["src"], 0, 0, 0, 0, $this->img["lebar_thumb"], $this->img["tinggi_thumb"], $this->img["lebar"], $this->img["tinggi"], $resizeMode ); 

			// format for thumbnails is the same as the image
			//if( $this->_outputMethod == THUMBNAIL_OUTPUT_FORMAT_SAME_AS_IMAGE || $this->_outputMethod == "" ) {
				if ($fileExt=="jpg" || $fileExt=="jpeg") {
					$result = @imageJPEG($this->img["des"],"$save",$this->img["quality"]);
				}
				elseif ($fileExt=="png") {
					$result = @imagePNG($this->img["des"],"$save");
				}
				elseif ($fileExt=="gif") {
					if( function_exists("imagegif")) {
						$result = @imageGIF($this->img["des"],"$save");
					}
					else {
						$result = false;
					}
				}

            return $result;
        }

    
		/** 
		 * @private
		 */
        function calcThumbFormat($targetWidth, $targetHeight) {
            
            // lebar = width
            // tinggi = height
            // in malay language
            
    	    $ratioimg = $this->img["tinggi"] / $this->img["lebar"];
    	
        	if ($ratioimg < $targetHeight / $targetWidth) {
        	    $this->img["lebar_thumb"] = (int)$targetWidth;
        	    $this->img["tinggi_thumb"] = (int)round($targetWidth * $ratioimg);
        	} else {
        	    $this->img["lebar_thumb"] = (int)round($targetHeight / $ratioimg);
        	    $this->img["tinggi_thumb"] = (int)$targetHeight;
        	}
        	return true;
        }
           
    }
?>
