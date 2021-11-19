<?php

require_once 'users.class.php';
require_once 'client.class.php';
require_once 'tasktype.class.php';

class Category {
	//price1 data manage
	    public function __construct() {
	        $this->_db = db::getInstance();
	        
	    }
	    
	    public function categorySave($data) {
	    	$data['created_date'] = date('Y-m-d H:i:s');
	    	$data['modified_date'] = date('Y-m-d H:i:s');
	    	$id = $this->_db->insert('tms_knowledge_category',$data);	    	
	    	if($id) {
	    		$result['result'] = 200;
	    		$result['msg'] = "Inserted Succefully";
	    	} else {
	    		$result['result'] = 401;
	    		$result['msg'] = "Not Inserted";
	    	}
	    	return $result;
	    }

	    public function KcategorygetAll() {
	    	$this->_db->orderBy('category_id','DESC');
	    	$data = $this->_db->get('tms_knowledge_category');	    	
	    	return $data;
	    }

	    public function KcategorygetOne($id) {
	    	$this->_db->where('category_id', $id);
	    	return $this->_db->getOne('tms_knowledge_category');
	    }

	    public function categoryUpdate($id, $data) {
	    	$data['created_date'] = date('Y-m-d H:i:s');
	    	$data['modified_date'] = date('Y-m-d H:i:s');
	    	$this->_db->where('category_id', $id);
	    	$id = $this->_db->update('tms_knowledge_category',$data);
	    	
	    	if($id) {
	    		$result['result'] = 200;
	    		$result['msg'] = "Inserted Succefully";
	    	} else {
	    		$result['result'] = 401;
	    		$result['msg'] = "Not Inserted";
	    	}
	    	return $result;
	    }

	    public function KcategoryDelete($id) {
	    	$this->_db->where('category', $id);
	    	$recordFound = $this->_db->getOne('tms_knowledge_articles');
	    	if($recordFound){
	    		$result['result'] = 401;
	    		$result['msg'] = "Not Deleted";
	    	}else{
	    		$this->_db->where('category_id', $id);
	    		return $this->_db->delete('tms_knowledge_category');
	    	}
	    }

	    public function categoryIconGet($icon) {
	    	$curl = curl_init(); 
			curl_setopt($curl,CURLOPT_URL,"https://api.iconfinder.com/v2/icons/search?query=$icon&count=30");
			curl_setopt($curl,CURLOPT_RETURNTRANSFER,true);
			curl_setopt($curl,CURLOPT_HEADER, false); 
			$result=curl_exec($curl);
			curl_close($curl);
			$iconArray = [];
			foreach (json_decode($result)->icons as $key => $value) {
			    	$value1 = end($value->raster_sizes);
			        $iconArray[] = $value1->formats[0]->preview_url;
			}
			return $iconArray;
	    }
}