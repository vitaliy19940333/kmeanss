<?php
class Model_Cluster extends Model{
	
	//Кластера
	public $clusters;  
	
	
	// Данные без нормализации
	public $data;
	
	//Информационное поле
	public $info;       
	
	//Имена признаков
	public $title;    

	//Нормализованные данные
	public $normal_data;
	
	
	//Количество кластеров
	public $k;     

    //Центроиды
	public $centroid; 
	
	
	 //Предыдущие центроиды
	public $prev_centroid;
	
	
	//Количество объектов
	public $count_data; 
	
	//Количество атрибутов
	public $count_attr; 
	
	
	//Коэффициенты нормализации
	public $koef_normal; 
	
	//Данные после обратного преобразования
	public $transorm_cluster; 
	
	
	public $transorm_centroid;
	public $summ_distance;
	public $start_centroids;
	public $result_cluster; //это свойство содержит конечные кластера;
	
	////////////////////////////////
	
	public $method_normal;
	public $metrics;
	public $method_polog_cluster;
	public $qality;
	
	
	
	
	
	//Конструктов принимает данные и количество кластеров
	public function __construct($k,$type,$data,$method_normal ='zero_to_one',$metrics = 'Evklid',$method_polog_cluster='random',$qality='')
	{
		$this->load_data($type,$data);
		$this->k = $k;
		$this->count_data = count($this->data);
		$this->count_attr = count($this->data[0]);
		$this->method_normal = $method_normal;
		$this->metrics = $metrics;
		$this->method_polog_cluster = $method_polog_cluster;
		$this->qality = $qality;	
	}
	
	public function setDataFromCluster($second_obj = false)
	{
		$method_normal = $this->method_normal;
		$this->$method_normal();
		
		if(!$second_obj)
		{
			$method_polog_cluster = $this->method_polog_cluster;
			$this->$method_polog_cluster();
		}else{
			$this->centroid = $second_obj[0];
			$this->start_centroids = $second_obj[0];
			$this->prev_centroid = $second_obj[0];
		}
		
		
		$this->allocation();
		$centr= $this->recenter($this->clusters);
		$this->centroid = $centr;
		$i = 0;
		
		while(true)
		{
			$this->allocation();
			$centr= $this->recenter($this->clusters);
			$this->centroid = $centr;
			if($this->prev_centroid == $this->centroid)
				break;
			else{
				$i++;
				$this->prev_centroid = $centr;
			}
		}
			$centr= $this->recenter($this->clusters);
			$this->centroid = $centr;
			$this->prev_centroid = $this->centroid;
			$this->allocation();
		
		$this->transorm_centroid();
		$this->transorm_data();
		
	}
	
	
	//Метод загрузки данных
	public function load_data($type,$array)
	{
		$path = $_SESSION['path'];
		if($type == 'excel')
			$data = readExelFile("$path");
		else
			$data = $array;

		$count_attribute = count($data[0]); //Вычеслиния количества атрибутов + 1
		$count_ob = count($data);			//Количество объектов + 1
		
		/*Цикл формирования названий аттрибутов*/
		for($i = 1; $i < $count_attribute; $i++)
			$title[($i-1)] = $data[0][$i];   //Формирования одномерного массива с названиями атрибутов
		
		/*--------------------------*/
		
		
		/*Цикл формирования информационного поля*/
		for($i = 1; $i < $count_ob; $i++ )
			$info[($i-1)] = $data[$i][0];  //Формирования одномерного массива
		/*-------------------------------------*/
		
		
		//Двойной цикл
		for($i = 1; $i < $count_ob;$i++)
			for($j = 1; $j < $count_attribute; $j++ )
				$date[($i-1)][($j-1)] = $data[$i][$j]; //Формирования двумерного массива со значениями атриботов для каждого объекта
			
		$this->data = $date;
		$this->normal_data = $date;
		$this->info = $info;
		$this->title = $title;
	}
	
	
	//Метод нормализации Стандартное отклонение
	public function standard_deviation($obj = false)
	{
		
		if($obj == false)
			$data = $this->data;
		else
			$data = $obj;
		
		$attr_summ = array();
		
		//Находим среднее значение атрибутов//
		foreach($data as $index_data => $value_data)
			for($i = 0; $i < $this->count_attr; $i++)
				$attr_summ[$i] += $value_data[$i];
		
		for($i = 0; $i < $this->count_attr; $i++)
		{
			$avarage_attr[$i] = $attr_summ[$i]/$this->count_data;
			$summa[$i] = 0;
		}
		//Среднее значение нашли//	
		
		//Вычисляем Sx для каджого атрибута//
		foreach($data as $index_data => $value_data)
		{
			for($i = 0; $i < $this->count_attr; $i++)
			{
				$summa[$i] = $summa[$i] + ($value_data[$i]-$avarage_attr[$i])*($value_data[$i]-$avarage_attr[$i]);
			}
		}
		
		
		for($i = 0; $i < $this->count_attr; $i++)
		{
			$sx[$i] = sqrt((1/($this->count_data - 1))*$summa[$i]);
		}
		//Конец вычисления Sx //

		//Нормализуем данные//
		foreach($data as $index_data => $value_data)
		{
			for($i = 0; $i < $this->count_attr; $i++)
			{
				$data[$index_data][$i] = ($value_data[$i]-$avarage_attr[$i])/$sx[$i];
			}
		}
		//Нормализация оконченна//

		$koef_normal['avarage_attr'] = $avarage_attr;
		$koef_normal['sx'] = $sx;
		if($obj == false)
		{
			$this->normal_data = $data; 
			$this->koef_normal =  array($avarage_attr,$sx);
		}else
		{
			return $data;
		}
			
	}
	
	//Метод нормализации от 0 до 1
	public function zero_to_one($obj = false)
	{
		if($obj == false)
			$data = $this->data;
		else
			$data = $obj;
		
		$attr = array();
		
		foreach($data as $index_data => $array_attr)
		{
			foreach($array_attr as $key_attr => $value_attr)
			{
				$attr[$key_attr][] = $value_attr;
			}
		}
		
		for($i = 0; $i < $this->count_attr; $i++)
		{
			$attr_min_max[$i]['min'] = min($attr[$i]);
			$attr_min_max[$i]['max'] = max($attr[$i]);
		}
		
		foreach($data as $index_data => $array_attr)
		{
			foreach($array_attr as $key_attr => $value_attr)
			{
				$data[$index_data][$key_attr] = ($value_attr-$attr_min_max[$key_attr]['min'])/($attr_min_max[$key_attr]['max']-$attr_min_max[$key_attr]['min']);
			}
		}
		if($obj == false)
		{
			$this->normal_data = $data;
			$this->koef_normal = $attr_min_max;
		}else{
			return $data;
		}
		
	}
	
	
	//Метод нормализации от -1 до 1
	public function m_one_to_one($obj = false)
	{
	
		if($obj == false)
			$data = $this->data;
		else
			$data = $obj;
		
		$attr = array();
		
		foreach($data as $index_data => $array_attr)
		{
			foreach($array_attr as $key_attr => $value_attr)
			{
				$attr[$key_attr][] = $value_attr;
			}
		}
		
		for($i = 0; $i < $this->count_attr; $i++)
		{
			$attr_min_max[$i]['min'] = min($attr[$i]);
			$attr_min_max[$i]['max'] = max($attr[$i]);
		}
		
		foreach($data as $index_data => $array_attr)
		{
			foreach($array_attr as $key_attr => $value_attr)
			{
				$data[$index_data][$key_attr] = 2*($value_attr-$attr_min_max[$key_attr]['min'])/($attr_min_max[$key_attr]['max']-$attr_min_max[$key_attr]['min'])-1;
			}
		}
		if(!$obj)
		{
			$this->normal_data = $data;
			$this->koef_normal = $attr_min_max;;
		}else
			
			return $data;
		
	}
	
