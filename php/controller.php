﻿<?php

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
                    //[0] = data; [1] = errores
                    $dataCategorias = chargeCategories();
                    frontView($dataCategorias[0], $dataCategorias[1]);
                    break;
                    
				case '1':
                    //Pagina de registro
					signUpView(null);
					break;
				
				case '2':
                    //Pagina de login
					loginView(null);
					break;

				case '3':
                    //Pagina de recuperacion de contraseña
					recoverPassView();
					break;

                case '4':
                    //Pagina de navegador
                    //[0] = data; [1] = errores
                    $dataCategorias = chargeCategories();
                    $dataAnuncios = chargeAnuncios(); 
                    browserView($dataCategorias[0], $dataAnuncios[0], array_merge($dataAnuncios[1],$dataCategorias[1]));
                    break;
                    
                case '5':
                    //Pagina de nuevo anuncio
                    $dataCategorias = chargeCategories();	
                    newAnuncioView($dataCategorias[0], $dataCategorias[1]);
                    break;
				case '6':
                	//Pagina de modificar perfil de usuario
                	editProfileView();
                	break;                    
                case '7':
                    //Pagina de anuncio
                    $dataAnuncio = chargeAnuncio();
                    anuncioView($dataAnuncio[0], $dataAnuncio[1]);
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
					
                    if ($ret == 0) {
						header("Location:controller.php");
 						exit;
 					}else{
                        signUpView($ret);
                    }
					break;
				
				case '2':
					$ret = loginUser();

					if ($ret == 0){
						header("Location:controller.php");
 						exit;
 					}else{
                        loginView($ret);
                    }
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
                    if ($ret[0] == '0'){
                        //Si todo se ha realizado correctamente:
                        //Redirigir al usuario a la pagina del anuncio
                        $dataCategorias = chargeCategories();
                        newAnuncioView($dataCategorias[0], array_merge($dataCategorias[1],$ret[1]));     
                    }else{
                        $dataCategorias = chargeCategories();
                        newAnuncioView($dataCategorias[0], array_merge($dataCategorias[1],$ret[1]));                     
                    }
					break;
				case '6':
					$ret = editProfile();
					switch ($ret) {						
						case '1':
							editProfileView('La contraseña es incorrecta');
							break;						
						default:
							header("Location:controller.php");
							break;
					}
				default:
					# code...
					break;
			}
			break;
		
		default:
			
			break;
	}
?>