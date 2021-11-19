<?php

require_once 'users.class.php';
require_once 'client.class.php';
require_once 'tasktype.class.php';

class jobchain {
	//price1 data manage
	    public function __construct() {
	        $this->_db = db::getInstance();
	    }
	    
	    public function jobChansaveJobs($data) {	
	    	$data['created_date'] = date('Y-m-d H:i:s');
    		$data['updated_date'] = date('Y-m-d H:i:s');
    		$id = $this->_db->insert('tms_new_job', $data);
    		if ($id) {
    		   $return['status'] = 200;
    		   $return['msg'] = 'Successfully Inserted.';
    		   $return['id'] = $id;
    		} else {
    		   $return['status'] = 422;
    		   $return['msg'] = 'Not Saved.';
    		}
    		return $return;
	    }

	    public function jobChanupdeateJobs($data) {
	    	$data['created_date'] = date('Y-m-d H:i:s');
	    	$data['updated_date'] = date('Y-m-d H:i:s');
	    	$id = $this->_db->insert('tms_new_job', $data);
	    	if ($id) {
	    		$return['status'] = 200;
	    		$return['msg'] = 'Successfully Inserted.';
	    	} else {
	    		$return['status'] = 422;
	    		$return['msg'] = 'Not Saved.';
	    	}
	    	return $return;
	    }

	    public function jobnumberGet($id) {

	    	$this->_db->where('job_chain_id',$id);
	    	$id = $this->_db->get('tms_new_job');

	    	if($id) {
	    		foreach ($id AS $data) {
	    			$data = $data['new_job_number'];
	    		}	
	    		if($data=="") {
	    			$data = 0;
	    		} else {
	    			$data = $data;
	    		}
	    	} else {
	    		$data = 0;
	    	}
	    	$info = $data;
	    	return $info; 
	    }

	    public function jobChinjobsGet($id) {
	    	$this->_db->where('job_chain_id',$id);
	    	$id = $this->_db->get('tms_new_job');
	    	return $id;
	    }

	    public function jobChainsave($data) {
	    	$data['created_date'] = date('Y-m-d H:i:s');
	    	$data['updated_date'] = date('Y-m-d H:i:s');
	    	$id = $this->_db->insert('tms_job_chain', $data);
	    	
	    	if ($id) {	
	    		$this->_db->where('job_chain_id',1);
	    		$this->_db->update('tms_new_job',array('job_chain_id'=>$id));
	    		
	    		$return['status'] = 200;
	    		$return['msg'] = 'Successfully Inserted.';
	    	} else {
	    		$return['status'] = 422;
	    		$return['msg'] = 'Not Saved.';
	    	}
	    	return $id;
	    }

	    public function jobchainnameGet() {
	    	$this->_db->orderBy ("job_name","asc");
	    	$data = $this->_db->get('tms_job_chain');
	    	return $data;
	    }
	    public function jobChainList() {
	    	//$this->_db->orderBy ("job_name","asc");
	    	//$data = $this->_db->get('tms_job_chain');
	    	$data = $this->_db->rawQuery("SELECT * FROM tms_job_chain WHERE job_name !='' AND job_status !='' ORDER BY job_chain_id DESC");
	    	$statuses = $this->jobchainstatusGet();
	    	foreach ($data as $key => $value) {
	    		foreach ($statuses as $skey => $svalue) {
	    			if($value['job_status'] == $skey){
						$data[$key]['job_status'] = $svalue;
					}
	    		}
	    		//echo $value['job_status'];
	    	}
	    	$rmNotSaved = $this->_db->rawQuery("DELETE FROM tms_job_chain WHERE job_name ='' AND job_status =''");
	    	return $data;
	    }

	    public function jobchainnameduplicateGet() {
	    	$data = $this->_db->get('tms_job_chain');
	    	foreach($data As $key=>$value){
 	    		$arr_unique = array_unique($value);
 	    		$arr_duplicates = array_diff_assoc($value, $arr_unique);
 	    		$data = $arr_duplicates;
	    	}
	    	return $data;
	    }

	    public function jobChingetOne($id) {
	    	$this->_db->where('job_chain_id',$id);
	    	$data = $this->_db->getone('tms_job_chain');
	    	return $data;
	    }	

	    public function jobChinnewgetOne($id) {
	    	$this->_db->where('new_job_id',$id);
	    	$data = $this->_db->getone('tms_new_job');
	    	return $data;
	    }

