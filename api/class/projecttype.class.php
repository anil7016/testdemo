<?php

class projecttype {

    public function __construct() {
        $this->_db = db::getInstance();
    }

    public function save($data) {
        $this->_db->where('code',$data['code']);
        $match = $this->_db->getOne('tms_project_type');
        if($match){
                $return['status'] = 422;
                $return['msg'] = 'Code already exists.'; 
        }else{
            $data['created_date'] = date('Y-m-d H:i:s');
            $data['modified_date'] = date('Y-m-d H:i:s');
            $id = $this->_db->insert('tms_project_type', $data);
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
        $this->_db->where('code',$data['code']);
        $match = $this->_db->getOne('tms_project_type');
        if($match && $match['pr_type_id'] != $id){
                $return['status'] = 422;
                $return['msg'] = 'Code already exists.'; 
        }else{
            $data['modified_date'] = date('Y-m-d H:i:s');
            $this->_db->where('pr_type_id', $id);
            $id = $this->_db->update('tms_project_type', $data);
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

        $results = $this->_db->get('tms_project_type');
        return $results;
    }

    public function delete($id) {
        $this->_db->where('project_type', $id);
        $ifIdUsedInGeneral = $this->_db->getOne('tms_general');
        if($ifIdUsedInGeneral){
            $return['status'] = 404;
            $return['msg'] = 'This record is already in use.';
        }else{
            $this->_db->where('pr_type_id', $id);
            $id = $this->_db->delete('tms_project_type');
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
        $this->_db->where('pr_type_id', $id);
        $result = $this->_db->getOne('tms_project_type');
        return $result;
    }

    public function getActive() {
        $this->_db->where('is_active', 1);
        $result = $this->_db->get('tms_project_type');
        return $result;
    }

}
