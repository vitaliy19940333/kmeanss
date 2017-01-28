<?php

/*
Класс-маршрутизатор для определения запрашиваемой страницы.
> цепляет классы контроллеров и моделей;
> создает экземпляры контролеров страниц и вызывает действия этих контроллеров.
*/
class Route
{
	public static $param;
	public static $path;
	static function start()
	{
		session_start();
		if(!isset($_SESSION['lang']))
			$_SESSION['lang'] = 'ru';
		
		// контроллер и действие по умолчанию
		$controller_name = 'home';
		$action_name = 'index';
		
		$routes = explode('/', $_SERVER['REQUEST_URI']);

		// получаем имя контроллера
		if ( !empty($routes[1]) )
		{	
			$controller_name = $routes[1];
		}
		
		// получаем имя экшена
		if ( !empty($routes[2]) )
		{
			$action_name = $routes[2];
		}
		
		if(!empty($routes[3])){
			$keys = $values = array();
				for($i=3, $cnt = count($routes); $i<$cnt; $i++){
					if($i % 2 != 0){
						//Чётное = ключ (параметр)
						$keys[] = $routes[$i];
					}else{
						//Значение параметра;
						$values[] = $routes[$i];
					}
				}
			self::$param = @array_combine($keys, $values);
		}
		self::$path[0] = $controller_name;
		self::$path[1] = $action_name;
		// добавляем префиксы
		$model_name = 'Model_'.$controller_name;
		$controller_name = 'Controller_'.$controller_name;
		$action_name = 'action_'.$action_name;

		/*
		echo "Model: $model_name <br>";
		echo "Controller: $controller_name <br>";
		echo "Action: $action_name <br>";
		*/

		// подцепляем файл с классом модели (файла модели может и не быть)

		$model_file = strtolower($model_name).'.php';
		$model_path = "application/models/".$model_file;
		if(file_exists($model_path))
		{
			include "application/models/".$model_file;
		}

		// подцепляем файл с классом контроллера
		$controller_file = strtolower($controller_name).'.php';
		$controller_path = "application/controllers/".$controller_file;
		if(file_exists($controller_path))
		{
			include "application/controllers/".$controller_file;
		}
		else
		{
			/*
			правильно было бы кинуть здесь исключение,
			но для упрощения сразу сделаем редирект на страницу 404
			*/
			Route::ErrorPage404();
		}
		$error = false;
		// создаем контроллер
		if($controller_name =='Controller_admin')
		{
			$error = false;
				$user = Model_Users::Instance();
				$user->ClearSessions();
				if (!$user->Can('admin')){
					session_destroy();
					Route::ErrorPage404();
					$error = true;
				}
		}
		if(!$error)
			$controller = new $controller_name;
		$action = $action_name;
		
		if(method_exists($controller, $action))
		{
			// вызываем действие контроллера
				echo $controller->$action();
		}else
		{
			// здесь также разумнее было бы кинуть исключение
			Route::ErrorPage404();
		}
	
	}

	function ErrorPage404()
	{
        $host = 'http://'.$_SERVER['HTTP_HOST'].'/';
       // header('HTTP/1.1 404 Not Found');
		//header("Status: 404 Not Found");
		header('Location:'.$host.'404');
    }
    
}
