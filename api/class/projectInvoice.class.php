<?php

class projectInvoice {

    public function __construct() {
        $this->_db = db::getInstance();
    }

    public function projectinvoiceGet($id) {
        if($id=='past') {
            $this->_db->where('tpo.project_status',Array('Approved','Reject','Send','Save','Cancel'),'IN');
            $this->_db->join('tms_customer tc','tpo.order_id=tc.order_id','LEFT');
            $this->_db->join('tms_general tg','tg.order_id=tc.order_id','LEFT');
            $this->_db->join('tms_users tu','tu.iUserId=tc.project_manager','LEFT');
            $this->_db->join('tms_client tcl','tcl.iClientId=tc.client','INNER');
            $data = $this->_db->get('tms_order tpo',null,'tg.order_no projectNumber,tu.vUserName projectManager,tg.project_name projectName, tpo.created_date projectCreated,tpo.project_status projectStatus, tpo.order_id orderId,tcl.iClientId clientId,tcl.vUserName clientName');
        } else {
            $this->_db->where('tpo.project_status',Array('Approved','Reject','Send','Save','Cancel'),'NOT IN');
            $this->_db->join('tms_customer tc','tpo.order_id=tc.order_id','INNER');
            $this->_db->join('tms_general tg','tg.order_id=tc.order_id','INNER');
            $this->_db->join('tms_users tu','tu.iUserId=tc.project_manager','INNER');
            $this->_db->join('tms_client tcl','tcl.iClientId=tc.client','INNER');
            $data = $this->_db->get('tms_order tpo',null,'tg.order_no projectNumber,tu.vUserName projectManager,tg.project_name projectName, tpo.created_date projectCreated,tpo.project_status projectStatus, tpo.order_id orderId,tcl.iClientId clientId,tcl.vUserName clientName');
        }
        return $data;
    }
    
    public function projectinvoiceGetOne($id, $invoiceStatus) {
        if($invoiceStatus=='past') {
            $this->_db->where('tpo.order_id',$id);
            $this->_db->where('tpo.project_status',Array('Approved','Reject','Send','Save','Cancel'),'IN');
            $this->_db->join('tms_customer tc','tpo.order_id=tc.order_id','LEFT');
            $this->_db->join('tms_general tg','tg.order_id=tc.order_id','LEFT');
            $this->_db->join('tms_users tu','tu.iUserId=tc.project_manager','LEFT');
            $this->_db->join('tms_users tco','tc.project_coordinator=tco.iUserId','LEFT');
            $this->_db->join('tms_users tqa','tc.QA_specialist=tqa.iUserId','LEFT');
            // $this->_db->join('tms_items itm','itm.order_id=tpo.order_id','LEFT');
            $data = $this->_db->get('tms_order tpo',null,'tg.order_no projectNumber,tu.vUserName projectManager,tg.project_name projectName, tpo.created_date projectCreated,tpo.project_status projectStatus, tpo.order_id orderId,tco.vUserName Coordinator,tqa.vUserName QaSpecialist,tg.specialization Specialization, tg.due_date Duedate');
        } else {
            $this->_db->where('tpo.order_id',$id);
            $this->_db->where('tpo.project_status',Array('Approved','Reject','Send','Save','Cancel'),'NOT IN');
            $this->_db->join('tms_customer tc','tpo.order_id=tc.order_id','INNER');
            $this->_db->join('tms_general tg','tg.order_id=tc.order_id','INNER');
            $this->_db->join('tms_users tu','tu.iUserId=tc.project_manager','INNER');
            $this->_db->join('tms_users tco','tc.project_coordinator=tco.iUserId','INNER');
            $this->_db->join('tms_users tqa','tc.QA_specialist=tqa.iUserId','INNER');
            // $this->_db->join('tms_items itm','itm.order_id=tpo.order_id','LEFT');
            $data = $this->_db->get('tms_order tpo',null,'tg.order_no projectNumber,tu.vUserName projectManager,tg.project_name projectName, tpo.created_date projectCreated,tpo.project_status projectStatus, tpo.order_id orderId,tco.vUserName Coordinator,tqa.vUserName QaSpecialist,tg.specialization Specialization, tg.due_date Duedate');
        }
        
        return $data;
    }
}