	//Метод нормализации Среднеквадратическое отклонение
	public function avarege_deviation($obj = false)
	{
		
		if($obj == false)
			$data = $this->data;
		else
			$data = $obj;
		
		$attr_summ = array();
		
		//Находим среднее значение атрибутов//
		foreach($data as $index_data => $value_data)
			for($i = 0; $i < $this->count_attr; $i++)
				$attr_summ[$i] += $value_data[$i];
		
		for($i = 0; $i < $this->count_attr; $i++)
		{
			$avarage_attr[$i] = $attr_summ[$i]/$this->count_data;
			$summa[$i] = 0;
		}
		//Среднее значение нашли//	
		
		//Вычисляем Sx для каджого атрибута//
		foreach($data as $index_data => $value_data)
		{
			for($i = 0; $i < $this->count_attr; $i++)
			{
				$summa[$i] = $summa[$i] + ($value_data[$i]-$avarage_attr[$i])*($value_data[$i]-$avarage_attr[$i]);
			}
		}
		
		
		for($i = 0; $i < $this->count_attr; $i++)
		{
				$sx[$i] = sqrt((1/($this->count_data))*$summa[$i]);
		}
		//Конец вычисления Sx //

		//Нормализуем данные//
		foreach($data as $index_data => $value_data)
		{
			for($i = 0; $i < $this->count_attr; $i++)
			{
				$data[$index_data][$i] = ($value_data[$i]-$avarage_attr[$i])/$sx[$i];
			}
		}
		//Нормализация оконченна//

		$koef_normal['avarage_attr'] = $avarage_attr;
		$koef_normal['sx'] = $sx;
		if(!$obj)
		{
			$this->normal_data = $data; 
			$this->koef_normal =  array($avarage_attr,$sx);
		}else
			return $data;
		
	}
	
	//Данные без нормализации
	public function none($obj = false)
	{
		if(!$obj)
			$this->normal_data = $this->data;
		else
			return $obj;
	}
	
	
	//Случайный метод выбора начальных центроидов
	public function random()
	{
		$data = $this->normal_data;
		
		for($i = 0;$i < $this->k;$i++)
		{
			$start_centroid[$i] = $data[rand(0,$this->count_data-1)];
		}
		$this->prev_centroid = $start_centroid;
		$this->start_centroids = $start_centroid;
	}
	
	//Инициализация центроидов по наибелее отдаленным точкам
	public function maxDistance()
	{
		$data = $this->normal_data; // В качестве данныех принимаем нормализованые даные
		
		for($i = 0; $i < $this->count_attr; $i++)
		{
			$array[] = 0;  // создаем массив для сравнения к началу координат
		}
		
		
		
		foreach($data as $key_data=>$value_data)
		{
			$distance_first[$key_data] = $this->distance($array,$value_data);
		}
		
		$min_dist = array_keys($distance_first,min($distance_first))[0];  //Ключ элемента ближайшего к начало координат
		
		
		$centroid[0] = $data[$min_dist]; //За первый центроид берем объект которые ближе всего к началу координат
		
		
		for($i = 1; $i < $this->k;$i++)
		{
			foreach($data as $key_data=>$value_data)
			{
				$distance[$i-1][$key_data] = $this->distance($centroid[$i-1],$value_data);
			}
			
			foreach($distance as $key => $value)
			{
				foreach($value as $k => $v)
				{
					$dist[$k] += $v;
				}
			}	
			$max_dist[$i] = array_keys($dist,max($dist))[0];
			
			$centroid[$i] = $data[$max_dist[$i]]; 		
		}
		
		$this->prev_centroid = $centroid;
		$this->start_centroids = $centroid;
	}
	
	//Инициализация начальных центроидов методом суммирования координат и 
	//Сортировки
	//Если $obj = false, то вычисляется центр масс
	//Если $obj = true, то центроид привязывается к объекту
	public function summCoord($obj = false)
	{
		$normal_data = $this->normal_data;
		
		foreach($normal_data as $index_data => $array_attr)
		{
			$transorm_data[$index_data] = 0; //ЗДЕСЬ МОЖЕТ БЫТЬ ОШИБКА
			foreach($array_attr as $key_attr => $value_attr)
			{
				$transorm_data[$index_data]+= $value_attr;
			}
		}
		
		asort($transorm_data);

		$object_in_cluster = floor($this->count_data/$this->k);
		
		$cnt = 0; //Счетчик количество объектов
		
		$ctn2 = 0; //Счетчик кластеров
		
		if(!$obj)
		{

			foreach($transorm_data as $key => $value)
			{
					for($i = 0; $i < $this->count_attr; $i++)
					{
						$centroid[$ctn2][$i] += $normal_data[$key][$i];	
					}
					$centroid[$ctn2]['obj'] = ++$cnt;		
					if(($cnt%$object_in_cluster == 0) AND ($ctn2 != ($this->k-1)))
					{
						$ctn2++; $cnt = 0;
					}
			}
			
			foreach($centroid as $key => $value)
			{
					for($i = 0; $i < $this->count_attr; $i++)
					{
						$center_mass[$key][$i]  = $value[$i]/$value['obj'];
					}
			}
		}			
		else
		{
			foreach($transorm_data as $key => $value)
			{
					$centroid[$ctn2]['summ'] += $value;
					$centroid[$ctn2]['obj'] = ++$cnt;
					
					if(($cnt%$object_in_cluster == 0) AND ($ctn2 != ($this->k-1)))
					{
						$ctn2++; $cnt = 0;
					}
						
			}
				
				
				foreach($centroid as $key => $value)
				{
					$center_mass[$key] = $value['summ']/$value['obj'];
					
					foreach($transorm_data as $k => $v)
					{
						$distance_attr[$k] = $this->distance(array($center_mass[$key]),array($v));
					}
				
					$center_mass[$key]  =  $normal_data[array_keys($distance_attr,min($distance_attr))[0]];
				}
		}
		$this->prev_centroid = $center_mass;
		$this->start_centroids = $center_mass;
	}
	
	//Метод автотического рассчета кластеров по полигонам
	function AutoToPolygom()
	{
		$combinate = $this->getCombinate();
		$this->k = count($combinate);
		$attr_min_max = $this->koef_normal;
		foreach($combinate as $key => $value)
		{
			for($i = 0; $i < count($combinate[1]); $i++)
			{
				$data[($key-1)] = $value;
			}
		}
		$method_normal = $this->method_normal;
		$data = $this->$method_normal($data);		
		$this->prev_centroid = $data;
		$this->start_centroids = $data;
			
	}
	
