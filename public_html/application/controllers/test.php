<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Test extends CI_Controller {

    public function index()
    {
		$this->load->model('user_model');
		
        //Test 1
        $this->load->library('unit_test');
        $test = $this->user_model->login('test@test.com','test');
        $expected_result = TRUE;
        $test_name = 'Tests log in procedure with correct user info';
        $this->unit->run($test, $expected_result, $test_name);   

        //Test 2
        $this->load->library('unit_test');
        $test = $this->user_model->login('test@test.com','bad');
        $expected_result = FALSE;
        $test_name = 'Tests log in procedure with incorrect user info';
        $this->unit->run($test, $expected_result, $test_name); 
        //Test 2
        $a=array();
        $this->unit->run(sizeof($a), 0, 'Empty array');   
		
		 
        echo $this->unit->report();
    }
}?>