<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Admin extends CI_Controller {

	//parent function
	function __construct()
	{
		parent::__construct();
		$this->load->helper(array('form', 'url', 'string', 'cookie'));  //load a form and the base_url
        $this->load->library(array('form_validation', 'security', 'session')); //set form_validation rules and xss_cleaning
        $this->load->model('Admin_model');
	}

	//gets user to login page
	public function index()
	{
		//if admin is already logged in send to competition page
		if($this->session->userdata('adminLoggedin'))
		{
			redirect('admin/competition');
		}

		$data['error'] = $this->session->flashdata('error');
		$this->load->view('admin/admin_login',$data);
	}

	//logic for logging in
	public function login()
	{
		//put validation on so email and password fields are required
		$this->form_validation->set_rules('email', 'Email', 'required');
		$this->form_validation->set_rules('password', 'Password', 'required');

		//if either is empty returns error
		if($this->form_validation->run() === FALSE)
		{
			$this->session->set_flashdata('error', 'Must not leave the email or password fields blank');
			redirect('admin/index');
		}

		//continue if fields are not empty
		else
		{
			//get post fields
			$email = $this->security->xss_clean($this->input->post('email'));
			$pass = $this->security->xss_clean($this->input->post('password'));
			
			//check to see if user is an admin in the db
			$query = $this->Admin_model->validate_admin_login($email,$pass);
			
			//if number of rows returned is zero then user is not in the db so return an error else redirect to competition page
			if($query->num_rows() >0)
			{
				//set session data for admin logged in
				$session_data = array(
				'adminLoggedin' => TRUE
				);

				//set session
				$this->session->set_userdata($session_data);
				redirect('admin/competition');
			}
			else
			{
			$this->session->set_flashdata('error', 'Email or password is incorrect');
			redirect('admin/index');
			}
		}
	}

	//loads main admin page
	public function competition()
	{
		//if user is not logged in redirect to login page
		if(!$this->session->userdata('adminLoggedin'))
		{
			redirect('admin/');
		}
		$this->load->view('admin/competition');
	}

	//load view that has form for a new competition
	public function newCompetition()
	{
		//if user is not logged in redirect to login page
		if(!$this->session->userdata('adminLoggedin'))
		{
			redirect('admin/');
		}
		$data['error'] = $this->session->flashdata('error');
		$data['error_title'] = $this->session->flashdata('error_title');
		$this->load->view('admin/new_competition',$data);
	}

	//logic for creating a new competition
	public function createCompetition()
	{
		//if user is not logged in redirect to login page
		if(!$this->session->userdata('adminLoggedin'))
		{
			redirect('admin/');
		}
		//load helper
		$this->load->helper('date');

		//put required validation on all form fields
		$this->form_validation->set_rules('from', 'From', 'required');
		$this->form_validation->set_rules('to', 'To', 'required');
		//$this->form_validation->set_rules('num_questions_per_day', 'Number of questions', 'required');
		//$this->form_validation->set_rules('num_answers', 'Number of answers', 'required');
		$this->form_validation->set_rules('title', 'Title', 'required');

		//if any fields are empty throw error
		if($this->form_validation->run() === FALSE)
		{
			$this->session->set_flashdata('error', 'Must not leave any fields blank');
			redirect('admin/newCompetition');
		}

		//if all fields filled in continue
		else
		{
			//get start_date convert string into date 
			$start_date = $this->security->xss_clean($this->input->post('from'));
			$start_date = date('Y-m-d', strtotime($start_date));
			$time_start = strtotime($start_date);
		
			//get end_date convert string into date
			$end_date = $this->security->xss_clean($this->input->post('to'));
			$end_date = date('Y-m-d', strtotime($end_date));
			$time_end = strtotime($end_date);

			//get rest of form data
			//$num_question = $this->security->xss_clean($this->input->post('num_questions_per_day'));
			//$num_answers = $this->security->xss_clean($this->input->post('num_answers'));
			$title = $this->security->xss_clean($this->input->post('title'));

			//check if the title name is already being used
			$query = $this->Admin_model->check_competition_title($title);

			//redirect to newCompetition page if title is already used
			if($query != 0)
			{
				$this->session->set_flashdata('error_title', 'title name is already being used');
				redirect('admin/newCompetition');
			}
			//find number of days in between start and end date		
			$difference = abs($time_end - $time_start); 
			$days = floor($difference/(60*60*24)) + 1;

			//send to admin_model to run function insert_competition_format(), throw error if it didn't add to database
			try
			{
				$this->Admin_model->insert_competition_format($start_date, $end_date, $days, $title);
			} catch (Exception $e) {
				echo 'Caught exception: ', $e->getMessage(), "\n";
			}

			//set cookie for the question day for creating questions
			$cookie_question_day = array(
				'name' => 'question_day',
				'value' => 1,
				'expire' => 86500,
				);

			//create cookie
			$this->input->set_cookie($cookie_question_day);

			//redirect to createQuestion page
			redirect("admin/test");
		}
	}


	//gets data and loads view that shows a review of the competition
	public function reviewCompetition()
	{
		//if user is not logged in redirect to login page
		if(!$this->session->userdata('adminLoggedin'))
		{
			redirect('admin/');
		}

		//puts update and error value if there is one
		$data['update'] = $this->session->flashdata('update');
		$data['error'] = $this->session->flashdata('error');
		
		//create object array to send to view
		$data['review'] = new ArrayObject();

		//get active competition
		$competition_id = $this->Admin_model->get_competition_id();
		
		//grab all questions that have to do with the specific competition id
		$question = $this->Admin_model->get_all_questions($competition_id);

		//loop through all questions
		foreach ($question->result() as $question) {
			$answer = new ArrayObject();
			
			$question_date = $question->QUESTION_DATE;

			//get all the question data
			$question_data = $this->Admin_model->get_question_data($question->QUESTION_ID);

			//get all the answers for a specific question
			$answer_array = $this->Admin_model->get_all_answers($question_data->QUESTION_ID);

			//loop through each answer
			foreach ($answer_array->result() as $row) {
				
				//add all the answer data to the answer array
				$answer->append($row);
			}

			//put question and answers in a review array
			$review_array = array(
				'question_id' => $question_data->QUESTION_ID,
				'question_name' => $question_data->QUESTION,
				'question_type' => $question_data->QUESTION_TYPE,
				'question_date' => $question_date,
				'answer_data' => $answer
				);

			//append array to object array to be sent to view
			$data['review']->append($review_array);

		}

		//load review page
		$this->load->view('admin/review_competition',$data);
	}

	//show all competitions that are in the db
	public function showCompetition()
	{
		//if user is not logged in redirect to login page
		if(!$this->session->userdata('adminLoggedin'))
		{
			redirect('admin/');
		}

		$data['delete_competition'] = $this->session->flashdata('delete_competition');
		//get all data for competitions
		$data['array'] = $this->Admin_model->get_all_competitions();
		$this->load->view('admin/show_competition',$data);
	}

	//logic for editing competition
	public function editCompetition()
	{

		//get the competition id
		$competition_id = $this->Admin_model->get_competition_id();

		//get all the questions for that competition with the competition id
		$questions = $this->Admin_model->get_all_questions($competition_id);

		foreach ($questions->result() as $row) {
			//get question data for each question
			$question_data = $this->Admin_model->get_question_data($row->question_id);

			//get the input data from the form
			$question_string = $this->security->xss_clean($this->input->post('q'.$question_data->question_id));
			
			//set form_validation rules
			$this->form_validation->set_rules('q'.$question_data->question_id, 'Question', 'required');

			//get answer
			$answers = $this->Admin_model->get_all_answers($row->question_id);

			foreach ($answers->result() as $ans) {
				//get input from form
				$answer_string = $this->security->xss_clean($this->input->post('a'.$ans->answer_id));
				//set form validation rules
				$this->form_validation->set_rules('a'.$ans->answer_id, 'Answer', 'required');
			}
		}

		//if any fields are empty throw error
		if($this->form_validation->run() === FALSE)
		{
			$this->session->set_flashdata('error', 'Must not leave any fields blank');
			redirect('admin/reviewCompetition');
		}

		foreach ($questions->result() as $row) {
			//get question data for each question
			$question_data = $this->Admin_model->get_question_data($row->question_id);

			//get the input data from the form
			$question_string = $this->security->xss_clean($this->input->post('q'.$question_data->question_id));

			//update or do nothing to the question
			$this->Admin_model->check_question($question_data->question_id, $question_string);
			
			$answers = $this->Admin_model->get_all_answers($row->question_id);

			foreach ($answers->result() as $ans) {
				$answer_string = $this->security->xss_clean($this->input->post('a'.$ans->answer_id));

				//update or do nothing to the answer
				$this->Admin_model->check_answer($ans->answer_id, $answer_string);
			}
		}

		$this->session->set_flashdata('update', 'Questions and answers have been updated');
		redirect('admin/reviewCompetition');
	}

	//show all active organizations for the active competition
	public function showOrganization()
	{
		//if user is not logged in redirect to login page
		if(!$this->session->userdata('adminLoggedin'))
		{
			redirect('admin/');
		}

		//get competition data for active competition and put in object array to send to view
		$competition_id = $this->Admin_model->get_competition_id();
		$query = $this->Admin_model->get_competition_data($competition_id);
		$data['competition'] = $query->row();

		//create object array to send to view
		$data['organization'] = new ArrayObject();
		

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



		$this->load->view('admin/show_organization',$data);
	}

	//show all participants associated with a specific user
	public function showParticipants($org_id)
	{
		//if user is not logged in redirect to login page
		if(!$this->session->userdata('adminLoggedin'))
		{
			redirect('admin/');
		}

		//get the organization data to send to view
		$data['competition'] = $this->Admin_model->get_org_data($org_id);

		//create object array to send to view
		$data['participant'] = new ArrayObject();

		//get all participants associated with a specific organization
		$query = $this->Admin_model->get_participants_by_org($org_id);

		//go through each participant to get their commitments
		foreach ($query->result() as $row) {
			//get participant data
			$participant_data = $this->Admin_model->get_participant_data($row->PARTICIPANT_ID);

			//get # of commitments by participants so far
			$commits = $this->Admin_model->commits_by_user($participant_data->USER_ID);

			//put data into array
			$participant_array = array(
				'user_id' => $participant_data->USER_ID,
				'email' => $participant_data->EMAIL,
				'commit' => $commits
				 );

			//append to end of object array
			$data['participant']->append($participant_array);
		}

		//load view
		$this->load->view('admin/show_participant',$data);
	}

	//logic for switching the active competition
	public function activateCompetition($competition_id)
	{
		//if user is not logged in redirect to login page
		if(!$this->session->userdata('adminLoggedin'))
		{
			redirect('admin/');
		}

		$this->Admin_model->activate_competition($competition_id);
		redirect('admin/showCompetition');
	}

	public function deleteCompetition($competition_id)
	{
		//if user is not logged in redirect to login page
		if(!$this->session->userdata('adminLoggedin'))
		{
			redirect('admin/');
		}

		//check to see if the competition to be deleted is the active one
		$num_rows = $this->Admin_model->check_if_active($competition_id);

		//if the competition is not active delete it
		if($num_rows == 0)
		{
			$this->Admin_model->delete_competition($competition_id);
			redirect('admin/showCompetition');
		}
		//if the competition is active throw an error
		else
		{
			$this->session->set_flashdata('delete_competition', 'You cannot delete the active competition');
			redirect('admin/showCompetition');
		}
	}

	//for testing to destroy session
	public function destroy_session()
	{
		$this->session->sess_destroy();
	}

	public function createQuestion()
	{
		$data['error'] = $this->session->flashdata('error');
		$data['added'] = $this->session->flashdata('added');
		$this->load->view('admin/create_question',$data);
	}


	public function uploadQuestion()
	{
		//put validation on so all fields are required
		$this->form_validation->set_rules('question', 'Question', 'required');
		$this->form_validation->set_rules('category', 'Category', 'required');
		$this->form_validation->set_rules('question_date', 'Question date', 'required');

		//if email is empty returns error
		if($this->form_validation->run() === FALSE)
		{
			$this->session->set_flashdata('error', 'Must not leave any field blank');
			redirect('admin/test');
		}

		//get all information from form
		$question = $this->security->xss_clean($this->input->post('question'));
		$question_type = $this->security->xss_clean($this->input->post('option_type'));
		$category = $this->security->xss_clean($this->input->post('category'));
		$question_date = $this->security->xss_clean($this->input->post('question_date'));

		//get the competition id
		$competition_id = $this->input->cookie('competition_id');

		//send category name to insert_category function returns category id
		$category_id = $this->Admin_model->insert_category($category);

		//send to admin_model to run function insert_question(), throw error if it didn't add to database
		try
		{
			$this->Admin_model->insert_question($question,$category_id,$question_type);
		} catch (Exception $e) {
			echo 'Caught exception: ', $e->getMessage(), "\n";
		}

		//retrieve question_id
		$question_id = $this->Admin_model->get_question_id($question,$category_id,$question_type,$competition_id);

		//send to admin_model to run function insert_date_question(), throw error if it didn't add to database
		try
		{
			$this->Admin_model->insert_date_question($question_id,$competition_id,$question_date);
		} catch (Exception $e) {
			echo 'Caught exception: ', $e->getMessage(), "\n";
		}
		
		
		//if the question type is true/false get true or false and insert into db
		if($question_type == 'true_false')
		{
			$answer = $this->security->xss_clean($this->input->post('answer'));		

			//send to admin_model to run function insert_true_false_answer
			try
			{
				$this->Admin_model->insert_true_false_answer($question_id, $answer);
			} catch (Exception $e) {
				echo 'Caught exception: ', $e->getMessage(), "\n";
			}

			$this->session->set_flashdata('added', 'True or false question added');
			redirect('admin/createQuestion');

		}

		//if question type is multiple choice insert all answers into db
		else if($question_type == 'multiple_choice')
		{
			$num_answers = $this->security->xss_clean($this->input->post('num_answers'));

			//get each answer for the question
			for($a=0;$a<($num_answers+1);$a++)
			{
				//get answer string
				$answer = $this->security->xss_clean($this->input->post('answer'.$a));
				
				//get radio button
				$correct = $this->security->xss_clean($this->input->post('radio_answer'.$a));
				
				//check whether the answer is the correct one by checking the value of the radio button
				if('correct_ans'.$a == $correct)
				{
					//if correct set var correct to 'y'				
					$correct = 'y';
				}
				else
				{
					//if answer is wrong answer set var correct to 'n'
					$correct = 'n';				
				}

				$this->Admin_model->insert_answer($answer,$correct,$question_id);	
			}

			$this->session->set_flashdata('added', 'Multiple choice question added');
			redirect('admin/createQuestion');
		}

		//if question type is multiple select insert all answers into db
		else if($question_type == 'multiple_select')
		{
			$num_answers = $this->security->xss_clean($this->input->post('num_answers'));

			//get each answer for the question
			for($a=0;$a<($num_answers+1);$a++)
			{
				//get answer string
				$answer = $this->security->xss_clean($this->input->post('answer'.$a));
				
				//get checkbox button
				$correct = $this->security->xss_clean($this->input->post('checkbox_answer'.$a));
		

				if('correct_ans'.$a == $correct)
				{	
					$correct = 'y';
				}
				else
				{
					$correct = 'n';
				}

				$this->Admin_model->insert_answer($answer,$correct,$question_id);	
			}

			$this->session->set_flashdata('added', 'Multiple select question added');
			redirect('admin/createQuestion');
		}	
	}



/*
	//page where question form is created
	public function createQuestion()
	{
		//if user is not logged in redirect to login page
		if(!$this->session->userdata('adminLoggedin'))
		{
			redirect('admin/');
		}

		//retrieve cookie data for and question day
		$question_day = $this->input->cookie('question_day');

		//grab the competition id from the active competition
		$id = $this->Admin_model->get_competition_id();

		//get all competition data from competition id
		$query = $this->Admin_model->get_competition_data($id);

		//make sure the query returns something
		if($query->num_rows() > 0)
		{
			$row = $query->row();
		}
		else
		{
			echo "error getting data from database";
		}

		//if the question day is equal to the number of days of the competition then go to the review competition page
		if($question_day >= $row->days_of_competition)
		{
			redirect('admin/reviewCompetition');
		}

		else
		{
			//put all data used on create_question page into array
			$data = array(
					'title' => $row->name,
					'questions' => $row->question_per_day,
					'answers' => $row->answers_per_day,
					'start_date' => $row->start_date,
					'end_date' => $row->end_date,
					'days_of_competition' => $row->days_of_competition,
					'current_day' => $question_day
				);


			//load create_question page with data array
			$this->load->view('admin/create_question', $data);
		}
	}
*/
	/*
	//upload questions into the database
	public function uploadQuestions()
	{
		//get cookie data
		$competition_id = $this->input->cookie('competition_id');
		$question_day = $this->input->cookie('question_day');

		//get all competition data from competition id to figure out num of questions and answers
		$query = $this->Admin_model->get_competition_data($competition_id);

		//make sure a row exists
		if($query->num_rows() > 0)
		{
			$row = $query->row();
		}
		else
		{
			echo "error getting data from database";
		}

		//get correct date, questions will be used on by adding the question day to the start day
		$date = strtotime("+$question_day day", strtotime($row->start_date));
		$date_question_asked = date("Y-m-d", $date); 


		//get all questions and answers from form
		for($i=1;$i<($row->question_per_day + 1);$i++)
		{
			//get the question and category input
			$question = $this->security->xss_clean($this->input->post('question'.$i));
			$category = $this->security->xss_clean($this->input->post('category'.$i));
			$type = $this->security->xss_clean($this->input->post('type'.$i));

			//send category name to insert_category function returns category id
			$category_id = $this->Admin_model->insert_category($category);

			//send to admin_model to run function insert_question(), throw error if it didn't add to database
			try
			{
				$this->Admin_model->insert_question($question,$category_id,$type,$competition_id);
			} catch (Exception $e) {
				echo 'Caught exception: ', $e->getMessage(), "\n";
			}

			//retrieve question_id
			$question_id = $this->Admin_model->get_question_id($question,$category_id,$type,$competition_id);


			//send to admin_model to run function insert_date_question(), throw error if it didn't add to database
			try
			{
				$this->Admin_model->insert_date_question($question_id,$competition_id,$date_question_asked);
			} catch (Exception $e) {
				echo 'Caught exception: ', $e->getMessage(), "\n";
			}

			
			//get each answer for the question
			for($a=1;$a<($row->answers_per_day +1);$a++)
			{
				//get answer string
				$answer = $this->security->xss_clean($this->input->post('q'.$i.'answer'.$a));
				
				//get radio button
				$correct = $this->security->xss_clean($this->input->post('correct_ans_q'.$i));
				
				//check whether the answer is the correct one by checking the value of the radio button
				if('q'.$i.'a'.$a == $correct)
				{
					//if correct set var correct to 'y'				
					$correct = 'y';
				}
				else
				{
					//if answer is wrong answer set var correct to 'n'
					$correct = 'n';				
				}

				$this->Admin_model->insert_answer($answer,$correct,$competition_id,$question_id);	
			}
			
		}

		//increment day
		$question_day = $question_day + 1;

		//put into cookie array
		$cookie_question_day = array(
			'name' => 'question_day',
			'value' => $question_day,
			'expire' => 86500,
			);

		//reload cookie
		$this->input->set_cookie($cookie_question_day);

		//redirect back to create question page and check whether there are more days or not
		redirect("admin/createQuestion");
	}
	*/

}
