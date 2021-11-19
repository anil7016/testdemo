<?php
require_once 'users.class.php';
require_once 'client.class.php';
class Invoice_due {
	
	public function __construct() {
		$this->_db = db::getInstance ();
	}
	
	public function save($data) {
		$data['created_date'] = date('Y-m-d');
		$data['modified_date'] = date('Y-m-d');
		$id = $this->_db->insert('tms_invoice_due_period', $data);
		if($id) {
			$result['status'] = 200;
			$result['msg'] = "Succefully inserted";
		} else {
			$result['status'] = 401;
			$result['msg'] = "Not inserted";
		}
		return $result;
	}
	
	public function getAllInvoicePeriod() {
		return $this->_db->get('tms_invoice_due_period');
	}
	
	public function getOneInvoicePeriod($id) {
		$this->_db->where('invoice_due_id', $id);
		return $this->_db->getOne('tms_invoice_due_period');
	}
	
	public function updateInvoicePeriod($id, $data) {
		$this->_db->where('invoice_due_id', $id);
		$id = $this->_db->update('tms_invoice_due_period', $data);
		if($id) {
			$result['status'] = 200;
			$result['msg'] = "Updated Succefully";
		} else {
			$result['status'] = 401;
			$result['msg'] = "Not Updated";
		}
		return $result;
	}
	
	public function deleteInvoicePeriod($id) {
		$this->_db->where('invoice_due_id', $id);
		return $this->_db->delete('tms_invoice_due_period');
	}
}

