<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Event extends CI_Controller {

	//parent function
	function __construct()
	{
		parent::__construct();
		$this->load->helper(array('form', 'url', 'string', 'cookie'));  //load a form and the base_url
        $this->load->library(array('form_validation', 'security', 'session')); //set form_validation rules and xss_cleaning
        date_default_timezone_set('America/Chicago');
	}

	public function index()
	{
		$this->load->model('Admin_model');

		$data['event_type'] = $this->Admin_model->get_event_types();


		$this->load->view('event/choose_event_type',$data);
	}
}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */