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


//	mysql_query('TRUNCATE `product`');
//	mysql_query('TRUNCATE `product_to_store`');
//	mysql_query('TRUNCATE `product_to_category`');
//	mysql_query('TRUNCATE `product_description`');

$xml = str_replace(" ", "+", $_POST['xml']);
$xml = base64_decode($xml);
file_put_contents(ROOT . '/tmp/parts.gz', $xml);

$xml = gzfile_get_contents(ROOT . '/tmp/parts.gz');

//file_put_contents(ROOT . '/tmp/parts_'.date('Y-m-d_hia').'.xml', $xml);

$xml = xml_parser($xml, 'PART', 'RECORD', array(
        'ID',
        'IDPARTTREE',
        'NAME',
        'FIRM',
        'ID_FIRM',
        'COUNTRY',
        'NUMBER',
        'MANUFACTURECODE',
        'ARTICUL',
        'UNIT',
        'REMARK',

            'META_KEYWORDS',
            'META_DESCRIPTION',

        'CURRENCY',
        'PRICER',
        'PRICEMO',
        'PRICEO',
        'PRICES',
                'CURRENCY_NAME',
                'CURRENCY_ID',
                'CURRENCY_RATE',
                'CURRENCY_PRICER',
                'CURRENCY_PRICEMO',
                'CURRENCY_PRICEO',
                'CURRENCY_PRICES',
        'LINKS',
        'STATUS',
        'COUNT',
        'IMAGES',
));

$config = mysql_assoc('SELECT * FROM `' . DB_PREFIX . 'setting`', 'key', 'value');
//$weight_config = mysql_assoc('SELECT * FROM `weight_class_description`', 'unit', 'weight_class_id');
//$length_config = mysql_assoc('SELECT * FROM `length_class_description`', 'unit', 'length_class_id');
$language_config = mysql_assoc('SELECT * FROM `' . DB_PREFIX . 'language`', 'code', 'language_id');

db_query( (isset($config['autosoft_last_update']) ? 'UPDATE' : 'INSERT INTO') . ' `' . DB_PREFIX . 'setting` SET `key` = "autosoft_last_update", `value` = ' . time() . (isset($config['autosoft_last_update']) ? ' WHERE `key` = "autosoft_last_update"' : '') );

