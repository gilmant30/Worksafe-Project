<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Training extends CI_Controller {

	//parent function
	function __construct()
	{
		parent::__construct();
		$this->load->helper(array('form', 'url', 'string', 'cookie'));  //load a form and the base_url
        $this->load->library(array('form_validation', 'security', 'session')); //set form_validation rules and xss_cleaning
        $this->load->model('Training_model');
	}

	public function index()
	{

		$data['error'] = $this->session->flashdata('error');
		$this->load->view('training/training_home_page',$data);
	}

	public function login()
	{
		//put validation on so email and password field is required
		$this->form_validation->set_rules('email', 'Email', 'required|valid_email');
		$this->form_validation->set_rules('password', 'Password', 'required');

		//if email is empty returns error
		if($this->form_validation->run() === FALSE)
		{
			$this->session->set_flashdata('error', 'Must not leave the email or password field blank, and must have a valid email address');
			redirect('training/');
		}

		$email = $this->security->xss_clean($this->input->post('email'));
		$password =  $this->security->xss_clean($this->input->post('password'));

		$authenticate = $this->Training_model->authenticate_user($email, $password);

		if($authenticate == 1)
			echo 'user is logged in';
		else
		{
			$this->session->set_flashdata('error', 'Email or password is not correct');
			redirect('training/');
		}
	}
}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */