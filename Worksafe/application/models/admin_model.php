<?php
class Admin_model extends CI_Model {
	
	function __construct()
	{
		parent::__construct();
		$this->load->database();
	}

	function validate_admin_login($user,$pass)
	{
		$pass = (string)$pass;
		$query = $this->db->query("SELECT * FROM user WHERE password = '$pass' AND name = '$user';");
		return $query;
	}

	function insert_competition_format($start, $end, $days, $question, $answer, $title)
	{
		//put data in array to be put in db
		$data = array(
			'competition_id' => 3,
			'year' => 2014,
			'question_per_day' => $question,
			'answers_per_day' => $answer,
			'name' => $title,
			'start_date' => $start,
			'end_date' => $end,
			'days_of_competition' => $days
		);

		//insert into db, throw error if data not inserted
		if( $this->db->insert('competition', $data) != TRUE)
		{
			throw new Exception("Cannot insert");
		}
		else
		{
			return $this->db->affected_rows();
		}
	}

}

?>