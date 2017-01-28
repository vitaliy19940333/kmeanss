<?php

class Controller_Admin extends Controller
{
	
	/*
	В конструкторе создаем модель для работы с пользователями
	И очищаем устаревшие сессии
	А так же проверям права доступа
	Если зашел не админ,то инклюдим шаблон ошибки
	*/
	public function __construct()
	{
		parent::__construct();
		$this->model = Model_Users::Instance();
	}
	public function __destruct()
	{
		
	}
	
	/*
	Инклюдим меню администратора
	*/
	public function action_index()
	{
			$this->view->generate('admin/about_menu.php', 'template_view.php');
	}
	

	
	/*
		Выводим страницу "Слово заведующего кафедры" на редакцию
	*/
	public function action_head()
	{
		$model = new Model_Page();
		
		if($this->isPost())
		{
			$data['lang_ua'] = trim($_POST['consist_ua']);
			$data['lang_ru'] = trim($_POST['consist_ru']);
			
			$model->Update($data['lang_ru'],$data['lang_ua'],'head');
		}

		$data = $model->Get("head");
		
		$this->view->generate('admin/edit_page.php', 'template_view.php',$data);
	}
	
	public function action_contacts()
	{
		$model = new Model_Page();
		
		if($this->isPost())
		{
			$data['lang_ua'] = trim($_POST['consist_ua']);
			$data['lang_ru'] = trim($_POST['consist_ru']);
			
			$model->Update($data['lang_ru'],$data['lang_ua'],'contactss');
		}

		$data = $model->Get("contactss");
		
		$this->view->generate('admin/edit_page.php', 'template_view.php',$data);
	}
	
	public function action_development()
	{
		$model = new Model_Page();
		
		if($this->isPost())
		{
			$data['lang_ua'] = trim($_POST['consist_ua']);
			$data['lang_ru'] = trim($_POST['consist_ru']);
			
			$model->Update($data['lang_ru'],$data['lang_ua'],'development');
		}

		$data = $model->Get("development");
		
		$this->view->generate('admin/edit_page.php', 'template_view.php',$data);
	}
	
	public function action_text_projects()
	{
		$model = new Model_Page();
		
		if($this->isPost())
		{
			$data['lang_ua'] = trim($_POST['consist_ua']);
			$data['lang_ru'] = trim($_POST['consist_ru']);
			
			$model->Update($data['lang_ru'],$data['lang_ua'],'projects');
		}

		$data = $model->Get("projects");
		
		$this->view->generate('admin/edit_page.php', 'template_view.php',$data);
	}
	
	
	public function action_concept()
	{
		$model = new Model_Page();
		
		if($this->isPost())
		{
			$data['lang_ua'] = trim($_POST['consist_ua']);
			$data['lang_ru'] = trim($_POST['consist_ru']);
			
			$model->Update($data['lang_ru'],$data['lang_ua'],'concept');
		}

		$data = $model->Get("concept");
		
		$this->view->generate('admin/edit_page.php', 'template_view.php',$data);
	}