	//Вспомогательный метод для автоматического рассчета начальных кластеров по полигонам
	private function getCombinate()
	{
		//Данные интервалов : массив; 1 - кол-интервалов; 2- дельта: 3 - интвервалы;
		$data_from_interval = $this->object_in_interval();

		//Кол-во объектов в интервале
		$interval_quantity = $data_from_interval[0];

		//Дельта интвервалов
		$delta = $data_from_interval[1];

		//кол-интервалов
		$count_interval = $this->count_interval()[1];

		//Цико обхода интервалов всеъ атрибутов
		foreach($data_from_interval[2] as $key => $value)
		{
			$i = 0;
			//Цикл обхода интвервалов отдельного атрибутов
			foreach($value as $k => $v)
			{
				if($k == count($value)-1) continue;
				if(empty($interval_quantity[$key][$i]))
					$interval_quantity[$key][$i] = 0;
				//Фомирование массива, ключ - это интвервал; значение - это кол-во объектов в интвервале
				$intervalAndObject[$key][round($v,1)."-".round($value[$k+1],1)] = $interval_quantity[$key][$i++];
			}
		}

		//Добавляем в 0 и 1 в интервалы
		foreach($intervalAndObject as $key => $value)
		{
			array_unshift($intervalAndObject[$key],0);
			array_push($intervalAndObject[$key],0);
		}

		for($e = 0; $e < count($intervalAndObject);$e++)
		{
			$re = 0;
			foreach($intervalAndObject[$e] as $key=>$value)
			{
				if(($re == 0) OR ($re == 1))
				{
					$tochka[$re]['val'] = $value;
					$tochka[$re]['key'] = $key;
					$re++;
				}
				else
				{
					if( (($tochka[$re-2]['val'] - $tochka[$re-1]['val']) <= 0)  AND (($tochka[$re-1]['val'] - $value) > 0) )
					{
						$bhu = 2;
						$counter = 0;
						while(true)
						{
							if( ($tochka[$re-1]['val']) == ($tochka[$re-$bhu]['val']))
							{
								$counter++;
								$bhu++;
							}else
							{
								break;
							}
						}
						if($counter%2 != 0)
						{
							$explode = explode("-",$tochka[$re-($bhu-1)]['key']);
							$explode2 = explode("-",$tochka[$re-1]['key']);
							$str = $explode[0]."-".$explode2[1];
							$pick[$e][]=  $str;
						}
						else
							$pick[$e][]= $tochka[$re-($counter/2+1)]['key'];
					}
						
					$tochka[$re]['val'] = $value;
					$tochka[$re]['key'] = $key;
					$re++;
				}
			}
		}
		
		$mixed = 1;
		foreach($pick as $value)
			$mixed*=(count($value));

		for($h=0; $h < count($pick);$h++)
		{
			foreach($pick[$h] as $key => $value)
			{
				$val = explode("-",$value);
				$picks[$h][$key] = $val[0]+($val[1]-$val[0])/2;
			}	
		}

		$p = 1;
		foreach($picks as $key => $value)
					$p*=count($value);
		$perl  = 1;
		
			for($j = 0; $j < count($picks); $j++)
			{
				$counter = count($picks[$j]);
				$schet = $p/$counter;
				$ttt = 0;
				$rtyu = 0;
				if($j != 0)
					$perl*=count($picks[$j-1]);
				for($i = 1; $i <= $p; $i++)
				{
					$xxx[$i][$j] = $picks[$j][$ttt];
					if($j == 0)
					{
						if($i%$schet == 0){
							$ttt++;
						}
					}else
					{
						if($i%($schet/$perl) == 0){
							$ttt++;
						if($ttt == $counter) $ttt = 0;
					}
				}
			}
		}
			return $xxx;
	}
	
	
	//Метод автотического рассчета кластеров по графикам
	public function AutoToGraphs($maxis = false)
	{
		$data = $this->raspologenie_attr($this->normal_data);
		
		$mass = array();
		
		foreach($data as $key => $dat)
			asort($data[$key]);
			
		
		foreach($data as $k => $v)
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

		foreach($mass as $key => $value)
		{
			foreach($value as $k => $v)
			{
				$mass2[$key]["$v"] +=1;
			}
		}
		$datas = $this->uniq_data();
		
		foreach($datas as $k => $v)
			$max_mas[$k] = count($v);
		
		foreach($max_mas as $k => $v)
		{
			if($v == max($max_mas)) $max_mass22[]=$k;
		}
		
		foreach($max_mass22 as $k => $v)
		{
			$min_val[$v] = min($mass2[$v]);
		}
		
		$max = 0;
		
		foreach($mass2 as $key => $value)
		{
			foreach($value as $k => $v)
			{
				if($k > $max) $max_k = $key;
			}
		}
		
		
		$max =  $max_k;
		if($maxis)
			$max =  array_search($maxis,$this->title);
		/////////////////////////
			
			
		
		foreach($mass2[$max] as $key => $value)
			if((($key = max(array_keys($mass2[$max])))))
				$mass3[] = "$key";
			
		$i = 0;
		
		foreach($mass[$max] as $key => $value)
		{
				if(@in_array("$value",$mass3))
					$i++;
			$mass4[$i][] = $this->data[$key];
			
		}
		foreach($mass4 as $key_interval => $value_interval)
		{
			foreach($value_interval as $key_object => $value_object)
			{
				for($i = 0; $i < count($value_object); $i++)
				{
					$mass5[$key_interval][$i][$value_object[$i]]+=1;
				}
			}
		}
		
		foreach($mass4 as $key_interval => $value_interval)
		{
			foreach($value_interval as $key_object => $value_object)
			{
				for($i = 0; $i < count($value_object); $i++)
				{
					ksort($mass5[$key_interval][$i]);
				}
			}
		}
		foreach($mass5 as $key => $value)
		{
			foreach($value as $k => $v)
			{
				if($k == $max) continue;
				$i = 0;
				foreach($v as $kk => $vv)
				{
					if($i == 0) {$vall = $vv;$i++;continue;}
					else{
						if((($vv-$vall) > 0) AND (($vv-$v[$kk+1]) >= 0))
						$mass6[$key][$k][$kk] = $vv;
						$vall = $vv;
						$i++;
					}
						
				}
			}
		}
		
		
		foreach($mass6 as $key => $value)
		{
			foreach($value as $k => $v)
			{
				foreach($v as $kk => $vv)
					if($vv/(max($v))*100 < 65)
						$mass7[$key][$k][$kk] = 0.5;
					else
						$mass7[$key][$k][$kk] = 1;
			}
		}
		
	
		
		foreach($mass5 as $key => $value)
		{
			foreach($value as $k => $v)
			{
				if($k == $max)
					foreach($v as $kk => $vv)
					{
						$mass8[$key][$kk] = $vv;
					}
					
			}
		
			foreach($mass8 as $k => $v)
			{
				$mass9[$k] = (max(array_keys($v))-min(array_keys($v)))/2+min(array_keys($v));
			}
		}
			
		
		foreach($mass7 as $key => $value)
		{
			foreach($value as $k => $v)
			{
				foreach($v as $kk => $vv)
				{
					foreach($value as $kes => $vas)
					{
						if($kes == $k) continue;
						foreach($vas as $kkk => $vvv)
						{
							if($vv == 1)
							{
							$arr[$k] = $kk;
							$arr[$kes] = $kkk;
							$arr[2] = $mass9[$key];
							$macc_centr[] = $arr;
								unset($value[$k][$kk]);
							}
						}
					}
				}
			}
		}
		$new_centr  = $this->transorm_centroid_init($macc_centr);
		$this->k = count($new_centr);
		$this->prev_centroid = $new_centr;
		$this->start_centroids = $new_centr;
		
		return array($max,$mass5,$new_centr);
	}
	//////////////////////////////////////////////////////////
	public function raspologenie_attr($obj = false)
	{
		if(!$obj)
			$data = $this->data;
		else
			$data = $obj;
		
		foreach($data as $index_data => $array_attr)
		{
			foreach($array_attr as $key_attr => $value_attr)
			{
				$attr[$key_attr][] = $value_attr;
			}
		}
		return $attr;
	}
	
	
	public function uniq_data()
	{
		$data = $this->raspologenie_attr();
		
		foreach($data as $key => $value)
		{
			$uniq[$key] = array_unique($value);
		}
		
		return $uniq;
	}
	
