<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Admin extends Admin_Controller {

	public function index()
	{
		$data['title'] = "Administration";
		$this->load->view('templates/header',$data);
		$this->load->view('admin/index');
		$this->load->view('templates/footer');
	}

	public function userlist()
	{
	
		//print_r($data['tabledata']);
		$data['title'] = "Manage Users";
		$this->load->view('templates/header',$data);
		$this->load->view('admin/userlist');
		$this->load->view('templates/footer');
	}

	public function ajax_usersdata() {
		$page = (int) $this->input->get('start');
		$length = (int) $this->input->get('len');
		$query = $this->input->get('q');
		$this->load->model('user_model');
		$list = $this->user_model->search_users($page,$length,$query);

		$response = array();
		//$response['sEcho'] = (int) $this->input->get('sEcho');
		$response['total'] = $this->user_model->total_users();
		$response['display'] = count($list);
		$proList = array();
		foreach($list as $item) {
			//$item[5] = $item[4];
			$add = array();
			$add[0] = $item['id'];
			$add[1] = $item['name'];
			$add[2] = $item['email'];
			$add[3] = $item['dob'];
			$add[4] = "No";
			if($item['admin'] == "1") {
				$add[4] = "Yes";
			}

			$memberships = $this->user_model->memberships($item['id']);
			$club_label = '';
			if(count($memberships) == 0) { $club_label = 'No Memberships'; } else {
				foreach($memberships as $membership) {
					$club_label .= $membership['name'] . ' (' . ucfirst($membership['level']) . ') <br>';
				}
			}
			$add[5] = $club_label;
			$proList[] = $add;
			
		}
		$response['items'] = $proList;

		$this->output
		->set_content_type('application/json')
		->set_output(json_encode($response));
	}

	public function ajax_clubsdata() {
		$page = (int) $this->input->get('start');
		$length = (int) $this->input->get('len');
		$query = $this->input->get('q');
		$this->load->model('club_model');
		$list = $this->club_model->search_clubs($page,$length,$query);

		$response = array();
		$response['total'] = $this->club_model->total_clubs();
		$response['display'] = count($list);
		$proList = array();
		foreach($list as $item) {
			//$item[5] = $item[4];
			$add = array();
			$add[0] = $item['id'];
			$add[1] = $item['name'];
			$add[2] = $item['phone'];
			$add[3] = $item['email'];
			$proList[] = $add;
			
		}
		$response['items'] = $proList;

		$this->output
		->set_content_type('application/json')
		->set_output(json_encode($response));
	}

	public function edit_user($id) {

		$this->load->model('user_model');
		$this->load->model('club_model');
		$this->load->helper(array('form'));
		$this->load->library('form_validation');

		if($this->user_model->get_by_id($id) == FALSE ) {
			return FALSE;
		}

		$data['title'] = "Edit User";
		$data['profile'] =  $this->user_model->get_by_id($id);
		$data['memberships'] = $this->user_model->memberships($id);

		if($this->input->post('email') != $data['profile']['email']) {
			$this->form_validation->set_rules('email', 'Email', 'valid_email|trim|required|xss_clean|is_unique[users.email]'); // checks email is valid,entered,trims blank space,remove XSS script,and checks it's unique
		}
		$this->form_validation->set_rules('name', 'Name', 'trim|required|xss_clean|min_length[2]|max_length[30]');
		$this->form_validation->set_rules('dob', 'Date Of Birth', 'trim|required|xss_clean|callback__dobcheck');
	   	
	   	if($this->input->post('password') != "") {
	   		$this->form_validation->set_rules('password', 'Password', 'trim|required|xss_clean|min_length[5]|max_length[30]');
	   		$this->form_validation->set_rules('password2', 'Confirmation Password', 'trim|required|xss_clean|matches[password]');
	   	}

		//$this->load->view('templates/header',$data);
		//$this->load->view('admin/edit_user');
		//$this->load->view('templates/footer');
		if($this->form_validation->run() == FALSE) // if the form is entered and validation fails
		{
			// show form + errors
			
		    $this->load->view('templates/header',$data);
			$this->load->view('admin/edit_user',$data);
			$this->load->view('templates/footer');
		}
		else // if the form has been submitted
		{
			$email = $this->input->post('email');
			$name = $this->input->post('name');
			$password = FALSE;
			if($this->input->post('password') != "" ) {
				$password = $this->input->post('password');
			}
			$dob = $this->input->post('dob');
			$gender = $this->input->post('gender');
			$suspend = $this->input->post('suspend');
			$admin = $this->input->post('admin');

			$clubs = $this->input->post('clubs');
			
			$this->user_model->update($id,$email,$name,$password,$dob,$gender);
			$this->user_model->set_suspension($id,$suspend);
			$this->user_model->set_admin($id,$admin);
			foreach($clubs as $club_id => $level) {
				if($level == "REMOVE") {
					$this->user_model->leave_club($id,$club_id);
				} else {
					$this->user_model->set_membership($id,$club_id,$level);
				}
			}
			$data['saved'] = TRUE;
			$data['profile'] =  $this->user_model->get_by_id($id);
			$data['memberships'] = $this->user_model->memberships($id);
			//print_r($data);
			$this->load->view('templates/header',$data);
			$this->load->view('admin/edit_user',$data);
			$this->load->view('templates/footer');
		}
	}

	public function edit_club($id) {

		$this->load->model('club_model');
		$this->load->helper(array('form'));
		$this->load->library('form_validation');

		if($this->club_model->get_by_id($id) == FALSE ) {
			return FALSE;
		}

		$data['title'] = "Edit Club";
		$data['countries'] = $this->club_model->list_countries();
		$data['club'] =  $this->club_model->get_by_id($id);

		if($this->input->post('email') != $data['club']['email']) {
			$this->form_validation->set_rules('email', 'Email', 'valid_email|trim|required|xss_clean|is_unique[clubs.email]'); // checks email is valid,entered,trims blank space,remove XSS script,and checks it's unique
		}
		$this->form_validation->set_rules('name', 'Name', 'trim|required|xss_clean|min_length[2]|max_length[60]');
		$this->form_validation->set_rules('website','Website URL', 'trim|xss_clean|min_length[4]|max_length[50]|prep_url');
		$this->form_validation->set_rules('tel','Phone Number', 'trim|xss_clean|numeric|min_length[7]|max_length[20]');

		$this->form_validation->set_rules('addr1','Address Line 1', 'trim|xss_clean|min_length[2]|max_length[40]');
		$this->form_validation->set_rules('addr2','Address Line 2', 'trim|xss_clean|min_length[2]|max_length[40]');
		$this->form_validation->set_rules('addrCity','Address City', 'trim|xss_clean|min_length[2]|max_length[10]');
		$this->form_validation->set_rules('addrCountry','Address Country', 'trim|xss_clean|min_length[2]|max_length[40]');
		$this->form_validation->set_rules('addrPostcode','Address Postcode', 'trim|xss_clean|min_length[2]|max_length[10]');

		$this->form_validation->set_rules('verify','Verification Status', 'greater_than[-1]|less_than[3]');

	 
		//$this->load->view('templates/header',$data);
		//$this->load->view('admin/edit_user');
		//$this->load->view('templates/footer');
		if($this->form_validation->run() == FALSE) // if the form is entered and validation fails
		{
			// show form + errors
			
		    $this->load->view('templates/header',$data);
			$this->load->view('admin/edit_club',$data);
			$this->load->view('templates/footer');
		}
		else // if the form has been submitted
		{
			$fields = array(
				'email' => $this->input->post('email'),
				'name'=> $this->input->post('name'),
				'website'=> $this->input->post('website'),
				'phone'=> $this->input->post('tel'),
				'addr_1'=> $this->input->post('addr1'),
				'addr_2'=> $this->input->post('addr2'),
				'addr_city'=> $this->input->post('addrCity'),
				'addr_postcode'=> $this->input->post('addrPostcode'),
				'addr_country'=> $this->input->post('addrCountry'),
				'verified'=>$this->input->post('verify')
				);
			$this->club_model->update($id,$fields);
	
			$data['club'] =  $this->club_model->get_by_id($id);
			$data['saved'] = TRUE;
			//print_r($data);
			$this->load->view('templates/header',$data);
			$this->load->view('admin/edit_club',$data);
			$this->load->view('templates/footer');
		}
	}

	public function add_club() {

		$this->load->model('club_model');
		$this->load->helper(array('form'));
		$this->load->library('form_validation');

		$data['title'] = "Add Club";
		$data['countries'] = $this->club_model->list_countries();

		$this->form_validation->set_rules('email', 'Email', 'valid_email|trim|required|xss_clean|is_unique[clubs.email]'); 
		$this->form_validation->set_rules('name', 'Name', 'trim|required|xss_clean|min_length[2]|max_length[60]');
		$this->form_validation->set_rules('website','Website URL', 'trim|xss_clean|min_length[4]|max_length[50]|prep_url');
		$this->form_validation->set_rules('tel','Phone Number', 'trim|xss_clean|numeric|min_length[7]|max_length[20]');

		$this->form_validation->set_rules('addr1','Address Line 1', 'trim|xss_clean|min_length[2]|max_length[40]');
		$this->form_validation->set_rules('addr2','Address Line 2', 'trim|xss_clean|min_length[2]|max_length[40]');
		$this->form_validation->set_rules('addrCity','Address City', 'trim|xss_clean|min_length[2]|max_length[10]');
		$this->form_validation->set_rules('addrCountry','Address Country', 'trim|xss_clean|min_length[2]|max_length[40]');
		$this->form_validation->set_rules('addrPostcode','Address Postcode', 'trim|xss_clean|min_length[2]|max_length[10]');

		if($this->form_validation->run() == FALSE) // if the form is entered and validation fails
		{
			// show form + errors
			
		    $this->load->view('templates/header',$data);
			$this->load->view('admin/add_club',$data);
			$this->load->view('templates/footer');
		}
		else // if the form has been submitted
		{
			$fields = array(
				'email' => $this->input->post('email'),
				'name'=> $this->input->post('name'),
				'website'=> $this->input->post('website'),
				'phone'=> $this->input->post('tel'),
				'addr_1'=> $this->input->post('addr1'),
				'addr_2'=> $this->input->post('addr2'),
				'addr_city'=> $this->input->post('addrCity'),
				'addr_postcode'=> $this->input->post('addrPostcode'),
				'addr_country'=> $this->input->post('addrCountry')
				);
			$this->club_model->add($fields);
	
			//$data['club'] =  $this->club_model->get_by_id($id);
			//print_r($data);
			redirect('admin/clublist');
		}
	}

	public function clublist()
	{
		$data['title'] = "Manage Clubs";
		$this->load->view('templates/header',$data);
		$this->load->view('admin/clublist');
		$this->load->view('templates/footer');
	}

	public function ajax_user_regclub()
	{
		$user = $this->input->get('id');
		$club = $this->input->get('club');
		$this->load->model('user_model');
		$this->load->model('club_model');

		$response['status'] = $this->user_model->join_club($user,$club);
		$club_r = $this->club_model->get_by_id($club);
		$name = 'clubs['.$club_r['id'].']';
		$response['panel'] = ' <div class="panel panel-default">
                     <div class="panel-body">
                      '. $club_r['name'] .'
                       <div class="radio">
                          <label>
                            <input type="radio" name="'.$name.'" id="clubs'.$club.'?>O1" value="athlete" checked="checked">
                            Athlete
                          </label>
                        </div>
                        <div class="radio">
                          <label>
                            <input type="radio" name="'.$name.'"  id="clubs'.$club.'O2" value="coach">
                            Coach</label></div>
                        <div class="radio">
                          <label>
                            <input type="radio" name="'.$name.'"  id="clubs'.$club.'O3" value="manager" >
                            Manager</label></div>
                        <div class="radio">
                          <label>
                            <input type="radio" name="'.$name.'"  id="clubs'.$club.'O4" value="REMOVE" >
                            Remove Membership</label></div>
                    </div><!-- /.col-lg-6 -->
                  </div>';
		$this->output
		->set_content_type('application/json')
		->set_output(json_encode($response));
	}
}

/* End of file diary.php */
/* Location: ./application/controllers/diary.php */