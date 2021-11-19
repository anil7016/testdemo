<?php

class News {
    
    public function __construct() {
        $this->_db = db::getInstance();   
    }
    
    public function newsGetAll() {
        $this->_db->orderBy("k_news_id","desc");
        return $data = $this->_db->get('tms_knowledge_news');
    }
    
    public function newsGetOne($id) {
        $this->_db->where('k_news_id', $id);
        return $this->_db->getOne('tms_knowledge_news');
    }
    
    public function newsGetLatest() {
        $query = "select * from tms_knowledge_news order by k_news_id desc limit 7";
        return $data = $this->_db->rawQuery($query);
    }
    
}