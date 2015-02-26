<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Page_About extends Controller_Page_Default {

	//public $layout = "Layouts/Page";
	  public $layout = "Layouts/Page-aside";

	public function action_index()
	{
		parent::action_index();

		// делаем запрос к контроллер $about
		$this->template->content = self::_response('about');

		// делаем запрос к контроллер $aside
		$this->template->aside   = self::_response('accordion');
	}
}
