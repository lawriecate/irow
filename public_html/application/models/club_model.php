<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');

Class Club_model extends CI_Model
{
	function club_exists($id)
	{
		$query = $this->db->get_where('clubs',array('id'=>$id));
		return $query->row_array();
	}
	
	
}