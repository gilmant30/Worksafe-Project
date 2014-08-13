<?php
class Competition_model extends CI_Model {

	//these are the event_type id's gotten from the db
	public $competition = '1';
	public $course = '2';

	//parent function that loads database	
	function __construct()
	{
		parent::__construct();
		$this->load->database();
	}

	function get_user_from_server()
	{
		$headers = apache_request_headers();

		if (!isset($headers['Authorization'])){
			header('HTTP/1.1 401 Unauthorized');
			header('WWW-Authenticate: NTLM');
			exit;
		}

		$auth = $headers['Authorization'];

		if (substr($auth,0,5) == 'NTLM ') {
			$msg = base64_decode(substr($auth, 5));
			if (substr($msg, 0, 8) != "NTLMSSP\x00")
				die('error header not recognised');

			if ($msg[8] == "\x01") {
				$msg2 = "NTLMSSP\x00\x02"."\x00\x00\x00\x00". // target name len/alloc
					"\x00\x00\x00\x00". // target name offset
					"\x01\x02\x81\x01". // flags
					"\x00\x00\x00\x00\x00\x00\x00\x00". // challenge
					"\x00\x00\x00\x00\x00\x00\x00\x00". // context
					"\x00\x00\x00\x00\x30\x00\x00\x00"; // target info len/alloc/offset

				header('HTTP/1.1 401 Unauthorized');
				header('WWW-Authenticate: NTLM '.trim(base64_encode($msg2)));
				exit;
			}
			else if ($msg[8] == "\x03") {
			function get_msg_str($msg, $start, $unicode = true) {
				$len = (ord($msg[$start+1]) * 256) + ord($msg[$start]);
				$off = (ord($msg[$start+5]) * 256) + ord($msg[$start+4]);
				if ($unicode)
					return str_replace("\0", '', substr($msg, $off, $len));
				else
					return substr($msg, $off, $len);
			}
			$user = get_msg_str($msg, 36);

			return $user;
			}
		}	
	}

	//get all the organizations that are active
	function get_all_organizations()
	{
		$query = $this->db->query("SELECT wsw_user.user_name, wsw_user.user_id FROM wsw_user INNER JOIN wsw_user_role ON wsw_user.user_id = wsw_user_role.user_id INNER JOIN wsw_role ON wsw_role.role_id = wsw_user_role.role_id WHERE wsw_user_role.role_id = '2' AND wsw_user_role.status = 'active'");

		//return entire query
		return $query;
	}

	//check to see if participant email is in the database and if so that they are a participant
	function check_participant_email($email)
	{
		$query = $this->db->query("SELECT wsw_user.user_name, wsw_user.user_id FROM wsw_user INNER JOIN wsw_user_role ON wsw_user.user_id = wsw_user_role.user_id WHERE wsw_user.email = '$email' AND wsw_user_role.role_id = '3'");

		return $query;
	}

	//check to see if participant email is in the database and if so that they are a participant
	function check_participant_email_with_comp($email,$competition_id)
	{
		$query = $this->db->query("SELECT wsw_user.user_name, wsw_user.user_id FROM wsw_user INNER JOIN wsw_user_role ON wsw_user.user_id = wsw_user_role.user_id INNER JOIN wsw_user_org_assoc ON wsw_user_org_assoc.participant_id = wsw_user.user_id WHERE wsw_user.email = '$email' AND wsw_user_role.role_id = '3' AND wsw_user_org_assoc.event_id = '$competition_id'");

		return $query;
	}

