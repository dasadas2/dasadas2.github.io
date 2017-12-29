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
    file_put_contents(ROOT . '/tmp/images.zip', $xml);
    
	$zip = new ZipArchive;
	if ($zip->open(ROOT . '/tmp/images.zip') === true)
	{
		$zip->extractTo(TMP_IMAGES);
    	$zip->close();
	}
?>