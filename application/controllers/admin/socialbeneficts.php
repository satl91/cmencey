<?php

class Admin_Socialbeneficts_Controller extends Base_Controller
{
	public $restful = true;

	public function __construct() 
	{
		parent::__construct();

		$this->title = 'Sistema de gestion de personal';
	}

	public function get_employeeslist() 
	{
		$title = $this->title .' - Liquidación.';

		$employees = Employee::where('active','=','1')->get();

		return View::make('admin.socialbeneficts.employeeslist')->with('title',$title)->with('employees',$employees);
	}

	public function get_pre($id) 
	{
		$title = $this->title .' - Liquidación ';
		
		$employee = Employee::find($id);

		if(!empty($employee->startdate)) { $employee->startdate = date('d-m-Y',strtotime($employee->startdate)); }

		return View::make('admin.socialbeneficts.pre')->with('title',$title)->with('employee',$employee);
	}

	public function post_view()
	{
		$title = $this->title .' - Liquidación ';

		$employee 						= Employee::find(Input::get('id'));

		$payment 						= new stdClass;

		$payment->reason 				= Input::get('reason'); 
		$payment->check 				= Input::get('check');
		$payment->down_salaries_days	= Input::get('down_salaries_days');
		$payment->received_advances 	= Input::get('received_advances');
		$payment->received_loans		= Input::get('received_loans');
		$payment->others				= Input::get('others'); 
		
		$payment->startdate 			= Input::get('startdate'); 
		$payment->stopdate 				= Input::get('stopdate'); 
		
		/*
		|--------------------------------------------------------------------------|
		| Service time calculation											   |
		|--------------------------------------------------------------------------|
		*/
		// formats the start and stop dates to a object in order to calculate the service time
		$start 	= new DateTime(date('Y-m-d',strtotime($payment->startdate)));
		$stop 	= new DateTime(date('Y-m-d',strtotime($payment->stopdate)));

		$payment->servicetime 			= $start->diff($stop);
		
		// +1 day its added to the days in order to count the first day worked if its less than a month of work
		if ( ($start->format('m') == $stop->format('m')) AND ($start->format('y') == $stop->format('y')) ) { $payment->servicetime->d = $payment->servicetime->d + 1; }

		/*
		|--------------------------------------------------------------------------|
		| Vacations days calculation											   |
		|--------------------------------------------------------------------------|
		*/
		// if is not the first year at work yet
		if ($payment->servicetime->y == 0)
		{  
			// the max days of the year in order to calculate the months and days
			$max_days 	 	= 80;
			$payment->vacations_days 	= round( ( $payment->servicetime->m * ($max_days / 12) ) + ( $payment->servicetime->d * ($max_days / 365) ),2); 
		}
		// if it have 1 year at work and counting
		if ($payment->servicetime->y == 1)
		{ 
			// 80 days because its the first year
			$vacations_days_by_years 	= 80;
			// the max days of the year in order to calculate the extra months and days
			$max_days 		= 81;
			// calculates the total vacations days
			$payment->vacations_days 	= round($vacations_days_by_years + ($payment->servicetime->m * ($max_days / 12)) + ( ($payment->servicetime->d) * ($max_days / 365) ),2); 
		}
		// if it have more than 1 year at work 
		if ($payment->servicetime->y > 1)
		{ 
			// 80 days by year + 1 day by extra year after the first year
			$vacations_days_by_years 	= ($payment->servicetime->y * 80) + $payment->servicetime->y - 1;
			// calculates the max days of the last year in order to calculate the months and days
			$max_days 		= (80 + ($payment->servicetime->y));
			// calculates the total vacations days
			$payment->vacations_days 	= round($vacations_days_by_years + ($payment->servicetime->m * ($max_days / 12)) + ( ($payment->servicetime->d) * ($max_days / 365) ),2); 
		}
		// calculates the total of vacations
		$payment->vacations_total		= round($payment->vacations_days * $employee->salary,2); 

		/*
		|--------------------------------------------------------------------------|
		| antiquity days calculation											   |
		|--------------------------------------------------------------------------|
		*/
		// if is not the first year at work yet
		if ($payment->servicetime->y == 0)
		{  
			$max_days 	 				= 72;
			$payment->antiquity_days 	= round( ( $payment->servicetime->m * ($max_days / 12) ) + ( $payment->servicetime->d * ($max_days / 365) ),2); 
		}
		// if it have 1 year at work
		if ($payment->servicetime->y == 1)
		{ 
			// 72 days because its the first year
			$antiquity_days_by_years 	= 72;
			// the max days of the year in order to calculate the extra months and days
			$max_days 		= 74;
			// calculates the total antiquity days
			$payment->antiquity_days 	= round($antiquity_days_by_years + ($payment->servicetime->m * ($max_days / 12)) + ( ($payment->servicetime->d) * ($max_days / 365) ),2); 
		}
		// if it have more than 1 year at work 
		if ($payment->servicetime->y != 0)
		{ 
			// 80 days by year + 1 day by extra year after the first year
			$antiquity_days_by_years 	= ($payment->servicetime->y * 72) + ( ($payment->servicetime->y - 1) * 2);
			// calculates the max days of the last year in order to calculate the months and days
			$max_days 		= (72 + ($payment->servicetime->y * 2));
			// calculates the total antiquity days
			$payment->antiquity_days 	= round($antiquity_days_by_years + ($payment->servicetime->m * ($max_days / 12)) + ( ($payment->servicetime->d) * ($max_days / 365) ),2); 
		}
		// calculates the total of antiquity
		$payment->antiquity_total		= round($payment->antiquity_days * $employee->salary,2); 
	
		/*
		|--------------------------------------------------------------------------|
		| Down salaries calculation												   |
		|--------------------------------------------------------------------------|
		*/
		$payment->down_salaries_total 	= round($payment->down_salaries_days * $employee->salary,2);

		/*
		|--------------------------------------------------------------------------|
		| Utilities calculation													   |
		|--------------------------------------------------------------------------|
		*/
		// modifies the date in order to calculate the utilities total - only effectives months
		if ($start->format('d')	!= '1') { $start->modify('first day of next month');} 
		if ($stop->format('d')	!= '1') { $stop->modify('first day of this month');	}

		// 75 days by year and 6.25 days by month  *  employee current salary
		$payment->utilities_days	= ($start->diff($stop)->y * 75) + $start->diff($stop)->m * 6.25;
		$payment->utilities_total 	= round(($payment->utilities_days) * $employee->salary,2); 

		/*
		|--------------------------------------------------------------------------|
		| Assignments and deductions calculation								   |
		|--------------------------------------------------------------------------|
		*/
		$payment->assignments_total = round($payment->antiquity_total + $payment->utilities_total + $payment->down_salaries_total + $payment->vacations_total,2);
		$payment->deductions_total 	= round($payment->received_advances - $payment->received_loans - $payment->others);
		
		// payment total calculation
		$payment->total 			= $payment->assignments_total - $payment->deductions_total;

		Session::put('payment',$payment);
		Session::put('employee',$employee);

		return View::make('admin.socialbeneficts.view')->with('title',$title)->with('employee',$employee)->with('payment',$payment);	
	}
}