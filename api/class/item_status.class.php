<?php

require_once 'users.class.php';
require_once 'client.class.php';
require_once 'tasktype.class.php';

class item_status {

    public function __construct() {
        $this->_db = db::getInstance();
    }

    public function save($data) {
        $data['created_date'] = date('Y-m-d H:i:s');
        $data['modified_date'] = date('Y-m-d H:i:s');
        $id = $this->_db->insert('tms_item_status', $data);
        if ($id) {
            $return['status'] = 200;
            $return['msg'] = 'Successfully Inserted.';
        } else {
            $return['status'] = 422;
            $return['msg'] = 'Not Saved.';
        }
        return $return;
    }
    public function getAllItem ()
    {
    	$data = $this->_db->get('tms_item_status');
    	return $data; 
    }
    public function getitemOne($id) {
    	$this->_db->where('item_status_id',$id);
    	$id = $this->_db->getone('tms_item_status');
    	return $id;
    }
    public function updateItem( $id, $data ) {
        $data['modified_date'] = date('Y-m-d H:i:s');
    	$this->_db->where('item_status_id',$id);
    	$id = $this->_db->update('tms_item_status', $data);
    	//echo $this->_db->getLastQuery();exit;
    	if ($id) {
    		$return['status'] = 200;
    		$return['msg'] = 'Successfully Updated.';
    	} else {
    		$return['status'] = 422;
    		$return['msg'] = 'Not Updated.';
    	}
    	return $return;
    }
    public function deleteItem($id)
    {
    	$this->_db->where('item_status_id',$id);
    	$id = $this->_db->delete('tms_item_status');
    	if ($id) {
    		$return['status'] = 200;
    		$return['msg'] = 'Successfully Delete.';
    	} else {
    		$return['status'] = 422;
    		$return['msg'] = 'Not Deleted.';
    	}
    	return $return;
    }

}
