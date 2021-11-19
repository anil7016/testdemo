<?php



require_once 'customer.class.php';

require_once 'client.class.php';



class order {



    public function __construct() {

        $this->_db = db::getInstance();

    }



    public function save($info) {

        //remove Unsaved orderData
        $this->_db->where('order_number',0);
        $this->_db->where('abbrivation',1);
        $this->_db->where('edited_by',0);
        $this->_db->where('edited_id',0);
        $this->_db->delete('tms_order');

        $info['created_date'] = date('Y-m-d H:i:s');

        $info['modified_date'] = date('Y-m-d H:i:s');

        $id = $this->_db->insert('tms_order', $info);

        if ($id) {

            $return['status'] = 200;

            $return['msg'] = 'Successfully Inserted.';

            $return['order_id'] = $id;

        } else {

            $return['status'] = 422;

            $return['msg'] = 'Not Saved.';

        }

        return $return;

    }

     

    public function getOrder($id) {

        $this->_db->join('tms_general tg','tor.order_id = tg.order_id','LEFT');

        //$this->_db->join('tms_customer tc','tg.order_id = tc.order_id','LEFT');

        $this->_db->where('tor.order_number !=0');

        $data = $this->_db->get('tms_order tor');

        $customer = new customer();

        $client = new client();

        foreach ($data as $key => $value) {

            $customer_data = $customer->getByorderID($value['order_id']);

            if (isset($customer_data['client'])) {

                $client_name = $client->getClientByField($customer_data['client'], "vUserName");

            } else {

                $client_name = "--";

            }

            $data[$key]['customer'] = $client_name;

            $id = $data[$key]['order_id'];

            if($id) {

            	$this->_db->where('abbrivation',1);

            	$id =  $this->_db->delete('tms_order');

            	if($id){

            		$data['delete'] =  1;

            	}

            }

        }

        

        return $data;

    }

    

    public function getTaskByUser($userId, $type) {

        $this->_db->where("iType", $type);

        $this->_db->where("iUserId", $userId);

        $results = $this->_db->get('tms_task');

        $users = new users();

        $client = new client();

        $tasktype = new tasktype();

        

        foreach ($results as $key => $value) {

            $type = $tasktype->getTypeById($value['vTaskType']);

            $results[$key]['task_type'] = $type['task_type'];

            $contact = $client->getClientByField($value['iContact']);

            $results[$key]['contact_person'] = $contact[0]['vClientName'];

            $responsible = $users->getUserByField($value['iPersonResponsible']);

            $results[$key]['person_responsible'] = $responsible[0]['vFirstName'] . ' ' . $responsible[0]['vLastName'];

        }

        

        $return['status'] = 200;

        $return['data'] = $results;

        return $return;

    }

    

    public function getTaskById($id) {

        $this->_db->where("iTaskId", $id);

        $results = $this->_db->getOne('tms_task');

        return $results;

    }

    

    public function getById($id) {

        $this->_db->where("order_id", $id);

        $results = $this->_db->getOne('tms_order');

        return $results;

    }

    

    public function getByOrderId($orderId, $userId) {

        //modified orders On edited user

        $data['modified_date'] = date('Y-m-d');

        $data['edited_by'] = $userId;

        $data['edited_id'] = 1;

        $this->_db->where("edited_by", 0);

        $this->_db->where("edited_id", 0);

        $this->_db->where("order_id", $orderId);

        $id = $this->_db->update("tms_order", $data);



        //get detail of orderId

        $this->_db->join('tms_users tu','tu.iUserId = tor.edited_by','LEFT');

        $this->_db->where("tor.edited_id!=0");

        $this->_db->where("tor.order_id", $orderId);

        $results = $this->_db->getOne("tms_order AS tor", "tor.*, tu.iUserId, tu.vUserName");

        if($results['edited_by'] == $userId) {

            $results['userName'] = "You";

        } else {

            $results['userName'] = $results['vUserName'];

        }

        return $results;

    }



    public function updateTask($id, $info) {

        $info['dtUpdatedDate'] = date('Y-m-d H:i:s');

        $this->_db->where('iTaskId', $id);

        $id = $this->_db->update('tms_task', $info);

        if ($id) {

            $return['status'] = 200;

            $return['msg'] = 'Successfully Updated.';

        } else {

            $return['status'] = 422;

            $return['msg'] = 'Not Saved.';

        }

        return $return;

    }



