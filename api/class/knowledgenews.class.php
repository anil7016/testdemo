<?php

require_once 'users.class.php';
require_once 'client.class.php';
require_once 'tasktype.class.php';

class Knowledgenews {
	//price1 data manage
    public function __construct() {
        $this->_db = db::getInstance();   
    }
    
    public function knowledgeNewsSave($data) {
        $data['created_date'] = date('Y/m/d H:i:s');
        $data['modified_date'] = date('Y/m/d H:i:s');
        $id = $this->_db->insert('tms_knowledge_news', $data);
        if($id) {
            $result['status'] = 200;
            $result['msg'] = "Inserted Sucessfully";
        } else {
            $result['status'] = 401;
            $result['msg'] = "Not Inserted";
        }
        return $result;
    }
    
    public function knowledgeNewsgetAll() {
        $this->_db->orderBy('k_news_id','DESC');
        //$this->_db->where('eUserStatus',3);//new Added after Set Inactive in users
        $data = $this->_db->get('tms_knowledge_news');
        return $data;
    }

    public function knowledgeNewsgetOne($id) {
        $this->_db->where('k_news_id', $id);
        $data = $this->_db->getOne('tms_knowledge_news');
        return $data;
    }

    public function knowledgeNewsDelete($id) {
        $this->_db->where('k_news_id', $id);
        return $this->_db->delete('tms_knowledge_news');
    }
    
    public function knowledgeNewsUpdate($id, $data) {
        $data['modified_date'] = date('Y/m/d H:i:s');
        $this->_db->where('k_news_id', $id);
        $id = $this->_db->update('tms_knowledge_news', $data);
        if($id) {
            $result['status'] = 200;
            $result['msg'] = "Inserted Sucessfully";
        } else {
            $result['status'] = 401;
            $result['msg'] = "Not Inserted";
        }
        return $result;
    }
    
}