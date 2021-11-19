<?php

require_once 'users.class.php';
require_once 'client.class.php';
require_once 'tasktype.class.php';

class Knowledgecalendar {
	//price1 data manage
    public function __construct() {
        $this->_db = db::getInstance();   
    }
    
    public function knowledgeEventSave($data) {
        $data['event_startdate'] = date('Y/m/d H:i:s', strtotime($data['event_startdate']));
        $data['event_enddate'] = date('Y/m/d 23:59:59', strtotime($data['event_enddate']));
        $data['created_date'] = date('Y/m/d H:i:s');
        $data['modified_date'] = date('Y/m/d H:i:s');
        $id = $this->_db->insert('tms_knowledge_events', $data);
        if($id) {
            $result['status'] = 200;
            $result['msg'] = "Inserted Sucessfully";
        } else {
            $result['status'] = 401;
            $result['msg'] = "Not Inserted";
        }
        return $result;
    }
    
    public function knowledgeEventGetAll() {
        $this->_db->orderBy('event_id','DESC');
        $data = $this->_db->get('tms_knowledge_events');        
        return $data;
    }
    
    public function knowledgeEventGetOne($id) {
        $this->_db->where('event_id', $id);
        return $data = $this->_db->getOne('tms_knowledge_events');
    }
    
    public function knowledgeEventUpdate($id, $data) {
        $data['event_startdate'] = date('Y/m/d H:i:s', strtotime($data['event_startdate']));
        $data['event_enddate'] = date('Y/m/d 23:59:59', strtotime($data['event_enddate']));
        $data['modified_date'] = date('Y/m/d H:i:s');
        $this->_db->where('event_id', $id);
        $id = $this->_db->update('tms_knowledge_events', $data);
        if($id) {
            $result['status'] = 200;
            $result['msg'] = "Updated Sucessfully";
        } else {
            $result['status'] = 401;
            $result['msg'] = "Not Updated";
        }
        return $result;
    }
    
    public function knowledgeEventDelete($id) {
        $this->_db->where('event_id', $id);
        return $this->_db->delete('tms_knowledge_events');
    }
}