<?php
class Controller_Insert extends Controller
{
	
	public function readExelFile($filepath){
	
		require_once "/PHPExcel.php"; //подключаем наш фреймворк
		
		$ar=array(); // инициализируем массив
		
		$inputFileType = PHPExcel_IOFactory::identify($filepath);  // узнаем тип файла, excel может хранить файлы в разных форматах, xls, xlsx и другие
		
		$objReader = PHPExcel_IOFactory::createReader($inputFileType); // создаем объект для чтения файла
		
		$objPHPExcel = $objReader->load($filepath); // загружаем данные файла в объект
		
		$ar = $objPHPExcel->getActiveSheet()->toArray(); // выгружаем данные из объекта в массив
		
		return $ar; //возвращаем массив
	}
	
	public function action_index()
	{
		if($this->isPost())
		{
			if(isset($_POST['send_form']))
			{
				$table_name = $_POST['table_name'];
				
				$excel = $_FILES['data_for_bd'];
				
				
				
				if(move_uploaded_file($excel['tmp_name'],$excel['name']))
				{
					$a = $this->readExelFile($excel['name']);
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
				//echo $str;
				
				unlink($excel['name']);
				
				$sql = "CREATE TABLE `$table_name` ($str)";
				
				//echo $sql;

				$this->m_mysql->Select($sql);
				
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
					//echo $sql1;
					
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
					$sql4 = "INSERT into `$table_name` ($sql1) VALUES ($sql2);";
					echo $sql4;

					$this->m_mysql->Select($sql4);
				}

			}
		}
		$this->view->generate('insert.php','template_view.php',$data);
	}
	
}
?>