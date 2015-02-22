<?php

	
	lt_include( PLOG_CLASS_PATH."class/data/stringutils.class.php" );
	lt_include( PLOG_CLASS_PATH."class/gallery/dao/galleryalbums.class.php" );

	/**
	 * \ingroup Gallery
	 * 
	 * some extra useful functions, which shouldn't be used by developers anyway...
	 */
	class GalleryTemplateTools 
	{
	
		/**
		 * returns a nicely formated string with <option>...</option> tags that can be
		 * used in a <select> list. It will nest the album names in order to graphically
		 * describe hierarchies, for easier understanding of how our album structure
		 * is built
		 *
		 * @param userId
		 * @return A string
		 */
		function getNestedDropDownListContents( $userId )
		{
			// fetch the list of albums properly arranged
			$albums = new GalleryAlbums();
			$userAlbums = $albums->getUserAlbumsGroupedByParentId( $userId );
			
			// printing format
			$format = '<option value="{id}">{name}</option>';
			
			// call the method and return the results
			$result = GalleryTemplateTools::_printNested( $userAlbums, $format, "&nbsp;&nbsp;&nbsp;" );
			
			return $result;
		}
		
		/** 
		 * @static
		 * @private
		 */
		function _printNested( $albums, $format, $spacing = " ", $start = 0, $level = -1 ) 
		{
			$level++;
			if( $albums["$start"] == "" )
				return "";
				
			foreach( $albums["$start"] as $album ) {
				// do the replacing
				$line = str_replace( '{id}', $album->getId(), $format);
				$albumName = StringUtils::pad( $level, $spacing).$album->getName();
				$line = str_replace( '{name}', $albumName, $line );
				$results .= $line;
				
				// make a recursive call				
				$results .= GalleryTemplateTools::_printNested( $albums, $format, $spacing, $album->getId(), $level);
			}
			
			return $results;
		}		
	}
?>