<?php
class Formulario {
	protected $accion ;
	public function graficar_apertura($accion)
  { 
		?>
    <form method="post" action="<?php echo $accion ; ?>"> 
		<?php
  }
	  public function graficar_cierre()
  { ?>
    </form>
		<?php
  }

}


class Principio {
	protected $titulo ;
	public function __construct($tit)
	{
    $this->titulo=$tit;
  }
  public function graficar()
  { ?>
    <!DOCTYPE html>
		<html>
		<head>
		<title><?php echo $this->titulo ; ?></title>
		<link rel= "stylesheet" href= estilos.css>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		</head>
		<body>
		<?php
  }
}

class Fin {
	public function graficar()
  { ?>
		</body>
		</html>
		<?php
  }
}


class Paginai {
	private $principio;
  private $cabecera;
  private $cuerpo;
  private $pie;
	private $fin;
	private $formulario;
	private $borde ;
  public function sinborde()
  {
	  $this->borde = false ;
  }
  public function __construct($texto1,$texto2)
  {
		$this->principio=new Principio($texto1);
    $this->cabecera=new Cabecera($texto1);
    $this->cuerpo=new Cuerpo();
    $this->pie=new Pie($texto2);
		$this->fin=new Fin();
		$this->formulario=new Formulario();
		$this->borde = true ;
  }
  public function insertarCuerpo($texto)
  {
    $this->cuerpo->insertarParrafo($texto);
  }
	public function graficar()
	{
		$this->principio->graficar() ;
		if ( $this->borde == true )
		{
			$this->cabecera->graficar();
		}
		else
		{
			$this->cabecera->graficar_nb();
		}	
    $this->cuerpo->graficar();
    $this->pie->graficar();
		$this->fin->graficar() ;
  }
  public function graficar_c_form($accion)
  {
		$this->principio->graficar() ;
		$this->formulario->graficar_apertura($accion);
		if ( $this->borde == true )
		{
			$this->cabecera->graficar();
		}
		else
		{
			$this->cabecera->graficar_nb();
		}
    $this->cuerpo->graficar();
    $this->pie->graficar();
		$this->formulario->graficar_cierre();
		$this->fin->graficar() ;
  }
	public function graficar_cuerpo()
	{
	$this->cuerpo->graficar();
	}
	public function graficar_cabecera()
	{
	$this->cabecera->graficar();
	}
	public function graficar_pie()
	{
	$this->pie->graficar();
	}
}
?>
