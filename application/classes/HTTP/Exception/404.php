<?php defined('SYSPATH') OR die('No direct script access.');

class HTTP_Exception_404 extends Kohana_HTTP_Exception_404 {
	/**
	 * Generate a Response for the 404 Exception.
	 * The user should be shown a nice 404 page.
	 * @return Response
	 */
    public function get_response()
    {
		// Remembering that `$this` is an instance of HTTP_Exception_404
		
		$content = Jade::factory('error/404')
			->set('error','Запрашиваемая страница не найдена')
			->set('message', $this->getMessage())
			->render();
		
		$params = ['content' => $content];
		
		$view = $this->_response('404', $params);
		
		$response = Response::factory()
			->status(404)
			->body($view);
 
		return $response;
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
			: Model::factory("Page_$model")->get('all', $controller);

		// запрос
		$response = Request::factory("$controller")
			->post($params)
			->method(HTTP_Request::POST)
			->execute();

		// проверяем статус запроса
		/*
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
		*/
		return $response;
	}

}
