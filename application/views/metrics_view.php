<?=$data?>
<h1>Шаг 6. Выбор метода рассчета расстояний между объектами:</h1>
<form id='form_count_cluster' method='post' action='/preparation/qality '>
<table class='table table-striped table-bordered table-hover' style='text-align:left'>
		<tr>
			<td><input type='radio' name='metrics' <?php if($_SESSION['metrics'] == 'evklid') echo "checked='checked'"?> value='evklid'>Евклидово расстояние</td>
		</tr>
		<tr>
			<td><input type='radio' name='metrics' <?php if($_SESSION['metrics'] == 'sq_evklid') echo "checked='checked'"?>  value='sq_evklid'>Квадрат Евклидового расстояния</td>
		</tr>
		<tr>
			<td><input type='radio' name='metrics'  <?php if($_SESSION['metrics'] == 'sec_evklid') echo "checked='checked'"?> disabled="disabled" value='sec_evklid'>"Взвешенное" Евклидово рассто</td>
		</tr>
		<tr>
			<td><input type='radio' name='metrics' <?php if($_SESSION['metrics'] == 'kvartal') echo "checked='checked'"?>  value='kvartal'>Расстояние городских кварталов или манхэттенское расстояние</td>
		</tr>
		<tr>
			<td><input type='radio' name='metrics' <?php if($_SESSION['metrics'] == 'chebiwev') echo "checked='checked'"?> value='chebiwev'>Расстояние Чебышева</td>
		</tr>
		<tr>
			<td><input type='radio' name='metrics' <?php if($_SESSION['metrics'] == 'stepennoe') echo "checked='checked'"?>disabled="disabled" value='stepennoe'>Степенное расстояние</td>
		</tr>
		<tr>
			<td><a href="/preparation/centroid" class='btn btn-primary pull-left'> <i class="fa fa-backward" aria-hidden="true"></i> Назад</a>
			<button class=' btn btn-primary pull-right' type='submit'> Вперед <i class="fa fa-forward" aria-hidden="true"></i></button></td>
		</tr>
</table>
</form>