	    public function jobChinfolllowsget($id) {
	    	$this->_db->where('job_chain_id',$id);
	    	$data = $this->_db->get('tms_new_job');
	    	return $data;
	    }

	    public function jobChainupdate($id,$data) {
	    	//$data['created_date'] = date('Y-m-d H:i:s');
	    	if($data['job_name'] == "") {
	    		$data['job_name'] = "Unknown";
	    	} else {
				$data['job_name'];
	    	}
		   	$data['updated_date'] = date('Y-m-d H:i:s');
		   	$this->_db->where('job_chain_id',$id);
		   	$id = $this->_db->update('tms_job_chain', $data);
		   	if ($id) {
		   	   $return['status'] = 200;
		   	   $return['msg'] = 'Successfully Updated.';
		 	} else {
		   	   $return['status'] = 422;
		   	   $return['msg'] = 'Not Updated.';
		   	}
		   	return $return;
	    }

		public function jobchainstatusGet() {
			$info = array("1"=>"In preparation","2"=>"Released","3"=>"Released For Selected Customers","4"=>"Canceled");
			return $info;
		}

		public function masterjobChaindelete() {
			$this->_db->where('job_name',1);
			$this->_db->delete('tms_job_chain');
		}

		public function masterJobchainget($orderId) {
			if($orderId == 'tmpIdOrder'){
				$this->_db->where('job_name',Array('Unknown'), 'NOT IN');
				$data = $this->_db->get('tms_job_chain');
				return $data;
			}else{
				$this->_db->where('order_id',$orderId);
				$customer = $this->_db->getOne('tms_customer');
				
				$this->_db->where('job_name',Array('Unknown'), 'NOT IN');
				$data = $this->_db->get('tms_job_chain');
				
				$jobChainArray = array();
				
				foreach ($data as $key => $value) {
					if($value['customer']){
						foreach (json_decode($value['customer'],true) as $key1 => $value1) {
							if($value1['Cusid'] == $customer['client']){
								array_push($jobChainArray, $data[$key]);
							}
						}		
					}
				}
				return $jobChainArray;
			}
		}

		public function chainDuplicateSave($id) {
			$this->_db->where('job_chain_id',$id);
			$data = $this->_db->getOne('tms_job_chain');
			$this->_db->where('job_chain_id',$data['job_chain_id']);
			$info = $this->_db->get('tms_new_job');
			if($data) {
				$data['job_name'] = $data['job_name']."(N)";
				$data['created_date'] = date('Y-m-d H:i:s');
	    		$data['updated_date'] = date('Y-m-d H:i:s');
	    		unset($data['job_chain_id']);
	    		$pid = $this->_db->insert('tms_job_chain',$data);
				if($info) {
					foreach ($info as $key => $value) {
						$value['job_chain_id'] = $pid;
						$value['created_date'] = date('Y-m-d H:i:s');
	    				$value['updated_date'] = date('Y-m-d H:i:s');
	    				$this->_db->insert('tms_new_job',$value);
					}
				}
			}
			return $pid;
		}

		public function masterjobGet($id) {
			$this->_db->where('job_id',$id);
			return $this->_db->getOne('tms_jobs');
		}

		public function deleteJobChain($id) {
			$this->_db->where('job_chain_id',$id);
			$chainInTsvTbl = $this->_db->getOne('tms_summmery_view');

			if($chainInTsvTbl){
				$return['status'] = 422;
		   	   	$return['msg'] = 'You can not delete assigned job chain.';
			}else{
				$this->_db->where('job_chain_id',$id);
				$del1 = $this->_db->delete('tms_new_job');
				
				$this->_db->where('job_chain_id',$id);
				$del = $this->_db->delete(' tms_job_chain');

				if($del && $del1){
					$return['status'] = 200;
		   	   		$return['msg'] = 'Job chain deleted successfully.';	
				}else{
					$return['status'] = 422;
		   	   		$return['msg'] = 'error while deleting.';	
				}
			}

			return $return;
		}
		public function sortedjobListSave($id,$data) {
			//echo gettype($data);exit;
			$sortField['sortedJobsJson'] = $data['sort'];
			$this->_db->where('job_chain_id',$id);
			$id = $this->_db->update('tms_job_chain', $sortField);
		}

}