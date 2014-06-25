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
				'value' => $row->USER_ID,
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
		//$this->form_validation->set_rules('zipcode', 'Zipcode', 'required|min_length[5]|max_length[5]');

		//if either email or zipcode is empty returns error
		if($this->form_validation->run() === FALSE)
		{
			$this->session->set_flashdata('error', 'Error with either email or zipcode');
			redirect('participant/signup');
		}
		else
		{
			//check to make sure email is valid
			$email = $this->security->xss_clean($this->input->post('email'));
			
			//$zipcode = $this->security->xss_clean($this->input->post('zipcode'));
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
					$this->Participant_model->insert_participant($email);
				} catch (Exception $e) {
					echo 'Caught exception: ', $e->getMessage(), "\n";
				}
				
				//grab participant id
				$participant_id = $this->Participant_model->get_participant_id($email);

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

				//set cookie for participant
				$cookie_participant_id = array(
				'name' => 'participant_id',
				'value' => $participant_id,
				'expire' => 86500,
				);

				//redirect to question page after enrollment is complete
				$session_data = array(
					'email' => $email,
					'isLoggedin' => TRUE
					);

				$this->input->set_cookie($cookie_participant_id);
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

		$flag = 0;
		$noQuestions = 0;
		$today = strtotime(date('Y-m-d'));
		$data['competition'] = $this->Participant_model->get_competition_data();
		$data['question'] = new ArrayObject();
		$data['answer'] = new ArrayObject();
		

		$competition_id = $data['competition']->COMPETITION_ID;
		$start_date = strtotime($data['competition']->START_DATE);
		$end_date = strtotime($data['competition']->END_DATE);
		$name = $data['competition']->COMPETITION_NAME;
		
		//$today_date = strtotime(date('d-m-Y'));
		//for now \/ but ^ for actual competition
		$today = strtotime('2014-07-07');
		$today_date = date('d-m-Y', $today);

		//switch to $today once testing is done
		if($today < $start_date)
		{
			echo "competition has not yet started";
		}
		elseif ($today > $end_date) {
			echo "competition has already ended";
		}
		else
		{
			//get question for the specific date
			$query = $this->Participant_model->get_question_data_from_date_question($competition_id);
		
			$participant_id = $this->input->cookie('participant_id');

			//for each question 
			foreach($query->result() as $row)
			{
				$date = date('d-m-Y',strtotime($row->QUESTION_DATE));
				if($today_date == $date)
				{
					$noQuestions++;

					//check if user has answered the question already
					$user_question_data = $this->Participant_model->get_user_question_data($participant_id, $row->QUESTION_ID);

					if($user_question_data->num_rows() == 0 && $flag == 0)
					{
						//get question data using the question id
						$question_data = $this->Participant_model->get_question($row->QUESTION_ID);

						$data['question'] = $question_data; 
					
						$answer = $this->Participant_model->get_answers($question_data->QUESTION_ID);

						//for each answer that coincides with the question just gotten
						foreach ($answer->result() as $ans_row) {
							//put data into an array of objects
							$data['answer']->append($ans_row);
						}
						

						$flag++;
					}
					
				}

			}

			
			if($noQuestions == 0)
			{
				redirect('participant/noQuestions');
			}

			$data['flag'] = $flag;
			
			if($flag == 0)
			{
				redirect('participant/giveCommitment');
			}
		$this->load->view('template/header');
		$this->load->view('participant/participant_question_page',$data);
		
		}		
	}

	public function noQuestions()
	{
		echo "no questions for today";
	}

	//logic for checking the answers submitted from the question page and giving commitments
	public function answerMultipleChoiceQuestion()
	{

		if(!$this->session->userdata('isLoggedin'))
		{
			redirect('participant/');
		}

		$data['competition'] = $this->Participant_model->get_competition_data();
		$data['question'] = new ArrayObject();
		$data['answer'] = new ArrayObject();
		$data['answer_type'] = 'multiple_choice';
		
		//get participant id from cookie
		$participant_id = $this->input->cookie('participant_id');

		//get competition id
		$competition_id = $this->Participant_model->get_competition_id();

		//grab the answer chosen id from the question 
		$answer = $this->security->xss_clean($this->input->post('answer'));

		//grab all information for the answer given by the answer_id
		$correct = $this->Participant_model->check_answer($answer);

		//get the question by using the answer id
		$question_data = $this->Participant_model->get_question($correct->QUESTION_ID);

		//put question data in object array to be sent to form
		$data['question'] = $question_data;

		//get all the answers for this question
		$all_answers = $this->Participant_model->get_answers($question_data->QUESTION_ID);
	
		//for each answer that coincides with the question just gotten
		foreach ($all_answers->result() as $ans_row) {
			//put data into an array of objects
			$data['answer']->append($ans_row);
		}
		

		//add the participant and question in the user_question table
		$this->Participant_model->insert_into_user_question($participant_id, $correct->QUESTION_ID, $competition_id, $answer);
		
		//display whether the answer is correct or not
		if($correct->CORRECT == 'y')
		{
			$data['correct'] = TRUE;
		}
		else
		{
			$data['correct'] = FALSE;
		}
		
		//load form
		$this->load->view('participant/participant_show_answer',$data);
	}

	public function answerMultipleSelectQuestion()
	{
		if(!$this->session->userdata('isLoggedin'))
		{
			redirect('participant/');
		}

		$data['competition'] = $this->Participant_model->get_competition_data();
		$data['question'] = new ArrayObject();
		$data['answer'] = new ArrayObject();
		$data['correct_answer'] = new ArrayObject();
		$data['answer_type'] = 'multiple_choice';
		
		//get participant id from cookie
		$participant_id = $this->input->cookie('participant_id');

		//get competition id
		$competition_id = $this->Participant_model->get_competition_id();

		$num_answers = $this->security->xss_clean($this->input->post('num_answers'));

		//grab the answer chosen id from the question 
		$answer = $this->security->xss_clean($this->input->post('answer'));

		//for each question selected see if it is right or wrong
		foreach ($answer as $ans) 
		{
			//grab all information for the answer given by the answer_id
			$correct = $this->Participant_model->check_answer($ans);

			$answer_array = array(
				'answer' => $ans,
				'correct' => $correct->CORRECT
				);

			$data['correct_answer']->append($answer_array);

			$this->Participant_model->insert_into_user_question($participant_id, $correct->QUESTION_ID, $competition_id, $ans);
		}
			

		//get the question by using the answer id
		$question_data = $this->Participant_model->get_question($correct->QUESTION_ID);

		//get all the answers for this question
		$all_answers = $this->Participant_model->get_answers($question_data->QUESTION_ID);

		//for each answer that coincides with the question just gotten
		foreach ($all_answers->result() as $ans_row) {
			//put data into an array of objects
			$data['answer']->append($ans_row);
		}
		
	
		//put question data in object array to be sent to form
		$data['question'] = $question_data;

		
		//display whether the answer is correct or not

		//load form
		$this->load->view('participant/participant_show_answer',$data);		
	}

	//logic for checking the answers of true and false questions
	public function answerTrueFalseQuestion()
	{
		if(!$this->session->userdata('isLoggedin'))
		{
			redirect('participant/');
		}

		$data['competition'] = $this->Participant_model->get_competition_data();
		$data['question'] = new ArrayObject();
		$data['answer_type'] = 'true_false';

		//get participant id from cookie
		$participant_id = $this->input->cookie('participant_id');

		//get competition id
		$competition_id = $this->Participant_model->get_competition_id();

		//grab the answer chosen id from the question 
		$answer_id = $this->security->xss_clean($this->input->post('answer_id'));

		//grab the answer from the input
		$answer = $this->security->xss_clean($this->input->post('answer'));

		//grab all information for the answer given by the answer_id
		$correct = $this->Participant_model->check_answer($answer_id);

		//get the question by using the answer id
		$question_data = $this->Participant_model->get_question($correct->QUESTION_ID);

		//put question data in object array to be sent to form
		$data['question'] = $question_data;

		//convert the clob to a string
		$correct_answer = $correct->ANSWER->load();

		if($correct_answer == $answer)
		{
			$data['correct'] = TRUE;
		}
		else
		{
			$data['correct'] = FALSE;
		}

		$data['answer'] = $correct_answer;
		
		//add the participant and question in the user_question table
		$this->Participant_model->insert_into_user_question($participant_id, $correct->QUESTION_ID, $competition_id, $answer_id);

		//load form
		$this->load->view('participant/participant_show_answer',$data);
	}

	//give the commitment to the participant
	public function giveCommitment()
	{
		$participant_id = $this->input->cookie('participant_id');
		$competition_id = $this->Participant_model->get_competition_id();

		//check to see if a commitment has been added that day for the participant
		$commit = $this->Participant_model->check_commitment($participant_id, $competition_id);

		//if commitment has been given already don't give another, redirect to message page
		if($commit > 0)
		{
			$this->session->set_flashdata('commitment', 'You have already recieved your commitment for the day');
			redirect('participant/info');
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
			$this->session->set_flashdata('commitment', 'Congratulations you recieved your commitment for the day!');
			redirect('participant/info');
		}
	}

	//info page that sets view for discussing what competition is about and for
	public function info()
	{
		if(!$this->session->userdata('isLoggedin'))
		{
			redirect('participant/');
		}

		$data['commitment'] = $this->session->flashdata('commitment');
		$this->load->view('template/header');
		$this->load->view('participant/participant_info_page',$data);
	}

	public function about()
	{
		$this->load->view('template/header');
		$this->load->view('participant/participant_about');
	}

	public function contact()
	{
		$this->load->view('template/header');
		$this->load->view('participant/participant_contact');
	}

	public function leaderboard()
	{
		if(!$this->session->userdata('isLoggedin'))
		{
			redirect('participant/');
		}
		$this->load->model('Admin_model');

		//get competition data for active competition and put in object array to send to view
		$competition_id = $this->Admin_model->get_competition_id();
		$query = $this->Admin_model->get_competition_data($competition_id);
		$data['competition'] = $query->row();

		//create object array to send to view
		$data['organization'] = new ArrayObject();
		

		$competition_id = $this->Admin_model->get_competition_id();

		//get all the active organizations for the active competition
		$org_data = $this->Admin_model->get_all_organizations($competition_id);

		//go through every org and get data from each
		foreach($org_data->result() as $org) {
			//reset the org commits
			$org_commits = 0;

			//get all participants associated with a specific organization
			$query = $this->Admin_model->get_participants_by_org($org->USER_ID);

			//go through each array of participants to get individual commitments
			foreach ($query->result() as $participant) {

				//get participant data
				$participant_data = $this->Admin_model->get_participant_data($participant->PARTICIPANT_ID);

				//get # of commitments by participants so far
				$participant_commits = $this->Admin_model->commits_by_user($participant_data->USER_ID);

				$org_commits = $org_commits + $participant_commits;

			}

			$num_rows = $this->Admin_model->check_org_competition_assoc($competition_id, $org->USER_ID);

			if($num_rows > 0)
			{
				//upload everything into array
				$total_commit_array = array(
					'user_id' => $org->USER_ID,
					'name' => $org->USER_NAME,
					'total_commits' => $org_commits
					);

				//add array to array of objects
				$data['organization']->append($total_commit_array);
			}
		}


		$this->load->view('template/header');
		$this->load->view('participant/participant_leaderboard',$data);
	}

	public function destroy_session()
	{
		$this->session->sess_destroy();
	}

}
?>	