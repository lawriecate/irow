<?php
class API_Controller extends MY_Controller
{
  function __construct()
  {
    parent::__construct();
    $this->appid = FALSE;
    $this->load->model('apikeys_model');
    // when controller loaded check valid API key present
    $key = $this->input->get('api_key');
    if($this->apikeys_model->check_key($key)){
      // vaild key
      $app =$this->apikeys_model->get_app_by_key($key);
      $this->appid= $app['id'];
    } else {
      show_error("No API Key");
    }
  }
}

?>