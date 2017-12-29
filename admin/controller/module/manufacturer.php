<?php
class ControllerModuleManufacturer extends Controller {
	private $error = array();

	public function index() {
		$this->language->load('module/manufacturer');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('setting/setting');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && ($this->validate())) {
			$this->model_setting_setting->editSetting('manufacturer', $this->request->post);

			$this->session->data['success'] = $this->language->get('text_success');

			$this->redirect($this->url->link('extension/module', 'token=' . $this->session->data['token'], 'SSL'));
		}

		$this->data['heading_title'] = $this->language->get('heading_title');

		$this->data['text_enabled'] = $this->language->get('text_enabled');
		$this->data['text_disabled'] = $this->language->get('text_disabled');
		$this->data['text_content_top'] = $this->language->get('text_content_top');
		$this->data['text_content_bottom'] = $this->language->get('text_content_bottom');
		$this->data['text_column_left'] = $this->language->get('text_column_left');
		$this->data['text_column_right'] = $this->language->get('text_column_right');

		$this->data['entry_position'] = $this->language->get('entry_position');
		$this->data['entry_status'] = $this->language->get('entry_status');
		$this->data['entry_sort_order'] = $this->language->get('entry_sort_order');
                            $this->data['entry_layout'] = $this->language->get('entry_layout');

		$this->data['button_save'] = $this->language->get('button_save');
		$this->data['button_cancel'] = $this->language->get('button_cancel');
            		$this->data['button_add_module'] = $this->language->get('button_add_module');
		$this->data['button_remove'] = $this->language->get('button_remove');


 		if (isset($this->error['warning'])) {
			$this->data['error_warning'] = $this->error['warning'];
		} else {
			$this->data['error_warning'] = '';
		}

  		$this->data['breadcrumbs'] = array();

   		$this->data['breadcrumbs'][] = array(
       		'href'      => HTTPS_SERVER . 'index.php?route=common/home&token=' . $this->session->data['token'],
       		'text'      => $this->language->get('text_home'),
      		'separator' => FALSE
   		);

   		$this->data['breadcrumbs'][] = array(
       		'href'      => HTTPS_SERVER . 'index.php?route=extension/module&token=' . $this->session->data['token'],
       		'text'      => $this->language->get('text_module'),
      		'separator' => ' :: '
   		);

   		$this->data['breadcrumbs'][] = array(
       		'href'      => HTTPS_SERVER . 'index.php?route=module/manufacturer&token=' . $this->session->data['token'],
       		'text'      => $this->language->get('heading_title'),
      		'separator' => ' :: '
   		);

                            $this->data['modules'] = array();

		if (isset($this->request->post['manufacturer_module'])) {
			$this->data['modules'] = $this->request->post['manufacturer_module'];
		} elseif ($this->config->get('manufacturer_module')) {
			$this->data['modules'] = $this->config->get('manufacturer_module');
		}

                            $this->data['action'] = $this->url->link('module/manufacturer', 'token=' . $this->session->data['token'], 'SSL');

		$this->data['cancel'] = $this->url->link('extension/module', 'token=' . $this->session->data['token'], 'SSL');

		if (isset($this->request->post['manufacturer_position'])) {
			$this->data['manufacturer_position'] = $this->request->post['manufacturer_position'];
		} else {
			$this->data['manufacturer_position'] = $this->config->get('manufacturer_position');
		}

		if (isset($this->request->post['manufacturer_status'])) {
			$this->data['manufacturer_status'] = $this->request->post['manufacturer_status'];
		} else {
			$this->data['manufacturer_status'] = $this->config->get('manufacturer_status');
		}

		if (isset($this->request->post['manufacturer_sort_order'])) {
			$this->data['manufacturer_sort_order'] = $this->request->post['manufacturer_sort_order'];
		} else {
			$this->data['manufacturer_sort_order'] = $this->config->get('manufacturer_sort_order');
		}

                            $this->load->model('design/layout');

		$this->data['layouts'] = $this->model_design_layout->getLayouts();

		$this->template = 'module/manufacturer.tpl';
		$this->children = array(
			'common/header',
			'common/footer'
		);

		$this->response->setOutput($this->render(TRUE));
	}

	private function validate() {
		if (!$this->user->hasPermission('modify', 'module/manufacturer')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		if (!$this->error) {
			return TRUE;
		} else {
			return FALSE;
		}
	}
}
?>