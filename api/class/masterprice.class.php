<?php

require_once 'users.class.php';
require_once 'client.class.php';
require_once 'tasktype.class.php';

class masterprice {
	//price1 data manage
	    public function __construct() {
	        $this->_db = db::getInstance();
	    }
		
	    public function savemasterPrice($data){
	    	$this->_db->where('name',$data['name']);
	        $match = $this->_db->getOne('tms_master_price');
	        if($match){
	            $return['status'] = 422;
	            $return['msg'] = 'Name already exists.'; 
	        }else{
		    	$data['created_date'] = date('Y-m-d H:i:s');
		    	$data['updated_date'] = date('Y-m-d H:i:s');
		    	$id = $this->_db->insert('tms_master_price', $data);
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
	   public function masterPriceGetAll(){
	   		$this->_db->orderBy('master_price_id','DESC');
		   	$data = $this->_db->get('tms_master_price');
	   	 	return $data;
	   }
	   public function masterPriceGetdata(){
	   		$this->_db->where('is_active',1);
	   		$data = $this->_db->get('tms_master_price');
	   		return $data;
	   	
	   }
	   public function masterpriceGetOne($id){
	   		$this->_db->where('master_price_id',$id);
	   	 	$data = $this->_db->getone('tms_master_price');
	   	    return $data;
	   }
	   public function masterpriceupdate($id,$data){
	   		$this->_db->where('name',$data['name']);
	        $match = $this->_db->getOne('tms_master_price');
	        if($match && $match['master_price_id'] != $id){
	            $return['status'] = 422;
	            $return['msg'] = 'Name already exists.'; 
	        }else{
			   	//$data['created_date'] = date('Y-m-d H:i:s');
			   	$data['updated_date'] = date('Y-m-d H:i:s');
			   	$this->_db->where('master_price_id',$id);
			   	$id = $this->_db->update('tms_master_price', $data);
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
	   public function masterPriceDelete($id){
		   	$this->_db->where('master_price_id',$id);
		   	$id = $this->_db->delete('tms_master_price');
		   	if ($id) {
		   	   $return['status'] = 200;
		   	   $return['msg'] = 'Successfully Deleted.';
		   	} else {
		   	   $return['status'] = 422;
		   	   $return['msg'] = 'Not Deleted.';
		   	}
		   	return $return;
	   }
	   public function mastergetPrice(){
	   		$info = array("Character","days","document","fixed rate", "hours","lines","minimum price","minutes","months","pages","paragraphs","segments","terms","units","words","years"); 
	   		return $info;
	   }
}