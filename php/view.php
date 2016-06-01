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
		if(isset($_SESSION["user"]) && isset($_SESSION["idUser"])){

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
    
    /*
    *   Carga la pagina para editar el perfil
    */
	function editProfileView($errores){
		$pathEdit = "../html/editprofile.html";
		$text = file_get_contents($pathEdit) or exit("Error editView, [$pathEdit]");
		$text = processErrors($text, $errores);
		echo chargeMenu($text);
	}
    
    /*
    *   Carga la pagina de simulacion de compra
    */
	function compraView(){
		$pathCompra = "../html/compraCompleta.html";
		$text = file_get_contents($pathCompra) or exit("Error editView, [$pathCompra]");
		echo chargeMenu($text);
	}
    
    /*
    *   Carga la pagina para ver la lista de articulos / anuncios
    *   Lanza error si no se puede obtener la direccion del html
    */
    function browserView($categories, $anuncios, $errores, $misAnuncios, $misFavoritos, $page) {
        
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
            $aux1 = str_replace("##tituloAnuncio##", $anuncios[$i]['titulo'], $aux1);
            $aux1 = str_replace("##miniaturaAnuncio##", $anuncios[$i]['miniatura'], $aux1);
            $aux1 = str_replace("##localizacionAnuncio##", $anuncios[$i]['localizacion'], $aux1);
            $aux1 = str_replace("##fechaAnuncio##", $anuncios[$i]['fecha'], $aux1);  
            if($anuncios[$i]['esMio'] == true){
                $aux1 = str_replace("##esMiAnuncio##", " esMiAnuncio", $aux1);
            }else{
                $aux1 = str_replace("##esMiAnuncio##", "", $aux1);
            }
            if($anuncios[$i]['estado'] == 0){
                $aux1 = str_replace("##precioAnuncio##", 'CANCELADO', $aux1); 
                $aux1 = str_replace("##estadoAnuncio##", " cancelado", $aux1);
            }elseif($anuncios[$i]['estado'] == 2){
                $aux1 = str_replace("##precioAnuncio##", 'VENDIDO', $aux1); 
                $aux1 = str_replace("##estadoAnuncio##", " terminado", $aux1);
            }elseif($anuncios[$i]['estado'] == 1){
                if($anuncios[$i]['precio'] != '0.00'){ 
                    $aux1 = str_replace("##precioAnuncio##", $anuncios[$i]['precio'].' €', $aux1); 
                }else{ 
                    $aux1 = str_replace("##precioAnuncio##", 'GRATIS', $aux1); 
                }
                $aux1 = str_replace("##estadoAnuncio##", "", $aux1);
            }
            $aux0 .= $aux1;
        }
        $text = $trozos[0].$aux0.$trozos[2];
        
        //Establecer si es la pagina de favoritos o la de mis anuncios a AJAX
        $text = str_replace("##misAnuncios##", $misAnuncios, $text);
        $text = str_replace("##misFavoritos##", $misFavoritos, $text);
        
        //Establecer la #pagina
        $text = str_replace("##numPagina##", $page, $text);
        
        echo chargeMenu($text);
        
    }
    
    /*
    *   Codifica los datos de los anuncios en JSON
    *   y despues los envia
    */
    function browserViewJSON($anuncios, $errores, $page) {
        
        $data = array('data' => $anuncios, 'errors' => $errores, 'page' => $page);
        header('Content-type: application/json; charset=utf-8');
        echo json_encode($data, JSON_FORCE_OBJECT);
        
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
	*/
	function recoverPassView($errores){
		$pathRecover = "../html/recoverpass.html";
		$text = file_get_contents($pathRecover) or exit("Error recoverPassView, [$pathRecover]");
		$text = processErrors($text, $errores);
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
                if($data[0]['estado'] == 0){
                    $trozos[1] = str_replace("##mensajeBotonComprar##", 'Mi Anuncio<br/>Cancelado', $trozos[1]); 
                }elseif($data[0]['estado'] == 2){
                    $trozos[1] = str_replace("##mensajeBotonComprar##", 'Mi Anuncio<br/>Vendido', $trozos[1]); 
                }elseif($data[0]['estado'] == 1){
                    $trozos[1] = str_replace("##mensajeBotonComprar##", 'Cancelar<br/>Anuncio', $trozos[1]); 
                }

            }else{
                //El anuncio no es mio
                $trozos[1] = str_replace("##canBuy##", 'buyAnuncio', $trozos[1]);
                if($data[0]['estado'] == 0){
                    $trozos[1] = str_replace("##mensajeBotonComprar##", 'Anuncio<br/>Cancelado', $trozos[1]); 
                }elseif($data[0]['estado'] == 2){
                    $trozos[1] = str_replace("##mensajeBotonComprar##", 'Anuncio<br/>Vendido', $trozos[1]); 
                }elseif($data[0]['estado'] == 1){
                    $trozos[1] = str_replace("##mensajeBotonComprar##", 'Adquirir<br/>Artículo', $trozos[1]); 
                }                   
            }
            
            $trozos[1] = str_replace("##estado##", $data[0]['estado'], $trozos[1]);
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
    
  /////////////////////////////////////////////////////////////////////////
  //              Funciones para el admin
  /////////////////////////////////////////////////////////////////////////

  function loginViewAdmin(){

    $pathFront = "../html/loginadmin.html";             
    $text = file_get_contents($pathFront) or exit("Error loginViewAdmin, [$pathFront]");        
    
    $text = str_replace("##error##", "", $text);
    
    echo $text;
  }

  function frontViewAdmin(){

    $pathFront = "../html/frontadmin.html";             
    $text = file_get_contents($pathFront) or exit("Error frontViewAdmin, [$pathFront]");
    $text = str_replace("##user##", $_SESSION["user"], $text);
    $text = str_replace("##result##", "", $text);
    
    echo $text;

  }

  function consultUsersView($data,$numUsers,$sizeElements,$order){

    $pathManageUsers = "../html/gestionusuarios.html";
    $textManageUsers = file_get_contents($pathManageUsers) or exit("Error gestionusuarios, [$pathManageUsers]");

    $trozos0 = explode("##manageusers##", $textManageUsers);
    $trozos1 = explode("##list##", $trozos0[1]);
    $trozos2 = explode("##paginacion##", $trozos1[0]);

    if ($order){          

      $user = explode("##datauser##", $trozos1[1]);

      $result = $user[0];
      $aux = "";

      foreach ($data as $key => $value) {
        $aux = $user[1];
        $aux = str_replace("##idUser##", $value["idUser"], $aux);
        $aux = str_replace("##nick##", $value["nick"], $aux);
        $aux = str_replace("##name##", $value["name"], $aux);
        $aux = str_replace("##email##", $value["email"], $aux);
        $result .= $aux;
      }

      echo $result.$user[2];

    }else{

      $pathFront = "../html/frontadmin.html";
      $text = file_get_contents($pathFront) or exit("Error frontViewAdmin, [$pathFront]");      

      $text = str_replace("##user##", $_SESSION["user"], $text);

      if(empty($data) && empty($order)){
        
        $text = str_replace("##result##", $trozos2[0].$trozos2[2], $text);        

      }else{

        $trozos3 = explode("##page##", $trozos2[1]);
        $trozos3[2] = str_replace("##currentPage##", 1, $trozos3[2]);
        
        $pagination = getPagination($trozos3[1], $numUsers, $sizeElements);
        $trozos3[2] = str_replace("##maxPage##", $pagination["nPag"], $trozos3[2]);
        
        $user = explode("##datauser##", $trozos1[1]);

        $result = $trozos2[0].$trozos3[0].$pagination["text"].$trozos3[2].$user[0];
        $aux = "";

        foreach ($data as $key => $value) {
          $aux = $user[1];
          $aux = str_replace("##idUser##", $value["idUser"], $aux);
          $aux = str_replace("##nick##", $value["nick"], $aux);
          $aux = str_replace("##name##", $value["name"], $aux);
          $aux = str_replace("##email##", $value["email"], $aux);
          $result .= $aux;
        }

        $text = str_replace("##result##", $result.$user[2], $text);      
      }

      echo $text;  

    }  

  }

  function getPagination($text, $amount, $limitElements){
    
    if (($amount % $limitElements) == 0)
      $nPag = $amount/$limitElements;
    else
      $nPag = ceil($amount/$limitElements);
    
    $ret = "";    

    for ($i=0; $i < $nPag; $i++) { 
      $aux = $text;
      $aux = str_replace("##num##", $i+1, $aux);
      $ret .= $aux;          
    }

    return array('text' => $ret, 'nPag' => $nPag);

  }

  function modifyUserViewAdmin($idUser){

    $pathFront = "../html/frontadmin.html";
    $text = file_get_contents($pathFront) or exit("Error frontViewAdmin, [$pathFront]");

    $text = str_replace("##user##", $_SESSION["user"], $text);

    $pathModify = "../html/gestionusuarios.html";
    $textModify = file_get_contents($pathModify) or exit("Error modifyUserViewAdmin, [$pathModify]");

    $trozos = explode("##modify##", $textModify);
    $text = str_replace("##result##", $trozos[1], $text);
    $text = str_replace("##idUser##", $idUser, $text);   

    echo $text;
  }
    
  function infoUserViewAdmin($data){

    $pathFront = "../html/frontadmin.html";
    $text0 = file_get_contents($pathFront) or exit("Error frontViewAdmin, [$pathFront]");

    $text0 = str_replace("##user##", $_SESSION["user"], $text0);

    $pathInfo = "../html/gestionusuarios.html";
    $text = file_get_contents($pathInfo) or exit("Error infoUserViewAdmin, [$pathInfo]");

    $trozos0 = explode("##info##", $text);
    $trozos1 = explode("##blocks##", $text);
    $trozos2 = explode("##block##", $trozos1[1]);

    $info0 = $trozos0[1];
    $info1 = "";
    $info2 = "";
    $info3 = "";

    foreach ($data as $k => $r) {      

      if ($k == "info0"){
        $row = mysqli_fetch_array($r);        
        $info0 = str_replace("##nick##", $row["nick"], $info0);
        $info0 = str_replace("##name##", $row["name"], $info0);
        $info0 = str_replace("##email##", $row["email"], $info0);
      }else{        
        $aux0 = "";
        $aux1 = "";
        $aux2 = "";
                
        while ($row = mysqli_fetch_array($r)){                    

          $aux1 = $trozos2[1];
          $aux1 = str_replace("##idAnuncio##", $row["idAnuncio"], $aux1);
          $aux1 = str_replace("##title##", $row["titulo"], $aux1);
          $aux1 = str_replace("##date##", $row["fecha"], $aux1);
          $aux1 = str_replace("##price##", $row["precio"], $aux1);
          $aux1 = str_replace("##txt##", $row['descripcion'], $aux1);                    

          if ($k == "info1")
            $info1 .= $aux1;
          elseif ($k == "info2")
            $info2 .= $aux1;
          elseif ($k == "info3")
            $info3 .= $aux1; 
        }      
      }        
    }

    $aux0 = $trozos2[0];
    $aux2 = $trozos2[2];
      
    $aux0 = str_replace("##state##", "Activas", $aux0);
    if (empty($info1))
      $info1 = "<div class = 'block' id=0>No hay anuncios activos.</div>";
    
    $activas = $aux0.$info1.$aux2;

    $aux0 = $trozos2[0];
    $aux0 = str_replace("##state##", "Vendidas", $aux0);
    
    if (empty($info2))
      $info2 = "<div class = 'block' id=0>No hay anuncios vendidos.</div>";
    
    $vendidas = $aux0.$info2.$aux2;

    $aux0 = $trozos2[0];
    $aux0 = str_replace("##state##", "Canceladas", $aux0);
    
    if (empty($info3))
      $info3 = "<div class = 'block' id=0>No hay anuncios cancelados.</div>";

    $canceladas = $aux0.$info3.$aux2;


    $div0 = "<div class = 'plentyblock'>";
    $div1 = "<input type = 'button' class = 'back'   value = 'Atras' ></div>";

    $info0 = str_replace("##result##", $div0.$activas.$vendidas.$canceladas.$div1, $info0);

    $text0 = str_replace("##result##", $info0, $text0);

    echo $text0;
  }

  function detailInfoViewAdmin($data){

    $pathFront = "../html/frontadmin.html";
    $text0 = file_get_contents($pathFront) or exit("Error detailInfoViewAdmin, [$pathFront]");

    $text0 = str_replace("##user##", $_SESSION["user"], $text0);

    $pathInfo = "../html/gestionusuarios.html";
    $text = file_get_contents($pathInfo) or exit("Error detailInfoViewAdmin, [$pathInfo]");

    $trozos = explode("##detailinfo##", $text);

    $trozos[1] = str_replace("##title##", $data["titulo"], $trozos[1]);
    $trozos[1] = str_replace("##date##", $data["fecha"], $trozos[1]);
    $trozos[1] = str_replace("##price##", $data["precio"], $trozos[1]);
    $trozos[1] = str_replace("##local##", $data["localizacion"], $trozos[1]);
    $trozos[1] = str_replace("##phone##", $data["telefono"], $trozos[1]);
    $trozos[1] = str_replace("##categoria##", $data["categoria"], $trozos[1]);
    $trozos[1] = str_replace("##txt##", $data["descripcion"], $trozos[1]);
    $trozos[1] = str_replace("##cancelar##", '', $trozos[1]);
    $trozos[1] .= "<input type = 'button' class = 'back'   value = 'Atras' >";
    $text0 = str_replace("##result##", $trozos[1], $text0);

    echo $text0;
  }

  function listAnuncioViewAdmin($data,$state,$order){

    $pathInfo = "../html/gestionusuarios.html";
    $text = file_get_contents($pathInfo) or exit("Error listAnuncioViewAdmin, [$pathInfo]");

    $trozos = explode("##detailinfo##", $text);

    $aux = "";
    $anuncios = "";

    while ($row = mysqli_fetch_array($data)) {
      $aux = $trozos[1];
      $aux = str_replace("##title##", $row['titulo'], $aux);
      $aux = str_replace("##date##", $row['fecha'], $aux);
      $aux = str_replace("##price##", $row['precio'], $aux);
      $aux = str_replace("##local##", $row['localizacion'], $aux);
      $aux = str_replace("##phone##", $row['telefono'], $aux);
      $aux = str_replace("##categoria##", $row['categoria'], $aux);
      $aux = str_replace("##txt##", $row['descripcion'], $aux);
      if ($state == 1){
        $aux = str_replace("##cancelar##", 
          "<input type = button class = buttoncommon value = Cancelar onclick=location.href='../php/controller.php?cmd=adminCmd&id=5&idAnuncio=##idAnuncio##&command=cancel'>", $aux);
        $aux = str_replace("##idAnuncio##", $row['idAnuncio'], $aux);
      }
      elseif ($state == 0){
        $aux = str_replace("##cancelar##", 
          "<input type = button class = buttoncommon value = Activar onclick=location.href='../php/controller.php?cmd=adminCmd&id=5&idAnuncio=##idAnuncio##&command=active'>", $aux);
        $aux = str_replace("##idAnuncio##", $row['idAnuncio'], $aux);
      }
        $aux = str_replace("##cancelar##", '', $aux);
      $anuncios .= $aux;      
    }

    
    $pathFront = "../html/frontadmin.html";
    $text0 = file_get_contents($pathFront) or exit("Error listAnuncioViewAdmin, [$pathFront]");

    $trozos1 = explode("##anuncio##", $text);

    $trozos1[1] = str_replace("##".$order."##", 'selected', $trozos1[1]);  


    if ($state == 1)
      $trozos1[1] = str_replace("##state##", 'Activos', $trozos1[1]);
    elseif ($state == 2)
      $trozos1[1] = str_replace("##state##", 'Vendidos', $trozos1[1]);
    elseif ($state == 0)
      $trozos1[1] = str_replace("##state##", 'Cancelados', $trozos1[1]);

    $trozos1[1] = str_replace("##anuncios##", $anuncios, $trozos1[1]);

    $text0 = str_replace("##user##", $_SESSION["user"], $text0);
    $text0 = str_replace("##result##", $trozos1[1], $text0);

    echo $text0;    

  }
?>