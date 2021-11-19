<?php

class Category {
	//price1 data manage
    public function __construct() {
        $this->_db = db::getInstance();   
    }
    
    public function getAll($id, $field, $table) {
        $this->_db->where($field,$id);
        if($table == 'tms_knowledge_articles'){
            $this->_db->orderBy('article_id','DESC');
        }
        return $data = $this->_db->get($table);
    }
    
    public function categoryGet() {
        $this->_db->orderBy('category_id','DESC');
 		$data = $this->_db->get('tms_knowledge_category');
 		return $data;
    }
    
    public function categoryDetail($id) {
    	$data['data'] = self::getAll($id, 'category', 'tms_knowledge_articles');
        $data['info'] = self::getAll($id, 'category_id', 'tms_knowledge_category');
        return $data;
    }
    
    public function categoryArticle($id) {
        return self::getAll($id, 'article_id', 'tms_knowledge_articles');
    }
    
    public function searchResult() {
        return $this->_db->get('tms_knowledge_articles');
    }

    public function searchMemberResult($id) {
        $this->_db->groupBy ("tu.vUserName");
        $this->_db->join('tms_user_type AS tt', 'tu.vResourceType = tt.iTypeId', 'LEFT');
        $this->_db->where('iFkUserTypeId', $id);
        $this->_db->where('eUserStatus',3);//external or internal resource.
        $this->_db->where('activation_status',1);//external or internal resource.
        return $data = $this->_db->get('tms_users AS tu');
    }
}