<?php
class Admin_model extends CI_Model {
	
	//these are the event_type_id's gotten from the db
	public $competition = '1';
	public $course = '2';

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
		$query = $this->db->query("SELECT * FROM wsw_user INNER JOIN wsw_user_role ON wsw_user.user_id = wsw_user_role.user_id WHERE wsw_user.user_password = '$pass' AND wsw_user.email = '$email' AND wsw_user_role.role_id = '1' AND wsw_user_role.status = 'active'");
		return $query;
	}

	//check if the title name is being used returns 0 if it isn't being used
	function check_competition_title($title)
	{
		$query = $this->db->query("SELECT * FROM wsw_event WHERE event_name = '$title'");

		return $query->num_rows();
	}

	//insert the format of the competition into the 'competition' table
	function insert_competition_format($start, $end, $days, $title, $event_type_id)
	{	
		//insert data for competition one field at a time
		$this->db->set('EVENT_YEAR', 2014);
		$this->db->set('EVENT_NAME', $title);
		$this->db->set('START_DATE', "TO_DATE('$start','YYYY-MM-DD')",false);
		$this->db->set('END_DATE', "TO_DATE('$end','YYYY-MM-DD')",false);
		$this->db->set('DAYS_OF_COMPETITION', $days);
		$this->db->set('ACTIVE', 'y');
		$this->db->set('EVENT_TYPE_ID', $event_type_id);

		//insert into db, throw error if data not inserted
		if( $this->db->insert('WSW_EVENT') != TRUE)
		{
			throw new Exception("Cannot insert");
		}
		else
		{
			return $this->db->affected_rows();
		}
	}

	function insert_course($title, $event_type_id)
	{
		$this->db->set('EVENT_NAME', $title);
		$this->db->set('ACTIVE', 'y');
		$this->db->set('EVENT_TYPE_ID', $event_type_id);
		$this->db->set('EVENT_YEAR', '2014');

		//insert into db, throw error if data not inserted
		if( $this->db->insert('WSW_EVENT') != TRUE)
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
		$query = $this->db->query("SELECT * FROM wsw_event WHERE active = 'y' AND event_type_id = '$this->competition'");
		if($query->num_rows() == 1)
		{
			$query = $query->row();
			return $query->EVENT_ID;
		}
		else
		{
			echo 'error with get_competition_id';
		}
	}

	//get all competition data by id
	function get_competition_data($id)
	{
		$query = $this->db->query("SELECT * FROM wsw_event WHERE event_id = '$id'");
		return $query;
	}

	//insert the category into the database
	function insert_category($category)
	{
		//check whether category name is already in the db
		$query = $this->db->query("SELECT * FROM WSW_CATEGORY WHERE CATEGORY_NAME = '$category'");

		//if not put it in
		if($query->num_rows() == 0)
		{
			$data = array(
				'CATEGORY_NAME' => $category
				);
			//insert name into category table
			$this->db->insert('WSW_CATEGORY', $data);
		}

		//query the category table to retrieve id
		$query = $this->db->query("SELECT * FROM WSW_CATEGORY WHERE CATEGORY_NAME = '$category'");
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
	function insert_question($question,$category_id,$type,$source)
	{
		//put question info into an array
		$data = array(
			'CATEGORY_ID' => $category_id,
			'QUESTION' => $question,
			'QUESTION_TYPE' => $type,
			'SOURCE_LINK' => $source
		);

		//insert into db, throw error if data not inserted
		if( $this->db->insert('WSW_QUESTION', $data) != TRUE)
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
		$query = $this->db->query("SELECT * FROM WSW_QUESTION WHERE to_char(QUESTION) = '$question' AND CATEGORY_ID = '$category_id' AND QUESTION_TYPE = '$type'");

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
		$this->db->set('EVENT_ID', $competition_id);
		$this->db->set('QUESTION_DATE', "TO_DATE('$date_question_asked','MM/DD/YYYY')",false);

		//insert into db, throw error if data not inserted
		if( $this->db->insert('WSW_DATE_QUESTION') != TRUE)
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
		if( $this->db->insert('WSW_ANSWER') != TRUE)
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
		if( $this->db->insert('WSW_ANSWER') != TRUE)
		{
			throw new Exception("Cannot insert into answer table");
		}
		else
		{
			return $this->db->affected_rows();
		}
	}

	function get_all_events()
	{
		$query = $this->db->query("SELECT * FROM wsw_event");

		if($query->num_rows() > 0)
		{
			return $query;
		}
		else
		{
			echo "error with retrieving competition data";
		}
	}

	//get all competitions from the 'competitions' table
	function get_all_competitions()
	{
		$query = $this->db->query("SELECT * FROM wsw_event WHERE event_type_id = '$this->competition'");

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
		$query = $this->db->query("SELECT wsw_user.user_name, wsw_user.user_id FROM wsw_user INNER JOIN wsw_user_role ON wsw_user.user_id = wsw_user_role.user_id INNER JOIN wsw_role ON wsw_role.role_id = wsw_user_role.role_id WHERE wsw_user_role.role_id = '2' AND wsw_user_role.status = 'active'");

		//return entire query
		return $query;
	}

	//get all participants that are linked to a specific organization
	function get_participants_by_org($org_id, $competition_id)
	{
		$query = $this->db->query("SELECT * FROM wsw_user_org_assoc WHERE org_id = '$org_id' AND event_id = '$competition_id'");

		return $query;
	}

	//get participant data for a single participant by the user id
	function get_participant_data($participant_id)
	{
		$query = $this->db->query("SELECT * FROM wsw_user WHERE user_id = '$participant_id'");

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
		$query = $this->db->query("SELECT * FROM wsw_user WHERE user_id = '$org_id'");

		return $query->row();
	}

	//get the number of commits for a specific user
	function commits_by_user($participant_id, $competition_id)
	{
		$query = $this->db->query("SELECT * FROM wsw_commitment WHERE user_id = '$participant_id' AND event_id = '$competition_id'");

		return $query->num_rows();	
	}

	//get all the questions for a specific comeptition by competition id
	function get_all_questions($competition_id)
	{
		$query = $this->db->query("SELECT * FROM wsw_date_question WHERE event_id = '$competition_id'");

		return $query;
	}

	//get all the question data by the question id
	function get_question_data($question_id)
	{
		$query = $this->db->query("SELECT * FROM wsw_question WHERE question_id = '$question_id'");

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
		$query = $this->db->query("SELECT * FROM wsw_answer WHERE question_id = '$question_id'");

		if($query->num_rows() > 0)
			return $query;
		else
			echo "error with retrieving question from get_answers function";
	}

	//check to see if question has changed at all
	function check_question($question_id, $question)
	{
		$query = $this->db->query("SELECT * FROM wsw_question WHERE question_id = '$question_id' AND to_char(question) = '$question'");

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
		$this->db->query("UPDATE wsw_question SET question = '$question' WHERE question_id = '$question_id'");
	}

	function check_answer($answer_id, $answer)
	{
		$query = $this->db->query("SELECT * FROM wsw_answer WHERE answer_id = '$answer_id' AND to_char(answer) = '$answer'");

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
		$this->db->query("UPDATE wsw_answer SET answer = '$answer' WHERE answer_id = '$answer_id'");
	}

	//either set the course selected as active or inactive
	function activate_event($competition_id)
	{
		$query = $this->get_competition_data($competition_id);

		$query = $query->row();

		$active = $query->ACTIVE;

		if($active == 'y')
		{
			$this->db->query("UPDATE wsw_event SET active = 'n' WHERE event_id = '$competition_id'");
		}
		else if($active == 'n')
		{
			$this->db->query("UPDATE wsw_event SET active = 'y' WHERE event_id = '$competition_id'");
		}
	}

	function check_if_active($competition_id)
	{
		$query = $this->db->query("SELECT * FROM wsw_event WHERE event_id = '$competition_id' AND active = 'y' AND event_type_id = '$this->competition'");

		return $query->num_rows();
	}

	//delete competition by id
	function delete_competition($competition_id)
	{
		//delete from the competition table
		$this->db->query("DELETE FROM wsw_event WHERE event_id = '$competition_id'");

		/*
		//delete from the date_question table
		$this->db->query("DELETE FROM date_question WHERE competition_id = '$competition_id';");

		//delete from commitment table
		$this->db->query("DELETE FROM commitment WHERE competition_id = '$competition_id';");

		//delete from user_org_assoc table
		$this->db->query("DELETE FROM user_org_assoc WHERE competition_id = '$competition_id';");
		*/
	}

	//check whether an organization is associated with a competition
	function check_org_competition_assoc($competition_id, $org_id)
	{
		$query = $this->db->query("SELECT * FROM wsw_user_org_assoc WHERE event_id = '$competition_id' AND org_id = '$org_id'");

		return $query->num_rows();
	}

	//delete a question from date_question which deletes it from a certain competition
	function delete_question($question_id)
	{
		$this->db->query("DELETE FROM wsw_date_question WHERE question_id = '$question_id'");
	}

	//get all the wsw_event types
	function get_event_types()
	{
		$query = $this->db->query("SELECT * FROM wsw_event_type");

		if($query->num_rows() > 0)
		{
			return $query;
		}
		else
		{
			echo "error with retrieving competition data";
		}
	}

	//get the wsw_event type id by the competition id
	function get_event_type_id($competition_id)
	{
		$query = $this->db->query("SELECT * FROM wsw_event WHERE event_id = '$competition_id'");

		if($query->num_rows() == 1)
		{
			$query = $query->row();
			return $query->EVENT_TYPE_ID;
		}
		else
		{
			echo "error with retrieving wsw_event type id data";
		}
	}

	//get the wsw_event name by the wsw_event id
	function get_event_by_id($event_type_id)
	{
		$query = $this->db->query("SELECT * FROM wsw_event_type WHERE event_type_id = '$event_type_id'");

		if($query->num_rows() == 1)
		{
			return $query;
		}
		else
		{
			echo "error with retrieving wsw_event type data";
		}
	}

	//insert course questions
	function insert_course_question($question_id,$competition_id)
	{
		//insert data for date_questino one field at a time
		$this->db->set('QUESTION_ID', $question_id);
		$this->db->set('EVENT_ID', $competition_id);

		//insert into db, throw error if data not inserted
		if( $this->db->insert('WSW_COURSE_QUESTION') != TRUE)
		{
			throw new Exception("Cannot insert course_question");
		}
		else
		{
			return $this->db->affected_rows();
		}
	}

	//get the # of correct answers from an organization
	function get_org_correct_ans($org_id, $competition_id)
	{
		$query = $this->db->query("SELECT wsw_user_question.CORRECT FROM wsw_user_question JOIN wsw_user_org_assoc ON wsw_user_question.USER_ID = wsw_user_org_assoc.PARTICIPANT_ID WHERE wsw_user_org_assoc.ORG_ID = '$org_id' AND wsw_user_question.event_id = '$competition_id' AND wsw_user_question.CORRECT = 'y'");
	
		return $query->num_rows();
	}

	//get the total # of answers from an organization
	function get_org_total_ans($org_id, $competition_id)
	{
		$query = $this->db->query("SELECT wsw_user_question.CORRECT FROM wsw_user_question JOIN wsw_user_org_assoc ON wsw_user_question.USER_ID = wsw_user_org_assoc.PARTICIPANT_ID WHERE wsw_user_org_assoc.ORG_ID = '$org_id' AND wsw_user_question.event_id = '$competition_id'");
	
		return $query->num_rows();		
	}

	//get the # of correct answers from a participant
	function get_participant_correct_ans($participant_id, $competition_id)
	{
		$query = $this->db->query("SELECT CORRECT FROM wsw_user_question WHERE user_id = '$participant_id' AND correct = 'y' AND event_id = '$competition_id'");
	
		return $query->num_rows();
	}

	//get the total # of answers from a participant
	function get_participant_total_ans($participant_id, $competition_id)
	{
		$query = $this->db->query("SELECT CORRECT FROM wsw_user_question WHERE user_id = '$participant_id' AND event_id = '$competition_id'");
	
		return $query->num_rows();
	}

	function add_organization($email,$name)
	{
		$this->db->set('USER_NAME', $name);
		$this->db->set('EMAIL', $email);

		if($this->db->insert('WSW_USER') != TRUE)
		{
			return 'error';
		}
		else
		{
			return 'added';
		}
	}

	//get the participant id from the user table
	function get_org_id($email)
	{
		//return query with user_id
		$query = $this->db->query("SELECT user_id FROM wsw_user WHERE email = '$email'");

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

	function add_org_user_role($org_id)
	{
		$this->db->set('ROLE_ID', '2');
		$this->db->set('USER_ID', $org_id);
		$this->db->set('STATUS', 'active');

		if($this->db->insert('WSW_USER_ROLE') != TRUE)
		{
			return 'error';
		}
		else
		{
			return 'added';
		}
	}

	function add_org_to_comp($email,$name,$competition_id)
	{
		$query = $this->add_organization($email,$name);

		if($query == 'error')
		{
			return $query;
		}
		else
		{
			$org_id = $this->get_org_id($email);
			$query = $this->add_org_user_role($org_id);

			if($query == 'error')
			{
				return $query;
			}
			else
			{
				$this->db->set('ORG_ID', $org_id);
				$this->db->set('EVENT_ID', $competition_id);

				if($this->db->insert('WSW_ORG_COMP') != TRUE)
				{
					return 'error';
				}
				else
				{
					return 'added';
				}
			}
		}
	}
}

?>