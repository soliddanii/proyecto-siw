<?php
/*
*	Sirve como interfaz entre la base de datos y nuestro model.php
*
*/
class Facade {
	
	private $con;

	public function __construct($con){

		$this->con = $con;

	}

	public function existNameUser($user){
		
		$query = "SELECT idUser FROM final_usuario WHERE user='".$user."'";

		return mysql_num_rows($this->con->action($query))> 0? True: False;
	}

	public function existUser($user,$pwd){

		$query = "SELECT idUser FROM final_usuario WHERE user='".$user."' and 
		password='".$pwd."'";

		return mysql_num_rows($this->con->action($query))> 0? True: False; 

	}

	public function insertUser($data){
		
		$query = "INSERT INTO final_usuario VALUES ('','".$data["user"]."'
			,'".$data["name"]."','".$data["pwd"]."','".$data["email"]."'
			,'hola','caracola')";

		return $this->con->action($query);
	}

	public function getIdUser($user){

		$query = "SELECT idUser,user FROM final_usuario WHERE user='".$user."'";

		$row = mysql_fetch_array($this->con->action($query));

		$data = array('idUser' => $row["idUser"], 'user' => $row["user"]);

		return $data;

	}

	public function modifyUser(){

	}

	public function deleteUser(){
		
	}

}

?>