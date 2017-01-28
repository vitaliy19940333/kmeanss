<h1>Результат кластеризации</h1>
<style>
.nav-tabs > li.active > a, .nav-tabs > li.active > a:hover, .nav-tabs > li.active > a:focus {
    color: #ffffff;
    background-color: #2c3e50;
    border: 1px solid #dddddd;
    border-bottom-color: transparent;
    cursor: default;
}
</style>
<div >
  <!-- Nav tabs -->
  <ul class="nav nav-tabs" role="tablist" style='padding-left:50px'>
    <li role="presentation" class="active"><a href="#home" aria-controls="home" role="tab" data-toggle="tab">3D Модель</a></li>
    <li role="presentation"><a href="#profile" aria-controls="profile" role="tab" data-toggle="tab">Данные кластеров</a></li>
    <li role="presentation"><a href="#messages" aria-controls="messages" role="tab" data-toggle="tab">Центроиды</a></li>
	 <li role="presentation"><a href="#quality" aria-controls="quality" role="tab" data-toggle="tab">Оценка качества</a></li>
  </ul>

  <!-- Tab panes -->
  <div class="tab-content" style='overflow: auto;'>
    <div role="tabpanel" class="tab-pane active" id="home">

		<?php $min_max_data = $data['min_max'];?> 
		
		<script type="text/javascript">
			$(function () {

				// Give the points a 3D feel by adding a radial gradient
				Highcharts.getOptions().colors = $.map(Highcharts.getOptions().colors, function (color,i) {
				//alert(color);
					return {
						radialGradient: {
							cx: 0.4,
							cy: 0.3,
							r: 0.5
						},
						stops: [
							[0, color],
							[1, Highcharts.Color(color).brighten(-0.2).get('rgb')]
						]
					};
				});

				// Set up the chart
				var chart = new Highcharts.Chart({
					chart: {
						renderTo: 'container',
						margin: 100,
						type: 'scatter',
						options3d: {
							enabled: true,
							alpha: 10,
							beta: 30,
							depth: 600,
							viewDistance: 5,

							frame: {
								bottom: { size: 1, color: 'rgba(0,0,0,0.02)' },
								back: { size: 1, color: 'rgba(0,0,0,0.04)' },
								side: { size: 1, color: 'rgba(0,0,0,0.06)' }
							}
						}
					},
					title: {
						text: '3D модель '
					},
					subtitle: {
						text: 'Зажмите  кнопку миши и вращайте'
					},
					plotOptions: {
						scatter: {
							width: 10,
							height: 10,
							depth: 10
						}
					},
					yAxis: {
						title: {
							text: '<?=$title[1]?>'
						},
						min: <?=$min_max_data[1]['min']?>,
						max: <?=$min_max_data[1]['max']?>,
						title: null
					},
					xAxis: {
						title: {
							text: '<?=$title[0]?>'
						},
						 min: <?=$min_max_data[0]['min']?>,
						max: <?=$min_max_data[0]['max']?>,
						gridLineWidth: 1
					},
					zAxis: {
						title: {
							text: '<?=$title[2]?>'
						},
						 min: <?if(!empty($min_max_data[2]['min'])) echo $min_max_data[2]['min'];else echo  1;?>,
						max: <? if(!empty($min_max_data[2]['max'])) echo $min_max_data[2]['max'];else echo  1; ?>,
						showFirstLabel: false
					},
					legend: {
						enabled: false
					},
					series: [
					
					<?php
					$clusteras = $data['clusters'];
					$scet = 0;$realt = 0;
					foreach($clusteras as $key => $value)
					{
						echo "{";
							if((count($clusteras)-1) == $key)
								echo "name:' Центр кластера".($key+1)."',";
							else
								echo "name:' Кластер".($key+1)."',";
							if((count($clusteras)-1) == $key)
								echo " colorByPoint: false,";
							else
								echo " colorByPoint: false,";
							echo "data:[";
							foreach($value as $K=>$v)
							{
								if(empty($v[2]))
									$rr = 1;else $rr = ($v[2]*1);
								if(empty($v[1]))
								{
									$v[1] = 1;
									$rr = $realt++;
								}
									
								if(empty($v[0]))
									$v[0] = $realts++;
									
								echo "[";
								echo (($v[0]*1)).",". (($v[1]*1)).",". $rr;
								echo "]";
								echo ",";
							}
							echo "]";
						echo "}";
						$scet ++;
						if($scet != count($clusteras))
						echo ",";
					}
					?>
				
					 
					 ]
				}
				);



				// Add mouse events for rotation
				$(chart.container).bind('mousedown.hc touchstart.hc', function (eStart) {
					eStart = chart.pointer.normalize(eStart);

					var posX = eStart.pageX,
						posY = eStart.pageY,
						alpha = chart.options.chart.options3d.alpha,
						beta = chart.options.chart.options3d.beta,
						newAlpha,
						newBeta,
						sensitivity = 5; // lower is more sensitive

					$(document).bind({
						'mousemove.hc touchdrag.hc': function (e) {
							// Run beta
							newBeta = beta + (posX - e.pageX) / sensitivity;
							chart.options.chart.options3d.beta = newBeta;

							// Run alpha
							newAlpha = alpha + (e.pageY - posY) / sensitivity;
							chart.options.chart.options3d.alpha = newAlpha;

							chart.redraw(false);
						},
						'mouseup touchend': function () {
							$(document).unbind('.hc');
						}
					});
				});

			});
		</script>
		

		<div id="container" style="height: 700px"></div>
	</div>
	
	
    <div role="tabpanel" class="tab-pane" id="profile" >
		<table class='table table-striped table-bordered table-hover' style='text-align:center;'>
			<tr>
			<?php foreach($data['clusters'] as $i => $vvv):?>
				<td align='center' class='title'>Кл.  <?=($i+1)?></td>
			<?php endforeach?>
			</tr>
			<tr>
			<? foreach($data['clusters'] as $key_cluster => $datas):?>
				<td>
					<table class='table table-striped table-bordered table-hover' style='text-align:center'>
						<tr>
							<td>
								<span>Инф</span>
							</td>
							<?php for($m = 0; $m < $data['count_attr'];$m++):?>
							<td>
								<?=$data['title'][$m]?>
							</td>
							<?php endfor?>
						</tr>
						<?php foreach($datas as $key_data => $attributes):?>
						<tr>
							<td><?=$data['info'][$key_data]?></td>
							<?php foreach($attributes as $key_attr => $value_attr):?>
							<td>
								<?=$value_attr?>
							</td>
							<?php endforeach?>
						</tr>
						<?php endforeach?>
					</table>
				</td>
				<?php endforeach?>
			</tr>
		</table>
	</div>
    <div role="tabpanel" class="tab-pane" id="messages">
	
	
	
			<script type="text/javascript">
