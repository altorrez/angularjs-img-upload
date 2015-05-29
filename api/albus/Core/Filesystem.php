<?php

namespace albus\Core;

class Filesystem {

	// default path to save/retrieve files
	private $path;
	private $errorMessage;

	public function __construct() {
		$this->path = ROOT.DS.'albus'.DS.'files';
	}
	/**
	 * Description
	 * @param String $path full path to save/retrieve files
	 * @return void
	 */
	public function setPath($path) {
		$this->path = $path;
	}
	/**
	 * Description
	 * @param String $name input name of file (<input name="$name">) 
	 * @return String saved filename
	 * TODO: implement maxsize
	 */
	public function saveImage($name, $maxsize = null) {

		// Check file upload
		if(!isset($_FILES[$name])) {
			$this->errorMessage = 'No image data passed';
			return false;
		}else if ($_FILES[$name]['error']) {
			$this->errorMessage = 'Max image size exceeded';
			return false;
		}

		if(!$this->isImage($name)) {
			$this->errorMessage = 'Not an image file';
			return false;
		}

		// save file with generated name
		$filename =  $this->genFilename();
		$this->save($name, $filename);

		// return newly filename
		return $filename;
	}

	public function getFile($filename) {
		$full_filename = $this->path.DS.$filename;
		if(file_exists($full_filename))
			return file_get_contents($full_filename);
		else
			return false;
	}

	public function getMessage() {
		return $this->errorMessage;
	}

	public function listDir($path = null) {
		if($path == null)
			$path = $this->path;
		$raw_dir = scandir($path);
		array_splice($raw_dir, 0, 2);
		return $raw_dir;
	}

	public function getFullFilepath($file) {
		return $this->path.DS.$file;
	}

	public function getThumb($image, $filename, $path = null) {
		if($path == null)
			$path = $this->path;

		// Max width and height of thumbnail
		$t_maxwidth = 400;
		$t_maxheight = 580;

		$filefullpath = $this->getFullFilepath($filename);
		list($width, $height) = getimagesize($filefullpath);

		if($width > $height) {
			$newwidth = $t_maxwidth;
			$newheight = $t_maxwidth * $height / $width;
		}else {
			$newheight = $t_maxheight;
			$newwidth = $t_maxheight * $width / $height;
		}

		// Load
		$thumb = imagecreatetruecolor($newwidth, $newheight);
		$source = imagecreatefromstring($image);

		// Resize
		imagecopyresized($thumb, $source, 0, 0, 0, 0, $newwidth, $newheight, $width, $height);

		// Output
		return imagejpeg($thumb);
	}

	private function save($name, $filename) {
		move_uploaded_file($_FILES[$name]["tmp_name"], $this->path.DS.$filename); 
	}

	private function isImage($file) {
		return getimagesize($_FILES[$file]["tmp_name"]); 
	}

	private function genFilename($size = 16) {
		// Generate random filename until a unique filename is generated
		do {
			// Generate random filename
			do {
				$bytes = openssl_random_pseudo_bytes($size, $cstrong);
			} while(!$cstrong);
			$filename = bin2hex($bytes);

		}while($this->fileExists($filename));

		return $filename;
	}

	private function fileExists($filename) {
		return file_exists($this->path.DS.$filename);
	}

}
