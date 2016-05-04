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
	$cmd = "userView";
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
		case 'userView':
			switch ($id) {
                case '0':
                    $data = chargeCategories();	
                    frontView($data);
                    break;
                    
				case '1':
					signUpView();
					break;
				
				case '2':
					loginView();
					break;

				case '3':
					recoverPassView();
					break;

                case '4':
                    $data = chargeCategories();	
                    browserView($data);
                    break;
                    
                case '5':
                    $data = chargeCategories();	
                    newAnuncioView($data);
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
					
					if ($ret == 1)
						signUpView("Nombre de usuario no disponible");
					elseif ($ret == 0) {
						header("Location:controller.php");
 						exit;
 					}
					elseif ($ret == -1)
						errorView("userCmd-signUp");
					break;
				
				case '2':
					$ret = loginUser();

					if ($ret == 0){
						header("Location:controller.php");
 						exit;
 					}
					elseif ($ret == 1)
						loginView("El usuario no existe, vuelva a intentarlo");
					elseif ($ret == -1)
						errorView("userCmd-loginUser");
					break;

				case '3':
					logOut();
					header("Location:controller.php");
 					exit;
					break;

				case '4':
					$pass = recoverPass();
					if ($pass == 1)
						recoverPassView("","Los datos no son correctos");
					elseif ($pass == -1)
						errorView("userCmd-recoverPass");
					else
						recoverPassView($pass);
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