<?php
class ControllerModule1000CategoriesAccordion extends Controller {
	protected $category_id = 0;
	protected $parent_id = 0;
	protected $path = array();
	//---------------------------------------------------------------------------------------------------------------------------
	protected function index() {
		$this->language->load('module/1000_categories_accordion');
	   	$this->data['heading_title'] = $this->language->get('heading_title');
		$this->load->model('catalog/category');
		
	   	$this->data['ajax_loader'] = $this->url->link('module/1000_categories_accordion/ajax');
	   	
		if (isset($this->request->get['path'])) {
			$this->path = explode('_', $this->request->get['path']);
			
			$this->category_id = end($this->path);
		}
		
		$this->data['category_accordion_menu'] = $this->loadCategories(0, '', $this->category_id);
		
		if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/module/1000_categories_accordion.tpl')) {
			$this->template = $this->config->get('config_template') . '/template/module/1000_categories_accordion.tpl';
		} else {
			$this->template = 'default/template/module/1000_categories_accordion.tpl';
		}
		
		$this->render();
	}
	//---------------------------------------------------------------------------------------------------------------------------
	protected function loadCategories($parent_id, $current_path = '', $cid = '') {
		//$path = explode('_', $current_path);
		//$category_id = array_pop($this->path);
		//$this->getCategoriesFromdb($category_id);
		
		$results = $this->getCategoriesByParentId($parent_id);
		
		$ret_string = '';
		if ($results) { 
			if ($parent_id == 0) {
				$ret_string .= '<ul class="categories_1000 box-category">';
			} else {
				$ret_string .= '<ul id="children_' . $parent_id . '" loaded="1">'; 
			}
		}
		
		foreach ($results as $result) {	
			if (!$current_path) {
				$new_path = $result['category_id'];
			} else {
				$new_path = $current_path . '_' . $result['category_id'];
			}
			
			$ret_string .= '<li class="cid' . $result['category_id'] . '">';
			
			$ajax = false;
			if (in_array($result['category_id'], $this->path)) {
				$children = $this->loadCategories($result['category_id'], $new_path, $cid);
			}
			elseif ($result['children']) {
				$children =  '<ul id="children_'.$result['category_id'].'"></ul>';
				$ajax = true;
			}
			else {
				$children = '';
			}
			if ($cid == $result['category_id']) {
			//if ($this->category_id == $result['category_id']) {
				$classactive = 'active';
			} else {
				$classactive = '';
			}
			
			
			if ($children) {
				$ret_string .= '<a class="havechild ' . $classactive . ' expand-categ" href="' . $this->url->link('product/category','path=' .  $new_path)  . '" category="'.$result['category_id'].'" path="'.$new_path.'">' . '- ' . $result['name'] . '</a>';
			} else {
				$ret_string .= '<a class="nochild ' . $classactive . '" href="' . $this->url->link('product/category','path=' .  $new_path)  . '">' . '- ' . $result['name'] . '</a>';
			}			
			
        	$ret_string .= $children.'</li>'; 
		}

 		
		if ($results) $ret_string .= '</ul>'; 
		return $ret_string;
	}
	//---------------------------------------------------------------------------------------------------------------------------
	
	public function ajax() {
		$parent_id = isset($this->request->post['parent_id']) ? $this->request->post['parent_id'] : '';
		$current_path = isset($this->request->post['path']) ? $this->request->post['path'] : '';
		
		$results = $this->getCategoriesByParentId($parent_id);
		$ret_string = '';
		
		foreach ($results as $result) {	
			if (!$current_path) {
				$new_path = $result['category_id'];
			} else {
				$new_path = $current_path . '_' . $result['category_id'];
			}
			
			$ret_string .= '<li class="cid' . $result['category_id'] . '" category="'.$result['category_id'].'">';
			
			if ($result['children']) {
				$children =  '<ul id="children_'.$result['category_id'].'"></ul>';
			}
			else {
				$children = '';
			}
			
			if ($children) { 
				$ret_string .= '<a class="havechild expand-categ" href="' . $this->url->link('product/category','path=' .  $new_path)  . '" category="'.$result['category_id'].'" path="'.$new_path.'">' . $result['name'] . '</a>';
			} else { 
				$ret_string .= '<a class="nochild" href="' . $this->url->link('product/category','path=' .  $new_path)  . '">' . $result['name'] . '</a>';
			}			
			
        	$ret_string .= $children.'</li>';
		}

 		
		$this->response->setOutput($ret_string);
	}
	//---------------------------------------------------------------------------------------------------------------------------

	protected function getCategoriesByParentId($parent_id = 0) {
		$query = $this->db->query("SELECT *, (SELECT COUNT(parent_id) FROM " . DB_PREFIX . "category WHERE parent_id = c.category_id AND status = '1' ) AS children FROM " . DB_PREFIX . "category c LEFT JOIN " . DB_PREFIX . "category_description cd ON (c.category_id = cd.category_id) WHERE c.parent_id = '" . (int)$parent_id . "' AND c.status = '1' AND cd.language_id = '" . (int)$this->config->get('config_language_id') . "' ORDER BY c.sort_order, cd.name");
		
		return $query->rows;
	}
}
?>
