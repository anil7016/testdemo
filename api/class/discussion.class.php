<?php

require_once 'customer.class.php';
require_once 'client.class.php';

class discussion {
    public function __construct() {
        $this->_db = db::getInstance();
    }
    
    public function discussionOrderGet($id) {
        $this->_db->where('order_id',$id);
        $data = $this->_db->get('tms_discussion');
        return $data;
    }
    
    public function discussionOrder($data) {
        if($data['created_by_current_user'] == 1) 
            $data['created_by_current_user'] = "true";
        else
            $data['created_by_current_user'] = "false";
        if($data['user_has_upvoted'] == 1) 
            $data['user_has_upvoted'] = "true";
        else
            $data['user_has_upvoted'] = "false";
        
        $id = $this->_db->insert('tms_discussion',$data);
        if($id) {
            $result['Status'] = 200;
            $result['msg'] = "Inserted Successfully";
        } else {
            $result['Status'] = 401;
            $result['msg'] = "No Inserted";
        }
        return $result;
    }

    public function discussionOrderDelete($id,$data) {
        $this->_db->where('id',$id);
        $cols = Array ("user_id");
        $getMatch = $this->_db->getOne("tms_discussion", null, $cols);
        if($data == $getMatch['user_id']){
             $this->_db->where('id',$id);
            $del = $this->_db->delete('tms_discussion');
            if($del) {
                $result['Status'] = 200;
                $result['msg'] = "Deleted Successfully";
            } else {
                $result['Status'] = 401;
                $result['msg'] = "No Updated";
            }   
        }else{
            $result['Status'] = 401;
            $result['msg'] = "You can not delete other user post";
        }
        return $result;
    }

    public function discussionOrderUpdate($data,$id) {
        $this->_db->where('id',$id);
        $cols = Array ("user_id");
        $getMatch = $this->_db->getOne("tms_discussion", null, $cols);
        //echo $getMatch['user_id'];exit;
        if($data['login_userid'] == $getMatch['user_id']){
            unset($data['login_userid']);
            if($data['created_by_current_user'] == 1) 
                $data['created_by_current_user'] = "true";
            else
                $data['created_by_current_user'] = "false";
            if($data['user_has_upvoted'] == 1) 
                $data['user_has_upvoted'] = "true";
            else
                $data['user_has_upvoted'] = "false";
            
            $this->_db->where('id',$id);
            $id = $this->_db->update('tms_discussion',$data);
            if($id) {
                $result['Status'] = 200;
                $result['msg'] = "Updated Successfully";
            } else {
                $result['Status'] = 401;
                $result['msg'] = "No Updated";
            }
            
        }else{
            $result['Status'] = 401;
            $result['msg'] = "You can not edit other user post";
        }

        
        return $result;
    }


    public function discussionCommentread($data,$orderId) {
        //$this->_db->where('order_id',$orderId);
        //$cmtData = $this->_db->get("tms_discussion");
        /*$menu_access = $this->_db->rawQuery("SELECT * FROM " . tms_discussion . " WHERE user_id = '" . $orderId . "' AND FIND_IN_SET('" . 1 . "', read_id)");*/
        $cmtval['status']=0;
        foreach ($data as $value) {
            $reead_id = "'".$value['read_id'].",'";    
            $qry="UPDATE tms_discussion set read_id=concat(read_id, ".$reead_id.") WHERE id = " . $value['id'] ;

            $this->_db->rawQuery($qry);
            $cmtval['status']=1;
        }
        return $cmtval;
    }
    public function discussionEmojitext() {
        $data = $this->_db->get('tms_emojitext');
        return $data;
    }

}
