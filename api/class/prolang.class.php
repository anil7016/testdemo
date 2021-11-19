<?php

class prolang {

    public function __construct() {
        $this->_db = db::getInstance();
    }

    public function save($info) {
        $info['created_date'] = date('Y-m-d H:i:s');
        $info['modified_date'] = date('Y-m-d H:i:s');
        $id = $this->_db->insert('tms_proj_language', $info);
        if ($id) {
            $return['status'] = 200;
            $return['msg'] = 'Successfully Inserted.';
        } else {
            $return['status'] = 422;
            $return['msg'] = 'Not Saved.';
        }
        return $return;
    }
     public function getByOrder($id) {
        $this->_db->where("order_id", $id);
        return $results = $this->_db->get('tms_proj_language');
    }
    public function update($id, $data) {
        $data['modified_date'] = date('Y-m-d H:i:s');
        $this->_db->where("pl_id", $id);
        $id = $this->_db->update('tms_proj_language', $data);
        if ($id) {
            $return['status'] = 200;
            $return['msg'] = 'Successfully Inserted.';
        } else {
            $return['status'] = 422;
            $return['msg'] = 'Not Saved.';
        }
        return $return;
    }
    public function prolanguagegetOne($id){
    	$this->_db->where("pl_id", $id);
    	return $results = $this->_db->getone('tms_proj_language');
    }
    public function proLangdelete($id){
    	$this->_db->where("pl_id", $id);
    	return $results = $this->_db->delete('tms_proj_language');
    }

}
