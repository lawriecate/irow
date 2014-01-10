<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');

Class User_model extends CI_Model
{
	function __construct() {
		$this->load->library('encrypt');
	}

	function login($email,$password)
	{
		// Login function takes an email and password and will check they're correct then set cookies on the browser

		// Builds query that gets the password for the entered email
		$this->db->select('id,email,salt,password');
		$this->db->from('users');
		$this->db->where('email',$email);
		$this->db->limit(1);
		$query = $this->db->get();
		if($query->num_rows()==1)	// if the email is found
		{ 
			$user = $query->row_array();
 			$db_password_hash = $this->encrypt->decode($user['password']);
 			$input_enc = hash('sha512',$password.$user['salt']); // get the encrypted value of the password

 				if($input_enc == $db_password_hash) {
 					// password matches this account's

 					 $sess_array = array(); // build an array to set in session
					   $sess_array = array(
					     'id' => $user['id'],
					     'email' => $user['email']
					   );
					   $this->session->set_userdata('logged_in', $sess_array); // sets the user cookies

 					return $user;
 				} else {
 					// the two encryptions DO NOT MATCH
 					return FALSE;
 				}
		} 
		else 
		{
			// the email entered doesn't match any user records in the database
			return FALSE;
		}
	}

	private function generate_password($id,$password) {
		 // function to update user password using encryption
		$random_salt = hash('sha512', uniqid(mt_rand(1, mt_getrandmax()), true));
		$hpassword = $this->encrypt->encode(hash('sha512',$password.$random_salt)); // generates encrypted value of new password
		$update = array(
			'password'=>$hpassword,
			'salt'=>$random_salt); // array of fields to update in db
		$this->db->where('id',$id); // limits to one user 
		return $this->db->update('users',$update); // run update query
		
	}

	function register($email,$name,$password) {
		// function to create a basic new user account using an email, full name, and password
		$newuser = array(
			'email'=>$email,
			'name'=>$name,
			'setup'=>2 // setup value 2 means the user will get shown a profile setup form on login
			); // setup the array with the information sent
			
		if($this->db->insert('users',$newuser)) { // if the array is inserted
			$new_id = $this->db->insert_id(); // get the new id
			return $this->generate_password($new_id,$password); // return whether the password can be set
		} else {
			return FALSE;
		}
	}

	function create_invited_user($coach,$name) {
		// function to create a 'placeholder' user whose can be invited via email later
		// this is useful for coaches to record data for people who haven't yet signed up
		$newuser = array(
			'name'=>$name,
			'invitee'=>$coach, // this value represents that the user belongs to the coach
			'setup'=>3 // this value will show the user a special setup form on login
			);
		$this->db->insert('users',$newuser); // insert into DB
		return $this->db->insert_id(); // return the new user ID
	}
	
	function setup($id,$dob,$gender,$height,$armspan,$weight,$club) {
		// function to update a user record with a basic profile
		$return = TRUE; // initialize return value

		$update = array( // setup an array of fields to update the record
			'dob' => date( 'Y-m-d', strtotime($dob)), 
			'gender' => $gender,
			'setup' => 1 // this setup value represents a completed value
			);
		
		$this->db->where('id',$id); // limit to the user selected
		// send update query to database
		$return = $this->db->update('users',$update); // set the return value to whether the update was performed

		// Now add the users first membership
		if($this->club_model->club_exists($club)) { 
			// register 1st club relationship
			$this->join_club($id,$club);
		} else {
			$newClub = $this->club_model->add(array('name'=>$club));// if they've entered a new club add it to database via club model
			$this->join_club($id,$newClub); // now join it
			$this->set_membership($id,$newClub,'manager');// upgrade them to manager since their the first member
		}
		// get special interface to access measurements model
		
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
		// update the users basic record 
		$update = array(
			
			'dob' => date( 'Y-m-d', strtotime($dob)),
			'gender' => $gender,
			'email' => $email,
			'name' =>$name
			);
		$this->db->where('id',$id);
		$this->db->update('users',$update);

		if($password != FALSE) { // if a new password has been sent set a new password
			$this->generate_password($id,$password);
		}

	}

	function set_suspension($id,$suspend) { 
		// function to update the suspension status of a user
		$this->db->where('id',$id);
		if($suspend == TRUE) {
			// update the record setting disabled to 1 (ie disable the user - they can't login)
			$this->db->update('users', array('disabled'=>'1'));
		} else {
			// update the record setting disabled to NULL (ie they're allowed to login)
			$this->db->update('users', array('disabled'=>NULL));
		}
		
	}

	function set_admin($id,$is_admin) {
		// function to update the admin status of a user
		if($is_admin == TRUE) {
			// update the record setting admin to 1 (ie admin)
			$this->db->update('users', array('admin'=>'1'),array('id'=>$id));
		} else {
			// update the record setting admin to NULL (ie not an admin)
			$this->db->update('users', array('admin'=>NULL),array('id'=>$id));
		}
		
	}

	function get_by_id($id) {
		// function to get the user record for a specific ID
		$query = $this->db->get_where('users',array('id'=>$id));
		return $query->row_array();
	}

	function search_users($page,$noItems,$search) {
		// function to search users and return basic value
		
		$this->db->select('id,name,email,dob,admin'); // selects values to send back

		$this->db->from('users');

		if($search != "" ) {
			// if a search query entered, match it against the name and email fields
			$this->db->where("name",$search);
			$this->db->or_where("email",$search);
		}

		// limit the search to a page
		$start = 10 * ($page); // set the limit offset to 10 * page, so 10 items per page
		$this->db->limit($noItems,$start);
		$query = $this->db->get();

		return $query->result_array(); // return found records
	}

	function total_users() {
		// function to get the total count of all users in database
		return $this->db->count_all('users');
	}

	function public_search($q) {
		// function to search users and return results which can be displayed to all users
		$this->db->select('id,email,name');
		$this->db->from('users');
		$this->db->where('setup','1'); // limit the search to users who are completed setup
		$this->db->like('name',$q);

		$this->db->limit(16);
		$query = $this->db->get();
		
		$results = $query->result_array();
		foreach($results as $key => $item) { // adds a url to the result which links to their public profile
			$results[$key]['url'] = base_url() . 'people/view/' . $item['id'];
		}
		return $results;
	}

	function is_member_of($id,$club) {
		// function to get a basic value representing if a user is a member of a club
		return ($this->membership($id,$club)!=FALSE);
	}

	function membership($id,$club) {
		// function to lookup the relationship between a user and a club
		$this->db->from('users_clubs');
		$this->db->where('user_id',$id);
		$this->db->where('club_id',$club);
		$this->db->limit(1);
		$query = $this->db->get();
		if($query->num_rows() > 0) {
			// is a member
			$row = $query->row_array();
			return $row['level']; // if the user is found return their current level
		} else {
			// otherwise they aren't a member
			return FALSE;
		}
	}

	function memberships($id) {
		// function to get all members of a club
		$this->db->from('users_clubs');
		$this->db->join('clubs','clubs.id = users_clubs.club_id');
		$this->db->where('user_id',$id);	
		$query= $this->db->get();
		return $query->result_array();
	}

	function is_coach($id) {
		// function to test if a user is a member of a club and have coach level permission
		$this->db->from('users_clubs');
		$this->db->where('user_id',$id);	
		$this->db->where('(level = "coach" OR level = "manager")');
		$query = $this->db->get();
		return $query->num_rows() > 0;
	}

	function join_club($id,$club){
		// function to setup a user membership of a club
		if($this->is_member_of($id,$club) == FALSE) { // if they're not already a member
			$row = array(
				'user_id'=>$id,
				'club_id'=>$club);
			return $this->db->insert('users_clubs',$row);
		}
	}

	function set_membership($id,$club,$level) {
		// function to update the users membership with a new level
		if($this->is_member_of($id,$club) == FALSE) { // if they're already a member insert a record
			$row = array(
				'user_id'=>$id,
				'club_id'=>$club,
				'level'=>$level);
			return $this->db->insert('users_clubs',$row);
		} else { // if they're not a member update the exsting relationship record
			$this->db->where("user_id",$id);
			$this->db->where("club_id",$club);
			$update = array(
				'level'=>$level);
			return $this->db->update("users_clubs",$update);
		}
	}

	function leave_club($id,$club) {
		// function to remove a users membership of a club
		if($this->is_member_of($id,$club) == TRUE) { // if a record of relationship exists
			$row = array(
				'user_id'=>$id,
				'club_id'=>$club);
			$this->db->delete('users_clubs',$row);
		}
	}
}