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
    file_put_contents(ROOT . '/tmp/ostatuses.gz', $xml);
	$xml = gzfile_get_contents(ROOT . '/tmp/ostatuses.gz');

	$xml = xml_parser($xml, 'ORDER_STATUSES', 'RECORD', array(
		'ID',
		'NAME',
		'DEFAULT',
	));

	$config = mysql_assoc('SELECT * FROM `' . DB_PREFIX . 'setting`', 'key', 'value');
	$language_config = mysql_assoc('SELECT * FROM `' . DB_PREFIX . 'language`', 'code', 'language_id');

	mysql_query('TRUNCATE `' . DB_PREFIX . 'order_status`');

	foreach ($xml as $_k => $ostatus)
	{
		mysql_query(
			'INSERT INTO
				`' . DB_PREFIX . 'order_status`
			SET
				`order_status_id` = ' . (int)$ostatus['ID'] . ',
				`language_id` = "' .  $language_config [ $config['config_language'] ] . '",
				`name` = "' . toUTF8($ostatus['NAME']) . '"'

		);

		if ($ostatus['DEFAULT'] == 1)
		{
			mysql_query('UPDATE `' . DB_PREFIX . 'order_status` SET `value` = ' . (int)$ostatus['ID'] . ' WHERE `key` = "config_order_status_id"');
		}
	}
?>