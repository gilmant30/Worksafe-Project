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
		$this->form_validation->set_rules('num_questions_per_day', 'Number of questions', 'required');
		$this->form_validation->set_rules('num_answers', 'Number of answers', 'required');
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
			$time_start = date('Y-m-d', strtotime($start_date));
			$time_start = strtotime($time_start);
			$start_date = date('Y-m-d', strtotime($start_date));
		
			//get end_date convert string into date
			$end_date = $this->security->xss_clean($this->input->post('to'));
			$time_end = date('Y-m-d', strtotime($end_date));
			$time_end = strtotime($time_end);
			$end_date = date('Y-m-d', strtotime($end_date));

			//get rest of form data
			$num_question = $this->security->xss_clean($this->input->post('num_questions_per_day'));
			$num_answers = $this->security->xss_clean($this->input->post('num_answers'));
			$title = $this->security->xss_clean($this->input->post('title'));

			//find number of days in betweem days		
			$difference = abs($time_end - $time_start); 
			$days = floor($difference/(60*60*24)) + 1;

			//send to admin_model to run function insert_competition_format(), throw error if it didn't add to database
			try
			{
				$this->Admin_model->insert_competition_format($start_date, $end_date, $days, $num_question, $num_answers, $title);
			} catch (Exception $e) {
				echo 'Caught exception: ', $e->getMessage(), "\n";
			}

			//get competition id
			$query = $this->Admin_model->get_competition_id($title);

			//make sure the query returns something
			if($query->num_rows() > 0)
			{
				$row = $query->row();
			}
			else
			{
				echo "error getting data from database";
			}

			//set cookie for competition id
			$cookie_id = array(
				'name' => 'competition_id',
				'value' => $row->competition_id,
				'expire' => 86500,
				);

			//set cookie for the question day for creating questions
			$cookie_question_day = array(
				'name' => 'question_day',
				'value' => 1,
				'expire' => 86500,
				);

			//set cookies
			$this->input->set_cookie($cookie_id);
			$this->input->set_cookie($cookie_question_day);

			//redirect to createQuestion page
			redirect("admin/createQuestion");
		}
	}

	public function selectCompetition()
	{


	}

	//page where question form is created
	public function createQuestion()
	{
		//if user is not logged in redirect to login page
		if(!$this->session->userdata('adminLoggedin'))
		{
			redirect('admin/');
		}

		//retrieve cookie data for competition id and question day
		$id = $this->input->cookie('competition_id');
		$question_day = $this->input->cookie('question_day');

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

	//gets data and loads view that shows a review of the competition
	public function reviewCompetition()
	{
		//if user is not logged in redirect to login page
		if(!$this->session->userdata('adminLoggedin'))
		{
			redirect('admin/');
		}

		//get competition id from cookie
		$competition_id = $this->input->cookie('competition_id');
		
		//grab all questions that have to do with the specific competition id
		$query['questions'] = $this->Admin_model->get_all_questions($competition_id);

		//load review page
		$this->load->view('admin/review_competition');
	}

	//show all competitions that are in the db
	public function showCompetition()
	{
		//if user is not logged in redirect to login page
		if(!$this->session->userdata('adminLoggedin'))
		{
			redirect('admin/');
		}
		//get all data for competitions
		$query['array'] = $this->Admin_model->get_all_competitions();
		$this->load->view('admin/show_competition',$query);
	}

	//show all active organizations
	public function showOrganization()
	{
		//if user is not logged in redirect to login page
		if(!$this->session->userdata('adminLoggedin'))
		{
			redirect('admin/');
		}
		$query['organization'] = $this->Admin_model->get_all_organizations();
		$this->load->view('admin/show_organization',$query);
	}

	//show all participants associated with a specific user
	public function showParticipants($org_id)
	{
		//if user is not logged in redirect to login page
		if(!$this->session->userdata('adminLoggedin'))
		{
			redirect('admin/');
		}

		$data['participant'] = new ArrayObject();

		$query = $this->Admin_model->get_participants_by_org($org_id);

		foreach ($query->result() as $row) {
			//get participant data
			$participant_data = $this->Admin_model->get_participant_data($row->participant_id);

			//get # of commitments by participants so far
			$commits = $this->Admin_model->commits_by_user($participant_data->user_id);

			//put data into array
			$test = array(
				'user_id' => $participant_data->user_id,
				'email' => $participant_data->email,
				'commit' => $commits
				 );

			$data['participant']->append($test);
		}

		$this->load->view('admin/show_participant',$data);
	}

	//for testing to destroy session
	public function destroy_session()
	{
		$this->session->sess_destroy();
	}
}
