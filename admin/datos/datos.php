<? 
session_start();
include("../../config.php");
if($_SESSION['autentificado']!='1')
{
session_destroy();
header("location:http://$dominio/intranet/salir.php");	
exit;
}
registraPagina($_SERVER['REQUEST_URI'],$db_host,$db_user,$db_pass,$db);
?>

<?
include("../../menu.php");

if (isset($_POST['grupo'])) {$grupo = $_POST['grupo'];} elseif (isset($_GET['grupo'])) {$grupo = $_GET['grupo'];} else{$grupo="";}
if (isset($_POST['nombre'])) {$nombre = $_POST['nombre'];} elseif (isset($_GET['nombre'])) {$nombre = $_GET['nombre'];} else{$nombre="";}
if (isset($_POST['apellidos'])) {$apellidos = $_POST['apellidos'];} elseif (isset($_GET['apellidos'])) {$apellidos = $_GET['apellidos'];} else{$apellidos="";}
if (isset($_GET['clave_al'])) {$clave_al = $_GET['clave_al'];} else{$clave_al="";}
if (isset($_GET['unidad'])) {
	$unidad = $_GET['unidad'];
	$tr_uni = explode("-",$_GET['unidad']);
	$nivel = $tr_uni[0];
	$grupo = $tr_uni[1];
	$AUXSQL = " and unidad = '$nivel-$grupo'";
} else{$unidad="";}
?>
 <br />
  <div align="center">
<div class="page-header" align="center">
  <h2>Datos de los Alumnos <small> Consultas</small></h2>
</div>
 </div>
 <div class='container-fluid'>
  <div class="row-fluid">
  <div class="span12">
  <?php
  // Si se envian datos desde el campo de b�squeda de alumnos, se separa claveal para procesarlo.
  if (!(isset($_GET['seleccionado']))) {
  	$seleccionado="";
  }else{
  	$seleccionado=$_GET['seleccionado'];
  }
    if (!(isset($_GET['alumno']))) {
  	$alumno="";
  }
  else{
  	$alumno=$_GET['alumno'];
  }
    if (!(isset($AUXSQL))) {
  	$AUXSQL="";
  }
  
  if (isset($seleccionado) and $seleccionado=="1") {
   	$tr=explode(" --> ",$alumno);
   	$clave_al=$tr[1];
	$nombre_al=$tr[0];
	$uni=mysql_query("select unidad, nivel, grupo from alma where claveal='$clave_al'");
	$un=mysql_fetch_array($uni);
	$unidad=$un[0];
   	
  	$foto = '../../xml/fotos/'.$clave.'.jpg';
	if (file_exists($foto)) {
		echo "<div align='center'><img src='../../xml/fotos/$clave_al.jpg' border='2' width='100' height='119' style='margin:auto;border:1px solid #bbb;'  /></div>";
			  echo "<br />";
	}    
   }
    $AUXSQL == "";
  #Comprobamos si se ha metido Apellidos o no.
    if  (TRIM("$apellidos")=="")
    {
    }
    ELSE
    {
    $AUXSQL .= " and alma.apellidos like '%$apellidos%'";
    }
  if  (TRIM("$nombre")=="")
    {
    }
    ELSE
    {
    $AUXSQL .= " and alma.nombre like '%$nombre%'";
    }
  
    if  (isset($_POST['unidad']))
    {
    	$AUXSQL=" and (";
    	foreach ($_POST['unidad'] as $grupo){
    	$AUXSQL .= " alma.unidad like '$grupo' or";
    	}
    	$AUXSQL=substr($AUXSQL,0,-2);
    	$AUXSQL.=")";
    }
	
  	if  (TRIM("$clave_al")=="")
    {
    }
    ELSE
    {
    $AUXSQL .= " and alma.claveal = '$clave_al'";
    }
