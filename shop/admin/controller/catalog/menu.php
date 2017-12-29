<?php
class ControllerCatalogMenu extends Controller {
	private $error = array();
	private $menu_id = 0;
	private $path = array();

  	public function index() {
		$this->load->language('catalog/menu');
		
		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('catalog/menu');

    	$this->getList();
  	}
    
  	public function insert() {
		$this->load->language('catalog/menu');

    	$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('catalog/menu');
			
		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {

            if ($this->request->post['type'] == 1) {
                $this->request->post['href'] = $this->request->post['cat_href'];
            } elseif ($this->request->post['type'] == 2) {
                $this->request->post['href'] = $this->request->post['man_href'];
            } elseif ($this->request->post['type'] == 3) {
                $this->request->post['href'] = $this->request->post['info_href'];
            }

			$this->model_catalog_menu->addMenu($this->request->post);

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
			
			$this->redirect($this->url->link('catalog/menu', 'token=' . $this->session->data['token'] . $url, 'SSL'));
		}
    
    	$this->getForm();
  	} 
   
  	public function update() {
		$this->load->language('catalog/menu');

    	$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('catalog/menu');
		
    	if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {

            if ($this->request->post['type'] == 1) {
                $this->request->post['href'] = $this->request->post['cat_href'];
            } elseif ($this->request->post['type'] == 2) {
                $this->request->post['href'] = $this->request->post['man_href'];
            } elseif ($this->request->post['type'] == 3) {
                $this->request->post['href'] = $this->request->post['info_href'];
            }

            if (!isset($this->request->post['subcategory'])) {
                $this->request->post['subcategory'] = '0';
            }

			$this->model_catalog_menu->editMenu($this->request->get['menu_id'], $this->request->post);

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
			
			$this->redirect($this->url->link('catalog/menu', 'token=' . $this->session->data['token'] . $url, 'SSL'));
		}
    
    	$this->getForm();
  	}   

