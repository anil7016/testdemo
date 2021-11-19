<?php

require_once 'users.class.php';
require_once 'client.class.php';
require_once 'tasktype.class.php';

class customergroup {
	//price1 data manage
	    public function __construct() {
	        $this->_db = db::getInstance();
	    }
		
	    public function customerGroupsave($data){
	    	$this->_db->where('group_name',$data['group_name']);
	   		$match = $this->_db->getOne('tms_customer_group');

	        if($match){
	            $return['status'] = 422;
	            $return['msg'] = 'Group name already exists.'; 
	        }else{
		    	$data['created_date'] = date('Y-m-d H:i:s');
		    	$data['updated_date'] = date('Y-m-d H:i:s');
		    	$id = $this->_db->insert('tms_customer_group', $data);
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
	   public function customerGroupGetAll(){
		   	$data = $this->_db->get('tms_customer_group');
	   	 	return $data;
	   }
	   public function customergroupGetdata(){
	   	$this->_db->where('is_active',1);
	   	$data = $this->_db->get('tms_customer_group');
	   	return $data;
	   }
	   public function customerGroupGetOne($id){
		   	$this->_db->where('group_id',$id);
		   	$data = $this->_db->getone('tms_customer_group');
		   	return $data;
	   }
	   
	   public function customerGroupUpdate($id,$data){
	   		$this->_db->where('group_name',$data['group_name']);
	   		$match = $this->_db->getOne('tms_customer_group');
	   		
	   		if($match && $match['group_id'] != $id) {
	   			$return['status'] = 422;
	            $return['msg'] = 'Group name already exists.';
	   		} else {
	   			//$data['created_date'] = date('Y-m-d H:i:s');
			   	$data['updated_date'] = date('Y-m-d H:i:s');
			   	$this->_db->where('group_id',$id);
			   	$id = $this->_db->update('tms_customer_group', $data);
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
	   public function customerGroupDelete($id){
		   	$this->_db->where('group_id',$id);
		   	$id = $this->_db->delete('tms_customer_group');
		   	if ($id) {
		   	   $return['status'] = 200;
		   	   $return['msg'] = 'Successfully Deleted.';
		   	} else {
		   	   $return['status'] = 422;
		   	   $return['msg'] = 'Not Deleted.';
		   	}
		   	return $return;
	   }
}