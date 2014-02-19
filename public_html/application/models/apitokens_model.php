<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');

Class Apitokens_model extends CI_Model
{
	
	function check_token($public,$input_private_hash,$appid,$timestamp) // updates height
	{
		// find a token using public key
	
		$this->db->where('token_public',$public);

		$query = $this->db->get('api_tokens');
		if( $query->num_rows() > 0) {
			// check private hash & appid
			$row = $query->row_array();
			$valid = FALSE;
			$actual_private_hash = $row['token_private'];
			$checkhash = sha1($timestamp.$actual_private_hash);
		
			if($checkhash == $input_private_hash AND $appid = $row['appid']) {

				// update last access
				$update = array(
					'lastaccess'=> date("Y-m-d H:i:s")
					);

				$this->db->where('id',$row['id']);
				$this->db->update('api_tokens',$update);

				return $row['userid'];

			} else {
				return FALSE; // invalid hash or app id
			}

		} else {
			return FALSE; // no key exists
		}
	}

	function generate_token($appid,$userid)
	{
		$public = sha1(uniqid());
		$private = sha1(uniqid());
		$row = array(
			'appid'=>$appid,
			'userid'=>$userid,
			'token_public'=>$public,
			'token_private'=>$private
			);

		if($this->db->insert('api_tokens',$row)) {
			return $row;
		} else {
			return FALSE;
		}
	}
	


}