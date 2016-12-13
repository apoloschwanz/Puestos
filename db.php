<?php

class Conexion {
  public $conexion;
  private $dr;
  private $host ;
  private $usr ;
  private $pass ;
  private $db ;
	private $extension ;
  public function __construct()
  {
	//
	// Levanta directorio raiz
	$this->dr = $_SERVER['DOCUMENT_ROOT'] ;
	//
	// Guindor
	if($this->dr == 'C:/wamp64/www' )
		{
		$this->db = 'puestos' ;
		$this->host = 'localhost';		
		$this->usr = 'root';
		$this->pass = '' ;
		$this->extension = 'mysql' ;
		}
	//
	// Conexion 
	//
	if ( $this->extension == 'mysql' )
		{
			//
			// Mysql
    	$this->conexion = mysqli_connect($this->host,$this->usr,$this->pass,$this->db) or die("Problemas en la conexion mysqli. Root=".$this->dr) ;
		}
	elseif ( $this->extension == 'pdo' )
		{
			//
			// PDO
			if (!file_exists($this->db)) 
				{
    			die("Could not find pdo database file: ".$this->db);
				}
			$str = "odbc:DRIVER={Microsoft Access Driver (*.mdb, *.accdb)}; DBQ=".$this->db."; Uid=; Pwd=;" ;
			$this->conexion = new PDO($str) ;
		}
	else
		{
			die("No se seleccióno un tipo de extensión válido para conectarse a la base de datos ".'Document Root = '.$this->dr ) ;
		}
 }
  public function cerrar()
  {
  mysqli_close($this->conexion);
  }
 public function ver_root()
  {
    echo ' El Document Root es: '.$this->dr ;
  }

}

class db {
	protected function creardb()
	{
		$txt = " CREATE DATABASE puestos CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ; " ;
	}
}

?>
