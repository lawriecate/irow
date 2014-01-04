<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');

Class Club_model extends CI_Model
{
	function club_exists($id)
	{
		$query = $this->db->get_where('clubs',array('id'=>$id));
		return $query->row_array();
	}

	function get_by_id($id) {
		$query = $this->db->get_where('clubs',array('id'=>$id));
		return $query->row_array();
	}

	function get_by_ref($ref) {
		$query = $this->db->get_where('clubs',array('ref'=>$ref));
		return $query->row_array();
	}

	function get_all() {
		$query= $this->db->get('clubs');
		return $query->result_array();
	}

	function search_clubs($page,$noItems,$search) {
		$start = 10 * ($page);
		//$end = 10 * $page;
		$this->db->select('id,name,email,phone');

		$this->db->from('clubs');

		if($search != "" ) {
			$this->db->like("name",$search);
			$this->db->or_like("email",$search);
		}

		$this->db->limit($noItems,$start);
		$query = $this->db->get();

		return $query->result_array();
	}

	function total_clubs() {
		return $this->db->count_all('clubs');
	}

	function update($id,$fields) {

		return	$this->db->update('clubs',$fields,array('id'=>$id));

	}

	function add($fields) {
		if(!isset($fields['ref'])) {
			$this->load->helper('url');
			$fields['ref'] = url_title($fields['name']);
		}
		$this->db->insert('clubs',$fields);
		return $this->db->insert_id();
	}
	
	function list_countries() {
		$this->db->from('countries');
		$this->db->order_by('code','asc');
		$query = $this->db->get();
		return $query->result_array();
	}

	function public_search($q) {
		$this->db->select('id,ref,email,name');
		$this->db->from('clubs');
		$this->db->like('name',$q);
		$this->db->or_like('email',$q);
		$this->db->limit(16);
		$query = $this->db->get();

		$results = $query->result_array();
		foreach($results as $key => $item) {
			$results[$key]['url'] = base_url() . 'club/profile/' . $item['ref'];
		}
		return $results;
	}

	function is_level($club_id,$user_id,$level) {
		$this->db->from('users_clubs');
		$this->db->where('club_id',$club_id);
		$this->db->where('user_id',$user_id);
		$this->db->where('level',$level);
		$query = $this->db->get();
		return $query->num_rows() == 1;
	}

	function get_coaches($id) {
		$this->db->from('users_clubs');
		$this->db->where('club_id',$id);
		$this->db->where('level', 'coach');
		$this->db->join('users','users.id = users_clubs.user_id');
		$query=  $this->db->get();
		return $query->result_array();
	}

	function is_coach($club_id,$user_id) {
		return $this->is_level($club_id,$user_id,'coach');
	}



	function get_managers($id) {
		$this->db->from('users_clubs');
		$this->db->where('club_id',$id);
		$this->db->where('level', 'manager');
		$this->db->join('users','users.id = users_clubs.user_id');
		$query=  $this->db->get();
		return $query->result_array();
	}


	function is_manager($club_id,$user_id) {
		return $this->is_level($club_id,$user_id,'manager');
	}

	function get_authorized_users($id) {
		$this->db->from('users_clubs');
		$this->db->where('club_id',$id);
		$this->db->where('(level = "coach" OR level = "manager")');
		$this->db->join('users','users.id = users_clubs.user_id');
		$query=  $this->db->get();
		return $query->result_array();
	}
	
}