	//insert the participant into 'user' table with parameters given
	function insert_participant($email)
	{
		$data = array(
			'EMAIL' => $email,
			);

		//insert into db, throw error if data not inserted
		if( $this->db->insert('WSW_USER', $data) != TRUE)
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

	//insert the participant into the 'wsw_user_role' table with the participant role associated with them
	function insert_participant_into_user_role($participant_id)
	{
		$data = array(
			'ROLE_ID' => 3,
			'USER_ID' => $participant_id,
			'STATUS' => 'active',
			);

		//insert into db, throw error if data not inserted
		if( $this->db->insert('WSW_USER_ROLE', $data) != TRUE)
		{
			throw new Exception("Cannot insert into wsw_user_role table");
		}
		else
		{
			return $this->db->affected_rows();
		}
	}

	//insert into the 'wsw_user_org_assoc' table to associate the participant with the organization they will represent
	function assoc_user_org($participant_id, $org_id, $competition_id)
	{
		$data = array(
			'ORG_ID' => $org_id,
			'PARTICIPANT_ID' => $participant_id,
			'EVENT_ID' => $competition_id
		);

		//insert into db, throw error if data not inserted
		if( $this->db->insert('WSW_USER_ORG_ASSOC', $data) != TRUE)
		{
			throw new Exception("Cannot insert into wsw_user_org_assoc table");
		}
		else
		{
			return $this->db->affected_rows();
		}
	}

	//get competition data for the active competition
	function get_competition_data($competition_id)
	{
		$query = $this->db->query("SELECT * FROM wsw_event WHERE active = 'y' AND event_type_id = '$this->competition' AND event_id = '$competition_id'");
		
		if($query->num_rows() == 1)
		{
			return $query->row();
		}
		else
		{
			return 0;
		}
	}

	//get competition data for the active competition
	function get_competitions()
	{
		$query = $this->db->query("SELECT * FROM wsw_event WHERE active = 'y' AND event_type_id = '$this->competition'");
		
		if($query->num_rows() == 1)
		{
			return 1;
		}
		else
		{
			return 0;
		}
	}	

	function get_courses()
	{
		$query = $this->db->query("SELECT * FROM wsw_event WHERE active = 'y' AND event_type_id = '$this->course'");

		if($query->num_rows() > 0)
		{
			return 1;
		}
		else
		{
			return 0;
		}
	}

	//get competition id for the active competition
	function get_competition_id()
	{
		$query = $this->db->query("SELECT * FROM wsw_event WHERE active = 'y' AND event_type_id = '$this->competition'");
		
		if($query->num_rows() == 1)
		{
			$row = $query->row();
			return $row->EVENT_ID;
		}
		else
		{
			echo "error with retrieving competition id";
		}
	}

	//get the data for the questions for the particular day it is
	function get_question_data_from_date_question($competition_id)
	{
		$query = $this->db->query("SELECT * FROM wsw_date_question WHERE event_id = '$competition_id'");

		if($query->num_rows() > 0)
			return $query;
		else
			echo "error with retrieving question data from get_question_data_from_date_question";
	}

	//get the question data for a single question by the question id
	function get_question($question_id)
	{
		$query = $this->db->query("SELECT * FROM wsw_question WHERE question_id = '$question_id'");

		if($query->num_rows() == 1)
			return $query->row();
		else
			echo "error with retrieving question from get_questions function";
	}

	//get all the answer data for a particular question
	function get_answers($question_id)
	{
		$query = $this->db->query("SELECT * FROM wsw_answer WHERE question_id = '$question_id'");

		if($query->num_rows() > 0)
			return $query;
		else
			echo "error with retrieving question from get_answers function";
	}

	//get the number of questions in the competition per day
	function get_questions_per_day_in_competition()
	{
		$query = $this->db->query("SELECT question_per_day FROM wsw_event WHERE active = 'y';");

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
		$query = $this->db->query("SELECT * FROM wsw_answer WHERE answer_id = '$answer_id'");

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
		$this->db->set('EVENT_ID', $competition_id);
		$this->db->set('COMMITMENT_DATE', "TO_DATE('$date','YYYY-MM-DD')",false);

		//insert into db, throw error if data not inserted
		if( $this->db->insert('WSW_COMMITMENT') != TRUE)
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
		$query = $this->db->query("SELECT * FROM wsw_commitment WHERE user_id = '$participant_id' AND event_id = '$competition_id'");

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
		$query = $this->db->query("SELECT * FROM wsw_user_question WHERE user_id = '$participant_id' AND question_id = '$question_id'");

		return $query;
	}

	function insert_into_user_question($participant_id, $question_id, $competition_id, $answer_id, $correct)
	{
		$data = array(
			'USER_ID' => $participant_id,
			'QUESTION_ID' => $question_id,
			'ANSWER_ID' => $answer_id,
			'EVENT_ID' => $competition_id, 
			'CORRECT' => $correct
			);

		//insert into db, throw error if data not inserted
		if( $this->db->insert('WSW_USER_QUESTION', $data) != TRUE)
		{
			throw new Exception("Cannot insert into user_question table");
		}
		else
		{
			return $this->db->affected_rows();
		}
	}

	function get_category_name($category_id)
	{
		$query = $this->db->query("SELECT * FROM WSW_CATEGORY WHERE CATEGORY_ID = '$category_id'");

		$query = $query->row();

		return $query->CATEGORY_NAME;
	}

	function get_all_org_competition($competition_id)
	{
		$query = $this->db->query("SELECT wsw_user.user_name, wsw_user.user_id FROM wsw_user INNER JOIN wsw_user_role ON wsw_user.user_id = wsw_user_role.user_id INNER JOIN wsw_role ON wsw_role.role_id = wsw_user_role.role_id INNER JOIN wsw_org_comp ON wsw_org_comp.ORG_ID = wsw_user.USER_ID WHERE wsw_user_role.role_id = '2' AND wsw_user_role.status = 'active' AND wsw_org_comp.EVENT_ID = '$competition_id'");
		
		return $query;
	}

	function competition_exists($competition_id)
	{
		$query = $this->db->query("SELECT * FROM wsw_event WHERE event_id = '$competition_id' AND active = 'y'");

		$query = $query->num_rows();

		return $query;
	}
}
?>