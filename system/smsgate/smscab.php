<?php

//final class SmsCab extends SmsGate {
class SmsCab extends SmsGate {

    public function send() {
        $results = array();


        if(is_array(@$this->ext)) {
            $search = array('{CLIENTFIRSTNAME}', '{CLIENTLASTNAME}', '{FULLITEMS}', '{LITEITEMS}', '{SUMREAL}', '{CITY}', '{ADDRESS}');
            $replace = array($this->ext['order_info']['firstname'], 
                             $this->ext['order_info']['lastname'], 
                             $this->ext['order_info']['fullitems'],
                             $this->ext['order_info']['liteitems'],
                             $this->ext['order_info']['sumreal'],
                             $this->ext['order_info']['shipping_city'],
                             $this->ext['order_info']['shipping_address_1']);
            
            $this->message = str_replace($search, $replace, $this->message);
        }

        if(@$this->username != "") $login = $this->username;
        else $login = @$this->login;
        
        $data = array(
            "login" => $login,
            "password" => $this->password,
            "space_force" => 1,
            "space" => substr(preg_replace("/[^\w\d\. -]+/", "", $this->from), 0, 11),
            "subno" => "%2B".$this->validatePhone($this->to),
            "text" => urlencode($this->message)
        );
        
        if (!is_array(@$this->ext) || @$this->ext['config_sms_alert_admin']>0) {
            $results[] = $this->process($data);
            
            if ($this->copy) {
                $phones = explode(',', str_replace("\n", ',', $this->copy));
                    foreach($phones as $phone) {
                        if(trim($phone) != "") {
                            $data['subno'] = "%2B".$this->validatePhone($phone);
                            $results[] = $this->process($data);                     
                        }
                    }
            }
        }
        
        if (is_array(@$this->ext) && $this->ext['config_sms_alert_client']>0) {
            $phone = $this->ext['order_info']['telephone'];
            $message2client = str_replace($search, $replace, $this->message2client);
            
            if(trim($phone) != "" && $message2client != "") {
                $data['subno'] = "%2B".$this->validatePhone($phone);
                $data['text'] = urlencode($message2client);
                $results[] = $this->process($data);                     
            }
        }
        

        return $results;
    }

    function process($data) {
        
        $get = '';
            foreach($data as $key=>$val) {
                $get .= "$key=$val&";
            }
        $url = 'http://cabinet.smscab.ru/smsout.php?'.$get;

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_FAILONERROR, 1);
        $result = curl_exec($ch);
        curl_close($ch);

        return $result;
    }
    
    
    
    function validatePhone($phone) {
    
        $phone = preg_replace('/\D+/', '', $phone);
        
        # валидачия на мобильный диапазон России 
        $phone = preg_replace('/^8/', '7', $phone);
        if(substr($phone, 0, 1) == 7 && ($phone > 79999999999 || $phone < 79000000000)) return false;

        return $phone;
    }
    
}
?>
