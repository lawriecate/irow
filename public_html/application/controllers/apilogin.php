<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class APILogin extends API_Controller {

	function __construct()
	{
	   parent::__construct();
	   $this->load->model('user_model');
	    $this->load->model('apitokens_model');
	}

	public function index()
	{
		$email = $this->input->post('email');
		$pass = $this->input->post('password');
		
		// check credentials
		$user = $this->user_model->login($email, $pass);

		if($user) {
			// log in and send back tokens
			$tokens = $this->apitokens_model->generate_token($this->appid,$user['id']);
			$response = array(
				'k1'=>$tokens['token_public'],
				'k2'=>$tokens['token_private']);
		} else {
			$response = 'false';
		}

		$this->output
		->set_content_type('application/json')
		->set_output(json_encode($response));
	}

}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */