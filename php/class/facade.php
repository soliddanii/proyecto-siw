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
	*	Devuelve la contraseña de un usuario a partir de su nombre de usuario y
	*	su email. 	
	*/
	public function recoverPass($user,$email){
		$query = "SELECT password FROM final_usuario WHERE nick='".$user."' and 
			email='".$email."'";
		return $this->con->action($query);
	}

	/*
	*	Modifica los datos de un usuario
	*/
	public function editName($id,$name){
		$query = "UPDATE final_usuario set name='".$name."' WHERE idUser ='".$id."'";
		$this->con->action($query);
	}

	public function editEmail($id,$email){
		$query = "UPDATE final_usuario set email='".$email."' WHERE idUser ='".$id."'";
		$this->con->action($query);
	}

	public function editPass($id,$passwd){
		$query = "UPDATE final_usuario set password='".$passwd."' WHERE idUser ='".$id."'";
		$this->con->action($query);
	}

	/*
	*	Elimina un usuario de la bbdd	
	*/
	public function deleteUser(){
		
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
		$query = "SELECT fi.IdImagen fi.small, fi.medium, fi.big FROM final_imagen fi WHERE idAnuncio = '".$idAnuncio."'";
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
    *   Devuelve un anuncio en concreto por su ID
    */
    public function getAnuncio($id){
        $query = "SELECT * FROM final_anuncio WHERE idAnuncio='".$id."'";
        return $this->con->action($query);
    }
    
    /*
    *  Devuleve anuncios con condicion y ordenados por una columna
    *  $condiciones = condiciones["precioMin"]["precioMax"]["titulo"]["localizacion"]["categoria"]
    *  $columnNameOrder = [precio;fecha;titulo]
    *  $order = [DESC;ASC]
    */
    public function getAnuncios($condiciones, $columnNameOrder, $order){
        $query = "SELECT fa.idAnuncio, fa.idCategoria, fa.precio, fa.fecha, fa.titulo, fa.localizacion, fi.small
                    FROM final_anuncio fa LEFT JOIN final_imagen fi ON (fi.idAnuncio = fa.idAnuncio AND
                        fi.idImagen = (SELECT MIN(idImagen) FROM final_imagen WHERE idAnuncio = fa.idAnuncio))";
                  
        $aux = "WHERE";
        
        if ($condiciones["precioMin"] != ""){
            $query = $query." ".$aux." fa.precio>=".$condiciones["precioMin"];
            $aux = "AND";
        }
        
        if ($condiciones["precioMax"] != ""){
            $query = $query." ".$aux." fa.precio<=".$condiciones["precioMax"];
            $aux = "AND";
        }
        
        if ($condiciones["titulo"] != ""){
            $query = $query." ".$aux." LOWER(fa.titulo) LIKE LOWER('%".$condiciones["titulo"]."%')";
            $aux = "AND";
        }
        
        if ($condiciones["localizacion"] != ""){
            $query = $query." ".$aux." LOWER(fa.localizacion) LIKE LOWER('%".$condiciones["localizacion"]."%')";
            $aux = "AND";
        }
        
        if ($condiciones["categoria"] != ""){
            $query = $query." ".$aux." fa.idCategoria=".$condiciones["categoria"];
            $aux = "AND";
        }
        
        if ($columnNameOrder != "" && $order != ""){
            $query = $query." ORDER BY fa.".$columnNameOrder." ".$order;
        }
        
        return $this->con->action($query);
    }
    
    
}

?>