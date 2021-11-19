<?php

require_once 'users.class.php';
require_once 'client.class.php';
require_once 'tasktype.class.php';

class orderstatussearch {
	//price1 data manage
	    public function __construct() {
	        $this->_db = db::getInstance();
	    }

	    public function statusorderReportFind(){
	   		$qry = "SELECT gen.order_no AS orderNumber,gen.order_id AS orderId, cust.created_date AS orderDate, cust.client AS customer,gen.project_name AS projectName, c.vUserName AS contactName,stus.status_name AS clientStatus,gen.company_code AS companyCode,cust.contact AS contactPerson,its.item_status AS itemStatus,gen.project_status AS projectStatus,gen.project_type AS projectType,its.source_lang AS sourceLanguage,its.target_lang AS targetLanguage,its.total_amount AS totalAmount FROM tms_general AS gen LEFT JOIN tms_customer AS cust ON gen.order_id=cust.order_id LEFT JOIN tms_items AS its ON gen.order_id=its.order_id  LEFT JOIN tms_client AS c ON cust.client = c.iClientId LEFT JOIN tms_user_status AS stus ON c.vStatus = stus.status_id group by gen.order_no";
	   		$data = $this->_db->rawQuery($qry);

	   		$qry = "SELECT gen.order_no AS orderNumber,gen.order_id AS orderId, cust.created_date AS orderDate, cust.client AS customer,gen.project_name AS projectName, c.vUserName AS contactName,stus.status_name AS clientStatus,gen.company_code AS companyCode,cust.contact AS contactPerson,its.item_status AS itemStatus,gen.project_status AS projectStatus,gen.project_type AS projectType,its.source_lang AS sourceLanguage,its.target_lang AS targetLanguage, its.q_date AS QuentityDate ,SUM(its.total_amount) As TotalAmount FROM tms_general AS gen LEFT JOIN tms_customer AS cust ON gen.order_id=cust.order_id LEFT JOIN tms_items AS its ON gen.order_id=its.order_id  LEFT JOIN tms_client AS c ON cust.client = c.iClientId LEFT JOIN tms_user_status AS stus ON c.vStatus = stus.status_id group by its.q_date";
	   		$info = $this->_db->rawQuery($qry);

	   		$qry = "SELECT gen.order_no AS orderNumber,gen.project_type,gen.order_id AS orderId,tpt.project_name AS projectTypeName, cust.created_date AS orderDate, cust.client AS customer,gen.project_name AS projectName, c.vUserName AS contactName,stus.status_name AS clientStatus,gen.company_code AS companyCode,cust.contact AS contactPerson,its.item_status AS itemStatus,gen.project_status AS projectStatus,gen.project_type AS projectType,its.source_lang AS sourceLanguage,its.target_lang AS targetLanguage, its.q_date AS QuentityDate ,SUM(its.total_amount) As TotalAmount FROM tms_general AS gen LEFT JOIN tms_customer AS cust ON gen.order_id=cust.order_id LEFT JOIN tms_items AS its ON gen.order_id=its.order_id LEFT JOIN tms_client AS c ON cust.client = c.iClientId LEFT JOIN tms_user_status AS stus ON c.vStatus = stus.status_id LEFT JOIN tms_project_type AS tpt ON gen.project_type = tpt.pr_type_id group by gen.project_type";
	   		$Typeinfo = $this->_db->rawQuery($qry);

	   		$qry = "SELECT gen.order_no AS orderNumber,gen.order_id AS orderId,tpt.project_name AS projectTypeName, cust.created_date AS orderDate, cust.client AS customer,gen.project_name AS projectName, c.vUserName AS contactName,stus.status_name AS clientStatus,gen.company_code AS companyCode,cust.contact AS contactPerson,its.item_status AS itemStatus,gen.project_status AS projectStatus,gen.project_type AS projectType,its.source_lang AS sourceLanguage,its.target_lang AS targetLanguage, its.q_date AS QuentityDate ,SUM(its.total_amount) As TotalAmount FROM tms_general AS gen LEFT JOIN tms_customer AS cust ON gen.order_id=cust.order_id LEFT JOIN tms_items AS its ON gen.order_id=its.order_id LEFT JOIN tms_client AS c ON cust.client = c.iClientId LEFT JOIN tms_user_status AS stus ON c.vStatus = stus.status_id LEFT JOIN tms_project_type AS tpt ON gen.project_type = tpt.pr_type_id group by c.iClientId";
			$customerType = $this->_db->rawQuery($qry);	   		


	   		$data['data'] = $data;
	   		$data['info'] = $info;
	   		$data['Typeinfo'] = $Typeinfo;
	   		$data['customerType'] = $customerType;
	   		return $data;
	    }

	    public function ordersearchProjectStatusUpdate($id,$status){
	    	$data['project_status'] = $status;
	    	$data['modified_date'] = date('Y-m-d H:i:s');
	    	$this->_db->where('order_id',$id);
	    	$data = $this->_db->update('tms_general',$data);
	    	return $data;
	    }

	    public function ordersearchItemStatusUpdate($id,$status){
	    	$data['item_status'] = $status;
	    	$data['updated_date'] = date('Y-m-d H:i:s');
	    	$this->_db->where('order_id',$id);
	    	$data = $this->_db->update('tms_items',$data);
	    	return $data;
	    }
}