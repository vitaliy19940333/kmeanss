<?php

class View
{
	
	//public $template_view; // здесь можно указать общий вид по умолчанию.
	
	/*
	$content_file - виды отображающие контент страниц;
	$template_file - общий для всех страниц шаблон;
	$data - массив, содержащий элементы контента страницы. Обычно заполняется в модели.
	*/
	function generate($content_view, $template_view, $data = null)
	{
		
		$nav = Controller_Nav::Instance();
		$menu = $nav::$tree;
		$bred_crumb = $nav->bread_crumbs(array(route::$path[0],route::$path[1]));
		if(!empty($data['name_'.$_SESSION['lang']]))
		$bred_crumb[2]['lang_'.$_SESSION['lang']] = $data['name_'.$_SESSION['lang']];
		$count_bread = count($bred_crumb);
		//$m_mysql = Model_Msql::Instance();
		//$news_last = $m_mysql->Select("Select * FROM news ORDER BY id LIMIT 5");
	/*
		
		if(is_array($data)) {
			
			// преобразуем элементы массива в переменные
			extract($data);
		}
		*/
		
		/*
		динамически подключаем общий шаблон (вид),
		внутри которого будет встраиваться вид
		для отображения контента конкретной страницы.
		*/
		include 'application/views/'.$template_view;
	}
}
