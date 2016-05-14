<?php

    //temp for debug
    require_once '../chromephp/ChromePhp.php';
    
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
                    //Pagina Principal
                    $data = chargeCategories();	
                    frontView($data, '');
                    break;
                    
				case '1':
                    //Pagina de registro
					signUpView("");
					break;
				
				case '2':
                    //Pagina de login
					loginView();
					break;

				case '3':
                    //Pagina de recuperacion de contraseña
					recoverPassView();
					break;

                case '4':
                    //Pagina de navegador
                    $categorias = chargeCategories();
                    $anuncios = chargeAnuncios();
                    browserView($categorias, $anuncios);
                    break;
                    
                case '5':
                    //Pagina de nuevo anuncio
                    $categorias = chargeCategories();	
                    newAnuncioView($categorias, '');
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
                    
                case '5':
                    $ret = newAnuncio();
                    if ($ret == '0'){
                        //Si todo se ha realizado correctamente:
                        //Redirigir al usuario a la pagina del anuncio
                    }else{
                        $categorias = chargeCategories();
                        if($ret == '1'){  
                            newAnuncioView($categorias, 'userCmd-newAnuncio: No existe sesión de usuario');
                        }elseif($ret == '2'){
                            newAnuncioView($categorias, 'userCmd-newAnuncio: Error con la base de datos');
                        }elseif ($ret == '3'){
                            newAnuncioView($categorias, 'userCmd-newAnuncio: Los valores recibidos son erroneos o falta alguno');
                        }elseif ($ret == '4'){
                            newAnuncioView($categorias, 'userCmd-newAnuncio: El formato de alguna de las imagenes no es valido');
                        }elseif ($ret == '5'){
                            newAnuncioView($categorias, 'userCmd-newAnuncio: El tamaño de la imagen es mayor que el permitido');
                        }                       
                    }
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