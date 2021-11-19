<?php



class decimaleparator {



    public function __construct() {

        $this->_db = db::getInstance();

    }



    public function save($data) {

        //echo '<pre>'; print_r($data); echo '</pre>';exit;

        $this->_db->where('separatorChar',$data['separatorChar']);

        $this->_db->where('iUserId',$data['iUserId']);

        $match = $this->_db->getOne('tms_decimal_separator');

        

        if($match){

            $return['status'] = 422;

            $return['msg'] = 'Decimal separator already exists.'; 

        }else{

            $this->_db->where('iUserId',$data['iUserId']);

            $this->_db->where('is_active',1);

            $checkAnyAcvtive = $this->_db->getOne('tms_decimal_separator');

            if(!$checkAnyAcvtive){

                $data['created_date'] = date('Y-m-d H:i:s');

                $data['updated_date'] = date('Y-m-d H:i:s');

                $id = $this->_db->insert('tms_decimal_separator', $data);

                if ($id) {

                    $return['status'] = 200;

                    $return['msg'] = 'Insert Successfully.';

                } else {

                    $return['status'] = 422;

                    $return['msg'] = 'Not inserted.';

                }  

            }else if($checkAnyAcvtive && $data['is_active'] == 0){

                $data['created_date'] = date('Y-m-d H:i:s');

                $data['updated_date'] = date('Y-m-d H:i:s');

                $id = $this->_db->insert('tms_decimal_separator', $data);

                if ($id) {

                    $return['status'] = 200;

                    $return['msg'] = 'Insert Successfully.';

                } else {

                    $return['status'] = 422;

                    $return['msg'] = 'Not inserted.';

                }

            }else{

                $return['status'] = 422;

                $return['msg'] = 'Only one decimal separator should active at a time.'; 

            }



            

        }

        



        return $return;

    }



    public function getById($id) {

        $this->_db->where('separator_id', $id);

        $result = $this->_db->getOne('tms_decimal_separator');

        return $result;

    }

    public function getDecimalSeperatorByIuserId($id) {

        $this->_db->where('iUserId', $id);

        $this->_db->where('is_active',1);

        $result = $this->_db->getOne('tms_decimal_separator');

        return $result;

    }



    public function getAllDecimalSeperator() {

        $results = $this->_db->get('tms_decimal_separator');

        return $results;

    }



    public function updateDecimalSeparator($id, $data) {

        $this->_db->where('separatorChar',$data['separatorChar']);

        $this->_db->where('iUserId',$data['iUserId']);

        $match = $this->_db->getOne('tms_decimal_separator');



        if($match && $match['separator_id'] != $id){

            $return['status'] = 422;

            $return['msg'] = 'Decimal separator already exists.'; 

        }else{

            $this->_db->where('is_active',1);

            $checkAnyAcvtive = $this->_db->getOne('tms_decimal_separator');

            if(!$checkAnyAcvtive){

                $data['updated_date'] = date('Y-m-d H:i:s');

                $this->_db->where('separator_id', $id);

                $id = $this->_db->update('tms_decimal_separator', $data);

                if ($id) {

                    $return['status'] = 200;

                    $return['msg'] = 'Updated Successfully.';

                } else {

                    $return['status'] = 422;

                    $return['msg'] = 'Not inserted.';

                }

            }else if($checkAnyAcvtive && $checkAnyAcvtive['separator_id'] == $id){

                $data['updated_date'] = date('Y-m-d H:i:s');

                $this->_db->where('separator_id', $id);

                $id = $this->_db->update('tms_decimal_separator', $data);

                if ($id) {

                    $return['status'] = 200;

                    $return['msg'] = 'Updated Successfully.';

                } else {

                    $return['status'] = 422;

                    $return['msg'] = 'Not inserted.';

                }

            }else{

                

                $return['status'] = 422;

                $return['msg'] = 'Only one decimal separator should active at a time.'; 

            }



        }    

        return $return;

    }

    public function delete($id) {

        $this->_db->where('separator_id', $id);

        $id = $this->_db->delete('tms_decimal_separator');

        if ($id) {

            $return['status'] = 200;

            $return['msg'] = 'Deleted Successfully.';

        } else {

            $return['status'] = 422;

            $return['msg'] = 'Not inserted.';

        }

        return $return;

    }

}

