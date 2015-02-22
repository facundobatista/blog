<?

    /**
     * \defgroup XML
     *
     * This module includes several external libraries and modules that deal with XML content
     * including parsing, RSS and so on. 
     */

// ##################################################################################
// Title                     : Class Path_parser
// Version                   : 1.0
// Author                    : Luis Argerich (lrargerich@yahoo.com)
// Last modification date    : 06-24-2002
// Description               : 
// ##################################################################################
// History: 
// 06-24-2002                : First version of this class.
// ##################################################################################
// To-Dos:
//
// ##################################################################################
// How to use it:
// Read the documentation in path_parser.html
// ##################################################################################


/*
 * Adapted by me to extend the Object class so that it fits in my
 * class hierarchy
 */
if(defined("_class_path_parser_is_included")) {
  // do nothing since the class is already included  
} else {
  define("_class_path_parser_is_included",1);

// This is a class to parse XML files and call user functions
// when specific elements (paths) are observed by the parser.





/**
 * \ingroup XML
 *
 * Allows to create XPath queries for XML documents, if needed.
 */
class Path_parser  {
  var $paths;
  var $context=Array();
  var $parser;
  var $error;
  var $path;


  function get_error() {
    return $this->error;
  }

  function Path_parser() {
	

    $this->init();
  }
  
  function init() {
    $this->paths=Array(); 
    $this->parser = xml_parser_create_ns("",'^');
    xml_set_object($this->parser,&$this);
    xml_parser_set_option($this->parser, XML_OPTION_CASE_FOLDING, false);
    xml_set_element_handler($this->parser, "_startElement", "_endElement");
    xml_set_character_data_handler($this->parser,"_data");
  }

  function parse_file($xml) {
    if (!($fp = fopen($xml, "r"))) {
      $this->error="Cannot open $rddl";
      return false;
    }
    while ($data = fread($fp, 4096)) {
      if (!xml_parse($this->parser, $data, feof($fp))) {
        $this->error="XML error: ".xml_error_string(xml_get_error_code($this->parser))." at line ".xml_get_current_line_number($this->parser);
        return false;
      }
    }
    xml_parser_free($this->parser);
    return true;
  }

  function parse($data,$is_final) {
    $ret = xml_parse($this->parser,$data,$is_final);
    if ($is_final) {
      xml_parser_free($this->parser);
    }
    if(!$ret) {
         $this->error="XML error: ".xml_error_string(xml_get_error_code($this->parser))." at line ".xml_get_current_line_number($this->parser);
    }
    return $ret;
 }
  


  function set_handler($path,$handler_name) {
    $this->paths[$path]["handler"]=$handler_name;
    $this->paths[$path]["depth"]=-1;
  }
   
  function _startElement($parser,$name,$attribs) {
    // Add the element to the context
    $names=explode('^',$name);
    if(count($names)>1) {
      $name=$names[1];
      $name_namespace_uri=$names[0]; 
    } else {
      $name=$names[0]; 
    }
    
    array_push($this->context, $name);
    
    $path='/'.implode("/",$this->context);
    $this->path=$path;
    //print("Actual path: $path <br/>\n");
    // Check all opened paths and update them
    foreach(array_keys($this->paths) as $pathk) {
      if($this->paths[$pathk]["depth"]>0  ) {
        $this->paths[$pathk]["depth"]++; 
        $this->paths[$pathk]["content"].='<'.$name;
        foreach($attribs as $atk => $atv) {
          $this->paths[$pathk]["content"].=' '.$atk.'="'.$atv.'"'; 
        }
        $this->paths[$pathk]["content"].='>';
      }
    }
    
    // If the context path matches some UNMATCHED path then init element data
    if(in_array($path,array_keys($this->paths))) {
       //print("Match!<br/>\n"); 
       if($this->paths[$path]["depth"]==-1) {
         $this->paths[$path]["depth"]=1;
         $this->paths[$path]["content"]='';
         $this->paths[$path]["content"]='<'.$name;
         $this->paths[$path]["name"]=$name;
         $this->paths[$path]["attribs"]=$attribs;
         foreach($attribs as $atk => $atv) {
           $this->paths[$path]["content"].=' '.$atk.'="'.$atv.'"'; 
         }
         $this->paths[$path]["content"].='>';
       }
    }  
  }
  
  function _endElement($parser,$name) {
    // Decrement element depth
    array_pop($this->context);
    $path='/'.implode("/",$this->context);
    $this->path=$path;
    //print("Actual path: $path <br/>\n");
    foreach(array_keys($this->paths) as $pathk) {
      if($this->paths[$pathk]["depth"]>0  ) {
        $this->paths[$pathk]["depth"]--; 
        $this->paths[$pathk]["content"].='</'.$name.'>';
      }
      if($this->paths[$pathk]["depth"]==0) {
        //print("Reportar: $pathk <br/>\n");
        //print_r($this->paths[$pathk]);
        $this->paths[$pathk]["depth"]=-1;
        $this->paths[$pathk]["handler"]($this->paths[$pathk]["name"],$this->paths[$pathk]["attribs"],$this->paths[$pathk]["content"]);
      }
    }

  }

  function _data($parser,$data) {
    foreach(array_keys($this->paths) as $pathk) {
      if($this->paths[$pathk]["depth"]>0  ) {
        $this->paths[$pathk]["content"].=$data;
      }

    }
  }


}

}
?>
