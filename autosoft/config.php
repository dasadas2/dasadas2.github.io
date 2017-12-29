<?php
	//date_default_timezone_set('Asia/Yekaterinburg');
        date_default_timezone_set('Europe/Moscow');

	define( 'ROOT', dirname(__FILE__));
	define( 'TMP_IMAGES', ROOT . '/tmp/images/' );
	define( 'IMAGES', ROOT . '/../image/data/product/' );
	define( 'MAN_IMAGES', ROOT . '/../image/data/man/' );

	//include_once(ROOT . '/../config.php');
        include_once(ROOT . '/../admin/config.php');

    define('PRODUCT_IMAGE', "no_image1.jpg");
    define('NOSTOCK_ID', 5);
    define('PREORDER_ID', 8);
    define('DAYS23_ID', 6);

    ///////////////////////////////////////////////

	mysql_connect( DB_HOSTNAME, DB_USERNAME, DB_PASSWORD );
	mysql_select_db( DB_DATABASE );
    mysql_query( 'SET NAMES "utf8"');