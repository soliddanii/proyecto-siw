<?php
    
	// Clases que vamos a utilizar	
	require_once 'class/connection.php';
	require_once 'class/facade.php';
	require_once 'class/sessions.php';
    require_once 'class/upload.php'; //subida de imagenes
    require_once 'class/pdf/makepdf.php'; //generar PDF

	/////////////////////////////////////////////////////////////////////////
	// 							Gestión de Sesion
	/////////////////////////////////////////////////////////////////////////
	function initSession(){
		
		$objSe = new Session();
		$objSe->init();
		
	}

	function setVarSession($varName,$varValue){
		$_SESSION[$varName] = $varValue;
	}

	/////////////////////////////////////////////////////////////////////////
	// 							Gestión de Usuarios
	/////////////////////////////////////////////////////////////////////////

	/*
	*	Return:
	*		 0 : usuario registrado correctamente	
	*    array : Se ha producido algun error y se devuelve con los detalles
	*/
	function signUp(){
    
        //Array para guardar los posibles errores que encontremos
        $errorList = array(); //Array de arrays: error("errorCode" => "code", "message" => "mensaje" )
    
		if(isset($_POST["nameuser"]) && isset($_POST["email"]) 
			&& isset($_POST["passwd0"]) && isset($_POST["passwd1"])){
            
            $name = '';
			$user  = filter_var($_POST["nameuser"],FILTER_SANITIZE_STRING);
			$email = filter_var($_POST["email"],FILTER_SANITIZE_STRING);
			$pwd  = filter_var($_POST["passwd0"],FILTER_SANITIZE_STRING);
            
            if(isset($_POST["name"])){
                $name  = filter_var($_POST["name"],FILTER_SANITIZE_STRING);
            }
            
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                array_push($errorList, array('errorCode' => '1', 'message' => "Formato de Email Inválido."));
                return $errorList;
            }

			$con = new Connection();
			$facade = new Facade($con);

			if($facade->existNameUser($user)){

				$con->close();
				array_push($errorList, array('errorCode' => '1', 'message' => "Lo sentimos, el nombre de usuario ya existe."));

			}else{

				$data = array("user"=>$user, "name"=>$name, "email"=>$email, "pwd"=>md5($pwd));				
				
				if($facade->insertUser($data)){
					
					$data = $facade->getIdUser($user);
					session_unset();
					setVarSession("idUser",$data["idUser"]);
					setVarSession("user",$data["user"]);					
					
					$con->close(); 

					return '0'; 								
 
				}else{
					$con->close();
					array_push($errorList, array('errorCode' => '2', 'message' => "Se ha producido un error con la BBDD"));
				}				
			}
		}else {
			array_push($errorList, array('errorCode' => '-1', 'message' => "No se han proporcionado todos los datos necesarios."));
		}		
        
        //Devolvemos los datos y los reportes de errores (si hay)
        return $errorList;

	}

	/*
	*	Return:
	*		 1 : usuario no consta en la bbdd
	*		 0 : usuario consta en la bbdd
	*		-1 : no se ha podido tomar datos de la bbdd o no existe variables
	*			 POST
	*
	*/
	function loginUser(){
    
        //Array para guardar los posibles errores que encontremos
        $errorList = array(); //Array de arrays: error("errorCode" => "code", "message" => "mensaje" )
        
		if(isset($_POST["name"]) && isset($_POST["passwd0"])){
			
			$user = filter_var($_POST["name"], FILTER_SANITIZE_STRING);
			$pwd  = filter_var($_POST["passwd0"], FILTER_SANITIZE_STRING);
			
            //Establecer la conexion a la BBDD
			$con    = new Connection();			
			$facade = new Facade($con);

			if($facade->existUser($user, md5($pwd))){

				$data = $facade->getIdUser($user);
                session_unset();
				setVarSession("idUser",$data["idUser"]);
				setVarSession("user",$data["user"]);
				$con->close();
				return '0';

			}else {
				$con->close();
				array_push($errorList, array('errorCode' => '1', 'message' => "El usuario no existe, vuelva a intentarlo."));
			}				

		}else {
			array_push($errorList, array('errorCode' => '-1', 'message' => "No se ha proporcionado un nick o una contraseña."));
		}
        
        //Devolvemos los datos y los reportes de errores (si hay)
        return $errorList;

	}

	/*
	*	Return:
	*		password: el usuario existe en la bbdd
	*		1 : el usuario no existe en la bbdd
	*	 -1 : ha ocurrido un error con la bbdd
	*/
	function recoverPass(){
    
        //Array para guardar los posibles errores que encontremos
        $errorList = array(); //Array de arrays: error("errorCode" => "code", "message" => "mensaje" )
        
		if(isset($_POST["nameuser"]) && isset($_POST["newpass"])) {

			$user  = filter_var($_POST["nameuser"],FILTER_SANITIZE_STRING);
			$pass = filter_var($_POST["newpass"],FILTER_SANITIZE_STRING);

			$con = new Connection();
			$facade = new Facade($con);

            if($facade->existNameUser($user)){
                $result = $facade->recoverPass($user, md5($pass));

                if(!$result) {
                    array_push($errorList, array('errorCode' => '-1', 'message' => "Se ha producido un error."));
                }
            }else{
                array_push($errorList, array('errorCode' => '2', 'message' => "El nombre de Usuario no existe."));
            }
            
            $con->close();
		}else{
            array_push($errorList, array('errorCode' => '3', 'message' => "No se han proporcionado todos los datos necesarios."));
        }
        
        return $errorList;
	}

	function editProfile(){
    
        //Array para guardar los posibles errores que encontremos
        $errorList = array(); //Array de arrays: error("errorCode" => "code", "message" => "mensaje" )

		if(isset($_SESSION['idUser']) && isset($_SESSION['user'])){
			
			$con = new Connection();
			$facade = new Facade($con);		

			$id = $_SESSION['idUser'];

            //NOMBRE
			if(isset($_POST['name']) && strlen($_POST['name']) > 0 ){
				$name = filter_var($_POST['name'],FILTER_SANITIZE_STRING);				
				$facade->editName($id,$name);
			}

            //EMAIL
			if(isset($_POST['email']) && strlen($_POST['email']) > 0){
				$email = filter_var($_POST['email'],FILTER_SANITIZE_STRING);
                if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    $facade->editEmail($id,$email);
                }else{
                    array_push($errorList, array('errorCode' => '1', 'message' => "Formato de Email Inválido."));
                }			
			}	
			
            //Contraseña
			if(isset($_POST['passwd0']) && isset($_POST['passwd1']) 
				&& strlen($_POST['passwd0']) > 0 && strlen($_POST['passwd1']) > 0){
				$passwd0 = filter_var($_POST['passwd0'],FILTER_SANITIZE_STRING);
				$passwd1 = filter_var($_POST['passwd1'],FILTER_SANITIZE_STRING);

				if($facade->existUser($_SESSION['user'], md5($passwd0)))
					$facade->editPass($id, md5($passwd1));
				else{
					array_push($errorList, array('errorCode' => '1', 'message' => "La contraseña es incorrecta."));
                }
			}

		}else{
			array_push($errorList, array('errorCode' => '-1', 'message' => "No existe sesión de usuario"));
        }
        
        $con->close();
        return $errorList;

	}

	function logout(){
		session_unset();
		session_destroy();
	}

	/////////////////////////////////////////////////////////////////////////
	/////////////////////////////////////////////////////////////////////////

    /////////////////////////////////////////////////////////////////////////
	// 							Gestión de los anuncios 
	/////////////////////////////////////////////////////////////////////////
    /*
	*	Return:
    *        0 : La insercion del anuncio se ha realizado con exito
	*		 1 : El usuario no está logueado
	*		 2 : Ocurrio un error con la bbdd
	*		 3 : Los campos enviados no son validos o falta alguno
    *        
	*
	*/
	function newAnuncio(){

        //Array para guardar los posibles errores que encontremos
        $errorList = array(); //Array de arrays: error("errorCode" => "code", "message" => "mensaje" )
        $return = '-1';
        
        //Obtener los datos del usuario que crea el anuncio
        if(isset($_SESSION["idUser"])){
            $idUser = $_SESSION["idUser"];
        }else{
            array_push($errorList, array('errorCode' => '1', 'message' => "No existe ninguna sesión de usuario."));
            return array('-1',$errorList);
        }
            
        
        //Conectar con la base de datos
        $con    = new Connection();			
        $facade = new Facade($con);
            
        //Obtener los datos del anuncio
		if(isset($_POST["titulo"]) && (strlen($_POST['titulo']) > 0) && isset($_POST["localizacion"]) && (strlen($_POST['localizacion']) > 0)){
			
			$titulo = filter_var($_POST["titulo"], FILTER_SANITIZE_STRING);
			$localizacion  = filter_var($_POST["localizacion"], FILTER_SANITIZE_STRING);
            
            $idCategoria = 0;
            if (isset($_POST["categoria"]) && is_numeric($_POST["categoria"])){
                $idCategoria = intval($_POST["categoria"]);
            }else{
                array_push($errorList, array('errorCode' => '3', 'message' => "Los valores recibidos son erroneos o falta alguno."));
                return array('-1',$errorList);
            }
            
            $telefono = "";
            if (isset($_POST["telefono"])){
                $telefono = filter_var($_POST["telefono"], FILTER_SANITIZE_STRING);
            }
            
            $precio = 0.00;
            if (isset($_POST["precio"]) && is_numeric($_POST["precio"])){
                $precio = floatval($_POST["precio"]);
            }
            
            $descripcion = "";
            if (isset($_POST["descripcion"])){
                $descripcion = filter_var($_POST["descripcion"], FILTER_SANITIZE_STRING);
            }
        
            $data = array("idUser"=>$idUser, "idCategoria"=>$idCategoria, "precio"=>$precio, "titulo"=>$titulo, 
                    "descripcion"=>$descripcion, "localizacion"=>$localizacion, "telefono"=>$telefono);				
				
			if(!$facade->addAnuncio($data)){
				$con->close();
                array_push($errorList, array('errorCode' => '2', 'message' => "Se ha producido un error con la BBDD."));
                return array('-1',$errorList);
            }

		}else {
            $con->close();
			array_push($errorList, array('errorCode' => '3', 'message' => "Los valores recibidos son erroneos o falta alguno."));
            return array('-1',$errorList);
		}
        
            
        //Obtener y procesar las imagenes
        $idAnuncio = $facade->getLastId();
        if(!$idAnuncio || $idAnuncio == 0){
            $con->close();
            array_push($errorList, array('errorCode' => '2.1', 'message' => "Se ha producido un error al recuperar el indice del Anuncio."));
            return array('-1',$errorList);
        }
        
        $return = $idAnuncio;
        
        $upload = new Upload();
        $ret = $upload->processUploads($idAnuncio);
        $ret_err = $ret[0];
        $ret_inf = $ret[1];
        
        array_merge($errorList, $ret_err);
        
        if (empty($ret_inf)){
            //No hay info de ninguna imagen
            $con->close();
            return array($return, $errorList);
        }else{
            //Almenos una imagen ha sido guardada
            for($i=0; $i<count($ret_inf); $i++){
            
                $data = array("idAnuncio"=>$idAnuncio, "idImagen"=>$ret_inf[$i][0], 
                "big"=>$ret_inf[$i][3], "medium"=>$ret_inf[$i][2], "small"=>$ret_inf[$i][1]);
            
                if(!$facade->addImage($data)){
                    array_push($errorList, array('errorCode' => '2', 'message' => "Se ha producido un error con la BBDD."));
                }
            }  
        }
        
        $con->close();
        return array($return, $errorList);
         
	}
    
    function chargeAnuncios(){
    
        //Array para guardar los posibles errores que encontremos
        $errorList = array(); //Array de arrays: error("errorCode" => "code", "message" => "mensaje" )
        
        //Establecer la conexion a la BBDD
		$con    = new Connection();			
		$facade = new Facade($con);

        //Fijar las condiciones para obtener los anuncios
        $localizacion = "";
        $idCategoria = "";
        $priceMin = "";
        $priceMax = "";
        $titulo = "";
        $idUser = "";
        $orden = 0;
        $misAnuncios = 0;
        $misFavoritos = 0;
        $columnNameOrder = "fecha";
        $order = "DESC";
        $page = 1;
        $maxPerPage = 8; //MAXIMO DE ANUNCIOS POR PAGINA
        
        
        if (isset($_POST["categoria"]) && is_numeric($_POST["categoria"])){
            $idCategoria = intval($_POST["categoria"]);
            if($idCategoria == -1){
                $idCategoria = "";
            }
        }

        if (isset($_POST["priceMin"]) && is_numeric($_POST["priceMin"])){
            $priceMin = floatval($_POST["priceMin"]);
        }

        
        if (isset($_POST["priceMax"]) && is_numeric($_POST["priceMax"])){
            $priceMax = floatval($_POST["priceMax"]);
        }        

        if (isset($_POST["titulo"])){
            $titulo = filter_var($_POST["titulo"], FILTER_SANITIZE_STRING);
        }    

        if (isset($_POST["localizacion"])){
            $localizacion = filter_var($_POST["localizacion"], FILTER_SANITIZE_STRING);
        }

        if (isset($_POST["orden"])){
            $orden = intval($_POST["orden"]);
        }

        if (isset($_POST["user"]) || isset($_GET["user"])){
            $misAnuncios = 1;
        }
        
        if (isset($_POST["favoritos"]) || isset($_GET["favoritos"])){
            $misFavoritos = 1;
        }
        
        if(isset($_SESSION["idUser"])){
            $idUser = $_SESSION["idUser"];
        }else{
            if($misFavoritos == 1 || $misAnuncios == 1){
                array_push($errorList, array('errorCode' => '4', 'message' => "Necesita estar registrado para visualizar sus favoritos o sus Anuncios."));
            }
        }
        
        if (isset($_GET["pagina"]) && is_numeric($_GET["pagina"])){
            $page = intval($_GET["pagina"]);
            if($page < 1){ $page = 1; }
        }

        $condiciones = array("precioMin" => $priceMin, "precioMax" => $priceMax, "idUser" => $idUser,
        "titulo" => $titulo, "localizacion" => $localizacion, "categoria" => $idCategoria);
        
        switch ($orden){
            case 0:
                $columnNameOrder = "fecha";
                $order = "DESC";
                break;
                
            case 1:
                $columnNameOrder = "fecha";
                $order = "ASC";
                break;
                
            case 2:
                $columnNameOrder = "precio";
                $order = "ASC";
                break;
                
            case 3:
                $columnNameOrder = "precio";
                $order = "DESC";
                break;
        }
        
        $fromLimit = ($page-1)*$maxPerPage;
        
		$result = $facade->getAnuncios($condiciones, $columnNameOrder, $order, $fromLimit, $maxPerPage, $misAnuncios, $misFavoritos);
        
        //Inicializacion de los datos
        $data = array();
 
		if($result){
			if(mysqli_num_rows($result) > 0) {
				
				while($row = mysqli_fetch_array($result)) {
                    
                    $esMio = false;
                    if($row['idUser'] == $idUser){ $esMio = true; }
                    
                    $esFav = false;
                    //if($row['idUser'] == $idUser){ $esMio = true; }
                
					$temp_data = array('id'=>$row['idAnuncio'], 'titulo'=>$row['titulo'], 'idUser'=>$row['idUser'], 'esMio'=>$esMio, 'esFav'=>$esFav,
                    'localizacion'=>$row['localizacion'], 'precio'=>$row['precio'], 'fecha'=>$row['fecha'], 'miniatura'=>$row['small'], 'estado'=>$row['estado']);
                    
                    if($row['small'] == null){
                        $temp_data['miniatura'] = '../images/default-product.png';
                    }
                    array_push($data, $temp_data);
				}
                
			}else
				array_push($errorList, array('errorCode' => '2', 'message' => "No se ha encontrado ningún artículo."));
		}else{
			array_push($errorList, array('errorCode' => '1', 'message' => "Error al consultar la BBDD."));
        }
        
        $con->close(); 
        //Devolvemos los datos y los reportes de errores (si hay)
        //ChromePhp::log($data);
        return array($data, $errorList, $misAnuncios, $misFavoritos, $page);

	}
    
    function chargeAnuncio(){
        //Array para guardar los posibles errores que encontremos
        $errorList = array(); //Array de arrays: error("errorCode" => "code", "message" => "mensaje" )
        
        //Inicializamos en array de datos [info, img, comentarios, isLoguedIn, isFavorito, itsMine] 
        $data = array(array(), array(), array(), false, false, false); 
        
        //Establecer la conexion a la BBDD
		$con    = new Connection();			
		$facade = new Facade($con);
        
        if ((isset($_POST["idAnuncio"]) && is_numeric($_POST["idAnuncio"])) 
            || (isset($_GET["idAnuncio"]) && is_numeric($_GET["idAnuncio"]))){
            
            $idAnuncio = -1;
            if (isset($_GET["idAnuncio"])){
                $idAnuncio = intval($_GET["idAnuncio"]);
            }
            if (isset($_POST["idAnuncio"])){
                $idAnuncio = intval($_POST["idAnuncio"]);
            }
             
            
            $result = $facade->getAnuncio($idAnuncio);
             if($result && (mysqli_num_rows($result) > 0)){ 
         
                //Procesar los datos del anuncio 
                $row = mysqli_fetch_array($result);    
                $data_inf = array('id'=>$row['idAnuncio'], 'idUser'=>$row['idUser'], 'fecha'=>$row['fecha'], 
                    'titulo'=>$row['titulo'], 'precio'=>$row['precio'], 'descripcion'=>$row['descripcion'], 
                    'localizacion'=>$row['localizacion'], 'telefono'=>$row['telefono'], 'estado'=>$row['estado'], 
                    'idComprador'=>$row['idComprador'], 'nickSeller'=>$row['nick'], 'nameSeller'=>$row['name']); 
                $data[0] = $data_inf;     
                 
                     
                //Obtener las imagenes del anuncio 
                $result = $facade->getImages($idAnuncio); 
                if($result){ 
                    if(mysqli_num_rows($result) > 0){ 
                        $data_img = array(); 
                        while($row = mysqli_fetch_array($result)){ 
                            $data_img_tmp = array('id'=>$row['idImagen'], 'small'=>$row['small'], 'medium'=>$row['medium'], 'big'=>$row['big']); 
                            array_push($data_img, $data_img_tmp); 
                        }   
                        $data[1] = $data_img; 
                    } 
                }else{ 
                    //Error al recuperar las imagenes 
                    array_push($errorList, array('errorCode' => '3', 'message' => "No se han podido recuperar las imagenes asociadas al anuncio.")); 
                } 
                     
                     
                     
                //Obtener los comentarios del anuncio 
                $result = $facade->getComentarios($idAnuncio); 
                if($result){ 
                    if(mysqli_num_rows($result) > 0){ 
                        $data_cmt = array(); 
                        while($row = mysqli_fetch_array($result)){ 
                            $data_cmt_tmp = array('id'=>$row['idComentario'], 'idUser'=>$row['idUser'], 'comentario'=>$row['comentario'] 
                                , 'idPadre'=>$row['idPadre'], 'fecha'=>$row['fecha'], 'nickAutor'=>$row['nick'], 'nickPadre'=>$row['nickPadre']); 
                            array_push($data_cmt, $data_cmt_tmp); 
                        }   
                        $data[2] = $data_cmt; 
                    } 
                }else{ 
                    //Error al recuperar los comentarios 
                    array_push($errorList, array('errorCode' => '4', 'message' => "No se han podido recuperar los comentarios asociados al anuncio.")); 
                } 
                 
                 
                //Y por ultimo, obtener informacion del usuario y de si este anuncio es suyo o es su favorito 
                if(isset($_SESSION["idUser"])){ 
                    $data[3] = true;
                    $idUser = $_SESSION["idUser"]; 
                    if($facade->isFavorito($idUser, $idAnuncio)){ 
                        $data[4] = true; 
                    } 
                    if($data[0]['idUser'] == $idUser){ 
                        $data[5] = true; 
                    } 
                } 
                 
            }else{ 
                //Error al obtener resultados del anuncio de la BBDD 
                array_push($errorList, array('errorCode' => '2', 'message' => "No se ha podido recuperar ningún dato coincidente de la BBDD.")); 
            } 
            
        }else{
            //Error no hay parametro de anuncio 
            array_push($errorList, array('errorCode' => '1', 'message' => "No se ha especificado el anuncio a recuperar."));
        }
    
    
        $con->close(); 
        //Devolvemos los datos y los reportes de errores (si hay)  
        return array($data, $errorList);
    }
    
    function descargaAnuncio($dataAnuncio){
        
        $makepdf = new MakePDF();
        $ret = $makepdf->createAndDownload($dataAnuncio);
        return $ret;
        
    }
    
    function actualizarAnuncio(){
        
        //Array para guardar los posibles errores que encontremos
        $errorList = array(); //Array de arrays: error("errorCode" => "code", "message" => "mensaje" )
        $idAnuncio = -1;
        $accion = -1;
        
        //Comprobar que es un usuario logueado
        if(isset($_SESSION["idUser"])){ 
            $idUser = $_SESSION["idUser"];
            
            if(isset($_POST["idAnuncio"]) && is_numeric($_POST["idAnuncio"])){
                $idAnuncio = intval($_POST["idAnuncio"]);
                
                //Establecer la conexion a la BBDD
                $con    = new Connection();			
                $facade = new Facade($con);
                 
                //Comprobar si el anuncio esta activo antes de seguir
                $aux = mysqli_fetch_array($facade->anuncioGetEstado($idAnuncio));
                $aux2 = intval($aux['estado']);
                //ChromePhp::log($aux2);
                if($aux2 == 1){
                    //Comprobar si el anuncio es mio:
                    if($facade->esMiAnuncio($idAnuncio, $idUser)){
                        //Si es mio, la accion es cancelarlo
                        $accion = 0;
                        if(!$facade->actualizarCanceladoAnuncio($idAnuncio)){
                            array_push($errorList, array('errorCode' => '3', 'message' => "Se ha producido un error inesperado en el proceso de Cancelación."));
                        } 
                    }else{
                        //Si el anuncio no es mio, la accion es comprarlo
                        $accion = 1;
                        if(!$facade->actualizarVendidoAnuncio($idAnuncio, $idUser)){
                            array_push($errorList, array('errorCode' => '2', 'message' => "Se ha producido un error inesperado en el proceso de Compra."));
                        }
                    }

                }else{
                    array_push($errorList, array('errorCode' => '2', 'message' => "No se puede acceder a un anuncio cancelado o vendido."));
                }
                

                $con->close();
                
            }else{
                array_push($errorList, array('errorCode' => '-1', 'message' => "Error en la compra. No se a especificado el anuncio."));
            }
        
        }else{
            array_push($errorList, array('errorCode' => '1', 'message' => "Es necesario estar registrado para adquirir artículos."));
        }
        
        return array($idAnuncio, $accion, $errorList);
        
    }
    
    /////////////////////////////////////////////////////////////////////////
	// 							Gestión de los Favoritos
	/////////////////////////////////////////////////////////////////////////
    
    function addOrDeleteFavorite(){
    
        //Array para guardar los posibles errores que encontremos
        $errorList = array(); //Array de arrays: error("errorCode" => "code", "message" => "mensaje" )
        
        $idAnuncio = -1;
        
        //Comprobar que es un usuario logueado
        if(isset($_SESSION["idUser"])){ 
            $idUser = $_SESSION["idUser"];
            
            if(isset($_POST["idAnuncio"]) && is_numeric($_POST["idAnuncio"])){
                
                //Establecer la conexion a la BBDD
                $con    = new Connection();			
                $facade = new Facade($con);
                
                $idAnuncio = intval($_POST["idAnuncio"]);
                if($facade->isFavorito($idUser, $idAnuncio)){
                    //Eliminar Favorito
                    if(!$facade->deleteFavorito($idUser, $idAnuncio)){
                        array_push($errorList, array('errorCode' => '2', 'message' => "No se ha podido eliminar de favoritos."));
                    }
                }else{
                    //Añadir Favorito
                    if(!$facade->addFavorito($idUser, $idAnuncio)){
                        array_push($errorList, array('errorCode' => '3', 'message' => "No se ha podido añadir a favoritos."));
                    }
                }
                
                $con->close();
                
            }else{
                array_push($errorList, array('errorCode' => '1', 'message' => "No se ha especificado ningun anuncio."));
            }
        
        }else{
            array_push($errorList, array('errorCode' => '-1', 'message' => "Es necesario estar registrado getionar los Favoritos."));
        }
        
        return array($idAnuncio, $errorList);
    }
    
    
    /////////////////////////////////////////////////////////////////////////
	// 							Gestión de los comentarios 
	/////////////////////////////////////////////////////////////////////////
    function guardarComentario(){
    
        //Array para guardar los posibles errores que encontremos
        $errorList = array(); //Array de arrays: error("errorCode" => "code", "message" => "mensaje" )
        $idAnuncio = -1;
        
        //Comprobar que es un usuario logueado
        if(isset($_SESSION["idUser"])){ 
            $idUser = $_SESSION["idUser"];
        
            //Comprobar que es un comentario valido para un anuncio valido
            if(isset($_POST["idAnuncio"]) && is_numeric($_POST["idAnuncio"]) 
                && isset($_POST["comentario"]) && (strlen($_POST['comentario']) > 0) && (strlen($_POST['comentario']) <= 400)){
                
                //Establecer la conexion a la BBDD
                $con    = new Connection();			
                $facade = new Facade($con);
                
                $idAnuncio = intval($_POST["idAnuncio"]);
                $comentario = filter_var($_POST["comentario"], FILTER_SANITIZE_STRING);
                
                //Verificar si es respuesta a otro comentario
                $idRespuesta = -1;
                if(isset($_POST["idComentarioRespuesta"]) && is_numeric($_POST["idComentarioRespuesta"])){
                    $idRespuesta = intval($_POST["idComentarioRespuesta"]);
                    
                    //Existe realmente ese comentario padre en ese anuncio?
                    if($idRespuesta >= 0){
                        if(!$facade->existeComentario($idRespuesta, $idAnuncio)){
                            array_push($errorList, array('errorCode' => '2', 'message' => "El comentario al que se hace referencia no existe o no se pudo comprobar."));
                            $idRespuesta = -1;
                        }
                    }
                }
                      
                //Insertar Comentario
                if(!$facade->insertarComentario($idUser, $idAnuncio, $comentario, $idRespuesta)){
                    array_push($errorList, array('errorCode' => '3', 'message' => "No se ha podido guardar el comentario."));
                }
                
                $con->close();
                
            }else{
                array_push($errorList, array('errorCode' => '1', 'message' => "El anuncio o el comentario no es válido."));
            }
        
        }else{
            array_push($errorList, array('errorCode' => '-1', 'message' => "Es necesario estar registrado para dejar un comentario."));
        }
        
        return array($idAnuncio, $errorList);
    
    }

    function borrarComentario(){
    
        //Array para guardar los posibles errores que encontremos
        $errorList = array(); //Array de arrays: error("errorCode" => "code", "message" => "mensaje" )
        $idAnuncio = -1;
        
        //Comprobar que es un usuario logueado
        if(isset($_SESSION["idUser"])){ 
            $idUser = $_SESSION["idUser"];
        
            //Comprobar que es un comentario valido para un anuncio valido
            if(isset($_POST["idAnuncio"]) && is_numeric($_POST["idAnuncio"]) 
                && isset($_POST["idComentario"]) && is_numeric($_POST["idComentario"])){
                
                //Establecer la conexion a la BBDD
                $con    = new Connection();			
                $facade = new Facade($con);
                
                $idAnuncio = intval($_POST["idAnuncio"]);
                $idComentario = intval($_POST["idComentario"]);
                
                //Verificar que el anuncio es mio (y por lo tanto tengo permisos de borrar comentarios
                if($facade->esMiAnuncio($idAnuncio, $idUser)){
                    //Borrar
                    if(!$facade->borrarComentario($idAnuncio, $idComentario)){
                        array_push($errorList, array('errorCode' => '3', 'message' => "No se ha podido borrar el comentario."));
                    }
                }else{
                    array_push($errorList, array('errorCode' => '4', 'message' => "El anuncio no te pertenece. No puedes borrar comentarios."));
                }
  
                $con->close();
                
            }else{
                array_push($errorList, array('errorCode' => '1', 'message' => "El anuncio o el comentario no es válido."));
            }
        
        }else{
            array_push($errorList, array('errorCode' => '-1', 'message' => "Como vas a borrar el comentario si no estas ni registrado."));
        }
        
        return array($idAnuncio, $errorList);
    
    }    
    
    
	/////////////////////////////////////////////////////////////////////////
	// 							Gestión de Categorias 
	/////////////////////////////////////////////////////////////////////////
	function chargeCategories(){
    
        //Array para guardar los posibles errores que encontremos
        $errorList = array(); //Array de arrays: error("errorCode" => "code", "message" => "mensaje" )

        //Establecer la conexion a la BBDD
		$con    = new Connection();			
		$facade = new Facade($con);

		$result = $facade->getCategories();

        //Inicializacion de los datos
        $data = array();
        
		if($result){
			if(mysqli_num_rows($result) > 0) {

				while($row = mysqli_fetch_array($result)) {
					$data[$row['idCategoria']] = $row['categoria'];
				}

			}else
				array_push($errorList, array('errorCode' => '2', 'message' => "No se ha obtenido ningúna categoria"));
		}else{
			array_push($errorList, array('errorCode' => '1', 'message' => "Error al consultar la BBDD"));
        }
        
        //Devolvemos los datos y los reportes de errores (si hay)
        return array($data, $errorList);
	}

    /////////////////////////////////////////////////////////////////////////
    //                          Gestión de Admmin
    /////////////////////////////////////////////////////////////////////////

  function loginAdmin(){

    if(isset($_POST["user"]) && isset($_POST["password"])){

      $user  = filter_var($_POST["user"],FILTER_SANITIZE_STRING);
      $pass  = filter_var($_POST["password"],FILTER_SANITIZE_STRING);

      $con = new Connection();
      $facade = new Facade($con);

      if($facade->existAdmin($user,$pass)){
        $data = $facade->getDataAdmin($user,$pass);
        session_unset();
        setVarSession('idAdmin',$data['idAdmin']);
        setVarSession('user',$data['user']);
        return 0;
      }else{
          return 1;
      }

    }else{      
      return -1;
    }
  }
  

  function getNumUsers(){

    $con = new Connection();
    $facade = new Facade($con);

    $amount = mysqli_fetch_array($facade->numUsers());
    
    return $amount[0]; 
  }

  function consultUsers($order, $start, $end){

    $con = new Connection();
    $facade = new Facade($con);

    $result = $facade->getDataUsers($order, $start, $end);

    if (mysqli_num_rows($result) > 0){

      $data = array();

      while ($row = mysqli_fetch_array($result)){
        array_push($data, array("idUser" => $row["idUser"] , "nick" => $row["nick"], 
          "name" => $row["name"], "email" => $row["email"]));
      }      
      return $data;

    }else // No hay datos en la bbdd
      return null;

  }

  /*
  * Retorna un array en el que almacena los datos de un usuario. 
  * Datos personales y sobre sus anuncios publicados
  */

  function getInfoUser($idUser){

    $con = new Connection();
    $facade = new Facade($con);
    
    return $facade->getInfoUser($idUser);    
  }

  function detailInfo(){
    
    if (isset($_GET['idAnuncio'])){

      $con = new Connection();
      $facade = new Facade($con);

      $result = $facade->getInfoAnuncio($_GET['idAnuncio']);

      return mysqli_fetch_array($result);

    }else
      return "Ocurrió un error.";

  }

  /*
  * Devuelve la lista de anuncios, segun el estado
  */

  function getAnuncios($state,$order){

    $con = new Connection();
    $facade = new Facade($con);

    $result = $facade->getListAnuncios($state,$order);    

    return $result;

  }

  function controlAnuncio($idAnuncio,$state){
  
    $con = new Connection();
    $facade = new Facade($con);

    return $facade->newStateAnuncio($idAnuncio,$state);

  }

  /*
  *
  */
  function modifyUser($idUser){

    $con = new Connection();
    $facade = new Facade($con);

    if (isset($_POST["name"]))
      $facade->editName($idUser,$_POST["name"]);
    

    if (isset($_POST["email"]))
      $facade->editEmail($idUser,$_POST["email"]);

  }

  function deleteUser($idUser){

    $con = new Connection();
    $facade = new Facade($con);

    $facade->deleteUser($idUser);

  }
?>