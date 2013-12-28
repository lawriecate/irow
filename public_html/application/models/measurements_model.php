<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');

Class Measurements_model extends CI_Model
{
	function update_height($id,$height)
	{
		// function inserts records into the height measurements table
		$row = array(
					'uid' => $id,
					'height' => $height
				);
			$this->db->insert('measurements_heights',$row);
	}
	
	function update_armspan($id,$armspan)
	{
		// function inserts records into the height measurements table
		$row = array(
					'uid' => $id,
					'armspan' => $armspan
				);
			$this->db->insert('measurements_armspans',$row);
	}
	
	function update_weight($id,$weight)
	{
		// function inserts records into the height measurements table
		$row = array(
					'uid' => $id,
					'weight' => $weight
				);
			$this->db->insert('measurements_weights',$row);
	}

	function get_height($id)
	{
		$this->db->from('measurements_heights');
		$this->db->where('uid',$id);
		$this->db->order_by('timestamp','desc');
		$this->db->limit(1);
		$query = $this->db->get();
		if($query->num_rows() == 1) {
		$height=  $query->row_array();
		return $height['height'];
		} else {
			return '--';
		}
	}

	function get_armspan($id)
	{
		$this->db->from('measurements_armspans');
		$this->db->where('uid',$id);
		$this->db->order_by('timestamp','desc');
		$this->db->limit(1);
		$query = $this->db->get();
		$armspan=  $query->row_array();
		if($query->num_rows() == 1) {
		return $armspan['armspan'];
		} else {
			return '--';
		}
	}

	function get_weight($id)
	{
		$this->db->from('measurements_weights');
		$this->db->where('uid',$id);
		$this->db->order_by('timestamp','desc');
		$this->db->limit(1);
		$query = $this->db->get();
		$weight=  $query->row_array();
		if($query->num_rows() == 1) {
		return $weight['weight'];
		} else {
			return '--';
		}
	}
}