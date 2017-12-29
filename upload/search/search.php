<?php

    $username  = 'alys2007';
    $password  = 'lav353';
    $number    = '051103101';

    if (isset($_POST['number'])) {
        $number = $_POST['number'];
    } else {
        $number = '';
    }

    $request  = 'usr_login=' . $username;
    $request .= '&usr_passwd=' . $password;
    $request .= '&Number=' . $number;
    $request .= '&Currency=USD';

    echo '<a href="http://im-autosoft.ru/search">Back</a>' . '<br />';

    checkNumber($request);

    function checkNumber($request) {

        $curl = curl_init('http://tehnomir.com.ua/ws/xml.php?act=GetPrice');

    	curl_setopt($curl, CURLOPT_POST, false);
    	curl_setopt($curl, CURLOPT_POSTFIELDS, $request);
    	curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    	curl_setopt($curl, CURLOPT_HEADER, false);
    	curl_setopt($curl, CURLOPT_TIMEOUT, 30);
    	curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);

      	$result = curl_exec($curl);
        curl_close($curl);

		$xml2arr = new XmlToArray($result);
        $data = $xml2arr->XmlToArray($result);
        $array = $xml2arr->createArray();

        $items = $array['GetPrice']['Detail'];

        echo '
        <html xmlns="http://www.w3.org/1999/xhtml">
        <head>
            <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
            <title>Поиск по базе tehnomir.com.ua</title>
        </head>
        <body>
        ';

        echo '<pre>';
        //print_r($items);
        echo '</pre>';

        echo '<table>';
        _start_table();
            _start_tr();
                _td_center('Brand');
                _td_center('Supplier');
                _td_center('Number');
                _td_center('Name');
                _td_center('Quantity');
                _td_center('Price');
                _td_center('Weight');
            _end_tr();

        foreach($items as $item) {
            if ($item['Quantity'] <= 0) {
                continue;
            }
            _start_tr();
                _td_left($item['Brand']);
                _td_left($item['SupplierCode']);
                _td_left($item['Number']);
                _td_left($item['Name']);
                _td_center($item['Quantity']);
                _td_center($item['Price'] . ' ' . $item['Currency']);
                _td_center($item['Weight']);
            _end_tr();
        }
        _end_table;

        echo '
        </body>
        </html>
        ';

    }

    function _start_table($value) { echo '<table border="1" cellpadding="5" >'; }
    function _end_table($value) { echo '</table>'; }
    function _start_tr($value) { echo '<tr>'; }
    function _end_tr($value) { echo '</tr>'; }
    function _td($value) { echo '<td>' . $value . '</td>'; }
    function _td_left($value) { echo '<td align="left">' . $value . '</td>'; }
    function _td_center($value) { echo '<td align="center">' . $value . '</td>'; }

    class XmlToArray {
		var $xml = '';
		function XmlToArray($xml) {
            $this->xml = $xml;
		}

		function _struct_to_array($values, &$i) {
			$child = array();
			if (isset($values[$i]['value']))
				array_push($child, $values[$i]['value']);

			while ($i++ < count($values)) {
				switch ($values[$i]['type']) {
					case 'cdata':
						array_push($child, $values[$i]['value']);
						break;

					case 'complete':
						$name = $values[$i]['tag'];
						if (!empty($name)) {
							$child[$name] = ($values[$i]['value']) ? ($values[$i]['value']) : '';
							if (isset($values[$i]['attributes'])) {
								$child[$name] = $values[$i]['attributes'];
							}
						}
						break;

					case 'open':
						$name = $values[$i]['tag'];
						$size = isset($child[$name]) ? sizeof($child[$name]) : 0;
						$child[$name][$size] = $this->_struct_to_array($values, $i);
						break;

					case 'close':
						return $child;
						break;
				}
			}
			return $child;
		}

		function createArray() {
			$xml = $this->xml;
			$values = array();
			$index = array();
			$array = array();
			$parser = xml_parser_create();
			xml_parser_set_option($parser, XML_OPTION_SKIP_WHITE, 1);
			xml_parser_set_option($parser, XML_OPTION_CASE_FOLDING, 0);
			xml_parse_into_struct($parser, $xml, $values, $index);
			xml_parser_free($parser);
			$i = 0;
			$name = $values[$i]['tag'];
			$array[$name] = isset($values[$i]['attributes']) ? $values[$i]['attributes'] : '';
			$array[$name] = $this->_struct_to_array($values, $i);
			return $array;
		}

	}


?>

