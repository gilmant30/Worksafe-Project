<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Competition extends CI_Controller {

	//parent function
	function __construct()
	{
		parent::__construct();
		$this->load->helper(array('form', 'url', 'string', 'cookie'));  //load a form and the base_url
        $this->load->library(array('form_validation', 'security', 'session')); //set form_validation rules and xss_cleaning
        $this->load->model('Competition_model');
	}


	//index/home page
	public function index($competition_id)
	{
		if(empty($competition_id))
		{
			if(empty($this->input->cookie('competition_id')))
				redirect('competition/fail');
			
			else if(!empty($this->input->cookie('competition_id')))
			{
				$competition_id = $this->input->cookie('competition_id');
				redirect('competition/index/'.$competition_id.'');
			}
		}

		if($this->Competition_model->competition_exists($competition_id) == 0)
		{
			redirect('competition/fail');
		}

		//set cookie for participant id
		$cookie_competition_id = array(
		'name' => 'competition_id',
		'value' => $competition_id,
		'expire' => 86500,
		);

		$this->input->set_cookie($cookie_competition_id);
	
		$data['error'] = $this->session->flashdata('error');
		$data['competition_id'] = $competition_id;
		$data['commitment'] = $this->session->flashdata('commitment');

		if(empty($this->input->cookie('participant_id')))
		{
			$this->load->view('template/login_header', $data);
			$data['signup'] = TRUE;
		}
		else
		{
			$this->load->view('template/header', $data);
			$data['signup'] = FALSE;
		}

		$this->load->view('competition/competition_home_page', $data);

		if($data['signup'] == TRUE)
			$this->load->view('competition/competition_login',$data);	
	}

	public function fail()
	{
		echo 'no comp id in the url or the competition does not exist';
	}

	//logic for logging in as a participant
	public function login()
	{

		//put validation on so email field is required
		$this->form_validation->set_rules('email', 'Email', 'required');

		//get hidden competition id
		$competition_id = $this->security->xss_clean($this->input->post('competition_id'));

		//if email is empty returns error
		if($this->form_validation->run() === FALSE)
		{
			$this->session->set_flashdata('error', 'Must not leave the email field blank');
			redirect('competition/index/'.$competition_id.'');
		}

		//continue if email is not empty
		else
		{
			//grab email from the postfield
			$email = $this->security->xss_clean($this->input->post('email'));

			//query db to make sure email is already in the database
			$query = $this->Competition_model->check_participant_email_with_comp($email, $competition_id);

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

				//set cookie for participant id
				$cookie_competition_id = array(
				'name' => 'competition_id',
				'value' => $competition_id,
				'expire' => 86500,
				);

				//set session data for logged in
				$session_data = array(
					'email' => $email,
					'isLoggedin' => TRUE
					);

				//set cookie and session
				$this->input->set_cookie($cookie_participant_id);
				$this->input->set_cookie($cookie_competition_id);
				$this->session->set_userdata($session_data);
				
				//redirect to the info page
				redirect('competition/index/'.$competition_id.'');
			}

			//if email is not in the db return an error
			else
			{

				$this->session->set_flashdata('error', 'Email is not yet registered');
				redirect('competition/index/'.$competition_id.'');
			}
		}
	}

	//sets page with organizations for new person signing up
	public function signup()
	{
		//if user is logged in redirect to info page
		if($this->session->userdata('isLoggedin'))
		{
			redirect('competition/info');
		}

		$query['error'] = $this->session->flashdata('error');
		//get all the organiztions in the database to fill the dropdown
		$query['organization'] = $this->Competition_model->get_all_org_competition($this->input->cookie('competition_id'));

		$this->load->view('competition/competition_signup',$query);
	}

	//logic for enrolling a new person
	public function enroll()
	{

		//put validation on so email field is required
		$this->form_validation->set_rules('email', 'Email', 'required|valid_email');

		//if either email or zipcode is empty returns error
		if($this->form_validation->run() === FALSE)
		{
			$this->session->set_flashdata('error', 'Must include a valid email address to sign up');
			redirect('competition/signup');
		}
		else
		{
			//check to make sure email is valid
			$email = $this->security->xss_clean($this->input->post('email'));
			
			$org_id = $this->security->xss_clean($this->input->post('organization'));

			$competition_id = $this->input->cookie('competition_id');

			//query to see if email is already registered
			$query = $this->Competition_model->check_participant_email_with_comp($email,$competition_id);


			//if number of rows is greater than 0 then user is registered
			if($query->num_rows() > 0)
			{
				$this->session->set_flashdata('error', 'User is already registered with this competition');
				redirect('competition/signup');
			}
			else
			{
				//check to see if user is already in the db
				$query = $this->Competition_model->check_participant_email($email);

				//if user is not insert
				if($query->num_rows() == 0)
				{
					//send to Competition_model to run function insert_participant(), throw error if it didn't add to database
					try
					{
						//insert participant into db
						$this->Competition_model->insert_participant($email);
					} catch (Exception $e) {
						echo 'Caught exception: ', $e->getMessage(), "\n";
					}
				}

				//grab participant id
				$participant_id = $this->Competition_model->get_participant_id($email);

				//send to Competition_model to run function insert_participant_into_user_role(), throw error if it didn't add to database
				try
				{
					//insert participant into db
					$this->Competition_model->insert_participant_into_user_role($participant_id);
				} catch (Exception $e) {
					echo 'Caught exception: ', $e->getMessage(), "\n";
				}
				
				//send to Competition_model to run function assoc_user_org(), throw error if it didn't add to database
				try
				{
					//assoc participant with organization
					$this->Competition_model->assoc_user_org($participant_id, $org_id, $competition_id);
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
				redirect('competition/index/'.$competition_id.'');
									
			}
		}
	}

	//logic for grabbing questions for correct competition and day
	//also fills view with data
	public function questionPage()
	{
		if(!$this->session->userdata('isLoggedin'))
		{
			redirect('competition/');
		}

		$competition_id = $this->input->cookie('competition_id');

		$data['error'] = $this->session->flashdata('error');

		$flag = 0;
		$noQuestions = 0;
		$today = strtotime(date('Y-m-d'));
		$data['competition'] = $this->Competition_model->get_competition_data($competition_id);
		$data['question'] = new ArrayObject();
		$data['answer'] = new ArrayObject();
		
		$start_date = strtotime($data['competition']->START_DATE);
		$end_date = strtotime($data['competition']->END_DATE);
		$name = $data['competition']->EVENT_NAME;
		
		$today = strtotime(date('d-m-Y'));
		$today_date = date('d-m-Y');
		//for now \/ but ^ for actual competition
		//$today = strtotime('2014-06-30');
		//$today_date = date('d-m-Y', $today);

		//check whether the competition has started yet
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
			$query = $this->Competition_model->get_question_data_from_date_question($competition_id);
		
			$participant_id = $this->input->cookie('participant_id');

			//for each question 
			foreach($query->result() as $row)
			{
				$date = date('d-m-Y',strtotime($row->QUESTION_DATE));
				if($today_date == $date)
				{
					$noQuestions++;

					//check if user has answered the question already
					$user_question_data = $this->Competition_model->get_user_question_data($participant_id, $row->QUESTION_ID);

					if($user_question_data->num_rows() == 0 && $flag == 0)
					{
						//get question data using the question id
						$question_data = $this->Competition_model->get_question($row->QUESTION_ID);

						$data['category'] = $this->Competition_model->get_category_name($question_data->CATEGORY_ID);

						$data['question'] = $question_data; 
					
						$answer = $this->Competition_model->get_answers($question_data->QUESTION_ID);

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
				redirect('competition/noQuestions');
			}

			$data['flag'] = $flag;
			
			if($flag == 0)
			{
				redirect('competition/giveCommitment');
			}

		//load forms
		$this->load->view('template/header');
		$this->load->view('competition/competition_question_page',$data);
		}		
	}

	public function noQuestions()
	{
		echo "no questions for today";
	}

	//about page
	public function about()
	{
		if(empty($this->input->cookie('participant_id')))
		{
			$this->load->view('template/login_header');
		}
		else
		{
			$this->load->view('template/header');
		}
		$this->load->view('competition/competition_about');
	}

	//contact page currently empty
	public function contact()
	{
		if(empty($this->input->cookie('participant_id')))
		{
			$this->load->view('template/login_header');
		}
		else
		{
			$this->load->view('template/header');
		}
		$this->load->view('competition/competition_contact');
	}

	//leaderboard page to show the organizations competing and the leaders
	public function leaderboard()
	{

		$this->load->model('Admin_model');

		//get competition data for active competition and put in object array to send to view
		$competition_id = $this->input->cookie('competition_id');
		$data['competition'] = $this->Competition_model->get_competition_data($competition_id);
		

		//create object array to send to view
		$data['organization'] = new ArrayObject();
		

		//get all the active organizations for the active competition
		$org_data = $this->Competition_model->get_all_org_competition($competition_id);

		//go through every org and get data from each
		foreach($org_data->result() as $org) {
			//reset the org commits
			$org_commits = 0;

			$correct = $this->Admin_model->get_org_correct_ans($org->USER_ID);
			$total = $this->Admin_model->get_org_total_ans($org->USER_ID);
			
			if($total == 0)
				$percent_correct = 0;
			else
			{
				$percent_correct = (intval($correct)/intval($total)) * 100;
				$percent_correct = number_format($percent_correct, 2, '.', '');
			}

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
					'total_commits' => $org_commits,
					'percent_correct' => $percent_correct
					);

				//add array to array of objects
				$data['organization']->append($total_commit_array);
			}
		}


		if(empty($this->input->cookie('participant_id')))
		{
			$this->load->view('template/login_header');
		}
		else
		{
			$this->load->view('template/header');
		}
		$this->load->view('competition/competition_leaderboard',$data);
	}

	//logic for checking the multiple choice answers
	public function answerMultipleChoiceQuestion()
	{

		if(!$this->session->userdata('isLoggedin'))
		{
			redirect('competition/');
		}

		//put validation on so email field is required
		$this->form_validation->set_rules('answer', 'Answer', 'required');

		//if email is empty returns error
		if($this->form_validation->run() === FALSE)
		{
			$this->session->set_flashdata('error', 'Must select an answer');
			redirect('competition/questionPage');
		}

		$competition_id = $this->input->cookie('competition_id');
		$data['competition'] = $this->Competition_model->get_competition_data($competition_id);
		$data['question'] = new ArrayObject();
		$data['answer'] = new ArrayObject();
		$data['answer_type'] = 'multiple_choice';
		
		//get participant id from cookie
		$participant_id = $this->input->cookie('participant_id');

		//grab the answer chosen id from the question 
		$answer = $this->security->xss_clean($this->input->post('answer'));

		//grab all information for the answer given by the answer_id
		$correct = $this->Competition_model->check_answer($answer);

		//get the question by using the answer id
		$question_data = $this->Competition_model->get_question($correct->QUESTION_ID);

		//get the category name by the question data
		$data['category'] = $this->Competition_model->get_category_name($question_data->CATEGORY_ID);

		//put question data in object array to be sent to form
		$data['question'] = $question_data;

		//get all the answers for this question
		$all_answers = $this->Competition_model->get_answers($question_data->QUESTION_ID);
	
		//for each answer that coincides with the question just gotten
		foreach ($all_answers->result() as $ans_row) {
			//put data into an array of objects
			$data['answer']->append($ans_row);
		}
		

		//add the participant and question in the user_question table
		$this->Competition_model->insert_into_user_question($participant_id, $correct->QUESTION_ID, $competition_id, $answer, $correct->CORRECT);
		
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
		$this->load->view('competition/competition_show_answer',$data);
	}

	//logic for checking the multiple select questions
	public function answerMultipleSelectQuestion()
	{
		if(!$this->session->userdata('isLoggedin'))
		{
			redirect('competition/');
		}

		//put validation on so email field is required
		$this->form_validation->set_rules('answer', 'Answer', 'required');

		//if email is empty returns error
		if($this->form_validation->run() === FALSE)
		{
			$this->session->set_flashdata('error', 'Must select an answer');
			redirect('competition/questionPage');
		}

		$competition_id = $this->input->cookie('competition_id');
		$data['competition'] = $this->Competition_model->get_competition_data($competition_id);
		$data['question'] = new ArrayObject();
		$data['answer'] = new ArrayObject();
		$data['correct_answer'] = new ArrayObject();
		$data['answer_type'] = 'multiple_choice';
		$data['correct'] = TRUE;
		//get participant id from cookie
		$participant_id = $this->input->cookie('participant_id');

		$num_answers = $this->security->xss_clean($this->input->post('num_answers'));

		//grab the answer chosen id from the question 
		$answer = $this->security->xss_clean($this->input->post('answer'));

		//for each question selected see if it is right or wrong
		foreach ($answer as $ans) 
		{
			//grab all information for the answer given by the answer_id
			$correct = $this->Competition_model->check_answer($ans);

			$answer_array = array(
				'answer' => $ans,
				'correct' => $correct->CORRECT
				);

			if($correct->CORRECT == 'n')
			{
				$data['correct'] = FALSE;
			}

			$data['correct_answer']->append($answer_array);

			$this->Competition_model->insert_into_user_question($participant_id, $correct->QUESTION_ID, $competition_id, $ans, $correct->CORRECT);
		}
			

		//get the question by using the answer id
		$question_data = $this->Competition_model->get_question($correct->QUESTION_ID);

		//get the category name by the question data
		$data['category'] = $this->Competition_model->get_category_name($question_data->CATEGORY_ID);

		//get all the answers for this question
		$all_answers = $this->Competition_model->get_answers($question_data->QUESTION_ID);

		//for each answer that coincides with the question just gotten
		foreach ($all_answers->result() as $ans_row) {
			//put data into an array of objects
			$data['answer']->append($ans_row);
		}
		
	
		//put question data in object array to be sent to form
		$data['question'] = $question_data;

		
		//display whether the answer is correct or not

		//load form
		$this->load->view('competition/competition_show_answer',$data);		
	}

	//logic for checking the answers of true and false questions
	public function answerTrueFalseQuestion()
	{
		if(!$this->session->userdata('isLoggedin'))
		{
			redirect('competition/');
		}

		//put validation on so email field is required
		$this->form_validation->set_rules('answer', 'Answer', 'required');

		//if email is empty returns error
		if($this->form_validation->run() === FALSE)
		{
			$this->session->set_flashdata('error', 'Must select an answer');
			redirect('competition/questionPage');
		}

		$competition_id = $this->input->cookie('competition_id');
		$data['competition'] = $this->Competition_model->get_competition_data($competition_id);;
		$data['question'] = new ArrayObject();
		$data['answer_type'] = 'true_false';

		//get participant id from cookie
		$participant_id = $this->input->cookie('participant_id');

		//grab the answer chosen id from the question 
		$answer_id = $this->security->xss_clean($this->input->post('answer_id'));

		//grab the answer from the input
		$answer = $this->security->xss_clean($this->input->post('answer'));

		//grab all information for the answer given by the answer_id
		$correct = $this->Competition_model->check_answer($answer_id);

		//get the question by using the answer id
		$question_data = $this->Competition_model->get_question($correct->QUESTION_ID);

		//get the category name by the question data
		$data['category'] = $this->Competition_model->get_category_name($question_data->CATEGORY_ID);

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
		$this->Competition_model->insert_into_user_question($participant_id, $correct->QUESTION_ID, $competition_id, $answer_id, $correct->CORRECT);

		//load form
		$this->load->view('competition/competition_show_answer',$data);
	}

	//give the commitment to the participant
	public function giveCommitment()
	{
		$participant_id = $this->input->cookie('participant_id');
		$competition_id = $this->input->cookie('competition_id');

		//check to see if a commitment has been added that day for the participant
		$commit = $this->Competition_model->check_commitment($participant_id, $competition_id);

		//if commitment has been given already don't give another, redirect to message page
		if($commit > 0)
		{
			$this->session->set_flashdata('commitment', 'You have already received your point for the day');
			redirect('competition/index/'.$competition_id.'');
		}
		else
		{
			//send to Competition_model to run function add_commitment, throw error if it didn't add to database
			try
			{
				//insert participant into db
				$this->Competition_model->add_commitment($participant_id, $competition_id);
			} catch (Exception $e) {
				echo 'Caught exception: ', $e->getMessage(), "\n";
			}
			$this->session->set_flashdata('commitment', 'Congratulations you recieved your point for the day!');
			redirect('competition/index/'.$competition_id.'');
		}
	}

	//logging out
	public function logout()
	{
		$competition_id = $this->input->cookie('competition_id');
		$this->session->sess_destroy();
		delete_cookie('participant_id');
		delete_cookie('competition_id');
		redirect('competition/index/'.$competition_id.'');
	}

}
?>	