$currencies = Array();

    foreach ($xml as $_k => $product) {
        $product_image = false;

        if ($productImages = mysql_assoc('SELECT * FROM `' . DB_PREFIX . 'product_image` WHERE `product_id` = ' . (int)$product['ID'], 'product_image_id', 'image')) {
                foreach ($productImages as $productImage) {
                        if (file_exists(IMAGES . $productImage))
                                unlink(IMAGES . $productImage);
                }
        }

        mysql_query('DELETE FROM `' . DB_PREFIX . 'product_image` WHERE `product_id` = ' . (int)$product['ID'] );
        mysql_query('UPDATE `' . DB_PREFIX . 'product` SET `image` = "" WHERE `product_id` = ' . (int)$product['ID'] );

        if ($product['STATUS'] == 'D') {
            mysql_query('DELETE FROM `' . DB_PREFIX . 'product` WHERE `product_id` = ' . (int)$product['ID'] );
            mysql_query('DELETE FROM `' . DB_PREFIX . 'product_to_store` WHERE `product_id` = ' . (int)$product['ID'] );
            mysql_query('DELETE FROM `' . DB_PREFIX . 'product_to_category` WHERE `product_id` = ' . (int)$product['ID'] );
            mysql_query('DELETE FROM `' . DB_PREFIX . 'product_description` WHERE `product_id` = ' . (int)$product['ID'] );
        } else {
            preg_match_all('#<IMAGE>(.*?)</IMAGE>#is', $product['IMAGES'], $images);

            if (!empty($images[1])) {
                $images = $images[1];

                $k = 0;
                foreach ($images as $image) {
                    if (file_exists(TMP_IMAGES . $image)) {
                        if (!empty($product['NUMBER'])) {
                            $new_name = toUTF8($product['NUMBER']);
                        } else {
                            $new_name = $product['ID'];
                        }

                        $new_name  = str_replace(array(',','.',' '), array('','','-'), $new_name);
                        if ($k++) {
                            $new_name .= '-' . (string)$k;
                        }
                        $new_name .= '.jpg';

                        //$new_name = $product['ID'] . '.jpg';
                        if ($product_image === false) {
                            $product_image = 'data/product/' . $new_name;
                        }

                        copy(TMP_IMAGES . $image, IMAGES . $new_name);
                        mysql_query('INSERT INTO `' . DB_PREFIX . 'product_image` SET  `product_id` = ' . (int)$product['ID'] . ', `image` = "data/product/' . $new_name . '"');
                    }
                }
            }

            $stock_status = $config['config_stock_status_id'];

            if ($product['COUNT'] == 0) {
                $stock_status = NOSTOCK_ID;
            }

            if ($product['COUNT'] == -1) {
                $stock_status = PREORDER_ID;
            }

            if ($product['COUNT'] == -2) {
                $stock_status = DAYS23_ID;
            }

            if(!mysql_single('SELECT product_id FROM ' . DB_PREFIX . 'product WHERE `product_id` = ' . (int)$product['ID'])) {
                $start_sql = 'INSERT INTO';
                $end_sql   = '';
            } else {
                $start_sql = 'UPDATE';
                $end_sql   = 'WHERE `product_id` = ' . (int)$product['ID'];
            }

                mysql_query(
                        $start_sql .
                        ' `' . DB_PREFIX . 'product` '.
                        'SET '.
                        '`sku` = "",'.
                        '`product_id` = ' . (int)$product['ID'] . ','.
                        '`image` = "' . ($product_image ? $product_image : PRODUCT_IMAGE) . '",'.
                        '`price` = "' . $product['CURRENCY_PRICER'] . '",'.
                        '`currency_code` = "' . $product['CURRENCY_NAME'] . '",'.
                        '`manufacturer_id` = "' . $product['ID_FIRM'] . '",'.
                        '`minimum` = 1,'.
                        '`stock_status_id` = ' . $stock_status . ','.
                        '`status` = 1,'.
                        '`quantity` = ' . (int)$product['COUNT'] . ','.
                        '`weight_class_id` = ' . $config['config_weight_class_id'] . ','.
                        '`length_class_id` = ' . $config['config_length_class_id'] . ','.
                        '`model` = "' . toUTF8($product['NUMBER']) . '"' .
                        $end_sql
                );

                if(!mysql_single('SELECT product_id FROM ' . DB_PREFIX . 'product_to_store WHERE `product_id` = ' . (int)$product['ID'])) {
                    $start_sql = 'INSERT INTO';
                    $end_sql = '';
                } else {
                    $start_sql = 'UPDATE';
                    $end_sql = 'WHERE `product_id` = ' . (int)$product['ID'];
                }

                mysql_query(
                    $start_sql . '
                        `' . DB_PREFIX . 'product_to_store`
                    SET
                        `product_id` = ' . (int)$product['ID'] . ',
                        `store_id` = 0 ' .
                    $end_sql
                );

                if(!mysql_single('SELECT product_id FROM ' . DB_PREFIX . 'product_to_category WHERE `product_id` = ' . (int)$product['ID'])) {
                        $start_sql = 'INSERT INTO';
                        $end_sql = '';
                } else {
                        $start_sql = 'UPDATE';
                        $end_sql = 'WHERE `product_id` = ' . (int)$product['ID'];
                }

                mysql_query(
                    $start_sql . '
                        `' . DB_PREFIX . 'product_to_category`
                    SET
                        `product_id` = ' . (int)$product['ID'] . ',
                        `category_id` = ' . (int)$product['IDPARTTREE'] .
                    $end_sql
                );

                $text =  array();

//			if ($product['FIRM'])
//				$text[] = '<b>Производитель:</b><br>' . toUTF8($product['FIRM']);
//
//			if ($product['COUNTRY'])
//				$text[] = '<b>Страна производства:</b><br>' . toUTF8($product['COUNTRY']);
//
                if ($product['MANUFACTURECODE'])
                        $text[] = '<b>Код производителя:</b><br>' . toUTF8($product['MANUFACTURECODE']);

                if ($product['ARTICUL'])
                        $text[] = '<b>Артикул:</b><br>' . toUTF8($product['ARTICUL']);

                if ($product['REMARK'])
                        $text[] =  toUTF8($product['REMARK']);

                if(!mysql_single('SELECT product_id FROM ' . DB_PREFIX . 'product_description WHERE `product_id` = ' . (int)$product['ID'])) {
                        $start_sql = 'INSERT INTO';
                        $end_sql = '';
                } else {
                        $start_sql = 'UPDATE';
                        $end_sql = 'WHERE `product_id` = ' . (int)$product['ID'];
                }

                mysql_query(
                        $start_sql .
                                ' `' . DB_PREFIX . 'product_description` '.
                        'SET '.
                                '`product_id` = ' . (int)$product['ID'] . ','.
                                '`name` = "' . safe(toUTF8($product['NAME'])) . '",'.
                                '`meta_keyword` = "' . safe(toUTF8($product['META_KEYWORDS'])) . '",'.
                                '`meta_description` = "' . safe(toUTF8($product['META_DESCRIPTION'])) . '",'.
                                '`_firm` = "' . safe(toUTF8($product['FIRM'])) . '",'.
                                '`_country` = "' . safe(toUTF8($product['COUNTRY'])) . '",'.
                                '`_mancode` = "' . safe(toUTF8($product['MANUFACTURECODE'])) . '",'.
                                '`_articul` = "' . safe(toUTF8($product['ARTICUL'])) . '",'.
                                '`description` = "' . safe(implode('<br><br>', $text)) . '",'.
                                '`language_id` = ' . $language_config [ $config['config_language'] ] .' '.
                        $end_sql
                );

                // currencies
                if (isset($product['CURRENCY_ID'])) {
                    if ($product['CURRENCY_ID'] && $product['CURRENCY_NAME']) {
                        if (!isset($currencies[$product['CURRENCY_ID']])) {
                                $currencies[$product['CURRENCY_ID']] = Array();
                                $currencies[$product['CURRENCY_ID']]['code'] = safe(toUTF8($product['CURRENCY_NAME']));
                                $currencies[$product['CURRENCY_ID']]['value'] = $product['CURRENCY_RATE'];
                        }
                    }
                }
        }
}
if (count($currencies) > 0) {
        foreach ($currencies as $currency) {
                if ($currency['code']) {
                        $currency_id = mysql_single('SELECT currency_id FROM ' . DB_PREFIX . 'currency WHERE `code` = \'' . $currency['code'] . '\'');
                        if(!$currency_id) {
                               $start_sql = 'INSERT INTO';
                               $end_sql = '';
                               mysql_query(
                                $start_sql . '
                                        `' . DB_PREFIX . 'currency`
                                SET
                                        `value` = 0,
                                        `date_modified` = NOW()' .
                                $end_sql
                        );
                       }/* disabled
                        else {
                               $start_sql = 'UPDATE';
                               $end_sql = 'WHERE `code` = \'' . $currency['code'].'\'';
                       }
                       // currency rate
                        mysql_query(
                                $start_sql . '
                                        `' . DB_PREFIX . 'currency`
                                SET
                                        `value` = ' . $currency['value'] . ',
                                        `date_modified` = NOW()' .
                                $end_sql
                        );
                       */
                        // default currency
                        /* disabled
                        if ($currency['value'] == 1) {
                            db_query( (isset($config['config_currency_auto']) ? 'UPDATE' : 'INSERT INTO') . ' `' . DB_PREFIX . 'setting` SET `key` = "config_currency_auto", `value` = ' . $currency_id . (isset($config['config_currency_auto']) ? ' WHERE `key` = "config_currency_auto"' : '') );
                            db_query( (isset($config['config_currency']) ? 'UPDATE' : 'INSERT INTO') . ' `' . DB_PREFIX . 'setting` SET `key` = "config_currency", `value` = \'' . $currency['code'] .'\''. (isset($config['config_currency']) ? ' WHERE `key` = "config_currency"' : '') );
                        }
                        */
                }
        }
}
include_once(ROOT . '/lib/seo_urls.php');
generateSEO();
?>