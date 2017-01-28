<?php
class Controller_Preparation extends Controller
{
	public function __construct()
	{
		parent::__construct();
		
		//if($this->isGet())
			//header("LOCATION: /home");
			
	}
	
	
	
	
	//Метод выбора информационного поля и признаков кластеризации
	//STEP 1 
	//
	public function action_fields($err_massage = "")
	{
		if($this->isPost() AND ($err_massage == ""))
			$_SESSION['table'] = $_POST['table'];
		elseif(empty($_SESSION['table']))
			header("LOCATION: /home");
		$data['column'] = $this->m_mysql->Select("SHOW COLUMNS FROM ".$_SESSION['table']);
		$data['data'] =  $this->m_mysql->Select("SELECT * FROM ".$_SESSION['table']);
		$black_list =  $this->m_mysql->Select("SELECT * FROM ".$_SESSION['table']." LIMIT 2,2");
		
		foreach($black_list[0] as $field => $value)
		{
			if(is_numeric($value))
				$white_list_full[] = $field;
		}
		$data['white_list_full'] = $white_list_full;
		$data['massage'] = $err_massage;
		$data['filed'] = $this->getSelectField();
		$this->view->generate('params_view.php','template_view.php',$data);	
		
	}
	
	//Метод выбора метода нормализации
	//STEP 2 
	//	
	public function action_normalization($err_massage = "")
	{
		if($this->isPost())
		{
			if($_POST['field_info'] == "")
				$this->action_fields("<p class='alert alert-danger'><i class='fa fa-exclamation-triangle' aria-hidden='true'></i> Необходимо выбрать информационное поле</p>");
			else
			{
				$_SESSION['field_info'] = $_POST['field_info'];
				
				$tr =0;
				
				$error_data = true;
				
				foreach($_POST as $key => $value)
				{
					if(preg_match("/^data[0-9]+$/",$key))
					{
						$_SESSION['data_cl'][$tr++] = substr($key,4);
						$error_data = false;
					}
				}
				
				$this->deleteSess($tr);
				
				if($error_data)
					$this->action_fields("<p class='alert alert-danger'><i class='fa fa-exclamation-triangle' aria-hidden='true'></i> Необходимо выбрать минимум 1 признак</p>");
				else
				{
					$data['normalization'] = $this->getMethodNormalization();
					$this->view->generate('normaliz_view.php','template_view.php',$data);	
				}
					
			}
		}elseif(!empty($_SESSION['data_cl'])){
			$data['normalization'] = $this->getMethodNormalization();
			$this->view->generate('normaliz_view.php','template_view.php',$data);	
		}
		else
			$this->action_fields();
	}
	
		//STEP 3
	public function action_visualization(){
		if(!empty($_POST['method_normal']))
			$_SESSION['method_normal'] = $_POST['method_normal'];
		if($_SESSION['method_normal'] == "")
		{
			$data['normalization'] = $this->getMethodNormalization();
			$data['massage'] = "<p class='alert alert-danger'><i class='fa fa-exclamation-triangle' aria-hidden='true'></i>Метод нормализации необходимо выбрать обязательно</p>";
			$this->view->generate('normaliz_view.php','template_view.php',$data);	
		}
		else{
			$data = $this->getDataArray();
			
			$model_cluster = new Model_Cluster('3','base',$data);
			
			$data['uniq_dat'] = $model_cluster->uniq_data();
			$data['polygon'] = $model_cluster->getDataForPolygin();
			$data['title_field'] = $model_cluster->title;
			
			foreach($data['uniq_dat'] as $key => $value)
				$data['uniq_data'][$model_cluster->title[$key]] = $value;
			
			$data['data_for_graph'] = $model_cluster ->raspologenie_attr();
			$mass = array();
		
			foreach($data['data_for_graph'] as $key => $dat)
				asort($data['data_for_graph'][$key]);				
			

			foreach($data['data_for_graph'] as $key => $dat)
			{
				$data['data_for_graphs'][$model_cluster->title[$key]] = $dat;	
				$data['data_for_graphss'][$model_cluster->title[$key]]['x'] = implode(",",$dat);
				$keys = array_keys($dat);
				asort($keys);
				$data['data_for_graphss'][$model_cluster->title[$key]]['y'] = implode(",",$keys);
			}
			
			foreach($data['data_for_graph'] as $k => $v)
			{
				$i = 0;
				foreach($v as $key => $value)
				{
					if($i == 0) $values = $value;
					$mass[$k][$key] = $value-$values;
					$values = $value;
					$i++;
				}
			}
			
			
			foreach($mass as $k => $v)
			{$zzz = 1;
				foreach($v as $kk => $vv)
				{
					$array_prom[0] = "'".$zzz++."'";
					$array_prom[1] = $vv;
					$arr[$k][] = "[".implode(",",$array_prom)."]";
				}
				
			}
			
			
			foreach($arr as $key => $value)
			{
				$data['sec_graphs'][$model_cluster->title[$key]]['x'] = implode(",",$value);
			}

			/*foreach($mass as $k => $v)
			{
				$data['data_for_second_graph'][$model_cluster->title[$k]]['x'] = implode(",",$v);
				$data['data_for_second_graph'][$model_cluster->title[$k]]['y'] = implode(",",array_keys($v));
			}*/
			
			$this->view->generate('visual_view.php','template_view.php',$data);	
		}	
	} 
	
