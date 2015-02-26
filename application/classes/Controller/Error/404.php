<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Error_404 extends Controller_Page_Default {

	public $layout = "Layouts/Page";

	public function action_index()
	{
		parent::action_index();

		if (Request::current()->post()):
			extract(Request::current()->post());
		else:
			$content = '<br/><br/><h2 style=\'text-align:center\'>Запрашиваемая страница не найдена</h2><br/><br/>';
		endif;
		$this->template->content = $content;
	}

}
