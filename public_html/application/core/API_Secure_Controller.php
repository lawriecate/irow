<?php
class API_Secure_Controller extends API_Controller
{
  function __construct()
  {
    parent::__construct();
    // upon load check valid tokens present then sets user IDs
    $this->load->model('apitokens_model');
    $this->load->model('user_model');
    $public_key = $this->input->get('key');
    $private_key_hash = $this->input->get('hash');
    $timestamp = $this->input->get('timestamp');

    $check =$this->apitokens_model->check_token($public_key,$private_key_hash,$this->appid,$timestamp);
    if($check) {
      // valid token set user id
      $user =$this->user_model->get_by_id($check);
      $sess_array = array(); // build an array to set in session
             $sess_array = array(
               'id' => $user['id'],
               'email' => $user['email']
             );
             $this->session->set_userdata('logged_in', $sess_array); // sets the user cookies
      $this->api_token_user_id = $user['id'];
    } else {
      show_error("Unauthorized");
    }
    
  }
}

?>