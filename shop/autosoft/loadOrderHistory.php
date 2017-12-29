<?php
    include('config.php');

    include_once(ROOT . '/lib/mysqlll.lib.php');
    include_once(ROOT . '/lib/xml.lib.php');
    include_once(ROOT . '/lib/base.lib.php');

    /*--- check password ---*/
    include_once(ROOT . '/lib/user.php');

    $username = $_GET['login'];
    $password = $_GET['password'];

    if (empty($username) && empty($password)) {
        $username = $_POST['login'];
        $password = $_POST['password'];
    }

    if (!checkLogin($username, $password)) {
        return;
    }
    /*--- check password ---*/

//    include_once(ROOT . '/lib/mail.php');

    //1.5
//    include_once(ROOT.'/../admin/config.php');
include_once (ROOT.'/lib/opencart_init.php');
include_once (ROOT.'/../system/engine/model.php');
include_once (ROOT."/../admin/model/sale/order.php");
include_once (ROOT."/../admin/model/sale/affiliate.php");

Class ModelSaleOrderAutosoft extends ModelSaleOrder {
    // добавляем дату
    public function addOrderHistory($order_id, $data) {
        error_log('autosoft');
                $this->db->query("UPDATE `" . DB_PREFIX . "order` SET order_status_id = '" . (int)$data['order_status_id'] . "', date_modified = '".$data['date']."' WHERE order_id = '" . (int)$order_id . "'");

                $this->db->query("INSERT INTO " . DB_PREFIX . "order_history SET order_id = '" . (int)$order_id . "', order_status_id = '" . (int)$data['order_status_id'] . "', notify = '" . (isset($data['notify']) ? (int)$data['notify'] : 0) . "', comment = '" . $this->db->escape(strip_tags($data['comment'])) . "', date_added = '".$data['date']."'");

                $order_info = $this->getOrder($order_id);

                // Send out any gift voucher mails
                if ($this->config->get('config_complete_status_id') == $data['order_status_id']) {
                        $this->load->model('sale/voucher');

                        $results = $this->getOrderVouchers($order_id);

                        foreach ($results as $result) {
                                $this->model_sale_voucher->sendVoucher($result['voucher_id']);
                        }
                }

                                if ($data['notify']) {
                        $language = new Language($order_info['language_directory']);
                        $language->load($order_info['language_filename']);
                        $language->load('mail/order');

                        $subject = sprintf($language->get('text_subject'), $order_info['store_name'], $order_id);

                        $message  = $language->get('text_order') . ' ' . $order_id . "\n";
                        $message .= $language->get('text_date_added') . ' ' . date($language->get('date_format_short'), strtotime($order_info['date_added'])) . "\n\n";

                        $order_status_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "order_status WHERE order_status_id = '" . (int)$data['order_status_id'] . "' AND language_id = '" . (int)$order_info['language_id'] . "'");

                        if ($order_status_query->num_rows) {
                                $message .= $language->get('text_order_status') . "\n";
                                $message .= $order_status_query->row['name'] . "\n\n";
                        }

                        if ($order_info['customer_id']) {
                                $message .= $language->get('text_link') . "\n";
                                $message .= html_entity_decode($order_info['store_url'] . 'index.php?route=account/order/info&order_id=' . $order_id, ENT_QUOTES, 'UTF-8') . "\n\n";
                        }

                        if ($data['comment']) {
                                $message .= $language->get('text_comment') . "\n\n";
                                $message .= strip_tags(html_entity_decode($data['comment'], ENT_QUOTES, 'UTF-8')) . "\n\n";
                        }

                        $message .= $language->get('text_footer');

                        $mail = new Mail();
                        $mail->protocol = $this->config->get('config_mail_protocol');
                        $mail->parameter = $this->config->get('config_mail_parameter');
                        $mail->hostname = $this->config->get('config_smtp_host');
                        $mail->username = $this->config->get('config_smtp_username');
                        $mail->password = $this->config->get('config_smtp_password');
                        $mail->port = $this->config->get('config_smtp_port');
                        $mail->timeout = $this->config->get('config_smtp_timeout');
                        $mail->setTo($order_info['email']);
                        $mail->setFrom($this->config->get('config_email'));
                        $mail->setSender($order_info['store_name']);
                        $mail->setSubject(html_entity_decode($subject, ENT_QUOTES, 'UTF-8'));
                        $mail->setText(html_entity_decode($message, ENT_QUOTES, 'UTF-8'));
                        $mail->send();
                }
        }
}
    if (isset($_POST['xml'])) {
        $xml = str_replace(" ", "+", $_POST['xml']);
        $xml = base64_decode($xml);
        file_put_contents(ROOT . '/tmp/ohistory.gz', $xml);
        $xml = gzfile_get_contents(ROOT . '/tmp/ohistory.gz');

        $xml = xml_parser($xml, 'ORDERS_HISTORY', 'RECORD', array(
            'ORDER_ID',
            'STATUS_ID',
            'DATE',
            'COMMENT',
        ));
    }
    if ($xml) {
        $old_order_id = "";
       //$config = mysql_assoc('SELECT * FROM `setting`', 'key', 'value');
    //1.5	$language_config = mysql_assoc('SELECT * FROM `language`', 'code', 'language_id');
        $statuses = mysql_assoc('SELECT * FROM `' . DB_PREFIX . 'order_status`', 'order_status_id', 'name');

        $new_statuses = array();

        foreach ($xml as $ostatus) {
            if ($ostatus['ORDER_ID'] != "") {
                // чтоб каждый раз не получать один и тот же заказ
                if ($old_order_id != $ostatus['ORDER_ID']) {
                    // получаем заказ
                    $order = mysql_row('SELECT * FROM `' . DB_PREFIX . 'order` WHERE `order_id` = ' . (int)$ostatus['ORDER_ID']);
                    // делаем массив с новыми статусами
                }
                if ($order) {
                    if ($ostatus['STATUS_ID'] != "") {
                        $data = array();

                        $data['order_status_id'] = $ostatus['STATUS_ID'];
                        $data['notify'] = "1";
                        $data['comment'] = toUTF8($ostatus['COMMENT']);
                        $data['date'] = toUTF8($ostatus['DATE']);
                        //1.5
                        $ModelSaleOrder = new ModelSaleOrderAutosoft($registry);
                        $ModelSaleOrder->addOrderHistory($ostatus['ORDER_ID'], $data);
                    }

                    $old_order_id = $ostatus['ORDER_ID'];
                }
            }
        }
    }
?>