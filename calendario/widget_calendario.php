<?php
if (! defined('MOD_CALENDARIO')) die ('<h3>FORBIDDEN</h3>');

// CALENDARIO
$dia_actual = date('d');
$mes_actual = date('n');
$anio_actual = date('Y');

$dia  = isset($_GET['dia'])  ? $_GET['dia']  : date('d');
$mes  = isset($_GET['mes'])  ? $_GET['mes']  : date('n');
$anio = isset($_GET['anio']) ? $_GET['anio'] : date('Y');

$semana = 1;

for ($i = 1; $i <= date('t', strtotime($anio.'-'.$mes)); $i++) {
	
	$dia_semana = date('N', strtotime($anio.'-'.$mes.'-'.$i));
	
	$calendario[$semana][$dia_semana] = $i;
	if ($dia_semana == 7) $semana++;
	
}


// HTML CALENDARIO MENSUAL
function vista_mes ($calendario, $dia, $mes, $anio) {

	// NAVEGACION
	$mes_ant = $mes - 1;
	$anio_ant = $anio;
	
	if ($mes == 1) {
		$mes_ant = 12;
		$anio_ant = $anio - 1;
	}
	
	
	$mes_sig = $mes + 1;
	$anio_sig = $anio;
	
	if ($mes == 12) {
		$mes_sig = 1;
		$anio_sig = $anio + 1;
	}
	
	// Correcci�n en mes
	($mes < 10) ? $mes = '0'.$mes : $mes = $mes;
	
	echo '<div class"table-responsive">';
	echo '<table id="calendar" class="table table-bordered table-condensed">';
	echo '	<thead>';
	echo '      <tr>';
	echo '			<th class="text-center"><h4><a href="?mes='.$mes_ant.'&anio='.$anio_ant.'"><span class="fa fa-chevron-left fa-fw"></span></a></h4></th>';
	echo '			<th colspan="5" class="text-center"><h4><span class="fa fa-calendar-o fa-fw"></span> '.strftime('%B, %Y', strtotime($anio.'-'.$mes)).'</h4></th>';
	echo '			<th class="text-center"><h4><a href="?mes='.$mes_sig.'&anio='.$anio_sig.'"><span class="fa fa-chevron-right fa-fw"></span></a></h4></th>';
	echo '		</tr>';
	echo '		<tr>';
	echo '			<th class="text-center">L</th>';
	echo '			<th class="text-center">M</th>';
	echo '			<th class="text-center">X</th>';
	echo '			<th class="text-center">J</th>';
	echo '			<th class="text-center">V</th>';
	echo '			<th class="text-center">S</th>';
	echo '			<th class="text-center">D</th>';
	echo '		</tr>';
	echo '	</thead>';
	echo '	<tbody>';
	
	foreach ($calendario as $dias) {
		echo '		<tr>';
	
		for ($i = 1; $i <= 7; $i++) {
			
			if ($i > 5) {
				if (isset($dias[$i]) && ($mes == date('m')) && ($dias[$i] == date('d'))) {
					echo '			<td class="text-muted today" width="14.28%">';
				}
				else {
					echo '			<td class="text-muted" width="14.28%">';
				}
			}
			else {
				if (isset($dias[$i]) && ($mes == date('m')) && ($dias[$i] == date('d'))) {
					echo '			<td style="background-color: #ecf0f1; font-weight: bold;" width="14.28%">';
				}
				else {
					echo '			<td width="14.28%">';
				}
			}
			
			if (isset($dias[$i])) {

				echo '				<p class="text-right" style="margin-bottom: 2px;">'.$dias[$i].'</p>';
				
				// Correcci�n en d�a
				($dias[$i] < 10) ? $dia0 = '0'.$dias[$i] : $dia0 = $dias[$i];
				
				
				// Consultamos los calendarios privados
				$result_calendarios = mysqli_query($GLOBALS['db_con'], "SELECT id, nombre, color FROM calendario_categorias WHERE profesor='".$_SESSION['ide']."' AND espublico=0");
				while ($calendario = mysqli_fetch_assoc($result_calendarios)) {
					$result_eventos = mysqli_query($GLOBALS['db_con'], "SELECT id, nombre, descripcion, fechaini, fechafin FROM calendario WHERE categoria='".$calendario['id']."' AND YEAR(fechaini)='$anio' AND MONTH(fechaini)='$mes'");
					
					while ($eventos = mysqli_fetch_assoc($result_eventos)) {
						if ($anio.'-'.$mes.'-'.$dia0 >= $eventos['fechaini'] && $anio.'-'.$mes.'-'.$dia0 <= $eventos['fechafin']) {
							echo '<small style="margin-right: 2px;"><span class="fa fa-circle" style="color: '.$calendario['color'].';" data-bs="tooltip" title="'.$calendario['nombre'].' - '.$eventos['nombre'].'"></span></small>';
						}
					}
					mysqli_free_result($result_eventos);
				}
				mysqli_free_result($result_calendarios);
				
				// Consultamos los calendarios p�blicos
				$result_calendarios = mysqli_query($GLOBALS['db_con'], "SELECT id, nombre, color FROM calendario_categorias WHERE espublico=1");
				while ($calendario = mysqli_fetch_assoc($result_calendarios)) {
					
					$result_eventos = mysqli_query($GLOBALS['db_con'], "SELECT id, nombre, descripcion, fechaini, fechafin FROM calendario WHERE categoria='".$calendario['id']."'");
					
					while ($eventos = mysqli_fetch_assoc($result_eventos)) {
						if ($anio.'-'.$mes.'-'.$dia0 >= $eventos['fechaini'] && $anio.'-'.$mes.'-'.$dia0 <= $eventos['fechafin']) {
							echo '<small style="margin-right: 2px;"><span class="fa fa-circle" style="color: '.$calendario['color'].';" data-bs="tooltip" title="'.$calendario['nombre'].' - '.$eventos['nombre'].'"></span></small>';
						}
					}
					mysqli_free_result($result_eventos);
				}
				mysqli_free_result($result_calendarios);
				
				// FESTIVOS
				$result = mysqli_query($GLOBALS['db_con'], "SELECT fecha, nombre FROM festivos");
				while ($festivo = mysqli_fetch_assoc($result)) {
					
					if ($festivo['fecha'] == $anio.'-'.$mes.'-'.$dia0) {
						echo '<small style="margin-right: 2px;"><span class="fa fa-circle" style="color: #e14939;" data-bs="tooltip" title="Festivo - '.$festivo['nombre'].'"></span></small>';
					}
				}
				mysqli_free_result($result);
				unset($festivo);
				
				
			}
			else {
				echo '&nbsp;';
			}
			
			echo '			</td>';
		}
		echo '		</tr>';
	}
	
	echo '	</tbody>';
	echo '</table>';
	echo '</div>';
}

