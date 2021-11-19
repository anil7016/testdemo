<?php

class workinstruct {

    public function __construct() {
        $this->_db = db::getInstance();
    }

    public function save($data) {
        $this->_db->where('instruct_name',$data['instruct_name']);
        $match = $this->_db->getOne('tms_work_instructs');
        if($match){
                $return['status'] = 422;
                $return['msg'] = 'Instruction already exists.'; 
        }else{
            $data['created_date'] = date('Y-m-d H:i:s');
            $data['modified_date'] = date('Y-m-d H:i:s');
            $id = $this->_db->insert('tms_work_instructs', $data);
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
        $this->_db->where('instruct_name',$data['instruct_name']);
        $match = $this->_db->getOne('tms_work_instructs');
        if($match && $match['id'] != $id){
                $return['status'] = 422;
                $return['msg'] = 'Instruction already exists.'; 
        }else{
            $data['modified_date'] = date('Y-m-d H:i:s');
            $this->_db->where('id', $id);
            $id = $this->_db->update('tms_work_instructs', $data);
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

    public function getAll() {

        $results = $this->_db->get('tms_work_instructs');
        return $results;
    }

    public function delete($id) {
        $this->_db->where('project_type', $id);
        $ifIdUsedInGeneral = $this->_db->getOne('tms_general');
        if($ifIdUsedInGeneral){
            $return['status'] = 404;
            $return['msg'] = 'This record is already in use.';
        }else{
            $this->_db->where('id', $id);
            $id = $this->_db->delete('tms_work_instructs');
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
        $this->_db->where('id', $id);
        $result = $this->_db->getOne('tms_work_instructs');
        return $result;
    }

    public function getActive() {
        $this->_db->where('is_active', 1);
        $result = $this->_db->get('tms_work_instructs');
        return $result;
    }

}
