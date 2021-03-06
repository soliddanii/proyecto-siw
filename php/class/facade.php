<?php
/*
*	Sirve como interfaz entre la base de datos y nuestro model.php
*
*/
class Facade {
	
	private $con;

	/*
	*	El constructor recibe como parametro una referencia al objeto conexion
	*	
	*/
	public function __construct($con){
		$this->con = $con;
	}

	/*
	*	Comprueba si un nombre de usuario ya existe en la bbdd.
	*	Los nombre de usuario son unicos. 
	*/
	public function existNameUser($user){		
		$query = "SELECT idUser FROM final_usuario WHERE nick='".$user."'";
		return mysqli_num_rows($this->con->action($query))> 0? True: False;
	}

	/*
	*	Comprueba si existe un usuario ya registrado dentro de la bbdd
	*/
	public function existUser($user,$pwd){
		$query = "SELECT idUser FROM final_usuario WHERE nick='".$user."' and 
		password='".$pwd."'";
		return mysqli_num_rows($this->con->action($query))> 0? True: False; 
	}

	/*
	*	Inserta un usuario en la bbdd. Los datos vienen dentro de un array mapeado
	*	$data["user"], $data["name"], ...
	*	Retorna un valor booleano que indica si se ha insertado correctamente o no.
	*/
	public function insertUser($data){		
		$query = "INSERT INTO final_usuario VALUES ('','".$data["user"]."'
			,'".$data["name"]."','".$data["pwd"]."','".$data["email"]."')";
		return $this->con->action($query);
	}

	/*
	*	Devuelve los datos relevantes para iniciar la sesion de usuario que
	*	ya está registrado en la bbdd.
	*	Para iniciar la sesion se necesita idUser y user, que son el id asignado
	*	al usuario en la bbdd y su nombre respectivamente
	*/
	public function getIdUser($user){
		$query = "SELECT idUser,nick FROM final_usuario WHERE nick='".$user."'";
		$row = mysqli_fetch_array($this->con->action($query));
		$data = array('idUser' => $row["idUser"], 'user' => $row["nick"]);
		return $data;
	}

	/*
	*	Establece una contraseña nueva a partir de un nick 
	*	(Esto nunca lo hariamos asi, es porque no podemos enviar emails de recuperacion ni nada de eso)
    *   Es una simulacion
	*/
	public function recoverPass($user, $pass){
		$query = "UPDATE final_usuario set password='".$pass."' WHERE nick ='".$user."'";
		return $this->con->action($query);
	}

	/*
	*	Modifica los datos de un usuario
	*/
	public function editName($id,$name){
		$query = "UPDATE final_usuario set name='".$name."' WHERE idUser ='".$id."'";
		return $this->con->action($query);
	}

	public function editEmail($id,$email){
		$query = "UPDATE final_usuario set email='".$email."' WHERE idUser ='".$id."'";
		return $this->con->action($query);
	}

	public function editPass($id,$passwd){
		$query = "UPDATE final_usuario set password='".$passwd."' WHERE idUser ='".$id."'";
		return $this->con->action($query);
	}

    /*
	*	Recupera todas las categorias de la bbdd	 
	*/
	public function getCategories(){
		$query = "SELECT * FROM final_categoria";
		return $this->con->action($query);
	}
    
    /*
	*	Añade una nueva categoria a la bbdd	
	*/
	public function addCategory($categoria){
		$query = "INSERT INTO final_categoria VALUES ('','".$categoria."')";
		return $this->con->action($query);
	}
    
    /*
	*	Borra una categoria de la bbdd	
	*/
	public function delCategory($idCategoria){
		$query = "DELETE FROM final_categoria WHERE idCategoria = '".$idCategoria."'";
		return $this->con->action($query);
	}
    
    /*
	*	Añade una nueva imagen a la bbdd	
	*/
	public function addImage($data){
		$query = "INSERT INTO final_imagen VALUES ('".$data["idAnuncio"]."','".$data["idImagen"]."'
        ,'".mysqli_real_escape_string($this->con->getConnection(), $data["big"])."'
        ,'".mysqli_real_escape_string($this->con->getConnection(), $data["medium"])."'
        ,'".mysqli_real_escape_string($this->con->getConnection(), $data["small"])."')";
        
		return $this->con->action($query);
	}
    
    /*
	*	Borra todas las imagenes de un articulo de la bbdd	
	*/
	public function delAllImages($idAnuncio){
		$query = "DELETE FROM final_imagen WHERE idAnuncio = '".$idAnuncio."'";
		return $this->con->action($query);
	}
    
    /*
	*	Borra una imagen de un anuncio de la bbdd	
	*/
	public function delImage($idAnuncio, $idImagen){
		$query = "DELETE FROM final_imagen WHERE idAnuncio = '".$idAnuncio."' AND idImagen = '".$idImagen."'";
		return $this->con->action($query);
	}
    
    /*
	*	Obtiene todas las imagenes de un anuncio de la bbdd	
	*/
	public function getImages($idAnuncio){
		$query = "SELECT fi.idImagen, fi.small, fi.medium, fi.big FROM final_imagen fi WHERE idAnuncio = '".$idAnuncio."'"; 
		return $this->con->action($query);
	}
    
      
    /*
	*	Añade un nuevo anuncio a la bbdd	
	*/
	public function addAnuncio($data){
		$query = "INSERT INTO final_anuncio (idUser, idCategoria, precio, titulo, descripcion, localizacion, telefono) 
            VALUES ('".$data["idUser"]."','".$data["idCategoria"]."','".$data["precio"]."','".$data["titulo"]."'
            ,'".$data["descripcion"]."','".$data["localizacion"]."','".$data["telefono"]."')";
		return $this->con->action($query);
	}
    
    /*
    *   Obtener el ultimo id insertado
    */
    public function getLastId(){
        return $this->con->insertId();
    }
    
    /*
    *   Actualiza el anuncio al estado cancelado
    */
    public function actualizarCanceladoAnuncio($idAnuncio){
        $query = "UPDATE final_anuncio SET estado=0 WHERE idAnuncio='".$idAnuncio."'";
        return $this->con->action($query);
    }
    
    /*
    *   Actualiza el anuncio al estado vendido
    */
    public function actualizarVendidoAnuncio($idAnuncio, $idUser){
        $query = "UPDATE final_anuncio SET estado=2, idComprador='".$idUser."' WHERE idAnuncio='".$idAnuncio."'";
        return $this->con->action($query);
    }
    
    /*
    *   Obtener el estado de un anuncio
    */
    public function anuncioGetEstado($idAnuncio){
        $query = "SELECT estado FROM final_anuncio WHERE idAnuncio='".$idAnuncio."'";
        return $this->con->action($query);
    }
    
    /*
    *   Devuelve un anuncio en concreto por su ID
    */
    public function getAnuncio($id){
        $query = "SELECT fa.idUser, fa.idAnuncio, fa.fecha, fa.precio, fa.titulo, fa.descripcion, 
        fa.localizacion, fa.telefono, fa.estado, fa.idComprador, fu.nick, fu.name 
        FROM final_anuncio fa INNER JOIN final_usuario fu USING(idUser) WHERE idAnuncio='".$id."'";
        return $this->con->action($query);
    }
    
    /*
    *  Devuleve anuncios con condicion y ordenados por una columna
    *  $condiciones = condiciones["precioMin"]["precioMax"]["titulo"]["localizacion"]["categoria"]
    *  $columnNameOrder = [precio;fecha;titulo]
    *  $order = [DESC;ASC]
    */
    public function getAnuncios($condiciones, $columnNameOrder, $order, $fromLimit, $toLimit, $misAnuncios, $misFavoritos){
        $query = "SELECT fa.idAnuncio, fa.idCategoria, fa.precio, fa.fecha, fa.titulo, fa.localizacion, fa.estado, fa.idUser, fi.small
                    FROM final_anuncio fa LEFT JOIN final_imagen fi ON (fi.idAnuncio = fa.idAnuncio AND
                        fi.idImagen = (SELECT MIN(idImagen) FROM final_imagen WHERE idAnuncio = fa.idAnuncio))";
                  
        $aux = "WHERE";
        
        if ($condiciones["precioMin"] !== ""){
            $query = $query." ".$aux." fa.precio>=".$condiciones["precioMin"];
            $aux = "AND";
        }

        if ($condiciones["precioMax"] !== ""){
            $query = $query." ".$aux." fa.precio<=".$condiciones["precioMax"];
            $aux = "AND";
        }
        
        if ($condiciones["titulo"] != ""){
            $query = $query." ".$aux." LOWER(fa.titulo) LIKE LOWER('%".$condiciones["titulo"]."%') 
                OR strcmp(soundex(fa.titulo), soundex('".$condiciones["titulo"]."')) = 0";
            $aux = "AND";
        }
        
        if ($condiciones["localizacion"] != ""){
            $query = $query." ".$aux." LOWER(fa.localizacion) LIKE LOWER('%".$condiciones["localizacion"]."%')
                OR strcmp(soundex(fa.localizacion), soundex('".$condiciones["localizacion"]."')) = 0";
            $aux = "AND";
        }
        
        if ($condiciones["categoria"] != ""){
            $query = $query." ".$aux." fa.idCategoria=".$condiciones["categoria"];
            $aux = "AND";
        }
        
        //Obtiene solo de los anuncios del usuario
        if ($misAnuncios == 1 && $condiciones["idUser"] != ""){
            $query = $query." ".$aux." fa.idUser=".$condiciones["idUser"];
            $aux = "AND";
        }
        
        //Obtiene solo de los favoritos del usuario
        if ($misFavoritos == 1 && $condiciones["idUser"] != ""){
            $query = $query." ".$aux." fa.idAnuncio IN (SELECT idAnuncio FROM final_favorito WHERE idUser=".$condiciones["idUser"].")";
            $aux = "AND";
        }
        
        //Si no se estan pidiendo los favoritos o mis anuncios, no mostramos los cancelados o terminados
        if ($misFavoritos != 1 && $misAnuncios != 1){
            $query = $query." ".$aux." fa.estado=1";
            $aux = "AND";
        }
        
        if ($columnNameOrder != "" && $order != ""){
            $query = $query." ORDER BY fa.".$columnNameOrder." ".$order;
        }
        
        $query = $query." LIMIT ".$fromLimit.",".$toLimit;
        
        //ChromePhp::log($query);
        return $this->con->action($query);
    }
    
    /* 
    *  Obtiene todas los comentarios de un anuncio de la bbdd   
    */ 
    public function getComentarios($idAnuncio){ 
        /*$query = "SELECT fc.idUser, fc.idComentario, fc.idAnuncio, fc.comentario, fc.idPadre, fc.fecha, fu.nick, fu2.nick nickPadre
            FROM final_comentario fc INNER JOIN final_usuario fu USING(idUser) LEFT JOIN  final_usuario fu2 ON(fc.idPadre = fu2.idUser) 
            WHERE idAnuncio = '".$idAnuncio."' ORDER BY fecha ASC"; */
            
        $query = "SELECT  fc.idAnuncio, fc.idComentario, fc.idUser, fu.nick, fc.comentario, fc.idPadre, fc.fecha, aux2.nick nickPadre
            FROM final_comentario fc INNER JOIN      final_usuario fu ON(fc.idUser=fu.idUser) LEFT JOIN
            (SELECT aux.idComentario, fu.nick
                FROM (SELECT fc2.idComentario, fc2.idUser FROM final_comentario fc1 INNER JOIN final_comentario fc2 ON (fc1.idPadre=fc2.idComentario)) aux
            INNER JOIN final_usuario fu ON(aux.idUser=fu.idUser)) aux2 ON (fc.idPadre=aux2.idComentario)
            WHERE fc.idAnuncio = '".$idAnuncio."' ORDER BY fc.fecha ASC";
    
        return $this->con->action($query); 
    }
     
     
    /* 
    *  Obtiene si un anuncio es favorito de un usuario 
    */ 
    public function isFavorito($idUser, $idAnuncio){ 
        $query = "SELECT idUser FROM final_favorito WHERE idUser='".$idUser."' AND idAnuncio='".$idAnuncio."'"; 
        return mysqli_num_rows($this->con->action($query))> 0 ? True : False; 
    } 
    
    /* 
    *  Añade un favorito
    */ 
    public function addFavorito($idUser, $idAnuncio){ 
        $query = "INSERT INTO final_favorito VALUES('".$idUser."', '".$idAnuncio."')"; 
        return $this->con->action($query);
    }
    
    /* 
    *  Elimina un favorito
    */ 
    public function deleteFavorito($idUser, $idAnuncio){ 
        $query = "DELETE FROM final_favorito WHERE idUser='".$idUser."' AND idAnuncio='".$idAnuncio."'"; 
        return $this->con->action($query);
    }
    
    
    /* 
    *  Comprueba si un comentario existe en un anuncio 
    */ 
    public function existeComentario($idComentario, $idAnuncio){ 
        $query = "SELECT idComentario FROM final_comentario WHERE idComentario='".$idComentario."' AND idAnuncio='".$idAnuncio."'"; 
        return mysqli_num_rows($this->con->action($query))> 0 ? True : False; 
    }
    
    /* 
    *  Inserta un comentario 
    */ 
    public function insertarComentario($idUser, $idAnuncio, $comentario, $idRespuesta){
        $query = '';
        if($idRespuesta >= 0){
            $query = "INSERT INTO final_comentario (idUser, idAnuncio, comentario, idPadre) 
                VALUES ('".$idUser."', '".$idAnuncio."', '".$comentario."', '".$idRespuesta."')"; 
        }else{
            $query = "INSERT INTO final_comentario (idUser, idAnuncio, comentario) 
                VALUES ('".$idUser."', '".$idAnuncio."', '".$comentario."')"; 
        }
        
        return $this->con->action($query); 
    }
    
    /* 
    *  Borra un comentario 
    */ 
    public function borrarComentario($idAnuncio, $idComentario){
        $query = "DELETE FROM final_comentario WHERE idAnuncio='".$idAnuncio."' AND idComentario='".$idComentario."'"; 
        return $this->con->action($query); 
    }
    
    /* 
    *  Comprueba si un un anuncio me pertenece 
    */ 
    public function esMiAnuncio($idAnuncio, $idUser){ 
        $query = "SELECT idAnuncio FROM final_anuncio WHERE idAnuncio='".$idAnuncio."' AND idUser='".$idUser."'"; 
        return mysqli_num_rows($this->con->action($query))> 0 ? True : False; 
    }
     
    /* 
    * GESTION DE ADMIN
    */

    /* 
    * Devuelve un valor booleano, true si existe el usuario en la bbdd y false
    * en caso contrario.
   	*/
    public function existAdmin($user, $pwd){    	
    	$query = "SELECT idAdmin,user FROM final_admin WHERE user='".$user."' and 
		password='".$pwd."'";
		return mysqli_num_rows($this->con->action($query))> 0? True: False; 
    }

    /*
    * Devuelve un array con los datos de un usuario admin existente en la bbdd
    */
    public function getDataAdmin($user, $pwd){
    	$query = "SELECT idAdmin,user FROM final_admin WHERE user='".$user."' and 
		password='".$pwd."'";
		$row = mysqli_fetch_array($this->con->action($query));
		$data = array('idAdmin' => $row["idAdmin"], 'user' => $row["user"]);
		return $data;
    }

    /*
        * Devuelve algunos datos personales de todos los usuarios.
    */
    public function getDataUsers($order, $start, $end){     
      if (empty($order))
        $query = "SELECT idUser,nick,name,email FROM final_usuario LIMIT ".$start.",".$end;
      else
        $query = "SELECT idUser,nick,name,email FROM final_usuario ORDER BY ".$order." LIMIT ".$start.",".$end;

      return $this->con->action($query);
    }

    /*
        * Devuelve el numero de usuarios que existe en la bbdd
    */
    public function numUsers(){
      $query = "SELECT count(*) FROM final_usuario";
      return $this->con->action($query);
    }

    /*
        * Muestra todos los datos de un usuario, anuncios, ....
    */
    public function getInfoUser($idUser){
      $query0 = "SELECT nick,name,email FROM final_usuario WHERE idUser=".$idUser;
      
      $query1 = "SELECT idAnuncio,titulo,fecha,precio,descripcion FROM final_anuncio
      WHERE idUser=".$idUser." and estado=1";
      
      $query2 = "SELECT idAnuncio,titulo,fecha,precio,descripcion FROM final_anuncio
      WHERE idUser=".$idUser." and estado=2";

      $query3 = "SELECT idAnuncio,titulo,fecha,precio,descripcion FROM final_anuncio
      WHERE idUser=".$idUser." and estado=0";

      $r0 = $this->con->action($query0);
      $r1 = $this->con->action($query1);
      $r2 = $this->con->action($query2);
      $r3 = $this->con->action($query3);

      return array('info0' => $r0, 'info1' => $r1, 'info2' => $r2, 'info3' => $r3);
    }

    public function getInfoAnuncio($idAnuncio){
      $query = "SELECT fa.idAnuncio,fc.categoria,fa.titulo,fa.fecha,fa.precio,fa.descripcion,fa.localizacion,fa.telefono 
      FROM final_anuncio fa INNER JOIN final_categoria fc ON(fa.idCategoria=fc.idCategoria)
      WHERE idAnuncio=".$idAnuncio;

      return $this->con->action($query);
    }

    public function getListAnuncios($state,$order){

      if ($order == 1){
        $query = "SELECT fa.idAnuncio,fc.categoria,fa.titulo,fa.fecha,fa.precio,fa.descripcion,fa.localizacion,fa.telefono 
        FROM final_anuncio fa INNER JOIN final_categoria fc ON(fa.idCategoria=fc.idCategoria)
        WHERE estado=".$state." ORDER BY fecha DESC";

      }elseif ($order == 2) {
        $query = "SELECT fa.idAnuncio,fc.categoria,fa.titulo,fa.fecha,fa.precio,fa.descripcion,fa.localizacion,fa.telefono 
        FROM final_anuncio fa INNER JOIN final_categoria fc ON(fa.idCategoria=fc.idCategoria)
        WHERE estado=".$state." ORDER BY fecha ASC";        

      }elseif ($order == 3) {
        $query = "SELECT fa.idAnuncio,fc.categoria,fa.titulo,fa.fecha,fa.precio,fa.descripcion,fa.localizacion,fa.telefono 
        FROM final_anuncio fa INNER JOIN final_categoria fc ON(fa.idCategoria=fc.idCategoria)
        WHERE estado=".$state." ORDER BY precio ASC";        

      }elseif ($order == 4) {
        $query = "SELECT fa.idAnuncio,fc.categoria,fa.titulo,fa.fecha,fa.precio,fa.descripcion,fa.localizacion,fa.telefono 
        FROM final_anuncio fa INNER JOIN final_categoria fc ON(fa.idCategoria=fc.idCategoria)
        WHERE estado=".$state." ORDER BY precio DESC";        
        
      }else{
        $query = "SELECT fa.idAnuncio,fc.categoria,fa.titulo,fa.fecha,fa.precio,fa.descripcion,fa.localizacion,fa.telefono 
        FROM final_anuncio fa INNER JOIN final_categoria fc ON(fa.idCategoria=fc.idCategoria)
        WHERE estado=".$state;        
      }

      return $this->con->action($query);

    }

    public function newStateAnuncio($idAnuncio,$state){
      $query = "UPDATE final_anuncio SET estado=".$state." WHERE idAnuncio=".$idAnuncio;
      return $this->con->action($query);
    }
    

    /*
    *   Elimina un usuario de la bbdd   
    */
    public function deleteUser($idUser){
      $query = "DELETE FROM final_usuario WHERE idUser=".$idUser;
      return $this->con->action($query);
    }
}
?>