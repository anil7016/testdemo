<?php

require_once 'users.class.php';
require_once 'client.class.php';

class event {

    public function __construct() {
        $this->_db = db::getInstance();
    }

    public function save($eventData) {
        $eventData['created_date'] = date('Y-m-d H:i:s');
        $eventData['updated_date'] = date('Y-m-d H:i:s');
        $id = $this->_db->insert('tms_events', $eventData);
        if ($id) {
            $return['status'] = 200;
            $return['msg'] = 'Successfully Inserted.';
        } else {
            $return['status'] = 422;
            $return['msg'] = 'Not Saved.';
        }
        return $return;
    }

    public function getAllEvent($id) {
        $this->_db->where('user_id', $id);
        $results = $this->_db->get('tms_events');
        return $results;
    }

    public function updateEvent($event_id, $info) {
        $info['updated_date'] = date('Y-m-d H:i:s');
        $this->_db->where('event_id', $event_id);
        $id = $this->_db->update('tms_events', $info);
        if ($id) {
            $return['status'] = 200;
            $return['msg'] = 'Successfully Updated.';
        } else {
            $return['status'] = 422;
            $return['msg'] = 'Not Saved.';
        }
        return $return;
    }

    public function deleteEvent($id) {
        $this->_db->where('event_id', $id);
        $id = $this->_db->delete('tms_events');
        if ($id) {
            $return['status'] = 200;
            $return['msg'] = 'Successfully Updated.';
        } else {
            $return['status'] = 422;
            $return['msg'] = 'Not Saved.';
        }
        return $return;
    }

    public function jobeventssave($eventData) {
        $eventData['created_date'] = date('Y-m-d H:i:s');
        $eventData['updated_date'] = date('Y-m-d H:i:s');
        $id = $this->_db->insert('tms_job_events', $eventData);
        if ($id) {
            $return['status'] = 200;
            $return['msg'] = 'Successfully Inserted.';
        } else {
            $return['status'] = 422;
            $return['msg'] = 'Not Saved.';
        }
        return $return;
    }

    public function updatejobevents($event_id, $info) {
        $info['updated_date'] = date('Y-m-d H:i:s');
        $this->_db->where('event_id', $event_id);
        $id = $this->_db->update('tms_job_events', $info);
        if ($id) {
            $return['status'] = 200;
            $return['msg'] = 'Successfully Updated.';
        } else {
            $return['status'] = 422;
            $return['msg'] = 'Not Saved.';
        }
        return $return;
    }
    
    public function getAlljobEvent($id) {
        $this->_db->where('order_id', $id);
        $results = $this->_db->get('tms_job_events');
        return $results;
    }
    
    public function deletejobEvent($id) {
        $this->_db->where('event_id', $id);
        $id = $this->_db->delete('tms_job_events');
        if ($id) {
            $return['status'] = 200;
            $return['msg'] = 'Successfully Deleted.';
        } else {
            $return['status'] = 422;
            $return['msg'] = 'Not Deleted.';
        }
        return $return;
    }

}
