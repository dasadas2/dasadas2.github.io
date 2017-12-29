<?php
class ControllerModuleMenu extends Controller {
	protected function index() {
		$this->language->load('module/menu');

    	$this->data['heading_title'] = $this->language->get('heading_title');
		
		if (isset($this->request->get['path'])) {
			$parts = explode('_', (string)$this->request->get['path']);
		} else {
			$parts = array();
		}
		
		if (isset($parts[0])) {
			$this->data['menu_id'] = $parts[0];
		} else {
			$this->data['menu_id'] = 0;
		}
		
		if (isset($parts[1])) {
			$this->data['child_id'] = $parts[1];
		} else {
			$this->data['child_id'] = 0;
		}


        $this->data['menu_mode'] = $this->config->get('menu_hide_menu');

		$this->load->model('catalog/menu');

		// Menu
		if (isset($this->request->get['path'])) {
			$parts = explode('_', (string)$this->request->get['path']);
		} else {
			$parts = array();
		}

		$this->load->model('catalog/category');
		$this->load->model('catalog/product');

		$this->data['menus'] = array();
		$menus = $this->model_catalog_menu->getMenus(0);

		foreach ($menus as $menu) {
			if ($menu['top']) {
				$children_data = array();

                $children_data = $this->getChildrenData($menu);

                switch($menu['type']) {
                    case 1:
                    case 2:
                    case 3:
                    case 5:
                        $href = $this->url->link($menu['href']);
                    break;

                    default:
                        $href = $menu['href'];
                }

				// Level 1
				$this->data['menus'][] = array(
					'name'     => $menu['name'],
					'children' => $children_data,
					'column'   => $menu['column'] ? $menu['column'] : 1,
					'href'     => $href,
					'class'    => $menu['class'],
					'active'   => in_array($menu['menu_id'], $parts)
				);
			}
		}

		if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/module/menu.tpl')) {
			$this->template = $this->config->get('config_template') . '/template/module/menu.tpl';
		} else {
			$this->template = 'default/template/module/menu.tpl';
		}

		$this->response->setOutput($this->render());
  	}

	private function getChildrenData($menu)
	{
		$this->load->model('catalog/category');
		$this->load->model('catalog/manufacturer');
		$this->load->model('catalog/menu');

		$children_data = array();
        $children      = array();

        if ($menu['type'] == 11) {

            $results = array();
            $parts = explode('=', $menu['href']);
            if (count($parts) > 2) {
		        $results = $this->model_catalog_category->getCategories($parts[2]);
            } elseif (isset($menu['category_id']) && $menu['category_id'] > 0) {
		        $results = $this->model_catalog_category->getCategories($menu['category_id']);
            } else {
		        $results = $this->model_catalog_category->getCategories(0);
            }

            foreach ($results as $result) {
                $children[] = array(
                    'type'          => 11,
                    'subcategory'   => 1,
                    'name'          => $result['name'],
                    'href'          => $this->url->link('product/category', 'path=' . $result['category_id']),
    				'class'         => '',
                    'category_id'   => $result['category_id']
                );
            }
        } elseif ($menu['type'] == 12) {

            $results = $this->model_catalog_manufacturer->getManufacturers();
            foreach ($results as $result) {
                $children[] = array(
                    'type'          => 10,
                    'subcategory'   => 1,
                    'name'          => $result['name'],
        			'href'          => $this->url->link('product/manufacturer/product', 'manufacturer_id=' . $result['manufacturer_id']),
    				'class'         => ''
                );
            }
        } else {

            if (isset($menu['menu_id'])) {
        		$children = $this->model_catalog_menu->getMenus($menu['menu_id']);
            }

        }


    		foreach ($children as $child) {
                $children_data2 = $this->getChildrenData($child);

                switch($child['type']) {
                    case 1:
                    case 2:
                    case 3:
                    case 5:
                        $href = $this->url->link($child['href']);
                    break;

                    default:
                        $href = $child['href'];
                }

        		$children_data[] = array(
        			'type'     => $child['type'],
        			'name'     => $child['name'],
        			'children' => $children_data2,
        			'href'     => $href,
    				'class'    => $child['class'],
        			'active'   => ''
        		);
            }


		return $children_data;
	}

	// http://rb.labtodo.com/page/opencart-15x-mainmenu-3rd-level
	private function getCategoryChildrenData( $ctg_id, $path_prefix )
	{
		$children_data = array();
		$children = $this->model_catalog_category->getCategories($ctg_id);

		foreach ($children as $child) {
			$data = array(
				'filter_category_id'  => $child['category_id'],
				'filter_sub_category' => true
			);

			$product_total = $this->model_catalog_product->getTotalProducts($data);

			$children_data[] = array(
				'name'  => $child['name'] . ' (' . $product_total . ')',
				'href'  => $this->url->link('product/category', 'path=' . $path_prefix . '_' . $child['category_id'])
			);
		}
		return $children_data;
	}

}
?>