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

			$con = new Connection();
			$facade = new Facade($con);

			if($facade->existNameUser($user)){

				$con->close();
				array_push($errorList, array('errorCode' => '1', 'message' => "Lo sentimos, el nombre de usuario ya existe."));

			}else{

				$data = array("user"=>$user, "name"=>$name, "email"=>$email, "pwd"=>$pwd);				
				
				if($facade->insertUser($data)){
					
					$data = $facade->getIdUser($user);
					
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
			
			$user = filter_var($_POST["name"],FILTER_SANITIZE_STRING);
			$pwd  = filter_var($_POST["passwd0"],FILTER_SANITIZE_STRING);
			
            //Establecer la conexion a la BBDD
			$con    = new Connection();			
			$facade = new Facade($con);

			if($facade->existUser($user,$pwd)){

				$data = $facade->getIdUser($user);
				setVarSession("idUser",$data["idUser"]);
				setVarSession("user",$data["user"]);
				$con->close();
				return '0';

			}else {
				$con->close();
				array_push($errorList, array('errorCode' => '1', 'message' => "El usuario no existe, vuelva a intentarlo."));
			}				

		}else {
			$con->close();
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

	function modifyData(){}

	function logout(){
		session_unset();
		session_destroy();
	}

	function changePassword(){}
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

        //Obtener los datos del usuario que crea el anuncio
        if(isset($_SESSION["idUser"])){
            $idUser = $_SESSION["idUser"];
        }else{
            return '1';
        }

        //Obtener los datos del anuncio
		if(isset($_POST["titulo"]) && isset($_POST["localizacion"])){
			
			$titulo = filter_var($_POST["titulo"], FILTER_SANITIZE_STRING);
			$localizacion  = filter_var($_POST["localizacion"], FILTER_SANITIZE_STRING);
            
            $idCategoria = 0;
            if (isset($_POST["categoria"]) && is_numeric($_POST["categoria"])){
                $idCategoria = intval($_POST["categoria"]);
            }else{
                return '3';
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

            //Conectar con la base de datos
			$con    = new Connection();			
			$facade = new Facade($con);

            $data = array("idUser"=>$idUser, "idCategoria"=>$idCategoria, "precio"=>$precio, "titulo"=>$titulo, 
                    "descripcion"=>$descripcion, "localizacion"=>$localizacion, "telefono"=>$telefono);				
				
			if(!$facade->addAnuncio($data)){
				$con->close();
                return '2';
            }

		}else {
			return '3';
		}
        
        //Obtener y procesar las imagenes
        $idAnuncio = $facade->getLastId();
        if(!$idAnuncio || $idAnuncio == 0){
            return '2';
        }
        
        $upload = new Upload();
        $ret = $upload->processUploads($idAnuncio);
        $ret_err = $ret['error'];
        if ($ret_err != '0'){
            $con->close();
            return $ret;
        }
        
        //ChromePhp::log($ret['info']);
        $ret_inf = $ret['info'];
        for($i=0; $i<count($ret_inf); $i++){
            
            $data = array("idAnuncio"=>$idAnuncio, "idImagen"=>$ret_inf[$i][0], 
            "big"=>$ret_inf[$i][3], "medium"=>$ret_inf[$i][2], "small"=>$ret_inf[$i][1]);
            
            if(!$facade->addImage($data)){
				$con->close();
                return '2';
            }
            
        }
        
        $con->close();
        return '0';
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
				array_push($errorList, array('errorCode' => '2', 'message' => "No se ha obtenido ningún dato"));
		}else{
			array_push($errorList, array('errorCode' => '1', 'message' => "Error al consultar la BBDD"));
        }
        
        //Devolvemos los datos y los reportes de errores (si hay)
        return array($data, $errorList);

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
?>