<?php

	function chargeMenu($text){
		
		$pathMenu = "../html/menu.html";

		$textMenu = file_get_contents($pathMenu) or exit("Error frontView, [$pathMenu]");

		$text = str_replace("##menu##",$textMenu,$text);

		// Comprueba si el usuario se ha logueado
		if(isset($_SESSION["user"])){

			$trozos0 = explode("##nologin##", $text);
			$trozos1 = explode("##login##", $trozos0[2]);

			$trozos1[1] = str_replace("##nameUser##", $_SESSION["user"], $trozos1[1]);

			return $trozos0[0].$trozos1[1].$trozos1[2];

		}else{

			$trozos0 = explode("##nologin##", $text);
			$trozos1 = explode("##login##", $trozos0[2]);

			return $trozos0[0].$trozos0[1].$trozos1[2];
		}

	}

	function frontView() {

		$pathFront = "../html/front.html";		
		
		$text = file_get_contents($pathFront) or exit("Error frontView, [$pathFront]");

		echo chargeMenu($text);
		
	}

	function loginView() {

		$pathFront = "../html/login.html";

		$text = file_get_contents($pathFront) or exit("Error signupView, [$pathFront]");

		echo chargeMenu($text);

	}

	function signUpView() {

		$pathFront = "../html/signup.html";

		$text = file_get_contents($pathFront) or exit("Error signupView, [$pathFront]");

		echo chargeMenu($text);

	}

?>