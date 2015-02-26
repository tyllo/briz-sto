<?php defined('SYSPATH') or die('No direct script access.');

abstract class Controller_Layouts_Section extends Controller_Template {

    public $auto_render = FALSE;

 	public function before()
	{
		parent::before();

		// $this->template = Section/Slider
		$this->template = Request::current()->uri();

		// on prettyprint (\t) tags in Jade
		Jade::$prettyprint = true;

		// Load the template
		$this->template = Jade::factory($this->template);
	}

	public function after()
	{
		$this->response->body($this->template->render());

		parent::after();
	}

	protected function error($error=404){
		
		$request = $this->request;

		throw HTTP_Exception::factory(404,
				'The requested URL :uri was not found on this server.',
				array(':uri' => $request->uri())
			)->request($request);
	}

} // End Base
