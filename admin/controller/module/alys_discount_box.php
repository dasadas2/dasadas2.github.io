<?php
class ControllerModuleAlysDiscountBox extends Controller {
	private $error = array(); 
	
	public function index() {   
		$this->load->language('module/alys_discount_box');

		$this->document->setTitle($this->language->get('heading_title'));
		
		$this->load->model('setting/setting');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
			$this->model_setting_setting->editSetting('alys_discount_box', $this->request->post);		
			
			$this->cache->delete('product');
			
			$this->session->data['success'] = $this->language->get('text_success');

			$this->redirect($this->url->link('extension/module', 'token=' . $this->session->data['token'], 'SSL'));
		}
				
		$this->data['heading_title']     = $this->language->get('heading_title');

		$this->data['text_none']         = $this->language->get('text_none');
		$this->data['text_yes']          = $this->language->get('text_yes');
		$this->data['text_no']           = $this->language->get('text_no');
		$this->data['text_select']       = $this->language->get('text_select');
		$this->data['text_enabled']      = $this->language->get('text_enabled');
		$this->data['text_disabled']     = $this->language->get('text_disabled');

		$this->data['entry_status_enter'] = $this->language->get('entry_status_enter');
		$this->data['entry_status_time']  = $this->language->get('entry_status_time');
		$this->data['entry_time_delay']   = $this->language->get('entry_time_delay');

		$this->data['entry_text_enter']  = $this->language->get('entry_text_enter');
		$this->data['entry_title']       = $this->language->get('entry_title');
		$this->data['entry_text']        = $this->language->get('entry_text');
		$this->data['entry_button']      = $this->language->get('entry_button');


		$this->data['button_save']       = $this->language->get('button_save');
		$this->data['button_cancel']     = $this->language->get('button_cancel');

		$this->data['token']             = $this->session->data['token'];

 		if (isset($this->error['warning'])) {
			$this->data['error_warning'] = $this->error['warning'];
		} else {
			$this->data['error_warning'] = '';
		}
		

  		$this->data['breadcrumbs'] = array();

   		$this->data['breadcrumbs'][] = array(
       		'text'      => $this->language->get('text_home'),
			'href'      => $this->url->link('common/home', 'token=' . $this->session->data['token'], 'SSL'),
      		'separator' => false
   		);

   		$this->data['breadcrumbs'][] = array(
       		'text'      => $this->language->get('text_module'),
			'href'      => $this->url->link('extension/module', 'token=' . $this->session->data['token'], 'SSL'),
      		'separator' => ' :: '
   		);
		
   		$this->data['breadcrumbs'][] = array(
       		'text'      => $this->language->get('heading_title'),
			'href'      => $this->url->link('module/alys_discount_box', 'token=' . $this->session->data['token'], 'SSL'),
      		'separator' => ' :: '
   		);


		if (isset($this->request->post['alys_discount_box_status_enter'])) {
			$this->data['alys_discount_box_status_enter'] = $this->request->post['alys_discount_box_status_enter'];
		} else {
			$this->data['alys_discount_box_status_enter'] = $this->config->get('alys_discount_box_status_enter');
		}

		if (isset($this->request->post['alys_discount_box_status_time'])) {
			$this->data['alys_discount_box_status_time'] = $this->request->post['alys_discount_box_status_time'];
		} else {
			$this->data['alys_discount_box_status_time'] = $this->config->get('alys_discount_box_status_time');
		}

		if (isset($this->request->post['alys_discount_box_title_enter'])) {
			$this->data['alys_discount_box_title_enter'] = $this->request->post['alys_discount_box_title_enter'];
		} else {
			$this->data['alys_discount_box_title_enter'] = $this->config->get('alys_discount_box_title_enter');
		}

		if (isset($this->request->post['alys_discount_box_title'])) {
			$this->data['alys_discount_box_title'] = $this->request->post['alys_discount_box_title'];
		} else {
			$this->data['alys_discount_box_title'] = $this->config->get('alys_discount_box_title');
		}

		if (isset($this->request->post['alys_discount_box_mail'])) {
			$this->data['alys_discount_box_text'] = $this->request->post['alys_discount_box_text'];
		} else {
			$this->data['alys_discount_box_text'] = $this->config->get('alys_discount_box_text');
		}

		if (isset($this->request->post['alys_discount_box_button'])) {
			$this->data['alys_discount_box_button'] = $this->request->post['alys_discount_box_button'];
		} else {
			$this->data['alys_discount_box_button'] = $this->config->get('alys_discount_box_button');
		}

		if (isset($this->request->post['alys_discount_box_time_delay'])) {
			$this->data['alys_discount_box_time_delay'] = $this->request->post['alys_discount_box_time_delay'];
		} else {
			$this->data['alys_discount_box_time_delay'] = $this->config->get('alys_discount_box_time_delay');
		}

		$this->data['action'] = $this->url->link('module/alys_discount_box', 'token=' . $this->session->data['token'], 'SSL');

		$this->data['cancel'] = $this->url->link('extension/module', 'token=' . $this->session->data['token'], 'SSL');


		$this->template = 'module/alys_discount_box.tpl';
		$this->children = array(
			'common/header',
			'common/footer'
		);
				
		$this->response->setOutput($this->render());
	}
	
	private function validate() {
		if (!$this->user->hasPermission('modify', 'module/alys_discount_box')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		if (!$this->error) {
			return true;
		} else {
			return false;
		}
	}

}
?>