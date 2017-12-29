<?php
class ControllerPaymentCod extends Controller {
	protected function index() {
    	$this->data['button_confirm'] = $this->language->get('button_confirm');

                    $this->data['continue'] = $this->url->link('checkout/success');

		if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/payment/cod.tpl')) {
			$this->template = $this->config->get('config_template') . '/template/payment/cod.tpl';
		} else {
			$this->template = 'default/template/payment/cod.tpl';
		}

		$this->render();
	}

	public function confirm() {
		$this->load->model('checkout/order');
                                if (isset($this->session->data['guest'])) {
                                    // register guest
                                    $json = $this->registerGuest();
                                    if (!$json) {
                                        $this->model_checkout_order->confirm($this->session->data['order_id'], $this->config->get('cod_order_status_id'));
                                    } else {
                                        $this->response->setOutput(json_encode($json));
                                    }
                                } else {
                                    $this->model_checkout_order->confirm($this->session->data['order_id'], $this->config->get('cod_order_status_id'));
                                }
	}

        private function registerGuest() {

                // register new user from guest
                $guest_data = array();
                $guest_data['firstname'] = $this->session->data['guest']['firstname'];
                $guest_data['lastname'] = $this->session->data['guest']['lastname'];
                $guest_data['email'] = $this->session->data['guest']['email'];
                $guest_data['telephone'] = $this->session->data['guest']['telephone'];
                $guest_data['fax'] = $this->session->data['guest']['fax'];
                $guest_data['password'] = substr(md5($guest_data['firstname'].$guest_data['email'].$guest_data['lastname'].'666'),0,19);

                $payment_address = $this->session->data['guest']['payment'];

                $guest_data['company'] = $payment_address['company'];
                $guest_data['company_id'] = $payment_address['company_id'];
                $guest_data['tax_id'] = $payment_address['tax_id'];
                $guest_data['address_1'] = $payment_address['address_1'];
                $guest_data['address_2'] = $payment_address['address_2'];
                $guest_data['city'] = $payment_address['city'];
                $guest_data['postcode'] = $payment_address['postcode'];
                $guest_data['country_id'] = $payment_address['country_id'];
                $guest_data['zone_id'] = $payment_address['zone_id'];
                // admin email
                //$this->config->get('config_email')
                $json = array();

                // maybe already automatically registered ?
                if (!$this->customer->login($guest_data['email'], $guest_data['password'])) {

                    // checks
                    $this->language->load('checkout/checkout');
                    $this->load->model('account/customer');

                    if ((utf8_strlen($guest_data['firstname']) < 1) || (utf8_strlen($guest_data['firstname']) > 32)) {
                          $json['error']['firstname'] = $this->language->get('error_firstname');
                    }

                    if ((utf8_strlen($guest_data['lastname']) < 1) || (utf8_strlen($guest_data['lastname']) > 32)) {
                          $json['error']['lastname'] = $this->language->get('error_lastname');
                    }

                    if ((utf8_strlen($guest_data['email']) > 96) || !preg_match('/^[^\@]+@.*\.[a-z]{2,6}$/i', $guest_data['email'])) {
                          $json['error']['email'] = $this->language->get('error_email');
                    }

                    if ($this->model_account_customer->getTotalCustomersByEmail($guest_data['email'])) {
                          $json['error']['warning'] = $this->language->get('error_exists');
                    }

                    if ((utf8_strlen($guest_data['telephone']) < 3) || (utf8_strlen($guest_data['telephone']) > 32)) {
                          $json['error']['telephone'] = $this->language->get('error_telephone');
                    }

                    // Customer Group
                    $this->load->model('account/customer_group');

                    if (isset($guest_data['customer_group_id']) && is_array($this->config->get('config_customer_group_display')) && in_array($guest_data['customer_group_id'], $this->config->get('config_customer_group_display'))) {
                          $customer_group_id = $guest_data['customer_group_id'];
                    } else {
                          $customer_group_id = $this->config->get('config_customer_group_id');
                    }

                    $customer_group = $this->model_account_customer_group->getCustomerGroup($customer_group_id);

                    if ($customer_group) {
                          // Company ID
                          if ($customer_group['company_id_display'] && $customer_group['company_id_required'] && empty($guest_data['company_id'])) {
                                  $json['error']['company_id'] = $this->language->get('error_company_id');
                          }

                          // Tax ID
                          if ($customer_group['tax_id_display'] && $customer_group['tax_id_required'] && empty($guest_data['tax_id'])) {
                                  $json['error']['tax_id'] = $this->language->get('error_tax_id');
                          }
                    }

                    if ((utf8_strlen($guest_data['address_1']) < 3) || (utf8_strlen($guest_data['address_1']) > 128)) {
                          $json['error']['address_1'] = $this->language->get('error_address_1');
                    }

                    if ((utf8_strlen($guest_data['city']) < 2) || (utf8_strlen($guest_data['city']) > 128)) {
                          $json['error']['city'] = $this->language->get('error_city');
                    }

                    $this->load->model('localisation/country');

                    $country_info = $this->model_localisation_country->getCountry($guest_data['country_id']);

                    if ($country_info) {
                          if ($country_info['postcode_required'] && (utf8_strlen($guest_data['postcode']) < 2) || (utf8_strlen($guest_data['postcode']) > 10)) {
                                  $json['error']['postcode'] = $this->language->get('error_postcode');
                          }

                          // VAT Validation
                          $this->load->helper('vat');

                          if ($this->config->get('config_vat') && $guest_data['tax_id'] && (vat_validation($country_info['iso_code_2'], $guest_data['tax_id']) == 'invalid')) {
                                  $json['error']['tax_id'] = $this->language->get('error_vat');
                          }
                    }

                    if ($guest_data['country_id'] == '') {
                          $json['error']['country'] = $this->language->get('error_country');
                    }

                    if (!isset($guest_data['zone_id']) || $guest_data['zone_id'] == '') {
                          $json['error']['zone'] = $this->language->get('error_zone');
                    }

                    if ((utf8_strlen($guest_data['password']) < 4) || (utf8_strlen($guest_data['password']) > 20)) {
                          $json['error']['password'] = $this->language->get('error_password');
                    }

                    if (isset($json['error'])) {
                        // error adding user
                        $error = '';
                        foreach ($json['error'] as $value) {
                            $error .= $value."\n";
                        }
                        $json = array();
                        $json['error'] = $error;
                        return $json;
                    }
                    //register
                    $this->model_account_customer->addCustomer($guest_data);

                    if ($this->session->data['guest']['shipping_address']) {
                        $diff_address = false;
                    } else {
                        $diff_address = true;
                    }
                    $shipping_address = $this->session->data['guest']['shipping'];

                    //login
                    $this->customer->login($guest_data['email'], $guest_data['password']);
                }

                $this->session->data['payment_address_id'] = $this->customer->getAddressId();


                if ($diff_address) {
                     $this->load->model('account/address');
                     $sh_ad_id =$this->model_account_address->addAddress($shipping_address);
                     $this->session->data['shipping_address_id'] = $sh_ad_id;
                } else {
                     $this->session->data['shipping_address_id'] = $this->session->data['payment_address_id'];
                }
                //modify order
                $this->modifyOrder();
                unset($this->session->data['guest']);
                return false;
          }

          private function modifyOrder() {
              $this->db->query("UPDATE `" . DB_PREFIX . "order` SET customer_id = '" . $this->customer->getId() . "', customer_group_id = '" . $this->customer->getCustomerGroupId() . "', firstname = '" . $this->customer->getFirstName() . "', lastname = '" . $this->customer->getLastName() . "', email = '" . $this->customer->getEmail() . "', telephone = '" . $this->customer->getTelephone() . "', fax = '" . $this->customer->getFax() . "' WHERE order_id = '".$this->session->data['order_id']."'");
          }
}
?>