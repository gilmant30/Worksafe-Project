<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Admin extends CI_Controller {

	/**
	 * Index Page for this controller.
	 *
	 * Maps to the following URL
	 * 		http://example.com/index.php/welcome
	 *	- or -  
	 * 		http://example.com/index.php/welcome/index
	 *	- or -
	 * Since this controller is set as the default controller in 
	 * config/routes.php, it's displayed at http://example.com/
	 *
	 * So any other public methods not prefixed with an underscore will
	 * map to /index.php/welcome/<method_name>
	 * @see http://codeigniter.com/user_guide/general/urls.html
	 */

	function __construct()
	{
		parent::__construct();
		$this->load->helper('url');
		$this->load->helper('form');
		$this->load->helper('string');
	}

	public function index()
	{
		$this->load->view('admin_login');
	}

	public function competition()
	{
		$this->load->view('competition');
	}

	public function createCompetition()
	{
		$this->load->view('create_competition');
	}

	public function login()
	{
		echo "page after login";
	}
}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */