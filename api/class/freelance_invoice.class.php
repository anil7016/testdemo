<?php
require_once 'users.class.php';
require_once 'client.class.php';
class Freelance_invoice {

	public function __construct() {
		$this->_db = db::getInstance ();
	}

	public function getAll($field, $id, $table) {
		$this->_db->where($field, $id);
		return $this->_db->get($table);
	}

	public function get($table) {
        return $this->_db->get($table);
    }

    public function getInvoiceNumber() {
		$invoiceData = self::get('tms_invoice');
        return $invoiceData;
	}

	//invoice serach data get
	public function invoiceCreate($data1) {
		$infoD = array();
		$paymentDue = self::getAll('invoice_due_id',1,'tms_invoice_due_period');
		foreach ($data1['id'] as $k => $v) {
			$this->_db->where('job_summmeryId', $v['id']);
			$this->_db->join('tms_users tu', 'tu.iUserId=tsv.resource', 'LEFT');
			$this->_db->join('tms_general tg','tg.order_id = tsv.order_id', 'INNER');
			$this->_db->join('tms_customer tcu','tcu.order_id = tsv.order_id', 'INNER');
			$this->_db->join('tms_client tci', 'tci.iClientId=tcu.client', 'LEFT');
			$data = $this->_db->getOne('tms_summmery_view tsv', 'tsv.job_summmeryId AS jobId,tsv.item_id AS item_number, tsv.order_id AS orderId, tsv.po_number AS poNumber, tci.iClientId AS clientId, tci.vAddress1 AS companyAddress, tci.vEmailAddress  AS companyEmail, tci.vPhone AS companyPhone, tu.iUserId AS freelanceId, tu.vUserName AS freelanceName, tu.vEmailAddress AS freelanceEmail, tu.vAddress1 AS freelanceAddress, tu.vProfilePic AS freelancePic, tu.iMobile AS freelancePhone, tg.company_code, tsv.job_code AS jobCode');
			$companyName = self::getAll('abbrivation',substr($data['company_code'],0,-2),'tms_centers');

			$data['companyName'] = $companyName[0]['name'];

			//payment due date number of day
			$data['number_of_days'] = $paymentDue[0]['number_of_days'];

			//invoiceNumber Count
			$data['invoiceCount'] = count(self::get('tms_invoice'));
            //echo '<pre>'; print_r($data); echo '</pre>';exit;

			if(isset($data['orderId'])) {
				//$info = self::getAll('order_id',$data['orderId'],'tms_items');
                $this->_db->where('item_number',$data['item_number']);
                $this->_db->where('order_id',$data['orderId']);
                $info = $this->_db->getOne('tms_items');
                $data['item'] = [];
				if($info){
                    if($info['price']){
                        foreach (json_decode($info['price']) as $field => $val) {
                            $data['item'][] = (array) $val;
                        }
                    }
                }
                /*foreach ($info as $key => $value) {
                    echo '<pre>'; print_r($value); echo '</pre>';exit;
                    if($value['price']){
                        foreach (json_decode($value['price']) as $field => $val) {
    						$data['item'][] = (array) $val;
    					}
                    }
				}*/
			}
			$infoD[$k] = $data;
		}
        return $infoD;
	}
	
	//invoice and draft save
    public function saveInvoice($data) {
        $invoiceAlreadyAdded = false;
        if($data['job_id']){
            $invoiceRecords = $this->_db->get('tms_invoice');

            foreach ($invoiceRecords as $k => $v) {
                foreach (json_decode($v['job_id'],true) as $ke => $val) {
                    $existedJobId = $val['id'];
                    foreach (json_decode($data['job_id'],true) as $k1 => $v1) {
                        $postedJobId = $v1['id'];
                        if($postedJobId == $existedJobId){
                            $invoiceAlreadyAdded = true;
                        }
                    }
                }
            }
        }
        
        if(!$invoiceAlreadyAdded){
            $data['created_date'] = date('Y-m-d');
        	$data['modified_date'] = date('Y-m-d');
        	$data['value_date'] = date('Y-m-d');
        	$data['invoice_date'] = date('Y-m-d');
        	$id = $this->_db->insert('tms_invoice', $data);
        	if($id) {
        		$result['status'] = 200;
        		$result['msg'] = "Successfully saved";
        	} else {
        		$result['status'] = 401;
        		$result['msg'] = "Not saved";
        	}
        	return $result;
        }else{
            $result['status'] = 422;
            $result['msg'] = "Invoice already added for this job";
            return $result;
        }

    }
    
