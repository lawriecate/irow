<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');

Class Apikeys_model extends CI_Model
{
	
	function check_key($key) // updates height
	{
		$this->db->where('key',$key);
		$query = $this->db->get('api_keys');
		return $query->num_rows() > 0;
	}
	
	function get_app_by_key($key) // updates height
	{
		$this->db->where('key',$key);
		$query = $this->db->get('api_keys');
		if( $query->num_rows() > 0) {
			return $query->row_array();
		} else {
			return FALSE;
		}
	}

}