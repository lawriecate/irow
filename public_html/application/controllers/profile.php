<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Profile extends Secure_Controller {

	function __construct()
	{
	   parent::__construct();
	   $this->load->model('user_model');
	}

	public function settings()
	{
		$me = $this->l_auth->current_user_id();
		$this->load->helper(array('form'));
		$this->load->library('form_validation');

		$data['title'] = "Account Settings";
		$data['profile'] =  $this->user_model->get_by_id($me);

		if($this->input->post('email') != $data['profile']['email']) {
			$this->form_validation->set_rules('email', 'Email', 'valid_email|trim|required|xss_clean|is_unique[users.email]'); // checks email is valid,entered,trims blank space,remove XSS script,and checks it's unique
		}
		$this->form_validation->set_rules('name', 'Name', 'trim|required|xss_clean|min_length[2]|max_length[30]|alpha');
		$this->form_validation->set_rules('dob', 'Date Of Birth', 'trim|required|xss_clean|callback__dobcheck');
	   	
	   	if($this->input->post('password') != "") {
	   		$this->form_validation->set_rules('password', 'Password', 'trim|required|xss_clean|min_length[5]|max_length[30]');
	   		$this->form_validation->set_rules('password2', 'Confirmation Password', 'trim|required|xss_clean|matches[password]');
	   	}
	   	//$this->form_validation->set_rules('tosconsent', 'Terms Of Service Agreement', 'callback__accept_terms');
		
		//print_r($data);
		if($this->form_validation->run() == FALSE) // if the form is entered and validation fails
		{
			// show form + errors
			
		    $this->load->view('templates/header',$data);
			$this->load->view('profile/settings',$data);
			$this->load->view('templates/footer');
		}
		else // if the form has been submitted
		{
			$email = $this->input->post('email');
			$name = $this->input->post('name');
			$password = FALSE;
			if($this->input->post('password') != "" ) {
				$password = $this->input->post('password');
			}
			$dob = $this->input->post('dob');
			$gender = $this->input->post('gender');
			
			$this->user_model->update($me,$email,$name,$password,$dob,$gender);
			$data['profile'] =  $this->user_model->get_by_id($me);
			//print_r($data);
			$this->load->view('templates/header',$data);
			$this->load->view('profile/settings',$data);
			$this->load->view('templates/footer');
		}
	}

	function _dobcheck() {
		$this->form_validation->set_message('_dobcheck', 'The date you entered is not valid');
		$input = $this->input->post('dob');
		$time = strtotime($input);

		
		
		if($time) {
			// check person is 10 to 100 years old
			$min = date("Y") - 100;
			$max = date("Y") - 10;
			$year = date("Y",$time);
			if($min > $year or $year > $max) {
				return FALSE;
			}

			// check date exists (i.e. leaps years, days in month etc)
			$month = date("m",$time);
			$day = date("d",$time);
			return checkdate($month,$day,$year);
		}

		return FALSE;
	}
	
	function _gendercheck() {
		$this->form_validation->set_message('_gendercheck', 'The gender you entered is not valid');
		$input = $this->input->post('gender');
		if($input == "m" or $input == "f") {
			return TRUE;
		} else {
			return FALSE;
		}
		
	}

}

/* End of file diary.php */
/* Location: ./application/controllers/diary.php */