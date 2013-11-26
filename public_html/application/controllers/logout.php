<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Logout extends Secure_Controller {

	public function index()
	{
		if($this->l_auth->logout()) {
			redirect();
		}
	}
}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */