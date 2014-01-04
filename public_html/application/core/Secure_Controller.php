<?php
class Secure_Controller extends MY_Controller
{
  function __construct()
  {
    parent::__construct();
    if( ! $this->l_auth->logged_in())
    {
      redirect(base_url() . 'login?m&r=' . urlencode(uri_string()));
    }
    if($this->l_auth->is_disabled() ) {
      show_error('Your account is suspended</br>Please contact support (support@irow.co.uk)',403);
    }
    $this->l_auth->check_setup();
  }
}

?>