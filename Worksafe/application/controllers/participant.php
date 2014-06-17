<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Participant extends CI_Controller {

	//parent function
	function __construct()
	{
		parent::__construct();
		$this->load->helper(array('form', 'url', 'string', 'cookie'));  //load a form and the base_url
        $this->load->library(array('form_validation', 'security', 'session')); //set form_validation rules and xss_cleaning
        $this->load->model('Participant_model');
	}

	//index page
	public function index()
	{
		//if user is logged in redirect to info page
		if($this->session->userdata('isLoggedin'))
		{
			redirect('participant/info');
		}

		$data['error'] = $this->session->flashdata('error');
		$this->load->view('participant/participant_login', $data);
	}

	//logic for logging in as a participant
	public function login()
	{

		//put validation on so email field is required
		$this->form_validation->set_rules('email', 'Email', 'required');

		//if email is empty returns error
		if($this->form_validation->run() === FALSE)
		{
			$this->session->set_flashdata('error', 'Must not leave the email field blank');
			redirect('participant/index');
		}

		//continue if email is not empty
		else
		{
			//grab email from the postfield
			$email = $this->security->xss_clean($this->input->post('email'));

			//query db to make sure email is already in the database
			$query = $this->Participant_model->check_participant_email($email);

			//if number of rows returned is greater than 0 then email exists 
			if($query->num_rows() > 0)
			{
				//grab all info for the user that has logged in
				$row = $query->row();

				//set cookie for participant id
				$cookie_participant_id = array(
				'name' => 'participant_id',
				'value' => $row->user_id,
				'expire' => 86500,
				);

				//set session data for logged in
				$session_data = array(
					'email' => $email,
					'isLoggedin' => TRUE
					);

				//set cookie and session
				$this->input->set_cookie($cookie_participant_id);
				$this->session->set_userdata($session_data);
				
				//redirect to the info page
				redirect('participant/info');
			}

			//if email is not in the db return an error
			else
			{

				$this->session->set_flashdata('error', 'Email is not yet registered');
				redirect('participant/index');
			}
		}
	}

	//sets page with organizations for new person signing up
	public function signup()
	{
		//if user is logged in redirect to info page
		if($this->session->userdata('isLoggedin'))
		{
			redirect('participant/info');
		}

		$query['error'] = $this->session->flashdata('error');
		//get all the organiztions in the database to fill the dropdown
		$query['organization'] = $this->Participant_model->get_all_organizations();

		$this->load->view('participant/participant_signup',$query);
	}

	//logic for enrolling a new person
	public function enroll()
	{

		//put validation on so email field is required
		$this->form_validation->set_rules('email', 'Email', 'required');
		$this->form_validation->set_rules('zipcode', 'Zipcode', 'required');

		//if either email or zipcode is empty returns error
		if($this->form_validation->run() === FALSE)
		{
			$this->session->set_flashdata('error', 'Must not leave the email or zipcode fields blank');
			redirect('participant/signup');
		}
		else
		{
			//check to make sure email is valid
			$email = $this->security->xss_clean($this->input->post('email'));
			$zipcode = $this->security->xss_clean($this->input->post('zipcode'));
			$org_id = $this->security->xss_clean($this->input->post('organization'));

			//query to see if email is already registered
			$query = $this->Participant_model->check_participant_email($email);


			//if number of rows is greater than 0 then user is registered
			if($query->num_rows() > 0)
			{
				$this->session->set_flashdata('error', 'User is already registered');
				redirect('participant/signup');
			}
			else
			{
				
				//send to participant_model to run function insert_participant(), throw error if it didn't add to database
				try
				{
					//insert participant into db
					$this->Participant_model->insert_participant($email,$zipcode);
				} catch (Exception $e) {
					echo 'Caught exception: ', $e->getMessage(), "\n";
				}
				
				$participant_id = $this->Participant_model->get_participant_id($email,$zipcode);

				//send to participant_model to run function insert_participant_into_user_role(), throw error if it didn't add to database
				try
				{
					//insert participant into db
					$this->Participant_model->insert_participant_into_user_role($participant_id);
				} catch (Exception $e) {
					echo 'Caught exception: ', $e->getMessage(), "\n";
				}
				
				//send to participant_model to run function assoc_user_org(), throw error if it didn't add to database
				try
				{
					//assoc participant with organization
					$this->Participant_model->assoc_user_org($participant_id, $org_id);
				} catch (Exception $e) {
					echo 'Caught exception: ', $e->getMessage(), "\n";
				}

				//set session for participant

				//redirect to question page after enrollment is complete
				$session_data = array(
					'email' => $email,
					'isLoggedin' => TRUE
					);

			$this->session->set_userdata($session_data);
				redirect('participant/info');
									
			}
		}
	}

	//logic for grabbing questions for correct competition and day
	//also fills view with data
	public function questionPage()
	{
		if(!$this->session->userdata('isLoggedin'))
		{
			redirect('participant/');
		}

		$today = strtotime(date('Y-m-d'));
		$data['competition'] = $this->Participant_model->get_competition_data();
		$data['question'] = new ArrayObject();
		$data['answer'] = new ArrayObject();
		
		$row = $data['competition']->row();

		$competition_id = $row->competition_id;
		$start_date = strtotime($row->start_date);
		$end_date = strtotime($row->end_date);
		$name = $row->name;
		
		/*
		if($today > $start_date)
		{
			echo "competition has not yet started";
		}
		elseif ($today < $end_date) {
			echo "competition has already ended";
		}
		else
		{
			$data['competition'] = $name;
			$this->load->view('participant/participant_question_page'$data);
		}

		*/

		//goes inside else statement
		//for testing purposes only
		$today_date = '2014-06-15';


		//get question for the specific date
		$query = $this->Participant_model->get_question_data_from_date_question($competition_id,$today_date);
		
		//make sure a row exists
		if($query->num_rows() > 0)
		{
			//for each question 
			foreach($query->result() as $row)
			{
				//get question data using the question id
				$question_data = $this->Participant_model->get_questions($row->question_id);
				
				//put the data into an array of objects
				$question_data = $question_data->result();

				//add to the array of object in data['question']
				$data['question']->append($question_data);

				//echo $question_data[0]->question_id;
				//echo "<br />";

				$answer = $this->Participant_model->get_answers($question_data[0]->question_id);

				if($answer->num_rows > 0)
				{
					//for each answer that coincides with the question just gottne
					foreach ($answer->result() as $ans_row) {
						//put data into an array of objects
						$data['answer']->append($ans_row);
					}
					
				}

				else
				{
					echo "error getting answers from database";
				}
			}


			//var_dump($data['question']);
			
		}
		else
		{
			echo "error getting data from database";
		}

		$this->load->view('participant/participant_question_page',$data);
	}

	//logic for checking the answers submitted from the question page and giving commitments
	public function answerQuestions()
	{

		if(!$this->session->userdata('isLoggedin'))
		{
			redirect('participant/');
		}

		//grabs the number of questions per day for the competition
		$num = $this->Participant_model->get_questions_per_day_in_competition();

		//goes through the number of questions per day
		for($i=0;$i<$num;$i++)
		{
			//grab the correct answer_id from each question 
			$answer = $this->security->xss_clean($this->input->post('correct_ans_q'.$i));

			//grab all information for the answer given by the answer_id
			$correct = $this->Participant_model->check_answer($answer);
			
			/*
			//display whether the answer is correct or not
			if($correct->correct == 'y')
			{
				echo $correct->answer.' is correct';
			}
			else
			{
				echo $correct->answer.' is incorrect';
			}
			echo '<br />';
			*/
		}

		$participant_id = $this->input->cookie('participant_id');
		$competition_id = $this->Participant_model->get_competition_id();

		//check to see if a commitment has been added that day for the participant
		$commit = $this->Participant_model->check_commitment($participant_id, $competition_id);

		//if commitment has been given already don't give another, redirect to message page
		if($commit->num_rows() > 0)
		{
			echo "commitment already gotten for the day";
		}
		else
		{
			//send to participant_model to run function add_commitment, throw error if it didn't add to database
			try
			{
				//insert participant into db
				$this->Participant_model->add_commitment($participant_id, $competition_id);
			} catch (Exception $e) {
				echo 'Caught exception: ', $e->getMessage(), "\n";
			}

			redirect('participant/addedCommitment');
		}
	}

	//info page that sets view for discussing what competition is about and for
	public function info()
	{
		if(!$this->session->userdata('isLoggedin'))
		{
			redirect('participant/');
		}
		$this->load->view('participant/participant_info_page');
	}

	public function addedCommitment()
	{
		echo 'commitment added';
	}

	public function destroy_session()
	{
		$this->session->sess_destroy();
	}

}
?>	