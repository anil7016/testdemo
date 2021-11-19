<?php

class dateformat {

    public function __construct() {
        $this->_db = db::getInstance();
    }

    public function save($data) {
        $this->_db->where('dateFormat',$data['dateFormat']);
        $this->_db->where('iUserId',$data['iUserId']);
        $match = $this->_db->getOne('tms_dateformat');
        
        if($match){
            $return['status'] = 422;
            $return['msg'] = 'dateformat already exists.'; 
        }else{
            $this->_db->where('iUserId',$data['iUserId']);
            $this->_db->where('is_active',1);
            $checkAnyAcvtive = $this->_db->getOne('tms_dateformat');
            if(!$checkAnyAcvtive){
                $data['created_date'] = date('Y-m-d H:i:s');
                $data['updated_date'] = date('Y-m-d H:i:s');
                $id = $this->_db->insert('tms_dateformat', $data);
                if ($id) {
                    $return['status'] = 200;
                    $return['msg'] = 'Insert Successfully.';
                } else {
                    $return['status'] = 422;
                    $return['msg'] = 'Not inserted.';
                }  
            }else if($checkAnyAcvtive && $data['is_active'] == 0){
                $data['created_date'] = date('Y-m-d H:i:s');
                $data['updated_date'] = date('Y-m-d H:i:s');
                $id = $this->_db->insert('tms_dateformat', $data);
                if ($id) {
                    $return['status'] = 200;
                    $return['msg'] = 'Insert Successfully.';
                } else {
                    $return['status'] = 422;
                    $return['msg'] = 'Not inserted.';
                }
            }else{
                $return['status'] = 422;
                $return['msg'] = 'Only one date format should active at a time.'; 
            }

            
        }
        

        return $return;
    }

    public function getById($id) {
        $this->_db->where('dateformat_id', $id);
        $result = $this->_db->getOne('tms_dateformat');
        return $result;
    }
    public function getdateFormatByIuserId($id) {
        $this->_db->where('iUserId', $id);
        $this->_db->where('is_active',1);
        $result = $this->_db->getOne('tms_dateformat');
        return $result;
    }

    public function getAllFormat($id) {
        $this->_db->where('iUserId', $id);
        $results = $this->_db->get('tms_dateformat');
        return $results;
        return $result;
    }

    public function updateDateFormat($id, $data) {
        $this->_db->where('dateFormat',$data['dateFormat']);
        $this->_db->where('iUserId',$data['iUserId']);
        $match = $this->_db->getOne('tms_dateformat');

        if($match && $match['dateformat_id'] != $id){
            $return['status'] = 422;
            $return['msg'] = 'dateformat already exists.'; 
        }else{

            $this->_db->where('iUserId',$data['iUserId']);
            $this->_db->where('is_active',1);
            $checkAnyAcvtive = $this->_db->getOne('tms_dateformat');
            if($checkAnyAcvtive && $checkAnyAcvtive['dateformat_id'] == $id || $checkAnyAcvtive['dateformat_id'] != $id && $data['is_active'] == 0){
                $data['updated_date'] = date('Y-m-d H:i:s');
                $this->_db->where('dateformat_id', $id);
                $id = $this->_db->update('tms_dateformat', $data);
                if ($id) {
                    $return['status'] = 200;
                    $return['msg'] = 'Updated Successfully.';
                } else {
                    $return['status'] = 422;
                    $return['msg'] = 'Not inserted.';
                }
            }elseif(!$checkAnyAcvtive){
                $data['updated_date'] = date('Y-m-d H:i:s');
                $this->_db->where('dateformat_id', $id);
                $id = $this->_db->update('tms_dateformat', $data);
                if ($id) {
                    $return['status'] = 200;
                    $return['msg'] = 'Updated Successfully.';
                } else {
                    $return['status'] = 422;
                    $return['msg'] = 'Not inserted.';
                }
            }else{
                $return['status'] = 422;
                $return['msg'] = 'Only one date format should active at a time.';
            }
            

        }    
        return $return;
    }
    public function delete($id) {
        $this->_db->where('dateformat_id', $id);
        $id = $this->_db->delete('tms_dateformat');
        if ($id) {
            $return['status'] = 200;
            $return['msg'] = 'Deleted Successfully.';
        } else {
            $return['status'] = 422;
            $return['msg'] = 'Not inserted.';
        }
        return $return;
    }
}
