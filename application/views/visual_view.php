<h1>Шаг 3. Визуализация данных по атрибутам (признакам)</h1>
<style>
.nav-tabs > li.active > a, .nav-tabs > li.active > a:hover, .nav-tabs > li.active > a:focus {
    color: #ffffff;
    background-color: #2c3e50;
    border: 1px solid #dddddd;
    border-bottom-color: transparent;
    cursor: default;
}
</style>
<style type="text/css">${demo.css}</style>
<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.8.2/jquery.min.js"></script>
<div>
  <!-- Nav tabs -->
  <ul class="nav nav-tabs" role="tablist">
    <li role="presentation" class="active"><a href="#home" aria-controls="home" role="tab" data-toggle="tab">Информация о данных</a></li>
    <li role="presentation"><a href="#profile" aria-controls="profile" role="tab" data-toggle="tab">Полигоны</a></li>
    <li role="presentation"><a href="#messages" aria-controls="messages" role="tab" data-toggle="tab">Графики</a></li>
  </ul>

  <!-- Tab panes -->
  <div class="tab-content">
    <div role="tabpanel" class="tab-pane active" id="home">
		<table class='table table-striped table-bordered table-hover'>
		<tr>
			<td>Признак</td>
			<td>Минимальное значение</td>
			<td>Максимальное значение</td>
			<td>Уникальных значений</td>
		</tr>
		<?php foreach($data['uniq_data'] as $k => $v):?>
			<tr>
				<td><?=$k?></td>
				<td><?=min($v)?></td>
				<td><?=max($v)?></td>
				<td><?=count($v)?></td>
			</tr>
		<?php endforeach;?>
		</table>
	</div>
    <div role="tabpanel" class="tab-pane" id="profile">
				<script type="text/javascript">
				$(function () {
					<? $kont = 50; foreach($data['polygon'] as $key => $value) 
						{	$i = 0;
							foreach($value as $k=>$v)
							{
								$ar[$key][$i++] = "'".$k."'";
							}
							
								$ar[$key] = implode(',',$ar[$key]);
							//echo $ar;
							//echo "<br>";
							
					?>
					Highcharts.chart('container<?=$kont++?>', {
						title: {
							text: '<?=$data['title_field'][$key]?>',
							x: -20 //center
						},
						subtitle: {
							text: 'Кластеризация',
							x: -20
						},
						xAxis: {
							categories: [<?=$ar[$key]?>]
						},
						yAxis: {
							title: {
								text: 'Mx'
							},
							plotLines: [{
								value: 0,
								width: 1,
								color: '#808080'
							}]
						},
						tooltip: {
							valueSuffix: ' Объектов'
						},
						legend: {
							layout: 'vertical',
							align: 'right',
							verticalAlign: 'middle',
							borderWidth: 0
						},
						series: [{
							name: null,
							data: [
							<?
							foreach($value as $k => $v){
								echo $v*1;
							
								echo ",";
							}
							?>
							]
						}
						]
					});
						<?}?>
				});
		</script>
		<?php for($k = 50; $k < $kont; $k++ ){?>
		<div id="container<?=$k?>" style="width:100%; height: 400px;max-width:1000px;margin: 0 auto"></div><hr></hr>
		<?php }?>
	</div>
    <div role="tabpanel" class="tab-pane" id="messages">
			<!-- Nav tabs -->
			  <ul class="nav nav-tabs" role="tablist" id='graphs'>
			  <?php $i = 1; foreach($data['data_for_graphss'] as $key => $value) :?>
				<li role="presentation" class=""><a href="#graph<?=$i?>" aria-controls="graph<?=$i++?>" role="tab" data-toggle="tab"><?=$key?></a></li>
			  <?php endforeach;?>
			  </ul>
		<?php $i = 1; foreach($data['data_for_graphss'] as $key => $value) :?>
		
		<script type="text/javascript">
			$(function () {
				Highcharts.chart('container<?=$i?>', {
					title: {
						text: '<?=""?>',
						x: -20 //center
					},
					subtitle: {
						text: '',
						x: -20
					},
					xAxis: {
						categories: [<?=$value['y']?>]
					},
					yAxis: {
						title: {
							text: 'Уникальность <?=$key?>'
						},
						plotLines: [{
							value: 0,
							width: 1,
							color: '#808080'
						}]
					},
					tooltip: {
						valueSuffix: ''
					},
					legend: {
						layout: 'vertical',
						align: 'right',
						verticalAlign: 'middle',
						borderWidth: 0
					},
					series: [{
						name: ' ',
						data: [<?=$value['x']?>]
					}]
				});
			});
		</script>
		
				<script type="text/javascript">
$(function () {
    Highcharts.chart('container<?=($i+1)?>', {
        chart: {
            type: 'column'
        },
        title: {
            text: '<?=$key?>'
        },
        subtitle: {
            text: ''
        },
        xAxis: {
            type: 'category',
            labels: {
                rotation: -45,
                style: {
                    fontSize: '13px',
                    fontFamily: 'Verdana, sans-serif'
                }
            }
        },
        yAxis: {
            min: 0,
            title: {
                text: 'Абсолютное отличие признаков (текущего и предыдущего)'
            }
        },
        legend: {
            enabled: false
        },
        tooltip: {
            pointFormat: 'Изменения на <b>{point.y:.1f} ед.</b>'
        },
        series: [{
            name: 'Population',
            data: [
                <?=$data['sec_graphs'][$key]['x']?>
            ],
            dataLabels: {
                enabled: true,
                rotation: -90,
                color: '#FFFFFF',
                align: 'right',
                format: '{point.y:.1f}', // one decimal
                y: 10, // 10 pixels down from the top
                style: {
                    fontSize: '13px',
                    fontFamily: 'Verdana, sans-serif'
                }
            }
        }]
    });
});
		</script>
		<script>
  $(function () {
    $('#graphs a:last').tab('show');
  })
</script>
		
<?php $i=$i+2; endforeach;?>	

 <div class="tab-content">
<?php $k = 1; for($j = 1; $j <= $i/2; $j++ ){?>
	<div role="tabpanel" class="tab-pane" id="graph<?=$j?>">
	<?php for($o = 1; $o <= 2; $o++ ){?>
		<div id="container<?=$k?>" style="width:100%; height: 400px;max-width:1000px;margin: 0 auto"></div><hr></hr>
	<?php $k++; }?>
	</div>
<?php }?>
  </div>
</div>
</div>
</div>

<table class='table'>
		<tr style='text-align:center'>
			<td><a href="/preparation/normalization" class='btn btn-primary'> <i class="fa fa-backward" aria-hidden="true"></i> Назад</a></td>
			<td><a href="/preparation/count" class='btn btn-primary'>Вперед <i class="fa fa-forward" aria-hidden="true"></a></td>
		</tr>
</table>
<script src="https://code.highcharts.com/highcharts.js"></script>
<script src="https://code.highcharts.com/modules/exporting.js"></script>
<script src="/js/vendor/jquery/jquery.min.js"></script>