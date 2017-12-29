<?php
	include('config.php');

	include_once(ROOT . '/lib/mysqlll.lib.php');
	include_once(ROOT . '/lib/xml.lib.php');
	include_once(ROOT . '/lib/base.lib.php');

    /*--- check password ---*/
    include_once(ROOT . '/lib/user.php');

    $username = $_GET['login'];
    $password = $_GET['password'];
    
    $where_product="";
    //$where_product=" where product_id<10000000";

    if (empty($username) && empty($password)) {
        $username = $_POST['login'];
        $password = $_POST['password'];
    }

    if (!checkLogin($username, $password)) {
        return;
    }
    /*--- check password ---*/

    if (file_exists(IMAGES)) {
       // echo IMAGES . ' exist.<br />';
    } else {
        mkdir(IMAGES, 0777, true);
    }
    if (file_exists(IMAGES . '/groups/')) {
        //echo IMAGES . '/groups/' . ' exist.<br />';
    } else {
        mkdir(IMAGES . '/groups', 0777, true);
    }

    $iterator = new DirectoryIterator(IMAGES);
    foreach($iterator as $file) {
        //echo 'delete ' . $file->getPathname() . '<br />';
        if(!$file->isDot()){
            unlink($file->getPathname());
        }
    }

    $iterator = new DirectoryIterator(IMAGES . '/groups/');
    foreach($iterator as $file) {
        //echo 'delete ' . $file->getPathname() . '<br />';
        if(!$file->isDot()){
            unlink($file->getPathname());
        }
    }

    $iterator = new DirectoryIterator(tmp);
    foreach($iterator as $file) {
        //echo 'delete ' . $file->getPathname() . '<br />';
        if(!$file->isDot()){
            unlink($file->getPathname());
        }
    }

    $iterator = new DirectoryIterator(tmp . '/images/');
    foreach($iterator as $file) {
        //echo 'delete ' . $file->getPathname() . '<br />';
        if(!$file->isDot()){
            unlink($file->getPathname());
        }
    }

	file_put_contents(IMAGES . '/index.html', '');
	file_put_contents(IMAGES . '/groups/index.html', '');

	mysql_query('TRUNCATE `' . DB_PREFIX . 'manufacturer`');
	mysql_query('TRUNCATE `' . DB_PREFIX . 'manufacturer_to_store`');

	mysql_query('DELETE FROM `' . DB_PREFIX . 'product` '.$where_product);
	mysql_query('DELETE FROM `' . DB_PREFIX . 'product_to_store`'.$where_product);
	mysql_query('DELETE FROM `' . DB_PREFIX . 'product_to_category`'.$where_product);
	mysql_query('DELETE FROM `' . DB_PREFIX . 'product_description`'.$where_product);
	mysql_query('DELETE FROM `' . DB_PREFIX . 'product_image`'.$where_product);


	mysql_query('TRUNCATE `' . DB_PREFIX . 'category`');
	mysql_query('TRUNCATE `' . DB_PREFIX . 'category_description`');
	mysql_query('TRUNCATE `' . DB_PREFIX . 'category_path`');
	mysql_query('TRUNCATE `' . DB_PREFIX . 'category_to_layout`');
	mysql_query('TRUNCATE `' . DB_PREFIX . 'category_to_store`');

	mysql_query('TRUNCATE `' . DB_PREFIX . 'order_status`');
    mysql_query('TRUNCATE `' . DB_PREFIX . 'url_alias`');

    echo 'Очистка товаров интернет магазина успешно произведена!';

?>