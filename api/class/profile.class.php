<?php

class profile {

    public function __construct() {
        $this->_db = db::getInstance();
    }

    public function saveInfo($info) {
        $info['created_date'] = date('Y-m-d H:i:s');
        $info['modified_date'] = date('Y-m-d H:i:s');
        $id = $this->_db->insert('tms_additional_info', $info);
        if ($id) {
            $return['status'] = 200;
            $return['msg'] = 'Successfully Inserted.';
        } else {
            $return['status'] = 422;
            $return['msg'] = 'Not Saved.';
        }
        return $return;
    }

    public function getAddtionalInfo($userId, $type) {
        $this->_db->where("user_id", $userId);
        $this->_db->where("user_type", $type);
        $results = $this->_db->getOne('tms_additional_info');
        return $results;
    }
    
    public function updateAddtionalInfo($id, $info) {
        $info['modified_date'] = date('Y-m-d H:i:s');
        $this->_db->where('add_id', $id);
        $id = $this->_db->update('tms_additional_info', $info);
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