    //display invoice
    public function viewAllInvoice($type,$userId) {
		$this->_db->join('tms_users tu', 'tu.iUserId=tmInvoice.freelance_id','LEFT');
		$this->_db->join('tms_client tc', 'tc.iClientId=tmInvoice.customer_id','LEFT');
		$this->_db->join('tms_summmery_view tsv', 'tsv.job_summmeryId=tmInvoice.job_id','LEFT');
		$this->_db->orderBy('tmInvoice.invoice_id', 'asc');
    	$this->_db->where('tmInvoice.invoice_type', $type);
    	$this->_db->where('tu.iUserId', $userId);
    	$data = $this->_db->get('tms_invoice tmInvoice', null,'tsv.job_summmeryId AS jobId, tsv.order_id AS orderId, tsv.po_number AS poNumber, tc.iClientId AS clientId, tc.vAddress1 AS companyAddress, tc.vEmailAddress  AS companyEmail, tc.vPhone AS companyPhone, tc.vCodeRights AS company_code, tu.iUserId AS freelanceId, tu.vUserName AS freelanceName, tu.vEmailAddress AS freelanceEmail, tu.vAddress1 AS freelanceAddress, tu.vProfilePic AS freelancePic, tu.iMobile AS freelancePhone, tsv.job_code AS jobCode, tmInvoice.invoice_number, tmInvoice.invoice_id, tmInvoice.invoice_status, tmInvoice.Invoice_cost, tmInvoice.paid_amount');
    	foreach ($data as $key => $value) {
    		$companyName = self::getAll('abbrivation',substr($value['company_code'],0,-2),'tms_centers');
    		$data[$key]['companyName'] = isset($companyName[0]['name'])?$companyName[0]['name']:'';	
    	}
    	
    	return $data;
    }
    public function viewAllInvoice1($type) {
		$this->_db->join('tms_users tu', 'tu.iUserId=tmInvoice.freelance_id','LEFT');
		$this->_db->join('tms_client tc', 'tc.iClientId=tmInvoice.customer_id','LEFT');
		$this->_db->join('tms_summmery_view tsv', 'tsv.job_summmeryId=tmInvoice.job_id','LEFT');
		$this->_db->orderBy('tmInvoice.invoice_id', 'asc');
    	$this->_db->where('tmInvoice.invoice_type', $type);
    	$data = $this->_db->get('tms_invoice tmInvoice', null,'tsv.job_summmeryId AS jobId, tsv.order_id AS orderId, tsv.po_number AS poNumber, tc.iClientId AS clientId, tc.vAddress1 AS companyAddress, tc.vEmailAddress  AS companyEmail, tc.vPhone AS companyPhone, tc.vCodeRights AS company_code, tu.iUserId AS freelanceId, tu.vUserName AS freelanceName, tu.vEmailAddress AS freelanceEmail, tu.vAddress1 AS freelanceAddress, tu.vProfilePic AS freelancePic, tu.iMobile AS freelancePhone, tsv.job_code AS jobCode, tmInvoice.invoice_number, tmInvoice.invoice_id, tmInvoice.invoice_status, tmInvoice.Invoice_cost, tmInvoice.paid_amount');
    	foreach ($data as $key => $value) {
    		$companyName = self::getAll('abbrivation',substr($value['company_code'],0,-2),'tms_centers');
    		$data[$key]['companyName'] = isset($companyName[0]['name'])?$companyName[0]['name']:'';	
    	}
    	
    	return $data;
    }
    
