<?php

    

    /**
     * \ingroup Security
     *
     * This is the base class from which all the objects that will be used in the
     * pipeline will inherit. It defines the basic operations and methods
     * that they'll have to use.
     *
     * Filters are the basic processing units of the Pipeline. Each filter implements some
     * logic, according to its purposes and will either accept or reject the incoming request by
     * returning a positive or negative PipelineResult object from the filter() method. Additionally, 
     * the PipelineResult object allows to specify an error message if needed. 
     *
     * In case the result is negative, the whole processing of the request by the blog will be stopped
     * and the user who made the request will be shown the error message via the ErrorView view.
     *
     * Please implement at least the filter() method when creating custom filters.
     */
    class PipelineFilter  
    {

        var $_pipelineRequest;

        /**
         * Constructor of the filter.
         *
         * @param pipelineRequest a PipelineRequest object that carries information about the
         * incoming request and so on
         */
        function PipelineFilter( $pipelineRequest )
        {
            

            $this->_pipelineRequest = $pipelineRequest;
        }
        
        /**
         * @return Returns the PipelineRequest object that was passed as a parameter to the constructor
         */
        function getPipelineRequest()
        {
            return $this->_pipelineRequest;
        }

        /**
         * This method must be implemented by all child classes extending this one and implements
         * the actual processing logic of the filter. It always returns a PipelineResult object with
         * either positive or negative result. Please see the PipelineResult object for more information.
         *
         * @return a PipelineResult object
         * @see Pipelineresult
         */
        function filter()
        {
            throw( new Exception( "This method must be implemented by child classes!" ));
        }
    }
?>
