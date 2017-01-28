<?php
class Controller {
	
	public $model;
	public $view;
	public $param = array();
	public $m_mysql;
	public $v;
	
	
	public function __construct()
	{
		$this->view = new View();
		$this->model = new Model();
		$this->m_mysql = Model_Msql::Instance();
	}
	//
	// Запрос произведен методом GET?
	//
	protected function IsGet()
	{
		return $_SERVER['REQUEST_METHOD'] == 'GET';
	}

	//
	// Запрос произведен методом POST?
	//
	protected  function IsPost()
	{
		return $_SERVER['REQUEST_METHOD'] == 'POST';
	}
	
	// действие (action), вызываемое по умолчанию
	protected function action_index()
	{
		// todo	
	}
}
