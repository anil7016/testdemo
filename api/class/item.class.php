<?php
require_once 'filemanager.class.php';
require_once 'currency.class.php';
require_once 'order.class.php';
require_once 'customer.class.php';
require_once 'client.class.php';

class item {

    public function __construct() {
        $this->_db = db::getInstance();
    }

    public function save($info) {
        $info['created_date'] = date('Y-m-d H:i:s');
        $info['modified_date'] = date('Y-m-d H:i:s');
        $id = $this->_db->insert('tms_item', $info);
        if ($id) {
            $return['status'] = 200;
            $return['msg'] = 'Successfully Inserted.';
        } else {
            $return['status'] = 422;
            $return['msg'] = 'Not Saved.';
        }
        return $return;
    }

     public function getByOrder($id) {
        $this->_db->where("order_id", $id);
        return $results = $this->_db->getOne('tms_proj_language');
    }

    public function update($id, $data) {
        $data['modified_date'] = date('Y-m-d H:i:s');
        $this->_db->where("pl_id", $id);
        $id = $this->_db->update('tms_item', $data);
        if ($id) {
            $return['status'] = 200;
            $return['msg'] = 'Successfully Inserted.';
        } else {
            $return['status'] = 422;
            $return['msg'] = 'Not Saved.';
        }
        return $return;
    }

    public function ItemSave($data) {
        $data['created_date'] = date('Y-m-d H:i:s');
        $data['updated_date'] = date('Y-m-d H:i:s');
        if(isset($data['due_date'])) {
            $yesterday = strtotime($data['due_date']);
            $dueDate = date("d-m-Y", $yesterday);
            $date = explode("-",$dueDate);
            $data['q_date'] = $date[1].'-'.$date[2];
        }
        $id = $this->_db->insert('tms_items', $data);
        if ($id) {
            $return['status'] = 200;
            $return['msg'] = 'Successfully Inserted.';
        } else {
            $return['status'] = 422;
            $return['msg'] = 'Not Saved.';
        }
        return $return;
    }

    public function itemsGet($id) {
        $data = $this->_db->rawQuery("SELECT ti.*,tg.project_name FROM `tms_items` As ti INNER JOIN tms_general As tg ON ti.order_id = tg.order_id WHERE ti.order_id = $id");
        return $data;
    }

    public function itemsgetone($id) {
        $this->_db->where("itemId", $id);
        $results = $this->_db->getOne('tms_items');
        return $results; 
    }

    public function ItemUpdate($id,$data) {
        unset($data['project_name']);
        if(isset($data['currencyRate'])) {
            $cur['currency'] = $data['currencyRate'];
            $cur['modified_date'] = date('Y-m-d H:i:s');
            $this->_db->where('order_id',$data['order_id']);    
            $this->_db->update('tms_order',$cur);
        }        

        $data['updated_date'] = date('Y-m-d H:i:s');
        if(isset($data['due_date'])) {
            $yesterday = strtotime($data['due_date']);
            $dueDate = date("d-m-Y", $yesterday);
            $date = explode("-",$dueDate);
            $data['q_date'] = $date[1].'-'.$date[2];
        }
        unset($data['currency'],$data['currencyRate']);
        $this->_db->where("itemId", $id);
        $idd = $this->_db->update('tms_items', $data);
        if ($idd) {
            $info['user_id'] = 0;
            $this->_db->where('item_id',$id);
            $this->_db->update('tms_filemanager',$info);
            $return['status'] = 200;
            $return['msg'] = 'Successfully Updated.';
        } else {
            $return['status'] = 422;
            $return['msg'] = 'Not Updated.';
        }
        return $return;
    }

    public function itemDelete($idItem,$orderId) { 
        $id = explode('-',$idItem)[0];
        $iNumber = explode('-',$idItem)[1];
        

        $this->_db->where('order_id',$orderId);
        $this->_db->where('item_id',$iNumber);
        $jobAvailable = $this->_db->get('tms_summmery_view');
        
        if(!$jobAvailable){
            $this->_db->where('itemId',$id);
            $data = $this->_db->getOne('tms_items');
            if($data) {
                $itemId = $data['item_number'];
                $this->_db->where('item_id',$itemId);
                $this->_db->where('order_id',$orderId);
                $this->_db->delete('tms_summmery_view');

                $this->_db->where('item_id',$id);
                $this->_db->delete('tms_filemanager');

                $this->_db->where('itemId',$id);
                $data = $this->_db->delete('tms_items');
            } else {
                $data = 0;  
            }
        }else{
            $data['status'] = 422;
            $data['msg'] = 'You can not delete because jobs available in this scoop.';
        }    
        
        return $data; 
    }

    public function contactPersonget($id) {
        $this->_db->where('order_id',$id);
        $data = $this->_db->getone('tms_customer');
        $getcust = array($data['project_coordinator'],$data['project_manager'],$data['QA_specialist']);
        $users = new users();
        $customer = array();
        foreach ($getcust as $key => $value) {
            $cust = $users->getUser($value,"");
            array_push($customer, $cust);
        }
        return $customer;   
    }

    public function itemsNumberGet($id) {
        $file = new filemanager();
        $fileGet = $file->filegetByfield('order_id',$id);

        $this->_db->where('order_id',$id);
        $info = $this->_db->get('tms_items');
        if($info) {
            foreach($info As $in) {
                $itemId = $in['item_number'];
            }
        } else {
            $itemId = 0;  
        }

        $item['order_id'] = $id;
        $item['item_number'] = 0;
        $item['created_date'] = date('Y-m-d H:i:s');
        $item['updated_date'] = date('Y-m-d H:i:s');
        $itmId = $this->_db->insert('tms_items',$item);
        if($itmId) {
            $data['order_id'] = $id;
            $data['parent_id'] = $fileGet['fmanager_id'];
            $data['user_id'] = 1;
            $data['item_id'] = $itmId;
            $data['name'] = "item".str_pad($itemId + 1, 3, '0', STR_PAD_LEFT);
            $data['created_date'] = date('Y-m-d H:i:s');
            $data['updated_date'] = date('Y-m-d H:i:s');
            $this->_db->insert('tms_filemanager',$data);    
        }
        $idd['itemId'] = $itmId;
        $idd['itemNumber'] = $itemId;
        return $idd;
    }

