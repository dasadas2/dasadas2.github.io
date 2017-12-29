<?php

include('config.php');

include_once(ROOT.'/lib/mysqlll.lib.php');
include_once(ROOT.'/lib/xml.lib.php');
include_once(ROOT.'/lib/base.lib.php');

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



//1.5
include_once (ROOT.'/lib/opencart_init.php');
include_once (ROOT.'/../system/engine/model.php');
include_once (ROOT."/../admin/model/catalog/category.php");

$xml = str_replace(" ", "+", $_POST['xml']);
$xml = base64_decode($xml);
file_put_contents(ROOT.'/tmp/parttree.gz', $xml);
$xml = gzfile_get_contents(ROOT.'/tmp/parttree.gz');

mysql_query('TRUNCATE `' . DB_PREFIX . 'category`');
mysql_query('TRUNCATE `' . DB_PREFIX . 'category_to_store`');
mysql_query('TRUNCATE `' . DB_PREFIX . 'category_description`');

$config = mysql_assoc('SELECT * FROM `' . DB_PREFIX . 'setting`', 'key', 'value');
$language_config = mysql_assoc('SELECT * FROM `' . DB_PREFIX . 'language`', 'code', 'language_id');
$language_id = $language_config [$config['config_language']];

foreach (xml_parser($xml, 'PARTTREE', 'RECORD', array('ID', 'NAME', 'PARENT', 'COMMENT', 'IMAGES')) as $_k => $group) {
    $image = '';

    if ($image = xml_parser($group['IMAGES'], false, 'IMAGE', array())) {
        if (file_exists(TMP_IMAGES.$image))
            copy(TMP_IMAGES.$image, IMAGES.'/groups/'.$image);
    }

    mysql_query('INSERT INTO `' . DB_PREFIX . 'category` SET
			`category_id` = '.(int) $group['ID'].',
			`parent_id` = '.(int) $group['PARENT'].',
                                          `status` = true,
                                          `top` = true,
                                          `column` = 1,
			`image` = "'.($image ? safe('data/groups/'.$image) : '').'"
		');

    mysql_query('INSERT INTO `' . DB_PREFIX . 'category_to_store` SET
			`category_id` = '.(int) $group['ID'].',
			`store_id` = 0

		');
    mysql_query('INSERT INTO `' . DB_PREFIX . 'category_description` SET
			`category_id` = '.(int) $group['ID'].',
			`name` = "'.safe(toUTF8($group['NAME'])).'",
			`description` = "'.safe(toUTF8($group['COMMENT'])).'",
			`language_id` = '.$language_id.'
		');
    //			`name` = "' . toUTF8(safe($group['NAME'])) . '",
}

//1.5
$catClass = new ModelCatalogCategory($registry);
$catClass->repairCategories();
?>