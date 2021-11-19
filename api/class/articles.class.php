<?php

require_once 'users.class.php';
require_once 'client.class.php';
require_once 'tasktype.class.php';

class Articles {
	//price1 data manage
    public function __construct() {
        $this->_db = db::getInstance();   
    }

    public function knowledgeArticleSave($data) {
    	$data['created_date'] = date("Y-m-d H:i:s");
    	$data['modified_date'] = date("Y-m-d H:i:s");
    	$id = $this->_db->insert('tms_knowledge_articles', $data);
    	if($id) {
    		$result['status'] = 200;
    		$result['msg'] = 'Inserted Succefully';
    	} else {
    		$result['status'] = 401;
    		$result['msg'] = 'Not Inserted';
    	}
    	return $result;
    }
    
    public function knowledgeArticlegetAll() {
    	$this->_db->join('tms_knowledge_category AS c','c.category_id=a.category', 'INNER');
        $this->_db->orderBy('a.article_id','DESC');
    	return $this->_db->get('tms_knowledge_articles AS a');
    }    

    public function knowledgeArticlegetOne($id) {
    	$this->_db->where('article_id', $id);
    	return $this->_db->getOne('tms_knowledge_articles');
    }

    public function knowledgeArticleDelete($id) {	    	
    	$this->_db->where('article_id', $id);
    	return $this->_db->delete('tms_knowledge_articles');
    }

    public function knowledgeArticleUpdate($id, $data) {
    	$data['created_date'] = date('Y-m-d H:i:s');
    	$data['modified_date'] = date('Y-m-d H:i:s');
    	$this->_db->where('article_id', $id);
    	$id = $this->_db->update('tms_knowledge_articles', $data);
        if($id) {
    		$result['status'] = 200;
    		$result['msg'] = 'Updated Succefully';
        } else {
    		$result['status'] = 401;
    		$result['msg'] = 'Not Updated';
        }
        return $result;
    }    
}