	//Метод вычисления максимального и минимальнрого значания данных
	public function min_max()
	{
		
		$attr = $this->raspologenie_attr();
		
		for($i = 0; $i < $this->count_attr; $i++)
		{
			$attr_min_max[$i]['min'] = min($attr[$i]);
			$attr_min_max[$i]['max'] = max($attr[$i]);
		}
		
		return $attr_min_max;

	}
	
	//Метод расчета количества интервалов
	public function count_interval()
	{
		$uniq_data = $this->uniq_data();
		$attr_min_max =  $this->min_max();
		for($i = 0; $i < $this->count_attr; $i++)
		{
			$step_interval[$i] = ($attr_min_max[$i]['max']-$attr_min_max[$i]['min'])/(1+3.322*log(count($uniq_data[$i])));
			$coun_interavl[$i] =  floor(($attr_min_max[$i]['max']-$attr_min_max[$i]['min'])/$step_interval[$i]);

		}
		
		return array($step_interval,$coun_interavl);
	}
	
	public function object_in_interval()
	{
		$data = $this->data;
		$attr_min_max =  $this->min_max();
		$max_interval = $this->count_interval();
		$delta = $max_interval[0];
		$coun_interavl = $max_interval[1];
		
		for($i = 0; $i < $this->count_attr; $i++)
		{	
			for($j = 0; $j <= $coun_interavl[$i]; $j++)
			{
				$interval[$i][$j] = $attr_min_max[$i]['min']+$delta[$i]*$j;
			}
			
			$interval[$i][] = $attr_min_max[$i]['max'];
			
		}
	
		
		$atribytu = $this->raspologenie_attr();
		
	
		
		foreach($atribytu as $key => $atribytus)
		{
			foreach($atribytus as $k => $v)
			{
				foreach($interval[$key] as $q => $z)
					if(($v >=  $interval[$key][$q]) AND ($v <= $interval[$key][$q+1]))
					{
						$counter[$key][$q] ++;
					}
			}
			ksort($counter[$key]);
		}
		return array($counter,$delta,$interval);
	}
	
	
	//расчет расстояния Евклидовое
	public function Evklid($point_1,$point_2)
	{
		$num_attributes = count($point_1);// Вычисления количества атрибутов объекта
		
		$summ = 0; //Инициализация переменной $summ ;
		
		
		//Рассчёт расстояние осуществляется Эвклидовым методом 
		//В данном цикле мы рассчитываем разницу между соответсвующеми координатами , затем  
		//возводим в квадрат и суммирует к квадратам разници остальных атрибутов 
		//Затем возвращаем корень квадратный из этой суммы.
		
		for($i = 0; $i < $num_attributes; $i++){
			
			$x = $point_1[$i] - $point_2[$i];//Рассчёт разници между координатами
			
			$summ = $x*$x + $summ;//Возведение в квадрат и суммировние с остальными квадратами разниц
		}
		
		$dist = sqrt($summ);// Извлечение корня квадратного
		
		return $dist;//Возврат расстояние
	}
	
	//Квадрат евклидова расстояния.
	public function sq_evklid($point_1,$point_2)
	{
		$num_attributes = count($point_1);// Вычисления количества атрибутов объекта
		
		$summ = 0; //Инициализация переменной $summ ;
		
		
		for($i = 0; $i < $num_attributes; $i++){
			
			$x = $point_1[$i] - $point_2[$i];//Рассчёт разници между координатами
			
			$summ = $x*$x + $summ;//Возведение в квадрат и суммировние с остальными квадратами разниц
		}
		
		return $summ;//Возврат расстояние
	}
	
	//раКвадрат евклидова расстояния.
	public function kvartal($point_1,$point_2)
	{
		$num_attributes = count($point_1);// Вычисления количества атрибутов объекта
		
		$summ = 0; //Инициализация переменной $summ ;
		
		for($i = 0; $i < $num_attributes; $i++){
			
			$x = abs($point_1[$i] - $point_2[$i]);//Рассчёт разници между координатами
			
			$summ = $x + $summ;//Возведение в квадрат и суммировние с остальными квадратами разниц
		}
		
		return $summ;//Возврат расстояние
	}
	
	
	//Расстояние Чебышева
	public function chebiwev($point_1,$point_2)
	{
		$num_attributes = count($point_1);// Вычисления количества атрибутов объекта
		
		$summ = 0; //Инициализация переменной $summ ;
		
		for($i = 0; $i < $num_attributes; $i++){
			
			$x = max(array($point_1,$point_2));//Рассчёт разници между координатами
			
			$summ = $x + $summ;//Возведение в квадрат и суммировние с остальными квадратами разниц
		}
		
		return $summ;//Возврат расстояние
	}
	
