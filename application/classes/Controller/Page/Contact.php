<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Page_Contact extends Controller_Page_Default {

	public function action_index()
	{
		parent::action_index();

		// лейаут
		$this->layout = "Layouts/Page-aside";

		// добавим карту к subheader
		$this->template->subheader .= self::_response('contact/map');

		// aside = adress
		$this->template->aside = self::_response('contact/adress');

		$this->template->content = self::_response('contact/form');
	}

	public function action_send(){

		// выводим без основного шаблона
		$this->layout = 'Layouts/Clear';

		$model = Model::factory('Page_Contact');

		// инициализируем процесс
		$post = Request::current()->post();
		$model->init($post);

		// отправляем
		$model->send();

		// true/false
		$answer = $model->get_answer();

		// в зависимости от ответа set headers
		self::setHeader($answer);

		$this->template->content = ($answer) ? 'success' : 'error';
		//die(); // нужно что бы не выводилось время генерации
	}

	private static function setHeader($answer)
	{
		// установка заголовков ответа 404 или 200 в зависиости от status
		if ($answer)
			header('HTTP/1.1 200 OK');
		else
			header('HTTP/1.1 404 Not Found');

		header('Content-type: text/plain; charset=UTF-8');
	}
}
