<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Admin extends CI_Controller {

	public $competition = '1';
	public $course = '2';

	//parent function
	function __construct()
	{
		parent::__construct();
		$this->load->helper(array('form', 'url', 'string', 'cookie'));  //load a form and the base_url
        $this->load->library(array('form_validation', 'security', 'session')); //set form_validation rules and xss_cleaning
        $this->load->model('Admin_model');
        date_default_timezone_set('America/Chicago');
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

	public function selectEventType()
	{
		//if user is not logged in redirect to login page
		if(!$this->session->userdata('adminLoggedin'))
		{
			redirect('admin/');
		}

		$data['event_type'] = $this->Admin_model->get_event_types();

		$this->load->view('admin/select_event',$data);
	}

	public function createNewEvent($event_type_id)
	{
		//if user is not logged in redirect to login page
		if(!$this->session->userdata('adminLoggedin'))
		{
			redirect('admin/');
		}

		if($event_type_id == $this->competition)
		{
			redirect('admin/newCompetition');
		}
		else if($event_type_id == $this->course)
		{
			redirect('admin/newCourse');
		}
		else
		{
			echo 'must enter a valid event type';
		}
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
			$title = $this->security->xss_clean($this->input->post('title'));
			$event_type_id = $this->security->xss_clean($this->input->post('event_type_id'));

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
				$this->Admin_model->insert_competition_format($start_date, $end_date, $days, $title, $event_type_id);
			} catch (Exception $e) {
				echo 'Caught exception: ', $e->getMessage(), "\n";
			}

			//redirect to createQuestion page
			redirect("admin/index");
		}
	}


	//load view that has form for new course
	public function newCourse()
	{
		//if user is not logged in redirect to login page
		if(!$this->session->userdata('adminLoggedin'))
		{
			redirect('admin/');
		}
		$data['error'] = $this->session->flashdata('error');
		$data['error_title'] = $this->session->flashdata('error_title');
		$this->load->view('admin/new_course',$data);
	}

	public function createCourse()
	{
		//if user is not logged in redirect to login page
		if(!$this->session->userdata('adminLoggedin'))
		{
			redirect('admin/');
		}
		
		//put required validation on all form fields
		$this->form_validation->set_rules('title', 'Title', 'required');

		//if any fields are empty throw error
		if($this->form_validation->run() === FALSE)
		{
			$this->session->set_flashdata('error', 'Must not leave name field blank');
			redirect('admin/newCourse');
		}

		//get rest of form data
		$title = $this->security->xss_clean($this->input->post('title'));
		$event_type_id = $this->security->xss_clean($this->input->post('event_type_id'));

		//send to admin_model to run function insert_competition_format(), throw error if it didn't add to database
		try
		{
			$this->Admin_model->insert_course($title, $event_type_id);
		} catch (Exception $e) {
			echo 'Caught exception: ', $e->getMessage(), "\n";
		}

		redirect('admin/index');
	}

	public function selectCompetition()
	{
		//if user is not logged in redirect to login page
		if(!$this->session->userdata('adminLoggedin'))
		{
			redirect('admin/');
		}

		$data['competition'] = $this->Admin_model->get_all_competitions();

		$this->load->view('admin/choose_competition',$data);
	}

	//gets data and loads view that shows a review of the competition
	public function reviewCompetition($competition_id)
	{
		//if user is not logged in redirect to login page
		if(!$this->session->userdata('adminLoggedin'))
		{
			redirect('admin/');
		}

		//puts update and error value if there is one
		$data['update'] = $this->session->flashdata('update');
		$data['error'] = $this->session->flashdata('error');
		$data['competition_id'] = $competition_id;
		//create object array to send to view
		$data['review'] = new ArrayObject();
		
		//grab all questions that have to do with the specific competition id
		$question = $this->Admin_model->get_all_questions($competition_id);

		if($question->num_rows() == 0)
		{
			$data['no_questions'] = 'not empty';
		}
		else
		{
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
		}
		//load review page
		$this->load->view('admin/review_competition',$data);
	}

	public function deleteQuestionAssoc($question_id)
	{
		//if user is not logged in redirect to login page
		if(!$this->session->userdata('adminLoggedin'))
		{
			redirect('admin/');
		}

		$this->Admin_model->delete_question($question_id);

		$this->session->set_flashdata('update','question has been deleted');
		redirect('admin/reviewCompetition',$data);
	}

	//show all competitions that are in the db
	public function showEvent()
	{
		//if user is not logged in redirect to login page
		if(!$this->session->userdata('adminLoggedin'))
		{
			redirect('admin/');
		}

		$data['delete_competition'] = $this->session->flashdata('delete_competition');
		//get all data for competitions
		$data['array'] = $this->Admin_model->get_all_events();
		$data['event_type'] = $this->Admin_model->get_event_types();
		$this->load->view('admin/show_event',$data);
	}

	//logic for editing competition
	public function editCompetition()
	{
		//if user is not logged in redirect to login page
		if(!$this->session->userdata('adminLoggedin'))
		{
			redirect('admin/');
		}

		//get the competition id
		$competition_id = $this->input->post('competition_id');

		//get all the questions for that competition with the competition id
		$questions = $this->Admin_model->get_all_questions($competition_id);

		foreach ($questions->result() as $row) {
			//get question data for each question
			$question_data = $this->Admin_model->get_question_data($row->QUESTION_ID);

			//get the input data from the form
			$question_string = $this->security->xss_clean($this->input->post('q'.$question_data->QUESTION_ID));
			
			//set form_validation rules
			$this->form_validation->set_rules('q'.$question_data->QUESTION_ID, 'Question', 'required');

			//get answer
			$answers = $this->Admin_model->get_all_answers($row->QUESTION_ID);

			foreach ($answers->result() as $ans) {
				//get input from form
				$answer_string = $this->security->xss_clean($this->input->post('a'.$ans->ANSWER_ID));
				//set form validation rules
				$this->form_validation->set_rules('a'.$ans->ANSWER_ID, 'Answer', 'required');
			}
		}

		//if any fields are empty throw error
		if($this->form_validation->run() === FALSE)
		{
			$this->session->set_flashdata('error', 'Must not leave any fields blank');
			redirect('admin/reviewCompetition/'.$competition_id.'');
		}

		foreach ($questions->result() as $row) {
			//get question data for each question
			$question_data = $this->Admin_model->get_question_data($row->QUESTION_ID);

			//get the input data from the form
			$question_string = $this->security->xss_clean($this->input->post('q'.$question_data->QUESTION_ID));

			//update or do nothing to the question
			$this->Admin_model->check_question($question_data->QUESTION_ID, $question_string);
			
			$answers = $this->Admin_model->get_all_answers($row->QUESTION_ID);

			foreach ($answers->result() as $ans) {
				$answer_string = $this->security->xss_clean($this->input->post('a'.$ans->ANSWER_ID));

				//update or do nothing to the answer
				$this->Admin_model->check_answer($ans->ANSWER_ID, $answer_string);
			}
		}

		$this->session->set_flashdata('update', 'Questions and answers have been updated');
		redirect('admin/reviewCompetition/'.$competition_id.'');
	}

	public function addOrganization($competition_id)
	{
		$query = $this->Admin_model->get_competition_data($competition_id);
		$data['competition'] = $query->row();

		//validate form
		$this->form_validation->set_rules('email', 'Email', 'required|valid_email');
		$this->form_validation->set_rules('name', 'Name', 'required');

		if($this->form_validation->run() == FALSE)
		{
			$data['success'] = '';
			$this->load->view('admin/new_organization', $data);
		}
		else
		{	
			$email = $this->security->xss_clean($this->input->post('email'));
			$name = $this->security->xss_clean($this->input->post('name'));

			$query = $this->Admin_model->add_org_to_comp($email,$name,$competition_id);
			
			if($query == 'error')
			{
				$data['success'] = 'Error with adding the organization';
				$this->load->view('admin/new_organization', $data);
			}
			else
			{
				$data['success'] = 'Organization has been added';
				$this->load->view('admin/new_organization', $data);
			}
			
		}
	}

	//show all active organizations for the active competition
	public function showOrganization($competition_id)
	{
		//if user is not logged in redirect to login page
		if(!$this->session->userdata('adminLoggedin'))
		{
			redirect('admin/');
		}

		//get competition data for active competition and put in object array to send to view
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

			$correct = $this->Admin_model->get_org_correct_ans($org->USER_ID, $competition_id);
			$total = $this->Admin_model->get_org_total_ans($org->USER_ID, $competition_id);
			if($total == 0)
			{
				$percent_correct = '0.00';
			}
			else
			{
				$percent_correct = (intval($correct)/intval($total)) * 100;
				$percent_correct = number_format($percent_correct, 2, '.', '');
			}

			//get all participants associated with a specific organization
			$query = $this->Admin_model->get_participants_by_org($org->USER_ID, $competition_id);

			//go through each array of participants to get individual commitments
			foreach ($query->result() as $participant) {

				//get participant data
				$participant_data = $this->Admin_model->get_participant_data($participant->PARTICIPANT_ID);

				//get # of commitments by participants so far
				$participant_commits = $this->Admin_model->commits_by_user($participant_data->USER_ID, $competition_id);

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



		$this->load->view('admin/show_organization',$data);
	}

	//show all participants associated with a specific user
	public function showParticipants($competition_id, $org_id)
	{
		//if user is not logged in redirect to login page
		if(!$this->session->userdata('adminLoggedin'))
		{
			redirect('admin/');
		}

		//get the organization data to send to view
		$data['org'] = $this->Admin_model->get_org_data($org_id);

		//get competition id
		$data['competition_id'] = $competition_id;

		//create object array to send to view
		$data['participant'] = new ArrayObject();

		//get all participants associated with a specific organization
		$query = $this->Admin_model->get_participants_by_org($org_id, $competition_id);

		//go through each participant to get their commitments
		foreach ($query->result() as $row) {
			//get participant data
			$participant_data = $this->Admin_model->get_participant_data($row->PARTICIPANT_ID);

			//get the # of correct answers for each participant
			$correct = $this->Admin_model->get_participant_correct_ans($row->PARTICIPANT_ID, $competition_id);
			
			//get the total number of answers for each participant
			$total = $this->Admin_model->get_participant_total_ans($row->PARTICIPANT_ID, $competition_id);
			if($total == 0)
			{
				$percent_correct = '0.00';
			}
			else
			{
				//calculate the percentage correct
				$percent_correct = (intval($correct)/intval($total)) * 100;
				$percent_correct = number_format($percent_correct, 2, '.', '');				
			}


			//get # of commitments by participants so far
			$commits = $this->Admin_model->commits_by_user($participant_data->USER_ID, $competition_id);

			//put data into array
			$participant_array = array(
				'user_id' => $participant_data->USER_ID,
				'email' => $participant_data->EMAIL,
				'commit' => $commits,
				'percent_correct' => $percent_correct
				 );

			//append to end of object array
			$data['participant']->append($participant_array);
		}

		//load view
		$this->load->view('admin/show_participant',$data);
	}


	//logic for switching the active course
	public function activateEvent($event_id)
	{
		//if user is not logged in redirect to login page
		if(!$this->session->userdata('adminLoggedin'))
		{
			redirect('admin/');
		}

		$this->Admin_model->activate_event($event_id);
		redirect('admin/showEvent');
	}

	public function deleteCompetition($competition_id)
	{
		//if user is not logged in redirect to login page
		if(!$this->session->userdata('adminLoggedin'))
		{
			redirect('admin/');
		}

			$this->Admin_model->delete_competition($competition_id);
			redirect('admin/showEvent');

	}

	//for testing to destroy session
	public function logout()
	{
		$this->session->sess_destroy();
	}

	//get all events so you can associate an event when creating a question
	public function questionEvent()
	{
		//if user is not logged in redirect to login page
		if(!$this->session->userdata('adminLoggedin'))
		{
			redirect('admin/');
		}

		$data['events'] =  $this->Admin_model->get_all_events();
		$data['error'] = $this->session->flashdata('error');
		if($data['events'] == NULL)
		{
			$data['error'] = 'Add an event to add questions to it';
		}
		$this->load->view('admin/question_event', $data);
	}

	//get info for creating a question
	public function createQuestion()
	{
		//if user is not logged in redirect to login page
		if(!$this->session->userdata('adminLoggedin'))
		{
			redirect('admin/');
		}

		$data['error'] = $this->session->flashdata('error');
		$data['added'] = $this->session->flashdata('added');
		$event_id = $this->security->xss_clean($this->input->post('event_id'));

		$query = $this->Admin_model->get_competition_data($event_id);

		$data['event'] = $query->row();
		$this->load->view('admin/create_question',$data);
	}

	//uploads all question information from the create question form
	public function uploadQuestion()
	{
		//if user is not logged in redirect to login page
		if(!$this->session->userdata('adminLoggedin'))
		{
			redirect('admin/');
		}

		//get the event id for the event
		$event_id = $this->input->post('event_id');
		$event_type_id = $this->Admin_model->get_event_type_id($event_id);

		//put validation on so all fields are required
		$this->form_validation->set_rules('question', 'Question', 'required');
		$this->form_validation->set_rules('category', 'Category', 'required');

		if($event_type_id == $this->competition)
			$this->form_validation->set_rules('question_date', 'Question date', 'required');

		//if email is empty returns error
		if($this->form_validation->run() === FALSE)
		{
			$this->session->set_flashdata('error', 'Must not leave any field blank');
			redirect('admin/createQuestion');
		}

		//get all information from form
		$question = $this->security->xss_clean($this->input->post('question'));
		$question_type = $this->security->xss_clean($this->input->post('option_type'));
		$category = $this->security->xss_clean($this->input->post('category'));
		$source = $this->security->xss_clean($this->input->post('source'));

		if(empty($source))
		{
			$source = NULL;
		}

		if($event_type_id == $this->competition)
		{
			$question_date = $this->security->xss_clean($this->input->post('question_date'));
			$competition = $this->Admin_model->get_competition_data($event_id);
			$competition = $competition->row();

			$start_date = strtotime($competition->START_DATE);
			$end_date = strtotime($competition->END_DATE);
			
			$today = strtotime(date('d-m-Y'));

			//check whether the competition has started yet
			if($today < $start_date || $today > $end_date)
			{
				$this->session->set_flashdata('error', 'date must be within the competition timeline');
				redirect('admin/questionEvent');
			}

		}
		//send category name to insert_category function returns category id
		$category_id = $this->Admin_model->insert_category($category);

		//send to admin_model to run function insert_question(), throw error if it didn't add to database
		try
		{
			$this->Admin_model->insert_question($question,$category_id,$question_type,$source);
		} catch (Exception $e) {
			echo 'Caught exception: ', $e->getMessage(), "\n";
		}

		//retrieve question_id
		$question_id = $this->Admin_model->get_question_id($question,$category_id,$question_type,$event_id);

		if($event_type_id == $this->competition)
		{
			//send to admin_model to run function insert_date_question(), throw error if it didn't add to database
			try
			{
				$this->Admin_model->insert_date_question($question_id,$event_id,$question_date);
			} catch (Exception $e) {
				echo 'Caught exception: ', $e->getMessage(), "\n";
			}
		}
		else if($event_type_id == $this->course)
		{
			//send data to course question to be inserted
			try
			{
				$this->Admin_model->insert_course_question($question_id,$event_id);
			} catch (Exception $e) {
				echo 'Caught exception: ', $e->getMessage(), "\n";
			}
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
			redirect('admin/questionEvent');

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
			redirect('admin/questionEvent');
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
			redirect('admin/questionEvent');
		}	
	}




}
