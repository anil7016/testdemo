<?php

require_once 'users.class.php';
require_once 'client.class.php';
require_once 'tasktype.class.php';

class Rounding {
	//price1 data manage
    public function __construct() {
        $this->_db = db::getInstance();
    }

    public function Save($data) {
        $this->_db->where('rounding_name',$data['rounding_name']);
        $match = $this->_db->getOne('tms_rounding');
        if($match){
            $result['status'] = 422;
            $result['msg'] = 'Name already exists.'; 
        }else{
            $data['created_date'] = date('Y/m/d H:i:s');
            $data['modified_date'] = date('Y/m/d H:i:s');
            $id = $this->_db->insert('tms_rounding', $data);
            if($id) {
                $result['status'] = 200;
                $result['msg'] = "Inserted Succefully";
            } else {
                $result['status'] = 401;
                $result['msg'] = "Not Inserted";
            }
        }
    	
    	return $result;
    }

    public function roundingPriceGetAll() {
    	//$this->_db->where('is_active', 1);
    	$data = $this->_db->get('tms_rounding');
    	return $data;
    }

    public function roundingPriceGetOne($id) {
        $this->_db->where('rounding_id', $id);
        $data = $this->_db->getOne('tms_rounding');
        return $data;
    }
    public function deleteRoundingprice($id) {
    	$this->_db->where('rounding_id', $id);
    	$data = $this->_db->delete('tms_rounding');
    	return $data;
    }

    public function roundingPriceUpdate($id, $data) {
        $this->_db->where('rounding_name',$data['rounding_name']);
        $match = $this->_db->getOne('tms_rounding');
        if($match && $match['rounding_id'] !=$id){
            
            $result['status'] = 422;
            $result['msg'] = 'Name already exists.'; 
        }else{
            $data['created_date'] = date('Y/m/d H:i:s');
            $data['modified_date'] = date('Y/m/d H:i:s');
            $this->_db->where('rounding_id', $id);
            $id = $this->_db->update('tms_rounding', $data);
            if($id) {
                $result['status'] = 200;
                $result['msg'] = "Updated Successfully";
            } else {
                $result['status'] = 401;
                $result['msg'] = "Not Updated";
            }
        }
    	
    	return $result;
    }
}