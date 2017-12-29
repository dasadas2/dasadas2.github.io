<?php
class ControllerModuleLatest extends Controller {
	protected function index($setting) {
		$this->language->load('module/latest');

                            $this->data['heading_title'] = $this->language->get('heading_title');

		$this->data['button_cart'] = $this->language->get('button_cart');

		$this->load->model('catalog/product');

		$this->load->model('tool/image');

		$this->data['products'] = array();

		if (intval($setting['limit']) <= 0) {
			$setting['limit'] = 10;
		}
		
		$random = true;

		if ($random) {
			$results = $this->model_catalog_product->getRandomProducts($setting['limit']);
		} else {
		$data = array(
			'sort'  => 'p.date_added',
			'order' => 'DESC',
			'start' => 0,
			'limit' => $setting['limit']
		);

		$results = $this->model_catalog_product->getProducts($data);
		}

		foreach ($results as $result) {
			if ($result['image']) {
				$image = $this->model_tool_image->resize($result['image'], $setting['image_width'], $setting['image_height']);
			} else {
				$image = false;
			}

			if (($this->config->get('config_customer_price') && $this->customer->isLogged()) || !$this->config->get('config_customer_price')) {
				$price = $this->currency->format($this->tax->calculate($result['price'], $result['tax_class_id'], $this->config->get('config_tax')),isset($result['currency_code'])?$result['currency_code']:'',1);
			} else {
				$price = false;
			}

			if ((float)$result['special']) {
				$special = $this->currency->format($this->tax->calculate($result['special'], $result['tax_class_id'], $this->config->get('config_tax')),isset($result['currency_code'])?$result['currency_code']:'',1);
			} else {
				$special = false;
			}

			if ($this->config->get('config_review_status')) {
				$rating = $result['rating'];
			} else {
				$rating = false;
			}

                                                $cat_string = '';
                                                /*
                                                $categories = $this->model_catalog_product->getCategories($result['product_id']);
                                                if ($categories) {
                                                    echo '<pre>';
                                                    var_export($categories);
                                                    echo '</pre>';
                                                    if (isset($categories[0])) {
                                                        if (isset($categories[0]['category_id'])) {
                                                            $cat_string = 'path='.$categories[0]['category_id'].'&';
                                                        }
                                                    }
                                                }
                                                 */
			$this->data['products'][] = array(
				'product_id' => $result['product_id'],
				'thumb'   	 => $image,
				'name'    	 => $result['name'],
                                                        'model'     => $result['model'],
				'price'   	 => $price,
				'special' 	 => $special,
				'rating'     => $rating,
				'reviews'    => sprintf($this->language->get('text_reviews'), (int)$result['reviews']),
				'href'    	 => $this->url->link('product/product', $cat_string.'product_id=' . $result['product_id']),
			);
		}

		if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/module/latest.tpl')) {
			$this->template = $this->config->get('config_template') . '/template/module/latest.tpl';
		} else {
			$this->template = 'default/template/module/latest.tpl';
		}

		$this->render();
	}
}
?>