$(function () {
    Highcharts.chart('container111', {
        chart: {
            type: 'column'
        },
        title: {
            text: 'Гистограммы центроидов'
        },
        subtitle: {
            text: '  '
        },
        xAxis: {
            categories: [
               <?php $i = 0;foreach($data['gr'] as $key => $value):?>
					 <?php foreach($value as $k => $v):?>
						'Кластер <?=($k+1)?>',
					 <?php endforeach;break;?>
					 
			   <?php endforeach?>
                
            ],
            crosshair: true
        },
        yAxis: {
            min: -1,
            title: {
                text: '  '
            }
        },
        tooltip: {
            headerFormat: '<span style="font-size:10px">{point.key}</span><table>',
            pointFormat: '<tr><td style="color:{series.color};padding:0">{series.name}: </td>' +
                '<td style="padding:0"><b>{point.y:.1f} </b></td></tr>',
            footerFormat: '</table>',
            shared: true,
            useHTML: true
        },
        plotOptions: {
            column: {
                pointPadding: 0.2,
                borderWidth: 2
            }
        },
		
		
        series: [
			<?php foreach($data['gr'] as $key => $value):?>
			{
				name:'<?=$key?>',
				data:[<?=implode(",",array_map('her',$value))?>]
			},
			<?php endforeach?>
		]
    });
});
		</script>
		<?php
		function her($value){
		return round($value,2);}
		?>
	<div id="container111" style="min-width: 1050px; height: 700px; margin: 0 auto"></div>
	
	
	
	
	
	
	
	
	
  </div>
  <div role="tabpanel" class="tab-pane" id="quality">
	<table class='table table-striped table-bordered table-hover' style='text-align:left;max-width:350px;'>
		<tr>
			<td>Суммарное расстояние</td>
			<td><?=(round($data['general_summ'],7)) ?></td>
		</tr>
		<?php foreach($data['index'] as $key => $value):?>
			<tr>
				<td><?=$value[1]?></td>
				<td><?=(round($value[0],7)*100)?></td>
			</tr>
		<?php endforeach;?>
	</table>
  </div>
	</div>
</div>
<script src="/js/highcharts.js"></script>
<script src="/js//highcharts-3d.js"></script>
<script src="/js/modules/exporting.js"></script>
