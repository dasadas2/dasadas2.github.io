<?php
class ControllerModuleTehnomir extends Controller {
	private $username = '';
	private $password = '';

  	public function index() {

        $this->username = $this->config->get('tehnomir_username');
        $this->password = $this->config->get('tehnomir_password');

  	}

    public function checkNumber() {

        require_once(DIR_SYSTEM . 'library/tehnomir.php');

        $json = array();

        if ($this->request->server['REQUEST_METHOD'] == 'POST') {

            if (isset($this->request->post['search'])) {
                $number = $this->request->post['search'];
            } else {
                $number = '';
            }

            $username = $this->config->get('tehnomir_username');
            $password = $this->config->get('tehnomir_password');

            $request  = 'usr_login=' . $username;
            $request .= '&usr_passwd=' . $password;
            $request .= '&Number=' . $number;
            $request .= '&Currency=USD';

            $curl = curl_init('http://tehnomir.com.ua/ws/xml.php?act=GetPrice');

        	curl_setopt($curl, CURLOPT_POST, false);
        	curl_setopt($curl, CURLOPT_POSTFIELDS, $request);
        	curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        	curl_setopt($curl, CURLOPT_HEADER, false);
        	curl_setopt($curl, CURLOPT_TIMEOUT, 30);
        	curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);

          	$result = curl_exec($curl);
            curl_close($curl);

    		$xml2arr = new XmlToArray($result);
            $data = $xml2arr->XmlToArray($result);
            $array = $xml2arr->createArray();

            $items = $array['GetPrice']['Detail'];

		    $json['products'] = $items;
		    $json['number'] = $number;

        } else {

        }

		$this->data = $json;

		if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/module/tehnomir.tpl')) {
			$this->template = $this->config->get('config_template') . '/template/module/tehnomir.tpl';
		} else {
			$this->template = 'default/template/module/tehnomir.tpl';
		}

		$this->response->setOutput($this->render());
    }


    public function addProduct() {
        $json = array();

        if ($this->request->server['REQUEST_METHOD'] == 'POST') {

            if (isset($this->request->post['brand'])) {
                $brand = $this->request->post['brand'];
            } else {
                $brand = '';
            }

            if (isset($this->request->post['supplierCode'])) {
                $supplierCode = $this->request->post['supplierCode'];
            } else {
                $supplierCode = '';
            }

    		$this->load->model('module/tehnomir');

            $manufacturer_id = $this->model_module_tehnomir->getManufacturerByName($brand);
            $json['manufacturer_id'] = $manufacturer_id;

            if ($manufacturer_id == 0) {
            }

            $data = $this->resetDefaultValues($this->request->post);

            $product_id = $this->model_module_tehnomir->addProduct($data);

            $json['product_id'] = $product_id;

        }

		$this->response->setOutput(json_encode($json));

    }

	private function resetDefaultValues($data = array()) {

        $this->log->write(print_r($data, true));

        if (isset($this->request->post['brand'])) {
            $brand = $this->request->post['brand'];
        } else {
            $brand = '';
        }

        if (isset($this->request->post['supplierCode'])) {
            $supplierCode = $this->request->post['supplierCode'];
        } else {
            $supplierCode = '';
        }

        if (isset($data['number'])) {
            $number = $data['number'];
        } else {
            $number = '';
        }

        if (isset($data['name'])) {
            $name = $data['name'];
        } else {
            $name = '';
        }

        if (isset($data['quantity']) && !empty($data['quantity'])) {
            $quantity = $data['quantity'];
        } else {
            $quantity = 1;
        }

        if (isset($data['price'])) {
            $price = $data['price'];
        } else {
            $price = '';
        }

        if (isset($data['weight'])) {
            $weight = $data['weight'];
        } else {
            $weight = '';
        }

		//required desc data
		$desc_data = array(
			'name'              => $brand . ' - ' . $name,
			'description'       => $name,
			'meta_description'  => $name,
            'seo_title'         => $name,
            'seo_h1'            => $name,
            'tag'               => '',
            '_firm'             => '',
            '_country'          => '',
            '_mancode'          => '',
            '_articul'          => '',
		);

		//required product data
		$product_data = array(
			'date_available'    => date('Y-m-d', time()-86400),
			'model'             => $number,
			'sku'	            => $number,
			'ean'	            => '',
			'jan'	            => '',
			'isbn'	            => '',
			'mpn'	            => '',
			'tag'	            => '',
			'upc'	            => '',
			'points'	        => 0,
			'location'          => '',
			'manufacturer_id'   => 0,
			'line_id'           => 0,
			'shipping'          => 1,
			'image'             => '',
			'quantity'          => $quantity,
			'minimum'           => 1,
			'maximum'           => 0,
			'subtract'          => 1,
			'sort_order'        => 1,
			'price'             => $price,
			'currency_code'     => 'USD',
			'status'            => 1,
			'tax_class_id'      => $this->config->get('config_length_class_id'),
			'weight'            => $weight,
			'weight_class_id'   => $this->config->get('config_weight_class_id'),
			'length'            => '',
			'width'             => '',
			'height'            => '',
			'length_class_id'   => $this->config->get('config_length_class_id'),
			'keyword'           => '',
			'stock_status_id'   => $this->config->get('config_stock_status_id'),
			'source'            => '5',
		);

        $product_data['product_description'][(int)$this->config->get('config_language_id')] = $desc_data;
        $product_data['product_category'] = array();

        $product_data['product_attribute'] = array();
        $product_data['product_option'] = array();
        $product_data['product_store'] = array();
        $product_data['product_discount'] = array();
        $product_data['product_special'] = array();
        $product_data['product_image'] = array();
        $product_data['product_download'] = array();
        $product_data['product_filter'] = array();
        $product_data['product_related'] = array();
        $product_data['product_reward'] = array();
        $product_data['product_layout'] = array();

        return $product_data;
	}


}
?>