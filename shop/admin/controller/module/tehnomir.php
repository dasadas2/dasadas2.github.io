<?php
class ControllerModuleTehnomir extends Controller {
	private $error = array(); 
	
	public function index() {   
		$this->load->language('module/tehnomir');

		$this->document->setTitle($this->language->get('heading_title'));
		
		$this->load->model('setting/setting');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
			$this->model_setting_setting->editSetting('tehnomir', $this->request->post);		
			
			$this->cache->delete('product');
			
			$this->session->data['success'] = $this->language->get('text_success');

			$this->redirect($this->url->link('extension/module', 'token=' . $this->session->data['token'], 'SSL'));
		}
				
		$this->data['heading_title'] = $this->language->get('heading_title');

		$this->data['text_none'] = $this->language->get('text_none');
		$this->data['text_yes'] = $this->language->get('text_yes');
		$this->data['text_no'] = $this->language->get('text_no');
		$this->data['text_select'] = $this->language->get('text_select');

		$this->data['text_enabled'] = $this->language->get('text_enabled');
		$this->data['text_disabled'] = $this->language->get('text_disabled');
		$this->data['text_content_top'] = $this->language->get('text_content_top');
		$this->data['text_content_bottom'] = $this->language->get('text_content_bottom');		
		$this->data['text_column_left'] = $this->language->get('text_column_left');
		$this->data['text_column_right'] = $this->language->get('text_column_right');

		$this->data['text_info'] = $this->language->get('text_info');

		$this->data['entry_status'] = $this->language->get('entry_status');
		$this->data['entry_username'] = $this->language->get('entry_username');
		$this->data['entry_password'] = $this->language->get('entry_password');

		$this->data['button_save'] = $this->language->get('button_save');
		$this->data['button_cancel'] = $this->language->get('button_cancel');
		$this->data['button_add_module'] = $this->language->get('button_add_module');
		$this->data['button_remove'] = $this->language->get('button_remove');

		$this->data['token'] = $this->session->data['token'];

 		if (isset($this->error['warning'])) {
			$this->data['error_warning'] = $this->error['warning'];
		} else {
			$this->data['error_warning'] = '';
		}
		
		if (isset($this->error['username'])) {
    		$this->data['error_username'] = $this->error['username'];
		} else {
			$this->data['error_username'] = '';
		}

		if (isset($this->error['password'])) {
			$this->data['error_password'] = $this->error['password'];
		} else {
			$this->data['error_password'] = '';
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
			'href'      => $this->url->link('module/tehnomir', 'token=' . $this->session->data['token'], 'SSL'),
      		'separator' => ' :: '
   		);

		if (isset($this->request->post['tehnomir_status'])) {
			$this->data['tehnomir_status'] = $this->request->post['tehnomir_status'];
		} else {
			$this->data['tehnomir_status'] = $this->config->get('tehnomir_status');
		}

    	if (isset($this->request->post['tehnomir_username'])) {
      		$this->data['tehnomir_username'] = $this->request->post['tehnomir_username'];
		} else {
      		$this->data['tehnomir_username'] = $this->config->get('tehnomir_username');
    	}

    	if (isset($this->request->post['tehnomir_password'])) {
      		$this->data['tehnomir_password'] = $this->request->post['tehnomir_password'];
		} else {
      		$this->data['tehnomir_password'] = $this->config->get('tehnomir_password');
    	}

		$this->data['action'] = $this->url->link('module/tehnomir', 'token=' . $this->session->data['token'], 'SSL');
		$this->data['cancel'] = $this->url->link('extension/module', 'token=' . $this->session->data['token'], 'SSL');


		$this->template = 'module/tehnomir.tpl';
		$this->children = array(
			'common/header',
			'common/footer'
		);
				
		$this->response->setOutput($this->render());
	}
	
	private function validate() {
		if (!$this->user->hasPermission('modify', 'module/tehnomir')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

    	if (empty($this->request->post['tehnomir_username'])) {
      		$this->error['username'] = $this->language->get('error_username');
    	}

    	if (empty($this->request->post['tehnomir_password'])) {
      		$this->error['password'] = $this->language->get('error_password');
    	}


		if (!$this->error) {
			return true;
		} else {
			return false;
		}
	}


}
?>