<?php

require_once 'users.class.php';
require_once 'client.class.php';
require_once 'tasktype.class.php';

class Tax {

    public function __construct() {
        $this->_db = db::getInstance();
    }

    public function save($taxdata) {
        $this->_db->where('tax_name',$taxdata['tax_name']);
        $match = $this->_db->getOne('tms_tax');
        if($match){
                $return['status'] = 422;
                $return['msg'] = 'Tax name already exists.'; 
        }else{
            $taxdata['created_date'] = date('Y-m-d H:i:s');
            $taxdata['modified_date'] = date('Y-m-d H:i:s');
            $id = $this->_db->insert('tms_tax', $taxdata);
            if ($id) {
                $return['status'] = 200;
                $return['msg'] = 'Successfully Inserted.';
            } else {
                $return['status'] = 422;
                $return['msg'] = 'Not Saved.';
            }
        }
        return $return;
    }
    public function getAlltax() {
        $this->_db->where('is_active',1);
        $data = $this->_db->get('tms_tax');
        return $data; 
    }
    public function getAlltaxList() {
        $this->_db->where('is_active','1');
        $data = $this->_db->get('tms_tax');
        return $data; 
    }
    public function getTaxById($id) {
    	$this->_db->where('tax_id',$id);
        $data = $this->_db->getOne('tms_tax');
    	return $data; 
    }
    public function gettaxstatusU($id) {
    	$this->_db->where('tax_id',$id);
    	$id = $this->_db->getone('tms_tax');
    	return $id;
    }
    public function updateTaxStatus( $id, $data ) {
        $this->_db->where('tax_name',$data['tax_name']);
        $match = $this->_db->getOne('tms_tax');
        if($match && $match['tax_id'] != $id){
                $return['status'] = 422;
                $return['msg'] = 'Tax name already exists.'; 
        }else{
        	//$data['created_date'] = date('Y-m-d H:i:s');
        	$data['modified_date'] = date('Y-m-d H:i:s');
        	$this->_db->where('tax_id',$id);
        	$id = $this->_db->update('tms_tax', $data);
        	//echo $this->_db->getLastQuery();exit;
        	if ($id) {
        		$return['status'] = 200;
        		$return['msg'] = 'Successfully Updated.';
        	} else {
        		$return['status'] = 422;
        		$return['msg'] = 'Not Updated.';
        	}
        }
    	return $return;
    }
    public function deleteTax($id) {
        $this->_db->where('vTextType',$id);
        $data = $this->_db->getOne('tms_client');
        if($data){
            $return['status'] = 422;
            $return['msg'] = 'Record already in use.';
        }else{
            $this->_db->where('tax_id',$id);
            $id = $this->_db->delete('tms_tax');
            if ($id) {
                $return['status'] = 200;
                $return['msg'] = 'Successfully Delete.';
            } else {
                $return['status'] = 422;
                $return['msg'] = 'Not Deleted.';
            }
        }
        return $return;
    }
    
    public function itemAlltaxget() {
        $this->_db->where('is_active',1);
        return $this->_db->get('tms_tax');
    }
}
