<?php
class Admin_Controller extends MY_Controller
{
  function __construct()
  {
    parent::__construct();
    if( ! $this->l_auth->is_admin_logged_in())
    {
      //redirect(base_url() . 'login?m&r=' . urlencode(uri_string()));
      show_error('Unauthorized',403);
    }
    if($this->l_auth->is_disabled() ) {
      show_error('Your account is suspended</br>Please contact support (support@irow.co.uk)',403);
    }
  }
}

?>