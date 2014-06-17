<?php
class Admin_model extends CI_Model {
	
	//parent function that loads database
	function __construct()
	{
		parent::__construct();
		$this->load->database();
	}

	//validate whether email and password are correct and that the role is admin
	function validate_admin_login($email,$pass)
	{
		//convert password to string
		$pass = (string)$pass;
		$query = $this->db->query("SELECT * FROM user INNER JOIN user_role ON user.user_id = user_role.user_id WHERE user.password = '$pass' AND user.email = '$email' AND user_role.role_id = '1' AND user_role.status = 'active';");
		return $query;
	}

	//insert the format of the competition into the 'competition' table
	function insert_competition_format($start, $end, $days, $question, $answer, $title)
	{
		//unblock when real db is used
		/*
		//update all competitions so the one created is active
		$data = array('active' => 'n');
		$this->db->update('competition', $data);
		*/
		
		//put data in array to be put in db
		$data = array(
			//remove competition_id when actually doing this because it'll be incremented automatically
			'competition_id' => 2,
			'year' => 2014,
			'question_per_day' => $question,
			'answers_per_day' => $answer,
			'name' => $title,
			'start_date' => $start,
			'end_date' => $end,
			'days_of_competition' => $days,
			'active' => 'y'
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

	//get competition id by title
	function get_competition_id($title)
	{
		$query = $this->db->query("SELECT * FROM competition WHERE name = '$title';");
	
		return $query;
	}

	//get all competition data by id
	function get_competition_data($id)
	{
		$query = $this->db->query("SELECT * FROM competition WHERE competition_id = '$id';");
		return $query;
	}

	//insert the category into the database
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

	//insert the question data into the 'question' table
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

	//get question id by all fields
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

	//insert the specific date the question will be asked on
	function insert_date_question($question_id,$competition_id,$date_question_asked)
	{
		//put question info into an array
		$data = array(
			//remove date_question_id when actually doing this because it'll be incremented automatically
			'date_question_id' => 2,
			'question_id' => $question_id,
			'competition_id' => $competition_id,
			'question_date' => $date_question_asked
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

	//insert the answer data into the 'answer' table
	function insert_answer($answer,$correct,$competition_id,$question_id)
	{
		$data = array(
			//remove answer_id when actually doing this because it'll be incremented automatically
			'answer_id' => 2,
			//***************
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

	//get all competitions from the 'competitions' table
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