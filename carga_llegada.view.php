<!DOCTYPE html>
<html>
<head>
	<meta charset="UTF-8">
	<title>Carga Llegada</title>
	<link rel="stylesheet" href="estilos.css">
</head>
<body>
	<div class="wrap" align="center" >
		<form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']);?>">
			<input type="number" class="form-control" name="part_id" placeholder="numero de participante" value="" width="35" autofocus >
			<input type="submit" name="ok_tiempo" value="Tiempo">
		</form>
	</div>
</body>
</html>