	//Метод расчета расстояние между двума точками
	public function distance($point_1,$point_2)
	{
		
		switch($this->metrics){
			case 'evklid' : return $this->Evklid($point_1,$point_2);break;
			case 'sq_evklid' : return $this->sq_evklid($point_1,$point_2);break;
			case 'kvartal' : return $this->kvartal($point_1,$point_2);break;
			case 'kvartal' : return $this->chebiwev($point_1,$point_2);break;
			default : return $this->Evklid($point_1,$point_2);
		}
		
		/*$num_attributes = count($point_1);// Вычисления количества атрибутов объекта
		
		$summ = 0; //Инициализация переменной $summ ;
		
		
		//Рассчёт расстояние осуществляется Эвклидовым методом 
		//В данном цикле мы рассчитываем разницу между соответсвующеми координатами , затем  
		//возводим в квадрат и суммирует к квадратам разници остальных атрибутов 
		//Затем возвращаем корень квадратный из этой суммы.
		
		for($i = 0; $i < $num_attributes; $i++){
			
			$x = $point_1[$i] - $point_2[$i];//Рассчёт разници между координатами
			
			$summ = $x*$x + $summ;//Возведение в квадрат и суммировние с остальными квадратами разниц
		}
		
		$dist = sqrt($summ);// Извлечение корня квадратного
		
		return $dist;//Возврат расстояние*/
	}
	
	
	//Метод привязки объектов к центроиду
	public function allocation()
	{
		$data = $this->normal_data;
		$centroid = $this->prev_centroid;
		$distance = array();
		
		for($i = 0; $i < $this->k;$i++)
		{
			$cluster[$i] = array();
			$clusters_summ[$i] = array();
		}
	
		foreach($data as $key => $value)
		{
			foreach($centroid as $key_centroid => $value_centroid)
			{
				$distance[$key][$key_centroid] = $this->distance($value,$value_centroid);
			}
			
			$clusters[array_keys($distance[$key],min($distance[$key]))[0]][$key] = $value;
			$clusters_summ[array_keys($distance[$key],min($distance[$key]))[0]][$key] = $distance[$key][array_keys($distance[$key],min($distance[$key]))[0]];
		}	

		if($this->method_polog_cluster == 'random')
		{
			if(count($clusters_summ) < $this->k)
			{
				$this->random();
				$this->allocation();
			}
		}
		
		foreach($centroid as $key => $value)
		{
			
			if($this->method_polog_cluster == 'random')
			{
				if($clusters_summ[$key] == null)
				{
					$this->random();
					$this->allocation();
				}
			}
			$clusters_summ[$key]['summ']=array_sum($clusters_summ[$key]);
		}
		
	
		$this->clusters = $clusters;
		
		$this->summ_distance[] = $clusters_summ;
	}
	
	
	//Метод перерасчета центров масс.
	public function recenter($data)
	{
		//$cluster = $this->clusters;
		$cluster = $data;
		
		foreach($cluster as $key => $value)
		{
			foreach($value as $k => $v)
			{
				for($i = 0; $i < $this->count_attr; $i++)
				{
					$centroid[$key][$i] += $v[$i];
				}
			}
			
			for($i = 0; $i < $this->count_attr; $i++)
			{
					$centroid[$key][$i] = $centroid[$key][$i]/count($cluster[$key]);
			}
		}
		
		//$this->centroid = $centroid;
		return $centroid;
	}
	
	
	public function getDataForPolygin(){
		//Данные интервалов : массив; 1 - кол-интервалов; 2- дельта: 3 - интвервалы;
		$data_from_interval = $this->object_in_interval();

		//Кол-во объектов в интервале
		$interval_quantity = $data_from_interval[0];


		//Дельта интвервалов
		$delta = $data_from_interval[1];

		//кол-интервалов
		$count_interval = $this->count_interval()[1];


		//Цико обхода интервалов всеъ атрибутов
		foreach($data_from_interval[2] as $key => $value)
		{
			$i = 0;
			//Цикл обхода интвервалов отдельного атрибутов
			foreach($value as $k => $v)
			{
				if($k == count($value)-1) continue;
				if(empty($interval_quantity[$key][$i]))
					$interval_quantity[$key][$i] = 0;
				//Фомирование массива, ключ - это интвервал; значение - это кол-во объектов в интвервале
				$intervalAndObject[$key][round($v,1)."-".round($value[$k+1],1)] = $interval_quantity[$key][$i++];
			}
		}

		//Добавляем в 0 и 1 в интервалы
		foreach($intervalAndObject as $key => $value)
		{
			array_unshift($intervalAndObject[$key],0);
			array_push($intervalAndObject[$key],0);
		}
		return $intervalAndObject;
	}
	
	//Преобразования нормализованных даных в их истиные значение
	public function transorm_data() // ($data[$index_data][$key_attr]+1)*($attr_min_max[$key_attr]['max']-$attr_min_max[$key_attr]['min'])+ $attr_min_max[$key_attr]['min']= $value_attr
	{
		$cluster = $this->clusters;
		$value_attr = $this->koef_normal;
	
		foreach($cluster as $key_cluster => $value_cluster)
		{
			foreach($value_cluster as $key_data => $value_data)
			{
				for($i = 0; $i < $this->count_attr; $i++)
				{
					switch($this->method_normal)
					{
						case 'zero_to_one' : 		$cluster[$key_cluster][$key_data][$i] = $value_data[$i]*(($value_attr[$i]['max']-$value_attr[$i]['min']))+$value_attr[$i]['min'];break;
						case 'm_one_to_one' : 		$cluster[$key_cluster][$key_data][$i] = ($value_data[$i]+1)*($value_attr[$i]['max']-$value_attr[$i]['min'])/2+ $value_attr[$i]['min'];break;
						case 'standard_deviation':  $cluster[$key_cluster][$key_data][$i] = $value_data[$i]*$value_attr[1][$i]+$value_attr[0][$i];break;
						case 'avarege_deviation' :  $cluster[$key_cluster][$key_data][$i] = $value_data[$i]*$value_attr[1][$i]+$value_attr[0][$i];break;
						case 'none' :   			$cluster[$key_cluster][$key_data][$i] = $value_data[$i];break;
					}
				}
			}
		}
		$this->transorm_cluster = $cluster;
	}
	
	public function transorm_centroid($obj = false)
	{
		if(!$obj)
			$cluster = $this->centroid;
		else
			$cluster = $obj;
		$value_attr = $this->koef_normal;
		$method = $this->method;
		
		foreach($cluster as $key_cluster => $value_cluster)
		{
			
				for($i = 0; $i < $this->count_attr; $i++)
				{
					switch($this->method_normal)
					{
						case 'zero_to_one' : 		$cluster[$key_cluster][$i] = $value_cluster[$i]*(($value_attr[$i]['max']-$value_attr[$i]['min']))+$value_attr[$i]['min'];break;
						case 'm_one_to_one' : 		$cluster[$key_cluster][$i] =  ($value_cluster[$i]+1)*($value_attr[$i]['max']-$value_attr[$i]['min'])+ $value_attr[$i]['min'];break;
						case 'standard_deviation':  $cluster[$key_cluster][$i] = $value_cluster[$i]*$value_attr[1][$i]+$value_attr[0][$i];break;
						case 'avarege_deviation' :  $cluster[$key_cluster][$i] = $$value_cluster[$i]*$value_attr[1][$i]+$value_attr[0][$i];break;
						case 'none' :   			$cluster[$key_cluster][$i] = $value_cluster[$i];break;
					}
				}     
		}
		$this->transorm_centroid = $cluster;
		
		return $cluster;
	}
	
