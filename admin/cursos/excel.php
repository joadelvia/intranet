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
<?php  
require_once("../../includes/php-excel/excel.php"); 
require_once("../../includes/php-excel/excel-ext.php");


if (isset($_POST['tipo'])) {
	$tipo=$_POST['tipo'];
}
else{
	$tipo="1";
}
$grupo=$_POST['select'];
//echo $tipo." ".$grupo;
$uni = substr($grupo,0,1);

	if($tipo==1) {
		$sql="SELECT nc as num, concat(alma.apellidos,', ',alma.nombre) as alumno FROM alma, FALUMNOS WHERE FALUMNOS.claveal=alma.claveal and alma.unidad='".$grupo."' ORDER BY nc";}
	
		if($tipo==2) {
		$sql="SELECT concat(alma.apellidos,', ',alma.nombre) as alumno, combasi as asignaturas, nc as num FROM alma, FALUMNOS WHERE alma.claveal=FALUMNOS.claveal and alma.Unidad='".$grupo."' ORDER BY nc";
	}	


$resEmp = mysql_query($sql) or die(mysql_error());
$totEmp = mysql_num_rows($resEmp);

if ($tipo==1){
while($datatmp = mysql_fetch_assoc($resEmp)) { 
	$data[] = $datatmp; 
}  
}
 
if ($tipo==2){
	while($datatmp = mysql_fetch_array($resEmp)) { 
		$mat="";
		$asig0 = explode(":",$datatmp[1]);
		foreach($asig0 as $asignatura){		
		$unidadn = substr($grupo,0,1);			
		$consulta = "select distinct abrev, curso from asignaturas where codigo = '$asignatura' and curso like '%$unidadn%' limit 1";
		$abrev = mysql_query($consulta);		
		$abrev0 = mysql_fetch_array($abrev);
		$curs=substr($abrev0[1],0,2);
		$mat.=$abrev0[0]."; ";
		}
		is_numeric($datatmp[2]);
	$data[] = array($datatmp[2],$datatmp[0],$mat);
}
} 
createExcel("listado_$grupo.xls", $data);
exit;
?>