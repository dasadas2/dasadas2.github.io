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

	$xml = new XMLWriter();
	$xml->openURI('php://output');

	header('Content-Type: text/xml');
	$xml->startDocument('1.0', 'Windows-1251');

	$orders = mysql_assoc('SELECT *, DATE_FORMAT(date_added, "%d.%m.%Y %H:%i") as fdate_added, DATE_FORMAT(date_modified, "%d.%m.%Y %H:%i") as fdate_modified FROM `' . DB_PREFIX . 'order` WHERE `order_status_id` > 0');

	$xml->startElement('orders');
	$xml->writeElement('creation-date', date('Y-m-d H:i:s').' GMT+5');

	foreach ($orders as $order)
	{
		$xml->startElement('order');

		$xml->writeElement('id', $order['order_id']);
		$xml->writeElement('comment', $order['comment']);
		$xml->writeElement('date_create',  $order['fdate_added']);
		$xml->writeElement('date_edit', $order['fdate_modified']);

		$xml->startElement('client');

		$xml->writeElement('id', $order['customer_id']);
		$xml->writeElement('firstname', $order['firstname']);
		$xml->writeElement('lastname', $order['lastname']);
		$xml->writeElement('telephone', $order['telephone']);
		$xml->writeElement('fax', $order['fax']);
		$xml->writeElement('email', $order['email']);
		$xml->writeElement('shipping_firstname', $order['shipping_firstname']);
		$xml->writeElement('shipping_lastname', $order['shipping_lastname']);
		$xml->writeElement('shipping_company', $order['shipping_company']);
		$xml->writeElement('shipping_address_1', $order['shipping_address_1']);
		$xml->writeElement('shipping_address_2', $order['shipping_address_2']);
		$xml->writeElement('shipping_city', $order['shipping_city']);
		$xml->writeElement('shipping_postcode', $order['shipping_postcode']);
		$xml->writeElement('shipping_zone', $order['shipping_zone']);
		$xml->writeElement('shipping_zone_id', $order['shipping_zone_id']);
		$xml->writeElement('shipping_country', $order['shipping_country']);

		$xml->endElement(); /** client */

		$statuses = mysql_assoc('SELECT *, DATE_FORMAT(date_added, "%d.%m.%Y %H:%i") as fdate_added FROM `' . DB_PREFIX . 'order_history` WHERE `order_id` = ' . (int)$order['order_id']);

		$xml->startElement('statuses');

		foreach ($statuses as $status)
		{
			$xml->startElement('status');

			$xml->writeElement('id', $status['order_history_id']);
			$xml->writeElement('status', $status['order_status_id']);
			$xml->writeElement('notify', $status['notify']);
			$xml->writeElement('comment', $status['comment']);
			$xml->writeElement('date', $status['fdate_added']);

			$xml->endElement(); /** status */
		}

		$xml->endElement(); /** status */

		$products = mysql_assoc(
            "SELECT op.*, p.source, p.location, m.name as manufacturer FROM " . DB_PREFIX . "order_product op
            LEFT JOIN " . DB_PREFIX . "product p ON (p.product_id = op.product_id)
            LEFT JOIN " . DB_PREFIX . "manufacturer m ON (m.manufacturer_id = p.manufacturer_id)
            WHERE order_id = '" . (int)$order['order_id'] . "'");

		$xml->startElement('products');

		foreach ($products as $product) {

            $query = "SELECT c.category_id, cd.name FROM " . DB_PREFIX . "category c LEFT JOIN " . DB_PREFIX . "category_description cd ON (c.category_id = cd.category_id) INNER JOIN " . DB_PREFIX . "product_to_category p2c ON (p2c.category_id = c.category_id) WHERE cd.language_id = '1' AND p2c.product_id = " . (int)$product['product_id'] . " ORDER BY c.sort_order, cd.name ASC";
            $cat = mysql_assoc($query);
            $category_name = '';
            if (count($cat)) {
                $category_name = $cat[0]['name'];
            }

			$xml->startElement('product');

			$xml->writeElement('product_id', $product['product_id']);
			$xml->writeElement('name', $product['name']);
			$xml->writeElement('model', $product['model']);
			$xml->writeElement('manufacturecode', $product['model']);
			$xml->writeElement('manufacturer', $product['manufacturer']);
			$xml->writeElement('category', $category_name);
			$xml->writeElement('pricefilename', $product['location']);
			$xml->writeElement('price', $product['price']);
			$xml->writeElement('quantity', $product['quantity']);
			$xml->writeElement('source', $product['source']);

            $currency_code = mysql_single('SELECT currency_code FROM ' . DB_PREFIX . 'product WHERE `product_id` = ' . (int)$product['product_id']);
            $xml->writeElement('currency_name', $currency_code?$currency_code:'');

			$xml->endElement(); /** product */
		}

		$xml->endElement(); /** products */

		$xml->endElement(); /** order */
	}

	$xml->endElement(); /** orders */
?>