vista_mes($calendario, $dia, $mes, $anio);

echo '<h4><span class="fa fa-tasks fa-fw"></span> Programado para hoy...</h4>';
echo '<div class="list-group">';

// Consultamos los calendarios privados
$result_calendarios = mysqli_query($db_con, "SELECT id, nombre, color FROM calendario_categorias WHERE profesor='".$_SESSION['ide']."' AND espublico=0");

if (mysqli_num_rows($result_calendarios)) {
	
	while ($calendario = mysqli_fetch_assoc($result_calendarios)) {
		$result_eventos = mysqli_query($db_con, "SELECT id, nombre, descripcion, fechaini, fechafin, horaini, horafin FROM calendario WHERE categoria='".$calendario['id']."' AND YEAR(fechaini)='$anio_actual' AND MONTH(fechaini)='$mes_actual' AND DAY(fechaini)='$dia_actual'");
		
		while ($eventos = mysqli_fetch_assoc($result_eventos)) {
			if ($eventos['fechaini'] == $eventos['fechafin'] && $eventos['horaini'] != $eventos['horafin']) $hora_evento = substr($eventos['horaini'], 0, -3).' - '.substr($eventos['horafin'], 0, -3);
			elseif ($eventos['fechaini'] != $eventos['fechafin'] && date('Y-m-d') >= $eventos['fechaini'] && date('Y-m-d') < $eventos['fechafin']) $hora_evento = "Todo el d�a";
			elseif ($eventos['fechaini'] != $eventos['fechafin'] && date('Y-m-d') == $eventos['fechafin']) echo $hora_evento = "Hasta las ".substr($eventos['horafin'], 0, -3);
			
			echo '<a href="//'.$dominio.'/intranet/calendario/index.php?viewModal='.$eventos['id'].'" class="list-group-item"><span class="pull-right badge">'.$hora_evento.'</span><span class="fa fa-circle" style="color: '.$calendario['color'].';" data-bs="tooltip" title="'.$calendario['nombre'].'"></span>&nbsp;'.$eventos['nombre'].'</a>';
		}
		mysqli_free_result($result_eventos);
	}
	mysqli_free_result($result_calendarios);
}

