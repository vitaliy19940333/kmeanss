<h1>Шаг 1. Определение признаков для кластеризации</h1>
<?=$data['massage']?>
<form class="form-inline" action='/preparation/normalization' method='post'>
	<table class='table table-striped table-bordered table-hover'>
		<tr class='info'>
			<td> <i class="fa fa-info-circle" aria-hidden="true"></i> Информационное поле (выбрать можно только одно)</td>
			<td> <i class="fa fa-database" aria-hidden="true"></i> Признаки кластеризации (выбрать можно более одного)</td>
		</tr>
		<?php $i = 0; foreach($data['column'] as $key => $value):?>
		<tr>
			<td><input type='radio' name='field_info' value="<?=$value['Field']?>" <?php if($_SESSION['field_info'] == $value['Field']) echo "checked='checked'";?>> <?=$value['Field']?></td>
			<td><?php if(@in_array($value['Field'],$data['white_list_full'])){?><input type='checkbox' name='data<?=$i++?>' value="<?=$value['Field']?>" <?php if(@in_array($key,$data['filed'])) echo "checked='checked'";?>><?php }else $i++;?> <?=$value['Field']?></td>
		</tr>
		<?php endforeach;?>
		<tr style='text-align:center'>
			<td><a href="/home" class='btn btn-primary'> <i class="fa fa-backward" aria-hidden="true"></i> Назад</a></td>
			<td><button class=' btn btn-primary' type='submit'> Вперед <i class="fa fa-forward" aria-hidden="true"></button></a></td>
		</tr>
	</table>
</form>


<h2 style='text-align:center'>Таблица данных "<?=$_SESSION['table']?>"</h2>
<table class='table table-striped table-bordered table-hover' style='text-align:center'>
	<tr class='success'> 
	<?php $i = 0; foreach($data['column'] as $key => $value):?>
		<td><?=$value['Field']?></td>
	<?php endforeach?>
	</tr>
	<?php $i = 0; foreach($data['data'] as $key => $value):?>
		<tr>
		<?php $i = 0; foreach($value as $k => $v):?>
			<td><?=$v?></td>
		<?php endforeach?>
		</tr>
	<?php endforeach?>
</table>