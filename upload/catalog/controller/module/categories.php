<?php
class ControllerModuleCategories extends Controller {
	protected function index($setting) {
		$this->language->load('module/categories');

        $this->data['heading_title'] = $this->language->get('heading_title');

		if (isset($this->request->get['path'])) {
			$parts = explode('_', (string)$this->request->get['path']);
		} else {
			$parts = array();
		}

        $this->load->model('catalog/categories');

		$this->load->model('catalog/product');

        $this->data['categories_data'] = array();

        $categories = $this->getChildData(0);

        $this->data['categories_data'] = $this->getChildTree($categories, $parts, true);

        if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/module/categories.tpl')) {
			$this->template = $this->config->get('config_template') . '/template/module/categories.tpl';
		} else {
			$this->template = 'default/template/module/categories.tpl';
		}

		$this->render();
  	}

          protected function getChildTree($children, $parts, $first = false) {
              $categories_data = '';
              if ($children) {
                  $category_id = array_shift($parts);
                $categories_data .= '<ul'.($first ? ' class="box-category"':'').'>';
                foreach ($children as $child) {
                    $categories_data .= '<li>';
                    if (isset($child['category_id'])) {
                        if ($child['category_id'] == $category_id) {
                            $categories_data .= '<a href="'.$child['href'].'" class="active"> - '.$child['name'].'</a>';
                        } else {
                            $categories_data .= '<a href="'.$child['href'].'"> - '.$child['name'].'</a>';
                        }
                        $categories_data .= $this->getChildTree($child['children'], $parts);
                    }
                    $categories_data .= '</li>';
                }
                $categories_data .= '</ul>';
            }
            return $categories_data;
          }

          protected function getChildData($category_id,$category_path = '') {
                    $get_children_data = array();

                    $children = $this->model_catalog_categories->getCategories($category_id);

                    foreach ($children as $child) {
                            $data = array(
                                    'filter_category_id'  => $child['category_id'],
                                    'filter_sub_category' => true
                            );

                            $product_total = $this->model_catalog_product->getTotalProducts($data);

                            $cur_path = ($category_path != '' ? $category_path.'_':'') . $child['category_id'];
                            $children_data = $this->getChildData($child['category_id'], $cur_path);

                            $get_children_data[] = array(
                                    'category_id' => $child['category_id'],
                                    'name'        => $child['name'] . ($this->config->get('config_product_count') ? ' (' . $product_total . ')' : ''),
                                    'children'  => $children_data,
                                    'product_total' => $product_total,
                                    'href'        => $this->url->link('product/category', 'path=' .$cur_path)
                            );
                    }

                    return $get_children_data;
          }
}
?>