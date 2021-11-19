<?php

require_once 'users.class.php';
require_once 'client.class.php';
require_once 'tasktype.class.php';

class service {

    public function __construct() {
        $this->_db = db::getInstance();
    }

    public function serviceSave($data) {
        $this->_db->where('service_name',$data['service_name']);
        $match = $this->_db->getOne('tms_service');
        if($match){
            $return['status'] = 422;
            $return['msg'] = 'Service name exists.'; 
        }else{
            $data['created_date'] = date('Y-m-d H:i:s');
            $data['updated_date'] = date('Y-m-d H:i:s');
            $id = $this->_db->insert('tms_service', $data);
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
    public function serviceget()
    {
    	$data = $this->_db->get('tms_service');
    	return $data;
    }
    public function servicegetone($id)
    {
    	$this->_db->where('sId',$id);
    	$data = $this->_db->getone('tms_service');
    	return $data;
    }
    public function serviceUpdate($id,$data){
        $this->_db->where('service_name',$data['service_name']);
        $match = $this->_db->getOne('tms_service');
        if($match && $match['sId'] != $id){
            $return['status'] = 422;
            $return['msg'] = 'Service name exists.'; 
        }else{
        	//$data['created_date'] = date('Y-m-d H:i:s');
        	$data['updated_date'] = date('Y-m-d H:i:s');
        	$this->_db->where('sId',$id);
        	$id = $this->_db->update('tms_service', $data);
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
    public function serviceDelete($id){
    	$this->_db->where('sId',$id);
    	$id = $this->_db->delete('tms_service');
    	if ($id) {
    		$return['status'] = 200;
    		$return['msg'] = 'Successfully Deleted.';
    	} else {
    		$return['status'] = 422;
    		$return['msg'] = 'Not Deleted.';
    	}
    	return $return;
    	
    }
    public function serviceJobrepresentationGet(){
    	$data = array('1'=>'Automatic','2'=>'Default','3'=>'Project Management');
    	return $data;
    }
    public function servicegetdata(){
        $this->_db->where('is_active',1);
        $data = $this->_db->get('tms_service');
        return $data;   
    }

}
