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
	
	function setup($id,$dob,$gender,$height,$armspan,$weight,$clubs) {
		
	}

	function get_by_id($id) {
		$query = $this->db->get_where('users',array('id'=>$id));
		return $query->row_array();
	}
}