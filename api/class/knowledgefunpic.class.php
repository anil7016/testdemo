<?php

require_once 'users.class.php';
require_once 'client.class.php';
require_once 'tasktype.class.php';

class Knowledgefunpic {
	//price1 data manage
    public function __construct() {
        $this->_db = db::getInstance();   
    }

    public function funpicGet($id) {
        $this->_db->join('tms_know_pic_like tkpl', "tkpl.`pic_id` = tkp.pic_id and tkpl.userId=$id", 'LEFT');
        $this->_db->join('tms_users tu', 'tkp.`user_id` = tu.iUserId', 'INNER');
        $this->_db->orderBy("tkp.pic_id","Desc");
        $this->_db->where('tu.eUserStatus',3);
        $data = $this->_db->get('`tms_knowledge_pic` tkp',null, 'tkpl.*, tkp.*, tu.vUserName');
        //echo $this->_db->getLastQuery();exit;
        return $data;
    }
    public function getReportedImages() {
        $query = "SELECT * FROM tms_knowledge_pic As tkp INNER JOIN tms_report_image As tri on tkp.funny_pic = tri.reported_image INNER JOIN tms_users AS tu ON tu.iUserId = tri.reportedUserId WHERE tu.eUserStatus = 3 ORDER BY tkp.pic_id DESC";
        // $query = "SELECT * FROM tms_knowledge_pic As tkp INNER JOIN tms_report_image As tri on tkp.funny_pic = tri.reported_image WHERE 'eUserStatus'= 3  ORDER BY tkp.pic_id DESC";
        $data = $this->_db->rawQuery($query);
        return $data;
    }
    public function UpdateImageStatus($imgName,$status) {
        $data = $this->_db->rawQuery("UPDATE tms_knowledge_pic SET status = '$status' WHERE funny_pic = '$imgName'");
    }


}