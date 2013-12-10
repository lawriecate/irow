<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');

Class Activity_model extends CI_Model
{
	function add($date,$person,$by,$type,$components,$notes)
	{
		//echo 'hi';
		$eref = uniqid();

		$row = array(
					'ref' => $eref,
					'user' => $person,
					'creator' => $by,
					'sort_time' => date('Y-m-d H:i:s',strtotime($date)),
					'type' => $type,
					'label' => 'TEST',
					'component_count' => count($components),
					'notes' => $notes
				);

		//print_r($row);
		$this->db->insert('activities',$row);
		$activity_id = $this->db->insert_id();
		// insert components
		$this->addComponents($activity_id,$components);

		// calculate statistics if components present
		if(count($components) > 0) {
			$component_array = $this->getActivityComponents($activity_id);
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

	/*private function componentsString($components) {
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
	}*/

	private function addComponents($activity_id,$components) {
		foreach($components as $key => $component) {
			$processed = array(
					'activity_id' => $activity_id,
					'time' => $this->time_to_seconds($component['time']),
					'split' => $this->time_to_seconds($component['split']),
					'distance' => $component['distance'],
					'rate' => $component['rate']
				);
			$this->db->insert('activities_components',$processed);
		}
	}

	private function activityStats($acid) {

	}

	public function getActivityComponents($acid) {
		$query = $this->db->get_where('activities_components',array('activity_id'=>$acid));
		$components = $query->result_array();	
		//$component_ids = explode(",",$activity['components']);
		//$components = array();
		$totalTime = 0; // refers to only the time spent exercising, for calculation of averages
		$totalDist = 0; // refers to total distance achieved
		$totalRate = 0; // refers to average rate
		$totalDuration = 0; // refers to the time taken for the whole exercise including rests
		foreach ($components as $key => $component) {
			
			// process part of exercise sequence
			/*$start_char = substr($component_id,0,1);
			if($start_char == "R") {
				// rest block
				$component['type'] = 'rest';
				$duration = substr($component_id, 1);
				if(is_numeric($duration)) {
					$component['time'] = $duration;
					$totalDuration += $duration;
				}
			} else {*/
				$components[$key]['type'] = 'active';
				//$query = $this->db->get_where('activities_components',array('id'=>$component_id),1);
				//$component = $query->row_array();

				$totalDuration += $component['time'];
				$totalTime += $component['time'];
				$totalDist += $component['distance'];
				$totalRate += $component['rate'];

				$components[$key]['raw_time'] = $component['time'];
				$components[$key]['time'] = $this->outputSplit($component['time']);

				$components[$key]['raw_split'] = $component['split'];
				$components[$key]['split'] = $this->outputSplit($component['split']);
			//}
			//$components[] = $component;
		}

		$no_of_components = count($components);

		$components['totalTime'] = $this->outputSplit($totalTime);
		$components['totalTimeSeconds'] = $totalTime;
		$components['totalDist'] = $totalDist;
		$avgTime = ($totalTime)/$no_of_components;
		$components['avgTimeSeconds'] = $avgTime;
		$components['avgTime'] = $this->outputSplit($avgTime);
		$components['avgDist'] = round(	(($totalDist)/$no_of_components)	, 2);
		$avgSplit = ($totalTime) / ($totalDist/500);
		$components['avgSplitSeconds'] = $avgSplit;
		$components['avgSplit'] = $this->outputSplit($avgSplit);
		$components['avgRate'] = round(($totalRate / $no_of_components),1);
		return $components;
	}
	
	public function get($ref) {
		$query =  $this->db->get_where('activities',array('ref'=>$ref));
		return $query->row_array();
	}

	public function list_years($uid) {
		$this->db->select('user, sort_time, YEAR(sort_time)');
		$this->db->from('activities');
		$this->db->where('user',$uid);
		$this->db->group_by('YEAR(sort_time)');
		$query = $this->db->get();

		$years = array();

		foreach ($query->result() as $row) {
			$years[] = $row->{"YEAR(sort_time)"};
		}
		return $years;
	}

	public function list_months($uid,$year) {
		$this->db->select('user, sort_time, YEAR(sort_time), MONTH(sort_time)');
		$this->db->from('activities');
		$this->db->where(array(
			'user' => $uid,
			'YEAR(sort_time)' => $year
			));
		$this->db->group_by('MONTH(sort_time)');
		$query = $this->db->get();

		$months = array();

		foreach ($query->result() as $row) {
			$months[] = $row->{"MONTH(sort_time)"};
		}
		return $months;
	}

	public function list_days($uid,$year,$month) {
		$this->db->select('user, sort_time, DAY(sort_time), COUNT(*)  ');
		$this->db->from('activities');
		$date_start = date("Y-m-1 00:00:00",strtotime("$year-$month"));
		$date_end = date("Y-m-t 24:00:00",strtotime("$year-$month"));
		
		$this->db->where(array(
			'sort_time >= ' => $date_start,
			'sort_time <= ' => $date_end
			));
		$this->db->order_by('sort_time', 'asc');
		$this->db->group_by('DAY(sort_time)');
		$query = $this->db->get();

		$days = array();

		foreach ($query->result() as $row) {
			$days[$row->{"DAY(sort_time)"}] =  $row->{"COUNT(*)"};
		}
		return $days;
	}

	public function list_exercises_for_day($uid,$year,$month,$day) {
		$date_start = strtotime("$year-$month-$day 00:00:00");
		$date_end = strtotime("$year-$month-$day 24:00:00");
		return $this->list_activities($date_start,$date_end,$uid);
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