    public function delete($id) {

        $this->_db->where('order_id', $id);

        $id = $this->_db->delete('tms_order');

        if ($id) {

            $return['status'] = 200;

            $return['msg'] = 'Successfully Updated.';

        } else {

            $return['status'] = 422;

            $return['msg'] = 'Not Saved.';

        }

        return $return;

    }

    

    public function orderdataget($id){

    	$this->_db->where('iClientId',$id);

    	$data = $this->_db->getone('tms_client');

    	return $data['vCodeRights'];

    }

    

    public function orderupdatedataget($id,$oldid){

    	$this->_db->where('iClientId',$id);

    	$data = $this->_db->getone('tms_client');

        

        $this->_db->where('iClientId',$oldid);

    	$info = $this->_db->getone('tms_client');

    	if($data['vCodeRights'] == $info['vCodeRights']){

    		$result['vCodeRights'] = $data['vCodeRights'];

    		$result['digit'] = 0;

    	} else {

    		$result['vCodeRights'] = $data['vCodeRights'];

    		$result['digit'] = 1;

    	}

    	return $result;

    }

    

    public function orderNumberget($id){

        $this->_db->orderBy('order_id',"desc");

    	$this->_db->where('abbrivation',$id);

    	$data = $this->_db->get('tms_order',1);

    	if($data) {

            $info = $data[0]['order_number'];

    	} else {

    		$info = 0;

    	}        

    	return $info;

    }

    

    public function orderNumbergetupdate($id) {

    	$this->_db->where('abbrivation',$id);

    	$data = $this->_db->get('tms_order');

    	if($data) { 

    		foreach($data As $d) {

    		}

    		$info = $d['order_number'];

    	} else {

    		$info = 0;

    	}

    	return $info;

    }



    public function orderupdate($id,$data){

        $info = $data;

    	$this->_db->where('order_id',$id);

        $data['modified_date'] = date('Y-m-d H:i:s');

    	$data = $this->_db->update('tms_order',$data);

    	if ($data) {

    		$return['status'] = 200;

    		$return['msg'] = 'Successfully Updated.';

    	} else {

    		$return['status'] = 422;

    		$return['msg'] = 'Not Updated.';

    	}

        return $info;

    }

    

    public function jobitemsGet($id){

    	$itms = new item();

    	$result = $itms->itemsGet($id);

        

        /*Remove Item if not added amount and due date*/

        foreach($result as $key => $value) {

            if($value['total_amount'] == ''){

                array_splice($result, $key, 1);

            }

        }

        

        if($result){

            $info = array();

            foreach ($result AS $data){

                $d = array('item_number'=>'All');

                $info = array_push($d, $data);

            }

        }

    	return $result;

    }



    public function orderfileSave($id,$data) {

        $name = $data['order_number'];

        $query = "SELECT * FROM `tms_filemanager` WHERE name = '$name' AND order_id = $id";

        $projectDefaultFolder = $this->_db->rawQuery($query);

        // $info['name'] = $data['order_number'];

        // $info['order_id'] = $id;

        // $info['parent_id'] = 0;

        // $info['created_date'] = date('Y-m-d H:i:s');

        // $info['updated_date'] = date('Y-m-d H:i:s');

        // $id = $this->_db->insert('tms_filemanager', $info);

        if ($projectDefaultFolder) {

            $array = array('jobs');

            foreach ($array as $key => $value) {

                $info['name'] = $value;

                //$info['name'] =$id;

                $info['parent_id'] = $projectDefaultFolder[0]['fmanager_id'];

                $this->_db->insert('tms_filemanager',$info);

            }

            $return['status'] = 200;

            $return['msg'] = 'Successfully Saved.';

        } else {

            $return['status'] = 422;

            $return['msg'] = 'Not Saved.';

        }

        return $return;

    }



    public function jobItemQuantityget($id, $itemId) {

        $this->_db->where('item_number',$itemId);

        $this->_db->where('order_id',$id);

        return $this->_db->getOne('tms_items');

    }



    public function orderEditedByUpdate($id) {

        $data['modified_date'] = date('Y-m-d');

        $data['edited_by'] = 0;

        $data['edited_id'] = 0;

        $this->_db->where('order_id', $id);

        return $this->_db->update('tms_order', $data);

    }

    public function getOrderSingle($id) {

        $this->_db->where('order_id', $id);

        return $this->_db->getOne('tms_general');

    }

}

