<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');

Class User_model extends CI_Model
{
	function login($email,$password)
	{
		//$password = hash('sha512',$password);
		$this->db->select('id,email,salt,password');
		$this->db->from('users');
		$this->db->where('email',$email);
		$this->db->limit(1);
		$query = $this->db->get();
		if($query->num_rows()==1)
		{
			$user = $query->row_array();
			//$db_password_hash = hash('sha512', $user['password'].$user['salt']); // hash the password with the unique salt.
 			$input_password_hash = hash('sha512',$password.$user['salt']);
 			
 				if($user['password'] == $input_password_hash) {
 					// password matches this account's

 					 $sess_array = array();
					 //foreach($result as $row)
					 //{
					   $sess_array = array(
					     'id' => $user['id'],
					     'email' => $user['email']
					   );
					   $this->session->set_userdata('logged_in', $sess_array);

 					return $user;
 				} else {
 					// the two encryptions DO NOT MATCH
 					return FALSE;
 				}
		} 
		else 
		{
			return FALSE;
		}
	}

	function register($email,$name,$password) {
		$random_salt = hash('sha512', uniqid(mt_rand(1, mt_getrandmax()), true));
		// Create salted password
		$hpassword = hash('sha512', $password.$random_salt);
		$newuser = array(
			'email'=>$email,
			'name'=>$name,
			'password'=>$hpassword,
			'salt'=>$random_salt,
			'setup'=>0
			);
		if($this->db->insert('users',$newuser)) {
			return TRUE;
		} else {
			return FALSE;
		}
	}
	
	function setup($dob,$gender,$height,$armspan,$weight,$club) {
		$return = TRUE;
		// edit user profile fields (dob, gender, club)
		$update = array(
			'id' => $this->l_auth->current_user_id(),
			'dob' => date( 'Y-m-d', strtotime($dob)),
			'gender' => $gender,
			'club' => $club,
			'setup' => 1
			);

		// send update query to database
		$return = $this->db->update('users',$update);

		// get special interface to access measurements model
		$CI =& get_instance();
        $CI->load->model('measurements_model');
        // if height is entered update it
        if($height != "" ) {
        	$return = $return AND $this->measurements_model->update_height($this->l_auth->current_user_id(),$height);
        }
        // if armspan is entered update it
        if($armspan != "" ) {
        	$return = $return AND $this->measurements_model->update_armspan($this->l_auth->current_user_id(),$armspan);
        }
        // if weight is entered update it
        if($weight != "" ) {
        	$return = $return AND $this->measurements_model->update_weight($this->l_auth->current_user_id(),$weight);
        }

        return $return;
	}

	function get_by_id($id) {
		$query = $this->db->get_where('users',array('id'=>$id));
		return $query->row_array();
	}
}