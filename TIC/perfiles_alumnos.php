<?
session_start();
include("../config.php");
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


if (isset($_POST['profe'])) $profe = $_POST['profe'];
if (isset($_POST['curso'])) $curso = $_POST['curso'];


include("../menu.php");
include("menu.php");
?>


<?php if(isset($_POST['enviar'])) : ?>

<?php
$exp_unidad = explode('-->',$curso);
$unidad = $exp_unidad[0];
$asignatura = $exp_unidad[3];
?>

	<div class="container">
		
		<!-- TITULO DE LA PAGINA -->
		<div class="page-header">
			<h2>Centro TIC <small>Perfiles de alumnos de <?php echo $unidad; ?></small></h2>
		</div>
		
		<div class="alert alert-info">
			<h4>Cambio de contrase�a</h4>
			Los alumnos/as podr�n cambiar su contrase�a de acceso accediendo a la p�gina web <a href="http://c0/gesuser/" class="alert-link" target="_blank">http://c0/gesuser/</a> desde la red local del centro. En caso de olvido deben ponerse en contacto con el Coordinador TIC para restablecer la contrase�a.
		</div>
		
		
		<!-- SCAFFOLDING -->
		<div class="row">
		
			<!-- COLUMNA CENTRAL -->
			<div class="col-sm-12">
				
				<div class="table-responsive">	
					<table class="table table-bordered table-striped">
						<thead>
							<tr>
								<th colspan="2">Alumno/a</th>
								<th>Usuario</th>
								<th>Contrase�a</th>
							</tr>
						</thead>
						<tbody>
							<?php $result = mysql_query("SELECT DISTINCT usuarioalumno.nombre, usuarioalumno.usuario, usuarioalumno.unidad, FALUMNOS.nombre, FALUMNOS.apellidos, usuarioalumno.pass, FALUMNOS.claveal FROM usuarioalumno, FALUMNOS, alma WHERE FALUMNOS.claveal = alma.claveal AND FALUMNOS.claveal = usuarioalumno.claveal AND usuarioalumno.unidad = '$unidad' AND combasi LIKE '%$asignatura%'ORDER BY nc ASC"); ?>
							<?php while ($row = mysql_fetch_array($result)): ?>
							<tr>
								<td class="col-xs-1 text-center">
									<?php if (file_exists('../xml/fotos/'.$row['claveal'].'.jpg')): ?>
									<img class="img-responsive" src="<?php echo '../xml/fotos/'.$row['claveal'].'.jpg'; ?>" alt="<?php echo $row['apellidos'].', '.$row['nombre']; ?>">
									<?php else: ?>
									<span class="fa fa-user fa-5x"></span>
									<?php endif; ?>
								</td>
								<td><?php echo $row['apellidos'].', '.$row['nombre']; ?></td>
								<td><?php echo $row['usuario']; ?></td>
								<td><?php echo $row['pass']; ?></td>
							</tr>
							<?php endwhile; ?>
							<?php mysql_free_result($result); ?>
						</tbody>
					</table>
				</div>
				
				<div class="hidden-print">
					<a href="#" class="btn btn-primary" onclick="javascript:print();">Imprimir</a>
					<a href="perfiles_alumnos.php" class="btn btn-default">Volver</a>
				</div>
					
				
			</div><!-- /.col-sm-6 -->
			
		
		</div><!-- /.row -->
		
	</div><!-- /.container -->

<?php else: ?>

	<div class="container">
		
		<!-- TITULO DE LA PAGINA -->
		<div class="page-header">
			<h2>Centro TIC <small>Perfiles de alumnos</small></h2>
		</div>
		
		
		<!-- SCAFFOLDING -->
		<div class="row">
		
			<!-- COLUMNA IZQUIERDA -->
			<div class="col-sm-6 col-sm-offset-3">
				
				<div class="well">
					
					<form method="post" action="">
						<fieldset>
							<legend>Perfiles de alumnos</legend>
							
							<?php if(stristr($_SESSION['cargo'],'1') == TRUE): ?>
							<div class="form-group">
						    <label for="profe">Profesor</label>
						    <?php $result = mysql_query("SELECT DISTINCT PROFESOR FROM profesores ORDER BY PROFESOR ASC"); ?>
						    <?php if(mysql_num_rows($result)): ?>
						    <select class="form-control" id="profe" name="profe" onchange="submit()">
						    <option></option>
							    <?php while($row = mysql_fetch_array($result)): ?>
							    <option value="<?php echo $row['PROFESOR']; ?>" <?php echo (isset($profe) && $profe == $row['PROFESOR']) ? 'selected' : ''; ?>><?php echo $row['PROFESOR']; ?></option>
							    <?php endwhile; ?>
							    <?php mysql_free_result($result); ?>
							   </select>
							   <?php else: ?>
							   <select class="form-control" id="profe" name="profe" disabled>
							   	<option value=""></option>
							   </select>
							   <?php endif; ?>
						  </div>
						  <?php else: ?>
						  <?php $profe = $_SESSION['profi']; ?>
						  <?php endif; ?>
						  
						  <div class="form-group">
						    <label for="curso">Unidad (Asignatura)</label>
						    
						    <?php $result = mysql_query("SELECT DISTINCT GRUPO, MATERIA, NIVEL, codigo FROM profesores, asignaturas WHERE materia = nombre AND abrev NOT LIKE '%\_%' AND PROFESOR = '$profe' AND nivel = curso ORDER BY grupo ASC"); ?>
						    <?php if(mysql_num_rows($result)): ?>
						    <select class="form-control" id="curso" name="curso">
						      <?php while($row = mysql_fetch_array($result)): ?>
						      <?php $key = $row['GRUPO'].'-->'.$row['MATERIA'].'-->'.$row['NIVEL'].'-->'.$row['codigo']; ?>
						      <option value="<?php echo $key; ?>" <?php echo (isset($curso) && $curso == $key) ? 'selected' : ''; ?>><?php echo $row['GRUPO'].' ('.$row['MATERIA'].')'; ?></option>
						      <?php endwhile; ?>
						      <?php mysql_free_result($result); ?>
						     </select>
						     <?php else: ?>
						     <select class="form-control" id="profesor" name="profesor" disabled>
						     	<option value=""></option>
						     </select>
						     <?php endif; ?>
						  </div>
						  
						  <button type="submit" class="btn btn-primary" name="enviar">Consultar</button>
					  </fieldset>
					</form>
					
				</div><!-- /.well -->
				
			</div><!-- /.col-sm-6 -->
			
		
		</div><!-- /.row -->
		
	</div><!-- /.container -->
<?php endif; ?>
  
<?php include("../pie.php"); ?>

</body>
</html>
