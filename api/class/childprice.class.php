<?php

require_once 'users.class.php';
require_once 'client.class.php';
require_once 'tasktype.class.php';

class childprice {
	//price1 data manage
	    public function __construct() {
	        $this->_db = db::getInstance();
	    }
		
	    public function childpricesave($data){
	    	// same name and project type
			$this->_db->where('name',$data['name']);
    		$this->_db->where('service',$data['service']); 
    		$match = $this->_db->getOne('tms_child_price_unit');
			if($match){
	    			$return['status'] = 422;
            		$return['msg'] = 'Name already exists.';
	    	}else{
	    		$data['created_date'] = date('Y-m-d H:i:s');
		    	$data['updated_date'] = date('Y-m-d H:i:s');
		    	$id = $this->_db->insert('tms_child_price_unit', $data);

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
	   public function childPriceGetAll(){
	   		$query = 'select tcpu.*,tmp.name AS masterPriceName from tms_child_price_unit As tcpu INNER JOIN tms_master_price  AS tmp ON tcpu.master_price_id = tmp.master_price_id ORDER BY tcpu.child_price_id DESC';
	   		$data = $this->_db->rawQuery($query);
	   		/*$this->_db->orderBy("is_active","Desc");
		   	$data = $this->_db->get('tms_child_price_unit');*/
	   	 	return $data;
	   }
	   public function childpriceGetOne($id){
		   	$this->_db->where('child_price_id',$id);
		   	$data = $this->_db->getone('tms_child_price_unit');
		   	return $data;
	   }
	   
	   public function childpriceupdate($id,$data){
			// same name and project type
			$this->_db->where('name',$data['name']);
    		$this->_db->where('service',$data['service']);
    		$match = $this->_db->getOne('tms_child_price_unit');
    		if($match && $match['child_price_id'] !=$id ){
				$return['status'] = 422;
        		$return['msg'] = 'Name already exists.';
    		}else{
			   	$data['updated_date'] = date('Y-m-d H:i:s');
			   	$this->_db->where('child_price_id',$id);
			   	$id = $this->_db->update('tms_child_price_unit', $data);
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
	   public function childPriceDelete($id){
		   	$this->_db->where('child_price_id',$id);
		   	$id = $this->_db->delete('tms_child_price_unit');
		   	if ($id) {
		   	   $return['status'] = 200;
		   	   $return['msg'] = 'Successfully Deleted.';
		   	} else {
		   	   $return['status'] = 422;
		   	   $return['msg'] = 'Not Deleted.';
		   	}
		   	return $return;
	   }
	   public function childpriceDataget(){
	   		$this->_db->where('is_active',1);
	   		$data = $this->_db->get('tms_child_price_unit');
	   		return $data;
	   }
	   public function getAllChildPricesByMasterPrice($masterId){
		   	$this->_db->where('master_price_id',$masterId);
		   	$this->_db->where('is_active',1);
		   	$data = $this->_db->get('tms_child_price_unit');
		   	return $data;
	   }
}