	/*
		Выводим страницу "История кафедры" на редакцию
	*/
	public function action_history()
	{
		$model = new Model_Page();
		
		if($this->isPost())
		{
			$data['lang_ua'] = trim($_POST['consist_ua']);
			$data['lang_ru'] = trim($_POST['consist_ru']);

			$model->Update($data['lang_ru'],$data['lang_ua'],'history');
			
		}

		$data = $model->Get("history");

		$this->view->generate('admin/edit_page.php', 'template_view.php',$data);
	}
	
	
	//Состав кафедры
	public function action_consist()
	{
		$model = new Model_Employee();
		
		//Проверяем каким методом пришли сюда
		if($this->isPost())
		{
			//Принимаем данныес с $_POST
			$data['name_ru'] = $_POST['name_employee_ru'];
			$data['name_ua'] = $_POST['name_employee_ua'];
			$data['category'] = $_POST['category'];
			$data['description_ru'] = $_POST['biography_employee_ru'];
			$data['description_ua'] = $_POST['biography_employee_ua'];
			
			//Проверяем на заполняемость поля
			if(!((trim($_POST['name_employee_ru'])=='') or (trim($_POST['name_employee_ua'])=='') or (trim($_POST['category'])=='') or (trim($_POST['biography_employee_ru'])=='') or (trim($_POST['biography_employee_ua'])=='')))
			{
				//Если добавляем
				if(!empty($_POST['insert']))
				{
					if(!is_array(($result=Model_Image::upload($_FILES['photo_employee']))))
						$data['massage'] = $result;
					else
					{
						$data['image'] = $result[1];
						if($model->Insert($data,'composition_department')){
							$data['name_ru'] ="";
							$data['name_ua'] = "";
							$data['category'] = "";
							$data['image'] = "";
							$data['description_ru'] ="";
							$data['description_ua'] = "";
						}
					}
				}
				
				//Если редактируем
				elseif(!empty($_POST['edit']))
				{
					//id в $_POST && $_GET == true ?
					if($_POST['item_id'] == route::$param['edit'])
					{
						if(is_array(($result=Model_Image::upload($_FILES['photo_employee_new']))))
							$data['image'] = $result[1];
						else 
							$data['image'] = $_POST['item_image'];
						
							if($model->Update('composition_department',$_POST['item_id'],$data))
								header("LOCATION: /");
							else $data['massage'] = 'При измененим произошла ошибка, обратитись к разработчику';
							
					}else{
						header("LOCATION: /");
					}
				}
				elseif(!empty($_POST['delete']))
				{
					if($_POST['item_id'] == route::$param['edit'])
					{
						if($model->Delete('composition_department',$_POST['item_id']))
							header("LOCATION: /");
						else 
							$data['massage'] = 'При удалении произошла ошибка, обратитись к разработчику';
					}
				}
			}else
				$data['massage'] = 'Заполните все поля';
		}
		
		if(!empty(route::$param['edit']))
		{
			$data['info'] = $model->One('composition_department',route::$param['edit']);
			$this->view->generate('admin/consist_edit.php', 'template_view.php',$data);
		}
		else{
			$data['all']= $model->Get('composition_department');
			$this->view->generate('admin/consist.php', 'template_view.php',$data);
		}
	}
	
	
	//Выпускники
	public function action_graduate()
	{
		$model = new Model_Employee();
		
		//Проверяем каким методом пришли сюда
		if($this->isPost())
		{
			//Принимаем данныес с $_POST
			$data['name_ru'] = $_POST['grad_name_ru'];
			$data['name_ua'] = $_POST['grad_name_ua'];
			$data['description_ru'] = $_POST['grad_employee_ru'];
			$data['description_ua'] = $_POST['grad_employee_ua'];
			
			//Проверяем на заполняемость поля
			if(!((trim($_POST['grad_name_ru'])=='') or (trim($_POST['grad_name_ua'])=='') or (trim($_POST['grad_employee_ru'])=='') or (trim($_POST['grad_employee_ua'])=='')))
			{
				//Если добавляем
				if(!empty($_POST['insert']))
				{
					if(!is_array(($result=Model_Image::upload($_FILES['photo_grad']))))
						$data['massage'] = $result;
					else
					{
						$data['image'] = $result[1];
						if($model->Insert($data,'outstanding_graduates')){
							$data['name_ru'] ="";
							$data['name_ua'] = "";
							$data['image'] = "";
							$data['description_ru'] ="";
							$data['description_ua'] = "";
						}
					}
				}
				
				//Если редактируем
				elseif(!empty($_POST['edit']))
				{
					//id в $_POST && $_GET == true ?
					if($_POST['item_id'] == route::$param['edit'])
					{
						if(is_array(($result=Model_Image::upload($_FILES['photo_grad']))))
							$data['image'] = $result[1];
						else 
							$data['image'] = $_POST['image'];
							if($model->Update('outstanding_graduates',$_POST['item_id'],$data))
								header("LOCATION: /");
							else $data['massage'] = 'Вы не изменили данные или произошла ошибка, обратитись к разработчику';
							
					}else{
						header("LOCATION: /");
					}
				}
				elseif(!empty($_POST['delete']))
				{
					if($_POST['item_id'] == route::$param['edit'])
					{
						if($model->Delete('outstanding_graduates',$_POST['item_id']))
							header("LOCATION: /");
						else 
							$data['massage'] = 'При удалении произошла ошибка, обратитись к разработчику';
					}
				}
			}else
				$data['massage'] = 'Заполните все поля';
		}
		
		if(!empty(route::$param['edit']))
		{
			$data['info'] = $model->One('outstanding_graduates',route::$param['edit']);
			$this->view->generate('admin/graduate_edit.php', 'template_view.php',$data);
		}
		else{
			$data['all']= $model->Get('outstanding_graduates');
			$this->view->generate('admin/graduate.php', 'template_view.php',$data);
		}
	}
	
