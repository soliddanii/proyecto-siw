<?php
	
    //temp for debug
    require_once '../chromephp/ChromePhp.php';
    
	// Clases que vamos a utilizar
	//require_once 'class/config.php'; // configuracion para la BBDD
	 'class/users.php'; // manejo de los usuarios
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

				$data = array("user"=>$user, "name"=>$name, "email"=>$email, "pwd"=>$pwd);				
				
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

			if($facade->existUser($user,$pwd)){

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

		if(isset($_POST["nameuser"]) && isset($_POST["email"])) {

			$user  = filter_var($_POST["nameuser"],FILTER_SANITIZE_STRING);
			$email = filter_var($_POST["email"],FILTER_SANITIZE_STRING);

			$con = new Connection();
			$facade = new Facade($con);

			$result = $facade->recoverPass($user,$email);

			if($result) {
				if(mysqli_num_rows($result) > 0) {
					$row = mysqli_fetch_array($result);
					return $row["password"];
				}else
					return 1;
			}else 
				return -1;
		}
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

				if($facade->existUser($_SESSION['user'],$passwd0))
					$facade->editPass($id,$passwd1);
				else{
					array_push($errorList, array('errorCode' => '1', 'message' => "La contraseña es incorrecta."));
                }
			}

		}else{
			array_push($errorList, array('errorCode' => '-1', 'message' => "No existe sesión de usuario"));
        }
        
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
		if(isset($_POST["titulo"]) && isset($_POST["localizacion"])){
			
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
        
        if (isset($_POST["categoria"]) && is_numeric($_POST["categoria"])){
            $idCategoria = intval($_POST["categoria"]);
            if($idCategoria == -1){
                $idCategoria = "";
            }
        }

        if (isset($_POST["priceMin"]) && is_numeric($_POST["priceMin"])){
            $priceMin = intval($_POST["priceMin"]);
        }

        if (isset($_POST["priceMax"]) && is_numeric($_POST["priceMax"])){
            $priceMax = intval($_POST["priceMax"]);
        }        

        if (isset($_POST["titulo"])){
            $titulo = filter_var($_POST["titulo"], FILTER_SANITIZE_STRING);
        }    

        if (isset($_POST["localizacion"])){
            $localizacion = filter_var($_POST["localizacion"], FILTER_SANITIZE_STRING);
        }        
        
        $condiciones = array("precioMin" => $priceMin, "precioMax" => $priceMax, 
        "titulo" => $titulo, "localizacion" => $localizacion, "categoria" => $idCategoria);
        
        $columnNameOrder = "";
        $order = "";
        
		$result = $facade->getAnuncios($condiciones, $columnNameOrder, $order);
        
        //Inicializacion de los datos
        $data = array();
 
		if($result){
			if(mysqli_num_rows($result) > 0) {
				
				while($row = mysqli_fetch_array($result)) {
					$temp_data = array('id'=>$row['idAnuncio'], 'titulo'=>$row['titulo'], 
                    'localizacion'=>$row['localizacion'], 'precio'=>$row['precio'], 'fecha'=>$row['fecha'], 'miniatura'=>$row['small']);
                    
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
        return array($data, $errorList);

	}
    
    function chargeAnuncio(){
        //Array para guardar los posibles errores que encontremos
        $errorList = array(); //Array de arrays: error("errorCode" => "code", "message" => "mensaje" )
        
        //Inicializamos en array de datos [info, img, comentarios, isLoguedIn, isFavorito, itsMine] 
        $data = array(array(), array(), array(), false, false, false); 
        
        //Establecer la conexion a la BBDD
		$con    = new Connection();			
		$facade = new Facade($con);
        
        if (isset($_POST["idAnuncio"]) && is_numeric($_POST["idAnuncio"])){
            $idAnuncio = intval($_POST["idAnuncio"]);
            
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
                                , 'idPadre'=>$row['idPadre'], 'fecha'=>$row['fecha']); 
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
      echo "retorno -1";
      return -1;
    }
  }
?>