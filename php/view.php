<?php
	
    //temp for debug
    require_once '../chromephp/ChromePhp.php';
    
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
    * NUEVA FUNCION PARA ADMINISTRAR ERRORES MULTIPLES
    * Estructura de cada error: ('errorCode' => 'code', 'message' => 'mensaje')
    */
    function processErrors($text, $errors){

        if (strpos($text, '##corteListaErrores##') !== false) {
            $trozos = explode('##corteListaErrores##', $text);
        
            if(is_null($errors) || empty($errors)){
                $text = $trozos[0].$trozos[2];
            }else{
                $aux0 = "";
                for($i=0; $i<count($errors); $i++) {
                    $aux1 = $trozos[1];
                    $aux1 = str_replace("##errorCode##", $errors[$i]['errorCode'], $aux1);
                    $aux1 = str_replace("##error##", $errors[$i]['message'], $aux1);
                    $aux0 .= $aux1;
                }
                $text = $trozos[0].$aux0.$trozos[2];
            }
        }
        
        return $text;
	}

	/*
	*	Muestra una pagina distinta cuando ocurre un error inesperado
	*/
	function errorView($errorMessage){
		echo "Ha ocurrido un error en [".$errorMessage."]";
	}
    
    /*
	*	Muestra una pagina distinta cuando ocurre un error inesperado
	*/
	function errorView2($errores){
		$errorView = "../html/errorView.html";		
        $text = file_get_contents($errorView) or exit("Error errorView, [$errorView]");
        $text = processErrors($text, $errores);
        echo chargeMenu($text);
	}
            

	/////////////////////////////////////////////////////////////////////////
	// 			Funciones para cargar paginas de la web
	/////////////////////////////////////////////////////////////////////////

	/*
	*	Carga la pagina principal de la web
	*	Lanza un error si no se puede obtner la direccion del html
	*/
	function frontView($categories, $errores) {

		$pathFront = "../html/front.html";				
		$text = file_get_contents($pathFront) or exit("Error frontView, [$pathFront]");
        $text = processErrors($text, $errores);
        
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
	*	Carga la pagina para realizar el login de usuarios
	*	Lanza un error si no se puede obtner la direccion del html
	*/
	function loginView($errors) {

		$pathFront = "../html/login.html";
		$text = file_get_contents($pathFront) or exit("Error signupView, [$pathFront]");
		$text = processErrors($text, $errors);
		echo chargeMenu($text);

	}

	/*
	*	Carga la pagina para realizar el registro de usuarios
	*	Lanza un error si no se puede obtner la direccion del html
	*/
	function signUpView($errors) {

		$pathFront = "../html/signup.html";
		$text = file_get_contents($pathFront) or exit("Error signupView, [$pathFront]");
		$text = processErrors($text, $errors);
		echo chargeMenu($text);

	}
    
	function editProfileView($errores){
		$pathEdit = "../html/editprofile.html";
		$text = file_get_contents($pathEdit) or exit("Error editView, [$pathEdit]");
		$text = processErrors($text, $errores);
		echo chargeMenu($text);
	}
    
    /*
    *   Carga la pagina para ver la lista de articulos / anuncios
    *   Lanza error si no se puede obtener la direccion del html
    */
    function browserView($categories, $anuncios, $errores) {
        
        $pathFront = "../html/browser.html";
        $text = file_get_contents($pathFront) or exit("Error browserView, [$pathFront]");
        $text = processErrors($text, $errores);
        
        //Carcar las categorias en el select
        $trozos = explode("##corteCategorias##", $text);
		$aux0 = "";
        foreach ($categories as $key => $value) {
            $aux1 = $trozos[1];
            $aux1 = str_replace("##idCategoria##", $key, $aux1);
            $aux1 = str_replace("##categoria##", $value, $aux1);
            $aux0 .= $aux1;
        }
		$text = $trozos[0].$aux0.$trozos[2];
        
        //Cargar los anuncios en la lista
        $trozos = explode("##corteListaArticulos##", $text);
        $aux0 = "";
        for($i = 0; $i<count($anuncios); $i++){
            $aux1 = $trozos[1];
            $aux1 = str_replace("##idAnuncio##", $anuncios[$i]['id'], $aux1);
            $aux1 = str_replace("##miniaturaAnuncio##", $anuncios[$i]['miniatura'], $aux1);
            $aux1 = str_replace("##tituloAnuncio##", $anuncios[$i]['titulo'], $aux1);
            $aux1 = str_replace("##localizacionAnuncio##", $anuncios[$i]['localizacion'], $aux1);
            $aux1 = str_replace("##fechaAnuncio##", $anuncios[$i]['fecha'], $aux1);
            if($anuncios[$i]['precio'] != '0.00'){ 
                $aux1 = str_replace("##precioAnuncio##", $anuncios[$i]['precio'].' €', $aux1); 
            }else{ 
                $aux1 = str_replace("##precioAnuncio##", 'GRATIS', $aux1); 
            }
            $aux0 .= $aux1;
        }
        $text = $trozos[0].$aux0.$trozos[2];
        
        echo chargeMenu($text);
        
    }

    /*
    *   Carga la pagina para publicar un anuncio nuevo
    *   Lanza error si no se puede obtener la direccion del html
    */
    function newAnuncioView($categories, $errores) {
        
        $pathFront = "../html/newanuncio.html";
        $text = file_get_contents($pathFront) or exit("Error newAnuncioView, [$pathFront]");
        $text = processErrors($text, $errores);
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
    
    /*
	*	Carga la pagina principal de la web
	*	Lanza un error si no se puede obtner la direccion del html
	*/
	function anuncioView($data, $errores) {
    
		$pathAnuncio = "../html/anuncio.html";		
		$text = file_get_contents($pathAnuncio) or exit("Error anuncioView, [$pathAnuncio]");
        $text = processErrors($text, $errores);
        
		// PROCESAR INFORMACION 
        $text = str_replace("##titulo##", $data[0]['titulo'], $text); 
        $text = str_replace("##localizacion##", $data[0]['localizacion'], $text);             
        $text = str_replace("##fecha##", $data[0]['fecha'], $text); 
        if($data[0]['precio'] != '0.00'){ 
            $text = str_replace("##precio##", $data[0]['precio'].' €', $text); 
        }else{ 
            $text = str_replace("##precio##", 'GRATIS', $text); 
        } 
        if($data[0]['nameSeller'] != ''){
            $text = str_replace("##nombre##", 'Nombre: '.$data[0]['nameSeller'].' ('.$data[0]['nickSeller'].')', $text);
        }else{
            $text = str_replace("##nombre##", 'Nick: '.$data[0]['nickSeller'], $text);
        }
        if($data[0]['telefono'] != ''){
            $text = str_replace("##telefono##", $data[0]['telefono'], $text);
        }else{
            $text = str_replace("##telefono##", 'NO', $text);
        }
        if($data[0]['descripcion'] != ''){
            $text = str_replace("##descripcion##", nl2br($data[0]['descripcion']), $text);
        }else{
            $text = str_replace("##descripcion##", 'No Disponible', $text);
        }

        
        // PROCESAR IMAGENES 
        //Asignar las ministuras
        $trozos = explode("##siHayMiniaturas##", $text); 
        if(!empty($data[1])){ 
            $subtrozos = explode("##corteMiniatura##", $trozos[1]); 
            $aux0 = ""; 
            for ($i=0; $i<count($data[1]); $i++) { 
                $aux1 = $subtrozos[1];      
                $aux1 = str_replace("##urlMiniatura##", $data[1][$i]['small'], $aux1); 
                $aux1 = str_replace("##urlMediana##", $data[1][$i]['medium'], $aux1); 
                $aux1 = str_replace("##urlGrande##", $data[1][$i]['big'], $aux1); 
                $aux1 = str_replace("##idAnuncio##", $data[1][$i]['id'], $aux1); 
                $aux0 .= $aux1; 
            } 
            $trozos[1] = $subtrozos[0].$aux0.$subtrozos[2]; 
             
            $text = $trozos[0].$trozos[1].$trozos[2]; 
        }else{ 
            $text = $trozos[0].$trozos[2];   
            $text = str_replace("##primeraMediana##", '../images/default-product.png', $text); 
        } 
         
        //Asignar la primera mediana, la primera grande, y el numero total de imagenes
        if(empty($data[1])){ 
            $text = str_replace("##primeraMediana##", '../images/default-product.png', $text); 
            $text = str_replace("##primeraGrande##", '../images/default-product.png', $text); 
            $text = str_replace("##maxImg##", '1', $text); 
        }else{
            $text = str_replace("##primeraMediana##", $data[1][0]['medium'], $text); 
            $text = str_replace("##primeraGrande##", $data[1][0]['big'], $text); 
            $text = str_replace("##maxImg##", count($data[1]), $text); 
        }
        
        
        
        // PROCESAR COMENTARIOS 
        if(!empty($data[2])){ 
            $trozos = explode("##bloqueComentario##", $text); 
            $aux0 = ""; 
            for ($i=0; $i<count($data[2]); $i++) { 
                $aux1 = $trozos[1];      
                $aux1 = str_replace("##idComentario##", $data[2][$i]['id'], $aux1); 
                $aux1 = str_replace("##nickI##", $data[2][$i]['nickAutor'], $aux1); 
                $aux1 = str_replace("##comentarioItem##", $data[2][$i]['comentario'], $aux1); 
                $aux1 = str_replace("##idPadre##", $data[2][$i]['idPadre'], $aux1); 
                $aux1 = str_replace("##fechaComentario##", $data[2][$i]['fecha'], $aux1); 
                if($data[2][$i]['nickPadre'] != ''){
                    $aux1 = str_replace("##nickAutor##", '#'.$data[2][$i]['id'].' <b>'.$data[2][$i]['nickAutor'].'</b> en respuesta a '
                    .$data[2][$i]['nickPadre'].' <a href="#coment'.$data[2][$i]['idPadre'].'">(#'.$data[2][$i]['idPadre'].')</a>', $aux1); 
                }else{
                    $aux1 = str_replace("##nickAutor##", '#'.$data[2][$i]['id'].' <b>'.$data[2][$i]['nickAutor'].'</b>', $aux1);
                }
                
                
                //Añadir el boton de borrar
                $subTrozos = explode("##borrarComentario0##", $aux1); 
                if($data[5]){
                    $aux1 = $subTrozos[0].$subTrozos[1].$subTrozos[2];
                }else{
                    $aux1 = $subTrozos[0].$subTrozos[2];
                }
                
                $aux0 .= $aux1; 
        
            } 
            $text = $trozos[0].$aux0.$trozos[2];
        
        
        }else{
            $trozos = explode("##bloqueComentario##", $text); 
            $text = $trozos[0].$trozos[2];
        }
		
        
        //Procesar favoritos y compra
        $text = str_replace("##idAnuncio##", $data[0]['id'], $text); 
        if($data[3]){
            //Logueado
            $trozos = explode("##nologin2##", $text);
            $text = $trozos[0].$trozos[2];
            
            $trozos = explode("##login2##", $text);
            
            //PROCESAR FAVORITO
            if($data[4]){
                //El anuncio esta en mis favoritos
                $trozos[1] = str_replace("##mensajeBotonFav##", 'Eliminar de<br/>Favoritos', $trozos[1]); 
                $trozos[1] = str_replace("##isFavorite##", 'isFavorite', $trozos[1]); 
            }else{
                //El anuncio no es mi favorito
                $trozos[1] = str_replace("##mensajeBotonFav##", 'Añadir a<br/>Favoritos', $trozos[1]); 
                $trozos[1] = str_replace("##isFavorite##", 'isNotFavorite', $trozos[1]); 
            }
            
            //PROCESAR COMPRA
            if($data[5]){
                //El anuncio es mio
                $trozos[1] = str_replace("##canBuy##", 'cancelAnuncio', $trozos[1]); 
                $trozos[1] = str_replace("##mensajeBotonComprar##", 'Cancelar<br/>Anuncio', $trozos[1]); 
            }else{
                //El anuncio no es mio
                $trozos[1] = str_replace("##canBuy##", 'buyAnuncio', $trozos[1]); 
                $trozos[1] = str_replace("##mensajeBotonComprar##", 'Adquirir<br/>Artículo', $trozos[1]); 
            }
            
            $text = $trozos[0].$trozos[1].$trozos[2];
            
        }else{
            //No logueado
            $trozos = explode("##login2##", $text);
            $text = $trozos[0].$trozos[2];
            $trozos = explode("##nologin2##", $text);
            $text = $trozos[0].$trozos[1].$trozos[2];
        }
        
        
        
		echo chargeMenu($text);
		
	}
    
    
?>