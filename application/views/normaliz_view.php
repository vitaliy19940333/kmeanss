<h1>Шаг 2. Выбор метода нормализации</h1>
<?=$data['massage']?>
<form class="form-inline" action='/preparation/visualization' method='post'>
	<table class='table table-striped table-bordered table-hover'>
		<tr class='info'>
			<td> <i class="fa fa-cog" aria-hidden="true"></i> Методы нормализации</td>
		</tr>
		<?php $i = 0; foreach($data['normalization'] as $key => $value):?>
		<tr>
			<td><input type='radio' name='method_normal' value="<?=$value['val']?>" <?php if($_SESSION['method_normal'] == $value['val']) echo "checked='checked'";?>> <?=$value['title']?></td>
		</tr>
		<?php endforeach;?>
		<tr style='text-align:center'>
			<td><a href="/preparation/fields" class='btn btn-primary'> <i class="fa fa-backward" aria-hidden="true"></i> Назад</a>
			<button class=' btn btn-primary' type='submit'> Вперед <i class="fa fa-forward" aria-hidden="true"></button></a></td>
		</tr>
	</table>
</form>