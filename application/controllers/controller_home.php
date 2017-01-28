<?php
class Controller_Home extends Controller
{
	
	public function action_index()
	{
		unset($_SESSION['table']);
		unset($_SESSION['field_info']);
		unset($_SESSION['data_cl']);
		$data['table'] = $this->m_mysql->Select("SHOW TABLES FROM kmeans");
		$this->view->generate('home_view.php','template_view.php',$data);
	}
	
}
?>