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
			//remove competition_id when actually doing this because it'll be incremented automatically
			'competition_id' => 1,
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

	function get_competition_id($start, $end, $days, $question, $answer, $title)
	{
		$query = $this->db->query("SELECT * FROM competition WHERE name = '$title';");
	
		return $query;
	}

	function get_competition_data($id)
	{

		$query = $this->db->query("SELECT * FROM competition WHERE competition_id = '$id';");
		return $query;
	}

	function insert_category($category)
	{
		//check whether category name is already in the db
		$query = $this->db->query("SELECT * FROM category WHERE name = '$category';");

		//if not put it in
		if($query->num_rows() == 0)
		{
			$data = array(
				'category_id' => 1,
				'name' => $category
				);
			//insert name into category table
			$this->db->insert('category', $data);
		}

		//query the category table to retrieve id
		$query = $this->db->query("SELECT * FROM category WHERE name = '$category';");
		if($query->num_rows() == 1)
		{
			$row = $query->row();
			return $row->category_id;
		}
		else
		{
			echo "error with retrieving category id";
		}
	}

	function insert_question($question,$category_id,$type,$competition_id)
	{
		//put question info into an array
		$data = array(
			//remove question_id when actually doing this because it'll be incremented automatically
			'question_id' => 2,
			'category_id' => $category_id,
			'question' => $question,
			'type' => $type
		);

		//insert into db, throw error if data not inserted
		if( $this->db->insert('question', $data) != TRUE)
		{
			throw new Exception("Cannot insert into question table");
		}
		else
		{
			return $this->db->affected_rows();
		}
	}	


	function get_question_id($question,$category_id,$type,$competition_id)
	{
		//query question table to get question_id
		$query = $this->db->query("SELECT * FROM question WHERE question = '$question' AND category_id = '$category_id' AND type = '$type';");

		if($query->num_rows() == 1)
		{
			$row = $query->row();
			return $row->question_id;
		}
		else
		{
			echo "error with retrieving question id";
		}
	}

	function insert_date_question($question_id,$competition_id,$date_question_asked)
	{
		//put question info into an array
		$data = array(
			//remove date_question_id when actually doing this because it'll be incremented automatically
			'date_question_id' => 2,
			'question_id' => $question_id,
			'competition_id' => $competition_id,
			'date' => $date_question_asked
		);


		//insert into db, throw error if data not inserted
		if( $this->db->insert('date_question', $data) != TRUE)
		{
			throw new Exception("Cannot insert date_question");
		}
		else
		{
			return $this->db->affected_rows();
		}
	}

	function insert_answer($answer,$correct,$competition_id,$question_id)
	{
		$data = array(
			//remove answer_id when actually doing this because it'll be incremented automatically
			'answer_id' => 2,
			'question_id' => $question_id,
			'answer' => $answer,
			'correct' => $correct
			);

		//insert into db, throw error if data not inserted
		if( $this->db->insert('answer', $data) != TRUE)
		{
			throw new Exception("Cannot insert into answer table");
		}
		else
		{
			return $this->db->affected_rows();
		}

	}

	function get_all_competitions()
	{
		$query = $this->db->query("SELECT * FROM competition;");

		if($query->num_rows() > 0)
		{
			return $query;
		}
		else
		{
			echo "error with retrieving competition data";
		}

	}

	function get_all_questions($competition_id)
	{

		
	}
}

?>