<?php

class Connection{

	//variables para los datos de la base de datos
	private $server;
	private $userDB;
	private $passDB;
	private $nameDB;

	public function __construct(){

		//Iniciar las variables con los datos de la base de datos
		$this->server = "localhost";
		$this->userDB = "root";
		$this->passDB = "Tesla314msql";
		$this->nameDB = "siw21";

		$this->getConnection();
	}

	public function getConnection(){

		// Conexion con MySQL
		mysql_connect($this->server,$this->userDB,$this->passDB) 
		or exit("Error Conexion con MySQL");

		//Conexion a la base de datos a utilizar
		mysql_select_db($this->nameDB) or exit("Error Conexion con $nameDB");
		
	}

	public function action($query){

		return mysql_query($query);

	}

	public function close(){

		mysql_close();

	}
}
?>