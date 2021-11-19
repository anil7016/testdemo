<?php

class tasktype {

    public function __construct() {
        $this->_db = db::getInstance();
    }

    public function save($data) {
        $this->_db->where('task_type',$data['task_type']);
        $match = $this->_db->getOne('tms_task_type');
        if($match){
            $return['status'] = 422;
            $return['msg'] = 'Task type already exists.'; 
        }else{
            $data['created_date'] = date('Y-m-d H:i:s');
            $data['updated_date'] = date('Y-m-d H:i:s');
            $id = $this->_db->insert('tms_task_type', $data);
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
        $this->_db->where('task_type',$data['task_type']);
        $match = $this->_db->getOne('tms_task_type');
        if($match && $match['task_id'] != $id){
            $return['status'] = 422;
            $return['msg'] = 'Task type already exists.'; 
        }else{
            $data['updated_date'] = date('Y-m-d H:i:s');
            $this->_db->where('task_id', $id);
            $id = $this->_db->update('tms_task_type', $data);
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

    public function getAll() {

        $results = $this->_db->get('tms_task_type');
        return $results;
    }

    public function delete($id) {
        $this->_db->where('task_id', $id);
        $id = $this->_db->delete('tms_task_type');
        if ($id) {
            $return['status'] = 200;
            $return['msg'] = 'Deleted Successfully.';
        } else {
            $return['status'] = 422;
            $return['msg'] = 'Not inserted.';
        }

        return $return;
    }

    public function getTypeById($id) {
        $this->_db->where('task_id', $id);
        $result = $this->_db->getOne('tms_task_type');
        return $result;
    }

    public function getActive() {
        $this->_db->where('is_active', 1);
        $result = $this->_db->get('tms_task_type');
        return $result;
    }

}