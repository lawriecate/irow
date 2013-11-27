<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');

Class Activity_model extends CI_Model
{
	function add($date,$person,$by,$type,$components,$notes)
	{
		echo 'hi';
		$eref = uniqid();

		$row = array(
					'ref' => $eref,
					'user' => $person,
					'creator' => $by,
					'sort_time' => date('Y-m-d H:i:s',strtotime($date)),
					'type' => $type,
					'label' => 'TEST',
					'components' => $this->componentsString($components),
					'component_count' => count($components),
					'notes' => $notes
				);
		print_r($row);
			$this->db->insert('activities',$row);

		// calculate statistics if components present
		if(count($components) > 0) {
			$component_array = $this->getActivityComponents($eref);
			$update = array(
				'total_distance' => $component_array['totalDist'],
				'total_time' => $component_array['totalTimeSeconds'],
				'avg_time' => $component_array['avgTimeSeconds'],
				'avg_split' => $component_array['avgSplitSeconds'],
				'avg_rate' => $component_array['avgRate']);
			
			$this->db->update('activities',$update,array('ref'=>$eref));
		}
	}

	function list_activities($start,$end,$user) {
		$this->db->order_by("sort_time", "asc") ;	
		$query = $this->db->get_where('activities', array(
			'user' => $user,
			'sort_time >= ' => date("Y-m-d H:i:s",($start)),
			'sort_time <= ' => date("Y-m-d H:i:s",($end))
		));
		
		return $query->result_array();
		//echo date("Y-m-d H:i:s",($start)) . '<br> ' . date("Y-m-d H:i:s",($end));
	}

	private function componentsString($components) {
		$ids = array();
		foreach ($components as $key => $component) {
			$processed = array(
					'time' => $this->time_to_seconds($component['time']),
					'split' => $this->time_to_seconds($component['split']),
					'distance' => $component['distance'],
					'rate' => $component['rate']
				);
			$this->db->insert('activities_components',$processed);
			$ids[] = $this->db->insert_id();
		}
		return implode(",",$ids);
	}

	private function activityStats($acid) {

	}

	public function getActivityComponents($acid) {
		$query = $this->db->get_where('activities',array('ref'=>$acid),1);
		$activity = $query->row_array();	
		$component_ids = explode(",",$activity['components']);
		$components = array();
		$totalTime = 0; // refers to only the time spent exercising, for calculation of averages
		$totalDist = 0; // refers to total distance achieved
		$totalRate = 0; // refers to average rate
		$totalDuration = 0; // refers to the time taken for the whole exercise including rests
		foreach ($component_ids as $component_id) {

			// process part of exercise sequence
			$start_char = substr($component_id,0,1);
			if($start_char == "R") {
				// rest block
				$component['type'] = 'rest';
				$duration = substr($component_id, 1);
				if(is_numeric($duration)) {
					$component['time'] = $duration;
					$totalDuration += $duration;
				}
			} else {
				$component['type'] = 'active';
				$query = $this->db->get_where('activities_components',array('id'=>$component_id),1);
				$component = $query->row_array();

				$totalDuration += $component['time'];
				$totalTime += $component['time'];
				$totalDist += $component['distance'];
				$totalRate += $component['rate'];

				$component['raw_time'] = $component['time'];
				$component['time'] = $this->outputSplit($component['time']);

				$component['raw_split'] = $component['split'];
				$component['split'] = $this->outputSplit($component['split']);
			}
			$components[] = $component;
		}

		$components['totalTime'] = $this->outputSplit($totalTime);
		$components['totalTimeSeconds'] = $totalTime;
		$components['totalDist'] = $totalDist;
		$avgTime = ($totalTime)/count($component_ids);
		$components['avgTimeSeconds'] = $avgTime;
		$components['avgTime'] = $this->outputSplit($avgTime);
		$components['avgDist'] = (($totalDist)/count($component_ids));
		$avgSplit = ($totalTime) / ($totalDist/500);
		$components['avgSplitSeconds'] = $avgSplit;
		$components['avgSplit'] = $this->outputSplit($avgSplit);
		$components['avgRate'] = round(($totalRate / count($component_ids)),1);
		return $components;
	}

	function delete($id)
	{

	}

	function getTypes() {
		$query = $this->db->get('activities_types');
		$types = array();
		foreach ($query->result_array() as $row)
		{
			$types[$row['group']][] = $row;	
		}
		return $types;
	}

	function getTypeByRef($ref) {
		$query = $this->db->get_where('activities_types',array('value'=>$ref));
		$result = $query->row_array();
		if($result) {
			return $result['id'];
		} else {
			return FALSE;
		}
	}

	private function time_to_seconds($time) {
		$parts = explode(":",$time);
		$parts = array_reverse($parts);
		$raise60 = 0;
		$total_seconds = 0;
		foreach($parts as $part) {
			//echo "$part x 60 ^ $raise60<br>";
		
			$seconds = $part * pow(60,$raise60);
			//echo $seconds . "<br>";
			$total_seconds += $seconds;
			$raise60++;
		}
		return $total_seconds;
	}

	private function outputSplit($init,$longOutput = FALSE) {
		// for i = 1 to 3
			// for j = 0 to (60 * i)
		//return gmdate("H:i:s",$seconds);
		$hours = floor($init / 3600);
		$minutes = floor(($init / 60) % 60);
		$seconds = $init % 60;
		$fractional =  strstr($init,".");
		if($fractional == "") {
			$fractional = ".0";
		}
		$combined_seconds = $seconds . substr($fractional,0,2);
		if($seconds < 10) {
			$second_pad = "0";
		} else {
			$second_pad = NULL;
		}

		$pretty = $minutes . ":" . $second_pad . $combined_seconds;

		if($longOutput == TRUE) {
			$pretty = str_pad($hours,2,"0",STR_PAD_LEFT) . ":"  . str_pad($minutes,2,"0",STR_PAD_LEFT) . ":" . $second_pad . $combined_seconds;
		}

		return $pretty;
	}

}