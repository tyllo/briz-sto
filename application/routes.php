<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Set the routes. Each route must have a minimum of a name, a URI and a set of
 * defaults for the URI.
 */

// кешируем роутинг в фаил
//if( ! Route::cache() )
//{

/////////////////////////////////// [home] ///////////////////////////////////////

	Route::set('home', '(about)')
		->defaults(array(
			'directory'  => 'Page',
			'controller' => 'About',
	));

/////////////////////////////////// [page] ///////////////////////////////////////

/*
	Route::set('page', '(contact)')
		->defaults(array(
			'directory'  => 'Page',
			'controller' => 'Default',
	));
*/
	Route::set('page/service', 'service')
		->defaults(array(
			'directory'  => 'Page',
			'controller' => 'Service',
	));
	Route::set('page/service/article', 'service/<id>')
		->defaults(array(
			'directory'  => 'Page',
			'controller' => 'Service',
			'action'     => 'article',
	));

	Route::set('page/gallery', 'gallery(/<id>)')
		->defaults(array(
			'directory'  => 'Page',
			'controller' => 'Gallery',
	));

	Route::set('page/contact', 'contact(/<action>)')
		->defaults(array(
			'directory'  => 'Page',
			'controller' => 'Contact',
	));
	Route::set('page/error', '(404)')
		->defaults(array(
			'directory'  => 'Error',
			'controller' => '404',
	));
	Route::set('page/test', 'test')
		->defaults(array(
			'directory'  => 'Page',
			'controller' => 'Test',
	));
/////////////////////////////// [section] ////////////////////////////////////////

	Route::set('section', 'section/<section>')
		->defaults(array(
			'directory'  => 'Section',
			'controller' => 'Default',
	));

	Route::set('section/controller', 'section/<controller>(/<action>)')
		->defaults(array(
			'directory'  => 'Section',
			'controller' => 'Default',
	));

/////////////////////////////////////////////////////////////////////////////////

	// Route::set('default', '(<controller>(/<action>(/<id>)))')
	// 	->defaults(array(
	// 		'controller' => 'Page',
	// 		'action'     => 'index',
	// 	));




		/*
	Route::set('error', 'error/<action>(/<message>)', array('action' => '[0-9]++', 'message' => '.+'))
	->defaults(array(
		 'controller' => 'error_handler'
	));
	*/


	// установка всех дальнейших роутов
//	Route::cache(TRUE);
//}

