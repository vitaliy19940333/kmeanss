<?=$data?>
<h1>Шаг 4. Определение количества кластеров:</h1>
<form id='form_count_cluster' method='post' action='/preparation/centroid'>
<table class='table table-striped table-bordered table-hover' style='text-align:left'>
		<tr>
			<td><input type='radio' name='method_count_cluster' id="hands" <?php if($_SESSION['count_method'] == 'hands') echo "checked=checked"?> value='hands'>Ручной Ввод</td>
			<td><input type='number' name='count_cluster_hands'id="val_hands"  class="form-control" disabled="disabled" value='<?=$_SESSION['count_cluster']?>'></td>
		</tr>
		<tr>
			<td><input type='radio' name='method_count_cluster' id="auto" value='auto' <?php if($_SESSION['count_method'] == 'auto') echo "checked=checked"?>> Определить количествв кластеров автоматически</td>
			<td></td>
		</tr>
		<tr>
			<td><a href="/preparation/visualization" class='btn btn-primary pull-left'> <i class="fa fa-backward" aria-hidden="true"></i> Назад</a></td>
			<td><button class=' btn btn-primary pull-right' type='submit'> Вперед <i class="fa fa-forward" aria-hidden="true"></button></a></td>
		</tr>
</table>
</form>

<script>
$(function (){

    $('#form_count_cluster').click(function (){

     if(!$("#hands").prop("checked"))
		$('#val_hands').attr('disabled',"disabled");
	else
        $('#val_hands').removeAttr('disabled');

   });

});
</script>

<script src="/bower_components/jquery/dist/jquery.min.js"></script>
<script src="/bower_components/jquery-validation/dist/jquery.validate.min.js"></script>
<script src="/js/custom.js"></script>
<style>
.error
{
	color:red;
}
</style>