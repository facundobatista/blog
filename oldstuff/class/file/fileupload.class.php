<?php

	/**
	 * \ingroup File
	 *
     * Object representation of a file upload.
     * Wraps around the values in the $_FILES or $HTTP_POST_FILES array.
     */
    class FileUpload  
	{

    	var $_name;
        var $_mimeType;
        var $_tmpName;
        var $_error;
        var $_size;
        var $_folder;

        /**
         * Constructor. Takes as a parameter a position of the $_FILES array.
         *
         * @param uploadInfo An associative array with information about the file uploaded.
         */
        function FileUpload( $uploadInfo )
        {
        	$this->_name     = $uploadInfo["name"];
            $this->_mimeType = $uploadInfo["type"];
            $this->_tmpName  = $uploadInfo["tmp_name"];
            $this->_size     = $uploadInfo["size"];
            $this->_error    = $uploadInfo["error"];
            $this->_folder   = null;
        }

		/**
		 * returns the real name of the file uploaded
		 *
		 * @return the file name
		 */
        function getFileName()
        {
        	return $this->_name;
        }

		function setFileName( $fileName ) 
		{
			$this->_name = $fileName;
		}

		/**
		 * returns the MIME type of the file
		 *
		 * @return a MIME type
		 */
        function getMimeType()
        {
        	return $this->_mimeType;
        }

		/**
		 * @return returns the name of this file in the temporary folder
		 */
        function getTmpName()
        {
        	return $this->_tmpName;
        }

		/**
		 * @returns an error code if there was a problem uploading the file
		 *
		 * @see http://www.php.net/manual/en/features.file-upload.errors.php
		 */
        function getError()
        {
        	return $this->_error;
        }

		/**
		 * sets the error code. This method will be rarely needed
		 *
		 * @param error A valid PHP upload error code
		 * @see http://www.php.net/manual/en/features.file-upload.errors.php
		 */
        function setError( $error )
        {
        	$this->_error = $error;
        }

		/**
		 * @return returns the size of the uploaded file
		 */
        function getSize()
        {
        	return $this->_size;
        }

		/**
		 * sets the folder
		 *
		 * @param folder
		 */
        function setFolder( $folder )
        {
        	$this->_folder = $folder;
        }

		/**
		 * @returns the folder
		 */
        function getFolder()
        {
        	return $this->_folder;
        }
    }
?>
