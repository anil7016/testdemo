<?php

require_once 'users.class.php';
require_once 'client.class.php';
require_once 'tasktype.class.php';

class center {
	//price1 data manage
		protected $_abbrivation;
	    public function __construct() {
	        $this->_db = db::getInstance();
	        
	    }

	    public function abbrivationMatch($name) {
	    	$this->_abbrivation = $name;
	    	$this->_db->where('abbrivation', $this->_abbrivation);
	    	return $data = $this->_db->getOne('tms_centers');
	    }
	    
	    public function centersave($data){
			$info = self::abbrivationMatch($data['abbrivation']);
			if(!$info) {
				$data['created_date'] = date('Y-m-d H:i:s');
		    	$data['updated_date'] = date('Y-m-d H:i:s');
		    	$id = $this->_db->insert('tms_centers', $data);
		    	if ($id) {
		    	   $return['status'] = 200;
		    	   $return['msg'] = 'Successfully Inserted.';
		    	} else {
		    	  $return['status'] = 422;
		    	  $return['msg'] = 'Not Saved.';
		    	}
		    	return $return;
			}
		}

		public function centerDategetAll(){
			$data = $this->_db->get('tms_centers');
			return $data; 
		}
		
		public function centergetOne($id){
			$this->_db->where('center_id',$id);
			$data = $this->_db->getone('tms_centers');
			return $data;
		}

		public function centerupdate($id,$data){
			$info = self::abbrivationMatch($data['abbrivation']);
			if($id == $info['center_id']) {
				$data['updated_date'] = date('Y-m-d H:i:s');
				$this->_db->where('center_id',$id);
				$id = $this->_db->update('tms_centers', $data);
				if ($id) {
					$return['status'] = 200;
					$return['msg'] = 'Updates Successfully Inserted.';
				} else {
					$return['status'] = 422;
					$return['msg'] = 'Not Updated.';
				}	
			}
			return $return;
		}

		public function deleteCenter($id){
			$this->_db->where('center_id',$id);
			$data = $this->_db->getOne('tms_centers');
			$abbrivation = $data['abbrivation'];
			
			//Checking abbrivation in tms_general table
			$queryMatch = "select company_code from tms_general where company_code like '$abbrivation%'";
			$exeQueryMatch = $this->_db->rawQuery($queryMatch);
			
			//Checking abbrivation in tms_order table
			$queryMatch1 = "select abbrivation from tms_order where abbrivation like '$abbrivation%'";
			$exeQueryMatch1 = $this->_db->rawQuery($queryMatch1);

			//Checking abbrivation in tms_client table
			$queryMatch2 = "select vCodeRights from tms_client where vCodeRights like '$abbrivation%'";
			$exeQueryMatch2 = $this->_db->rawQuery($queryMatch2);
			
			if($exeQueryMatch || $exeQueryMatch1 ||$exeQueryMatch2 ){
				$return['status'] = 422;
				$return['msg'] = 'You can not delete this record.';
			}else{
				$this->_db->where('center_id',$id);
				$data = $this->_db->delete('tms_centers');
				$return['status'] = 200;
				$return['msg'] = 'Successfully Deleted.';
			}
			return $return;
		}
		
		public function centerClientGet(){
			$this->_db->where('is_active','1');
			$data = $this->_db->get('tms_centers');
			return $data;
		}
}