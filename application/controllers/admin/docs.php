<?php

class Admin_Docs_Controller extends Base_Controller 
{
	public $layout = 'layouts.admin';
	public $restful = true;

	public function get_index()
	{
		$this->layout->title .= ' - Gestionar documentos.';

		$view = View::make('admin.docs.index');

		$view->document_types = DB::table('document_types')->get();
		
		$this->layout->content = $view;
	}

	public function get_pending()
	{
		$this->layout->title .= ' - Documentos pendientes.';
		
		$view = View::make('admin.docs.pending');

		$view->pendings = DB::table('documents')
							->where('up_to_date','=','0')
							->join('employees','employees.id','=','documents.employee_id')
							->join('roles','roles.id','=','employees.role_id')
							->join('document_types','document_types.id','=','documents.document_type_id')
							->get(array('*','document_types.description as description','documents.id as id'));

		$this->layout->content = $view;
	}

	public function get_add() 
	{
		$this->layout->title .=' - Añadir documento.';

		$view = View::make('admin.docs.add');

		$view->employees = DB::table('employees')->get();

		$this->layout->content = $view;
	}

	public function post_add()
	{
		// creates the new document type
		$document_type 	= new DocumentType();
		
		$document_type->description 	= Input::get('document_type_description');
		$document_type->expires_in 		= Input::get('document_type_expires_in');
		
		$document_type->save();

		// retrives the new document_type ID
		$required_document = DB::table('document_types')
								->where('description','=',Input::get('document_type_description'))
								->first();

		// gets the ids of the employees wich will have the new document assigned
		$employees_ids = Input::get('employees_ids');

		// creates the new documents
		foreach ($employees_ids as $employee_id) 
		{
			$document = new Document();

			$document->employee_id 			= $employee_id;
			$document->document_type_id 	= $required_document->id;
			$document->up_to_date 			= false;
			$document->expires	 			= null;

			$document->save();
		}
		
		return Redirect::to('admin');
	}
}