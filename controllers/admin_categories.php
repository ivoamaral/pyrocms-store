<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * This is a store module for PyroCMS
 *
 * @author 		Jaap Jolman And Kevin Meier - pyrocms-store Team
 * @website		http://jolman.eu
 * @package 	PyroCMS
 * @subpackage 	Store Module
**/
class Admin_categories extends Admin_Controller
{
	protected $section = 'categories';

	public function __construct()
	{
		parent::__construct();

		// Load all the required classes
		$this->load->model('categories_m');
		$this->load->library('form_validation');
		$this->load->library('store_settings');
		$this->load->language('store');
		$this->load->helper('date');
		
		// We'll set the partials and metadata here since they're used everywhere
		$this->template->set_partial('shortcuts', 'admin/partials/shortcuts')
						->append_metadata(js('admin.js', 'store'))
						->append_metadata(css('admin.css', 'store'));
	}
	
	public function index()
	{
		$id = $this->store_settings->item('store_id');
		$categories = $this->categories_m->get_all();

		$this->data = array(
			'categories'	=>	$categories
		);
		
		$this->template->build('admin/list_categories', $this->data);
	}

	public function add()
	{
		$id = $this->store_settings->item('store_id');

		if ( ! $this->form_validation->run('add_category') )
		{
			//if($id){$this->data->parent_id = $id;}else{$this->data->parent_id = '';}
			$this->data->categories = $this->categories_m->make_categories_dropdown(0);
			
			$this->template
				->append_metadata($this->load->view('fragments/wysiwyg', $this->data, TRUE))
				->build('admin/add_category', $this->data);	
		}
		else
		{
			if ( $this->categories_m->add_category() )
			{
				$this->session->set_flashdata('success', sprintf(lang('store_cat_add_success'), $this->input->post('name')));
				redirect('admin/store/categories');
			}
			else
			{
				$this->session->set_flashdata(array('error'=> lang('store_cat_add_error')));
			}
		}
	}// end add()
	
	
	/**
	 * Edit a category, specified by an ID.
	 *
	 * @param integer $categories_id The row's ID
	 * @return none
	 * @author Rudolph Arthur Hernandez
	 */
	public function edit($categories_id)
	{
		$id = $this->store_settings->item('store_id');

		if ( ! $this->form_validation->run('add_category') )
		{
			$this->data->categories = $this->categories_m->make_categories_dropdown($categories_id);
			$this->data->category = $this->categories_m->get($categories_id);			
			
			$this->template
				->append_metadata($this->load->view('fragments/wysiwyg', $this->data, TRUE))
				->build('admin/edit_category', $this->data);	
		}
		else
		{
			if ( $this->store_m->update_category($categories_id) )
			{
				$this->session->set_flashdata('success', sprintf(lang('store_cat_add_success'), $this->input->post('name')));
				redirect('admin/store/categories');
			}
			else
			{
				$this->session->set_flashdata(array('error'=> lang('store_cat_add_error')));
			}
		}
	}// end edit()
	
	public function delete($categories_id){
		$this->categories_m->delete($categories_id);		
		redirect('admin/store/categories');
		}
}
