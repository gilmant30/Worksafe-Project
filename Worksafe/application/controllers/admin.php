<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Admin extends CI_Controller {


	function __construct()
	{
		parent::__construct();
		$this->load->helper(array('form', 'url', 'string'));  //load a form and the base_url
        $this->load->library(array('form_validation', 'security')); //set form_validation rules and xss_cleaning

	}

	public function index()
	{
			$this->load->view('admin_login');
	}

	public function competition()
	{
		$this->load->view('competition');
	}

	public function newCompetition()
	{
		$this->load->view('new_competition');
	}

	public function createCompetition()
	{
		//load model and helper
		$this->load->model('Admin_model');
		$this->load->helper('date');

		//get start_date convert string into date 
		$start_date = $this->security->xss_clean($this->input->post('from'));
		$time_start = date('Y-m-d', strtotime($start_date));
		$time_start = strtotime($time_start);

		//get end_date convert string into date
		$end_date = $this->security->xss_clean($this->input->post('to'));
		$time_end = date('Y-m-d', strtotime($end_date));
		$time_end = strtotime($time_end);

		$num_question = $this->security->xss_clean($this->input->post('num_questions_per_day'));
		$num_answer = $this->security->xss_clean($this->input->post('num_answers'));
		$title = $this->security->xss_clean($this->input->post('title'));

		//find number of days in betweem days		
		$difference = abs($time_end - $time_start); // that's it!
		$days = floor($difference/(60*60*24)) + 1;

		//send to admin_model to run function insert_competition_format(), throw error if it didn't add to database
		try
		{
			$this->Admin_model->insert_competition_format($start_date, $end_date, $days, $num_question, $num_answer, $title);
		} catch (Exception $e) {
			echo 'Caught exception: ', $e->getMessage(), "\n";
		}

	}

	public function createQuestion()
	{
		$this->load->view('create_question');
	}

	public function login()
	{
		$this->load->model('Admin_model');


		$user = $this->security->xss_clean($this->input->post('username'));
		$pass = $this->security->xss_clean($this->input->post('password'));
		
		$query = $this->Admin_model->validate_admin_login($user,$pass);
		
		if($query->num_rows() >0)
		{
			redirect('admin/competition');
		}
		else
		{
			redirect('admin/index');
		}
		
	}

}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */