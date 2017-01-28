<?=$data?>
<h1>Шаг 5. Метод определения положений начальных центроидов:</h1>
<form id='form_count_cluster' method='post' action='/preparation/metrics'>
<table class='table table-striped table-bordered table-hover' style='text-align:left'>
		<tr>
			<td><input type='radio' name='method_polog_cluster' <?php if($_SESSION['method_polog_cluster'] == 'random') echo "checked='checked'"?> value='random'>Случайный</td>
		</tr>
		<tr>
			<td><input type='radio' name='method_polog_cluster' <?php if($_SESSION['method_polog_cluster'] == 'maxDistance') echo "checked='checked'"?> value='maxDistance'>По максимальному расстоянию</td>
		</tr>
		<tr>
			<td><input type='radio' name='method_polog_cluster'  <?php if($_SESSION['method_polog_cluster'] == 'summCoord') echo "checked='checked'"?>value='summCoord'>Суммирование координат</td>
		</tr>
		<?php if($_SESSION['count_method'] != 'hands'):?>
		<tr>
			<td><input type='radio' name='method_polog_cluster' <?php if($_SESSION['method_polog_cluster'] == 'AutoToPolygom') echo "checked='checked'"?> disabled='disabled'  value='AutoToPolygom'>Автоматический по полигонам</td>
		</tr>
		<tr>
			<td><input type='radio' name='method_polog_cluster' <?php if($_SESSION['method_polog_cluster'] == 'AutoToGraphs') echo "checked='checked'"?>value='AutoToGraphs'>Автоматический по графикам</td>
		</tr>
		<?php endif?>
		<tr>
			<td><a href="/preparation/count" class='btn btn-primary pull-left'> <i class="fa fa-backward" aria-hidden="true"></i> Назад</a>
			<button class=' btn btn-primary pull-right' type='submit'> Вперед <i class="fa fa-forward" aria-hidden="true"></i></button></td>
		</tr>
</table>
</form>