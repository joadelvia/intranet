<?php
require('../../bootstrap.php');
include("../../menu.php");
include("menu.php");

?>
<script src="../../js/ChartJS/Chart.min.js"></script>

<script type="text/javascript">
window.chartColors = {
    red: 'rgb(244, 67, 54)',
    pink: 'rgb(255, 64, 129)',
    purple: 'rgb(156, 39, 176)',
    deeppurple: 'rgb(124, 77, 255)',
    indigo: 'rgb(63, 81, 181)',
    blue: 'rgb(68, 138, 255)',
    lightblue: 'rgb(3, 169, 244)',
    cyan: 'rgb(0, 188, 212)',
    teal: 'rgb(0, 150, 136)',
    green: 'rgb(76, 175, 80)',
    lightgreen: 'rgb(139, 195, 74)',
    lime: 'rgb(205, 220, 57)',
    yellow: 'rgb(255, 235, 59)',
    amber: 'rgb(255, 193, 7)',
    orange: 'rgb(255, 152, 0)',
    deeporange: 'rgb(255, 87, 34)',
    brown: 'rgb(121, 85, 72)',
    grey: 'rgb(158, 158, 158)',
    bluegrey: 'rgb(96, 125, 139)'
};
</script>

<div class="chart-container">

	<div class="page-header">
		<h2 style="display: inline;">Ausencias del profesorado <small> Informe sobre las Ausencias</small></h2>
	</div>

	<div class="row">
		<br>
		<div class="well" style="width:750px;margin:auto">
			<p class="block-help">
			<h4 class=" text-info" align="center">Información sobre los datos.</h4>
				Al lado del nombre del profesor, entre paréntesis, aparece el número de días que ha estado ausente (días completos + días con horas sueltas). Los días de ausencia pueden ser completos (<span style="color:red"><b>color rojo</b></span>) o días con horas sueltas (<span style="color:blue"><b>color azul</b></span>). En <span style="color:yellow"><b>amarillo</b></span> se muestran las horas totales que el profesor se ha ausentado <em>en los días con horas sueltas</em>.
			</p>
		</div>
		<br />

		<div class="col-sm-12">

		<br>

		<?php $result = mysqli_query($db_con, "SELECT distinct profesor, count(*) as numero FROM ausencias group by profesor order by profesor ASC"); ?>
		<?php 

		$chart_n = 0;

		while ($total = mysqli_fetch_array($result)){
			$profe = $total[0];
			$num_total = $total[1];
			$num_horas = "";
			$horas = "";
			$dias = "";
		?>
			<?php $result1 = mysqli_query($db_con, "SELECT horas FROM ausencias where profesor = '$profe' and (horas = '0' or horas = '123456')");
				$dias = mysqli_num_rows($result1);
				if($dias==0) $dias="";
			?>

			<?php $result2 = mysqli_query($db_con, "SELECT horas FROM ausencias where profesor = '$profe' and horas not like '0' and horas not like '123456'");
				$horas = mysqli_num_rows($result2);
				if($horas==0) $horas="";
				while ($sueltas = mysqli_fetch_array($result2)) {
					$num_horas+=strlen($sueltas[0]);
				}
			?>

			<?php $profe_a.='"'.nomprofesor($profe).' ('.$num_total.')",'; ?>
			<?php $num_total_a.='"'.$num_total.'",'; ?>
			<?php $dias_a.='"'.$dias.'",'; ?>
			<?php $horas_a.='"'.$horas.'",'; ?>
			<?php $num_horas_a.='"'.$num_horas.'",'; ?>

		<?php } ?>

<?php $chart_n++; ?>
<canvas id="chart_<?php echo $chart_n; ?>" width="200" height="460"></canvas>
<script>
    var ctx = document.getElementById('chart_<?php echo $chart_n; ?>').getContext('2d');
    var myChart = new Chart(ctx, {
      type: 'horizontalBar',
      data: {
    labels: [<?php echo $profe_a; ?>],
    datasets: [{
      backgroundColor: window.chartColors.red,
      label: 'Días completos',
      stack: 'Stack 0',
      data: [
        <?php echo $dias_a; ?>
      ]
    }, {
      label: 'Días con horas',
      backgroundColor: window.chartColors.blue,
      stack: 'Stack 0',
      data: [
        <?php echo $horas_a; ?>
      ]
    }, {
      label: 'Nº de horas',
      backgroundColor: window.chartColors.yellow,
      stack: 'Stack 0',
      data: [
        <?php echo $num_horas_a; ?>
      ]
    }]
  },
      options: {
        tooltips: {
          mode: 'index',
          intersect: false
        },
        responsive: true,
        scales: {
          xAxes: [{
            stacked: true,
            position: 'top'
          }],
          yAxes: [{
            stacked: true
          }]
        }
      }
    });

</script>

		</div><!-- /.col-sm-6 -->

	</div><!-- /.row -->

</div><!-- /.container -->

	<?php include("../../pie.php"); ?>

</body>
</html>
