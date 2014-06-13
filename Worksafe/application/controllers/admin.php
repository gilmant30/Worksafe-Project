<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Admin extends CI_Controller {


	function __construct()
	{
		parent::__construct();
		$this->load->helper(array('form', 'url', 'string'));  //load a form and the base_url
        $this->load->library(array('form_validation', 'security', 'session')); //set form_validation rules and xss_cleaning

	}

	public function index()
	{
			$this->load->view('admin/admin_login');
	}

	public function competition()
	{
		$this->load->view('admin/competition');
	}

	public function newCompetition()
	{
		$this->load->view('admin/new_competition');
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
		$num_answers = $this->security->xss_clean($this->input->post('num_answers'));
		$title = $this->security->xss_clean($this->input->post('title'));

		//find number of days in betweem days		
		$difference = abs($time_end - $time_start); // that's it!
		$days = floor($difference/(60*60*24)) + 1;

		//send to admin_model to run function insert_competition_format(), throw error if it didn't add to database
		try
		{
			$this->Admin_model->insert_competition_format($start_date, $end_date, $days, $num_question, $num_answers, $title);
		} catch (Exception $e) {
			echo 'Caught exception: ', $e->getMessage(), "\n";
		}


		//get competition id
		$query = $this->Admin_model->get_competition_id($start_date, $end_date, $days, $num_question, $num_answers, $title);


		//make sure the query returns something
		if($query->num_rows() > 0)
		{
			$row = $query->row();
		}
		else
		{
			echo "error getting data from database";
		}

		//set cookie for competition id
		$cookie_id = array(
			'name' => 'Competition_id',
			'value' => $row->competition_id,
			'expire' => '86500',
			);

		$this->input->set_cookie($cookie_id);


		//redirect to createQuestion page
		redirect("admin/createQuestion");
	
	}

	public function selectCompetition()
	{
		$this->load->model('Admin_model');
	}

	public function createQuestion()
	{

		//set which day and how many question and answers to put on the screen
		$this->load->model('Admin_model');

		$competition_name;
		$this->load->view('admin/create_question');
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