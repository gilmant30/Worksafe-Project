<?php
class Training_model extends CI_Model {

	//this is the event type id for training gotten from db
	public $training = '21';

	//parent function that loads database	
	function __construct()
	{
		parent::__construct();
		$this->load->database();
	}

	function authenticate_user($email, $password)
	{
		$query = $this->db->query("SELECT * FROM user_table INNER JOIN user_role ON user_table.user_id = user_role.user_id WHERE user_table.email = '$email' AND user_table.user_password = '$password' AND user_role.role_id = '$this->training'");

		$query = $query->num_rows();

		return $query;
	}
}