	public function action_courses()
	{
		$model = new Model_Courses();
		
		if($this->isPost())
		{
			if( (trim($_POST['title_ru']) == '') or (trim($_POST['title_ua']) == '') )
			{
				$data['massage'] == 'Заполните все поля';
			}else{
				$data['lang_ru'] = $_POST['title_ru'];
				$data['lang_ua'] = $_POST['title_ua'];
				$data['category'] = $_POST['category'];
				$data['programm'] = $_POST['programm'];
				$model->Insert($data,'disciplines');
			}
		}
		if(isset(route::$param['delete']))
		{
			$model->Delete('disciplines',route::$param['delete']);
		}
		$data['category'] = $model->category();
		$data['programm'] = $model->programm();
		$data['disciplines'] = $model->getDiscipline();
		$this->view->generate('admin/courses.php','template_view.php',$data);
	}
	
	//Консультация
	public function action_consultations()
	{
		$model = Model_Msql::Instance();
		if($this->isPost())
		{
			$data['name_ru'] = $_POST['name_ru'];
			$data['name_ua'] = $_POST['name_ua'];
			$data['lang_ru'] = $_POST['contain_ru'];
			$data['lang_ua'] = $_POST['contain_ua'];
			if(!((trim($_POST['name_ru'])=='') or (trim($_POST['name_ua'])=='') or (trim($_POST['contain_ru'])=='') or (trim($_POST['contain_ua'])=='')))
			{
				if(isset($_POST['insert'])){
					$data['pubdate'] = time();
					if($model->Insert('consultation',$data))
					{
						$data['name_ru'] = "";
						$data['name_ua'] = "";
						$data['lang_ru'] = "";
						$data['lang_ua'] = "";
					}
				}
				elseif(isset($_POST['edit']))
				{
					if(route::$param['edit'] == $_POST['item_id'])
					{
						$id = intval(route::$param['edit']);
						if($model->Update('consultation',$data,"id='$id'"))
							header("LOCATION: /admin/consultations/");
					}
				}
				elseif(isset($_POST['delete']))
				{
					$id = intval(route::$param['edit']);
					if($model->Delete('consultation',"id='$id'"))
						header("LOCATION: /admin/consultations/");
				}

			}else
				$data['massage'] = 'Заполните все поля';
		}
		if(isset(route::$param['edit']))
		{
			$id = intval(route::$param['edit']);
			$data['post'] = $model->Select("Select * FROM consultation where id='$id'");
			$data['post'] = $data['post'][0];
			$this->view->generate('admin/consultations_adit.php','template_view.php',$data);
		}else{
			$data['all'] = $model->Select("Select * FROM consultation ORDER BY id DESC");
			$this->view->generate('admin/consultations.php','template_view.php',$data);
		}
	}
	
	
	//Заочная форма обучения
	public function action_extramural()
	{
		$model = new Model_Page();
		
		if($this->isPost())
		{
			$data['lang_ua'] = trim($_POST['consist_ua']);
			$data['lang_ru'] = trim($_POST['consist_ru']);

			$model->Update($data['lang_ru'],$data['lang_ua'],'extramural');
			
		}

		$data = $model->Get("extramural");

		$this->view->generate('admin/edit_page.php', 'template_view.php',$data);
	}
	
	
	
	//Методички
	public function action_materials()
	{
		$model = new Model_Materials();		
		if($this->isPost())
		{
			if((trim($_POST['title_ru'])=='') or (trim($_POST['title_ua'])==''))
				$data['massage'] = 'Заполните все поля';
			else{
				if(!is_array($res=$model->upload($_FILES['material'])))
					$data['massage'] = $res;
				else{
					$data['title_ru'] = $_POST['title_ru'];
					$data['title_ua'] = $_POST['title_ua'];
					$data['disciplines'] = $_POST['descipline'];
					$data['link'] = $res['1'];
					if($model->Insert('materials',$data))
					{
						$data['title_ru'] ='';
						$data['title_ua'] = '';
					}else{
						$data['massage'] = 'Ошибка при добавлении, обратитись к разработчику';
					}
				}
				
			}
		}
		if(route::$param['delete'])
			$model->Delete(route::$param['delete']);
		
		$data['disciplines'] = $model->discipline();
		$data['materials'] = $model->getMaterials("SELECT * FROM materials");
		$this->view->generate('admin/materials.php','template_view.php',$data);
	}
	
	//Лаборатории
	public function action_laboratory()
	{
		$model = Model_Msql::Instance();
		if($this->isPost())
		{
			$data['name_ru'] = $_POST['name_ru'];
			$data['name_ua'] = $_POST['name_ua'];
			$data['lang_ru'] = $_POST['contain_ru'];
			$data['lang_ua'] = $_POST['contain_ua'];
			if(!((trim($_POST['name_ru'])=='') or (trim($_POST['name_ua'])=='') or (trim($_POST['contain_ru'])=='') or (trim($_POST['contain_ua'])=='')))
			{
				if(isset($_POST['insert'])){
					if($model->Insert('laboratory',$data))
					{
						$data['name_ru'] = "";
						$data['name_ua'] = "";
						$data['lang_ru'] = "";
						$data['lang_ua'] = "";
					}
				}
				elseif(isset($_POST['edit']))
				{
					if(route::$param['edit'] == $_POST['item_id'])
					{
						$id = intval(route::$param['edit']);
						if($model->Update('laboratory',$data,"id='$id'"))
							header("LOCATION: /admin/laboratory/");
					}
				}
				elseif(isset($_POST['delete']))
				{
					$id = intval(route::$param['edit']);
					if($model->Delete('laboratory',"id='$id'"))
						header("LOCATION: /admin/laboratory/");
				}

			}else
				$data['massage'] = 'Заполните все поля';
		}
		if(isset(route::$param['edit']))
		{
			$id = intval(route::$param['edit']);
			$data['post'] = $model->Select("Select * FROM laboratory where id='$id'");
			$data['post'] = $data['post'][0];
			$this->view->generate('admin/consultations_adit.php','template_view.php',$data);
		}else{
			$data['all'] = $model->Select("Select * FROM laboratory ORDER BY id DESC");
			$this->view->generate('admin/laboratory.php','template_view.php',$data);
		}
	}
	
	
	public function action_projects()
	{
		$model = Model_Msql::Instance();
		if($this->isPost())
		{
			$data['name_ru'] = $_POST['name_ru'];
			$data['name_ua'] = $_POST['name_ua'];
			$data['lang_ru'] = $_POST['contain_ru'];
			$data['lang_ua'] = $_POST['contain_ua'];
			if(!((trim($_POST['name_ru'])=='') or (trim($_POST['name_ua'])=='') or (trim($_POST['contain_ru'])=='') or (trim($_POST['contain_ua'])=='')))
			{
				if(isset($_POST['insert'])){
					if($model->Insert('projects',$data))
					{
						$data['name_ru'] = "";
						$data['name_ua'] = "";
						$data['lang_ru'] = "";
						$data['lang_ua'] = "";
					}
				}
				elseif(isset($_POST['edit']))
				{
					if(route::$param['edit'] == $_POST['item_id'])
					{
						$id = intval(route::$param['edit']);
						if($model->Update('projects',$data,"id='$id'"))
							header("LOCATION: /admin/projects/");
					}
				}
				elseif(isset($_POST['delete']))
				{
					$id = intval(route::$param['edit']);
					if($model->Delete('projects',"id='$id'"))
						header("LOCATION: /admin/projects/");
				}

			}else
				$data['massage'] = 'Заполните все поля';
		}
		if(isset(route::$param['edit']))
		{
			$id = intval(route::$param['edit']);
			$data['post'] = $model->Select("Select * FROM projects where id='$id'");
			$data['post'] = $data['post'][0];
			$this->view->generate('admin/consultations_adit.php','template_view.php',$data);
		}else{
			$data['all'] = $model->Select("Select * FROM projects ORDER BY id DESC");
			$this->view->generate('admin/projects.php','template_view.php',$data);
		}
	}
	
	//Практика производственная
	public function action_practice()
	{
		$model = new Model_Page();
		
		if($this->isPost())
		{
			$data['lang_ua'] = trim($_POST['consist_ua']);
			$data['lang_ru'] = trim($_POST['consist_ru']);

			$model->Update($data['lang_ru'],$data['lang_ua'],'practice');
			
		}

		$data = $model->Get("practice");

		$this->view->generate('admin/edit_page.php', 'template_view.php',$data);
	}
	
	//Практика Трудоустройство
	public function action_employment()
	{
		$model = new Model_Page();
		
		if($this->isPost())
		{
			$data['lang_ua'] = trim($_POST['consist_ua']);
			$data['lang_ru'] = trim($_POST['consist_ru']);

			$model->Update($data['lang_ru'],$data['lang_ua'],'employment');
			
		}

		$data = $model->Get("employment");

		$this->view->generate('admin/edit_page.php', 'template_view.php',$data);
	}
	
	//Тематика научных исследований
	public function action_subjects()
	{
		$model = new Model_Page();
		
		if($this->isPost())
		{
			$data['lang_ua'] = trim($_POST['consist_ua']);
			$data['lang_ru'] = trim($_POST['consist_ru']);

			$model->Update($data['lang_ru'],$data['lang_ua'],'subjects');
			
		}

		$data = $model->Get("subjects");

		$this->view->generate('admin/edit_page.php', 'template_view.php',$data);
	}
	
	//Диссертации
	public function action_theses()
	{
		$model = new Model_Page();
		
		if($this->isPost())
		{
			$data['lang_ua'] = trim($_POST['consist_ua']);
			$data['lang_ru'] = trim($_POST['consist_ru']);

			$model->Update($data['lang_ru'],$data['lang_ua'],'theses');
			
		}

		$data = $model->Get("theses");

		$this->view->generate('admin/edit_page.php', 'template_view.php',$data);
	}
	
	
	//Научная работа студентов
	public function action_students()
	{
		$model = new Model_Page();
		
		if($this->isPost())
		{
			$data['lang_ua'] = trim($_POST['consist_ua']);
			$data['lang_ru'] = trim($_POST['consist_ru']);

			$model->Update($data['lang_ru'],$data['lang_ua'],'students');
			
		}

		$data = $model->Get("students");

		$this->view->generate('admin/edit_page.php', 'template_view.php',$data);
	}
	
	//Клуб юных исследовалей
	public function action_club()
	{
		$model = new Model_Page();
		
		if($this->isPost())
		{
			$data['lang_ua'] = trim($_POST['consist_ua']);
			$data['lang_ru'] = trim($_POST['consist_ru']);

			$model->Update($data['lang_ru'],$data['lang_ua'],'club');
			
		}

		$data = $model->Get("club");

		$this->view->generate('admin/edit_page.php', 'template_view.php',$data);
	}
	
