<?php

require_once 'users.class.php';
require_once 'client.class.php';
require_once 'tasktype.class.php';

class jobs {

    public function __construct() {
        $this->_db = db::getInstance();
    }

    public function save($jobdata) {
        $this->_db->where('job_code',$jobdata['job_code']);
        $match = $this->_db->getOne('tms_jobs');
        if($match){
                $return['status'] = 422;
                $return['msg'] = 'Job code already exists.'; 
        }else{
            $jobdata['created_date'] = date('Y-m-d H:i:s');
            $jobdata['modified_date'] = date('Y-m-d H:i:s');
            $id = $this->_db->insert('tms_jobs', $jobdata);
            if ($id) {
                $return['status'] = 200;
                $return['id'] = $id;
            } else {
                $return['status'] = 422;
                $return['msg'] = 'Not Saved.';
            }
        }    
        return $return;
    }

    public function getAlljob() {
        $this->_db->orderby('job_id','desc');
    	$data = $this->_db->get('tms_jobs');
    	return $data; 
    }

    public function getjobOne($id) {
    	$this->_db->where('job_id',$id);
    	$data = $this->_db->getone('tms_jobs');
        if($data) {
            $this->_db->where('job_id',$id);
            $info = $this->_db->get('tms_work_instruction');
        }
        
        $data['data'] = $data;
        $data['info'] = $info;
    	return $data;
    }
    
    public function updateJobs( $id, $data ) {
        $this->_db->where('job_code',$data['job_code']);
        $match = $this->_db->getOne('tms_jobs');
        if($match && $match['job_id'] != $id){
                $return['status'] = 422;
                $return['msg'] = 'Job code already exists.'; 
        }else{
            $data['modified_date'] = date('Y-m-d H:i:s');
        	$this->_db->where('job_id',$id);
        	$id = $this->_db->update('tms_jobs', $data);
        	//echo $this->_db->getLastQuery();exit;
        	if ($id) {
        		$return['status'] = 200;
        		$return['msg'] = 'Successfully Updated.';
        	} else {
        		$return['status'] = 422;
        		$return['msg'] = 'Not Updated.';
        	}
        }    
    	return $return;
    }

    public function deletejob($id) {
        $this->_db->where('job_id',$id);
        $match1 = $this->_db->getOne('tms_new_job');
        
        $this->_db->where('job_id',$id);
        $match2 = $this->_db->getOne('tms_summmery_view');
        
        if($match1 || $match2){
            $return['status'] = 422;
            $return['msg'] = 'You can not delete assigned job.';
        }else{
            $this->_db->where('job_id',$id);
        	$data = $this->_db->delete('tms_jobs');
            if($data) {
                $this->_db->where('job_id',$id);
                $this->_db->delete('tms_work_instruction');
            }
        	if ($data) {
        		$return['status'] = 200;
        		$return['msg'] = 'Successfully Delete.';
        	} else {
        		$return['status'] = 422;
        		$return['msg'] = 'Not Deleted.';
        	}
        }
    	return $return;
    }

    public function Jobsummeryget() {
        return $data = $this->_db->get('tms_jobs tj');
    }

    public function saveJobInstruction($data) {
        $data['created_date'] = date('Y-m-d H:i:s');
        $data['modified_date'] = date('Y-m-d H:i:s');
        $id = $this->_db->insert('tms_work_instruction',$data);
        if($id) {
            $return['status'] = 200;
            $return['msg']  = "Successfully Inserted";
        } else {
            $return['status'] = 422;
            $return['msg'] = "Not Inserted";
        }
        return $data;
    }

    public function DeleteJobInstruction($id) {
        $this->_db->where('w_id',$id);
        return $this->_db->delete('tms_work_instruction');
    }

    public function updateJobInstruction($data,$id) {
        $data['created_date'] = date('Y-m-d H:i:s');
        $data['modified_date'] = date('Y-m-d H:i:s');
        $this->_db->where('w_id',$id);
        $id = $this->_db->update('tms_work_instruction',$data);
        if($id) {
            $return['status'] = 200;
            $return['msg']  = "Successfully Inserted";
        } else {
            $return['status'] = 422;
            $return['msg'] = "Not Inserted";
        }
        return $data;
    }

    public function selectWorkInstruction($id) {
        $this->_db->where('w_display',1);
        $this->_db->where('job_id',$id);
        $data = $this->_db->get('tms_work_instruction');
        return $data;
    }

    public function selectWorkInstructs() {
        $this->_db->where('is_active',1);
        $data = $this->_db->get('tms_work_instructs');
        return $data;
    }

}
