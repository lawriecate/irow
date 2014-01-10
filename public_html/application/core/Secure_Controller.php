<?php
class Secure_Controller extends MY_Controller
{
  function __construct()
  {
    parent::__construct();
    // whne controller is loaded check the user is logged in
    if( ! $this->l_auth->logged_in())
    {
      redirect(base_url() . 'login?m&r=' . urlencode(uri_string())); // redirect to login if they aren't 
    }
    if($this->l_auth->is_disabled() ) { // check if their account is suspendded
      show_error('Your account is suspended</br>Please contact support (support@irow.co.uk)',403); // if so quit with error page
    }
    $this->l_auth->check_setup(); // check they're account is setup
  }
}

?>