<?php

class projectDetail {

    public function __construct() {
        $this->_db = db::getInstance();
    }
    
    public function projectGet() {
        $this->_db->join('tms_general tg','tor.order_id = tg.order_id','LEFT');
        $this->_db->join('tms_customer tc','tg.order_id = tc.order_id','LEFT');
        $this->_db->join('tms_users tu','tu.iUserId = tc.project_manager','LEFT');
        return $data = $this->_db->get('tms_order tor');
    }
    
    public function projectgetOne($id) {
        $this->_db->where('tor.order_id',$id);
        $this->_db->join('tms_general tg','tor.order_id = tg.order_id','LEFT');
        $this->_db->join('tms_customer tc','tg.order_id = tc.order_id','LEFT');
        $this->_db->join('tms_users tu','tu.iUserId = tc.project_manager','LEFT');
        return $data = $this->_db->getOne('tms_order tor');   
    }
    
    public function projectJobapprovedGet($id) {
        $this->_db->groupBy("tsv.job_summmeryId");
        $this->_db->where('tsv.order_id',$id);
        $this->_db->join('tms_general AS tg','tsv.order_id = tg.order_id','INNER');
        $this->_db->join('tms_filemanager AS tf','tsv.job_summmeryId = tf.job_id','INNER');
        $this->_db->join('tms_items AS ti','tsv.order_id = ti.order_id','LEFT');
        $this->_db->join('tms_proj_language AS tpl','ti.item_language = tpl.pl_id','LEFT');
        $this->_db->join('tms_users AS tu','tsv.resource = tu.iUserId','LEFT');
        $data = $this->_db->get('tms_summmery_view AS tsv',null,'tsv.*,tg.order_no AS company_code, tf.fmanager_id, ti.item_name AS description, tu.vUserName As userName');
        $dataCount = sizeof($data);
        $info = [];
        foreach($data AS $key=>$value) {
            if($value['item_status'] == 'Approved') {
                array_push($info, $value);
            }   
        }
        $infoCount = sizeof($info);
        if($dataCount==$infoCount) {
            return $info;   
        }
    }
    
    public function jobGetOnOrderId($id) {
        $this->_db->where('tsv.job_summmeryId',$id);
        $this->_db->join('tms_general AS tg','tsv.order_id = tg.order_id','INNER');
        $this->_db->join('tms_filemanager AS tf','tsv.job_summmeryId = tf.job_id','INNER');
        $this->_db->join('tms_items AS ti','tsv.order_id = ti.order_id','LEFT');
        $this->_db->join('tms_proj_language AS tpl','ti.item_language = tpl.pl_id','LEFT');
        $this->_db->join('tms_users AS tu','tsv.resource = tu.iUserId','LEFT');
        $data = $this->_db->get('tms_summmery_view AS tsv',null,'tsv.*,tg.order_no AS company_code, 
            tf.fmanager_id, ti.item_name AS description, tu.vUserName As userName');
        return $data;
    }
}