    public function saveDirectory($data) {
        $name = $data['item_no'];
        $dir = $output_file = UPLOADS_ROOT . $name;
        if(!is_dir($dir)) {
            mkdir( $dir, 0777, true );
        }
    }
    
    public function itemFilemanagerCheck($id){
        $this->_db->where('order_id',$id);
        $this->_db->where('user_id',1);
        $data = $this->_db->getOne('tms_filemanager');
        if($data) {
            $this->_db->where('parent_id',$data['fmanager_id']);
            $this->_db->delete('tms_filemanager');

            $this->_db->where('fmanager_id',$data['fmanager_id']);
            $info = $this->_db->delete('tms_filemanager');

            $this->_db->where('order_id',$id);
            $this->_db->where('item_number',0);
            $info = $this->_db->delete('tms_items');
        } else {
            $info = "No Item";
        }
        return $info;
    }

    public function orderCurrencyMatch ($id) {
        $currency = new currency();
        $default = $currency->defaultCurrencyGet();

        $order = new order();
        $data = $order->getById($id);

        $result['currency'] = $data['currency'];
        $result['defCurrency'] = $default['country_name'];
        return $result;
    }

    public function itemCurrencyGet($id) {
        $currency = new currency();
        $default = $currency->defaultCurrencyGet();        
        $from = $default['country_name'];
        $to = $id;
        $amount = urlencode(1);
        $from_Currency = urlencode($from);
        $to_Currency = urlencode($to);
        //echo 'https://www.google.com/finance/converter?a=1&from='.$from.'&to='.$to;exit;
        $get = file_get_contents("https://www.google.com/finance/converter?a=1&from=$from&to=$to");
        $get = explode("<span class=bld>",$get);
        $get = explode("</span>",$get[1]);  
        $converted_amount = preg_replace("/[^0-9.]/", null, $get[0]);
        $result['currency'] = $id;
        $result['defCurrency'] = $default['country_name'];
        $result['currencyRate'] = round($converted_amount,3);
        return $result;
    }

    public function AddNumberOfItems($data) {
        // /$data = $this->_db->rawQuery("SELECT COUNT(items) AS NumberOfProducts FROM TMS;");
        $this->_db->where('order_id',$data['order_id']);
        $info = $this->_db->get('tms_items');
        if($info) {
            foreach($info As $in) {
                $itemId = $in['item_number'];
            }
        } else {
            $itemId = 0;  
        }
        
        $no_of_items = $data['no_of_items'];
        $order_id = $data['order_id'];
        if($data['order_id']){
            $this->_db->where('order_id',$order_id);
            $this->_db->where('is_project_folder',1);
            $info = $this->_db->getOne('tms_filemanager');
            
            //Get Client from order Id
            $client = new customer();
            $result = $client->getByorderID($order_id);
            
            $Indirectclient = new client();
            $IcData = $Indirectclient->getIndirectClient($result['indirect_customer']);
            
            $itemName = $IcData['vUserName'].' | English (US) - English (US)';
            $contactPerson = $result['client'];
            //echo $itemId;exit;
            for($i=$itemId+1;$i<=$no_of_items+$itemId;$i++){
                $Idata['item_number'] =$i;
                $Idata['contact_person'] =$contactPerson;
                $Idata['item_name'] =$itemName;
                $Idata['order_id'] = $data['order_id'];
                $Idata['created_date'] = date('Y-m-d H:i:s');
                $Idata['updated_date'] = date('Y-m-d H:i:s');

                //Item Start Date
                /*$currentDate = date('Y-m-d H:i:s');
                $date= explode(' ',$currentDate);
                $convertedDate = explode('-',$date[0]);//Like 25.10.2017
                $Idata['start_date'] = $convertedDate[2].'.'.$convertedDate[1].'.'.$convertedDate[0];*/
                $Idata['start_date'] = date('Y-m-d H:i:s');
                
                $ItemId = $this->_db->insert('tms_items',$Idata);
                if($ItemId){
                    $fdata['name'] = 'Item'.str_pad($i,3, "0", STR_PAD_LEFT);
                    $fdata['order_id'] = $data['order_id'];
                    $fdata['item_id'] = $ItemId;
                    $fdata['parent_id'] = $info['fmanager_id'];
                    $fdata['created_date'] = date('Y-m-d H:i:s');
                    $fdata['updated_date'] = date('Y-m-d H:i:s');
                    $ItemId = $this->_db->insert('tms_filemanager',$fdata);
                }
            }

            $return['status'] = 200;
            $return['msg'] = 'Inserted';

        }else{

            $return['status'] = 422;
            $return['msg'] = 'Error';
        }

        return $return;
    }

    public function languagesGet() {
        $data = $this->_db->rawQuery("SELECT * FROM `tms_languages` WHERE is_active=1 ORDER BY `is_favourite`=1 DESC");
        return $data;
    }

    public function getFilestotal($id) {
        $data = $this->_db->rawQuery("SELECT COUNT(`parent_id`) as totalfile FROM `tms_filemanager` WHERE parent_id IN (SELECT `fmanager_id` FROM tms_filemanager WHERE `item_id`=$id) ");
        return $data;
    }

}

?>