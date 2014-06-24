<?php
class Participant_model extends CI_Model {

	//parent function that loads database	
	function __construct()
	{
		parent::__construct();
		$this->load->database();
	}

	//get all the organizations that are active
	function get_all_organizations()
	{
		$query = $this->db->query("SELECT user_table.user_name, user_table.user_id FROM user_table INNER JOIN user_role ON user_table.user_id = user_role.user_id INNER JOIN role_table ON role_table.role_id = user_role.role_id WHERE user_role.role_id = '2' AND user_role.status = 'active'");


		//return entire query
		return $query;
	}

	//check to see if participant email is in the database and if so that they are a participant
	function check_participant_email($email)
	{
		$query = $this->db->query("SELECT user_table.user_name, user_table.user_id FROM user_table INNER JOIN user_role ON user_table.user_id = user_role.user_id WHERE user_table.email = '$email' AND user_role.role_id = '3'");

		return $query;
	}

	//insert the participant into 'user' table with parameters given
	function insert_participant($email)
	{
		$data = array(
			'EMAIL' => $email,
			);

		//insert into db, throw error if data not inserted
		if( $this->db->insert('USER_TABLE', $data) != TRUE)
		{
			throw new Exception("Cannot insert into user table");
		}
		else
		{
			return $this->db->affected_rows();
		}
	}

	//get the participant id from the user table
	function get_participant_id($email,$zipcode)
	{
		//return query with user_id
		$query = $this->db->query("SELECT user_id FROM user_table WHERE email = '$email'");

		if($query->num_rows() == 1)
		{
			$row = $query->row();
			return $row->USER_ID;
		}
		else
		{
			echo "error with retrieving question id";
		}
	}

	//insert the participant into the 'user_role' table with the participant role associated with them
	function insert_participant_into_user_role($participant_id)
	{
		$data = array(
			'ROLE_ID' => 3,
			'USER_ID' => $participant_id,
			'STATUS' => 'active',
			);

		//insert into db, throw error if data not inserted
		if( $this->db->insert('USER_ROLE', $data) != TRUE)
		{
			throw new Exception("Cannot insert into user_role table");
		}
		else
		{
			return $this->db->affected_rows();
		}
	}

	//insert into the 'user_org_assoc' table to associate the participant with the organization they will represent
	function assoc_user_org($participant_id, $org_id)
	{
		$competition_id = $this->get_competition_id();

		$data = array(
			'ORG_ID' => $org_id,
			'PARTICIPANT_ID' => $participant_id,
			'COMPETITION_ID' => $competition_id
		);

		//insert into db, throw error if data not inserted
		if( $this->db->insert('USER_ORG_ASSOC', $data) != TRUE)
		{
			throw new Exception("Cannot insert into user_org_assoc table");
		}
		else
		{
			return $this->db->affected_rows();
		}
	}

	//get competition data for the active competition
	function get_competition_data()
	{
		$query = $this->db->query("SELECT * FROM competition WHERE active = 'y'");
		
		if($query->num_rows() == 1)
		{
			return $query->row();
		}
		else
		{
			echo "error with retrieving competition id";
		}
	}

	//get competition id for the active competition
	function get_competition_id()
	{
		$query = $this->db->query("SELECT * FROM competition WHERE active = 'y'");
		
		if($query->num_rows() == 1)
		{
			$row = $query->row();
			return $row->COMPETITION_ID;
		}
		else
		{
			echo "error with retrieving competition id";
		}
	}

	//get the data for the questions for the particular day it is
	function get_question_data_from_date_question($competition_id)
	{
		$query = $this->db->query("SELECT * FROM date_question WHERE competition_id = '$competition_id'");

		if($query->num_rows() > 0)
			return $query;
		else
			echo "error with retrieving question data from get_question_data_from_date_question";
	}

	//get the question data for a single question by the question id
	function get_questions($question_id)
	{
		$query = $this->db->query("SELECT * FROM question WHERE question_id = '$question_id'");

		if($query->num_rows() == 1)
			return $query->row();
		else
			echo "error with retrieving question from get_questions function";
	}

	//get all the answer data for a particular question
	function get_answers($question_id)
	{
		$query = $this->db->query("SELECT * FROM answer WHERE question_id = '$question_id'");

		if($query->num_rows() > 0)
			return $query;
		else
			echo "error with retrieving question from get_answers function";
	}

	//get the number of questions in the competition per day
	function get_questions_per_day_in_competition()
	{
		$query = $this->db->query("SELECT question_per_day FROM competition WHERE active = 'y';");

		if($query->num_rows() == 1)
		{
			$query = $query->row();
			return $query->question_per_day;
		}
		else
			echo "error with retrieving questions_per_day from competition db";
	}

	//get all answer data for a single answer by the answer id
	function check_answer($answer_id)
	{
		$query = $this->db->query("SELECT * FROM answer WHERE answer_id = '$answer_id'");

		if($query->num_rows() == 1)
		{
			$query = $query->row();
			return $query;
		}
		else
			echo "error with retrieving correct field from answer db";
	}

	function add_commitment($participant_id, $competition_id)
	{
		$date = date('Y-m-d');

		$this->db->set('USER_ID', $participant_id);
		$this->db->set('COMPETITION_ID', $competition_id);
		$this->db->set('COMMITMENT_DATE', "TO_DATE('$date','YYYY-MM-DD')",false);

		//insert into db, throw error if data not inserted
		if( $this->db->insert('COMMITMENT') != TRUE)
		{
			throw new Exception("Cannot insert into commitment table");
		}
		else
		{
			return $this->db->affected_rows();
		}
	}

	//check to see if participant has already added a commitment for the day
	function check_commitment($participant_id, $competition_id)
	{
		$flag = 0;
		$today_date = date('d-m-Y');
		$query = $this->db->query("SELECT * FROM commitment WHERE user_id = '$participant_id' AND competition_id = '$competition_id'");

		foreach ($query->result() as $commit) {
			$date = date('d-m-Y',strtotime($commit->COMMITMENT_DATE));

			if($today_date == $date)
				{
					$flag++;
				}
		}
		return $flag;

		return $query;
	}

	function get_user_question_data($participant_id, $question_id)
	{
		$query = $this->db->query("SELECT * FROM user_question WHERE user_id = '$participant_id' AND question_id = '$question_id'");

		return $query;
	}

	function insert_into_user_question($participant_id, $question_id, $competition_id, $answer_id)
	{
		$data = array(
			'USER_ID' => $participant_id,
			'QUESTION_ID' => $question_id,
			'ANSWER_ID' => $answer_id,
			'COMPETITION_ID' => $competition_id
			);

		//insert into db, throw error if data not inserted
		if( $this->db->insert('USER_QUESTION', $data) != TRUE)
		{
			throw new Exception("Cannot insert into user_question table");
		}
		else
		{
			return $this->db->affected_rows();
		}
	}
}
?>