	public function transorm_start_centroid()
	{
		$cluster = $this->start_centroids;
		$value_attr = $this->koef_normal;
		
		foreach($cluster as $key_cluster => $value_cluster)
		{
				for($i = 0; $i < $this->count_attr; $i++)
				{
					switch($this->method_normal)
					{
						case 'zero_to_one' : 		$cluster[$key_cluster][$i] = $value_cluster[$i]*(($value_attr[$i]['max']-$value_attr[$i]['min']))+$value_attr[$i]['min'];break;
						case 'm_one_to_one' : 		$cluster[$key_cluster][$i] = ($value_cluster[$i]+1)*($value_attr[$i]['max']-$value_attr[$i]['min'])+ $value_attr[$i]['min'];break;
						case 'standard_deviation':  $cluster[$key_cluster][$i] = $value_cluster[$i]*$value_attr[1][$i]+$value_attr[0][$i];break;
						case 'avarege_deviation' :  $cluster[$key_cluster][$i] = $$value_cluster[$i]*$value_attr[1][$i]+$value_attr[0][$i];break;
						case 'none' :   			$cluster[$key_cluster][$i] = $value_cluster[$i];break;
					}
				}
		}
		$this->start_centroids = $cluster;
	}
	
	
	
	function transorm_centroid_init($array)
	{
		$attr_min_max = $this->koef_normal;
	
		foreach($array as $index_data => $array_attr)
		{
			foreach($array_attr as $key_attr => $value_attr) 
			{
				switch($this->method_normal)
					{
						case 'zero_to_one' : 		$data[$index_data][$key_attr] = ($value_attr-$attr_min_max[$key_attr]['min'])/($attr_min_max[$key_attr]['max']-$attr_min_max[$key_attr]['min']);break;
						case 'm_one_to_one' : 		$data[$index_data][$key_attr] = 2*(($value_attr-$attr_min_max[$key_attr]['min']))/($attr_min_max[$key_attr]['max']-$attr_min_max[$key_attr]['min'])-1;break;
						case 'standard_deviation':  $data[$index_data][$key_attr] = ($value_attr-$attr_min_max[0][$key_attr])/$attr_min_max[1][$key_attr];break;
						case 'avarege_deviation' :  $data[$index_data][$key_attr] = ($value_attr-$attr_min_max[0][$key_attr])/$attr_min_max[1][$key_attr];break;
						case 'none' :   			$data[$index_data][$key_attr] = $value_attr;break;
					}
				
			}
		}
			$this->prev_centroid = $data;
			$this->start_centroids = $data;
			return $data;
	}
	
	///Расчет Индекса ДАНА
	function distance_for_data($data,$key,$array,$distance)
	{
		foreach($data as $ket => $cluster)
		{
			if($ket == $key) continue;
			foreach($array as $kkk => $vvv)
				foreach($cluster as $kk => $vv)
				{
					$distance[$kk."-".$kkk] =  $this->distance($vv,$vvv);
				}
		}
		return $distance;
	}
	
	
	function min_dist_dana($data)
	{
		$dist = array();
		foreach($data as $ket => $cluster)
		{
			$dist = $this->distance_for_data($data,$ket,$cluster,$dist);
		}
		$max_disance[array_keys($dist, min($dist))[0]] = min($dist);
		return min($dist);
	}
		
	public function Dana($data)
	{
		foreach($data as $key => $cluster)
		{
			foreach($cluster as $k => $v)
			{
				foreach($cluster as $ke => $val)
				{
					if($k == $ke) continue;
					$distance_in_cluster[($ke+1)."-".($k+1)] = $this->distance($v,$val);
				}
			}
				$max_disance[array_keys($distance_in_cluster, max($distance_in_cluster))[0]] = max($distance_in_cluster);
				unset($distance_in_cluster);
		}
			$max = max(array_unique($max_disance));
			$indexDana = $this->min_dist_dana($data)/$max;
			return array($indexDana,'Индекс Дана');
	}
	//Завершение расчета индекса данна
	
	
	//Индес силуэта
	public function Siluet($data)
	{
		$summ = 0;
		foreach($data as $key_cluster => $element_cluster)
		{
			foreach($element_cluster as $key => $value)
			{
				$b = $this->avarage_distance_other_cluster($value,$key_cluster,$data);
				$a = $this->avarage_distance_self_cluster($value,$key,$element_cluster);
				$sw = ($b - $a )/$this->maxim($a,$b);
				$summ+=$sw;
			}
		}
		return array($summ/$this->count_data,'Индекс оценки силуэта');
	}
	
	private function avarage_distance_self_cluster($point,$key,$cluster)
	{
		$summ = 0;
		foreach($cluster as $keys => $value)
		{
			if($key == $keys) continue;
			$summ+= $this->distance($point,$value);
		}
		return $summ/(count($cluster)-1);
	}
	
	private function avarage_distance_other_cluster($point,$key,$clusters)
	{
		
		foreach($clusters as $k => $v)
		{
			$summ = 0;
			if($key == $k) continue;
			
			foreach($v as $kk => $vv)
			{
				$summ+= $this->distance($point,$vv);
			}
			$s[] = $summ/count($clusters[$k]);
			
		}
		return min($s);
	}
	
	private function maxim($n1,$n2)
	{
		if($n1 > $n2) return $n1; return $n2;
	}
	//Завершение индекса силуэта
	
	
	//Score Function 
	public function SC($cluster,$centroid)
	{
		$bcd = $this->getBCD($cluster,$centroid);
		$wcd = $this->getWCD($cluster,$centroid);
		$c = exp(($bcd-$wcd)/100);
		$c2 = exp($c);
		$c3 = 1/$c2;
		$SF = 1-$c3;
		return array($SF,'Score Function');
	}
	
	private function getBCD($cluster,$centroid)
	{
		$priznak = array();
		foreach($centroid as $key => $value)
		{
			foreach($value as $k => $v)
				$priznak[$k]+= $v;
		}
		$centr_centroids = array();
		
		foreach($priznak as $k => $v)
			$centr_centroids[$k] = $v/count($centroid);//Центр Центроидов
			
		foreach($centroid as $key => $value)
		{
			$cd+= ($this->distance($value,$centr_centroids)*(count($cluster[$key])));
		}
		$bcd = $cd/(count($this->normal_data)*(count($centroid)));
		return $bcd;
	}
	
	private function getWCD($clusters,$centroid)
	{
		$summa_dist_in_1_cluster = array();
		$wcd = 0;
		
		foreach($clusters as $key_cluster => $value_cluster)
		{
			foreach($value_cluster as $k => $v)
				$summa_dist_in_1_cluster[$key_cluster] += $this->distance($v,$centroid[$key_cluster]);
		}
		
		foreach($summa_dist_in_1_cluster as $key_cluster => $value)
			$wcd+= $value/count($clusters[$key_cluster]);
		
		return $wcd;
	}
	
	//Конец Score Function
	
	
	
	//Index MB
	public function MB($cluster,$centroid)
	{
		$data = $this->normal_data;
		$summ_cluster = 0;
		foreach($cluster as $key => $value)
		{
			foreach($value as $k => $v)
			{
				$summ_cluster+= $this->distance($v,$centroid[$key]);
			}
		}
		
		$summ_centre = 0;
		
		foreach($centroid as $key => $value)
		{
			foreach($data as $k => $v)
				$summ_centre+=  $this->distance($value,$v);
		}
		
		$max_dist = $this->max_dist_dana($cluster);
		
		$MB = pow((($summ_centre*$max_dist)/($summ_cluster*$this->k)),2);
		return array($MB,'Индекс Maulik-Bandoypadhyay');
	}
	
