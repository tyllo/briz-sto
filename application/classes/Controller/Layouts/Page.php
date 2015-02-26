<?php defined('SYSPATH') or die('No direct script access.');

abstract class Controller_Layouts_Page extends Controller_Template {

    public $layout      = "Layouts/Page";
    public $auto_render = FALSE;

 	public function before()
	{
		parent::before();

		// on prettyprint (\t) tags in Jade
		Jade::$prettyprint = true;

		// Load the template
		$this->template = Jade::factory();

        // если заглавная страница, то выводим слайдер, иначе крошки
		$params = Model::factory('Layouts_Page')->get('default');

		// забиндим переменную по умолчанию в шаблоне
		$this->template->bind('content', $content);
		$this->template->bind('aside',   $aside);

		foreach ($params as $param => $value):

			// забиндим все переменные в шаблон
			$this->template->bind($param, $$param);

		endforeach;

		extract($params); unset($params);
	}

	protected function error($error=404){

		$request = $this->request;

		throw HTTP_Exception::factory(404,
				'The requested URL :uri was not found on this server.',
				array(':uri' => $request->uri())
			)->request($request);
	}

	/*
	 * возвращает строку с section или NULL
	 * делает запрос к другому контроллеру
	 * проверяем статус запроса
   	 */
	protected function _response($controller, $model = FALSE, $params = FALSE)
	{
		// упорядочим входные данные
		$params = is_array($model) ? $model : $params;
		$model  = is_array($model) ? FALSE  : $model;

		// if $model == TRUE, то своя модель
		$model  = ($model) ? $controller : 'Default';

		// параметры передаваемые в запрос
		$params = ($params)
			? $params
			: Model::factory("Section_$model")->get('all', $controller);

		// запрос
		$response = Request::factory("section/$controller")
			->post($params)
			->method(HTTP_Request::POST)
			->execute();

		// проверяем статус запроса
		switch ($response->status()):
			case 200:
				// все ок, можно показывать результат
				break;

			default:
        		// если в режиме девелопинга то показываем ошибку
        		$response = ( Kohana::$environment === Kohana::DEVELOPMENT )
        			? $response
        			: NULL;
		endswitch;

		return $response;
	}

	public function after()
	{
		$this->response->body(
			$this->template
					->set_filename($this->layout)
					->render()
		);

		parent::after();
	}

} // End Base