if ($seleccionado=='1') {
	    $AUXSQL = " and alma.claveal = '$clave_al'";
}
  
  $SQL = "select distinct alma.claveal, alma.apellidos, alma.nombre, alma.nivel, alma.grupo,\n
  alma.DNI, alma.fecha, alma.domicilio, alma.telefono, alma.telefonourgencia, padre, matriculas from alma
  where 1 " . $AUXSQL . " order BY nivel, grupo, alma.apellidos, nombre";
   //echo $SQL;
  $result = mysql_query($SQL);
  if (mysql_num_rows($result)>25 and !($seleccionado=="1")) {
  	$datatables_activado = true;
  }
  if ($row = mysql_fetch_array($result))
        {

echo "<div align=center><table  class='table table-striped tabladatos' style='width:auto;'>";
	echo "<thead><tr>
			<th>Clave</th>
	        <th> DNI</th>
	        <th>Nombre</th>
	        <th width='60'>Grupo</th>
	        <th> Fecha</th>
	        <th>Repite</th>
	        <th>Domicilio</th>
        	<th>Padre</th>
        	<th>Tfno. Urgencias</th>					
		";

echo "</th><th></th>";			
				echo "</tr></thead><tbody>";
                do {
                	if ($row[11]>1) {
                		$repite="S�";
                	}
                	else{
                		$repite="No";
                	}
                	$nom=$row[1].", ".$row[2];
                	$unidad = $row[3]."-".$row[4];
		$claveal = $row[0];
		echo "<tr>
<td>$row[0]</td>
<td>$row[5]</td>
<td>$nom</td>
<td>$unidad</td>
<td>$row[6]</td>
<td>$repite</td>
<td>$row[7]</td>
<td>$row[10]</td>
<td>$row[9]</td>";
 
if ($seleccionado=='1'){
	$todo = '&todos=Ver Informe Completo del Alumno';
}
echo "<td><a href='http://$dominio/intranet/admin/informes/index.php?claveal=$claveal&todos=Ver Informe Completo del Alumno'><i class='icon icon-search' rel='Tooltip' title='Ver detalles'> </i> ";
echo '</a></td></tr>';
        } while($row = mysql_fetch_array($result));
        echo "</tbody></table></font></center>\n";
        } else
        {
			echo '<div align="center"><div class="alert alert-warning alert-block fade in" style="max-width:500px;">
            <button type="button" class="close" data-dismiss="alert">&times;</button>
			<h5>ATENCI�N:</h5>
No hubo suerte, bien porque te has equivocado
        al introducir los datos, bien porque ning�n dato se ajusta a tus criterios.
		</div></div>';
        
        }
  ?>
  <br />
  <?
  if ($_GET['seleccionado']=='1'){
  	
  	  echo "<a href='http://$dominio/intranet/admin/informes/index.php?claveal=$claveal&todos=Ver Informe Completo del Alumno' class='btn btn-primary'>Datos completos</a>";
  echo "&nbsp;<a class='btn btn-primary' href='http://$dominio/intranet/admin/informes/cinforme.php?nombre_al=$alumno&nivel=$un[1]&grupo=$un[2]'>Informe hist�rico del Alumno</a> ";
   	echo "&nbsp;<a class='btn btn-primary' href='../fechorias/infechoria.php?seleccionado=1&nombre_al=$alumno'>Problema de disciplina</a> ";
   	echo "&nbsp;<a class='btn btn-primary' href='http://$dominio/intranet/admin/cursos/horarios.php?curso=$unidad'>Horario</a>";
   	if (stristr($_SESSION['cargo'],'1') == TRUE) {
   		$dat = mysql_query("select nivel, grupo from FALUMNOS where claveal='$clave_al'");
   		$tut=mysql_fetch_row($dat);
   		$nivel=$tut[0];
   		$grupo=$tut[1];
   		echo "&nbsp;<a class='btn btn-primary' href='../jefatura/tutor.php?seleccionado=1&alumno=$alumno&nivel=$nivel&grupo=$grupo'>Acci�n de Tutor�a</a>";
   	}
   	if (stristr($_SESSION['cargo'],'8') == TRUE) {
   		$dat = mysql_query("select nivel, grupo from FALUMNOS where claveal='$clave_al'");
   		$tut=mysql_fetch_row($dat);
   		$nivel=$tut[0];
   		$grupo=$tut[1];
   		echo "&nbsp;<a class='btn btn-primary' href='../orientacion/tutor.php?seleccionado=1&alumno=$alumno&nivel=$nivel&grupo=$grupo'>Acci�n de Tutor�a</a>";
   	}
   	if (stristr($_SESSION['cargo'],'2') == TRUE) {
   		$tutor = $_SESSION['profi'];
   		$dat = mysql_query("select nivel, grupo from FALUMNOS where claveal='$clave_al'");
   		$dat_tutor = mysql_query("select nivel, grupo from FTUTORES where tutor='$tutor'");
   		$tut=mysql_fetch_row($dat);
   		$tut2=mysql_fetch_array($dat_tutor);
   		$nivel=$tut[0];
   		$grupo=$tut[1];
   		$nivel_tutor=$tut2[0];
   		$grupo_tutor=$tut2[1];
   		if ($nivel==$nivel_tutor and $grupo==$grupo_tutor) {
   		echo "&nbsp;<a class='btn btn-primary' href='../tutoria/tutor.php?seleccionado=1&alumno=$alumno&nivel=$nivel&grupo=$grupo&tutor=$tutor'>Acci�n de Tutor�a</a>";	
   		}
   	}
  }
	?>
    <? include("../../pie.php");?>
</BODY>
</HTML>