	//STEP 4 Выбор количество кластеров
	//
	//
	public function action_count($massage = "")
	{
		if(!empty($_POST['method_normal']))
			$_SESSION['method_normal'] = $_POST['method_normal'];
		if($_SESSION['method_normal'] == "")
		{
			if(empty($_SESSION['field_info']))
			{
				$this->action_normalization($err_massage = "<p class='alert alert-danger'><i class='fa fa-exclamation-triangle' aria-hidden='true'></i>Метод нормализации необходимо выбрать обязательно</p>");
			}else{
				$data['normalization'] = $this->getMethodNormalization();
				$data['massage'] = "<p class='alert alert-danger'><i class='fa fa-exclamation-triangle' aria-hidden='true'></i>Метод нормализации необходимо выбрать обязательно</p>";
				$this->view->generate('count_cluster_view.php','template_view.php',$massage);
				}				
		}else{
			$this->view->generate('count_cluster_view.php','template_view.php',$massage);	
		}
	}
	
	//STEP 5 Определение положения начальных цетроидов
	//
	//
	public function action_centroid($massage = "")
	{
		if(empty($_POST) AND empty($_SESSION['count_cluster']))
		{
			if(empty($_SESSION['count_cluster']) OR $_SESSION['count_cluster'] = "")
			
				$this->action_count("<p class='alert alert-danger'><i class='fa fa-exclamation-triangle' aria-hidden='true'></i>Выберите автоматический методот рассчета количества кластеров или введите в ручную</p>");
		}else{
			if(($_POST['method_count_cluster'] == 'hands') OR ($_SESSION['count_method']  == 'hands'))
			{
				$_SESSION['count_method']  = 'hands';
				$_SESSION['count_cluster'] = $_POST['count_cluster_hands'];
			}
			if(($_POST['method_count_cluster'] == 'auto') OR ($_SESSION['count_method'] == 'auto'))
			{
				$_SESSION['count_method']  = 'auto';
				$data = $this->getDataArray();
				$model_cluster = new Model_Cluster('3','base',$this->getDataArray(),'zero_to_one','Evklid','AutoToGraphs','');
				$model_cluster->setDataFromCluster();
				$_SESSION['count_cluster'] = $model_cluster->k;
			}
			
			$this->view->generate('select_cluster_view.php','template_view.php',$massage);	
		}
			
	}
	
	//STEP 6
	//
	//
	
	public function action_metrics($massage = "")
	{
		if(empty($_POST['method_polog_cluster']) AND empty($_SESSION['method_polog_cluster']))
		{
			$this->action_centroid("<p class='alert alert-danger'><i class='fa fa-exclamation-triangle' aria-hidden='true'></i>Необходимо выбрать один из методов</p>");
		}else{
			if(!empty($_POST['method_polog_cluster']))
			{
				$_SESSION['method_polog_cluster'] = $_POST['method_polog_cluster'];
			}
			$this->view->generate('metrics_view.php','template_view.php',$massage);	
		}
	}
	