// Consultamos los calendarios p�blicos
$result_calendarios = mysqli_query($db_con, "SELECT id, nombre, color FROM calendario_categorias WHERE espublico=1");

while ($calendario = mysqli_fetch_assoc($result_calendarios)) {
	
	$result_eventos = mysqli_query($db_con, "SELECT id, nombre, descripcion, fechaini, fechafin, horaini, horafin FROM calendario WHERE categoria='".$calendario['id']."' AND YEAR(fechaini)='$anio_actual' AND MONTH(fechaini)='$mes_actual' AND DAY(fechaini)='$dia_actual'");
	
	while ($eventos = mysqli_fetch_assoc($result_eventos)) {
		if ($eventos['fechaini'] == $eventos['fechafin'] && $eventos['horaini'] != $eventos['horafin']) $hora_evento = substr($eventos['horaini'], 0, -3).' - '.substr($eventos['horafin'], 0, -3);
		elseif ($eventos['fechaini'] != $eventos['fechafin'] && date('Y-m-d') >= $eventos['fechaini'] && date('Y-m-d') < $eventos['fechafin']) $hora_evento = "Todo el d�a";
		elseif ($eventos['fechaini'] != $eventos['fechafin'] && date('Y-m-d') == $eventos['fechafin']) echo $hora_evento = "Hasta las ".substr($eventos['horafin'], 0, -3);
		
		echo '<a href="//'.$dominio.'/intranet/calendario/index.php?viewModal='.$eventos['id'].'" class="list-group-item"><span class="pull-right badge">'.$hora_evento.'</span><span class="fa fa-circle" style="color: '.$calendario['color'].';" data-bs="tooltip" title="'.$calendario['nombre'].'"></span>&nbsp;'.$eventos['nombre'].'</a>';
	}
	mysqli_free_result($result_eventos);
}
mysqli_free_result($result_calendarios);

// FESTIVOS
$result = mysqli_query($db_con, "SELECT fecha, nombre FROM festivos AND YEAR(fecha)='$anio_actual' AND MONTH(fecha)='$mes_actual' AND DAY(fecha)='$dia_actual'");
while ($festivo = mysqli_fetch_assoc($result)) {
	echo '<a href="#" class="list-group-item"><span class="pull-right badge">Todo el d�a</span><span class="fa fa-circle" style="color: #e14939;" data-bs="tooltip" title="'.$festivo['nombre'].'"></span></a>';
}
mysqli_free_result($result);
unset($festivo);

echo '</div>';

echo '<a href="//'.$dominio.'/intranet/calendario/index.php" class="btn btn-default btn-sm">Ver calendario</a>';

?>