	function max_dist_dana($data)
	{
		$dist = array();
		foreach($data as $ket => $cluster)
		{
			$dist = $this->distance_for_data($data,$ket,$cluster,$dist);
		}
		$max_disance[array_keys($dist, max($dist))[0]] = max($dist);
		return max($dist);
	}
	
	//Конец Index MB
	
	//INDEX VNND
	public function VNND($cluster)
	{
		$summ = 0;
		foreach($cluster as $key => $value)
		{
			$v = $this->getBeanSosed($value);
			$summ+= $v;
		}
		return array($summ,'VNND индекс');
	}
	
	
	public function getBeanSosed($cluster)
	{
		$summ = 0;
		foreach($cluster as $key => $value)
		{
			foreach($cluster as $keys => $values)
			{
				if($key == $keys) continue;
				$distance_for_point[$keys] = $this->distance($value,$values);
			}
			$distance_min_sosed[$key."-".array_keys($distance_for_point,min($distance_for_point))[0]] = min($distance_for_point);
			$summ+= min($distance_for_point);
		}
		
		$avarege_distance = $summ/count($cluster);
		
		$summ = 0;
		
		foreach($distance_min_sosed as $key => $value)
		{
			$summ+= ($value - $avarege_distance)*($value - $avarege_distance);
		}
		
		return $summ/((count($cluster) -1));
	}
	
	//Index CDbw
	public function SDbw($cluster,$centroids)
	{
        $data = $this->normal_data;
		$combine = $this->getCombine($cluster);
		
		
		foreach($combine as $key => $value)
		{
			$keys = explode('-',$value);
			$RCR[$value] = $this->getRCR2($cluster[$keys[0]],$cluster[$keys[1]],$centroids[$keys[0]],$centroids[$keys[1]]);
		}
		
		
		foreach($cluster as $key => $value)
		{
			$dispersiya[$key] = $this->dispersiya($value,$centroids[$key]);
		}
		
		$stdev = $this->stdev($dispersiya);
		
		
		$DENS = $this->getAllDens($cluster,$stdev,$RCR);
		
		
        $InertDens = $this->InertDens($DENS);
		
        $Dist = $this->Dist($cluster,$RCR);
		
        $Sep = $this->Sep($Dist,$InertDens);
		
        $Intra_Dens = $this->Intra_Dens($RCR,$cluster,$centroids,$stdev,$data);
		
        $Compactness = $this->getCompactness($Intra_Dens);
        $Intra_change = $this->getIntraChange($Intra_Dens);
        $Cohension =  $Compactness/(1+$Intra_change);
        $CDbw = $Sep*$Cohension*$Compactness;
		return array($CDbw,'Индекс плотности CDbw');
        return $CDbw;
	}
	
    
    private function getIntraChange($Intra_Dens)
    {
        $summ = 0;
        foreach($Intra_Dens as $key => $value)
        {
            $summ+= abs($value - $Intra_Dens[$key-1]);
        }

        return $summ/(count($Intra_Dens)-1);
    }
    
    private function getCompactness($Intra_Dens)
    {
        $summ = 0;
        foreach($Intra_Dens as $value)
        {
            $summ+= $value;
        }
        return $summ/count($Intra_Dens);
    }
    
    
	private function getCombine($clusters)
	{
		$array = array();
		$keys = array_keys($clusters);
		foreach($keys as $k => $v)
		{
			foreach($keys as $ke => $vv)
			{
				if($ke == $k) continue;
				$array[] = $k."-".$ke;
				unset($keys[$k]);
			}
		}
		return $array;
	}
	
	private function getRCR2($cluster_1,$cluster_2,$centroids_1,$centroids_2)
    {
       $min_object =  16;
       $min_objects = (count($cluster_1) > count($cluster_2)) ? count($cluster_2) : count($cluster_1);
	   if($min_objects < $min_object)
		   $min_object = $min_objects;
		
		
		$i = 0;
        
       foreach($cluster_1 as $key1 => $value1)
       {
           $dist[$key1] = $this->distance($value1,$centroids_1);
       }
       
       $key_1 = array_keys($dist,max($dist))[0];
      
        foreach($cluster_2 as $key2 => $value1)
        {
           $distt[$key2] = $this->distance($value1,$centroids_2);
        }
        
        
       $key_2 = array_keys($distt,max($distt))[0];
       $cr[$key_1."-".$key_2] = $this->distance($cluster_1[$key_1],$cluster_2[$key_2]);//Первая  пара
       
        
        $distance_1 = 0;
        $distance_2 = 0;
        
        
         
         $coorde_1 = $cluster_1[$key_1];
         $coorde_2 = $cluster_2[$key_2];
         
        unset($cluster_1[$key_1]);
        unset($cluster_2[$key_2]);
		
        while(true)
		{
            if($i == 0)
            {
                foreach($cluster_1 as $key1 => $value1)
                {
                    $dist[$key1] = $this->distance($value1,$coorde_1)+$distance_1;
                }
             
                
                $distance_1 += max($dist);
                $key_1 = array_keys($dist,max($dist))[0];
               
                
                
                foreach($cluster_2 as $key2 => $value1)
                {
                   $distt[$key2] = $this->distance($value1,$coorde_2)  + $distance_2;
                }
                
                $distance_2 += max($distt);
                $key_2 = array_keys($distt,max($distt))[0];
               
                
                
                $cr[$key_1."-".$key_2] = $this->distance($cluster_1[$key_1],$cluster_2[$key_2]);
                $i++;
                 unset($cluster_1[$key_1]);
                unset($cluster_2[$key_2]);
            }else
            {
                foreach($cluster_1 as $key1 => $value1)
                {
                    $dist[$key1] = $this->distance($value1,$cluster_1[$key_1])+$distance_1;
                }
                
                $distance_1 += max($dist);
                $key_1 = array_keys($dist,max($dist))[0];
               
                
                
                foreach($cluster_2 as $key2 => $value1)
                {
                   $distt[$key2] = $this->distance($value1,$cluster_2[$key_2])  + $distance_2;
                }
                
                $distance_2 += max($distt);
                $key_2 = array_keys($distt,max($distt))[0];
               
                
                
                $cr[$key_1."-".$key_2] = $this->distance($cluster_1[$key_1],$cluster_2[$key_2]);
           
            
                unset($cluster_1[$key_1]);
                unset($cluster_2[$key_2]);

                $i++;
            }
            
            
			if($i == ($min_object-1)){ 
				return $cr;
			}
		}
    }
    
    
    
    
    
