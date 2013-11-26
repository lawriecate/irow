<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Register extends CI_Controller {

	function __construct()
	{
	   parent::__construct();
	   $this->load->model('user_model');
	}

	public function index()
	{
		if($this->l_auth->logged_in() ) {
			redirect();
		}
		$this->load->helper(array('form'));
		// validate the data entered from registration form
		$this->load->library('form_validation');

		$this->form_validation->set_rules('email', 'Email', 'email|trim|required|xss_clean|is_unique[users.email]');
		$this->form_validation->set_rules('name', 'Name', 'trim|required|xss_clean');
		$this->form_validation->set_rules('dob', 'Date Of Birth', 'trim|required|xss_clean|callback__dobcheck');
	   	$this->form_validation->set_rules('password', 'Password', 'trim|required|xss_clean');
	   	$this->form_validation->set_rules('password2', 'Confirmation Password', 'trim|required|xss_clean|matches[password]');
	   	$this->form_validation->set_rules('tosconsent', 'Terms Of Service Agreement', 'callback__accept_terms');
	   	$data['registration_failure'] = FALSE;

	   	// 
		if($this->form_validation->run() == FALSE)
		{
			// show form + errors
		    $this->load->view('templates/header');
			$this->load->view('auth/register',$data);
			$this->load->view('templates/footer');
		}
		else
		{
	     	// validates
			$email = $this->input->post('email');
			$name = $this->input->post('name');
			$password = $this->input->post('password');
			$dob = $this->input->post('dob');

			$registration = $this->user_model->register($email,$name,$dob,$password);
			if($registration) {
				// registered succesfully
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
			$year = date("Y",$time);
			$month = date("m",$time);
			$day = date("d",$time);
			return checkdate($month,$day,$year);
		}
		return FALSE;
	}
}

/* End of file register.php */
/* Location: ./application/controllers/register.php */