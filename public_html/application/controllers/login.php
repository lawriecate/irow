<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Login extends CI_Controller {

	function __construct()
	{
	   parent::__construct();
	   $this->load->model('user_model');
	}

	public function index()
	{
		$data['title'] = "Log In";
		$this->load->helper(array('form'));
		$this->load->library('form_validation');

		$this->form_validation->set_rules('email', 'Email', 'email|trim|required|xss_clean');
	   	$this->form_validation->set_rules('password', 'Password', 'trim|required|xss_clean|callback_check_database');

		if($this->form_validation->run() == FALSE)
		{
			$data['display_redirected_message'] = isset($_GET['m']);
			if($_GET['r']) {
				$querystring = '?r=' . $this->input->get('r');
			} else {
				$querystring = NULL;
			}
			$data['action'] = 'login' . $querystring;
		    $this->load->view('templates/header',$data);
			$this->load->view('auth/login',$data);
			$this->load->view('templates/footer');
		}
		else
		{
			if(isset($_GET['r'])) {
				redirect($this->input->get('r'));
			} else {
	     		redirect();
	     	}
		}

		
	}

	function check_database($password)
	{
	//Field validation succeeded.  Validate against database
	$email = $this->input->post('email');

	//query the database
	$result = $this->user_model->login($email, $password);

		if($result)
		{
		
		 //}
		 return TRUE;
		}
		else
		{
	 	$this->form_validation->set_message('check_database', 'Wrong email/password combination');
		 return false;
		}

	}
}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */