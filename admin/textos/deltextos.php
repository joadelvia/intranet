<?
session_start();
include("../../config.php");
// COMPROBAMOS LA SESION
if ($_SESSION['autentificado'] != 1) {
	$_SESSION = array();
	session_destroy();
	header('Location:'.'http://'.$dominio.'/intranet/salir.php');	
	exit();
}

if($_SESSION['cambiar_clave']) {
	header('Location:'.'http://'.$dominio.'/intranet/clave.php');
}

registraPagina($_SERVER['REQUEST_URI'],$db_host,$db_user,$db_pass,$db);


?>
<?
include("../../menu.php");
if (isset($_GET['id'])) {$id = $_GET['id'];}elseif (isset($_POST['id'])) {$id = $_POST['id'];}else{$id="";}

		echo '<br />
<div align="center">
<div class="page-header">
  <h2>Libros de Texto <small> Borrar Libro de Texto</small></h2>
</div><br />';
 
$connection = mysql_connect($db_host, $db_user, $db_pass) or die ("No es posible conectar con la base de datos!");

mysql_select_db($db) or die ("No es posible conectar con la base de datos!");

$query = "DELETE FROM Textos WHERE id = '$id'";
$result = mysql_query($query) or die ("Error en la Consulta: $query. " . mysql_error());

mysql_close($connection);

echo '<br /><div align="center"><div class="alert alert-success alert-block fade in" style="width:auto;max-width:500px;">
            <button type="button" class="close" data-dismiss="alert">&times;</button>
			El Libro de Texto ha sido borrado de la Base de datos.
		</div></div><br />';
?>
 <? include("../../pie.php");?>		
</body>
</html>
