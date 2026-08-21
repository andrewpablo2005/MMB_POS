<?php

namespace Classes;

use Classes\Pdo;


class fileupload
{
    public $filename;
    private $basefile;
    private $directory;
    private $fileextenstion;
    private $response = array();

    private $con;

//    public function __construct($file_post, $dir, $con)
    public function __construct($file_post, $dir)
    {
        $today = date('Y-m-d H:i:s');
        $this->basefile = $file_post;
        $this->directory = $dir;
        $this->filename = uniqid() . "-" . time() . basename($file_post['name']);
        $this->fileextenstion = strtolower(pathinfo($this->filename, PATHINFO_EXTENSION));
    }

    public function _validate()
    {
        $check = true;
        $attrib = getimagesize($this->basefile['tmp_name']);
        if (!$attrib) {
            array_push($this->response, ["File is not an image."]);
            $check = false;
        }
        if ($this->basefile["size"] > 5000000) {
            array_push($this->response, ["Sorry, your file is too large."]);
            $check = false;
        }
        if ($this->fileextenstion != "jpg" && $this->fileextenstion != "jpeg" && $this->fileextenstion != "png" && $this->fileextenstion != "pdf") {
            array_push($this->response, ["Sorry, only JPG, JPEG, PNG & GIF files are allowed."]);
            $check = false;
        }

        return $check;
    }

    public function upload()
    {
        $today = date('Y-m-d H:i:s');
        if ($this->_validate() && move_uploaded_file($this->basefile["tmp_name"], $this->directory . $this->filename)) {
            $this->response = array('success' => 'Uploaded');
            return true;
        }
        $this->response = array('error' => 'Failed to upload');
        return false;
    }

}
?>