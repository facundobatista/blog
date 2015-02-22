<?php

    /**
     * @package net
     */


	

	class PrettyRequestParser  {

    	function PrettyRequestParser( $function, $path_info )
        {
            $len = strlen($path_info);
            if($len && $path_info[$len-1] == '/')
               $this->_path_info = substr($path_info, 0, -1);
            else
               $this->_path_info = $path_info;

            $this->_function  = $function;
        }
		
		/** 
		 * @private
		 * Retrieves the page from the request
		 */
		function getPageValue( $request )
		{
			if( preg_match( "/\/page\/([0-9]*)/", $request, $matches ))
				$page = $matches[1];
			else
				$page = "";
			
			return( $page );
		}

        function parse()
        {
			// remove the page from the string, if any and then remove the string itself so that
			// it doesn't interfere with the old parsing code
			$page = $this->getPageValue( $this->_path_info );
			$this->_path_info = preg_replace( "/\/page\/[0-9]*/", "", $this->_path_info );		
		
            // get the blog id and the post id
            $params = explode( "/", $this->_path_info );

            // the blog id always comes after the name of the operation
            $blogId = $params[count($params)-2];

            $result = Array();
            $result["blogId"] = $blogId;

            switch( $this->_function ) {
            	case "post":
                	$articleId = $params[count($params)-1];
                    $result["articleId"] = $articleId;
                    $result["op"] = "ViewArticle";
                    break;
            	case "album":
                	$albumId = $params[count($params)-1];
                    $result["albumId"] = $albumId;
                    $result["op"] = "ViewAlbum";
                    break;
            	case "comment":
                	$articleId = $params[count($params)-1];
                    $result["articleId"] = $articleId;
                    $result["op"] = "Comment";
                    break;
                case "rss":
                	if( count($params) == 3 ) {
                        $param1 = $params[count($params)-2];
                        $param2 = $params[count($params)-1];
                        // this is a bit trickier because if we only have two parameters
                        // it can either be the profile or the category identifier so we
                        // we have to check for that...Of course it we ever have a profile
                        // that is a number then we'll have a problem here. Till then,
                        // it's all fine and dandy :)
                        if( is_numeric($param1)) {
                        	$result["categoryId"] = $param2;
                            $result["blogId"] = $param1;
                        }
                        else {
                        	$result["profile"] = $param1;
                            $result["blogId"] = $param2;
                        }
                    }
                    elseif( count($params) == 4 ) {
                    	$result["categoryId"] = $params[count($params)-1];
                		$result["blogId"] = $params[count($params)-2];
                    	$result["profile"] = $params[count($params)-3];
                    }
                    $result["op"] = "RssAction";
                    break;
                case "archives":
                	$date = $params[count($params)-1];
                    $result["op"] = "Default";
                    $result["Date"] = $date;
                    break;
                case "user":
                	$userId = $params[count($params)-1];
                    $result["userId"] = $userId;
                    $result["op"] = "Default";
                    break;                    
                case "category":
                	$categoryId = $params[count($params)-1];
                    $result["postCategoryId"] = $categoryId;
                    $result["op"] = "Default";
                    break;
                case "static":
                	$templateName = $params[count($params)-1];
                    $result["show"] = $templateName;
                    $result["op"] = "Template";
                    break;
                 case "resource":
                 	$resParam = $params[count($params)-1];
                    if( is_numeric( $resParam )) {
                    	$result["resId"] = $resParam;
                    }
                    else {
                    	$result["resource"] = $resParam;
                    }
                    $result["op"] = "ViewResource";
                    break;
                 case "get":
                 	$resFile = $params[count($params)-1];
                    $result["resource"] = $resFile;
                    break;
                 case "trackbacks":
                 	$postId = $params[count($params)-1];
                    $result["op"] = "Trackbacks";
                    $result["articleId"] = $postId;
                    break;
                 default:
                 	$result["op"] = "Default";
					$userParam=$params[count($params)-1];
					if( is_numeric($userParam))
						$result["blogId"] = $userParam;
					else
						$result["blogUserName"] = $userParam;
             }
			 
			 $result["page"] = $page;

             return $result;
        }
    }
?>
