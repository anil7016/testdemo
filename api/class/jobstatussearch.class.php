<?php



require_once 'users.class.php';

require_once 'client.class.php';

require_once 'tasktype.class.php';



class jobstatussearch {

	//price1 data manage

	    public function __construct() {

	        $this->_db = db::getInstance();

	    }



	    public function statusJobReportFind(){

    		$qry = "SELECT tmv.ItemLanguage,tmv.job_no AS jobNo,tmv.job_code AS jobCode,tmu.iUserId AS contactPerson,tmu.vUserName AS contactPersonName, tmv.resource AS resource, tu.vUserName As resourceName,tc.vUserName AS customerName,tmv.company_code AS companyCode,tcu.client AS customer,tu.vResourceType AS serviceGroup,tg.project_type AS projectType,tmv.item_status AS jobStatus,ti.item_status AS itemStatus,tu.iFkUserTypeId AS orderTypes,tg.order_no AS orderNum,tmv.job_summmeryId AS jobId,tu.iFkUserTypeId AS ifkuserId, tmv.po_number AS poNumber FROM tms_items AS ti INNER JOIN tms_summmery_view AS tmv ON ti.order_id = tmv.order_id INNER JOIN tms_customer AS tcu ON ti.order_id = tcu.order_id INNER JOIN tms_client AS tc ON tcu.client = tc.iClientId INNER JOIN tms_general AS tg ON ti.order_id = tg.order_id INNER JOIN tms_users AS tu ON tmv.resource = tu.iUserId INNER JOIN tms_users AS tmu ON tmv.contact_person = tmu.iUserId group by tmv.job_summmeryId";

	    	$data = $this->_db->rawQuery($qry);

	    	return $data;

	    }

	    

	    public function jobsearchStatusUpdate($id,$status){

	    	$data['item_status'] = $status;

	    	$data['updated_date'] = date('Y-m-d H:i:s');

	    	$this->_db->where('job_summmeryId',$id);

	    	$data = $this->_db->update('tms_summmery_view',$data);

	    	return $data;

	    }



	    public function jobsearchStatusDelete($id){

	    	$this->_db->where('job_summmeryId',$id);

	    	$data = $this->_db->delete('tms_summmery_view');

	    	return $data;

	    }

	    public function advanceSearchResource(){

    		$qry = "SELECT tmv.job_no AS jobNo,tmv.job_code AS jobCode,tmu.iUserId AS contactPerson,tmu.vUserName AS contactPersonName, tmv.resource AS resource, tu.vUserName As resourceName,tu.freelancer As freelancerType,tc.vUserName AS customerName,tmv.company_code AS companyCode,tcu.client AS customer,tu.vResourceType AS serviceGroup,tg.project_type AS projectType,tmv.item_status AS jobStatus,ti.item_status AS itemStatus,tu.iFkUserTypeId AS orderTypes,tg.order_no AS orderNum,tmv.job_summmeryId AS jobId,tu.iFkUserTypeId AS ifkuserId, tmv.po_number AS poNumber,tupl.currancy_id As currency,tu.address1Detail AS userCountry ,tmv.due_date As JobDueDate,Rate.OverallRating FROM tms_items AS ti LEFT JOIN tms_summmery_view AS tmv ON ti.order_id = tmv.order_id LEFT JOIN tms_customer AS tcu ON ti.order_id = tcu.order_id LEFT JOIN tms_client AS tc ON tcu.client = tc.iClientId LEFT JOIN tms_general AS tg ON ti.order_id = tg.order_id LEFT JOIN tms_users AS tu ON tmv.resource = tu.iUserId LEFT JOIN tms_users AS tmu ON tmv.contact_person = tmu.iUserId LEFT JOIN tms_userprice_list As tupl ON tupl.iUserId = tu.iUserId LEFT OUTER JOIN (SELECT (((ceil(floor(sum(floor(language)))*5/sum(floor(language)+floor(specialist)+floor(technical)+floor(deadlines)+floor(flexibility))) + ceil(floor(sum(floor(specialist)))*5/sum(floor(language)+floor(specialist)+floor(technical)+floor(deadlines)+floor(flexibility))) + ceil(floor(sum(floor(technical)))*5/sum(floor(language)+floor(specialist)+floor(technical)+floor(deadlines)+floor(flexibility))) + ceil(floor(sum(floor(deadlines)))*5/sum(floor(language)+floor(specialist)+floor(technical)+floor(deadlines)+floor(flexibility))) + ceil(floor(sum(floor(flexibility)))*5/sum(floor(language)+floor(specialist)+floor(technical)+floor(deadlines)+floor(flexibility))))*20)/5) As OverallRating ,iUserId FROM `tms_resource_assets` group by iUserId) As Rate ON Rate.iUserId = tu.iUserId group by tmv.job_summmeryId";

    		/*$qry = "SELECT tmv.job_no AS jobNo,tmv.job_code AS jobCode,tmu.iUserId AS contactPerson,tmu.vUserName AS contactPersonName, tmv.resource AS resource, tu.vUserName As resourceName,tu.freelancer As freelancerType,tc.vUserName AS customerName,tmv.company_code AS companyCode,tcu.client AS customer,tu.vResourceType AS serviceGroup,tg.project_type AS projectType,tmv.item_status AS jobStatus,ti.item_status AS itemStatus,tu.iFkUserTypeId AS orderTypes,tg.order_no AS orderNum,tmv.job_summmeryId AS jobId,tu.iFkUserTypeId AS ifkuserId, tmv.po_number AS poNumber,tupl.currancy_id As currency,tu.address1Detail AS userCountry ,tmv.due_date As JobDueDate,tra.language,tra.specialist ,tra.technical,tra.deadlines,tra.flexibility FROM tms_items AS ti LEFT JOIN tms_summmery_view AS tmv ON ti.order_id = tmv.order_id LEFT JOIN tms_customer AS tcu ON ti.order_id = tcu.order_id LEFT JOIN tms_client AS tc ON tcu.client = tc.iClientId LEFT JOIN tms_general AS tg ON ti.order_id = tg.order_id LEFT JOIN tms_users AS tu ON tmv.resource = tu.iUserId LEFT JOIN tms_users AS tmu ON tmv.contact_person = tmu.iUserId LEFT JOIN tms_userprice_list As tupl ON tupl.iUserId = tu.iUserId LEFT JOIN tms_resource_assets AS tra ON tra.iUserId = tu.iUserId group by tmv.job_summmeryId"*/

	    	$data = $this->_db->rawQuery($qry);

	    	

	    	return $data;

	    }



}