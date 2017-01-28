<?php
class Model_Page extends Model
{
	public $msql;
	
	public function __construct()
	{
		$this->msql = Model_Msql::Instance();
	}
	
	public function Update($lang_ru,$lang_ua,$title_page)
	{
		if(trim($lang_ru) == '')
			$lang_ru = 'Страница в обработке';
		if(trim($lang_ua) == '')
			$lang_ru = 'Сторінка в обробці';
		
		$obj['lang_ru'] = $lang_ru;
		$obj['lang_ua'] = $lang_ua;
		$this->msql->Update('static_content',$obj,"title_page='$title_page'");
	}
	
	public function Get($title)
	{
		return  $this->msql->Select("Select * FROM static_content WHERE title_page='$title'");
	}
}
?>
