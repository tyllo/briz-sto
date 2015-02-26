<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Page_Default extends Controller_Layouts_Page {

	// Layouts/Page       - without aside
	// Layouts/Page-aside - with aside
	public $template  = "Layouts/Page";

	public function action_index()
	{
		// переменые по дефолту
		$this->template->content   = 'content';
		$this->template->aside     = 'aside';

		// какой контроллер вызван
		$controller = Request::current()->uri();

    // если заглавная страница, то выводим слайдер, иначе крошки

		// делаем запрос к контроллер $subheader с параметрами $param
		if ($controller === '/'):

			$subheader = 'sliders';
			$this->template->subheader = self::_response($subheader);

		else:

			$subheader = 'Subheader';
			$params = Model::factory("Section_".$subheader)->get($subheader);
			$this->template->subheader = self::_response($subheader, $params);

		endif;
	}
}
