<?php

require_once 'value.class.php';

class property {

    protected $_value;

    public function __construct() {
        $this->_db = db::getInstance();
        $this->_value = new value();
    }

    public function save($data) {
        $this->_db->where('property_name',$data['property_name']);
        $match = $this->_db->getOne('tms_property');
        if($match){
            $return['status'] = 422;
            $return['msg'] = 'Property name already exists.'; 
        }else{
            $data['created_date'] = date('Y-m-d H:i:s');
            $data['updated_date'] = date('Y-m-d H:i:s');
            $id = $this->_db->insert('tms_property', $data);
            if ($id) {
                $return['status'] = 200;
                $return['id'] = $id;
            } else {
                $return['status'] = 422;
                $return['msg'] = 'Not inserted.';
            } 
        }
        

        return $return;
    }

    public function addUserProperty($data) {
        $this->_db->where('user_id', $data['user_id']);
        $this->_db->where('type', $data['type']);
        $this->_db->where('property_id', $data['property_id']);
        $prop = $this->_db->get('tms_user_property');
        $us = [];
        if (!empty($prop)) {
            foreach ($prop as $key => $value) {
                $val = explode(",", $value['value_id']);
                foreach ($val as $k => $v) {
                    array_push($us, $v);
                }
            }
            array_push($us, $data['value_id']);
            $final = implode(",", $us);
            $this->_db->where('id', $prop[0]['id']);
            $id = $this->_db->update('tms_user_property', array('value_id' => $final));
            if ($id) {
                $return['status'] = 200;
                $return['msg'] = 'Inserted Successfully';
            } else {
                $return['status'] = 422;
                $return['msg'] = 'Not inserted.';
            }
        } else {
            $data['created_date'] = date('Y-m-d H:i:s');
            $data['updated_date'] = date('Y-m-d H:i:s');
            $id = $this->_db->insert('tms_user_property', $data);
            if ($id) {
                $return['status'] = 200;
                $return['msg'] = 'Inserted Successfully';
            } else {
                $return['status'] = 422;
                $return['msg'] = 'Not inserted.';
            }
        }

        return $return;
    }

    public function updateUserProperty($index_id, $data) {
        $data['updated_date'] = date('Y-m-d H:i:s');
        $this->_db->where('id', $index_id);
        $id = $this->_db->update('tms_user_property', $data);
        if ($id) {
            $return['status'] = 200;
            $return['msg'] = 'Updated Successfully';
        } else {
            $return['status'] = 422;
            $return['msg'] = 'Not inserted.';
        }

        return $return;
    }

    public function update($id, $data) {
        $this->_db->where('property_name',$data['property_name']);
        $match = $this->_db->getOne('tms_property');
        if($match && $match['property_id'] != $id){
            $return['status'] = 422;
            $return['msg'] = 'Property name already exists.'; 
        }else{
            $data['updated_date'] = date('Y-m-d H:i:s');
            $this->_db->where('property_id', $id);
            $id = $this->_db->update('tms_property', $data);
            if ($id) {
                $return['status'] = 200;
                $return['msg'] = 'Insert Successfully.';
            } else {
                $return['status'] = 422;
                $return['msg'] = 'Not inserted.';
            }
        }
        
        return $return;
    }

    public function getAll() {

        $results = $this->_db->get('tms_property');
        return $results;
    }

    public function delete($id) {
        $this->_db->where('property_id',$id);
        $match = $this->_db->getOne('tms_user_property');
        
        if($match){
            $return['status'] = 422;
            $return['msg'] = 'You can not delete this property.'; 
        }else{
            $this->_db->where('property_id', $id);
            $id = $this->_db->delete('tms_property');
            if ($id) {
                $return['status'] = 200;
                $return['msg'] = 'Deleted Successfully.';
            } else {
                $return['status'] = 422;
                $return['msg'] = 'Not inserted.';
            }
        }
        return $return;
    }

    public function getTypeById($id) {
        $this->_db->where('property_id', $id);
        $result = $this->_db->getOne('tms_property');
        return $result;
    }

    public function getByPriority($priority) {
        $this->_db->where($priority, 1);
        $result = $this->_db->get('tms_property');
        for ($r = 0; $r < sizeof($result); $r++) {
            $result[$r]['value'] = $this->_value->getValueByProperty($result[$r]['property_id']);
        }
        return $result;
    }

    public function getActive($priority) {
        $this->_db->where($priority, 1);
        $this->_db->where('is_active', 1);
        $result = $this->_db->get('tms_property');
        return $result;
    }

    public function getUserProperty($id, $type) {
        $values = new value();
        $this->_db->where('user_id', $id);
        $this->_db->where('type', $type);
        $result = $this->_db->get('tms_user_property');
        foreach ($result as $key => $value) {
            $property_name = self::getTypeById($value['property_id']);
            $result[$key]['property_name'] = $property_name['property_name'];
            $result[$key]['values_names'] = $values->getBunch($value['value_id']);
        }
        return $result;
    }

    public function userPropertyById($id) {
        $this->_db->where('id', $id);
        $result = $this->_db->get('tms_user_property');
        return $result;
    }

    public function deleteUserProperty($in_id) {
        $this->_db->where('id', $in_id);
        $id = $this->_db->delete('tms_user_property');
        if ($id) {
            $return['status'] = 200;
            $return['msg'] = 'Deleted Successfully.';
        } else {
            $return['status'] = 422;
            $return['msg'] = 'Not inserted.';
        }

        return $return;
    }

    public function searchResult($poperty_id, $user_id, $type) {
        $values = new value();
        $this->_db->where('user_id', $user_id);
        $this->_db->where('property_id', $poperty_id);
        $this->_db->where('type', $type);
        $result = $this->_db->get('tms_user_property');
        $value_data = $values->getValueByProperty($poperty_id);
        if (!empty($result)) {
            foreach ($result as $key => $value) {
                $user_value[$key] = $value['value_id'];
            }
            $new_result = implode(",", $user_value);
            $array = explode(",", $new_result);
            foreach ($value_data as $key => $value) {
                $allValues[$key] = $value['value_id'];
            }
            $return = array_diff($allValues, $array);
            if (!empty($return)) {
                foreach ($return as $key => $value) {
                    $final_result[$key] = $values->getTypeById($value);
                }
                return $final_result;
            } else {

                return $return;
            }
        } else {
            return $value_data;
        }
    }

    public function generalPropertiesView($id){
        $this->_db->where('value_id',$id);
        $data = $this->_db->getOne('tms_property_values');
        return $data['value_name'];
    }
}
