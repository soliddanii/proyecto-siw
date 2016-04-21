<?php

class Session{

	public function __construct(){}

	public function init(){
		@session_start();		
	}

	public function setVar($varName , $value){
		$_SESSION[$varName] = $value;
	}

	public function getId(){
		return session_id();
	}

	public function getName(){
		return session_name();
	}

	public function destroy(){
		session_unset();
		session_destroy();
	}

}

?>