    //display one invoice
    public function invoiceViewOne($id) {
    	$id1 = self::getAll('invoice_id',$id,'tms_invoice');
    	$paymentDue = self::getAll('invoice_due_id',1,'tms_invoice_due_period');

    		foreach (json_decode($id1[0]['job_id']) as $k => $v) {
    			
	    		$this->_db->where('job_summmeryId', $v->id);
				$this->_db->join('tms_users tu', 'tu.iUserId=tsv.resource', 'LEFT');
                //$this->_db->join('tms_general tg','tg.order_id = tsv.order_id', 'INNER');
                $this->_db->join('tms_customer tcu','tcu.order_id = tsv.order_id', 'INNER');
                $this->_db->join('tms_client tci', 'tci.iClientId=tcu.client', 'LEFT');
                $data = $this->_db->getOne('tms_summmery_view tsv', 'tsv.job_summmeryId AS jobId,tsv.item_id AS item_number, tsv.order_id AS orderId, tsv.po_number AS poNumber, tci.iClientId AS clientId, tci.vAddress1 AS companyAddress, tci.vPhone AS companyPhone, tu.iUserId AS freelanceId, tu.vUserName AS freelanceName, tu.vEmailAddress AS freelanceEmail, tu.vAddress1 AS freelanceAddress, tu.vProfilePic AS freelancePic, tu.iMobile AS freelancePhone, tci.vCodeRights As company_code, tsv.job_code AS jobCode');
                
                //, tci.vEmailAddress  AS companyEmail
                $companyName = self::getAll('abbrivation',substr($data['company_code'],0,-2),'tms_centers');
                

                $data['companyName'] = $companyName[0]['name'];

                //payment due date number of day
                $data['number_of_days'] = $paymentDue[0]['number_of_days'];

                //invoiceNumber Count
                $data['invoiceCount'] = count(self::get('tms_invoice'));
                $this->_db->where('item_number',$data['item_number']);
                $this->_db->where('order_id',$data['orderId']);
                $info = $this->_db->getOne('tms_items');
                
                if($info){
                    if($info['price']){
                        foreach (json_decode($info['price']) as $field => $val) {
                            $data['item'][] = (array) $val;
                        }
                    }
                }

                /*if(isset($data['orderId'])) {
                    $info = self::getAll('order_id',$data['orderId'],'tms_items');
                    $data['item'] = [];
                    foreach ($info as $key => $value) {
                        if($value['price']){
                            foreach (json_decode($value['price']) as $field => $val) {
                                $data['item'][] = (array) $val;
                            }
                        }
                    }
                }*/
				$infoD[$k] = array_merge($data, $id1[0]);
	    	}
	    	return $infoD;
    }

    //update invoice
    public function invoiceUpdate($id) {
    	$data['value_date'] = date('Y-m-d');
    	$data['invoice_type'] = "save";
    	$this->_db->where('invoice_id', $id);
    	$id = $this->_db->update('tms_invoice',$data);
    	return $id;
    }

