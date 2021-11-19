<?php

require_once 'users.class.php';
require_once 'client.class.php';
require_once 'tasktype.class.php';
require_once 'currency.class.php';
require_once 'order.class.php';

class price {
	//price1 data manage
    public function __construct() {
        $this->_db = db::getInstance();
    }

    public function price1Save($data) {
        $data['created_date'] = date('Y-m-d H:i:s');
        $data['updated_date'] = date('Y-m-d H:i:s');
        $id = $this->_db->insert('tms_price1', $data);
        if ($id) {
            $return['status'] = 200;
            $return['msg'] = 'Successfully Inserted.';
        } else {
            $return['status'] = 422;
            $return['msg'] = 'Not Saved.';
        }
        return $return;
    }
    public function getAllprice1()
    {
    	$data = $this->_db->get('tms_price1');
    	return $data;
    }
    public function price1getone($id)
    {
    	$this->_db->where('priceId',$id);
    	$data = $this->_db->getone('tms_price1');
    	return $data;
    }
    public function price1Update($id,$data){
    	//$data['created_date'] = date('Y-m-d H:i:s');
    	$data['updated_date'] = date('Y-m-d H:i:s');
    	$this->_db->where('priceId',$id);
    	$id = $this->_db->update('tms_price1', $data);
    	if ($id) {
    		$return['status'] = 200;
    		$return['msg'] = 'Successfully Updated.';
    	} else {
    		$return['status'] = 422;
    		$return['msg'] = 'Not Updated.';
    	}
    	return $return;
    }
    
    public function price1Delete($id){
    	$this->_db->where('priceId',$id);
    	$id = $this->_db->delete('tms_price1');
    	if ($id) {
    		$return['status'] = 200;
    		$return['msg'] = 'Successfully Deleted.';
    	} else {
    		$return['status'] = 422;
    		$return['msg'] = 'Not Deleted.';
    	}
    	return $return;
    }
    
    //price2 data manage
    public function price2Servicelist(){
    	$data = $this->_db->get('tms_service');
    	return $data; 
    }
    public function price2Save($data) {
    	$data['created_date'] = date('Y-m-d H:i:s');
    	$data['update_date'] = date('Y-m-d H:i:s');
    	$id = $this->_db->insert('tms_price2', $data);
    	if ($id) {
    		$return['status'] = 200;
    		$return['msg'] = 'Successfully Inserted.';
    	} else {
    		$return['status'] = 422;
    		$return['msg'] = 'Not Saved.';
    	}
    	return $return;
    }
    public function price2get()
    {
    	$data = $this->_db->get('tms_price2');
    	return $data;
    }
    public function price2getone($id)
    {
    	$this->_db->where('priceId',$id);
    	$data = $this->_db->getone('tms_price2');
    	return $data;
    }
    //Array ( [priceId] => 2 [s_name] => angular [price_name] => abc [unit] => asdf [sort_code] => 1234 [is_active] => 1 [created_date] => 2016-01-13 16:53:23 [update_date] => 2016-01-13 16:53:23 )
    public function price2Update($id,$data){
		$serviceName  = $data['s_name'];
		$priceName  = $data['price_name'];
		$unit  = $data['unit'];
		$sortCode  = $data['sort_code'];
		$is_active = $data['is_active'];
    	$id = $this->_db->rawQuery("UPDATE `kanhawhp_tms`.`tms_price2` SET `s_name` = '$serviceName', `price_name` = '$priceName', `unit` = '$unit', `sort_code` = '$sortCode', `is_active` = '$is_active' WHERE `tms_price2`.`priceId` = '$id'");
    	if ($id) {
    		$return['status'] = 200;
    		$return['msg'] = 'Successfully Updated.';
    	} else {
    		$return['status'] = 422;
    		$return['msg'] = 'Not Updated.';
    	}
    	return $return;
    }
    public function price2Delete($id){
    	$this->_db->where('priceId',$id);
    	$id = $this->_db->delete('tms_price2');
    	if ($id) {
    		$return['status'] = 200;
    		$return['msg'] = 'Successfully Deleted.';
    	} else {
    		$return['status'] = 422;
    		$return['msg'] = 'Not Deleted.';
    	}
    	return $return;
    }

    public function masterPriceitemget($id){
        $this->_db->where('is_active',1);
        return $this->_db->get('tms_master_price');
    }

    public function childPriceitemget(){
        $this->_db->where('is_active',1);
        return $this->_db->get('tms_child_price_unit');   
    }

    public function childPriceactiveget() {
        $this->_db->where('is_active',1);
        $data = $this->_db->get('tms_child_price_unit');
        return $data;
    }

    public function childPriceitemAmountget($id) {
        $this->_db->where('child_price_id', $id);
        $this->_db->where('is_active',1);
        $data = $this->_db->getOne('tms_child_price_unit');
        return $data;
    }

    public function masterPriceitemgetFromPriceList() {
        $this->_db->where('is_active', 1);
        $data = $this->_db->get('tms_master_price');
        if($data){
            foreach ($data as $key => $value) {
                $Id = $value['master_price_id'];
                $rawQuery = "select count(child_price_id) As childTotal from tms_child_price_unit where master_price_id ={$Id}";
                $exeQueryMatch = $this->_db->rawQuery($rawQuery);
                $data[$key]['childCount'] = $exeQueryMatch[0]['childTotal'];
            }
        }
        return  $data;
    }

}
