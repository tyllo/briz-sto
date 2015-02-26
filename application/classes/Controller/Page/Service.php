<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Page_Service extends Controller_Page_Default
{
	public function action_index()
	{
		parent::action_index();

		// делаем запрос к контроллер $service со своими $params
		$params = Model::factory("Page_Service")->get('service');
		$this->template->content = self::_response('service/index', $params);

	}

	public function action_article()
	{
		parent::action_index();

		// лейаут
		$this->layout = "Layouts/Page-aside";

		// делаем запрос к контроллер $service со своими $params
		$params = Model::factory("Page_Service")->get('article');
		if ( $params == FALSE ) self::error(404);
		$this->template->content = self::_response('service/article', $params);

		// делаем запрос к контроллеру $aside
		$this->template->aside   = self::_response('accordion');
	}
}
