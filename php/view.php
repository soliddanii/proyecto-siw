<?php
	
	/////////////////////////////////////////////////////////////////////////
	// 				Funciones comunes para varias paginas
	/////////////////////////////////////////////////////////////////////////

	/*
	*	Carga el menu, y tiene en cuenta si el usuario esta logeado o no.
	*	Devuelve un string con todos los datos cargados
	*/
	function chargeMenu($text){
		
		$pathMenu = "../html/menu.html";
		$textMenu = file_get_contents($pathMenu) or exit("Error frontView, [$pathMenu]");
		$text     = str_replace("##menu##",$textMenu,$text);

		// Comprueba si el usuario se ha logueado
		if(isset($_SESSION["user"])){

			$trozos0 = explode("##nologin##", $text);
			$trozos1 = explode("##login##", $trozos0[2]);

			$trozos1[1] = str_replace("##nameUser##", $_SESSION["user"], $trozos1[1]);

			return $trozos0[0].$trozos1[1].$trozos1[2];

		}else{

			$trozos0 = explode("##nologin##", $text);
			$trozos1 = explode("##login##", $trozos0[2]);

			return $trozos0[0].$trozos0[1].$trozos1[2];
		}

	}

	/*
	*	Funcion para administrar errores
	*
	*/
	function error($page,$errorMessage){

		if(strlen($errorMessage) > 0)
			$page = str_replace("##error##", $errorMessage, $page);
		else
			$page = str_replace("##error##", "", $page);
		return $page;
	}

	/*
	*	Muestra una pagina distinta cuando ocurre un error inesperado
	*/
	function errorView($errorMessage){
		echo "Ha ocurrido un error en [".$errorMessage."]";
	}

	/////////////////////////////////////////////////////////////////////////
	// 			Funciones para cargar paginas de la web
	/////////////////////////////////////////////////////////////////////////

	/*
	*	Carga la pagina principal de la web
	*	Lanza un error si no se puede obtner la direccion del html
	*/
	function frontView($categories, $errorMessage) {

		$pathFront = "../html/front.html";				
		$text = file_get_contents($pathFront) or exit("Error frontView, [$pathFront]");
		$trozos = explode("##corteCategorias##", $text);
		$aux0 = "";
		foreach ($categories as $key => $value) {
            $aux1 = $trozos[1];
            $aux1 = str_replace("##idCategoria##", $value, $aux1);
            $aux1 = str_replace("##categoria##", $value, $aux1);
            $aux0 .= $aux1;
        }
		$text = $trozos[0].$aux0.$trozos[2];
		$text = error($text,$errorMessage);
		echo chargeMenu($text);
		
	}

	/*
	*	Carga la pagina para realizar el login de usuarios
	*	Lanza un error si no se puede obtner la direccion del html
	*/
	function loginView($errorMessage) {

		$pathFront = "../html/login.html";
		$text = file_get_contents($pathFront) or exit("Error signupView, [$pathFront]");
		$text = error($text,$errorMessage);
		echo chargeMenu($text);

	}

	/*
	*	Carga la pagina para realizar el registro de usuarios
	*	Lanza un error si no se puede obtner la direccion del html
	*/
	function signUpView($errorMessage) {

		$pathFront = "../html/signup.html";
		$text = file_get_contents($pathFront) or exit("Error signupView, [$pathFront]");
		$text = error($text,$errorMessage);
		echo chargeMenu($text);

	}
    
    /*
    *   Carga la pagina para ver la lista de articulos / anuncios
    *   Lanza error si no se puede obtener la direccion del html
    */
    function browserView($categories) {
        
        $pathFront = "../html/browser.html";
        $text = file_get_contents($pathFront) or exit("Error browserView, [$pathFront]");
        $trozos = explode("##corteCategorias##", $text);
		$aux0 = "";
        foreach ($categories as $key => $value) {
            $aux1 = $trozos[1];
            $aux1 = str_replace("##idCategoria##", $key, $aux1);
            $aux1 = str_replace("##categoria##", $value, $aux1);
            $aux0 .= $aux1;
        }

		$text = $trozos[0].$aux0.$trozos[2];
        echo chargeMenu($text);
        
    }

    /*
    *   Carga la pagina para publicar un anuncio nuevo
    *   Lanza error si no se puede obtener la direccion del html
    */
    function newAnuncioView($categories) {
        
        $pathFront = "../html/newanuncio.html";
        $text = file_get_contents($pathFront) or exit("Error newAnuncioView, [$pathFront]");
        $trozos = explode("##corteCategorias##", $text);
		$aux0 = "";
        foreach ($categories as $key => $value) {
            $aux1 = $trozos[1];
            $aux1 = str_replace("##idCategoria##", $key, $aux1);
            $aux1 = str_replace("##categoria##", $value, $aux1);
            $aux0 .= $aux1;
        }

		$text = $trozos[0].$aux0.$trozos[2];
        echo chargeMenu($text);
        
    }
    
	/*
	*	Carga la pagina para recuperar la contraseña de un usuario
	*	Recibe como parametro la contraseña que va a mostrar y un mensaje, que
	*	puede ser un mensaje de error. 
	*	Ambos parametros pueden ser vacios
	*/
	function recoverPassView($password,$errorMessage){

		$pathFront = "../html/recoverpass.html";
		$text = file_get_contents($pathFront) or exit("Error recoverPassView, [$pathFront]");
		$text = error($text,$errorMessage);
		$text = str_replace("##password##", $password, $text);
		echo chargeMenu($text);
	}

?>