<?php

class Connection{

	//variables para los datos de la base de datos
	private $server;
	private $userDB;
	private $passDB;
	private $nameDB;
    private $conn;

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
        $this->conn = mysqli_connect($this->server,$this->userDB,$this->passDB, $this->nameDB);
        
        if (!$this->conn) {
            echo "Error: No se pudo conectar a MySQL." . PHP_EOL;
            echo "errno de depuración: " . mysqli_connect_errno() . PHP_EOL;
            echo "error de depuración: " . mysqli_connect_error() . PHP_EOL;
            exit;
        }

		//Conexion a la base de datos a utilizar
		//mysqli_select_db($this->conn, $this->nameDB) or die("Error Conexion con $nameDB");
		
	}

	public function action($query){
    
        //Ejecutar una sentencia y devolver el error si se produce
		$temp = mysqli_query($this->conn, $query) or die(mysqli_error($this->conn));
        return $temp;

	}
    
    public function insertId(){
        
        //Devolver el ultimo id que ha producido una insercion
        $id = mysqli_insert_id($this->conn);
        return $id;
        
    }

	public function close(){

		mysqli_close($this->conn);

	}
}
?>