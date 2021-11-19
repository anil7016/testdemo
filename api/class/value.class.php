<?php

class value {

    protected $_value;

    public function __construct() {
        $this->_db = db::getInstance();
    }

    public function save($data) {
        $data['created_date'] = date('Y-m-d H:i:s');
        $data['updated_date'] = date('Y-m-d H:i:s');
        $id = $this->_db->insert('tms_property_values', $data);
        if ($id) {
            $return['status'] = 200;
            $return['id'] = $id;
        } else {
            $return['status'] = 422;
            $return['msg'] = 'Not inserted.';
        }

        return $return;
    }

    public function update($id, $data) {

        $data['updated_date'] = date('Y-m-d H:i:s');
        $this->_db->where('value_id', $id);
        $id = $this->_db->update('tms_property_values', $data);
        if ($id) {
            $return['status'] = 200;
            $return['msg'] = 'Insert Successfully.';
        } else {
            $return['status'] = 422;
            $return['msg'] = 'Not inserted.';
        }
        return $return;
    }

    public function getAll() {

        $results = $this->_db->get('tms_property_values');
        return $results;
    }

    public function delete($id) {
        $this->_db->where('value_id', $id);
        $id = $this->_db->delete('tms_property_values');
        if ($id) {
            $return['status'] = 200;
            $return['msg'] = 'Deleted Successfully.';
        } else {
            $return['status'] = 422;
            $return['msg'] = 'Not inserted.';
        }

        return $return;
    }

    public function getTypeById($id) {
        $this->_db->where('value_id', $id);
        $result = $this->_db->getOne('tms_property_values');
        return $result;
    }

    public function getValueByProperty($id) {
        $this->_db->where('property_id', $id);
        $result = $this->_db->get('tms_property_values');
        return $result;
    }

    public function getBunch($ids) {
        $array = explode(",", $ids);
        foreach ($array as $key => $value) {
            $values = self::getTypeById($value);
            $return[$key] = $values['value_name'];
        }
        $result = implode(", ", $return);
        return $result;
    }

}
