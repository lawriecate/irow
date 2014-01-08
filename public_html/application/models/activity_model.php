<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');

Class Activity_model extends CI_Model
{
	function add($date,$person,$by,$type,$components,$notes)
	{
		//echo 'hi';
		if($date == "" ) { 
			$date = date('Y-m-d H:i:s');
		}
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

		// get type information
		$type = $this->getTypeByID($type);
		$doSplitCalc = FALSE;
		if($type['erg_calc'] == "1") {
			$doSplitCalc = TRUE;
		}

		// insert components
		$this->addComponents($activity_id,$components,$doSplitCalc);

		// calculate statistics if components present
		if(count($components) > 0) {
			$component_array = $this->getActivityComponents($activity_id);
			$update = array(
				'total_distance' => $component_array['totalDist'],
				'total_time' => $component_array['totalTimeSeconds'],
				'avg_time' => $component_array['avgTimeSeconds'],
				'avg_distance' => $component_array['avgDistance'],
				'avg_split' => $component_array['avgSplitSeconds'],
				'avg_rate' => $component_array['avgRate']);
			
			$this->db->update('activities',$update,array('ref'=>$eref));
		}
		$this->generate_label($activity_id);

		// return final activity record
		return $this->get_by_id($activity_id);
	}

	function update($acid,$update) {
		//$permitted_fields = array('label','notes','sort_time');
		//$update = 
		 return $this->db->update('activities',$update,array('id'=>$acid));
	}

	function get_by_id($acid) {
		$query = $this->db->get_where('activities',array('id'=>$acid));
		$activity = $query->row_array();
		return $activity;
	}

	function get_id_from_ref($ref) {
		$query = $this->db->get_where('activities',array('ref'=>$ref));
		$activity = $query->row_array();
		return $activity['id'];
	}

	 function generate_label($acid) {
		$query = $this->db->get_where('activities',array('id'=>$acid));
		$activity = $query->row_array();
		//$component_array = $this->getActivityComponents($acid);
		$type = $this->getTypeByID($activity['type']);


		$label = "Activity";
		switch($type['value']) {
			case "ergd": 
				
				$distance = $activity['total_distance'];
				if($distance > 0) {
					$rounded = round(($distance/1000),1);
	
					$label = $rounded."K erg";
					break;
				}
			default:
			$time = $activity['total_time'];
				if($time> 0) {
					$rounded = round(($time/60),1);
					$label = $rounded." min " . $type['noun'];
				} else {
					$label = $type['noun'];
				}
			break;
		}

		$this->db->update('activities',array('label'=>$label),array('id'=>$acid));
	}

	/*function list_activities($start,$end,$user) {
		$this->db->order_by("sort_time", "asc") ;	
		$query = $this->db->get_where('activities', array(
			'user' => $user,
			'sort_time >= ' => date("Y-m-d H:i:s",($start)),
			'sort_time <= ' => date("Y-m-d H:i:s",($end))
		));
		
		return $query->result_array();
		//echo date("Y-m-d H:i:s",($start)) . '<br> ' . date("Y-m-d H:i:s",($end));
	}*/

	function list_activities($user,$page,$by=NULL) {
		$this->db->from('activities,users');
		if($by != NULL) {
			$this->db->where('creator',$user);
		} else {
			$this->db->where('user',$user);
		}
		$this->db->where('activities.user = users.id');
		//$this->db->order_by("sort_time", "desc");
		$this->db->order_by("added", "desc");
		$offset = 30 * ($page - 1);
		if($page != NULL) {
			$this->db->limit(30,$offset);
		}
		$query = $this->db->get();
		$results = $query->result_array();
		$return = array();
		foreach($results as $result) {

			
			$row = array();

			$row['date'] = date("M jS Y",strtotime($result['sort_time']));

			if($by != NULL) {
				$row['person'] = $result['name'];
			}

			$row['label'] = $result['label'];

			if($result['avg_split'] != "") {
				$row['split'] = $this->outputSplit($result['avg_split']);
			} else {
				$row['split'] = "-";
			}
			if($result['total_time'] != "") {
				$row['time'] = $this->outputSplit($result['total_time']);
			} else {
				$row['time'] = "-";
			}
			if($result['total_distance'] != "") {
				$row['distance'] = ($result['total_distance']);
			} else {
				$row['distance'] = "-";
			}
			if($result['avg_rate'] != "") {
				$row['rate'] = ($result['avg_rate']);
			} else {
				$row['rate'] = "-";
			}
			$row['avg_split'] = $result['avg_split'];
			$row['sort_time'] = $result['sort_time'];
			$return[] = $row;
		}
		return $return;
	}

	function count_for_day($id,$day) {
		$day = date("Y-m-d",($day));
		$start = $day . " 00:00:00";
		$end = $day . " 23:59:59";


		$this->db->select('COUNT(sort_time),DATE(sort_time)');
		$this->db->from('activities');
		$this->db->where('sort_time >= ', $start);
		$this->db->where('sort_time <= ', $end);
		$this->db->where('user',$id);
		$this->db->group_by('DATE(sort_time)');
		$this->db->order_by('sort_time','desc');

		$query = $this->db->get();
		if($query->num_rows() > 0) {
			$results = $query->row_array();
			//print_r($results);echo"\n";
			$field = 'COUNT(sort_time)';
		return (int)$results[$field];
		} else {
			return 0;
		}
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

	private function addComponents($activity_id,$components,$doSplitCalc=FALSE) {
		foreach($components as $key => $component) {

			// validate each data item
			$tts = $this->time_to_seconds($component['time']);
			if($tts > 0) {
				$time = $tts;
			} else {
				$time = NULL;
			}

			if($component['distance'] > 0 && $component['distance'] < 1000000) {
				$distance = $component['distance'];
			} else {
				$distance = NULL;
			}

			$tts2 = $this->time_to_seconds($component['split']);
			if($distance != NULL && $time != NULL && $distance > 0 && $time > 0 && $doSplitCalc==TRUE) {
				// recalculate
				$split = ($time) / ($distance/500);
				//$split = $tts2;
				if($split != $tts2) {
					// doesn't match user input
				}
			} else {
				$split = NULL;
			}

			if($component['rate'] > 0 && $component['rate'] < 100) {
				$rate = $component['rate'];
			} else {
				$rate = NULL;
			}

			$processed = array(
					'activity_id' => $activity_id,
					'time' => $time,
					'split' => $split,
					'distance' => $distance,
					'rate' => $rate
				);
			$this->db->insert('activities_components',$processed);
		}
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
		$nullTime = FALSE;
		$nullDistance = FALSE;
		$nullRate = FALSE;
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

				////// NULL CHECKS
				if($component['time'] == NULL) {
					$nullTime = TRUE;
				}
				if($component['distance'] == NULL) {
					$nullDistance = TRUE;
				}
				if($component['rate'] == NULL) {
					$nullRate = TRUE;
				}
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
		$components['avgDistance'] = round(	(($totalDist)/$no_of_components)	, 2);
		if($totalTime > 0 && $totalDist > 0) {
			$avgSplit = ($totalTime) / ($totalDist/500);
			$components['avgSplitSeconds'] = $avgSplit;
			$components['avgSplit'] = $this->outputSplit($avgSplit);
		} else {
			$components['avgSplitSeconds'] = null;
			$components['avgSplit'] = null;
		}
		$components['avgRate'] = round(($totalRate / $no_of_components),1);

		if($nullTime) {
			$components['totalTime'] = NULL;
			$components['totalTimeSeconds'] = NULL;
			$components['avgTimeSeconds'] = NULL;
			$components['avgTime'] = NULL;
			$components['avgSplitSeconds'] = NULL;
			$components['avgSplit'] = NULL;
		}
		if($nullDistance) {
			$components['totalDist'] = NULL;
			$components['avgDistance'] = NULL;
			$avgSplit = NULL;
			$components['avgSplitSeconds'] = NULL;
			$components['avgSplit'] =NULL;
		}
		if($nullRate) {
			$components['avgRate'] = NULL;
		}
		return $components;
	}
	
	public function get($ref) {
		$query =  $this->db->get_where('activities',array('ref'=>$ref));
		return $query->row_array();
	}

	public function list_years($uid) {
		$this->db->select('user, sort_time, YEAR(sort_time), COUNT(*)');
		$this->db->from('activities');
		$this->db->where('user',$uid);
		$this->db->group_by('YEAR(sort_time)');
		$query = $this->db->get();

		$years = array();

		foreach ($query->result() as $row) {
			$year = $row->{"YEAR(sort_time)"};
			$years[$year] = $row->{"COUNT(*)"};
		}

		$currentYear = date("Y");
		if(!isset($years[$currentYear])) {
			$years[$currentYear] = 0;
		}

		return $years;
	
	}

	public function list_months($uid,$year) {
		$this->db->select('user, sort_time, YEAR(sort_time), MONTH(sort_time),COUNT(*)');
		$this->db->from('activities');
		$this->db->where(array(
			'user' => $uid,
			'YEAR(sort_time)' => $year
			));
		$this->db->group_by('MONTH(sort_time)');
		$query = $this->db->get();

		$months = array();

		for($i=1;$i<=12;$i++) {
			$months[$i] =FALSE;
		}

		foreach ($query->result() as $row) {
			$month = $row->{"MONTH(sort_time)"};
			$months[$month] = $row->{"COUNT(*)"};
		}
	
		return $months;
	}

	public function get_year_averages($uid,$year,$mode='2K') {
		$this->db->select('user, sort_time, YEAR(sort_time), MONTH(sort_time),COUNT(*),AVG(avg_split),total_distance,type');
		$this->db->from('activities');
		$where = "user = $uid AND YEAR(sort_time) = $year AND (type = '1' OR type = '2') ";
		switch($mode) {
			case '2K':
				$where .= "AND total_distance = 2000";
				break;
			case '30min':
				$where .= "AND total_time = 1800";
				break;
		}
		$this->db->where($where);
		/*$this->db->where(array(
			'user' => $uid,
			'YEAR(sort_time)' => $year
			));*/
		
		$this->db->group_by('MONTH(sort_time)');
		$query = $this->db->get();

		$months = array();

		for($i=1;$i<=12;$i++) {
			$months[$i] = 0;
		}

		foreach ($query->result() as $row) {
			$month = $row->{"MONTH(sort_time)"};
			$months[$month] = (round($row->{"AVG(avg_split)"}));
		}

		$list = array();
		foreach($months as $month) {
			$list[] = $month;
		}
	
		return $list;
	}

	public function get_month_averages($uid,$year,$month,$mode='2K') {
		$this->db->select('user, sort_time, YEAR(sort_time), MONTH(sort_time),DAY(sort_time),COUNT(*),AVG(avg_split),total_distance,type');
		$this->db->from('activities');
		$where = "user = $uid AND YEAR(sort_time) = $year AND MONTH(sort_time) = $month AND (type = '1' OR type = '2') ";
		switch($mode) {
			case '2K':
				$where .= "AND total_distance = 2000";
				break;
			case '30min':
				$where .= "AND total_time = 1800";
				break;
		}
		$this->db->where($where);
		/*$this->db->where(array(
			'user' => $uid,
			'YEAR(sort_time)' => $year
			));*/
		
		$this->db->group_by('DAY(sort_time)');
		$query = $this->db->get();

		$days = array();

		for($i=1;$i<=cal_days_in_month(CAL_GREGORIAN, $month, $year);$i++) {
			$days[$i] = 0;
		}

		foreach ($query->result() as $row) {
			$day = $row->{"DAY(sort_time)"};
			$days[$day] = (round($row->{"AVG(avg_split)"}));
		}

		$list = array();
		foreach($days as $day) {
			$list[] = $day;
		}
	
		return $list;
	}

	public function list_days($uid,$year,$month) {
		$this->db->select('user, sort_time, DAY(sort_time), COUNT(*)  ');
		$this->db->from('activities');
		$date_start = date("Y-m-1 00:00:00",strtotime("$year-$month"));
		$date_end = date("Y-m-t 24:00:00",strtotime("$year-$month"));
		
		$this->db->where(array(
			'sort_time >= ' => $date_start,
			'sort_time <= ' => $date_end,
			'user' =>$uid

			));
		$this->db->order_by('sort_time', 'asc');
		$this->db->group_by('DAY(sort_time)');
		$query = $this->db->get();

		$days = array();

		for($i=1;$i<=cal_days_in_month(CAL_GREGORIAN, $month, $year);$i++) {
			$days[$i] = 0;
		}

		foreach ($query->result() as $row) {
			$days[$row->{"DAY(sort_time)"}] =  $row->{"COUNT(*)"};
		}
		return $days;
	}

	public function list_exercises_for_day($uid,$year,$month,$day) {
		$date_start = strtotime("$year-$month-$day 00:00:00");
		$date_end = strtotime("$year-$month-$day 23:59:59");

		$this->db->order_by("sort_time", "asc") ;	
		$query = $this->db->get_where('activities', array(
			'user' => $uid,
			'sort_time >= ' => date("Y-m-d H:i:s",($date_start)),
			'sort_time <= ' => date("Y-m-d H:i:s",($date_end))
		));
		
		return $query->result_array();

		//return $this->list_activities($date_start,$date_end,$uid);
	}

	private function get_pb_distance($id,$dist) {
		$this->db->select('user,ref,type,component_count,total_distance,avg_split,total_time');
		$this->db->from('activities');
		$where = "user = $id AND total_distance = " . ($dist * 1000) . " AND type = '2' ";
		$this->db->where($where);
			$this->db->order_by('total_time','asc');
		$this->db->limit(1);
		$query = $this->db->get();

		$pb1 = array();
		$pb1['label'] = $dist.'K';
		$pb1['type'] = "distance";
		$pb1['dislen'] = $dist;
		if($query->num_rows() == 1) {
			$result = $query->row_array();
			$tt = $result['total_time'];
			$at = $result['avg_split'];
			$pb1['found'] = TRUE;
			$pb1['score'] = $this->outputSplit($tt);
			$pb1['split'] = $this->outputSplit($at);
		} else {
			$pb1['found'] = FALSE;
			$pb1['score'] = "No ".$dist."K recorded";
			$pb1['split'] = "";
		} 
		return $pb1;
	}

	public function get_pb_time($id,$time) {
		$this->db->select('user,ref,type,component_count,total_distance,total_time,avg_split');
		$this->db->from('activities');
		$where = "user = $id AND total_time = ".($time*60)." AND type = '1' AND total_distance > 0 ";
		$this->db->where($where);
			$this->db->order_by('total_distance','desc');
		$this->db->limit(1);
		$query = $this->db->get();
		$pb2 = array();
		$pb2['type'] = "time";
		$pb2['dislen'] = $time;
		
		if($query->num_rows()==1) {
			$result  = $query->row_array();
		$at = $result['avg_split'];
		$pb2['found'] = TRUE;
		$pb2['score'] = $result['total_distance'] . 'm';
		$pb2['split'] = $this->outputSplit($at);
		} else {
			$pb2['found'] = FALSE;
			$pb2['score'] = "";
			$pb2['split'] = "";
		} 
		$pb2['label'] = '&frac12; hour';
		return $pb2;
	}

	public function get_personal_bests($id) {
		$pbs = array();

		// distance PBs
		$pbs[] = $this->get_pb_distance($id,2); // gets best score for 2K
		$pbs[] = $this->get_pb_distance($id,5); // gets best score for 5K 
		$pbs[] = $this->get_pb_distance($id,12); // gets best score for 12K
		$pbs[] = $this->get_pb_time($id,30); // gets best score for 1/2hour
		return $pbs;

	
	}



	public function erg_history($id,$type,$distance_or_length,$no=5) {
		$this->db->select('user,ref,type,component_count,total_distance,avg_split,total_time,added,sort_time');
		$this->db->from('activities');
		$this->db->where('user',$id);
		if($type == "distance") {
			$this->db->where('total_distance',($distance_or_length * 1000) ); 
			$this->db->where('type', '2');
			
		} else {
			$this->db->where('total_time',($distance_or_length * 60) ); 
			$this->db->where('type', '1');
		}
		$this->db->order_by('total_time','asc');
		$this->db->limit($no);
		$query = $this->db->get();
		return $query->result_array();
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

	function getTypeByID($id) {
		$query = $this->db->get_where('activities_types',array('id'=>$id));
		$result = $query->row_array();

		if($result) {
			return $result;
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

	function outputSplit($init,$longOutput = FALSE) {
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

	function get_types() {
		$this->db->from('activities_types');
		//$this->db->group_by('group');

		$query = $this->db->get();
		$types = $query->result_array();
		$return = array();
		foreach($types as $type) {
			$group = $type['group'];
			$type_object= $type;
			$return[$group][] = $type_object;
		}
		return $return;
	}

	function get_coaches_people($coach,$input) {
		// get coaches permission
		$this->db->from('users_clubs');
		$this->db->where('user_id',$coach);
		$this->db->where('(level = "coach" OR level = "manager")');
		$query = $this->db->get();

		$permitted_clubs = array();
		foreach($query->result_array() as $permission) {
			$permitted_clubs[] = $permission['club_id'];
		}

		$this->db->select("users.name,users_clubs.user_id,users_clubs.club_id, GROUP_CONCAT(clubs.name) as clubs");
		$this->db->from('users,users_clubs,clubs');
		$this->db->like('users.name',$input);
		$this->db->group_by('users.id');
		$this->db->where("(users.id = users_clubs.user_id AND clubs.id = users_clubs.club_id)");
		//$this->db->or_where("users.invitee", $coach);
		$clubs_where_phrase = "(";
		foreach($permitted_clubs as $key => $permitted_club) {
			if($key>0) {
				$clubs_where_phrase .= " OR ";
			}
			$clubs_where_phrase .= " users_clubs.club_id = " . $permitted_club;
		}
		$clubs_where_phrase .= ")";
	
		$this->db->where($clubs_where_phrase);

		$query = $this->db->get();
		$from_club = $query->result_array();

		$this->db->select("users.name,users.id as user_id,users.invitee");
		$this->db->from('users');
		$this->db->like('users.name',$input);
		$this->db->where('users.invitee',$coach);
		$query = $this->db->get();
		$from_invited = $query->result_array();

		return array_merge($from_club,$from_invited);
	}

	function search_activities($fields,$users) {
		$this->db->from('activities');
		$this->db->where($fields);

		$user_where = 'user IN (' . implode(",",$users) . ')';

		$this->db->where($user_where);
		//$this->db->order_by("sort_time", "desc");
		$query = $this->db->get();
		$results = $query->result_array();
		//echo ' 			' . $this->db->last_query() . '         ';
		return $results;
	}

}