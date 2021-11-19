<?php
require_once 'users.class.php';
require_once 'client.class.php';
require_once 'tasktype.class.php';
require_once 'functions.class.php';
require_once 'sendmail/class.phpmailer.php';

class customer {
    public function __construct() {
        $this->_db = db::getInstance();
    }
    
    public function save($info) {
        $info['created_date'] = date('Y-m-d H:i:s');
        $info['modified_date'] = date('Y-m-d H:i:s');
        $id = $this->_db->insert('tms_customer', $info);
        if ($id) {

            // getting projectId and name from tms_general
            $this->_db->where("order_id",$info['order_id']);
            $GeneralData = $this->_db->getOne('tms_general');
            if(!$GeneralData){
                $this->_db->where("order_id",$info['order_id']);
                $GeneralData = $this->_db->getOne('tms_general');
            }
            
            $clienID = $info['client'];
            $query = "SELECT * FROM `tms_filemanager` AS tf inner JOIN (SELECT `fmanager_id` As Fid FROM `tms_filemanager` WHERE `client_id` = $clienID && `parent_id` = 0) AS demo ON demo.Fid = tf.parent_id AND tf.name = 'Projects'";
            
            $projectDefaultFolder = $this->_db->rawQuery($query);
            
            $ProjectFolderName = $GeneralData['order_no'];
            $ProjectFolderParentId = $projectDefaultFolder[0]['fmanager_id'];
            $projectOrderId = $info['order_id'];
            
            $insert = "INSERT INTO tms_filemanager (name,parent_id,order_id,is_project_folder) VALUES ('$ProjectFolderName',$ProjectFolderParentId,$projectOrderId,1)";
            
            $this->_db->rawQuery($insert);
            
            $this->_db->where("order_id",$info['order_id']);
            $this->_db->where("name",$ProjectFolderName);
            $pData = $this->_db->getOne('tms_filemanager');
            
            $jobFolderPid = $pData['fmanager_id'];
            $jobsFolderOrderId = $info['order_id'];
            $query1 = "INSERT INTO tms_filemanager (name,parent_id,order_id,is_default_folder) VALUES ('Jobs',$jobFolderPid,$jobsFolderOrderId,1)";
            $this->_db->rawQuery($query1);
            
            $return['status'] = 200;
            $return['msg'] = 'Successfully Inserted.';
            

        } else {
            $return['status'] = 422;
            $return['msg'] = 'Not Saved.';
        }
        return $return;
    }
    
    public function getByorderID($id) {
        $this->_db->where("order_id", $id);
        $results = $this->_db->getOne('tms_customer');
        if(isset($results['client'])) {
        	$client = new client();
        	$data = $client->getClientByField($results['client'],$field_name = "");
            if($data) {
                $results['memo'] = $data[0]['tMemo'];     
            }
        }
        return $results;
    }

    public function update($id, $data) {
        $data['modified_date'] = date('Y-m-d H:i:s');
        $this->_db->where("c_id", $id);
        $id = $this->_db->update('tms_customer', $data);
        if ($id) {
            $return['status'] = 200;
            $return['msg'] = 'Successfully Updated.';
            $this->_db->where("iClientId",$data['indirect_customer']);
            $IndirectClientData = $this->_db->getOne('tms_client_indirect');
            if($IndirectClientData){
                $return['indirectData'] = $IndirectClientData;
            }
        } else {
            $return['status'] = 422;
            $return['msg'] = 'Not Updated.';
        }
        return $return;
    }
    
