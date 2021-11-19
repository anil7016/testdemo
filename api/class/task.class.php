<?php
require_once 'users.class.php';
require_once 'client.class.php';
require_once 'tasktype.class.php';
require_once 'functions.class.php';
require_once 'sendmail/class.phpmailer.php';
class Task {
	public function __construct() {
		$this->_db = db::getInstance ();
	}
	public function save($info) {
		$info ['dtCreatedDate'] = date ( 'Y-m-d H:i:s' );
		$info ['dtUpdatedDate'] = date ( 'Y-m-d H:i:s' );
		$id = $this->_db->insert ( 'tms_task', $info );
		if ($id) {
			$return ['status'] = 200;
			$return ['msg'] = 'Successfully Inserted.';
		} else {
			$return ['status'] = 422;
			$return ['msg'] = 'Not Saved.';
		}
		
		if (isset ( $info ['tMemo'] )) {
			$memo = $info ['tMemo'];
		} else {
			$memo = " ";
		}
		if (isset ( $info ['orderno'] )) {
			$order = $info ['orderno'];
		} else {
			$order = " ";
		}
		$users = new users ();
		$client = new client ();
		$tasktype = new tasktype ();
		$type = $tasktype->getTypeById ( $info ['iType'] );
		$results ['task_type'] = $type ['task_type'];
		$contact = $client->getClientByField ( $info ['iClientId'], $field_name = "" );
		$results ['ClientName'] = $contact [0] ['vUserName'];
		if ($results ['ClientName'] != "") {
			$clientName = $results ['ClientName'];
		} else {
			$clientName = "";
		}
		
		$results ['contact_person'] = $info ['iContact'];
		$responsible = $users->getUser ( $info ['iPersonResponsible'] );
		$results ['person_responsible'] = $responsible ['vFirstName'] . ' ' . $responsible ['vLastName'];
		$results ['completer'] = $responsible ['vEmailAddress'];

		$body = "<p> Hello " . $results ['person_responsible'] . "</p></br>";
		$body .= "<p> Following task has been created and assigned to you. Please address it at the earliest: </p>";
		$body .= "<p> " . $results ['task_type'] . "-" . $memo . "</p>";
		$body .= "<p> Contact person :" . $results ['contact_person'] . "</p>";
		$body .= "<p> Project NO :" . $order . "</p>";
		$body .= "<p> Direct Client name :" . $clientName . "</p>";
		$body .= "<p> This task has to be completed on or before " . $info ['vDue'] . "</p>";
		$body .= "<p> This task has a " . $info ['vPriority'] . " priority </p>";

		$subject = "Project Detail";
		$to = $results ['completer'];
		
		$this->_mailer = new PHPMailer ();
		// $this->_mailer = 'ISO-8859-1';
		$this->_mailer->IsSMTP ();
		$this->_mailer->Host = "ssl://smtp.gmail.com";
		$this->_mailer->SMTPAuth = "true";
		$this->_mailer->Port = "465";
		$this->_mailer->Username = "ChrisGilesItWorks@gmail.com";
		$this->_mailer->Password = "Admin@Kanhasoft@123";
		$this->_mailer->From = "Kanhasoft.com";
		$this->_mailer->FromName = "Kanhasoft";
		$this->_mailer->Subject = $subject;
		$this->_mailer->Body = $body;
		$this->_mailer->WordWrap = 50;
		$this->_mailer->AddAddress ( $to );
		$this->_mailer->IsHTML ( true );
		
		if ($this->_mailer->Send ()) // output success or failure messages
		{
			$result ['status'] = 200;
			$result ['msg'] = 'Thank you for your email';
		} else {
			$result ['status'] = 200;
			$result ['msg'] = 'Could not send mail!';
		}
		return $result;
	}
	public function getTaskByUser($userId, $type) {
		$this->_db->where ( "iType", $type );
		$this->_db->where ( "order_id", $userId );
		$results = $this->_db->get ( 'tms_task' );
		
		$users = new users ();
		$client = new client ();
		$tasktype = new tasktype ();
		foreach ( $results as $key => $value ) {
			$type = $tasktype->getTypeById ( $value ['vTaskType'] );
			$results [$key] ['task_type'] = $type ['task_type'];
			// $contact = $client->getClientByField ( $value ['iContact'], $field_name = "" );
			// $results [$key] ['contact_person'] = $contact [0] ['vUserName'];
			$responsible = $users->getUser ( $value ['iPersonResponsible'] );
			$results [$key] ['person_responsible'] = $responsible ['vFirstName'] . ' ' . $responsible ['vLastName'];
		}
		
		$return ['status'] = 200;
		$return ['data'] = $results;
		return $return;
	}
	public function getTaskById($id) {
		$this->_db->where ( "iTaskId", $id );
		$results = $this->_db->getOne ( 'tms_task' );
		return $results;
	}
	public function updateTask($id, $info) {
		$info ['dtUpdatedDate'] = date ( 'Y-m-d H:i:s' );
		$this->_db->where ( 'iTaskId', $id );
		$id = $this->_db->update ( 'tms_task', $info );
		if ($id) {
			$return ['status'] = 200;
			$return ['msg'] = 'Successfully Updated.';
		} else {
			$return ['status'] = 422;
			$return ['msg'] = 'Not Saved.';
		}
		
		if (isset ( $info ['tMemo'] )) {
			$memo = $info ['tMemo'];
		} else {
			$memo = " ";
		}
		if (isset ( $info ['orderno'] )) {
			$order = $info ['orderno'];
		} else {
			$order = " ";
		}
		// print_r ( $info );exit;
		$users = new users ();
		$client = new client ();
		$tasktype = new tasktype ();
		$type = $tasktype->getTypeById ( $info ['iType'] );
		$results ['task_type'] = $type ['task_type'];
		$contact = $client->getClientByField ( $info ['iClientId'], $field_name = "" );
		$results ['ClientName'] = $contact [0] ['vUserName'];
		if ($results ['ClientName'] != "") {
			$clientName = $results ['ClientName'];
		} else {
			$clientName = "";
		}
		
		$results ['contact_person'] = $info ['iContact'];
		$responsible = $users->getUser ( $info ['iPersonResponsible'] );
		$results ['person_responsible'] = $responsible ['vFirstName'] . ' ' . $responsible ['vLastName'];
		$results ['completer'] = $responsible ['vEmailAddress'];
		$body = "<p> Hello " . $results ['person_responsible'] . "</p></br>";
		$body .= "<p> Following task has been created and assigned to you. Please address it at the earliest: </p>";
		$body .= "<p> " . $results ['task_type'] . "-" . $memo . "</p>";
		$body .= "<p> Contact person :" . $results ['contact_person'] . "</p>";
		$body .= "<p> Project NO :" . $order . "</p>";
		$body .= "<p> Direct Client name :" . $clientName . "</p>";
		$body .= "<p> This task has to be completed on or before " . $info ['vDue'] . "</p>";
		$body .= "<p> This task has a " . $info ['vPriority'] . " priority </p>";
		
		$subject = "Project Detail";
		$to = $results ['completer'];
		
		$this->_mailer = new PHPMailer ();
		// $this->_mailer = 'ISO-8859-1';
		$this->_mailer->IsSMTP ();
		$this->_mailer->Host = "ssl://smtp.gmail.com";
		$this->_mailer->SMTPAuth = "true";
		$this->_mailer->Port = "465";
		$this->_mailer->Username = "ChrisGilesItWorks@gmail.com";
		$this->_mailer->Password = "Admin@Kanhasoft@123";
		
		$this->_mailer->From = "Kanhasoft.com";
		$this->_mailer->FromName = "Kanhasoft";
		
		$this->_mailer->Subject = $subject;
		
		$this->_mailer->Body = $body;
		$this->_mailer->WordWrap = 50;
		$this->_mailer->AddAddress ( $to );
		$this->_mailer->IsHTML ( true );
		
		if ($this->_mailer->Send ()) // output success or failure messages
		{
			$result ['status'] = 200;
			$result ['msg'] = 'Thank you for your email';
		} else {
			$result ['status'] = 200;
			$result ['msg'] = 'Could not send mail!';
		}
		return $result;
	}
	public function deleteTask($id) {
		$this->_db->where ( 'iTaskId', $id );
		$id = $this->_db->delete ( 'tms_task' );
		if ($id) {
			$return ['status'] = 200;
			$return ['msg'] = 'Successfully Updated.';
		} else {
			$return ['status'] = 422;
			$return ['msg'] = 'Not Saved.';
		}
		return $return;
	}
	public function usertaskodueDategate($id) {
		if(isset($id)) {
			$client_id = " ";
			$general = new general ();
			$data = $general->getByorderID ( $id, $client_id );
			if(isset($data ['due_date'])) {
				return $data ['due_date'];		
			}	
		}
	}
	public function projectNumberget($id) {
		$this->_db->where ( 'order_id', $id );
		$id = $this->_db->getone ( 'tms_general' );
		return $id['order_no'];
	}

	public function projectTaskContactPer($id){
		$this->_db->where ( 'iContactId', $id );
		$id = $this->_db->getone ( 'tms_client_contact' );
		$data = $id['vFirstName']  . " " .  $id['vLastName'];
		return $data;	
	}
}
