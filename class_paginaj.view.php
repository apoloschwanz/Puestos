<!DOCTYPE html>
<html>
	<head>
		<title><?php echo $this->titulo ; ?></title>
		<link rel= "stylesheet" href= estilos.css>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	</head>
	<body>
		<table class="tablaext" width="98%" >
			<tr>
				<th style="text-align:center"><h2><?php echo $this->titulo;?></h2></th>
			</tr>
			<tr>
				<td align="center">
					<table class="tablacert">
						<tr>
							<td align="center">
								<!-- comienzo detalle -->
								<table class="<?php echo $this->clase_tabladet()?>">
									<!-- comienzo cuerpo -->
									<?php echo $this->graficar_cuerpo() ; ?>
								    <!-- fin cuerpo -->
								    <tr>
										<td style="text-align:center"><?php echo $this->pie ?>
										</td>
									</tr>
								</table>
								<!-- fin detalle -->
							</td>
						</tr>
					</table>
				</td>
			</tr>
		</table>			
	</body>
</html>
