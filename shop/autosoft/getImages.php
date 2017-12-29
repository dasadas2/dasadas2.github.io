<?php

    //Получим список фотографий, которые есть на сервере
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

    $xml->startElement('images');

    $xml->startElement('imagesGoods');
    foreach (glob(IMAGES."*.*") as $filename) {
        $xml->writeElement('image', basename($filename, ".jpg"));
    }
    $xml->endElement();/** imagesGoods */
    $xml->startElement('imagesGroups');
    foreach (glob(IMAGES."/groups/*.*") as $filename) {
        $xml->writeElement('image', basename($filename, ".jpg"));
    }
    $xml->endElement();/** imagesGroups */
    $xml->endElement();/** images */
?>