  	public function delete() {
		$this->load->language('catalog/menu');

    	$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('catalog/menu');
			
    	if (isset($this->request->post['selected']) && $this->validateDelete()) {
			foreach ($this->request->post['selected'] as $menu_id) {
				$this->model_catalog_menu->deleteMenu($menu_id);
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
			
			$this->redirect($this->url->link('catalog/menu', 'token=' . $this->session->data['token'] . $url, 'SSL'));
    	}
	
    	$this->getList();
  	}  
    
  	private function getList() {

        $this->model_catalog_menu->makeInstall();

		if (isset($this->request->get['sort'])) {
			$sort = $this->request->get['sort'];
		} else {
			$sort = 'name';
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
			'href'      => $this->url->link('catalog/menu', 'token=' . $this->session->data['token'] . $url, 'SSL'),
      		'separator' => ' :: '
   		);
							
		$this->data['insert'] = $this->url->link('catalog/menu/insert', 'token=' . $this->session->data['token'] . $url, 'SSL');
		$this->data['delete'] = $this->url->link('catalog/menu/delete', 'token=' . $this->session->data['token'] . $url, 'SSL');

		if (isset($this->request->get['path'])) {
			if ($this->request->get['path'] != '') {
				$this->path = explode('_', $this->request->get['path']);
				$this->menu_id = end($this->path);
				$this->session->data['path'] = $this->request->get['path'];
			} else {
				unset($this->session->data['path']);
			}
		} elseif (isset($this->session->data['path'])) {
			$this->path = explode('_', $this->session->data['path']);
			$this->menu_id = end($this->path);
		}

		$this->data['menus'] = $this->getMenus(0);

		$this->data['heading_title'] = $this->language->get('heading_title');

		$this->data['text_no_results'] = $this->language->get('text_no_results');

		$this->data['column_name'] = $this->language->get('column_name');
		$this->data['column_href'] = $this->language->get('column_href');
		$this->data['column_sort_order'] = $this->language->get('column_sort_order');
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

		$this->template = 'catalog/menu_list.tpl';
		$this->children = array(
			'common/header',
			'common/footer'
		);

		$this->response->setOutput($this->render());
	}

  	private function getForm() {
    	$this->data['heading_title'] = $this->language->get('heading_title');

    	$this->data['text_enabled'] = $this->language->get('text_enabled');
    	$this->data['text_disabled'] = $this->language->get('text_disabled');
		$this->data['text_none'] = $this->language->get('text_none');
    	$this->data['text_yes'] = $this->language->get('text_yes');
    	$this->data['text_no'] = $this->language->get('text_no');
		$this->data['text_default'] = $this->language->get('text_default');

		$this->data['entry_parent'] = $this->language->get('entry_parent');
		$this->data['entry_name'] = $this->language->get('entry_name');
		$this->data['entry_href'] = $this->language->get('entry_href');
		$this->data['entry_class'] = $this->language->get('entry_class');
		$this->data['entry_top'] = $this->language->get('entry_top');
		$this->data['entry_subcategory'] = $this->language->get('entry_subcategory');
		$this->data['entry_column'] = $this->language->get('entry_column');
		$this->data['entry_sort_order'] = $this->language->get('entry_sort_order');
		$this->data['entry_status'] = $this->language->get('entry_status');

		$this->data['entry_type']   = $this->language->get('entry_type');
		$this->data['entry_type_0'] = $this->language->get('entry_type_0');
		$this->data['entry_type_1'] = $this->language->get('entry_type_1');
		$this->data['entry_type_2'] = $this->language->get('entry_type_2');
		$this->data['entry_type_3'] = $this->language->get('entry_type_3');
		$this->data['entry_type_4'] = $this->language->get('entry_type_4');
		$this->data['entry_type_5'] = $this->language->get('entry_type_5');
		$this->data['entry_type_11'] = $this->language->get('entry_type_11');
		$this->data['entry_type_12'] = $this->language->get('entry_type_12');

    	$this->data['button_save'] = $this->language->get('button_save');
    	$this->data['button_cancel'] = $this->language->get('button_cancel');

    	$this->data['tab_general'] = $this->language->get('tab_general');
    	$this->data['tab_data'] = $this->language->get('tab_data');

 		if (isset($this->error['warning'])) {
			$this->data['error_warning'] = $this->error['warning'];
		} else {
			$this->data['error_warning'] = '';
		}

 		if (isset($this->error['name'])) {
			$this->data['error_name'] = $this->error['name'];
		} else {
			$this->data['error_name'] = '';
		}

 		if (isset($this->error['href'])) {
			$this->data['error_href'] = $this->error['href'];
		} else {
			$this->data['error_href'] = '';
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
			'href'      => $this->url->link('catalog/menu', 'token=' . $this->session->data['token'] . $url, 'SSL'),
      		'separator' => ' :: '
   		);

		if (!isset($this->request->get['menu_id'])) {
			$this->data['action'] = $this->url->link('catalog/menu/insert', 'token=' . $this->session->data['token'] . $url, 'SSL');
		} else {
			$this->data['action'] = $this->url->link('catalog/menu/update', 'token=' . $this->session->data['token'] . '&menu_id=' . $this->request->get['menu_id'] . $url, 'SSL');
		}
		
		$this->data['cancel'] = $this->url->link('catalog/menu', 'token=' . $this->session->data['token'] . $url, 'SSL');

		$this->data['build_main'] = $this->url->link('catalog/menu/build_main', 'token=' . $this->session->data['token'], 'SSL');
		$this->data['build_left'] = $this->url->link('catalog/menu/build_left', 'token=' . $this->session->data['token'], 'SSL');
        
		$this->data['token'] = $this->session->data['token'];
		
    	if (isset($this->request->get['menu_id']) && ($this->request->server['REQUEST_METHOD'] != 'POST')) {
      		$menu_info = $this->model_catalog_menu->getMenu($this->request->get['menu_id']);
    	}

		$this->load->model('localisation/language');

		$this->data['languages'] = $this->model_localisation_language->getLanguages();

		if (isset($this->request->post['menu_description'])) {
			$this->data['menu_description'] = $this->request->post['menu_description'];
		} elseif (isset($this->request->get['menu_id'])) {
			$this->data['menu_description'] = $this->model_catalog_menu->getMenuDescriptions($this->request->get['menu_id']);
		} else {
			$this->data['menu_description'] = array();
		}

    	if (isset($this->request->post['href'])) {
      		$this->data['href'] = $this->request->post['href'];
    	} elseif (isset($menu_info)) {
			$this->data['href'] = $menu_info['href'];
		} else {
      		$this->data['href'] = '#';
    	}

    	if (isset($this->request->post['class'])) {
      		$this->data['class'] = $this->request->post['class'];
    	} elseif (isset($menu_info)) {
			$this->data['class'] = $menu_info['class'];
		} else {
      		$this->data['class'] = '';
    	}

		$menus = $this->model_catalog_menu->getAllMenus();

		$this->data['menus'] = $this->getAllMenus($menus);

		if (isset($menu_info)) {
			unset($this->data['menus'][$menu_info['menu_id']]);
		}

		if (isset($this->request->post['parent_id'])) {
			$this->data['parent_id'] = $this->request->post['parent_id'];
		} elseif (!empty($menu_info)) {
			$this->data['parent_id'] = $menu_info['parent_id'];
		} else {
			$this->data['parent_id'] = 0;
		}

		if (isset($this->request->post['type'])) {
			$this->data['type'] = $this->request->post['type'];
		} elseif (!empty($menu_info)) {
			$this->data['type'] = $menu_info['type'];
		} else {
			$this->data['type'] = 0;
		}

		if (isset($this->request->post['subcategory'])) {
			$this->data['subcategory'] = $this->request->post['subcategory'];
		} elseif (!empty($menu_info)) {
			$this->data['subcategory'] = $menu_info['subcategory'];
		} else {
			$this->data['subcategory'] = 0;
		}

		if (isset($this->request->post['top'])) {
			$this->data['top'] = $this->request->post['top'];
		} elseif (!empty($menu_info)) {
			$this->data['top'] = $menu_info['top'];
		} else {
			$this->data['top'] = 0;
		}

		if (isset($this->request->post['column'])) {
			$this->data['column'] = $this->request->post['column'];
		} elseif (!empty($menu_info)) {
			$this->data['column'] = $menu_info['column'];
		} else {
			$this->data['column'] = 1;
		}

		if (isset($this->request->post['sort_order'])) {
			$this->data['sort_order'] = $this->request->post['sort_order'];
		} elseif (!empty($menu_info)) {
			$this->data['sort_order'] = $menu_info['sort_order'];
		} else {
			$this->data['sort_order'] = 0;
		}

		if (isset($this->request->post['status'])) {
			$this->data['status'] = $this->request->post['status'];
		} elseif (!empty($menu_info)) {
			$this->data['status'] = $menu_info['status'];
		} else {
			$this->data['status'] = 1;
		}

    	if (isset($this->request->post['cat_href'])) {
      		$this->data['cat_href'] = $this->request->post['cat_href'];
    	} elseif (isset($menu_info)) {
			$this->data['cat_href'] = str_replace('&amp;', '&', $menu_info['href']);
		} else {
      		$this->data['cat_href'] = '#';
    	}

    	if (isset($this->request->post['man_href'])) {
      		$this->data['cat_href'] = $this->request->post['man_href'];
    	} elseif (isset($menu_info)) {
			$this->data['man_href'] = str_replace('&amp;', '&', $menu_info['href']);
		} else {
      		$this->data['man_href'] = '#';
    	}

    	if (isset($this->request->post['info_href'])) {
      		$this->data['info_href'] = $this->request->post['info_href'];
    	} elseif (isset($menu_info)) {
			$this->data['info_href'] = str_replace('&amp;', '&', $menu_info['href']);
		} else {
      		$this->data['info_href'] = '#';
    	}

		$this->load->model('catalog/category');
		$categories = $this->model_catalog_category->getAllCategories();
		$categories = $this->getAllCategories($categories);

        $this->data['categories'] = array();
        foreach($categories as $category) {
            $this->data['categories'][] = array(
                'name'  => $category['name'],
                'href'  => 'product/category&path=' . $category['category_id']
            );
        }

		$this->load->model('catalog/manufacturer');
        $manufacturers = $this->model_catalog_manufacturer->getManufacturers();

        foreach($manufacturers as $manufacturer) {
            $this->data['manufacturers'][] = array(
                'name'  => $manufacturer['name'],
				'href'  => 'product/manufacturer/product&manufacturer_id=' . $manufacturer['manufacturer_id']
            );
        }

		$this->load->model('catalog/information');
        $informations = $this->model_catalog_information->getInformations();

        foreach($informations as $information) {
            $this->data['informations'][] = array(
                'name'  => $information['title'],
				'href'  => 'information/information&information_id=' . $information['information_id']
            );
        }


		$this->template = 'catalog/menu_form.tpl';
		$this->children = array(
			'common/header',	
			'common/footer'	
		);
		
		$this->response->setOutput($this->render());
	}  
	 
  	private function validateForm() {
    	if (!$this->user->hasPermission('modify', 'catalog/menu')) {
      		$this->error['warning'] = $this->language->get('error_permission');
    	}

    	foreach ($this->request->post['menu_description'] as $language_id => $value) {
      		if ((utf8_strlen($value['name']) < 3) || (utf8_strlen($value['name']) > 255)) {
        		$this->error['name'][$language_id] = $this->language->get('error_name');
      		}
    	}

    	if ((utf8_strlen($this->request->post['href']) < 1) || (utf8_strlen($this->request->post['href']) > 255)) {
      		$this->error['href'] = $this->language->get('error_href');
    	}

		if (!$this->error) {
	  		return true;
		} else {
	  		return false;
		}
  	}    

  	private function validateDelete() {
    	if (!$this->user->hasPermission('modify', 'catalog/menu')) {
			$this->error['warning'] = $this->language->get('error_permission');
    	}	
		
		$this->load->model('catalog/product');

		if (!$this->error) {
	  		return true;
		} else {
	  		return false;
		}  
  	}

    private function validateApply() {
        if (!$this->user->hasPermission('modify', 'catalog/menu')) {
            $this->data['text_error'] = $this->language->get('error_permission');
            return false;
        }
        return true;
    }

	private function getMenus($parent_id, $parent_path = '', $indent = '') {
		$menu_id = array_shift($this->path);

		$output = array();

		static $href_menu = null;
		static $href_action = null;

		if ($href_menu === null) {
			$href_menu = $this->url->link('catalog/menu', 'token=' . $this->session->data['token'] . '&path=', 'SSL');
			$href_action = $this->url->link('catalog/menu/update', 'token=' . $this->session->data['token'] . '&menu_id=', 'SSL');
		}

		$results = $this->model_catalog_menu->getMenusByParentId($parent_id);

		foreach ($results as $result) {
			$path = $parent_path . $result['menu_id'];

			$href = ($result['children']) ? $href_menu . $path : '';

			$name = $result['name'];

			if ($menu_id == $result['menu_id']) {
				$name = '<b>' . $name . '</b>';

				$this->data['breadcrumbs'][] = array(
					'text'      => $result['name'],
					'href'      => $href,
					'separator' => ' :: '
				);

				$href = '';
			}

			$selected = isset($this->request->post['selected']) && in_array($result['menu_id'], $this->request->post['selected']);

			$action = array();

			$action[] = array(
				'text' => $this->language->get('text_edit'),
				'href' => $href_action . $result['menu_id']
			);

			$output[$result['menu_id']] = array(
				'menu_id'     => $result['menu_id'],
				'type'        => $result['type'],
				'name'        => $name,
				'link'        => $result['href'],
				'sort_order'  => $result['sort_order'],
				'selected'    => $selected,
				'action'      => $action,
				'href'        => $href,
				'indent'      => $indent
			);

			if ($menu_id == $result['menu_id']) {
				$output += $this->getMenus($result['menu_id'], $path . '_', $indent . str_repeat('&nbsp;', 8));
			}
		}

		return $output;
	}

	private function getAllMenus($menus, $parent_id = 0, $parent_name = '') {
		$output = array();

		if (array_key_exists($parent_id, $menus)) {
			if ($parent_name != '') {
				$parent_name .= $this->language->get('text_separator');
			}

			foreach ($menus[$parent_id] as $menu) {
				$output[$menu['menu_id']] = array(
					'menu_id' => $menu['menu_id'],
					'name'        => $parent_name . $menu['name']
				);

				$output += $this->getAllMenus($menus, $menu['menu_id'], $parent_name . $menu['name']);
			}
		}

		return $output;
	}

	private function getAllCategories($categories, $parent_id = 0, $parent_name = '') {
		$output = array();

		if (array_key_exists($parent_id, $categories)) {
			if ($parent_name != '') {
				$parent_name .= ' &raquo; '; //$this->language->get('text_separator');
			}

			foreach ($categories[$parent_id] as $category) {
				$output[$category['category_id']] = array(
					'category_id' => $category['category_id'],
					'name'        => $parent_name . $category['name']
				);

				$output += $this->getAllCategories($categories, $category['category_id'], $parent_name . $category['name']);
			}
		}

		return $output;
	}

}
?>