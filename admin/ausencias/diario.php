<?php
require('../../bootstrap.php');

include("../../menu.php");
include("menu.php");
?>
<div class="container">
    <div class="row">
        <br />
        <div class="page-header">
            <h2>Ausencias del profesorado <small> Profesores ausentes hoy</small></h2>
        </div>
        <div class="col-md-12">
            <?php	
            $hoy = date('Y-m-d');

	        // Consulta de datos del alumno.
	        $result = mysqli_query($db_con, "select inicio, fin, tareas, id, profesor, horas from ausencias  where  date(inicio) <= '$hoy' and date(fin) >= '$hoy' order by inicio" );
	        echo '<br /><table class="table table-striped table-bordered" style="width:100%;">';	

	        $result_tramos = mysqli_query($db_con, "SELECT `hora`, `hora_inicio`, `hora_fin` FROM `tramos` WHERE `hora` <> 'R' AND `hora` <> 'Rn' ORDER BY `tramo` ASC");
	        $total_tramos = mysqli_num_rows($result_tramos);

	        echo "<thead>";
	        $n_cols = 0;
	        while ($row_tramos = mysqli_fetch_array($result_tramos)) {
	        	echo "<th>".$row_tramos['hora']."ª Hora<br>".substr($row_tramos['hora_inicio'], 0, 5)." - ".substr($row_tramos['hora_fin'], 0, 5)."</th>";
	        	$n_cols++;
	        }
	        echo "</thead><tbody>";

	        while($row = mysqli_fetch_array ( $result ))
            {
	            $profe_baja = $row[4];
	            $tar = $row[2];
	            $horas = $row[5];

	            echo "<tr><th colspan='".$n_cols."' style='text-align:center'>";
		        echo "$profe_baja";
		        echo "</th></tr><tr>";
	            $ndia = date ( "w" );

	            $result_tramos = mysqli_query($db_con, "SELECT `hora`, `hora_inicio`, `hora_fin` FROM `tramos` WHERE `hora` <> 'R' AND `hora` <> 'Rn' ORDER BY `tramo` ASC");
	            $total_tramos = mysqli_num_rows($result_tramos);

	            while ($row_tramos = mysqli_fetch_array($result_tramos))
                {
	                echo "<td>";
	                if ($horas == 0 or mb_strstr($horas, $row_tramos['hora']))
                    {	
	                    $hor = mysqli_query($db_con, "select a_asig, a_grupo, a_aula, c_asig from horw where prof = '$profe_baja' and dia = '$ndia' and hora = '".$row_tramos['hora']."'");
	                    //echo "select a_asig, a_grupo, a_aula from horw where prof = '$profe_baja' and dia = '$ndia' and hora = '$i'<br>";
	                    $hor_asig = mysqli_fetch_array($hor);
	                    if (mysqli_num_rows($hor) > '0')
                        {
	                        echo "<p class='text-info'>Horario: $hor_asig[0]</p>";
	                        if (strlen($hor_asig[1]) > '1' and $hor_asig[3] != "25")
                            {
		                        $hor2 = mysqli_query($db_con, "select a_grupo from horw where prof = '$profe_baja' and dia = '$ndia' and hora = '".$row_tramos['hora']."'");
		                        echo "<p class='text-success'>Grupos: ";
	                            while($hor_bj = mysqli_fetch_array($hor2))
	                                echo $hor_bj[0]." ";
			                    echo "</p>";
	                        }
	                        if (strlen($hor_asig[2] > '1'))
	                            echo "<p class='text-warning'>Aula: <span style='font-weight:normal;'>$hor_asig[2]</p>";
	                    }
	                }
	                echo "</td>";
	            }
	            echo "</tr>";
            }   
            echo "</table>";
            echo "<br /><legend>Tareas para los Alumnos</legend>";
            $result2 = mysqli_query($db_con, "select inicio, fin, tareas, id, profesor, horas, archivo from ausencias  where date(inicio) <= '$hoy' and date(fin) >= '$hoy' order by inicio" );
	        while($row2 = mysqli_fetch_array ( $result2 ))
            {
	            $profe_baja=$row2[4];
	            $tar = $row2[2];
	            if (strlen($tar) > '1')
                {
	                echo '<table class="table table-striped table-bordered">';	
	                echo "<tr><th class='text-center'>$profe_baja</th></tr><tr><td>$tar</td></tr>";
	                if (strlen($row2[6]) > 0)
		                echo "<tr class='info'><td>Archivo adjunto:&nbsp; <a href='archivos/$row2[6]'><i class='far fa-file'> </i> $row2[6]</a></td></tr>";
	
                    echo "</table><br />";
	            }
	        }
            ?>
        </div>
    </div>
</div>

<?php include("../../pie.php"); ?>

