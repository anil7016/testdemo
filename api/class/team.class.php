<?php

class team {

    public function __construct() {
        $this->_db = db::getInstance();
    }

    public function save($info) {
        $info['created_date'] = date('Y-m-d H:i:s');
        $info['modified_date'] = date('Y-m-d H:i:s');
        $id = $this->_db->insert('tms_team', $info);
        if ($id) {
        	$return['id'] = $id; 
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
        return $results = $this->_db->getOne('tms_team');
    }

    public function update($id, $data) {
        $data['modified_date'] = date('Y-m-d H:i:s');
        $this->_db->where("team_id", $id);
        $id = $this->_db->update('tms_team', $data);
        if ($id) {
            $return['status'] = 200;
            $return['msg'] = 'Successfully Inserted.';
        } else {
            $return['status'] = 422;
            $return['msg'] = 'Not Saved.';
        }
        return $return;
    }
    
    public function projectTeamget($id){
    	$this->_db->where('iClientId',$id);
    	return $data = $this->_db->getone('tms_client');
    }
    
}
