<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Logout extends Secure_Controller {

	public function index()
	{
		// logout user and redirect
		if($this->l_auth->logout()) {
			redirect();
		}
	}
}

/* End of file logut.php */
/* Location: ./application/controllers/logout.php */