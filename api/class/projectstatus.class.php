<?php

class projectstatus {

    public function __construct() {
        $this->_db = db::getInstance();
    }

    public function save($data) {
        //echo '<pre>'; print_r($data); echo '</pre>';exit;
        $this->_db->where('project_status_name',$data['project_status_name']);
        $match = $this->_db->getOne('tms_project_status');

        
        if($match){
                $return['status'] = 422;
                $return['msg'] = 'Status already exists.'; 
        }else{
            $this->_db->where('is_default',1);
            $isDefault = $this->_db->getOne('tms_project_status');

            if($isDefault && $data['is_default'] == 1){
                $return['status'] = 422;
                $return['msg'] = 'Default status already assign.Please remove it first';
            }else{
                $data['created_date'] = date('Y-m-d H:i:s');
                $data['modified_date'] = date('Y-m-d H:i:s');
                
                $id = $this->_db->insert('tms_project_status', $data);
                if ($id) {
                    $return['status'] = 200;
                    $return['msg'] = 'Insert Successfully.';
                } else {
                    $return['status'] = 422;
                    $return['msg'] = 'Not inserted.';
                }
            }
        }    
        return $return;
    }
    
    public function getAll() {
        $results = $this->_db->get('tms_project_status');
        return $results;
    }
    
    public function getTypeById($id) {
        $this->_db->where('pr_status_id', $id);
        $result = $this->_db->getOne('tms_project_status');
        return $result;
    }
    
    public function update($id, $data) {
        $this->_db->where('project_status_name',$data['project_status_name']);
        $match = $this->_db->getOne('tms_project_status');
        if($match && $match['pr_status_id'] !=$id){
                $return['status'] = 422;
                $return['msg'] = 'Status already exists.'; 
        }else{
            $this->_db->where('is_default',1);
            $isDefault = $this->_db->getOne('tms_project_status');
            
            if($isDefault && $data['is_default'] == 1){
                $return['status'] = 422;
                $return['msg'] = 'Default status already assign.Please remove it first';
            }else{
                $data['modified_date'] = date('Y-m-d H:i:s');
                $this->_db->where('pr_status_id', $id);
                $id = $this->_db->update('tms_project_status', $data);
                if ($id) {
                    $return['status'] = 200;
                    $return['msg'] = 'Updated Successfully.';
                } else {
                    $return['status'] = 422;
                    $return['msg'] = 'Not Updated.';
                }
            }
        }    
        return $return;
    }

    public function delete($id) {
        $this->_db->where('project_status', $id);
        $ifIdUsedInGeneral = $this->_db->getOne('tms_general');
        if($ifIdUsedInGeneral){
            $return['status'] = 422;
            $return['msg'] = 'This record is already in use.';
        }else{
            $this->_db->where('pr_status_id', $id);
            $defaultStatusNeverDel = $this->_db->getOne('tms_project_status');
            
            if($defaultStatusNeverDel['default_added'] == 1){
                $return['status'] = 422;
                $return['msg'] = 'You can not delete this record.';
            }else{
                $this->_db->where('pr_status_id', $id);
                $id = $this->_db->delete('tms_project_status');
                if ($id) {
                    $return['status'] = 200;
                    $return['msg'] = 'Deleted Successfully.';
                } else {
                    $return['status'] = 422;
                    $return['msg'] = 'Not inserted.';
                }
            }

        }
        return $return;
    }
    
    public function proStatusget() {
        $this->_db->where('is_active', 1);
        $result = $this->_db->get('tms_project_status');
        return $result;
    }
    public function proStatusgetOne() {
        $this->_db->where('is_default', 1);
        $result = $this->_db->getOne('tms_project_status');
        if(!$result){
            $this->_db->where("is_active",1);
            $this->_db->where("project_status_name", '%progress%', 'like');
            $result = $this->_db->getOne('tms_project_status');
        }
        return $result;
    }

}