	public function action_schools()
	{
		$model = new Model_Page();
		
		if($this->isPost())
		{
			$data['lang_ua'] = trim($_POST['consist_ua']);
			$data['lang_ru'] = trim($_POST['consist_ru']);

			$model->Update($data['lang_ru'],$data['lang_ua'],'schools');
			
		}

		$data = $model->Get("schools");

		$this->view->generate('admin/edit_page.php', 'template_view.php',$data);
	}
	
	
	//Новости 
	public function action_news()
	{
		$model = Model_Msql::Instance();
		if($this->isPost())
		{
			$data['name_ru'] = $_POST['name_ru'];
			$data['name_ua'] = $_POST['name_ua'];
			$data['lang_ru'] = $_POST['contain_ru'];
			$data['lang_ua'] = $_POST['contain_ua'];
			if(!((trim($_POST['name_ru'])=='') or (trim($_POST['name_ua'])=='') or (trim($_POST['contain_ru'])=='') or (trim($_POST['contain_ua'])=='')))
			{
				if(isset($_POST['insert'])){
					$data['pubdate'] = time();
					if($model->Insert('news',$data))
					{
						$data['name_ru'] = "";
						$data['name_ua'] = "";
						$data['lang_ru'] = "";
						$data['lang_ua'] = "";
					}
				}
				elseif(isset($_POST['edit']))
				{
					if(route::$param['edit'] == $_POST['item_id'])
					{
						$id = intval(route::$param['edit']);
						if($model->Update('news',$data,"id='$id'"))
							header("LOCATION: /admin/news/");
					}
				}
				elseif(isset($_POST['delete']))
				{
					$id = intval(route::$param['edit']);
					if($model->Delete('news',"id='$id'"))
						header("LOCATION: /admin/news/");
				}

			}else
				$data['massage'] = 'Заполните все поля';
		}
		if(isset(route::$param['edit']))
		{
			$id = intval(route::$param['edit']);
			$data['post'] = $model->Select("Select * FROM news where id='$id'");
			$data['post'] = $data['post'][0];
			$this->view->generate('admin/consultations_adit.php','template_view.php',$data);
		}else{
			$data['all'] = $model->Select("Select * FROM news ORDER BY id DESC");
			$this->view->generate('admin/news.php','template_view.php',$data);
		}
	}
	
	
	//Разраюлтки
	/*public function action_development()
	{
		$model = Model_Msql::Instance();
		if($this->isPost())
		{
			$data['name_ru'] = $_POST['name_ru'];
			$data['name_ua'] = $_POST['name_ua'];
			$data['lang_ru'] = $_POST['contain_ru'];
			$data['lang_ua'] = $_POST['contain_ua'];
			if(!((trim($_POST['name_ru'])=='') or (trim($_POST['name_ua'])=='') or (trim($_POST['contain_ru'])=='') or (trim($_POST['contain_ua'])=='')))
			{
				if(isset($_POST['insert'])){
					if($model->Insert('development',$data))
					{
						$data['name_ru'] = "";
						$data['name_ua'] = "";
						$data['lang_ru'] = "";
						$data['lang_ua'] = "";
					}
				}
				elseif(isset($_POST['edit']))
				{
					if(route::$param['edit'] == $_POST['item_id'])
					{
						$id = intval(route::$param['edit']);
						if($model->Update('development',$data,"id='$id'"))
							header("LOCATION: /admin/development/");
					}
				}
				elseif(isset($_POST['delete']))
				{
					$id = intval(route::$param['edit']);
					if($model->Delete('development',"id='$id'"))
						header("LOCATION: /admin/development/");
				}

			}else
				$data['massage'] = 'Заполните все поля';
		}
		if(isset(route::$param['edit']))
		{
			$id = intval(route::$param['edit']);
			$data['post'] = $model->Select("Select * FROM development where id='$id'");
			$data['post'] = $data['post'][0];
			$this->view->generate('admin/consultations_adit.php','template_view.php',$data);
		}else{
			$data['all'] = $model->Select("Select * FROM development ORDER BY id DESC");
			$this->view->generate('admin/development.php','template_view.php',$data);
		}
	}*/
	
	public function action_questions()
	{
		$model = new Model_Page();
		
		if($this->isPost())
		{
			$data['lang_ua'] = trim($_POST['consist_ua']);
			$data['lang_ru'] = trim($_POST['consist_ru']);

			$model->Update($data['lang_ru'],$data['lang_ua'],'questions');
			
		}

		$data = $model->Get("questions");

		$this->view->generate('admin/edit_page.php', 'template_view.php',$data);
	}
	
	
	// Действие для разлогинивания администратора
	function action_logout()
	{
		session_start();
		session_destroy();
		header('Location:/');
	}

}
