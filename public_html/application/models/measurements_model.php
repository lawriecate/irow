<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');

Class Measurements_model extends CI_Model
{
	private function update_measurement($id,$value,$table,$field) {
		// limits update to one a day
		$this->db->select('id,DATE(timestamp)');
		$this->db->from('measurements_'.$table);
		$this->db->where('DATE(timestamp)',date("Y-m-d")); 
		$this->db->limit(1);
		$query = $this->db->get();
		 
		if($query->num_rows() == 0) {
		// function inserts records into the  measurements table
			$row = array(
					'uid' => $id,
					$field => $value
				);
			$this->db->insert('measurements_'.$table,$row);
		} else {
			// function updates that days record
			$row = $query->row_array();
			echo $row['id'];
			$this->db->where('id',$row['id']);
			$update = array($field=>$value,'timestamp'=>date("Y-m-d H:i:s"));
			$this->db->update('measurements_'.$table,$update);
		}
	}
	function update_height($id,$height)
	{
		$this->update_measurement($id,$height,'heights','height');
	}
	
	function update_armspan($id,$armspan)
	{
		$this->update_measurement($id,$armspan,'armspans','armspan');
	}
	
	function update_weight($id,$weight)
	{
		$this->update_measurement($id,$weight,'weights','weight');
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