	/*private function getRCR($cluster_1,$cluster_2)
	{
		$min_object = (count($cluster_1) > count($cluster_2)) ? count($cluster_2) : count($cluster_1);
		
		$i = 0;
		
		while(true)
		{
			foreach($cluster_1 as $key1 => $value1)
			{
				foreach($cluster_2 as $key2 => $value2)
				{
					$cr[$key1."-".$key2] = $this->distance($value1,$value2);
				} 
			}
			
			$min_keys = array_keys($cr,min($cr))[0];			
			
			$kkk = explode("-",$min_keys);
			
			$crc[$kkk[0]."-".$kkk[1]] = $cr[$kkk[0]."-".$kkk[1]];
			$i++;
			unset($cr);
			
			if($i == ($min_object-1)){ 
             
				return $crc;
			}
			else{
				unset($cluster_1[$kkk[0]]);
				unset($cluster_2[$kkk[1]]);
			}
		}
	}*/
	
	
	
	private function dispersiya($cluster,$centroid)
	{
		$summ  = 0;
		foreach($cluster as $key => $value)
		{
			$summ+= $this->distance($value,$centroid);
		}
		return $summ/(count($cluster));
	}
	
	
	
	private function stdev($dispersiya)
	{
		$summ = 0;
		foreach($dispersiya as $v)
		{
			$summ+=$v;
		}
		
		return $summ/(count($dispersiya));
	}
    
    
	
	private function getAllDens($clusters,$stdev,$RCR)
	{
		
        $i = 0;
		foreach($clusters as $key_cluster => $cluster)
		{
			foreach($RCR as $k => $v)
			{
				$points = explode("-",$k);
				$point_1 = $points[0];
				$point_2 = $points[1];
				if(($point_1 == $key_cluster) OR ($point_2 == $key_cluster))
				{
					$keys = ($point_1 == $key_cluster) ? $point_2 : $point_1;
					$DENS[$key_cluster][$key_cluster."-".$keys] = $this->getDens($cluster,$clusters[$keys],$stdev,$v);
				}
			}
		}
            return $DENS;    
	}
	
	private function getUk($data,$combinate)
	{
		$points = explode("-",$combinate);
		$point_1 = $data[$points[0]];
		$point_2 = $data[$points[1]];
		for($i = 0; $i < $this->count_attr; $i++)
			$uk[$i] = ($point_1[$i]+$point_2[$i])/2;
		return $uk;
	}
	
	
	private function getDens($cluster_1,$cluster_2,$stdev,$combination)
	{
		$data = $this->normal_data;
		$summ = 0;
		
		foreach($combination as $key => $value)
		{
			$cardinality = $this->cardinality($cluster_1,$cluster_2,$stdev,$key,$data);
			$summ+= ($value)/(2*$stdev)*$cardinality;
		}
		
        
		return $summ/(count($combination));
		
	}
	
	private function cardinality($cluster_1,$cluster_2,$stdev,$combinate,$data)
	{
		$summ = 0;
		$uk = $this->getUk($data,$combinate);
		foreach($cluster_1 as $key => $value)
			if(($this->distance($value,$uk)) <= $stdev)
				$summ++;
           return $summ;
	}
    
    private function InertDens($DENS)
    {
        $summ = 0;
        foreach($DENS as $key => $value)
        {
           $summ+= max($value);
        }
        return $summ/(count($DENS));
    }
    
    private function Dist($clusters,$RCR)
    {
        foreach($clusters as $key_cluster => $cluster)
		{
			foreach($RCR as $k => $v)
			{
				$points = explode("-",$k);
				$point_1 = $points[0];
				$point_2 = $points[1];
				if(($point_1 == $key_cluster) OR ($point_2 == $key_cluster))
				{
					$keys = ($point_1 == $key_cluster) ? $point_2 : $point_1;
                    $summa = 0;
                    foreach($v as $kee => $vvv)
                    {
                         $summa+= $vvv;
                    }     
                    $Dist[$key_cluster][$keys] = $summa;
				}
			}
		}
        return $Dist;
    }
    
    private function Sep($Dist,$InertDens)
    {
        $summa = 0;
        
        foreach($Dist as $key => $value)
        {
            $summa+=  min($value);
        }
        return (($summa/count($Dist))/(1+$InertDens));
    
    }
    
    private function Intra_Dens($RCR,$cluster,$centroids,$stdev,$data)
    {
        $s = 1; 
        $s_end = 8;
        
        for($i = $s; $i <= $s_end; $i++)
        {
            $intra_dens[$i] = $this->getCurrentIntraDens($RCR,$cluster,$centroids,$stdev,$data,$i);
        }
		
          return $intra_dens;
    }
    
    private function getCurrentIntraDens($RCR,$cluster,$centroids,$stdev,$data,$i)
    {
         foreach($RCR as $key => $value)
         {
             $n = count($value);
             $clusters = explode("-",$key);
             
             
             foreach($value as $k => $v)
             {
                 $points = explode("-",$k);
                 $clus[$clusters[0]][$points[0]] = $data[$points[0]];
                 $clus[$clusters[1]][$points[1]] = $data[$points[1]];
             }
         }
		  
         
         $shift_points = $this->getShiftPoints($clus,$centroids,$i);
        
        foreach($shift_points as $key => $value)
        {
            foreach($value as $k => $v)
            $summ+= $this->getSecondCardinality($cluster[$key],$stdev,$v);
        }
		
       return $summ/$n;
    }
    
    private function getShiftPoints($clus,$centroids,$i)
    {
		
        foreach($clus as $key => $value)
        {
            foreach($value as $k => $v)
            {
                for($j = 0; $j < count($v); $j++)
                {
                    $new_clus[$key][$k][$j] = $v[$j]+$i/10*($centroids[$key][$j]-$v[$j]);
                }
            }
        }
        
       return $new_clus;
        
    }
    
    private function getSecondCardinality($cluster,$stdev,$shift_point)
    {
        $summ = 0;
   
        foreach($cluster as $key => $value)
        {
            if($this->distance($value,$shift_point) <= $stdev)
                $summ++;
        }
        return $summ/count($cluster);
    }
    
    
    
   /* private function Intra_Dens($RCR,$cluster,$centroids,$stdev,$data)
    {
        $s = 0.1;
        $s_end = 0.8;
       
       
        for($i = 1; $i <= 10; $i++)
        {
            foreach($RCR as $key => $value)
            {
                $centrs = explode("-",$key);
                
                foreach($value as $k => $v)
                {
                     $points = explode("-",$k);
                     
                     $point_1 = $data[$points[0]];
                     $point_2 = $data[$points[1]];
                     
                     $count_attribute = count($point_1);
                     
                     for($j = 0 ; $j < $count_attribute; $j++)
                     {
                            $new_first_point[$j] = $point_1[$j]+$i/10*($centroids[$centrs[0]][$j]-$point_1[$j]);
                     }
                     for($j = 0 ; $j < $count_attribute; $j++)
                     {
                            $new_second_point[$j] = $point_2[$j]+$i/10*($centroids[$centrs[1]][$j]-$point_2[$j]);
                     }
                     $RCR_S[$i][$key][$k] = $this->distance($new_first_point,$new_second_point);
                }
            }
        }
        echo "<pre>";;
        print_r($RCR_S);
       // return $RCR_S;
    }*/
}
?>