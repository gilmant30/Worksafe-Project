<?php
class Participant_model extends CI_Model {
	
	function __construct()
	{
		parent::__construct();
		$this->load->database();
	}

	function get_all_organizations()
	{
		$query = $this->db->query("SELECT user.name, user.user_id FROM user INNER JOIN user_role ON user.user_id = user_role.user_id INNER JOIN roles ON roles.role_id = user_role.role_id WHERE user_role.role_id = '2';");

		return $query;

	}

	function check_participant_email($email)
	{
		//check if participant email is already in the db
		$query = $this->db->query("SELECT user.name, user.user_id FROM user INNER JOIN user_role ON user.user_id = user_role.user_id WHERE user.email = '$email' AND user_role.role_id = '3';");

		return $query;
	}

	function insert_participant($email,$zipcode)
	{
		$data = array(
			//id just for testing, automatically put in with oracle tables
			'user_id' => 4,
			'email' => $email,
			'zipcode' => $zipcode
			);

		//insert into db, throw error if data not inserted
		if( $this->db->insert('user', $data) != TRUE)
		{
			throw new Exception("Cannot insert into user table");
		}
		else
		{
			return $this->db->affected_rows();
		}
	}

	function get_participant_id($email,$zipcode)
	{
		//return query with user_id, where the role of that user is participant (3)
		$query = $this->db->query("SELECT user_id FROM user WHERE email = '$email' AND zipcode = '$zipcode';");

		if($query->num_rows() == 1)
		{
			$row = $query->row();
			return $row->user_id;
		}
		else
		{
			echo "error with retrieving question id";
		}
	}

	function insert_participant_into_user_role($participant_id)
	{
		$data = array(
			//id just for testing, automatically put in with oracle tables
			'user_role_id' => 4,
			'role_id' => 3,
			'user_id' => $participant_id,
			'status' => 'active',
			//creation date will be done when row is created
			);
		//insert into db, throw error if data not inserted
		if( $this->db->insert('user_role', $data) != TRUE)
		{
			throw new Exception("Cannot insert into user_role table");
		}
		else
		{
			return $this->db->affected_rows();
		}
	}

	function assoc_user_org($participant_id, $org_id)
	{
		$data = array(
			//id just for testing, automatically put in with oracle tables
			'user_org_assoc_id' => 1,
			'org_id' => $org_id,
			'participant_id' => $participant_id
		);

		//insert into db, throw error if data not inserted
		if( $this->db->insert('user_org_assoc', $data) != TRUE)
		{
			throw new Exception("Cannot insert into user_org_assoc table");
		}
		else
		{
			return $this->db->affected_rows();
		}
	}

	function get_competition_data()
	{
		$query = $this->db->query("SELECT * FROM competition WHERE active = 'yes';");
		
		if($query->num_rows() == 1)
		{
			return $query;
		}
		else
		{
			echo "error with retrieving competition id";
		}
	}
}
?>