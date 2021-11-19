<?php

class userposition {

    public function __construct() {
        $this->_db = db::getInstance();
    }

    public function save($data) {
        $this->_db->where('position_name',$data['position_name']);
        $match = $this->_db->getOne('tms_user_position');

        if($match){
            $return['status'] = 422;
            $return['msg'] = 'Position already exists.'; 
        }else{
            $data['created_date'] = date('Y-m-d H:i:s');
            $data['updated_date'] = date('Y-m-d H:i:s');
            $id = $this->_db->insert('tms_user_position', $data);
            if ($id) {
                $return['status'] = 200;
                $return['msg'] = 'Insert Successfully.';
            } else {
                $return['status'] = 422;
                $return['msg'] = 'Not inserted.';
            }
        }
        

        return $return;
    }

    public function update($id, $data) {
        $this->_db->where('position_name',$data['position_name']);
        $match = $this->_db->getOne('tms_user_position');

        if($match && $match['position_id'] != $id){
            $return['status'] = 422;
            $return['msg'] = 'Position already exists.'; 
        }else{
            $data['updated_date'] = date('Y-m-d H:i:s');
            $this->_db->where('position_id', $id);
            $id = $this->_db->update('tms_user_position', $data);
            if ($id) {
                $return['status'] = 200;
                $return['msg'] = 'Updated Successfully.';
            } else {
                $return['status'] = 422;
                $return['msg'] = 'Not inserted.';
            }
        }    
        return $return;
    }

    public function GetuserPosition(){
        $results = $this->_db->get('tms_user_position');
        return $results;
    }

    public function delete($id) {
        $this->_db->where('vResourcePosition', $id);
        $ifIdUsedInUsers = $this->_db->getOne('tms_users');
        if($ifIdUsedInUsers){
            $return['status'] = 404;
            $return['msg'] = 'This record is already in use.';
        }else{
            $this->_db->where('position_id', $id);
            $id = $this->_db->delete('tms_user_position');
            if ($id) {
                $return['status'] = 200;
                $return['msg'] = 'Deleted Successfully.';
            } else {
                $return['status'] = 422;
                $return['msg'] = 'Not inserted.';
            }   
        }
        return $return;
    }

    public function getTypeById($id) {
        $this->_db->where('position_id', $id);
        $result = $this->_db->getOne('tms_user_position');
        return $result;
    }
    public function select2GetAll() {
        $results = $this->_db->rawQuery("select * From tms_user_position where status = '1'");
        return $results;
    }
}