	//step 7
	//
	//
	public function action_qality()
	{
		if(empty($_POST['metrics']) AND empty($_SESSION['metrics']))
		{
				$this->action_metrics("<p class='alert alert-danger'><i class='fa fa-exclamation-triangle' aria-hidden='true'></i>Необходимо выбрать одну из метрик</p>");
		}else{
			if(!empty($_POST['metrics']))
			{
				$_SESSION['metrics'] = $_POST['metrics'];
			}
			$this->view->generate('quality_view.php','template_view.php',$massage);	
		}
	}
	
	
	//STEP 8
	public function action_result($save = false){
		if($save == false) {
			unset($_SESSION['qality']);
			if(!empty($_POST))
			{
				$tr = 1;
				foreach($_POST as $key => $value)
					if(strripos($key,'qality') == false)
						$_SESSION['qality'][$tr++] = substr($key,6);
			}
		}
		
		$model_cluster = new Model_Cluster($_SESSION['count_cluster'],'base',$this->getDataArray(),$_SESSION['method_normal'],$_SESSION['metrics'],$_SESSION['method_polog_cluster'],$_SESSION['qality']);
		$model_cluster->setDataFromCluster();
		
		$clustera = $model_cluster->clusters;
		$clusteras = $model_cluster->clusters;
		$centroidu = $model_cluster->centroid;
		$centroidu_m = $centroidu;
		
		$optimizated = $this->optimizated($model_cluster,$clusteras,$centroidu,$clustera);
		$first_dist = $optimizated[1];
		$second_cluster = new Model_Cluster($_SESSION['count_cluster'],'base',$this->getDataArray(),$_SESSION['method_normal'],$_SESSION['metrics'],$_SESSION['method_polog_cluster'],$_SESSION['qality']);
		$second_cluster->setDataFromCluster($optimizated);
	
		$summ_first2 = 0;
		foreach($second_cluster->summ_distance[0] as $i => $vvv)
			$summ_first2+=$vvv['summ'];
		
		
		
		if($summ_first2 > $first_dist)
			$object = $model_cluster;
		else
			$object = $second_cluster;
		
		foreach($second_cluster->summ_distance as $key => $value)
		{
			$summ = 0;
			foreach($value as $k => $v)
			{
				$summ+=$v['summ'];
			}
			$summs[] = $summ;
		}
		
		
		$data['title'] = $model_cluster->title;
		$data['min_max'] = $model_cluster->min_max();
		$data['clusters'] = $object->transorm_cluster;
		$data['count_attributes'] = $model_cluster->count_attr;
		$data['title'] = $model_cluster->title;
		$data['info'] = $model_cluster->info;
		$data['count_attr'] = $model_cluster->count_attr;
		
		//Индексы
		$data['general_summ'] = min($summs);
		
		
		foreach($_SESSION['qality'] as $k => $v)
		{
			if($v == 'Distance') continue;
			$data['index'][] = $object->$v($object->clusters,$object->centroid);
		}
	//echo "<pre>";
	//print_r($data['index']);exit();
		
		
		$data['normalization'] = $this->getMethodNormalization($this->getDataArray());
		
		foreach($object->prev_centroid as $key => $value)
			foreach($value as $k => $v)
				$data['gr'][$model_cluster->title[$k]][] = $v;
				
		$this->view->generate('result_view.php','template_result.php',$data);	
	}
	
	//Методо сохранения настроек
	//
	//
	public function action_saveSetting()
	{
		$_SESSION['method_normal'] = $_POST['method_normal'];
		
			if($_POST['method_count_cluster'] != 'auto')
			{
				$_SESSION['count_method'] = 'hands';
				$_SESSION['count_cluster'] = $_POST['count_cluster_hands'];
			}
		else{
			$model_cluster = new Model_Cluster('3','base',$this->getDataArray(),'zero_to_one','Evklid','AutoToGraphs','');
			$model_cluster->setDataFromCluster();
			$_SESSION['count_cluster'] = $model_cluster->k;
			$_SESSION['count_method'] = 'auto';
		}
		
		$_SESSION['method_polog_cluster'] = $_POST['method_polog_cluster'];
		$_SESSION['metrics'] = $_POST['metrics'];
		
		$tr = 1;
		unset($_SESSION['qality']);
		foreach($_POST as $key => $value)
		{
			if(preg_match('/qality/',$key))
			{
				$_SESSION['qality'][$tr++] = substr($key,6);
			}
		}
		$this->action_result(true);
	}
	
	
	private function getSelectField()
	{
		if(is_array($_SESSION['data_cl']))
		{
			foreach($_SESSION['data_cl'] as $key => $value)
				$array[] = $value;
			return $array;
		}
	}
	
	
	private function getMethodNormalization()
	{
		$method['0']['val'] = 'zero_to_one';
		$method['0']['title'] = 'Нормализация [0,…,1]';
		
		$method['1']['val'] = 'm_one_to_one';
		$method['1']['title'] = 'Нормализация [-1,…,1]';
		
		$method['2']['val'] = 'standard_deviation';
		$method['2']['title'] = 'Стандартное отклонение';
		
		$method['3']['val'] = 'avarege_deviation';
		$method['3']['title'] = 'Cреднеквадратическое отклонение';
		
		$method['4']['val'] = 'none';
		$method['4']['title'] = 'Не нормализировать';
		return $method;
	}
	private function deleteSess($i)
	{
		if(is_array($_SESSION['data_cl']))
		{
			foreach($_SESSION['data_cl'] as $key => $value)
			{
				if($key >= $i)
					unset($_SESSION['data_cl'][$key]);
			}
		}
	}
	

	
	private function getDataArray()
	{
		$data_from_table = $this->m_mysql->Select("SELECT * FROM ".$_SESSION['table']);
				
		$data['column'] = $this->m_mysql->Select("SHOW COLUMNS FROM ".$_SESSION['table']);
				
		$array[0][0] = $_SESSION['field_info'];
		
		$i = 1;

		foreach($_SESSION['data_cl'] as $keys => $values)
		{
			$array[0][$i++] = $data['column'][$values]['Field'];				
		}
		
		$i = 1;
		
		foreach($data_from_table as $key => $value)
		{
			$array[$i][0] = $value[$_SESSION['field_info']];
			foreach($_SESSION['data_cl'] as $keys => $values)
			{
					$array[$i][] = $value[$data['column'][$values]['Field']];
								
			}
			$i++;
		}
		
		return $array;
		
	}
	
