<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');

Class Activity_model extends CI_Model
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
					'armspan' => $height
				);
			$this->db->insert('measurements_armspans',$row);
	}
	
	function update_weight($id,$weight)
	{
		// function inserts records into the height measurements table
		$row = array(
					'uid' => $id,
					'weight' => $height
				);
			$this->db->insert('measurements_weights',$row);
	}

}