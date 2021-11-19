<?php

require_once 'functions.class.php';
require_once 'mail_format.class.php';

class payment {

    public function __construct() {
        $this->_db = db::getInstance();
    }

    public function save($info) {
        $info['dtCreatedDate'] = date('Y-m-d H:i:s');
        $info['dtUpdatedDate'] = date('Y-m-d H:i:s');
        $id = $this->_db->insert('tms_payment', $info);
        if ($id) {
            $return['status'] = 200;
            $return['msg'] = 'Successfully Inserted.';
        } else {
            $return['status'] = 422;
            $return['msg'] = 'Not Saved.';
        }
        return $return;
    }

    public function getPaymentById($user_id, $type) {
        $this->_db->where('iUserId', $user_id);
        $this->_db->where('iType', $type);
        return $data = $this->_db->getOne('tms_payment');
    }

    public function updatePayment($id, $type, $info) {
        $info['dtUpdatedDate'] = date('Y-m-d H:i:s');
        $this->_db->where('iUserId', $id);
        $this->_db->where('iType', $type);
        $id = $this->_db->update('tms_payment', $info);
        if ($id) {
            $return['status'] = 200;
            $return['msg'] = 'Successfully Updated.';
        } else {
            $return['status'] = 422;
            $return['msg'] = 'Not Saved.';
        }
        return $return;
    }
    public function getuserpaymentdata($id){
    	$this->_db->where('iUserId', $id);
    	$data = $this->_db->getOne('tms_payment');
    	return $data;
    }
    public function getClientpayment($id){
    	$this->_db->where('iClientId', $id);
    	$data = $this->_db->getOne('tms_payment');
    	return $data;
    }
    public function paymentdirectUpdate($id, $type, $info) {
    	$info['dtUpdatedDate'] = date('Y-m-d H:i:s');
    	$this->_db->where('iClientId', $id);
    	$this->_db->where('iType', $type);
    	$id = $this->_db->update('tms_payment', $info);
    	if ($id) {
    		$return['status'] = 200;
    		$return['msg'] = 'Successfully Updated.';
    	} else {
    		$return['status'] = 422;
    		$return['msg'] = 'Not Saved.';
    	}
    	return $return;
    }

    public function getVatcount($countryCode, $vat) {//818624212
        $country1 = $countryCode;
        $vatnum1 = $vat;
        if($country1 == 'NO'){
            //$url = 'http://data.brreg.no/enhetsregisteret/enhet/'.$vatnum1;
            $url = 'https://data.brreg.no/enhetsregisteret/enhet/'.$vatnum1.'.json';
            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_TIMEOUT, 5);
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt( $ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
            $data = curl_exec($ch);
            curl_close($ch);
            
            $return['data'] = json_decode($data);
            $return['From'] = 'norway';
            return $return; 
        }else{//26375245
            //Prepare the URL 
            $url = 'http://ec.europa.eu/taxation_customs/vies/viesquer.do?ms='.$country1.'&iso='.$country1.'&vat='.$vatnum1.'&name=&companyType=&street1=&postcode=&city=&BtnSubmitVat=Verify';
            
            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_TIMEOUT, 5);
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            $data = curl_exec($ch);
            curl_close($ch);
            $return['data'] = $data;
            $return['From'] = 'europa';
            return $return;
        }

    }
       
}