    public function invoiceStatusChange($data, $id) {
        /* Insert Part paid invoice payment detail in database START */
        
        $partPaidAmount = array();
        $partPaidAmount['invoice_id']                   = $id;
        $partPaidAmount['invoice_partial_paid_amount']  = $data['partPaid'];
    	$partPaidAmount['created_date']                 = date('Y-m-d H:i:s');
        
        $partPaymentInsert = $this->_db->insert('tms_invoice_payments', $partPaidAmount);

        /* Insert Part paid invoice payment detail in database END */

        unset($data['partPaid']);
        $data['modified_date'] = date('Y-m-d');
    	$this->_db->where('invoice_id', $id);
    	$idd = $this->_db->update('tms_invoice', $data);
    	



        if($idd && $partPaymentInsert) {
    		$request['status'] = 200;
    		$request['msg'] = "Successfully updated";
    	} else {
    		$request['status'] = 401;
    		$request['msg'] = "Not updated";
    	}

    	return $request;
    }
    public function updateJobSummeryItemStatus($data, $id) {
    	$this->_db->where('job_summmeryId', $id);
    	$idd = $this->_db->update('tms_summmery_view', $data);
    	if($idd) {
    		$request['status'] = 200;
    		$request['msg'] = "Successfully updated";
    	} else {
    		$request['status'] = 401;
    		$request['msg'] = "Not updated";
    	}

    	return $request;
    }
    public function getFreelanceStatement($id){
         
        $data = $this->_db->rawQuery("SELECT tu.vUserName,tmInvoice.invoice_number AS InvoiceNo,tmInvoice.paid_date As Date,tmInvoice.Invoice_cost As Amount,tmInvoice.paid_amount,tmInvoice.freelance_id,tmInvoice.customer_id,tmInvoice.value_date,tmInvoice.invoice_type,tmInvoice.invoice_status,tmInvoice.created_date FROM tms_users AS tu INNER JOIN tms_invoice AS tmInvoice ON tu.iUserId = tmInvoice.freelance_id WHERE tu.iUserId = $id");
        return $data;
    }
    public function filterStatement($filterParams){
        //echo '<pre>'; print_r($filterParams); echo '</pre>';
        $this->_db->join('tms_users tu', 'tu.iUserId=tmInvoice.freelance_id','INNER');
        if(isset($filterParams['dueDateFrom']) && isset($filterParams['dueDateTo'])){

            $Frm = $filterParams['dueDateFrom'].' '.'00:00:00';
            $To = $filterParams['dueDateTo'].' '.'00:00:00';
            $this->_db->where('tmInvoice.created_date', Array ($Frm,$To),'BETWEEN');
        }
        
        if(isset($filterParams['comapanyCode'])){
            $cCode = $filterParams['comapanyCode'];
            $this->_db->where('tmInvoice.invoice_number','%'.$cCode.'%', 'like');
        }

        if(isset($filterParams['resource'])){
            $this->_db->where('tmInvoice.freelance_id', $filterParams['resource']);
        }
        if(isset($filterParams['invoiceStatus'])){
            $this->_db->where('tmInvoice.invoice_status', $filterParams['invoiceStatus']);
        }
        if(isset($filterParams['invoiceNumber'])){
            $this->_db->where('tmInvoice.invoice_number', $filterParams['invoiceNumber']);
        }
        
        $data = $this->_db->get('tms_invoice tmInvoice', null,' tu.vUserName,tmInvoice.invoice_number AS InvoiceNo,tmInvoice.paid_date As Date,tmInvoice.Invoice_cost As Amount,tmInvoice.paid_amount,tmInvoice.freelance_id,tmInvoice.customer_id,tmInvoice.value_date,tmInvoice.invoice_type,tmInvoice.invoice_status,tmInvoice.created_date');
        return $data;
    }

    /* Get All save invoice BY FreelancerId/UserId */
    public function getAllInvoiceByUserId($type,$userId) {
        //echo $userId;exit;
        $this->_db->join('tms_users tu', 'tu.iUserId=tmInvoice.freelance_id','LEFT');
        $this->_db->join('tms_client tc', 'tc.iClientId=tmInvoice.customer_id','LEFT');
        $this->_db->join('tms_summmery_view tsv', 'tsv.job_summmeryId=tmInvoice.job_id','LEFT');
        $this->_db->orderBy('tmInvoice.invoice_id', 'asc');
        $this->_db->where('tmInvoice.invoice_type', $type);
        $this->_db->where('tmInvoice.freelance_id',$userId);
        $data = $this->_db->get('tms_invoice tmInvoice', null,'tsv.job_summmeryId AS jobId, tsv.order_id AS orderId, tsv.po_number AS poNumber, tc.iClientId AS clientId, tc.vAddress1 AS companyAddress, tc.vEmailAddress  AS companyEmail, tc.vPhone AS companyPhone, tc.vCodeRights AS company_code, tu.iUserId AS freelanceId, tu.vUserName AS freelanceName, tu.vEmailAddress AS freelanceEmail, tu.vAddress1 AS freelanceAddress, tu.vProfilePic AS freelancePic, tu.iMobile AS freelancePhone, tsv.job_code AS jobCode, tmInvoice.invoice_number, tmInvoice.invoice_id, tmInvoice.invoice_status, tmInvoice.Invoice_cost, tmInvoice.paid_amount');
        foreach ($data as $key => $value) {
            $companyName = self::getAll('abbrivation',substr($value['company_code'],0,-2),'tms_centers');
            $data[$key]['companyName'] = isset($companyName[0]['name'])?$companyName[0]['name']:''; 
        }
        
        return $data;
    }


    // //getInvoicePartPayments
    public function getInvoicePartPayments($id) {
        $this->_db->where('invoice_id', $id);
        $data = $this->_db->get('tms_invoice_payments');
        return $data;
    }
}