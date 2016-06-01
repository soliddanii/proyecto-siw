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
                    //Publicar un nuevo anuncio
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
                    
                case '11':
                    //Comprar un articulo
                    $ret = actualizarAnuncio();
                    if(empty($ret[2])){
                        if($ret[1] == 1){
                            //Anuncio adquirido
                            compraView();
                        }elseif($ret[1] == 0){
                            //Anuncio Cancelado
                            header("Location:controller.php?cmd=userView&id=7&idAnuncio=".$ret[0]);
                        }
                    }else{
                        errorView2($ret[2]);
                    }
                    break;
                    
				default:
					# code...
					break;
			}
			break;

		case 'adminView':
			switch ($id) {
				
				// Pagina de login
				case '0':
					if (isset($_SESSION['idAdmin']))
						frontViewAdmin();
					else	
						loginViewAdmin();
					break;

				//	Front del admin
				case '1':					
					if (!isset($_SESSION['idAdmin']))
						loginViewAdmin();
					else
						frontViewAdmin();
					break;

				// Pagina de informacion de un usuario
				case '2':
					if (isset($_GET["u"])){
						$data = getInfoUser($_GET["u"]);
						infoUserViewAdmin($data);
					}else
						echo "No existe identificacion de usuario";

					break;

				// Pagina de modificacion de un usuario
				case '3':
					if (isset($_GET["u"]))
						modifyUserViewAdmin($_GET["u"]);
					else
						echo "No existe identificacion de usuario";		
					break;			

				// Detalles de un anuncio
				case '4':
					$data = detailInfo();
					detailInfoViewAdmin($data);
					break;

				// Lista de anuncios 
				case '5':

					if (isset($_GET['state'])){
						if ($_GET['state'] == 'Activos')
							$state = 1;
						elseif ($_GET['state'] == 'Cancelados')
							$state = 0;
						elseif ($_GET['state'] == 'Vendidos')
							$state = 2;
						else
							$state = $_GET['state'];
					}
										
					if (isset($_POST['ordenar']) || isset($_GET['ordenar'])){
						if ($_POST['ordenar'] == 'new' || $_GET['ordenar'] == 'new' )
							$ordenar = 1;
						elseif ($_POST['ordenar'] == 'old' || $_GET['ordenar'] == 'old')
							$ordenar = 2;						
						elseif ($_POST['ordenar'] == 'cheap' || $_GET['ordenar'] == 'cheap')
							$ordenar = 3;
						elseif ($_POST['ordenar'] == 'expensive' || $_GET['ordenar'] == 'expensive')
							$ordenar = 4;
						elseif (isset($_POST['ordenar']))
							$ordenar = $_POST['ordenar'];
						elseif (isset($_GET['ordenar']))
							$ordenar = $_GET['ordenar'];						

						$_SESSION['ordenar'] = $ordenar;											
						
						$data = getAnuncios($state,$ordenar);
						listAnuncioViewAdmin($data,$state,$ordenar);
						
					}
					else{
						$_SESSION['ordenar'] = 0;
						$data = getAnuncios($state,false);
						listAnuncioViewAdmin($data,$state,0);
					}

					break;			

				default:
					# code...
					break;
			}// end switch adminView
			break;
			
		case 'adminCmd':
			switch ($id) {
				
				// Petición de login
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
				
				// Cerrar sesión
				case '1':
					logOut();
					header("Location:controller.php?cmd=adminView");
					break;

				// Gestion de usuarios
				case '2':
					
					$sizeElements = 10;

					if (!isset($_GET["order"])){
						// Cargar la pagina incial
						consultUsersView(null,null,null);

					}elseif (isset($_GET["order"]) && isset($_GET["orderby"])){  
						// Listar segun un criterio
						if (isset($_GET["nextPage"])){
							$startPage = ($_GET["nextPage"]-1)*$sizeElements;
							$endPage = $startPage + $sizeElements;							
						}else{
							$startPage = ($_GET["currentPage"]-1)*$sizeElements;
							$endPage = $startPage + $sizeElements;
						}
						
						$data   = consultUsers($_GET["orderby"],$startPage,$endPage);						
						$numUsers  = getNumUsers();
						consultUsersView($data,$numUsers,$sizeElements,true);

					}elseif (isset($_GET["order"])){ 
						// Listar todos los usuarios
						$startPage = 0;
						$endPage = 10;
						$data   = consultUsers(null,$startPage,$endPage);
						$numUsers  = getNumUsers();
						consultUsersView($data,$numUsers,$sizeElements,false);

					}
					
					break;

				// Modificar usuario					
				case '3':
						modifyUser($_GET["u"]);						
						header("Location:controller.php?cmd=adminCmd&id=2&order=false");						
						break;

				// Eliminar usuarios
				case '4':
						deleteUser($_GET["u"]);
						header("Location:controller.php?cmd=adminCmd&id=2$order=false");
						break;
				
				// Eliminar anuncio
				case '5':
					if (isset($_GET['command'])){
						if ($_GET['command'] == 'cancel'){
							controlAnuncio($_GET['idAnuncio'],0);
							//header('Location: ' . $_SERVER['HTTP_REFERER']);
							header("Location: controller.php?cmd=adminView&id=5&state=1&ordenar=".$_SESSION['ordenar']);
						}elseif ($_GET['command'] == 'active'){										
							controlAnuncio($_GET['idAnuncio'],1);
							header("Location: controller.php?cmd=adminView&id=5&state=0&ordenar=".$_SESSION['ordenar']);
							//header('Location: ' . $_SERVER['HTTP_REFERER']);
						}
					}else
						echo "error en eliminar anuncio";
					break;

				default:
					break;
			}// end switch adminCmd
			break;
    
    default:		
		break;
	}
?>