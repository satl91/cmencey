<?php

class Base_Controller extends Controller {

	/**
	 * Catch-all method for requests that can't be matched.
	 *
	 * @param  string    $method
	 * @param  array     $parameters
	 * @return Response
	 */
	
	public $layout = 'layouts.default';
	
	public function __construct() 
	{
		parent::__construct();
		$this->layout->title = 'Construccione Mencey, C.A';
		$this->layout->header = View::make('inc.header');
		$this->layout->footer = View::make('inc.footer');
	}

	public function __call($method, $parameters)
	{
		return Response::error('404');
	}

}