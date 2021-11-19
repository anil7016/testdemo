<?php
class asserts {
    
    public function __construct() {
        $this->_db = db::getInstance();
    }
    
    public function getByIdasserts($field,$id,$table) {
    	$this->_db->where($field,$id);
    	$data = $this->_db->getOne($table);
    	return $data;
    }
    
    public function getByAll($field, $id, $table) {
        $this->_db->where($field, $id);
        return $this->_db->get($table);
    }
    
    public function resourceAssetsGet($id) {
        $this->_db->where('iUserId',$id);
        $data = $this->_db->get('tms_resource_assets');
        return $data;
    }
    public function resourceAssetsByIuserId($id) {
        $this->_db->where('iUserId',$id);
        $data = $this->_db->get('tms_resource_assets');
    	if($data){
            foreach ($data as $key => $value) {
                $this->_db->where('job_summmeryId',$value['job_id']);
                $job = $this->_db->getOne('tms_summmery_view');
                if($job){
                    $data[$key]['jobName'] = $job['po_number'];
                }
            }
        }
        /*echo '<pre>'; print_r($data); echo '</pre>';
        exit;*/
        return $data;
    }
    

    public function resourceAssetsGetOne($id, $jobId) {
        $this->_db->join('tms_users tu', 'tu.iUserId = trs.carryOut', 'LEFT');
        $this->_db->where('trs.iUserId',$id);
        $this->_db->where('trs.job_id',$jobId);
        $data = $this->_db->getOne('tms_resource_assets trs');
        return $data;
    }
    public function getAllReviewByUserId($id, $jobId) {
        $this->_db->join('tms_users tu', 'tu.iUserId = trs.carryOut', 'LEFT');
        $this->_db->where('trs.iUserId',$id);
        //$this->_db->where('trs.job_id',$jobId);
        $data = $this->_db->get('tms_resource_assets trs');
        return $data;
    }
    
    public function resourceAssetsSave($data) {

        //unset
        unset($data['resourceId']);
        unset($data['vUserName']);
        unset($data['freelancer']);
        unset($data['vEmailAddress']);
        unset($data['vSecondaryEmailAddress']);
        unset($data['vPassword']);
        unset($data['org_pass']);
        unset($data['eUserStatus']);
        unset($data['vResourceType']);
        unset($data['vResourcePosition']);
        unset($data['iFkUserTypeId']);
        unset($data['iResourceNumber']);
        unset($data['dtCreationDate']);
        unset($data['vTimeZone']);
        unset($data['vTimeZoneCity']);
        unset($data['vUTC']);
        unset($data['vFirstName']);
        unset($data['vLastName']);
        unset($data['dtBirthDate']);
        unset($data['companyName']);
        unset($data['vFacebook_id']);
        unset($data['vTwitter_id']);
        unset($data['vGoogle_plus_id']);
        unset($data['iGender']);
        unset($data['iMobile']);
        unset($data['vPhoneNumber']);
        unset($data['vWebsite']);
        unset($data['vSkypeName']);
        unset($data['vFileManage']);
        unset($data['vEmailTemplate']);
        unset($data['tMemo']);
        unset($data['vAddress1']);
        unset($data['vAddress2']);
        unset($data['address1Detail']);
        unset($data['address2Detail']);
        unset($data['vSalesId']);
        unset($data['vComapanyCode']);
        unset($data['vholiday_country']);
        unset($data['vProfilePic']);
        unset($data['enableDisbaleLoginStatus']);
        unset($data['created_by']);
        unset($data['modified_by']);
        unset($data['iEditedBy']);
        unset($data['dtCreatedDate']);
        unset($data['dtUpdatedDate']);
        
        $data['created_date'] = date('Y-m-d H:i:s');
    	$data['modified_date'] = date('Y-m-d H:i:s');
        $data['totalRate'] = str_replace('%', '', $data['totalRate']);

        $this->_db->where('job_id',$data['job_id']);
        $exists = $this->_db->getOne('tms_resource_assets');
        //echo '<pre>'; print_r($exists); echo '</pre>';exit;
        if($exists){
            $result['status'] = 403;
            $result['msg'] = "Review already given for this job.";
        }else{
            $id = $this->_db->insert('tms_resource_assets',$data);
        	if($id) {
        		$result['status'] = 200;
        		$result['msg'] = "Successfully Inserted";
        	} else {
        		$result['status'] = 401;
        		$result['msg'] = "Not Inserted";
        	}
        }
    	return $result;
    }
}
