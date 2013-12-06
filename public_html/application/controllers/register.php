<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Register extends CI_Controller {

	function __construct()
	{
	   parent::__construct();
	   $this->load->model('user_model');
	   $this->load->model('club_model');
	}
	
	public function index()
	{
		if($this->l_auth->logged_in() ) { // redirects users if they're already logged in
			redirect();
		}
		$this->load->helper(array('form'));
		// validate the data entered from registration form
		$this->load->library('form_validation');

		// set validation rules for registration form
		$this->form_validation->set_rules('email', 'Email', 'valid_email|trim|required|xss_clean|is_unique[users.email]'); // checks email is valid,entered,trims blank space,remove XSS script,and checks it's unique
		$this->form_validation->set_rules('name', 'Name', 'trim|required|xss_clean|min_length[2]|max_length[30]|alpha');
		//$this->form_validation->set_rules('dob', 'Date Of Birth', 'trim|required|xss_clean|callback__dobcheck');
	   	$this->form_validation->set_rules('password', 'Password', 'trim|required|xss_clean|min_length[5]|max_length[30]');
	   	$this->form_validation->set_rules('password2', 'Confirmation Password', 'trim|required|xss_clean|matches[password]');
	   	$this->form_validation->set_rules('tosconsent', 'Terms Of Service Agreement', 'callback__accept_terms');
	   	$data['registration_failure'] = FALSE;

		if($this->form_validation->run() == FALSE) // if the form is entered and validation fails
		{
			// show form + errors
		    $this->load->view('templates/header');
			$this->load->view('auth/register',$data);
			$this->load->view('templates/footer');
		}
		else // if the form has been submitted
		{
	     	// read in inputs
			$email = $this->input->post('email');
			$name = $this->input->post('name');
			$password = $this->input->post('password');
			//$dob = $this->input->post('dob');

			// register the user using the function contained in the user model
			$registration = $this->user_model->register($email,$name,$password);
			if($registration) {
				// registered succesfully
				$this->user_model->login($email,$password);
				$this->load->view('templates/header');
				$this->load->view('auth/register_success',$data);
				$this->load->view('templates/footer');
			} else {
				$data['registration_failure'] = TRUE;
				$this->load->view('templates/header');
				$this->load->view('auth/register',$data);
				$this->load->view('templates/footer');
			}
			
		}
		
	}

	function _accept_terms() {
		if (isset($_POST['tosconsent'])) return true;
		$this->form_validation->set_message('_accept_terms', 'Please read and accept our terms of service');
		return FALSE;
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

	function _heightcheck() {
		return $this->_measurementcheck('height','Height',120,200); // refers height validation where input is height, label is Height, minimum is 120, maximum is 200
	}

	function _weightcheck() {
		return $this->_measurementcheck('weight','Weight',40,200,TRUE); // likewise refers weight validation (decimals allowed is TRUE)
	}

	function _armspancheck() {
		return $this->_measurementcheck('armspan','Arm Span',50,300); // likewise refers armspan validation
	}

	function _measurementcheck($post,$label,$min,$max,$decimal=FALSE) { // function which validates measurement fields
		$input = $this->input->post($post); // reads input
		$this->form_validation->set_message('_' . $post . 'check', 'The ' . $label . ' you enter should be a number inbetween ' . $min .  ' and ' . $max); // sets user friendly error message
		if($input != "" ) { // if something entered
			if($decimal == TRUE) { // if decimals allowed
				if(!is_numeric($input)) { // check its any number
					return FALSE;
				}
			} else {
				if(!ctype_digit($input)) { // otherwise check its an integer type number
					return FALSE;
				}
			}
			if(($input<$min) OR ( $input > $max) ) { // check number is within bounds suppplied
				return FALSE;
			}
		} else { // otherwise ignore the measurement fields
			return TRUE;
		}
	}

	function _clubcheck() { // checks the club selected is valid or represents individual
		$input = $this->input->post('club');
		$this->form_validation->set_message('_clubcheck', 'The club you entered is not valid');
		if($input == 0) {
			// individual
			return TRUE;
		} else {
			// checks if it exists in database
			return (bool)$this->club_model->club_exists($input);
		}
	}
	
	public function setup()
	{
		if(!$this->l_auth->logged_in() ) { // if the user isn't logged in redirect them
			redirect('/login');
		}
		$this->load->helper(array('form'));
		
		$this->load->library('form_validation');
		// set validation rules for setup form
		$this->form_validation->set_rules('dob', 'Date Of Birth', 'trim|required|xss_clean|callback__dobcheck');
		$this->form_validation->set_rules('gender', 'Gender', 'trim|required|xss_clean|callback__gendercheck');
		$this->form_validation->set_rules('height', 'Height', 'xss_clean|callback__heightcheck');
		$this->form_validation->set_rules('weight', 'Weight', 'xss_clean|callback__weightcheck');
		$this->form_validation->set_rules('armspan', 'Arm Span', 'xss_clean|callback__armspancheck');
		$this->form_validation->set_rules('club', 'Club', 'xss_clean|callback__clubcheck');

	   	// 
		if($this->form_validation->run() == FALSE) // if the form isn't valid / or submitted
		{
			// show form + any errors
		    $this->load->view('templates/header');
		    $data['system_error'] = FALSE;
			$this->load->view('auth/register_success',$data);
			$this->load->view('templates/footer');
		}
		else
		{
			// save user
			$dob = $this->input->post('dob');
			$gender = $this->input->post('gender');
			$height = $this->input->post('height');
			$armspan = $this->input->post('armspan');
			$weight = $this->input->post('weight');
			$club = $this->input->post('club');
			$setup = $this->user_model->setup($dob,$gender,$height,$armspan,$weight,$club);
			if($setup) {
				redirect('/diary');
			} else {
				$this->load->view('templates/header');
				$data['system_error'] = TRUE;
				$this->load->view('auth/register_success',$data);
				$this->load->view('templates/footer');
			}
			
		}
	}
}

/* End of file register.php */
/* Location: ./application/controllers/register.php */