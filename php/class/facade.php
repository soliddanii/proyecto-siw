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
		return mysql_num_rows($this->con->action($query))> 0? True: False;
	}

	/*
	*	Comprueba si existe un usuario ya registrado dentro de la bbdd
	*/
	public function existUser($user,$pwd){
		$query = "SELECT idUser FROM final_usuario WHERE nick='".$user."' and 
		password='".$pwd."'";
		return mysql_num_rows($this->con->action($query))> 0? True: False; 
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
		$row = mysql_fetch_array($this->con->action($query));
		$data = array('idUser' => $row["idUser"], 'nick' => $row["user"]);
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
	public function modifyUser(){

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
        ,'".$data["big"]."','".$data["medium"]."','".$data["small"]."')";
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
	*	Añade un nuevo anuncio a la bbdd	
	*/
	public function addAnuncio($data){
		$query = "INSERT INTO final_anuncio VALUES ('','".$data["idUser"]."'
			,'".$data["idCategoria"]."','','".$data["precio"]."','".$data["titulo"]."'
            ,'".$data["descripcion"]."','".$data["localizacion"]."','".$data["telefono"]."','','')";
		return $this->con->action($query);
	}
}

?>