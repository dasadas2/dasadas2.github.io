<?php
    function xml_parser($xml, $group, $element, $fields = array())
    {
        preg_match_all( '#<'.$element.'>(.*?)</'.$element.'>#is',  $xml, $elements );

        if ($elements[1]) {
            if ($fields) {
              $_return = array();
              foreach( $elements[1] as $element_data )
              {
                  $_elem = array();
                  foreach ($fields as $field_name) {
                     preg_match_all( '#<'.$field_name.'>(.*?)</'.$field_name.'>#si', $element_data, $field_value );
                     $_elem[$field_name] = '';
                     if ($field_value[1]) {
                       if (isset($field_value[1][0])) {
                          $_elem[$field_name] = $field_value[1][0];
                       }
                     }
                  }
                  $_return[]  = $_elem;
              }
            } else {
              $_return = $elements[1][0];
            }
        } else {
              $_return = false;
        }

        return $_return;
    }
?>