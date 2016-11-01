<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');

Class Activity_model extends CI_Model
{
	function add($date,$person,$by,$type,$components,$notes)
	{
		// function to add a activity record for one user
		if($date == "" ) { 
			$date = date('Y-m-d H:i:s'); // if the date isn't specified use today
		}
		$eref = uniqid(); // generate a new unique reference code

		$row = array( // setup a row array
					'ref' => $eref, // unique reference
					'user' => $person, // the person who did the activity
					'creator' => $by, // the person who logged the activity
					'sort_time' => date('Y-m-d H:i:s',strtotime($date)), // when it happened
					'type' => $type, // what kind of activity it is
					'label' => 'Untitled activity', // placeholder title
					'component_count' => count($components), // the number of components
					'notes' => $notes // any notes entered
				);

		
		$this->db->insert('activities',$row); // insert into the db
		$activity_id = $this->db->insert_id(); // get the record ID of the activity

		// get information about the type of activity
		$type = $this->getTypeByID($type);
		$doSplitCalc = FALSE;
		if($type['erg_calc'] == "1") { // if this is a rowing activity, you should calculate a 500m average split
			$doSplitCalc = TRUE;
		}

		// insert components via another function
		// components represent sub sections of an activity
		// either to record interval training (common with rowing machines
		// , or activities that are split up into different segments e.g jogging)
		$this->addComponents($activity_id,$components,$doSplitCalc);

		// after adding all components, calculate statistics
		if(count($components) > 0) {
			$component_array = $this->getActivityComponents($activity_id); // call a function which does calculations
			
			$update = array( // setup an array with all the calcualted statistics
				'total_distance' => $component_array['totalDist'],
				'total_time' => $component_array['totalTimeSeconds'],
				'avg_time' => $component_array['avgTimeSeconds'],
				'avg_distance' => $component_array['avgDistance'],
				'avg_split' => $component_array['avgSplitSeconds'],
				'avg_rate' => $component_array['avgRate'],
				'avg_hr' => $component_array['avgHr']);
			
			$this->db->update('activities',$update,array('ref'=>$eref)); // update the activity record with these statistics
		}

		$this->generate_label($activity_id); // generate a user friendly label for the activity which they can customize

		// return complete activity record
		return $this->get_by_id($activity_id);
	}

	function update($acid,$update) {
		// function to update an activity record
		 return $this->db->update('activities',$update,array('id'=>$acid));
	}

	function get_by_id($acid) {
		// function to lookup a single activity by database ID
		$query = $this->db->get_where('activities',array('id'=>$acid));
		$activity = $query->row_array();
		return $activity;
	}

	function get_id_from_ref($ref) {
		// function to lookup a single activity by unique reference
		$query = $this->db->get_where('activities',array('ref'=>$ref));
		$activity = $query->row_array();
		return $activity['id'];
	}

	 function generate_label($acid) {
	 	// function to generate the label
	 	// get the acivity record
		$query = $this->db->get_where('activities',array('id'=>$acid));
		$activity = $query->row_array();
		$type = $this->getTypeByID($activity['type']); // retrieve information about the type of activity


		$label = "Activity"; // set a default label
		switch($type['value']) {
			case "ergd": 
				// if its a distance based rowing machine activity set a label which includes the distance
				$distance = $activity['total_distance'];
				if($distance > 0) { // check for a recorded distance
					$rounded = round(($distance/1000),1);
	
					$label = $rounded."K erg";
					break;
				}
			default:
				// otherwise set a label based on the total time spent on the activity and the type
			$time = $activity['total_time'];
				if($time> 0) { // check for a recorded time
					$rounded = round(($time/60),1);
					$label = $rounded." min " . $type['noun'];
				} else {
					$label = $type['noun'];
				}
			break;
		}

		// update record in the database
		$this->db->update('activities',array('label'=>$label),array('id'=>$acid));
	}

	function list_activities($user,$page,$by=NULL) {
		// function to lookup a page of a user's activities
		$this->db->from('activities,users');
		if($by != NULL) { // if a 'by' value is set it will lookup activities they've logged
			$this->db->where('creator',$user);
		} else { // otherwise activities logged for them
			$this->db->where('user',$user);
		}
		$this->db->where('activities.user = users.id');
		$this->db->order_by("added", "desc");
		$offset = 30 * ($page - 1); // set the record offset to 30 per page
		if($page != NULL) {
			$this->db->limit(30,$offset);
		}
		$query = $this->db->get();
		$results = $query->result_array();
		$return = array();
		foreach($results as $result) {
			// generate friendly output
			
			$row = array();

			$row['date'] = date("M jS Y",strtotime($result['sort_time']));

			if($by != NULL) {
				$row['person'] = $result['name'];
			}

			$row['label'] = $result['label']; // inlcude label

			if($result['avg_split'] != "") {
				$row['split'] = $this->outputSplit($result['avg_split']); // output average in HH:MM:SS format
			} else {
				$row['split'] = "-";
			}
			if($result['total_time'] != "") {
				$row['time'] = $this->outputSplit($result['total_time']); // output tiem in HH:MM:SS format
			} else {
				$row['time'] = "-";
			}
			if($result['total_distance'] != "") { // include any disttance
				$row['distance'] = ($result['total_distance']);
			} else {
				$row['distance'] = "-";
			}
			if($result['avg_rate'] != "") { // include any rate
				$row['rate'] = ($result['avg_rate']);
			} else {
				$row['rate'] = "-";
			}
			if($result['avg_hr'] != "") { // include any rate
				$row['hr'] = ($result['avg_hr']);
			} else {
				$row['hr'] = "-";
			}
			$row['avg_split'] = $result['avg_split']; // add average in seconds
			$row['sort_time'] = $result['sort_time']; // add time of activity
			$row['ref'] = $result['ref']; // add unique reference
			$return[] = $row;
		}
		return $return;
	}

	function count_for_day($id,$day) {
		// count the number of activites a user recorded in one day
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
			$field = 'COUNT(sort_time)';
		return (int)$results[$field];
		} else {
			return 0;
		}
	}


	private function addComponents($activity_id,$components,$doSplitCalc=FALSE) {
		// function to add components for an activity
		foreach($components as $key => $component) {

			// validate each data item
			$tts = $this->time_to_seconds($component['time']); // convert time into seconds
			if($tts > 0) { // if numeric set
				$time = $tts;
			} else { // otherwise nullify
				$time = NULL;
			}

			// if a distance is present add to record and check within appropriate range 
			if($component['distance'] > 0 && $component['distance'] < 1000000) {
				$distance = $component['distance'];
			} else { // otherwise nullify
				$distance = NULL;
			}

			// convert the entered average into seconds
			$tts2 = $this->time_to_seconds($component['split']);

			// if this is a rowing machine activity and time and distance entered calculate rowing averages
			if($distance != NULL && $time != NULL && $distance > 0 && $time > 0 && $doSplitCalc==TRUE) {
				$split = ($time) / ($distance/500); // calculate an average based on time and distance entered
				if($split != $tts2) { // check if the average calculated using the time and distance given
					// doesn't match user input
				}
			} else { // if not calculated set to null
				$split = NULL;
			}

			if($component['rate'] > 0 && $component['rate'] < 100) { // if rate entered and within range set it
				$rate = $component['rate'];
			} else { // otherwise set rate to null
				$rate = NULL;
			}

			if($component['hr'] > 0 && $component['hr'] < 200) { // if heart rate entered and within range set it
				$hr = $component['hr'];
			} else { // otherwise set rate to null
				$hr = NULL;
			}

			$processed = array(
					'activity_id' => $activity_id,
					'time' => $time,
					'split' => $split,
					'distance' => $distance,
					'rate' => $rate,
					'hr'=> $hr
				);
			$this->db->insert('activities_components',$processed); // insert into db
		}
	}

	public function getActivityComponents($acid) {
		// function to calculate statistics for an activity
		$query = $this->db->get_where('activities_components',array('activity_id'=>$acid));
		$components = $query->result_array();	
		//$component_ids = explode(",",$activity['components']);
		//$components = array();
		$totalTime = 0; // refers to only the time spent exercising, for calculation of averages
		$totalDist = 0; // refers to total distance achieved
		$totalRate = 0; // refers to average rate
		$totalDuration = 0; // refers to the time taken for the whole exercise including rests
		$totalHr = 0;
		$nullTime = FALSE; // initialize variables which are set to true if a null value is found
		$nullDistance = FALSE;
		$nullRate = FALSE;
		$nullHr = FALSE;
		foreach ($components as $key => $component) { // loop through all components
			
				$components[$key]['type'] = 'active'; // create an item in the final return array

				$totalDuration += $component['time']; // total up all distances, times, rates for averaging
				$totalTime += $component['time'];
				$totalDist += $component['distance'];
				$totalRate += $component['rate'];
				$totalHr += $component['hr'];

				$components[$key]['raw_time'] = $component['time'];
				$components[$key]['time'] = $this->outputSplit($component['time']);

				$components[$key]['raw_split'] = $component['split'];
				$components[$key]['split'] = $this->outputSplit($component['split']);

				////// NULL CHECKS if any null values are found stop averages being calcualted
				if($component['time'] == NULL) {
					$nullTime = TRUE;
				}
				if($component['distance'] == NULL) {
					$nullDistance = TRUE;
				}
				if($component['rate'] == NULL) {
					$nullRate = TRUE;
				}
				if($component['hr'] == NULL ) {
					$nullHr = TRUE;
				}
			
		}

		$no_of_components = count($components); // total number of compoents

		$components['totalTime'] = $this->outputSplit($totalTime); // output the total time HH:MM:SS
		$components['totalTimeSeconds'] = $totalTime; // output total time in seconds
		$components['totalDist'] = $totalDist; // output total distance
		$avgTime = ($totalTime)/$no_of_components; // calculate average time in seconds
		$components['avgTimeSeconds'] = $avgTime; // output average time in secnods
		$components['avgTime'] = $this->outputSplit($avgTime); // output average time in HH:MM:SS
		$components['avgDistance'] = round(	(($totalDist)/$no_of_components)	, 2); // output rounded average distance
		$components['avgHr'] = round(	(($totalHr)/$no_of_components)	, 2);  // output rounded average Heart Rate
		if($totalTime > 0 && $totalDist > 0) { // if the total time and distance are set
			$avgSplit = ($totalTime) / ($totalDist/500); // calcualte an average over 500m
			$components['avgSplitSeconds'] = $avgSplit; // output it in seconds
			$components['avgSplit'] = $this->outputSplit($avgSplit); // output it in HH:MM:SS
		} else {
			$components['avgSplitSeconds'] = null; // otherwise set to nnull
			$components['avgSplit'] = null;
		}
		$components['avgRate'] = round(($totalRate / $no_of_components),1); // output average stroke rate

		if($nullTime) { // if a null time found remove all time stats
			$components['totalTime'] = NULL;
			$components['totalTimeSeconds'] = NULL;
			$components['avgTimeSeconds'] = NULL;
			$components['avgTime'] = NULL;
			$components['avgSplitSeconds'] = NULL;
			$components['avgSplit'] = NULL;
		}
		if($nullDistance) { // if a null distance found remove all distance stats
			$components['totalDist'] = NULL;
			$components['avgDistance'] = NULL;
			$avgSplit = NULL;
			$components['avgSplitSeconds'] = NULL;
			$components['avgSplit'] =NULL;
		}
		if($nullRate) { // if a null rate found remove all rate stats
			$components['avgRate'] = NULL;
		}
		if($nullHr) {
			$components['avgHr'] = NULL;
		}
		return $components;
	}
	
	public function get($ref) {
		// function to get an activity by reference code
		$query =  $this->db->get_where('activities',array('ref'=>$ref));
		return $query->row_array();
	}

	public function list_years($uid) {
		// function to output all years containing a user activity
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
		// function to output all months in which a user records activity for a specific year
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
		// function which retrieves the latest splits for special test ergs
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
		// function to get special rowing averages for a given month
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
		// function to output all days in which a user records activity for a specific year / month
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
		// function to output all activites for a given date
		$date_start = strtotime("$year-$month-$day 00:00:00");
		$date_end = strtotime("$year-$month-$day 23:59:59");

		$this->db->order_by("sort_time", "asc") ;	
		$query = $this->db->get_where('activities', array(
			'user' => $uid,
			'sort_time >= ' => date("Y-m-d H:i:s",($date_start)),
			'sort_time <= ' => date("Y-m-d H:i:s",($date_end))
		));
		
		return $query->result_array();

	}

	private function get_pb_distance($id,$dist) {
		// function to output the best score a user has achieved for one type of distance exercise
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
		// function to output the best score a user has achieved for one type of time exercise
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
		// function which collates common personal best scores
		$pbs = array();

		// distance PBs
		$pbs[] = $this->get_pb_distance($id,2); // gets best score for 2K
		$pbs[] = $this->get_pb_distance($id,5); // gets best score for 5K 
		$pbs[] = $this->get_pb_distance($id,12); // gets best score for 12K
		$pbs[] = $this->get_pb_time($id,30); // gets best score for 1/2hour
		return $pbs;

	
	}



	public function erg_history($id,$type,$distance_or_length,$no=5) {
		// function to lookup a users last 5 scores for a particulare type of either timed or distance rowing machine exercise
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

	function delete($user,$acref)
	{
		// function to delete an activity on behalf of a user
		// check permission
		$activity = $this->get($acref);
		$id = $activity['id'];
		$permission = FALSE;
		if($activity['user'] == $user) {
			$permission = TRUE;
		}

		if($permission == TRUE) {
			$this->db->where('activity_id',$id);
			$this->db->delete('activities_components');
			$this->db->where('ref',$acref);
			$this->db->limit(1);
			$this->db->delete('activities');
			return TRUE;
		} else {
			return FALSE;
		}
	}

	function getTypes() {
		// function to lookup all stored types of activity
		$query = $this->db->get('activities_types');
		$types = array();
		foreach ($query->result_array() as $row) // output seperated by type group (rowing, indoor, outdoor etc)
		{
			$types[$row['group']][] = $row;	 
		}
		return $types;
	}

	function getTypeByRef($ref) {
		// function to lookup a type by ref
		$query = $this->db->get_where('activities_types',array('value'=>$ref));
		$result = $query->row_array();

		if($result) {
			return $result['id'];
		} else {
			return FALSE;
		}
	}

	function getTypeIDByRef($ref) {
		// function which gets the DB id of a type by its short string value
		$query = $this->db->get_where('activities_types',array('value'=>$ref));
		$result = $query->row_array();

		if($result) {
			return $result['id'];
		} else {
			return FALSE;
		}
	}

	function getTypeByID($id) {
		// function lookup an activity type by db ID
		$query = $this->db->get_where('activities_types',array('id'=>$id));
		$result = $query->row_array();

		if($result) {
			return $result;
		} else {
			return FALSE;
		}
	}

	private function time_to_seconds($time) {
		// function to calculate the seconds a HH:MM:SS time represents
		$parts = explode(":",$time); // split up by colon
		$parts = array_reverse($parts); // reverse array so starts with seconds
		$raise60 = 0; // initialize calculate values
		$total_seconds = 0;
		foreach($parts as $part) { // go through each part
			//echo "$part x 60 ^ $raise60<br>";
		
			$seconds = $part * pow(60,$raise60);
			//echo $seconds . "<br>";
			$total_seconds += $seconds;
			$raise60++; // increment the power of 60 used, 0 for secs, 1 for minutes, 2 for hour etc
		}
		return $total_seconds;
	}

	function outputSplit($init,$longOutput = FALSE) {
		// function to convert a time in seconds to HH:MM:SS (split time format)

		$hours = floor($init / 3600); // get integer division of 3600 (seconds in an hour)
		$minutes = floor(($init / 60) % 60); // likewise for minutes
		$seconds = $init % 60; // and what's left in seconds
		$fractional =  strstr($init,"."); // plus anything else in decimal section
		if($fractional == "") {
			$fractional = ".0"; // if no decimal section, use placeholder .0
		}
		$combined_seconds = $seconds . substr($fractional,0,2); // put together integer and decimal seconds
		if($seconds < 10) {
			$second_pad = "0";
		} else {
			$second_pad = NULL;
		}

		$pretty = $minutes . ":" . $second_pad . $combined_seconds; // set pretty output with colons seperating each part

		if($longOutput == TRUE) { // if longoutput wanted, prepend hours and pad values so if they're less than 10 they have padding 0
			$pretty = str_pad($hours,2,"0",STR_PAD_LEFT) . ":"  . str_pad($minutes,2,"0",STR_PAD_LEFT) . ":" . $second_pad . $combined_seconds;
		}

		return $pretty;
	}

	function get_types() {
		// get all activity types from database
		$this->db->from('activities_types');
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
		// function to lookup all people in the users table who were created by a coach
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
		// function to search for activities done by a set of users
		$this->db->from('activities');
		$this->db->where($fields);

		$user_where = 'user IN (' . implode(",",$users) . ')';

		$this->db->where($user_where);
		$this->db->order_by("sort_time", "desc");
		$query = $this->db->get();
		$results = $query->result_array();
		return $results;
	}

}