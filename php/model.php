<?php
	
	// Clases que vamos a utilizar
	//require_once 'class/config.php'; // configuracion para la BBDD
	require_once 'class/users.php'; // manejo de los usuarios
	require_once 'class/connection.php';
	require_once 'class/facade.php';
	require_once 'class/sessions.php';

	/////////////////////////////////////////////////////////////////////////
	// 							Gestión de Sesion
	/////////////////////////////////////////////////////////////////////////
	function initSession(){
		
		$objSe = new Session();
		$objSe->init();
		
	}
	/////////////////////////////////////////////////////////////////////////
	/////////////////////////////////////////////////////////////////////////

	/////////////////////////////////////////////////////////////////////////
	// 							Gestión de Usuarios
	/////////////////////////////////////////////////////////////////////////

	/*
	*	Return:
	*		-1 : usuario ya existe
	*		 0 : usuario registrado correctamente	
	*    1 : error al registrar usuario
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

				//El nombre de usuario ya existe
				$con->close();
				return "-1";

			}else{

				$data = array("user"=>$user, "name"=>$name, "email"=>$email, "pwd"=>$pwd);				
				
				if($facade->insertUser($data)){
					
					$data = $facade->getIdUser($user);					
					$_SESSION["idUser"] = $data["idUser"];
					$_SESSION["user"] = $data["user"];
					
					$con->close();
					return "0"; 								

				}else{

					$con->close();
					return "1";

				}				
			}
		}		

	}

	function loginUser(){

		if(isset($_POST["name"]) && isset($_POST["passwd0"])){
			
			$user = filter_var($_POST["name"],FILTER_SANITIZE_STRING);
			$pwd  = filter_var($_POST["passwd0"],FILTER_SANITIZE_STRING);
			
			$con = new Connection();
			
			$facade = new Facade($con);

			if($facade->existUser($user,$pwd))
				echo "existe";
			else
				echo "no existe";

			$con->close();



		}else {
			echo "Error al ler las variables en loginUser()";
		}

	}

	function modifyData(){}

	function logout(){

		session_unset();
		session_destroy();

	}

	function change_password(){}
	/////////////////////////////////////////////////////////////////////////
	/////////////////////////////////////////////////////////////////////////

	/////////////////////////////////////////////////////////////////////////
	// 							Gestión de 
	/////////////////////////////////////////////////////////////////////////

?>