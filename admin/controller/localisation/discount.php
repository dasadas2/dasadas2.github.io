<?php
class ControllerLocalisationDiscount extends Controller {
	private $error = array(); 

	public function index() {
		$this->language->load('localisation/discount');

		$this->document->setTitle($this->language->get('heading_title'));
		
		$this->load->model('localisation/discount');
		
		$this->getList();
	}

	public function insert() {
		$this->language->load('localisation/discount');

		$this->document->setTitle($this->language->get('heading_title'));
		
		$this->load->model('localisation/discount');
		
		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
			$this->model_localisation_discount->addDiscount($this->request->post);
	
			$this->session->data['success'] = $this->language->get('text_success');
			
			$url = '';
			
			if (isset($this->request->get['sort'])) {
				$url .= '&sort=' . $this->request->get['sort'];
			}

			if (isset($this->request->get['order'])) {
				$url .= '&order=' . $this->request->get['order'];
			}

			if (isset($this->request->get['page'])) {
				$url .= '&page=' . $this->request->get['page'];
			}
			
			$this->redirect($this->url->link('localisation/discount', 'token=' . $this->session->data['token'] . $url, 'SSL'));
		}

		$this->getForm();
	}

	public function update() {
		$this->language->load('localisation/discount');

		$this->document->setTitle($this->language->get('heading_title'));
		
		$this->load->model('localisation/discount');
		
		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
			$this->model_localisation_discount->editDiscount($this->request->get['discount_id'], $this->request->post);			
			
			$this->session->data['success'] = $this->language->get('text_success');
			
			$url = '';
			
			if (isset($this->request->get['sort'])) {
				$url .= '&sort=' . $this->request->get['sort'];
			}

			if (isset($this->request->get['order'])) {
				$url .= '&order=' . $this->request->get['order'];
			}

			if (isset($this->request->get['page'])) {
				$url .= '&page=' . $this->request->get['page'];
			}
			
			$this->redirect($this->url->link('localisation/discount', 'token=' . $this->session->data['token'] . $url, 'SSL'));
		}

		$this->getForm();
	}

	public function delete() {
		$this->language->load('localisation/discount');

		$this->document->setTitle($this->language->get('heading_title'));
		
		$this->load->model('localisation/discount');
		
		if (isset($this->request->post['selected']) && $this->validateDelete()) {
			foreach ($this->request->post['selected'] as $discount_id) {
				$this->model_localisation_discount->deleteDiscount($discount_id);
			}			
			
			$this->session->data['success'] = $this->language->get('text_success');
			
			$url = '';
			
			if (isset($this->request->get['sort'])) {
				$url .= '&sort=' . $this->request->get['sort'];
			}

			if (isset($this->request->get['order'])) {
				$url .= '&order=' . $this->request->get['order'];
			}

			if (isset($this->request->get['page'])) {
				$url .= '&page=' . $this->request->get['page'];
			}

			$this->redirect($this->url->link('localisation/discount', 'token=' . $this->session->data['token'] . $url, 'SSL'));
		}

		$this->getList();
	}

	protected function getList() {
		if (isset($this->request->get['sort'])) {
			$sort = $this->request->get['sort'];
		} else {
			$sort = 'sort_order';
		}
		
		if (isset($this->request->get['order'])) {
			$order = $this->request->get['order'];
		} else {
			$order = 'ASC';
		}
		
		if (isset($this->request->get['page'])) {
			$page = $this->request->get['page'];
		} else {
			$page = 1;
		}
				
		$url = '';
			
		if (isset($this->request->get['sort'])) {
			$url .= '&sort=' . $this->request->get['sort'];
		}

		if (isset($this->request->get['order'])) {
			$url .= '&order=' . $this->request->get['order'];
		}
		
		if (isset($this->request->get['page'])) {
			$url .= '&page=' . $this->request->get['page'];
		}

  		$this->data['breadcrumbs'] = array();

   		$this->data['breadcrumbs'][] = array(
       		'text'      => $this->language->get('text_home'),
			'href'      => $this->url->link('common/home', 'token=' . $this->session->data['token'], 'SSL'),
      		'separator' => false
   		);

   		$this->data['breadcrumbs'][] = array(
       		'text'      => $this->language->get('heading_title'),
			'href'      => $this->url->link('localisation/discount', 'token=' . $this->session->data['token'] . $url, 'SSL'),
      		'separator' => ' :: '
   		);
							
		$this->data['insert'] = $this->url->link('localisation/discount/insert', 'token=' . $this->session->data['token'] . $url, 'SSL');
		$this->data['delete'] = $this->url->link('localisation/discount/delete', 'token=' . $this->session->data['token'] . $url, 'SSL');

		$this->data['discounts'] = array();

		$discount_total = $this->model_localisation_discount->getTotalDiscounts();

		$results = $this->model_localisation_discount->getDiscounts();

		foreach ($results as $result) {
			$action = array();
			
			$action[] = array(
				'text' => $this->language->get('text_edit'),
				'href' => $this->url->link('localisation/discount/update', 'token=' . $this->session->data['token'] . '&discount_id=' . $result['discount_id'] . $url, 'SSL')
			);
					
			$this->data['discounts'][] = array(
				'discount_id'   => $result['discount_id'],
				'min_price'     => $result['min_price'],
				'max_price'     => $result['max_price'],
				'percent'       => $result['percent'],
				'sort_order'    => $result['sort_order'],
				'selected'      => isset($this->request->post['selected']) && in_array($result['discount_id'], $this->request->post['selected']),
				'action'        => $action
			);
		}
	
		$this->data['heading_title'] = $this->language->get('heading_title');

		$this->data['text_no_results'] = $this->language->get('text_no_results');
    	$this->data['text_intro'] = $this->language->get('text_intro');

		$this->data['column_sort_order'] = $this->language->get('column_sort_order');
		$this->data['column_min_price'] = $this->language->get('column_min_price');
		$this->data['column_max_price'] = $this->language->get('column_max_price');
		$this->data['column_percent'] = $this->language->get('column_percent');
		$this->data['column_action'] = $this->language->get('column_action');

		$this->data['button_insert'] = $this->language->get('button_insert');
		$this->data['button_delete'] = $this->language->get('button_delete');
 
 		if (isset($this->error['warning'])) {
			$this->data['error_warning'] = $this->error['warning'];
		} else {
			$this->data['error_warning'] = '';
		}

		if (isset($this->session->data['success'])) {
			$this->data['success'] = $this->session->data['success'];
		
			unset($this->session->data['success']);
		} else {
			$this->data['success'] = '';
		}
 
		$url = '';

		if ($order == 'ASC') {
			$url .= '&order=DESC';
		} else {
			$url .= '&order=ASC';
		}

		if (isset($this->request->get['page'])) {
			$url .= '&page=' . $this->request->get['page'];
		}
		 
		$this->data['sort_sort_order'] = $this->url->link('localisation/discount', 'token=' . $this->session->data['token'] . '&sort=d.sort_order' . $url, 'SSL');
		$this->data['sort_min_price'] = $this->url->link('localisation/discount', 'token=' . $this->session->data['token'] . '&sort=d.min_price' . $url, 'SSL');
		$this->data['sort_max_price'] = $this->url->link('localisation/discount', 'token=' . $this->session->data['token'] . '&sort=d.max_price' . $url, 'SSL');
		$this->data['sort_percent'] = $this->url->link('localisation/discount', 'token=' . $this->session->data['token'] . '&sort=d.percent' . $url, 'SSL');

		$url = '';

		if (isset($this->request->get['sort'])) {
			$url .= '&sort=' . $this->request->get['sort'];
		}
												
		if (isset($this->request->get['order'])) {
			$url .= '&order=' . $this->request->get['order'];
		}

		$pagination = new Pagination();
		$pagination->total = $discount_total;
		$pagination->page = $page;
		$pagination->limit = $this->config->get('config_admin_limit');
		$pagination->text = $this->language->get('text_pagination');
		$pagination->url = $this->url->link('localisation/discount', 'token=' . $this->session->data['token'] . $url . '&page={page}', 'SSL');

		$this->data['pagination'] = $pagination->render();
		
		$this->data['sort'] = $sort;
		$this->data['order'] = $order;

		$this->template = 'localisation/discount_list.tpl';
		$this->children = array(
			'common/header',
			'common/footer'
		);
				
		$this->response->setOutput($this->render());
	}

	protected function getForm() {
		$this->data['heading_title'] = $this->language->get('heading_title');

		$this->data['entry_sort_order'] = $this->language->get('entry_sort_order');
		$this->data['entry_min_price'] = $this->language->get('entry_min_price');
		$this->data['entry_max_price'] = $this->language->get('entry_max_price');
		$this->data['entry_percent'] = $this->language->get('entry_percent');

		$this->data['text_enabled'] = $this->language->get('text_enabled');
		$this->data['text_disabled'] = $this->language->get('text_disabled');
		
		$this->data['button_save'] = $this->language->get('button_save');
		$this->data['button_cancel'] = $this->language->get('button_cancel');

 		if (isset($this->error['warning'])) {
			$this->data['error_warning'] = $this->error['warning'];
		} else {
			$this->data['error_warning'] = '';
		}
		
		$url = '';
			
		if (isset($this->request->get['sort'])) {
			$url .= '&sort=' . $this->request->get['sort'];
		}

		if (isset($this->request->get['order'])) {
			$url .= '&order=' . $this->request->get['order'];
		}
		
		if (isset($this->request->get['page'])) {
			$url .= '&page=' . $this->request->get['page'];
		}

  		$this->data['breadcrumbs'] = array();

   		$this->data['breadcrumbs'][] = array(
       		'text'      => $this->language->get('text_home'),
			'href'      => $this->url->link('common/home', 'token=' . $this->session->data['token'], 'SSL'),  		
      		'separator' => false
   		);

   		$this->data['breadcrumbs'][] = array(
       		'text'      => $this->language->get('heading_title'),
			'href'      => $this->url->link('localisation/discount', 'token=' . $this->session->data['token'] . $url, 'SSL'),
      		'separator' => ' :: '
   		);
							
		if (!isset($this->request->get['discount_id'])) {
			$this->data['action'] = $this->url->link('localisation/discount/insert', 'token=' . $this->session->data['token'] . $url, 'SSL');
		} else {
			$this->data['action'] = $this->url->link('localisation/discount/update', 'token=' . $this->session->data['token'] . '&discount_id=' . $this->request->get['discount_id'] . $url, 'SSL');
		}
		 
		$this->data['cancel'] = $this->url->link('localisation/discount', 'token=' . $this->session->data['token'] . $url, 'SSL');

		if (isset($this->request->get['discount_id']) && ($this->request->server['REQUEST_METHOD'] != 'POST')) {
			$discount_info = $this->model_localisation_discount->getDiscount($this->request->get['discount_id']);
		}

		if (isset($this->request->post['sort_order'])) {
			$this->data['sort_order'] = $this->request->post['sort_order'];
		} elseif (!empty($discount_info)) {
			$this->data['sort_order'] = $discount_info['sort_order'];
		} else {
			$this->data['sort_order'] = '1';
		}
		
		if (isset($this->request->post['min_price'])) {
			$this->data['min_price'] = $this->request->post['min_price'];
		} elseif (!empty($discount_info)) {
			$this->data['min_price'] = $discount_info['min_price'];
		} else {
			$this->data['min_price'] = '0';
		}

		if (isset($this->request->post['max_price'])) {
			$this->data['max_price'] = $this->request->post['max_price'];
		} elseif (!empty($discount_info)) {
			$this->data['max_price'] = $discount_info['max_price'];
		} else {
			$this->data['max_price'] = '0';
		}

		if (isset($this->request->post['percent'])) {
			$this->data['percent'] = $this->request->post['percent'];
		} elseif (!empty($discount_info)) {
			$this->data['percent'] = $discount_info['percent'];
		} else {
			$this->data['percent'] = '0.00';
		}

		$this->template = 'localisation/discount_form.tpl';
		$this->children = array(
			'common/header',
			'common/footer'
		);
				
		$this->response->setOutput($this->render());
	}

	protected function validateForm() {
		if (!$this->user->hasPermission('modify', 'localisation/discount')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		//if ((utf8_strlen($this->request->post['name']) < 3) || (utf8_strlen($this->request->post['name']) > 64)) {
		//	$this->error['name'] = $this->language->get('error_name');
		//}

		if (!$this->error) {
			return true;
		} else {
			return false;
		}
	}

	protected function validateDelete() {
		if (!$this->user->hasPermission('modify', 'localisation/discount')) {
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