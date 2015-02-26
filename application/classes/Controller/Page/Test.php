<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Page_Test extends Controller_Page_Default {

	public $layout = "Layouts/Clear";

	public function action_index()
	{
		parent::action_index();

		echo $this->style();

		//$this->template->content = Scss::render('Test');
	}

	private function style()
	{
		$search = 'media'.DIRECTORY_SEPARATOR.'scss';
		$scss   = Kohana::list_files($search.DIRECTORY_SEPARATOR.'briz');

		foreach ($scss as $key => & $value):
			$value = str_replace($search.DIRECTORY_SEPARATOR,'',$key);
			$value = str_replace('.scss','',$value);
		endforeach;

		return Scss::render($scss);
	}
}
