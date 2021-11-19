<?php

require_once 'users.class.php';
require_once 'client.class.php';
require_once 'tasktype.class.php';

class log_master {
	//price1 data manage
    public function __construct() {
        $this->_db = db::getInstance();    
    }

    public function logMatch($log_type, $typeId, $type, $created) {
    	$this->_db->where('log_type_id', $typeId);
    	$this->_db->where('log_status', $type);
    	$this->_db->where('log_type', $log_type);
    	$this->_db->where('created_by', $created);
    	$data = $this->_db->getOne('tms_log_master');
    	return $data['log_id'];
    }

    public function logTaskMatch($log_type, $taskId, $typeId, $type, $created) {
    	$this->_db->where('log_type_id', $typeId);
    	$this->_db->where('log_status', $type);
    	$this->_db->where('task_id', $taskId);
    	$this->_db->where('log_type', $log_type);
    	$this->_db->where('created_by', $created);
    	$data = $this->_db->getOne('tms_log_master');
    	return $data['log_id'];
    }

    public function saveLog($data) {
    	if(isset($data['task_id'])) {
    		$id = self::logTaskMatch($data['log_type'], $data['task_id'], $data['log_type_id'], $data['log_status'], $data['created_by']);
    	} else {
    		$id = self::logMatch($data['log_type'], $data['log_type_id'], $data['log_status'], $data['created_by']);	
    	}
    	
    	if(!$id) {
    		$data['created_date'] = date('Y-m-d H:i:s');
    		$data['modified_date'] = date('Y-m-d H:i:s');
	    	$result = $this->_db->insert('tms_log_master', $data);
    	} else {
    		$data['modified_date'] = date('Y-m-d H:i:s');
    		$this->_db->where('log_id', $id);
	    	$result = $this->_db->update('tms_log_master', $data);
    	}
    	return $result;
    }   
    
    public function recentActivityGet($id) {
    	// $this->_db->orderBy("modified_date","Desc");
    	// $this->_db->where('created_by', $id);
    	// $data = $this->_db->get('tms_log_master',17);
        $data = $this->_db->rawQuery("SELECT * FROM tms_log_master WHERE created_by = $id AND log_title !='' ORDER BY modified_date DESC");
        return $data;
    }
}