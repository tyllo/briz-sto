<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Page_Gallery extends Controller_Page_Default {

	  public $layout = "Layouts/Page";
	//public $layout = "Layouts/Page-aside";

	public function action_index()
	{
		parent::action_index();

		$model = Model::factory("Page_Gallery");

		// $id = 5 - номер страницы
		$model->id = $this->request->param('id');

		// делаем запрос к контроллеру $service со своими $tags
		$params = $model->get('tags');
		if ( $params == FALSE ) self::error(404);
		$this->template->content  = self::_response('tags', $params);

		// делаем запрос к контроллеру $service со своими $params
		$params = $model->get('gallery');
		if ( $params == FALSE ) self::error(404);
		$this->template->content .= self::_response('gallery', $params);

		// делаем запрос к контроллеру $pagination со своими $params
		$params = $model->get('pagination');
		if ( ! $params ) self::error(404);
		if ( is_array($params) ) // если всего одна станица то не выводим
			$this->template->content .= self::_response('pagination', $params);
	}
}