    public function customerContactPerson($contactPerid, $orderId){
        
        $this->_db->where("vUserName", $contactPerid);
        $data = $this->_db->getone(' tms_users');
        
        if($data){
            $this->_db->where("order_id", $orderId);
            $info = $this->_db->getone('tms_general');            
        }
        
        if (isset($data['vEmailAddress'])){
            $to = $data['vEmailAddress'];
        }else{
            $to = "";
        }
        //return $info;
        if (isset($info['order_no']))
            $jobNo = $info['order_no'];
        else
            $jobNo = "";
        if (isset($info['description']))
            $description = $info['description'];
        else
            $description = "";
        if (isset($info['project_name']))
            $resource = $info['project_name'];
        else
            $resource = "";
        if (isset($info['due_date']))
            $dueDate = $info['due_date'];
        else
            $dueDate = "";
        if (isset($info['late_delivery']))
            $itemStatus = $info['late_delivery'];
        else
            $itemStatus = "";

        $body = "<p>Hello</p>";
        $body .= "<p>Following Job has been created and assigned to you.</p>";
        $body .= "<p>Job No. : " . $jobNo . ",</p>";
        $body .= "<p>Detail : " . $description . ",</p>";
        $body .= "<p>Project Name : " . $resource . ",</p>";
        $body .= "<p>Due Date : " . $dueDate . ",</p>";
        $body .= "<p>Item Status : " . $itemStatus . ".</p>";
        $subject = "Job Information";
        $this->_mailer = new PHPMailer();
        //$this->_mailer = 'ISO-8859-1';
        $this->_mailer->IsSMTP();
        $this->_mailer->Host = "ssl://smtp.gmail.com";
        $this->_mailer->SMTPAuth = "true";
        $this->_mailer->Port = "465";
        $this->_mailer->Username = "ChrisGilesItWorks@gmail.com";
        $this->_mailer->Password = "Admin@Kanhasoft@123";

        $this->_mailer->From = "Kanhasoft.com";
        $this->_mailer->FromName = "Kanhasoft";

        $this->_mailer->Subject = "Job Detail's";

        $this->_mailer->Body = $body;
        $this->_mailer->WordWrap = 50;
        $this->_mailer->AddAddress($to);
        $this->_mailer->IsHTML(true);

        if ($this->_mailer->Send()) { //output success or failure messages
            $result['status'] = 200;
            $result['msg'] = 'Thank you for your email';
        } else {
            $result['status'] = 200;
            $result['msg'] = 'Could not send mail!';
        }
        return $result;
    }
    
    public function clientContactEmail($clientId,$orderId) {
        $this->_db->where("iClientId", $clientId);
        $data = $this->_db->getone('tms_client');
        if($data) {
            $this->_db->where("order_id", $orderId);
            $info = $this->_db->getone('tms_general');            
        }
        
        if (isset($data['vEmailAddress']))
            $to = $data['vEmailAddress'];       
        else
            $to = "";
        //return $info;
        if (isset($info['order_no']))
            $jobNo = $info['order_no'];
        else
            $jobNo = "";
        if (isset($info['description']))
            $description = $info['description'];
        else
            $description = "";
        if (isset($info['project_name']))
            $resource = $info['project_name'];
        else
            $resource = "";
        if (isset($info['due_date']))
            $dueDate = $info['due_date'];
        else
            $dueDate = "";
        if (isset($info['late_delivery']))
            $itemStatus = $info['late_delivery'];
        else
            $itemStatus = "";

        $body = "<p>Hello</p>";
        $body .= "<p>Following Job has been created and assigned to you.</p>";
        $body .= "<p>Job No. : " . $jobNo . ",</p>";
        $body .= "<p>Detail : " . $description . ",</p>";
        $body .= "<p>Project Name : " . $resource . ",</p>";
        $body .= "<p>Due Date : " . $dueDate . ",</p>";
        $body .= "<p>Item Status : " . $itemStatus . ".</p>";
        $subject = "Job Information";
        $this->_mailer = new PHPMailer();
        //$this->_mailer = 'ISO-8859-1';
        $this->_mailer->IsSMTP();
        $this->_mailer->Host = "ssl://smtp.gmail.com";
        $this->_mailer->SMTPAuth = "true";
        $this->_mailer->Port = "465";
        $this->_mailer->Username = "ChrisGilesItWorks@gmail.com";
        $this->_mailer->Password = "Admin@Kanhasoft@123";

        $this->_mailer->From = "Kanhasoft.com";
        $this->_mailer->FromName = "Kanhasoft";

        $this->_mailer->Subject = "Job Detail's";

        $this->_mailer->Body = $body;
        $this->_mailer->WordWrap = 50;
        $this->_mailer->AddAddress($to);
        $this->_mailer->IsHTML(true);

        if ($this->_mailer->Send()) { //output success or failure messages
            $result['status'] = 200;
            $result['msg'] = 'Thank you for your email';
        } else {
            $result['status'] = 200;
            $result['msg'] = 'Could not send mail!';
        }
        return $result;
    }
    
