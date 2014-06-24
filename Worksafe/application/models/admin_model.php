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
		$query = $this->db->query("SELECT * FROM user_table INNER JOIN user_role ON user_table.user_id = user_role.user_id WHERE user_table.user_password = '$pass' AND user_table.email = '$email' AND user_role.role_id = 1 AND user_role.status = 'active'");
		return $query;
	}

	//check if the title name is being used returns 0 if it isn't being used
	function check_competition_title($title)
	{
		$query = $this->db->query("SELECT * FROM competition WHERE competition_name = '$title'");

		return $query->num_rows();
	}

	//insert the format of the competition into the 'competition' table
	function insert_competition_format($start, $end, $days, $title)
	{
		//unblock when real db is used
		
		//update all competitions so the one created is active
		$data = array('ACTIVE' => 'n');
		$this->db->update('COMPETITION', $data);
		
		//insert data for competition one field at a time
		$this->db->set('COMPETITION_YEAR', 2014);
		$this->db->set('COMPETITION_NAME', $title);
		$this->db->set('START_DATE', "TO_DATE('$start','YYYY-MM-DD')",false);
		$this->db->set('END_DATE', "TO_DATE('$end','YYYY-MM-DD')",false);
		$this->db->set('DAYS_OF_COMPETITION', $days);
		$this->db->set('ACTIVE', 'y');

		//insert into db, throw error if data not inserted
		if( $this->db->insert('COMPETITION') != TRUE)
		{
			throw new Exception("Cannot insert");
		}
		else
		{
			return $this->db->affected_rows();
		}
	}

	//get competition id by active competition
	function get_competition_id()
	{
		$query = $this->db->query("SELECT * FROM COMPETITION WHERE ACTIVE = 'y'");
		if($query->num_rows() == 1)
		{
			$query = $query->row();
			return $query->COMPETITION_ID;
		}
		else
		{
			echo 'error with get_competition_id';
		}
	}

	//get all competition data by id
	function get_competition_data($id)
	{
		$query = $this->db->query("SELECT * FROM COMPETITION WHERE COMPETITION_ID = '$id'");
		return $query;
	}

	//insert the category into the database
	function insert_category($category)
	{
		//check whether category name is already in the db
		$query = $this->db->query("SELECT * FROM CATEGORY_TABLE WHERE CATEGORY_NAME = '$category'");

		//if not put it in
		if($query->num_rows() == 0)
		{
			$data = array(
				'CATEGORY_NAME' => $category
				);
			//insert name into category table
			$this->db->insert('CATEGORY_TABLE', $data);
		}

		//query the category table to retrieve id
		$query = $this->db->query("SELECT * FROM CATEGORY_TABLE WHERE CATEGORY_NAME = '$category'");
		if($query->num_rows() == 1)
		{
			$row = $query->row();
			return $row->CATEGORY_ID;
		}
		else
		{
			echo "error with retrieving category id";
		}
	}

	//insert the question data into the 'question' table
	function insert_question($question,$category_id,$type)
	{
		//put question info into an array
		$data = array(
			'CATEGORY_ID' => $category_id,
			'QUESTION' => $question,
			'QUESTION_TYPE' => $type,
		);

		//insert into db, throw error if data not inserted
		if( $this->db->insert('QUESTION', $data) != TRUE)
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
		$query = $this->db->query("SELECT * FROM QUESTION WHERE QUESTION = '$question' AND CATEGORY_ID = '$category_id' AND QUESTION_TYPE = '$type'");

		if($query->num_rows() == 1)
		{
			$row = $query->row();
			return $row->QUESTION_ID;
		}
		else
		{
			echo "error with retrieving question id";
		}
	}

	//insert the specific date the question will be asked on
	function insert_date_question($question_id,$competition_id,$date_question_asked)
	{

		//insert data for date_questino one field at a time
		$this->db->set('QUESTION_ID', $question_id);
		$this->db->set('COMPETITION_ID', 2);
		$this->db->set('QUESTION_DATE', "TO_DATE('$date_question_asked','MM/DD/YYYY')",false);

		//insert into db, throw error if data not inserted
		if( $this->db->insert('DATE_QUESTION') != TRUE)
		{
			throw new Exception("Cannot insert date_question");
		}
		else
		{
			return $this->db->affected_rows();
		}
	}

	function insert_true_false_answer($question_id, $answer)
	{
		$this->db->set('QUESTION_ID', $question_id);
		$this->db->set('ANSWER', $answer);
		$this->db->set('CORRECT', 'y');

		//insert into db, throw error if data not inserted
		if( $this->db->insert('ANSWER') != TRUE)
		{
			throw new Exception("Cannot insert into answer table");
		}
		else
		{
			return $this->db->affected_rows();
		}
	}

	//insert the answer data into the 'answer' table
	function insert_answer($answer,$correct,$question_id)
	{
		//set data to be put into database
		$this->db->set('QUESTION_ID', $question_id);
		$this->db->set('ANSWER', $answer);
		$this->db->set('CORRECT', $correct);

		//insert into db, throw error if data not inserted
		if( $this->db->insert('ANSWER') != TRUE)
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
		$query = $this->db->query("SELECT * FROM COMPETITION");

		if($query->num_rows() > 0)
		{
			return $query;
		}
		else
		{
			echo "error with retrieving competition data";
		}
	}

	//get all the organizations that are active with active competition
	function get_all_organizations($competition_id)
	{
		$query = $this->db->query("SELECT user_table.user_name, user_table.user_id FROM user_table INNER JOIN user_role ON user_table.user_id = user_role.user_id INNER JOIN role_table ON role_table.role_id = user_role.role_id WHERE user_role.role_id = '2' AND user_role.status = 'active'");

		//return entire query
		return $query;
	}

	//get all participants that are linked to a specific organization
	function get_participants_by_org($org_id)
	{
		$query = $this->db->query("SELECT * FROM user_org_assoc WHERE org_id = '$org_id'");

		return $query;
	}

	//get participant data for a single participant by the user id
	function get_participant_data($participant_id)
	{
		$query = $this->db->query("SELECT * FROM user_table WHERE user_id = '$participant_id'");

		if($query->num_rows() > 0)
		{
			$query = $query->row();
			return $query;
		}
		else
		{
			echo 'Error with get_participant_data() function';
		}
	}

	//get the organization data by the org id
	function get_org_data($org_id)
	{
		$query = $this->db->query("SELECT * FROM user WHERE user_id = '$org_id';");

		return $query->row();
	}

	//get the number of commits for a specific user
	function commits_by_user($participant_id)
	{
		$query = $this->db->query("SELECT * FROM commitment WHERE user_id = '$participant_id'");

		return $query->num_rows();	
	}

	//get all the questions for a specific comeptition by competition id
	function get_all_questions($competition_id)
	{
		$query = $this->db->query("SELECT * FROM date_question WHERE competition_id = '$competition_id';");

		if($query->num_rows() > 0)
			return $query;
		else
			echo "Error with get_all_questions() function";
	}

	//get all the question data by the question id
	function get_question_data($question_id)
	{
		$query = $this->db->query("SELECT * FROM question WHERE question_id = '$question_id';");

		if($query->num_rows == 1)
		{
			$query = $query->row();
			return $query;		}
		else
		{
			echo "error with get_question_name() function";
		}
	}

	//get all the answer data for a particular question
	function get_all_answers($question_id)
	{
		$query = $this->db->query("SELECT * FROM answer WHERE question_id = '$question_id';");

		if($query->num_rows() > 0)
			return $query;
		else
			echo "error with retrieving question from get_answers function";
	}

	//check to see if question has changed at all
	function check_question($question_id, $question)
	{
		$query = $this->db->query("SELECT * FROM question WHERE question_id = '$question_id' AND question = '$question';");

		//if number of rows returned is 0 then update the question field
		if($query->num_rows() == 0)
		{
			$this->update_qustion($question_id, $question);
			echo "updated   ";
		}
		else
		{
			echo "question remains the same ";
		}
	}

	//update the question
	function update_qustion($question_id, $question)
	{
		$this->db->query("UPDATE question SET question = '$question' WHERE question_id = '$question_id';");
	}

	function check_answer($answer_id, $answer)
	{
		$query = $this->db->query("SELECT * FROM answer WHERE answer_id = '$answer_id' AND answer = '$answer';");

		//if number of rows returned is 0 then update the answer field
		if($query->num_rows() == 0)
		{
			$this->update_answer($answer_id, $answer);
			echo "updated   ";
		}
		else
		{
			echo "answer remains the same ";
		}
	}

	function update_answer($answer_id, $answer)
	{
		$this->db->query("UPDATE answer SET answer = '$answer' WHERE answer_id = '$answer_id';");
	}

	//sets the active competition 
	function activate_competition($competition_id)
	{
		//set all competitions active column to n
		$data = array('active' => 'n');
		$this->db->update('competition', $data);

		$this->db->query("UPDATE competition SET active = 'y' WHERE competition_id = '$competition_id';");
	}

	function check_if_active($competition_id)
	{
		$query = $this->db->query("SELECT * FROM competition WHERE competition_id = '$competition_id' AND active = 'y';");

		return $query->num_rows();
	}

	//delete competition by id
	function delete_competition($competition_id)
	{
		//delete from the competition table
		$this->db->query("DELETE FROM competition WHERE competition_id = '$competition_id';");

		/*
		//delete from the date_question table
		$this->db->query("DELETE FROM date_question WHERE competition_id = '$competition_id';");

		//delete from commitment table
		$this->db->query("DELETE FROM commitment WHERE competition_id = '$competition_id';");

		//delete from user_org_assoc table
		$this->db->query("DELETE FROM user_org_assoc WHERE competition_id = '$competition_id';");
		*/
	}

	function check_org_competition_assoc($competition_id, $org_id)
	{
		$query = $this->db->query("SELECT * FROM user_org_assoc WHERE competition_id = '$competition_id' AND org_id = '$org_id'");

		return $query->num_rows();
	}

	function oracletest()
	{
		$q = $this->db->query("SELECT email FROM user_table");

		return $q;
	}

}

?>