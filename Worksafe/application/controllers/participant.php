<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Participant extends CI_Controller {


	function __construct()
	{
		parent::__construct();
		$this->load->helper(array('form', 'url', 'string', 'cookie'));  //load a form and the base_url
        $this->load->library(array('form_validation', 'security', 'session')); //set form_validation rules and xss_cleaning
        $this->load->model('Participant_model');

	}

	function index()
	{
		$data['error'] = $this->session->flashdata('error');
		$this->load->view('participant/participant_login', $data);
	}

	function login()
	{

		//put validation on so email field is required
		$this->form_validation->set_rules('email', 'Email', 'required');

		//if either is empty returns error
		if($this->form_validation->run() === FALSE)
		{
			$this->session->set_flashdata('error', 'Must not leave the email field blank');
			redirect('participant/index');
		}
		else
		{
			//check to make sure email is valid
			$email = $this->security->xss_clean($this->input->post('email'));

			//query db to make sure email is already in the database
			$query = $this->Participant_model->check_participant_email($email);

			//check if number of rows returned is greater than 0
			if($query->num_rows() > 0)
			{
				$row = $query->row();

				$cookie_participant_id = array(
				'name' => 'participant_id',
				'value' => $row->user_id,
				'expire' => 86500,
				);

			$this->input->set_cookie($cookie_participant_id);

			redirect('participant/questionPage');
			}
			else
			{
				$this->session->set_flashdata('error', 'Email is not yet registered');
				redirect('participant/index');
			}
		}
	}

	function signup()
	{
		$query['error'] = $this->session->flashdata('error');
		//get all the organiztions in the database to fill the dropdown
		$query['organization'] = $this->Participant_model->get_all_organizations();

		$this->load->view('participant/participant_signup',$query);
	}

	function enroll()
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
				redirect('participant/questionPage');
									
			}
		}

	}

	function questionPage()
	{
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

	function answerQuestions()
	{

		$num = $this->Participant_model->get_days_in_competition();


		for($i=0;$i<$num;$i++)
		{
			$answer = $this->security->xss_clean($this->input->post('correct_ans_q'.$i));

			$correct = $this->Participant_model->check_answer($answer);
			
			if($correct->correct == 'y')
			{
				echo $correct->answer.' is correct';
			}
			else
			{
				echo $correct->answer.' is incorrect';
			}
			echo '<br />';
			
		}

	}

}
?>	