	private function optimizated($first_obj,$clusteras,$centroidu,$clustera)
	{
		for($m = 0; $m < 1; $m++)
		{			
		//Рассчитываем расстояние от точки до центроида
			foreach($clusteras as $key => $value)
			{
				foreach($value as $key_point => $point)
				{
					$distance_point_to_centr[$key][$key_point] = $first_obj->distance($point,$centroidu[$key]);
				}
				$point_max_distance[$key]['dist'] = max($distance_point_to_centr[$key]);
				$point_max_distance[$key]['key'] = array_keys($distance_point_to_centr[$key],max($distance_point_to_centr[$key]))[0];
				$point_max_distance[$key]['key_min'] = array_keys($distance_point_to_centr[$key],min($distance_point_to_centr[$key]))[0];
				
			}
			
			
			//Находим наиболее отдаленною точку
			foreach($distance_point_to_centr as $key => $value)
			{
				$max_distance[$key] = array_keys($value,max($value))[0];
			}	
			

			//Создвем массив данные без крайней точки
			foreach($clusteras as $key => $value)
			{
				foreach($value as $key_point => $point)
				{
					if($key_point == $max_distance[$key]) continue;
					$clustera_bez_tochki[$key][$key_point] = $point;
				}
			}
			
			$clusteras = $clustera_bez_tochki;
			$clustera_bez_tochki = array();
			$distance_point_to_centr = array();
		}

		foreach($point_max_distance as $key => $value)
		{
			$max_distance_point[$key] = $value['dist'];
		}

		$distance_max = max($max_distance_point);

		foreach($point_max_distance as $key => $value)
		{
			if($value['dist'] == $distance_max)
			{
				$poligin_point_max[$key]['point'] = $clustera[$key][$value['key']];
				$poligin_point_max[$key]['centr'] = $centroidu[$key];
				$poligin_point_max[$key]['min'] = $clustera[$key][$value['key_min']];
			}
				
		}
		

		foreach($centroidu as $key => $value)
			$new_centroid[$key] = $value;
			
		/*Среднее расстояние по кластеру*/
		$summ_first=0;
		foreach($first_obj->summ_distance[0] as $i => $vvv)
		{
			if($vvv['summ'] == 0) continue;
			$avarage_distance[$i] = $vvv['summ']/(count($vvv)-1);
			$summ_first+=$vvv['summ'];
		}
		/*-----------------------------*/
		foreach($poligin_point_max as $key => $value)
		{
			$count_param = count($value['point']);
			for($i = 0 ; $i < $count_param; $i++)
			{
				if(($value['point'][$i] - $value['centr'][$i]) < 0)
					$new_centroid[$key][$i] = $value['centr'][$i] + $avarage_distance[$i]/100*25;
				else
					$new_centroid[$key][$i] = $value['centr'][$i] - $avarage_distance[$i]/100*15;
			}
		}
		return array($new_centroid,$summ_first);
	}
}
?>