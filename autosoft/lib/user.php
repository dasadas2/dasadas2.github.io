<?php

  	function checkLogin($username, $password) {

        if (empty($username) || empty($password)) {
            return false;
        }

        $query = mysql_assoc("SELECT * FROM " . DB_PREFIX . "user WHERE username_md5 = '" . $username . "' AND password_md5 = '" . $password. "' AND status = '1'");

    	if (count($query)) {
      		return true;
    	} else {
            echo 'Access denied.';
      		return false;
    	}
  	}

    // test
    //date_default_timezone_set('Europe/Moscow');
    //include_once(dirname(__FILE__) . '/../../admin/config.php');
    //checkLogin('6ccf929934691710135f3f0df7cc43c5', 'f6112e526e1a6bb0e8f31335d7b1ebad');
    //checkLogin('recovery', 'recovery');

?>