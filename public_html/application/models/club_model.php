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
		return $this->db->insert('clubs',$fields);
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
	
}