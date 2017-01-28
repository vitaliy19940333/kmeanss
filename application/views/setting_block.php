					<h1><i class="fa fa-cog" aria-hidden="true"></i> Настройки</h1>
					<form id='setting' method='POST' action='/preparation/saveSetting' style='height:430px;overflow: auto;'>
						<p>Методы нормализации</p>
						<ul>
							<?php $i = 0; foreach($data['normalization'] as $key => $value):?>
								<li><input type='radio' name='method_normal' value="<?=$value['val']?>" <?php if($_SESSION['method_normal'] == $value['val']) echo "checked='checked'";?>> <?=$value['title']?></li>
							<?php endforeach;?>
						</ul>
						<p>Определение количества кластеров</p>
						<ul id='sels'>
							<li><input type='radio' name='method_count_cluster' id="hands" <?php if($_SESSION['count_method'] == 'hands') echo "checked=checked"?> value='hands'>Ручной Ввод 
							<input type='number' name='count_cluster_hands'id="val_hands"  class="form-control" disabled="disabled" value='<?=$_SESSION['count_cluster']?>'></li>
							<li><input type='radio' name='method_count_cluster' id="auto" value='auto' <?php if($_SESSION['count_method'] == 'auto') echo "checked=checked"?>> Определить количествв кластеров автоматически</li>

						</ul>
						<p>Метод определения положений начальных центроидов</p>
						<ul>
								<li><input type='radio' name='method_polog_cluster' <?php if($_SESSION['method_polog_cluster'] == 'random') echo "checked='checked'"?> value='random'>Случайный</li>
								<li><input type='radio' name='method_polog_cluster' <?php if($_SESSION['method_polog_cluster'] == 'maxDistance') echo "checked='checked'"?>  value='maxDistance'>По максимальному расстоянию</li>
								<li><input type='radio' name='method_polog_cluster'  <?php if($_SESSION['method_polog_cluster'] == 'summCoord') echo "checked='checked'"?> id='summ_c'  value='summCoord'>Суммирование координат</li>
							<?php if($_SESSION['count_method'] != 'hands'):?>
								<li><input type='radio' name='method_polog_cluster' <?php if($_SESSION['method_polog_cluster'] == 'AutoToPolygom') echo "checked='checked'"?>  disabled="disabled"  value='AutoToPolygom'>Автоматический по полигонам</li>
								<li><input type='radio' name='method_polog_cluster' <?php if($_SESSION['method_polog_cluster'] == 'AutoToGraphs') echo "checked='checked'"?> id='sel_auto' value='AutoToGraphs'>Автоматический по графикам</li>
							<?php endif?>
						</ul>
						<p>Выбор метрики</p>
						<ul>
								<li><input type='radio' name='metrics' <?php if($_SESSION['metrics'] == 'evklid') echo "checked='checked'"?> value='evklid'>Евклидово расстояние</li>
								<li><input type='radio' name='metrics' <?php if($_SESSION['metrics'] == 'sq_evklid') echo "checked='checked'"?>  value='sq_evklid'>Квадрат Евклидового расстояния</li>
								<li><input type='radio' name='metrics'  <?php if($_SESSION['metrics'] == 'sec_evklid') echo "checked='checked'"?> disabled="disabled" value='sec_evklid'>"Взвешенное" Евклидово рассто</li>
								<li><input type='radio' name='metrics' <?php if($_SESSION['metrics'] == 'kvartal') echo "checked='checked'"?>  value='kvartal'>Расстояние городских кварталов или манхэттенское расстояние</li>
								<li><input type='radio' name='metrics' <?php if($_SESSION['metrics'] == 'chebiwev') echo "checked='checked'"?> value='chebiwev'>Расстояние Чебышева</li>
								<li><input type='radio' name='metrics' <?php if($_SESSION['metrics'] == 'stepennoe') echo "checked='checked'"?>disabled="disabled" value='stepennoe'>Степенное расстояние</li>
						</ul>
						<p>Оценка качества кластеризации</p>
						<ul>
									<li><input type='checkbox' name='qalityDistance' <?php if(@in_array('Distance',$_SESSION['qality'])) echo "checked='checked'"?> value='qalityDistance'>Сумарное расстояние</td>
									<li><input type='checkbox' name='qalityDana'  <?php if(@in_array('Dana',$_SESSION['qality'])) echo "checked='checked'"?> value='qalityDana'>Индекс Дана</li>
									<li><input type='checkbox' name='qalitySiluet'  <?php if(@in_array('Siluet',$_SESSION['qality'])) echo "checked='checked'"?> value='qalitySiluet'>Оценка Силуэта</li>
									<li><input type='checkbox' name='qalityVNND'   <?php if(@in_array('VNND',$_SESSION['qality'])) echo "checked='checked'"?>  value='qalityVNND'>Индекс VNND</li>
									<li><input type='checkbox' name='qalityMB'  <?php if(@in_array('MB',$_SESSION['qality'])) echo "checked='checked'"?>  value='qalityMB'>Индекс MB</li>
									<li><input type='checkbox' name='qalitySC'  <?php if(@in_array('SC',$_SESSION['qality'])) echo "checked='checked'"?>  value='qalitySC'>ScoreFunction</li>
									<li><input type='checkbox' name='qalitySDbw'  <?php if(@in_array('SDbw',$_SESSION['qality'])) echo "checked='checked'"?>value='qalitySDbw'>CDbw</li>
						</ul>
						<button type='submit' class='btn btn-danger pull-right'>Применить</button>
					</form>
<script>
$(function (){

    $('#sels').click(function (){

     if(!$("#hands").prop("checked"))
	 {
		 $('#val_hands').attr('disabled',"disabled");
		 $("#sel_auto").removeAttr('disabled');
		
	 }
		
	else{
		 if($("#sel_auto").prop("checked"))
			$('#summ_c').attr('checked','checked');
		$("#sel_auto").attr('disabled',"disabled");
        $('#val_hands').removeAttr('disabled');
	}

   });

});
</script>
<style>
.error
{
	color:red;
}
</style>