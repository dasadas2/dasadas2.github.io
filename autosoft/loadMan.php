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

    $xml = str_replace(" ", "+", $_POST['xml']);
    $xml = base64_decode($xml);
    file_put_contents(ROOT . '/tmp/man.gz', $xml);
	$xml = gzfile_get_contents(ROOT . '/tmp/man.gz');
	//$xml = file_get_contents(ROOT . '/tmp/man.xml');
	

	mysql_query('TRUNCATE `' . DB_PREFIX . 'manufacturer`');
	mysql_query('TRUNCATE `' . DB_PREFIX . 'manufacturer_to_store`');

	foreach (xml_parser($xml, 'DIRFIRMS', 'RECORD', array('ID', 'NAME')) as $_k => $group)
	{
		$image_name="NULL";
		if (file_exists(TMP_IMAGES . "f_".$group['ID'].".jpg")) {
			copy(TMP_IMAGES . "f_".$group['ID'].".jpg", MAN_IMAGES .$group['ID'].".jpg");
			$image_name='data/man/'.$group['ID'].'.jpg';
		}
		
		//echo TMP_IMAGES . "f_".$group['ID'].".jpg";
		
		mysql_query('INSERT INTO `' . DB_PREFIX . 'manufacturer` SET
			`manufacturer_id` = ' . (int)$group['ID'] . ',
			`name` = "' . toUTF8($group['NAME']) . '",
			`image` = "'.$image_name.'"
			
		');

		mysql_query('INSERT INTO `' . DB_PREFIX . 'manufacturer_to_store` SET
			`manufacturer_id` = ' . (int)$group['ID'] . ',
			`store_id` = 0

		');
	}
?>