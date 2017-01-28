<?php
//
// Помощник работы с БД
//
class Model_Msql extends Model
{
    private static $instance; 	// ссылка на экземпляр класса
    
    //
	// Получение единственного экземпляра (одиночка)
	//
	public static function Instance()
	{
		if (self::$instance == null)
			self::$instance = new Model_Msql();
		
		return self::$instance;
	}

	private function __construct()
	{	
		mysql_connect('localhost', 'root', '') or die('No connect with data base'); 
		mysql_query('SET NAMES utf8');
		mysql_select_db('kmeans') or die('No data base');
	}
	
	public function readExelFile($filepath){
	
		require_once "excel/PHPExcel.php"; //подключаем  фреймворк
		
		$ar=array(); // инициализируем массив
		
		$inputFileType = PHPExcel_IOFactory::identify($filepath);  // узнаем тип файла, excel может хранить файлы в разных форматах, xls, xlsx и другие
		
		$objReader = PHPExcel_IOFactory::createReader($inputFileType); // создаем объект для чтения файла
		
		$objPHPExcel = $objReader->load($filepath); // загружаем данные файла в объект
		
		$ar = $objPHPExcel->getActiveSheet()->toArray(); // выгружаем данные из объекта в массив
		
		return $ar; //возвращаем массив
	}
	
	public function CreateTable($file,$name)
	{
		
		if(move_uploaded_file($file['tmp_name'],$file['name']))
		{
			$a = $this->readExelFile($file['name']);
		}
			$co = count($a[0]);
	
		for($i = 0 ; $i < $co; $i++)
		{
			if($i == ($co-1))
			{
				$str = "$str`".$a[0][$i]."` varchar(200) NOT NULL";
				break;
			}
			$str = "$str`".$a[0][$i] ."` varchar(200) NOT NULL,";
		}
	
		unlink($file['name']);
	
		$sql = "CREATE TABLE `$name` ($str)";
		echo $sql;
	
		mysql_query($sql);
	
		$count_data = count($a);
	
		for($j = 0; $j < $co; $j++)
		{
			if($j == ($co-1))
			{
				$sql1 = "$sql1`".$a[0][$j]."`";
				break;
			}
			$sql1 = "$sql1`".$a[0][$j]."`,";
		}

		
		for($i = 1; $i < $count_data; $i++)
		{
			$sql4 = '';
			$sql2 = '';
			for($j = 0; $j < $co; $j++)
			{
				if($j == ($co-1))
				{
					$sql2 = "$sql2"."'".$a[$i][$j]."'";
					break;
				}
				$sql2 = "$sql2"."'".$a[$i][$j]."',";
			}
			$sql4 = "INSERT into `$name` ($sql1) VALUES ($sql2);";
			mysql_query($sql4);
		}
	}
	
	
    //
	// Выборка строк
	// $query    	- полный текст SQL запроса
	// результат	- массив выбранных объектов
	//
	public function Select($query)
	{
		$result = mysql_query($query);
		
		if (!$result)
			die(mysql_error());
		
		$n = mysql_num_rows($result);
		$arr = array();
	
		for($i = 0; $i < $n; $i++)
		{
			$row = mysql_fetch_assoc($result);		
			$arr[] = $row;
		}

		return $arr;				
	}
	
	//
	// Вставка строки
	// $table 		- имя таблицы
	// $object 		- ассоциативный массив с парами вида "имя столбца - значение"
	// результат	- идентификатор новой строки
	//
	public function Insert($table, $object)
	{			
		$columns = array();
		$values = array();
	
		foreach ($object as $key => $value)
		{
			$key = mysql_real_escape_string($key . '');
			$columns[] = $key;
			
			if ($value === null)
			{
				$values[] = 'NULL';
			}
			else
			{
				$value = mysql_real_escape_string($value . '');							
				$values[] = "'$value'";
			}
		}
		
		$columns_s = implode(',', $columns);
		$values_s = implode(',', $values);
			
		$query = "INSERT INTO $table ($columns_s) VALUES ($values_s)";
		$result = mysql_query($query);
								
		if (!$result)
			die(mysql_error());
			
		return mysql_insert_id();
	}
	
	//
	// Изменение строк
	// $table 		- имя таблицы
	// $object 		- ассоциативный массив с парами вида "имя столбца - значение"
	// $where		- условие (часть SQL запроса)
	// результат	- число измененных строк
	//	
	public function Update($table, $object, $where)
	{
		$sets = array();
	
		foreach ($object as $key => $value)
		{
			$key = mysql_real_escape_string($key . '');
			
			if ($value === null)
			{
				$sets[] = "$key=NULL";			
			}
			else
			{
				$value = mysql_real_escape_string($value . '');					
				$sets[] = "$key='$value'";			
			}			
		}
		
		$sets_s = implode(',', $sets);			
		$query = "UPDATE $table SET $sets_s WHERE $where";
		$result = mysql_query($query);
		
		if (!$result)
			die(mysql_error());

		return mysql_affected_rows();	
	}
	
	//
	// Удаление строк
	// $table 		- имя таблицы
	// $where		- условие (часть SQL запроса)	
	// результат	- число удаленных строк
	//		
	public function Delete($table, $where)
	{
		$query = "DELETE FROM $table WHERE $where";		
		$result = mysql_query($query);
						
		if (!$result)
			die(mysql_error());

		return mysql_affected_rows();	
	}
}
