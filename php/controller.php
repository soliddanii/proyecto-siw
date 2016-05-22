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
					recoverPassView(null);
					break;

                case '4':
                    //Pagina de navegador
                    //[0] = data; [1] = errores
                    $dataCategorias = chargeCategories();
                    $dataAnuncios = chargeAnuncios(); 
                    browserView($dataCategorias[0], $dataAnuncios[0],
                        array_merge($dataAnuncios[1],$dataCategorias[1]), $dataAnuncios[2], $dataAnuncios[3], $dataAnuncios[4]);
                    break;
                    
                case '5':
                    //Pagina de nuevo anuncio
                    $dataCategorias = chargeCategories();	
                    newAnuncioView($dataCategorias[0], $dataCategorias[1]);
                    break;
                    
				case '6':
                	//Pagina de modificar perfil de usuario
                	editProfileView(null);
                	break;    
                    
                case '7':
                    //Pagina de anuncio
                    $dataAnuncio = chargeAnuncio();
                    if(empty($dataAnuncio[0][0]) || $dataAnuncio[0][0] == null){
                        //Si no hay datos, directamente a la pagina de errores
                        errorView2($dataAnuncio[1]);
                    }
                    anuncioView($dataAnuncio[0], $dataAnuncio[1]);
                    break;
                    
                case '8':
                    //Navegador (PETICIONES AJAX)
                    $dataAnuncios = chargeAnuncios();
                    browserViewJSON($dataAnuncios[0], $dataAnuncios[1], $dataAnuncios[4]);
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
 					//exit;
					break;

				case '4':
                    //Recuperar la contraseña
					$ret = recoverPass();
					if (empty($ret)){
						header("Location:controller.php");
					}else{
						recoverPassView($ret);
                    }
					break;
                    
                case '5':
                    $ret = newAnuncio();
                    if ($ret[0] != '-1'){
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
                    //Editar el perfil
					$ret = editProfile();
                    if(empty($ret)){
                        header("Location:controller.php");
                    }else{
                        editProfileView($ret);
                    }
                    break;
                  
                case '7':
                    //Añadir o eliminar de favoritos 
                    $ret = addOrDeleteFavorite();
                    if(empty($ret[1])){
                        header("Location:controller.php?cmd=userView&id=7&idAnuncio=".$ret[0]);
                    }else{
                        $dataAnuncio = chargeAnuncio();
                        anuncioView($dataAnuncio[0], array_merge($dataAnuncio[1], $ret[1]));
                    } 
                    break;
                
                case '8':
                    //Descargar anuncio en PDF
                    $dataAnuncio = chargeAnuncio();
                    if(!empty($dataAnuncio[0][0])){
                        descargaAnuncio($dataAnuncio[0]);
                    }else{
                        errorView2($dataAnuncio[1]);
                    }
                    break;
                 
                case '9':                 
                    //Publicar un nuevo comentario
                    $ret = guardarComentario();
                    if(empty($ret[1])){
                        header("Location:controller.php?cmd=userView&id=7&idAnuncio=".$ret[0]);
                    }else{
                        $dataAnuncio = chargeAnuncio();
                        anuncioView($dataAnuncio[0], array_merge($dataAnuncio[1], $ret[1]));
                    }    
                    break;
                    
                case '10':
                    //Borrar un comentario
                    $ret = borrarComentario();
                    if(empty($ret[1])){
                        header("Location:controller.php?cmd=userView&id=7&idAnuncio=".$ret[0]);
                    }else{
                        $dataAnuncio = chargeAnuncio();
                        anuncioView($dataAnuncio[0], array_merge($dataAnuncio[1], $ret[1]));
                    }
                    break;
                
				default:
					# code...
					break;
			}
			break;

		case 'adminView':
			switch ($id) {
				// Mostrar pagina de login
				case '0':
					if (isset($_SESSION['idAdmin']))
						frontViewAdmin();
					else	
						loginViewAdmin();
					break;
				case '1':					
					if (!isset($_SESSION['idAdmin']))
						loginViewAdmin();
					else
						frontViewAdmin();
					break;
				default:
					# code...
					break;
			}
			break;
			
		case 'adminCmd':
			switch ($id) {
				
				case '0':				
					$ret = loginAdmin();

					if ($ret == 0){
						frontViewAdmin();
					}
					elseif ($ret == 1){
						loginViewAdmin('Los datos de usuario no son correctos.');
					}elseif ($ret == -1){
						errorView('No se pudieron tomar datos');
					}
					break;
				
				case '1':
					logOut();
					header("Location:controller.php?cmd=adminView");
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