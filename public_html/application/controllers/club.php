<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Club extends Secure_Controller {

	function __construct()
	{
	   parent::__construct();
	   $this->load->model('club_model');
	   $this->load->model('user_model');
	}

	public function index()
	{
		// club finder index page
		$data['title'] = "Rowing Clubs on iRow";
		$data['clubs'] = $this->club_model->get_all();
		$this->load->view('templates/header',$data);
		$this->load->view('club/tempindex');
		$this->load->view('templates/footer');
	}

	public function profile($ref) {
		// displays overview of club
		$data['title'] = "My Club";
		$data['club'] = $this->club_model->get_by_ref($ref);
		$club_id = $data['club']['id'];
		$me = $this->l_auth->current_user_id();
		$data['membership'] = $this->user_model->membership($me,$data['club']['id']);
		$data['coaches'] = $this->club_model->get_coaches($club_id);
		$data['managers'] = $this->club_model->get_managers($club_id);
		if($data['club']) {
			$this->load->view('templates/header',$data);
			$this->load->view('club/view',$data);
			$this->load->view('templates/footer');
		}
	}

	public function ajax_chmembership() {
		// javascript interface to toggle current users membership
		$club_ref = $this->input->get('club');
		$club_ob = $this->club_model->get_by_ref($club_ref);
		$club = $club_ob['id'];
		$me=  $this->l_auth->current_user_id();
		$current = $this->user_model->membership($me,$club);
		if($current==FALSE) {
			// make member
			$this->user_model->join_club($me,$club);
			echo "Joined";
		} else {
			// leave club
			$this->user_model->leave_club($me,$club);
			echo "Not Joined";
		}
	}

	public function ajax_getmembers() {
		// javascript interface to list all members names of a clubb
		$club_ref = $this->input->get('club');
		$club_ob = $this->club_model->get_by_ref($club_ref);
		$club = $club_ob['id'];
		$members = $this->club_model->get_members($club);

		$this->output
		->set_content_type('application/json')
		->set_output(json_encode($members));
	}

	public function manage($ref) {
		// function which lets users who are managers of a club edit it
		$this->load->helper(array('form'));
		$this->load->library('form_validation');
		$data['title'] = "Manage Club";
		$data['club'] = $this->club_model->get_by_ref($ref);
		$club_id = $data['club']['id'];
		
		$data['coaches'] = $this->club_model->get_coaches($club_id);
		
		$data['managers'] = $this->club_model->get_managers($club_id);

		$data['members'] = $this->club_model->get_members($club_id);
		
		$me = $this->l_auth->current_user_id();
		if(!$this->club_model->is_manager($club_id,$me)) {
			return FALSE; // if they're not a manager halt page
		}

		$data['countries'] = $this->club_model->list_countries(); // get countries for drop down
		
		if($this->input->post('email') != $data['club']['email']) {
			$this->form_validation->set_rules('email', 'Email', 'valid_email|trim|required|xss_clean|is_unique[clubs.email]'); // checks email is valid,entered,trims blank space,remove XSS script,and checks it's unique
		}
		$this->form_validation->set_rules('name', 'Name', 'trim|required|xss_clean|min_length[2]|max_length[60]');
		$this->form_validation->set_rules('website','Website URL', 'trim|xss_clean|min_length[4]|max_length[50]|prep_url');
		$this->form_validation->set_rules('tel','Phone Number', 'trim|xss_clean|numeric|min_length[7]|max_length[20]');

		$this->form_validation->set_rules('addr_1','Address Line 1', 'trim|xss_clean|min_length[2]|max_length[40]');
		$this->form_validation->set_rules('addr_2','Address Line 2', 'trim|xss_clean|min_length[2]|max_length[40]');
		$this->form_validation->set_rules('addr_city','Address City', 'trim|xss_clean|min_length[2]|max_length[10]');
		$this->form_validation->set_rules('addr_country','Address Country', 'trim|xss_clean');
		$this->form_validation->set_rules('addr_postcode','Address Postcode', 'trim|xss_clean|min_length[2]|max_length[10]');
	 
		if($this->form_validation->run() == FALSE) // if the form is entered and validation fails
		{
			// show form + errors
			
		    $this->load->view('templates/header',$data);
			$this->load->view('club/edit',$data);
			$this->load->view('templates/footer');
		}
		else // if the form has been submitted
		{
			$fields = array(
				'email' => $this->input->post('email'),
				'name'=> $this->input->post('name'),
				'website'=> $this->input->post('website'),
				'phone'=> $this->input->post('tel'),
				'addr_1'=> $this->input->post('addr_1'),
				'addr_2'=> $this->input->post('addr_2'),
				'addr_city'=> $this->input->post('addr_city'),
				'addr_postcode'=> $this->input->post('addr_postcode'),
				'addr_country'=> $this->input->post('addr_country')
				);
			$this->club_model->update($club_id,$fields); // update the club record
	
			// add new coaches and managers, removing all members before readding/updating them
			$this->club_model->reset_all_memberships($club_id);

			$coaches = $this->input->post('coaches');
			if(!empty($coaches)) {
				foreach($coaches as $coach) { // add any new coaches
					$this->user_model->set_membership($coach,$club_id,'coach');
				}
			}

			$managers = $this->input->post('managers');
			if(!empty($managers)) {
			
				foreach($managers as $manager) { // add any new managers
					$this->user_model->set_membership($manager,$club_id,'manager');
				}
			}

			$data['club'] =  $this->club_model->get_by_id($club_id); // get back values out of database
			$data['coaches'] = $this->club_model->get_coaches($club_id);
			$data['managers'] = $this->club_model->get_managers($club_id);
			$data['members'] = $this->club_model->get_members($club_id);
			$data['saved'] = TRUE;
			//print_r($data);
			$this->load->view('templates/header',$data);
			$this->load->view('club/edit',$data); // redisplay form
			$this->load->view('templates/footer');
		}
	}

}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */