<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Section_Default extends Controller_Layouts_Section {

 	public function before()
	{
		parent::before();

		$request = $this->request;
		
		// Запрещаем запросы извне
		if ( $request->is_initial() ):
			// генерируем 404 ошибку
			self::error(404);
		endif;
 	}

 	public function action_index()
	{

		$controller = Request::current()->uri();
		$params     = Request::current()->post();

		foreach ($params as $key => $value):

			// забиндим все переменные в шаблоне что есть в моделе
			$this->template->bind($key, $$key);

		endforeach;

		extract($params); unset($params, $controller);
	}

} // End Base
