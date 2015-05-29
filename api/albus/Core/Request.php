<?php 

namespace albus\Core;

class Request {

	public function __construct() {
		
	}

	public function getBody() {
		return file_get_contents('php://input');
	}

	public function getParams() {
		return $_GET;
	}

	public function getCookie($name) {
		return isset($_COOKIE[$name]) ? $_COOKIE[$name] : false;
	}

	public function getAuthUser() {
		return isset($_SERVER['PHP_AUTH_USER']) ? $_SERVER['PHP_AUTH_USER'] : false;
	}

	public function getAuthPass() {
		return isset($_SERVER['PHP_AUTH_PW']) ? $_SERVER['PHP_AUTH_PW'] : false;
	}

	public function getFile($name) {
		return $_FILES[$name];
	}

	public function getURLPath() {
		return $this->getProtocol() . '://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'] . '/';
	}

	private function getProtocol() {
		preg_match('~^\w+~', $_SERVER['SERVER_PROTOCOL'], $protocol);
		return strtolower($protocol[0]);
	}
}