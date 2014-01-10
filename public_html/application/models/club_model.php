<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');

Class Club_model extends CI_Model
{
	function club_exists($id)
	{
		// function to lookup if a club exists
		$query = $this->db->get_where('clubs',array('id'=>$id));
		return $query->row_array();
	}

	function get_by_id($id) {
		// function to retrieve a club record for a certain id
		$query = $this->db->get_where('clubs',array('id'=>$id));
		return $query->row_array();
	}

	function get_by_ref($ref) {
		// function to retrieve a club record by their url code
		$query = $this->db->get_where('clubs',array('ref'=>$ref));
		return $query->row_array();
	}

	function get_all() {
		// function to get records of all clubs
		$query= $this->db->get('clubs');
		return $query->result_array();
	}

	function search_clubs($page,$noItems,$search) {
		// function to search for clubs
		
		$this->db->select('id,name,email,phone');

		$this->db->from('clubs');

		if($search != "" ) { // if a query entered match the query againt the name and email
			$this->db->like("name",$search);
			$this->db->or_like("email",$search);
		}

		$start = 10 * ($page); // sets the limit offset based on 10 records per page
		$this->db->limit($noItems,$start);
		$query = $this->db->get();

		return $query->result_array();
	}

	function total_clubs() {
		// function to get the total number of clubs
		return $this->db->count_all('clubs');
	}

	function update($id,$fields) {
		// function to update a club record
		return	$this->db->update('clubs',$fields,array('id'=>$id));

	}

	function add($fields) {
		// function to add a club
		if(!isset($fields['ref'])) {
			// if a url reference not specified, generate one
			$this->load->helper('url');
			$fields['ref'] = url_title($fields['name']);
		}
		$this->db->insert('clubs',$fields);
		return $this->db->insert_id();
	}
	
	function list_countries() {
		// function to lookup all the countries a club can be set to
		$this->db->from('countries');
		$this->db->order_by('code','asc');
		$query = $this->db->get();
		return $query->result_array();
	}

	function public_search($q) {
		// function to search for clubs which can be pubclicly listed
		$this->db->select('id,ref,email,name');
		$this->db->from('clubs');
		$this->db->like('name',$q); // match against name and email
		$this->db->or_like('email',$q);
		$this->db->limit(16);
		$query = $this->db->get();

		$results = $query->result_array();
		foreach($results as $key => $item) {
			// add a url whicl links to a public profile page
			$results[$key]['url'] = base_url() . 'club/profile/' . $item['ref'];
		}
		return $results;
	}

	function is_level($club_id,$user_id,$level) {
		// function tests if a user has a membership of a club at a specified permission level
		$this->db->from('users_clubs');
		$this->db->where('club_id',$club_id);
		$this->db->where('user_id',$user_id);
		$this->db->where('level',$level);
		$query = $this->db->get();
		return $query->num_rows() == 1;
	}

	function get_members($id) {
		// function to get all members of a club
		$this->db->select('id,name');
		$this->db->from('users_clubs');
		$this->db->where('club_id',$id);
		$this->db->join('users','users.id = users_clubs.user_id');
		$query=  $this->db->get();
		return $query->result_array();
	}

	function get_coaches($id) {
		// function to get all coaches of a club
		$this->db->from('users_clubs');
		$this->db->where('club_id',$id);
		$this->db->where('level', 'coach');
		$this->db->join('users','users.id = users_clubs.user_id');
		$query=  $this->db->get();
		return $query->result_array();
	}

	function is_coach($club_id,$user_id) {
		// function which looksup if an individual user is a coach
		return $this->is_level($club_id,$user_id,'coach');
	}

	function get_managers($id) {
		// function to get all managers of a club
		$this->db->from('users_clubs');
		$this->db->where('club_id',$id);
		$this->db->where('level', 'manager');
		$this->db->join('users','users.id = users_clubs.user_id');
		$query=  $this->db->get();
		return $query->result_array();
	}

	function reset_all_memberships($id) {
		// function which sets all user-club relationships to athlete level
		// this is useful for forms which let the mangers/coaches of a club be specified
		$update = array('level'=>'athlete');
		$this->db->where('club_id',$id);
		$this->db->update('users_clubs',$update);
	}

	function is_manager($club_id,$user_id) {
		// function which looksup if an individual user is a manager
		return $this->is_level($club_id,$user_id,'manager');
	}

	function get_authorized_users($id) {
		// function which gets all coaches and managers of a club
		$this->db->from('users_clubs');
		$this->db->where('club_id',$id);
		$this->db->where('(level = "coach" OR level = "manager")');
		$this->db->join('users','users.id = users_clubs.user_id');
		$query=  $this->db->get();
		return $query->result_array();
	}
	
}