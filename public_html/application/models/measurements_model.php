<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');

Class Measurements_model extends CI_Model
{
	private function update_measurement($id,$value,$table,$field) {  // function that updates a measurement but restricts the user to once a day
		// this query tests if there is already a record with todays date
		$this->db->select('id,DATE(timestamp)');
		$this->db->from('measurements_'.$table);
		$this->db->where('DATE(timestamp)',date("Y-m-d")); 
		$this->db->limit(1);
		$query = $this->db->get();
		 
		if($query->num_rows() == 0) { // if there isn't a record it will insert
			// row array contains id of user, and the value for $field
			$row = array(
					'uid' => $id,
					$field => $value
				);
			$this->db->insert('measurements_'.$table,$row); // inserts record
		} else {
			// row array contains id of user, and the value for $field
			$row = $query->row_array();
			echo $row['id'];
			$this->db->where('id',$row['id']);
			$update = array($field=>$value,'timestamp'=>date("Y-m-d H:i:s"));
			$this->db->update('measurements_'.$table,$update); // updates record
		}
	}
	function update_height($id,$height) // updates height
	{
		$this->update_measurement($id,$height,'heights','height');
	}
	
	function update_armspan($id,$armspan) // updates arm span
	{
		$this->update_measurement($id,$armspan,'armspans','armspan');
	}
	
	function update_weight($id,$weight) // updates weight
	{
		$this->update_measurement($id,$weight,'weights','weight');
	}

	function get_height($id) // gets the latest height
	{
		$this->db->from('measurements_heights');
		$this->db->where('uid',$id);  // limits to user parameter
		$this->db->order_by('timestamp','desc'); // sorts by timestamp
		$this->db->limit(1);
		$query = $this->db->get();
		if($query->num_rows() == 1) {
		$height=  $query->row_array();
		return $height['height'];  // return the value
		} else { // if theres no record return a placeholder value
			return '--';
		}
	}

	function get_armspan($id) // gets the latest armspan
	{
		$this->db->from('measurements_armspans');
		$this->db->where('uid',$id);  // limits to user parameter
		$this->db->order_by('timestamp','desc'); // sorts by timestamp
		$this->db->limit(1);
		$query = $this->db->get();
		$armspan=  $query->row_array();
		if($query->num_rows() == 1) {
		return $armspan['armspan']; // return the value
		} else { // if theres no record return a placeholder value
			return '--';
		}
	}

	function get_weight($id) // gets the latest weight
	{
		$this->db->from('measurements_weights');
		$this->db->where('uid',$id);  // limits to user parameter
		$this->db->order_by('timestamp','desc'); // sorts by timestamp
		$this->db->limit(1);
		$query = $this->db->get();
		$weight=  $query->row_array();
		if($query->num_rows() == 1) {
		return $weight['weight']; // return the value
		} else { // if theres no record return a placeholder value
			return '--';
		}
	}
}