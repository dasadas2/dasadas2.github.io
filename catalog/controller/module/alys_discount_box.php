<?php
class ControllerModuleAlysDiscountBox extends Controller {

	private $_name = 'alys_discount_box';

	public function index() {
		$this->language->load('module/' . $this->_name);

		$setting_info = array(
            'title',
            'text',
            'button'
        );

		foreach ($setting_info as $param) {
			$this->data[$param] = $this->config->get($this->_name . '_' . $param);
		}

        $this->data['text'] = html_entity_decode($this->config->get($this->_name . '_text'), ENT_QUOTES, 'UTF-8');

		$this->data['enable_box_enter'] = $this->config->get($this->_name . '_status_enter');
		$this->data['enable_box_time']  = $this->config->get($this->_name . '_status_time');
		$this->data['time_delay']       = $this->config->get($this->_name . '_time_delay');

		$this->data['email_prompt']     = $this->language->get('email_prompt');

        //echo '<pre>';
        //print_r($this->data);
        //echo '</pre>';

        //$ip = $this->request->server['REMOTE_ADDR'];

		//$this->load->model('module/alys_discount_box');
        //$check = $this->model_module_alys_discount_box->getTotalCustomerDiscountsByIp($ip);

		//$this->data['ip']       = $ip;
		//$this->data['ip_fixed'] = $check;

		if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/module/alys_discount_box.tpl')) {
			$this->template = $this->config->get('config_template') . '/template/module/alys_discount_box.tpl';
		} else {
			$this->template = 'default/template/module/alys_discount_box.tpl';
		}

		$this->response->setOutput($this->render());
	}

    public function save_email() {
		$this->language->load('module/' . $this->_name);

        $json = array();

        if ($this->request->server['REQUEST_METHOD'] == 'POST') {

            if (!empty($this->request->post['email'])) {

        		$this->load->model('module/alys_discount_box');
                $data = array(
                    'ip'        => $this->request->server['REMOTE_ADDR'],
                    'email'     => $this->request->post['email'],
                    'status'    => 0,
                );

                $customer_discount_id = $this->model_module_alys_discount_box->addCustomerDiscount($data);

		        $json['title_exit'] = $this->config->get($this->_name . '_title_exit');
		        $json['success']    = $this->config->get($this->_name . '_success');

                $this->SendTextMail($this->request->post['email']);

            } else {
		        $json['error_save'] = $this->language->get('error_save');
            }
        }

        $this->response->setOutput(json_encode($json));
    }


	public function SendTextMail($email) {
		$this->language->load('module/' . $this->_name);

        $text_mail = $this->config->get($this->_name . '_mail');

        // Письмо клиенту
		if ( !empty($email) && !empty($text_mail) ) {
			//Mail

			$mail = new Mail();
			$mail->protocol = $this->config->get('config_mail_protocol');
			$mail->parameter = $this->config->get('config_mail_parameter');
			$mail->hostname = $this->config->get('config_smtp_host');
			$mail->username = $this->config->get('config_smtp_username');
			$mail->password = $this->config->get('config_smtp_password');
			$mail->port = $this->config->get('config_smtp_port');
			$mail->timeout = $this->config->get('config_smtp_timeout');

			$mail->setFrom($this->config->get('config_email'));
			$mail->setSender($this->config->get('config_name'));

			//body
    		$body_message_customer = $text_mail;
    		$body_message_customer .= "\n";

    		$mail->setTo($email);

    		$subject = sprintf($this->language->get('heading_title_mail_customer'), $this->config->get('config_name'));
    		$mail->setSubject(html_entity_decode($subject), ENT_QUOTES, 'UTF-8');

    		$content = html_entity_decode(sprintf($body_message_customer), ENT_QUOTES, 'UTF-8');
    		$mail->setText(strip_tags($content));

    		$mail->send();

        }
    }

}
?>