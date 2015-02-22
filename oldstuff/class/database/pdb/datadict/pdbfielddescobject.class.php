<?php
    

    /**
     * Helper class for FetchFields -- holds info on a column
     *     
     * \ingroup PDb
     */    
    class PDbFieldDescObject 
    {
        var $name = '';
        var $max_length=0;
        var $type="";
        var $not_null = false;
        var $has_default = false;
        var $default_value;    
    }
?>