    public function customergroupeMail($groupId, $orderId)  {
        $this->_db->where("vUserName", $groupId);
        $data = $this->_db->getone('tms_users');
        if($data) {
            $this->_db->where("order_id", $orderId);
            $info = $this->_db->getone('tms_general');            
        }
        
        if (isset($data['vEmailAddress']))
            $to = $data['vEmailAddress'];       
        else
            $to = "";
        //return $info;
        if (isset($info['order_no']))
            $jobNo = $info['order_no'];
        else
            $jobNo = "";
        if (isset($info['description']))
            $description = $info['description'];
        else
            $description = "";
        if (isset($info['project_name']))
            $resource = $info['project_name'];
        else
            $resource = "";
        if (isset($info['due_date']))
            $dueDate = $info['due_date'];
        else
            $dueDate = "";
        if (isset($info['late_delivery']))
            $itemStatus = $info['late_delivery'];
        else
            $itemStatus = "";

        $body = "<p>Hello</p>";
        $body .= "<p>Following Job has been created and assigned to you.</p>";
        $body .= "<p>Job No. : " . $jobNo . ",</p>";
        $body .= "<p>Detail : " . $description . ",</p>";
        $body .= "<p>Project Name : " . $resource . ",</p>";
        $body .= "<p>Due Date : " . $dueDate . ",</p>";
        $body .= "<p>Item Status : " . $itemStatus . ".</p>";
        $subject = "Job Information";
        $this->_mailer = new PHPMailer();
        //$this->_mailer = 'ISO-8859-1';
        $this->_mailer->IsSMTP();
        $this->_mailer->Host = "ssl://smtp.gmail.com";
        $this->_mailer->SMTPAuth = "true";
        $this->_mailer->Port = "465";
        $this->_mailer->Username = "ChrisGilesItWorks@gmail.com";
        $this->_mailer->Password = "Admin@Kanhasoft@123";

        $this->_mailer->From = "Kanhasoft.com";
        $this->_mailer->FromName = "Kanhasoft";

        $this->_mailer->Subject = "Job Detail's";

        $this->_mailer->Body = $body;
        $this->_mailer->WordWrap = 50;
        $this->_mailer->AddAddress($to);
        $this->_mailer->IsHTML(true);
        
        if ($this->_mailer->Send()) { //output success or failure messages
            $result['status'] = 200;
            $result['msg'] = 'Thank you for your email';
        } else {
            $result['status'] = 200;
            $result['msg'] = 'Could not send mail!';
        }
        return $result;
    }
    
    public function viewProjectCustomerDetail($id){
        $this->_db->where("order_id", $id);
        $results = $this->_db->getOne('tms_customer');
        if($results['client']){
            $client = new client();
            $data = $client->getClientByField($results['client'],$field_name = "");
            $results['memo'] = $data[0]['tMemo']; 
            $results['client'] = $data[0]['vUserName'];
        }
        if($results['contact']){
            $this->_db->where("iContactId", $results['contact']);
            $data = $this->_db->getOne('tms_client_contact');
            $results['contact'] = $data['vFirstName'] . " " . $data['vLastName'];
        }   
        if($results['indirect_customer']){
            $this->_db->where("iClientId", $results['indirect_customer']);
            $data = $this->_db->getOne('tms_client_indirect');
            $results['indirect_customer'] = $data['vUserName'];
        }
        //print_r($results);exit;
        return $results;        
    }

    public function getClientIndirectClient($id) {
        $this->_db->where("order_id", $id);
        $results = $this->_db->getOne('tms_customer');
        
        $this->_db->where("order_id", $id);
        $order = $this->_db->getOne('tms_order');

        if($results && $order){
            $this->_db->where("iClientId", $results['indirect_customer']);
            $data = $this->_db->getOne('tms_client_indirect');
            $orderData['indirect_customer'] = $data['vUserName'];
            $orderData['abbrivation'] = $order['abbrivation'];
            $orderData['order_number'] = $order['order_number'];
        }else{
            $orderData['status'] = 403;
            $orderData['msg'] = 'not found';
        }
        return $orderData;

    }
}
