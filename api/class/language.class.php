<?php
class language {

    public function __construct() {
        $this->_db = db::getInstance();
    }

    public function save($data) {
        $this->_db->where('lang_name',$data['lang_name']);
        $match = $this->_db->getOne('tms_language');
        if($match) {
                $return['status'] = 422;
                $return['msg'] = 'Language  already exists.';
        }else{
            $data['created_date'] = date('Y-m-d H:i:s');
            $data['modified_date'] = date('Y-m-d H:i:s');
            $id = $this->_db->insert('tms_language', $data);
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

    public function update($id, $data) {
        $this->_db->where('lang_name',$data['lang_name']);
        $match = $this->_db->getOne('tms_language');
        if($match && $match['lang_id'] != $id) {
                $return['status'] = 422;
                $return['msg'] = 'Language  already exists.';
        } else {
            $data['modified_date'] = date('Y-m-d H:i:s');
            $this->_db->where('lang_id', $id);
            $id = $this->_db->update('tms_language', $data);
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

        $results = $this->_db->get('tms_language');
        return $results;
    }

    public function delete($id) {
        $this->_db->where('lang_id', $id);
        $id = $this->_db->delete('tms_language');
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
        $this->_db->where('lang_id', $id);
        $result = $this->_db->getOne('tms_language');
        return $result;
    }

    public function getActive() {
        $this->_db->where('is_active', 1);
        $result = $this->_db->get('tms_language');
        return $result;
    }
    
    public function activelanguageGet(){
        $result = $this->_db->get('tms_language');
        return $result;
    }

    public function getMemoQLanguage(){
    	$ch = curl_init ("http://memoq.helpmax.net/en/reference-guide/supported-languages/");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $page = curl_exec($ch);

        $dom = new DOMDocument();
        libxml_use_internal_errors(true);
        $dom->loadHTML($page);
        libxml_clear_errors();
        $xpath = new DOMXpath($dom);

        $data = array();

        $table_rows = $xpath->query('//table[@id="TOGGLE0186A1"]/tr//table/tr');

        foreach($table_rows as $row) {
            foreach($row->childNodes as $td) {
                if(strlen(trim($td->nodeValue)) != 0){
                    array_push($data,trim($td->nodeValue));
                }
            }
        }

        $firstEleRemove     = array_shift($data);
        $secondEleRemove    = array_shift($data);
        $data = array_chunk($data,2);
        /*$dataOfArray = [];
        foreach ($data as $key => $value) {
            $dataOfArray[$key] = [];
            $dataOfArray[$key]["id"] = $data[$key][1];
            $dataOfArray[$key]["text"] = $data[$key][0];
        }*/
        //$data = $dataOfArray;
        return $data;
    }

    //------------------- Language Translation -----------------//
    public function languagesGetAll() {
        $results = $this->_db->get('tms_languages');
        return $results;
    }
    public function langsgetOne($id){
        $this->_db->where('lang_id',$id);
        $data = $this->_db->getone('tms_languages');
        return $data;
    }        
    public function langsupdate($id, $data) {
        $this->_db->where('lang_id',$data['lang_id']);
        $match = $this->_db->getOne('tms_languages');
        if($match['lang_id'] != $id) {
                $return['status'] = 422;
                //$return['msg'] = 'Language  already exists.';
                $return['msg'] = 'Not inserted';
        } else {
            $data['modified_date'] = date('Y-m-d H:i:s');
            $this->_db->where('lang_id', $id);
            $id = $this->_db->update('tms_languages', $data);
            if ($id) {
                $return['status'] = 200;
                $return['msg'] = 'Updated Successfully';
            } else {
                $return['status'] = 422;
                $return['msg'] = 'Not Updated.';
            }
        } 
        return $return;
    }

    public function deletelangs($id) {
        $this->_db->where('lang_id', $id);
        $id = $this->_db->delete('tms_languages');
        if ($id) {
            $return['status'] = 200;
            $return['msg'] = 'Deleted Successfully.';
        } else {
            $return['status'] = 422;
            $return['msg'] = 'Not Deleted.';
        }

        return $return;

    }
    public function languagesave($data) {
        $this->_db->where('name',$data['name']);
        $match = $this->_db->getOne('tms_languages');
        if($match) {
                $return['status'] = 422;
                $return['msg'] = 'Language  already exists.';
        }else{
            $data['created_date'] = date('Y-m-d H:i:s');
            $data['modified_date'] = date('Y-m-d H:i:s');
            $id = $this->_db->insert('tms_languages', $data);
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



}
