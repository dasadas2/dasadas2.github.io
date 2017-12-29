<?php
class ControllerModuleBankTransferSimple extends Controller {
    protected function index() {
        $this->language->load('payment/bank_transfer');
        
        $this->data['text_instruction'] = $this->language->get('text_instruction');
        $this->data['text_description'] = $this->language->get('text_description');
        $this->data['text_payment'] = $this->language->get('text_payment');
        
        $this->data['bank'] = nl2br($this->config->get('bank_transfer_bank_' . $this->config->get('config_language_id')));

        if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/module/bank_transfer_simple.tpl')) {
            $this->template = $this->config->get('config_template') . '/template/module/bank_transfer_simple.tpl';
        } else {
            $this->template = 'default/template/module/bank_transfer_simple.tpl';
        }    
        
        $this->render(); 
    }
}
?>