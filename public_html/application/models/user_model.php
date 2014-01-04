<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');

Class User_model extends CI_Model
{
	function __construct() {
		$this->load->library('encrypt');
	}

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
 			$db_password_hash = $this->encrypt->decode($user['password']);
 			$input_enc = hash('sha512',$password.$user['salt']);
 			//echo $db_password_hash; echo "<br>";
 			//echo $input_enc;
 				if($input_enc == $db_password_hash) {
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

	private function generate_password($id,$password) {
		 // function to update user password using encryption
		$random_salt = hash('sha512', uniqid(mt_rand(1, mt_getrandmax()), true)); // generate unique user id
		$hpassword = $this->encrypt->encode(hash('sha512',$password.$random_salt));
		$update = array(
			'password'=>$hpassword,
			'salt'=>$random_salt);
		$this->db->where('id',$id);
		return $this->db->update('users',$update);
		
	}

	function register($email,$name,$password) {
		/*$random_salt = hash('sha512', uniqid(mt_rand(1, mt_getrandmax()), true));
		// Create salted password
		$hpassword = hash('sha512', $password.$random_salt);*/		$newuser = array(
			'email'=>$email,
			'name'=>$name,
			'setup'=>2
			);
			
		if($this->db->insert('users',$newuser)) {
			$new_id = $this->db->insert_id();
			return $this->generate_password($new_id,$password);
		} else {
			return FALSE;
		}
	}

	function create_invited_user($coach,$name) {
		$newuser = array(
			'name'=>$name,
			'invitee'=>$coach,
			'setup'=>3
			);
		$this->db->insert('users',$newuser);
		return $this->db->insert_id();
	}
	
	function setup($dob,$gender,$height,$armspan,$weight,$club) {
		$return = TRUE;
		// edit user profile fields (dob, gender, club)
		$update = array(
			
			'dob' => date( 'Y-m-d', strtotime($dob)),
			'gender' => $gender,
			
			'setup' => 1
			);
		$id = $this->l_auth->current_user_id();
		$this->db->where('id',$id);
		// send update query to database
		$return = $this->db->update('users',$update);

		if($this->club_model->club_exists($club)) {
			// register 1st club relationship
			$this->join_club($id,$club);
		} else {
			$newClub = $this->club_model->add(array('name'=>$club));
			$this->join_club($id,$newClub);
			$this->set_membership($id,$newClub,'manager');
		}
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

	function update($id,$email,$name,$password,$dob,$gender) {
		$update = array(
			
			'dob' => date( 'Y-m-d', strtotime($dob)),
			'gender' => $gender,
			'email' => $email,
			'name' =>$name
			);
		$this->db->where('id',$id);
		$this->db->update('users',$update);

		if($password != FALSE) {
			$this->generate_password($id,$password);
		}

	}

	function set_suspension($id,$suspend) {
		$this->db->where('id',$id);
		if($suspend == TRUE) {
			$this->db->update('users', array('disabled'=>'1'));
		} else {
			$this->db->update('users', array('disabled'=>NULL));
		}
		
	}

	function set_admin($id,$is_admin) {
		if($is_admin == TRUE) {
			$this->db->update('users', array('admin'=>'1'),array('id'=>$id));
		} else {
			$this->db->update('users', array('admin'=>NULL),array('id'=>$id));
		}
		
	}

	function get_by_id($id) {
		$query = $this->db->get_where('users',array('id'=>$id));
		return $query->row_array();
	}

	function search_users($page,$noItems,$search) {
		$start = 10 * ($page);
		//$end = 10 * $page;
		$this->db->select('id,name,email,dob,admin');

		$this->db->from('users');

		if($search != "" ) {
			$this->db->where("name",$search);
			$this->db->or_where("email",$search);
		}

		$this->db->limit($noItems,$start);
		$query = $this->db->get();

		return $query->result_array();
	}

	function total_users() {
		return $this->db->count_all('users');
	}

	function public_search($q) {
		$this->db->select('id,email,name');
		$this->db->from('users');
		$this->db->like('name',$q);
		$this->db->or_like('email',$q);
		$this->db->limit(16);
		$query = $this->db->get();
		$results = $query->result_array();
		foreach($results as $key => $item) {
			$results[$key]['url'] = base_url() . 'people/' . $item['id'];
		}
		return $results;
	}

	function is_member_of($id,$club) {
		return ($this->membership($id,$club)!=FALSE);
	}

	function membership($id,$club) {
		// return false, or level
		$this->db->from('users_clubs');
		$this->db->where('user_id',$id);
		$this->db->where('club_id',$club);
		$this->db->limit(1);
		$query = $this->db->get();
		if($query->num_rows() > 0) {
			// is a member
			$row = $query->row_array();
			return $row['level'];
		} else {
			// isn't a member
			return FALSE;
		}
	}

	function memberships($id) {
		$this->db->from('users_clubs');
		$this->db->join('clubs','clubs.id = users_clubs.club_id');
		$this->db->where('user_id',$id);	
		$query= $this->db->get();
		return $query->result_array();
	}

	function is_coach($id) {
		$this->db->from('users_clubs');
		$this->db->where('user_id',$id);	
		$this->db->where('(level = "coach" OR level = "manager")');
		$query = $this->db->get();
		return $query->num_rows() > 0;
	}

	function join_club($id,$club){
		if($this->is_member_of($id,$club) == FALSE) {
			$row = array(
				'user_id'=>$id,
				'club_id'=>$club);
			return $this->db->insert('users_clubs',$row);
		}
	}

	function set_membership($id,$club,$level) {
		if($this->is_member_of($id,$club) == FALSE) {
			$row = array(
				'user_id'=>$id,
				'club_id'=>$club,
				'level'=>$level);
			return $this->db->insert('users_clubs',$row);
		} else {
			$this->db->where("user_id",$id);
			$this->db->where("club_id",$club);
			$update = array(
				'level'=>$level);
			return $this->db->update("users_clubs",$update);
		}
	}

	function leave_club($id,$club) {
		if($this->is_member_of($id,$club) == TRUE) {
			$row = array(
				'user_id'=>$id,
				'club_id'=>$club);
			$this->db->delete('users_clubs',$row);
		}
	}
}