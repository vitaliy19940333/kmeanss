<?=$data?>
<h1>Шаг 7. Оценка качества кластеризации:</h1>
<form id='form_count_cluster' method='post' action='/preparation/result'>
<table class='table table-striped table-bordered table-hover' style='text-align:left'>
		<tr>
			<td><input type='checkbox' name='qalityDistance' <?php if(@in_array('Distance',$_SESSION['qality'])) echo "checked='checked'"?> value='qalityDistance'>Сумарное расстояние</td>
		</tr>
		<tr>
			<td><input type='checkbox' name='qalityDana'  <?php if(@in_array('Dana',$_SESSION['qality'])) echo "checked='checked'"?> value='qalityDana'>Индекс Дана</td>
		</tr>
		<tr>
			<td><input type='checkbox' name='qalitySiluet'  <?php if(@in_array('Siluet',$_SESSION['qality'])) echo "checked='checked'"?> value='qalitySiluet'>Оценка Силуэта</td>
		</tr>
		<tr>
			<td><input type='checkbox' name='qalityVNND'   <?php if(@in_array('VNND',$_SESSION['qality'])) echo "checked='checked'"?>  value='qalityVNND'>Индекс VNND</td>
		</tr>
		<tr>
			<td><input type='checkbox' name='qalityMB'  <?php if(@in_array('MB',$_SESSION['qality'])) echo "checked='checked'"?>  value='qalityMB'>Индекс MB</td>
		</tr>
		<tr>
			<td><input type='checkbox' name='qalitySC'  <?php if(@in_array('SC',$_SESSION['qality'])) echo "checked='checked'"?>  value='qalitySC'>ScoreFunction</td>
		</tr>
		<tr>
			<td><input type='checkbox' name='qalitySDbw'  <?php if(@in_array('SDbw',$_SESSION['qality'])) echo "checked='checked'"?>value='qalitySDbw'>CDbw</td>
		</tr>
		<tr>
			<td><a href="/preparation/metrics" class='btn btn-primary pull-left'> <i class="fa fa-backward" aria-hidden="true"></i> Назад</a>
			<button class=' btn btn-primary pull-right' type='submit'> Вперед <i class="fa fa-forward" aria-hidden="true"></i></button></td>
		</tr>
</table>
</form>