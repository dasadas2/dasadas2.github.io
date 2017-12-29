<?php
function generateSEO() {
//    require_once('config.php');
    require_once(DIR_SYSTEM . 'library/db.php');

    $db = new DB(DB_DRIVER, DB_HOSTNAME, DB_USERNAME, DB_PASSWORD, DB_DATABASE);

    //$db->query("truncate " . DB_PREFIX . "url_alias");

    // create product alias
    $query_product = $db->query("SELECT * FROM " . DB_PREFIX . "product_description");
    foreach ($query_product->rows as $result) {
        $keyword = title2uri($result['name']);
        addSEO("product_id",$keyword, $result['product_id'],$db );
    }

    // create category alias
    $query_category = $db->query("SELECT * FROM " . DB_PREFIX . "category_description");
    foreach ($query_category->rows as $result) {
        $keyword = title2uri($result['name']);
        addSEO("category_id",$keyword, $result['category_id'],$db );
    }

    // create information alias
    /*
    $query_information = $db->query("SELECT * FROM " . DB_PREFIX . "information_description");
    foreach ($query_information->rows as $result) {
        $keyword = title2uri($result['title']);
        addSEO("information_id",$keyword, $result['information_id'],$db );
    }
     */

    // create manufacturer alias
    $query_manufacturer = $db->query("SELECT * FROM " . DB_PREFIX . "manufacturer");
    foreach ($query_manufacturer->rows as $result) {
        $keyword = title2uri($result['name']);
        addSEO("manufacturer_id",$keyword, $result['manufacturer_id'],$db );
    }
}

function title2uri($sValue) {

    $clean = str_replace(
		array('&', '/', '\\', '"', '+',' '),
		array('-', '-', '-', '-', '-','-'),
		$sValue);
    $clean = rus2translit($clean);
   return $clean;
}

function rus2translit($string)
{
    $converter = array(
        'а' => 'a',   'б' => 'b',   'в' => 'v',
        'г' => 'g',   'д' => 'd',   'е' => 'e',
        'ё' => 'e',   'ж' => 'zh',  'з' => 'z',
        'и' => 'i',   'й' => 'y',   'к' => 'k',
        'л' => 'l',   'м' => 'm',   'н' => 'n',
        'о' => 'o',   'п' => 'p',   'р' => 'r',
        'с' => 's',   'т' => 't',   'у' => 'u',
        'ф' => 'f',   'х' => 'h',   'ц' => 'c',
        'ч' => 'ch',  'ш' => 'sh',  'щ' => 'sch',
        'ь' => "",  'ы' => 'y',   'ъ' => "",
        'э' => 'e',   'ю' => 'yu',  'я' => 'ya',

        'А' => 'A',   'Б' => 'B',   'В' => 'V',
        'Г' => 'G',   'Д' => 'D',   'Е' => 'E',
        'Ё' => 'E',   'Ж' => 'Zh',  'З' => 'Z',
        'И' => 'I',   'Й' => 'Y',   'К' => 'K',
        'Л' => 'L',   'М' => 'M',   'Н' => 'N',
        'О' => 'O',   'П' => 'P',   'Р' => 'R',
        'С' => 'S',   'Т' => 'T',   'У' => 'U',
        'Ф' => 'F',   'Х' => 'H',   'Ц' => 'C',
        'Ч' => 'Ch',  'Ш' => 'Sh',  'Щ' => 'Sch',
        'Ь' => "",  'Ы' => 'Y',   'Ъ' => "",
        'Э' => 'E',   'Ю' => 'Yu',  'Я' => 'Ya',
    );
    return strtr($string, $converter);
}

function addSEO($category, $keyword, $id, $db) {
    /*$category
     * manufacturer_id
     * information_id
     * category_id
     * product_id
     */
    if (!$keyword) return false;

    $query = $category . "=" . (int)$id;
    // Если найден $query & $keyword - уже есть - ничего не делаем.
     $query_double = $db->query("SELECT * FROM " . DB_PREFIX . "url_alias WHERE ( `keyword` = '".$keyword."' or `keyword` = '".$keyword."-".$id."' ) and `query` = '".$query."'");
    if ($query_double->num_rows) {
        // если уже есть этот алиас для этого продукта
        return false;
    }

    //$query_double = $db->query("SELECT * FROM " . DB_PREFIX . "url_alias WHERE `keyword` = '" . $keyword . "' and `query` LIKE '" . $category . "=%'");
    $query_double = $db->query("SELECT * FROM " . DB_PREFIX . "url_alias WHERE `keyword` = '" . $keyword . "'");
    if ($query_double->num_rows) {
        // если уже есть такой keyword в category, тогда добавляем id
        $keyword = $keyword . "-" . $id;

        $query_double2 = $db->query("SELECT * FROM " . DB_PREFIX . "url_alias WHERE `keyword` = '" . $keyword . "'");
        if ($query_double->num_rows) {
            $keyword = $keyword . "-" . mt_rand (1,999999);
        }
    }

    $query_double = $db->query("SELECT * FROM " . DB_PREFIX . "url_alias WHERE `query` = '" . $query . "'");
    if ($query_double->num_rows) {
        // Если есть такая $query обновляем её.
         $db->query("UPDATE `" . DB_PREFIX . "url_alias` SET `keyword` = '".$keyword."' WHERE `query` = '" . $query."'" );
    } else {
        // Если нет такой $query добавляем её.
        $db->query("INSERT INTO " . DB_PREFIX . "url_alias SET query = '". $query. "', `keyword` = '" . $keyword ."'");
    }

}
// test
/*
        date_default_timezone_set('Europe/Moscow');
        include_once(dirname(__FILE__) . '/../../admin/config.php');

        generateSEO();
 */
?>