<?php
	
    //temp for debug
    //include '../chromephp/ChromePhp.php';
    
	// Clases que vamos a utilizar
	//require_once 'class/config.php'; // configuracion para la BBDD
	require_once 'class/users.php'; // manejo de los usuarios
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
	*		 1 : usuario ya existe
	*		 0 : usuario registrado correctamente	
	*   	-1 : error al registrar o no se encontraron variables POST
	*/
	function signUp(){
    
		if(isset($_POST["nameuser"]) && isset($_POST["name"]) && isset($_POST["email"]) 
			&& isset($_POST["passwd0"]) && isset($_POST["passwd1"])){
            
			$user  = filter_var($_POST["nameuser"],FILTER_SANITIZE_STRING);
			$name  = filter_var($_POST["name"],FILTER_SANITIZE_STRING);
			$email = filter_var($_POST["email"],FILTER_SANITIZE_STRING);
			$pwd  = filter_var($_POST["passwd0"],FILTER_SANITIZE_STRING);

			$con = new Connection();
			$facade = new Facade($con);

			if($facade->existNameUser($user)){

				$con->close();
				return "1";

			}else{

				$data = array("user"=>$user, "name"=>$name, "email"=>$email, "pwd"=>$pwd);				
				
				if($facade->insertUser($data)){
					
					$data = $facade->getIdUser($user);
					
					setVarSession("idUser",$data["idUser"]);
					setVarSession("user",$data["user"]);					
					
					$con->close();

					return "0"; 								

				}else{

					$con->close();
					return "-1";

				}				
			}
		}else {
			return "-1";
		}		

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

		if(isset($_POST["name"]) && isset($_POST["passwd0"])){
			
			$user = filter_var($_POST["name"],FILTER_SANITIZE_STRING);
			$pwd  = filter_var($_POST["passwd0"],FILTER_SANITIZE_STRING);
			
			$con    = new Connection();			
			$facade = new Facade($con);

			if($facade->existUser($user,$pwd)){

				$data = $facade->getIdUser($user);
				setVarSession("idUser",$data["idUser"]);
				setVarSession("user",$data["user"]);
				$con->close();
				return "0";

			}else {

				$con->close();
				return "1";

			}				

		}else {
			$con->close();
			return "-1";
		}

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
            return '3';
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
        if ($ret !== '0'){
            return $ret;
        }
        
        $con->close();
        return '0';
	}
    
	/////////////////////////////////////////////////////////////////////////
	// 							Gestión de Categorias 
	/////////////////////////////////////////////////////////////////////////
	function chargeCategories(){

		$con    = new Connection();			
		$facade = new Facade($con);

		$result = $facade->getCategories();

		if($result){
			if(mysqli_num_rows($result) > 0) {
				$data = array();
				while($row = mysqli_fetch_array($result)) {
					$data[$row['idCategoria']] = $row['categoria'];
				}
				return $data;

			}else
				echo "Error al realizar consulta";
		}else
			echo "Error con acceso a bbdd";

	}
?>