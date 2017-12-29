<?php
class ControllerToolUpdatePrice extends Controller {
	private $error = array();
	private $discounts = array();

	public function index() {
		$this->language->load('tool/update_price');

		$this->document->setTitle($this->language->get('heading_title'));
		
		$this->data['heading_title'] = $this->language->get('heading_title');
		 
		$this->data['button_update'] = $this->language->get('button_update');

		if (isset($this->session->data['success'])) {
			$this->data['success'] = $this->session->data['success'];
		
			unset($this->session->data['success']);
		} else {
			$this->data['success'] = '';
		}
		
  		$this->data['breadcrumbs'] = array();

   		$this->data['breadcrumbs'][] = array(
       		'text'      => $this->language->get('text_home'),
			'href'      => $this->url->link('common/home', 'token=' . $this->session->data['token'], 'SSL'),       		
      		'separator' => false
   		);

   		$this->data['breadcrumbs'][] = array(
       		'text'      => $this->language->get('heading_title'),
			'href'      => $this->url->link('tool/update_price', 'token=' . $this->session->data['token'], 'SSL'),
      		'separator' => ' :: '
   		);
		
		$this->data['update'] = $this->url->link('tool/update_price/update', 'token=' . $this->session->data['token'], 'SSL');
		
		$this->template = 'tool/update_price.tpl';
		$this->children = array(
			'common/header',
			'common/footer'
		);
				
		$this->response->setOutput($this->render());
	}
	
	public function update() {

        if (isset($this->request->post['source'])) {
    	    $source = $this->request->post['source'];
        } else {
    	    $source = 1;
        }


		$this->language->load('tool/update_price');

		$this->load->model('localisation/discount');
		$this->load->model('tool/update_price');

		$results = $this->model_localisation_discount->getDiscounts();

		foreach ($results as $result) {
			$this->discounts[] = array(
				'discount_id'   => $result['discount_id'],
				'min_price'     => $result['min_price'],
				'max_price'     => $result['max_price'],
				'percent'       => $result['percent'],
				'sort_order'    => $result['sort_order'],
			);
		}

        //----------------------------------------------------------------------
		$products = $this->model_tool_update_price->getProducts($source);
		foreach ($products as $product) {
            $percent   = 0.0;

            foreach($this->discounts as $discount) {
                $price_in = $product['price_import'];
                if ($price_in > $discount['min_price'] && $price_in <= $discount['max_price']) {
                    $price_out = $price_in + ($price_in * (float)$discount['percent'])/100.0;
                    $percent = (float)$discount['percent'];
                    break;
                }
            }

            $data['price']   = $price_out;
            $data['percent'] = $percent;

            $this->model_tool_update_price->getUpdateProductPrice($product['product_id'], $data);

        }
        //----------------------------------------------------------------------

		$this->session->data['success'] = $this->language->get('text_success');

		$this->redirect($this->url->link('tool/update_price', 'token=' . $this->session->data['token'], 'SSL'));
	}
}
?>