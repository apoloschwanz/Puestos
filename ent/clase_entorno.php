<?php
class entorno
{
	protected $tipo ;
	public function __construct()
	{
		//$this->tipo = 'des' ;
		//$this->tipo = 'prod' ;
		//$this->tipo = 'paralelo' ;
		$this->tipo = 'respaldo' ;
	}
	public function es_prod()
	{
		if( $this->tipo == 'prod' )
			return true ;
		else
			return false ;
	}
	public function es_des()
	{
		if( $this->tipo == 'des' )
			return true ;
		else
			return false ;
	}
	public function es_respaldo()
	{
		if( $this->tipo == 'respaldo' )
			return true ;
		else
			return false ;
	}
	public function es_paralelo()
	{
		if( $this->tipo == 'paralelo' )
			return true ;
		else
			return false ;
	}
}
