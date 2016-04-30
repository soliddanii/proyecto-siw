<?php

	// Incluimos los ficheros que vamos a utilizar
	require_once 'view.php';
	require_once 'model.php';

	// Llamamos a las clases que vamos a utilizar	
	// manejo de sesiones
	//require 'class/'; //

	// Iniciamos sesion
	initSession();

	// Recogemos parametros del usuario {GET POST}
	$cmd = "frontView";
	$id = 0;

	if(isset($_GET["cmd"])) {

		$cmd = $_GET["cmd"];

	}else if (isset($_POST["cmd"])) {
		
		$cmd = $_POST["cmd"];

	}

	if(isset($_GET["id"])) {

		$id = $_GET["id"];

	}else if (isset($_POST["id"])) {
		
		$id = $_POST["id"];

	}

	// Switch de las opciones
	switch ($cmd) {
		case 'frontView':			
			frontView();
			break;

		case 'userView':
			switch ($id) {
				case '1':
					signUpView();
					break;
				
				case '2':
					loginView();
					break;

				default:
					# code...
					break;
			}
			break;

		case 'userCmd':
			switch ($id) {
				case '1':
					$ret = signUp();
					if($ret == -1){
						echo "El usuario ya existe";
					}elseif ($ret == 0) {
						frontView();
					}elseif ($ret == 1){
						echo "Ocurrio un error al registrar";
					}	
					break;
				
				case '2':
					loginUser();
					break;

				case '3':
					logOut();
					frontView();
					break;
				
				default:
					# code...
					break;
			}
			break;
		
		default:
			
			break;
	}
?>