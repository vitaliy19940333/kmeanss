<h1>Загрузка данных в БД из файла MS Excel</h1>
<div class= 'add_to_base'>
		<form class="form-inline"  action='' method='post' enctype = "multipart/form-data">
	<table class='table table-striped table-bordered table-hover'>
		<tr>
			<td>
				<p>Выберите Excel файл</p>
			</td>
			<td>
				<input type ='file' name = 'data_for_bd'>
			</td>
		</tr>
		<tr>
			<td>
				<p>Введите название  таблицы</p>
			</td>
			<td>
				<input type = 'text' name = 'table_name'>
			</td>
		</tr>
		<tr>
			<td colspan='2' align='center'>
				<input type='submit'  class='btn btn-primary' name = 'send_form' id = 'submit' value ='Добовить в БД'>
			</td>
		</tr>
		</table>
		</form>
	</div>
	
	