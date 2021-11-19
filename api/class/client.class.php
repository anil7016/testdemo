<?php

require_once 'functions.class.php';
require_once 'mail_format.class.php';
require_once 'users.class.php';
require_once 'filemanager.class.php';

class client {

    protected $_status;
    protected $_db;
    protected $_clientemail;
    protected $_clientid;

    public function __construct() {
        $this->_db = db::getInstance();
        $this->_status = new userstatus ();
    }

    protected function getClientEmail() {
        $this->_db->where("vEmailAddress", $this->_clientemail);
        return $results = $this->_db->getOne('tms_client');
    }

    public function getClientId($id) {
        $this->_db->where("iClientId", $id);
        $results = $this->_db->getOne('tms_client');
        $user = new users();
        $userData = $user->getUserUsingId($results['created_id']);
        $results['created_id'] = $userData['vUserName'];
        
        return $results;
    }

    public function client_indirect_update($id) {
        $this->_db->where("iClientId", $id);
        return $results = $this->_db->getOne('tms_client_indirect');
    }

    public function clientdirect_login_details($id) {
        $this->_db->where("iUserId", $id);
        return $results = $this->_db->get('tms_directclientlogin');
        // echo $this->_db->getLastQuery();exit;
    }

    public function getallclient($field, $id) {
        $this->_db->where($field, $id);
        $data = $this->_db->getOne('tms_client');
        return $data;
    }

    public function getClientByField($client_id, $field_name = "") {
        $this->_db->where('iClientId', $client_id);
        $data = $this->_db->get('tms_client');
        if ($data) {
            if ($field_name == "") {
                return $data;
            } else {
                $field_names = explode(',', $field_name);
                $return_data = "";
                foreach ($field_names as $field) {
                    $return_data .= $data [0] [$field] . ' ';
                }
                return trim($return_data);
            }
        }
    }

    public function clientlist() {
        //select tc.*,tu.vUserName As projectCoordinator from tms_client As tc INNER JOIN tms_users As tu ON tu.iUserId = tc.vProjectCoordinator
        //$this->_db->join('tms_client AS tc','tc.vProjectCoordinator = tu.iUserId','INNER');
        //$data = $this->_db->get('tms_client As tc','tc.*','tu.vUserName As projectCoordinator');
        //echo 'select tc.*,tu.vUserName As projectCoordinator,tu1.vUserName As projectManager from tms_client As tc INNER JOIN tms_users As tu ON tu.iUserId = tc.vProjectCoordinator INNER JOIN tms_users As tu1 ON tu1.iUserId = tc.vProjectManager';exit;
        
        $data = $this->_db->rawQuery("select * from tms_client");
        
        foreach ($data as $key => $value) {
            $data [$key] ['status'] = $this->_status->getStatusByField($value ['vStatus'], 'status_name');
        }
        return $data;
    }

    public function clientIndirectCheckList($info) {
        $retuen = array();
        $id = $info ['updated_id'];
        $updatedBy = $info ['updatedBy_id'];
        $this->_db->where('iClientId', $id);
        $info = $this->_db->getOne('tms_client_indirect');
        $eidtedId = $info ['iEditedBy'];
        if ($eidtedId == 0) {
            $this->_db->where('iClientId', $id);
            $id = $this->_db->update('tms_client_indirect', array(
                'iEditedBy' => $updatedBy
            ));
            $this->_db->where('iUserId', $updatedBy);
            $data = $this->_db->getOne('tms_users');
            $retuen ['updatedBy_name'] = "You";
            $retuen ['updatedBy_id'] = $updatedBy;
        } else if ($eidtedId != 0 && $eidtedId == $updatedBy) {
            $retuen ['updatedBy_name'] = "You";
            $retuen ['updatedBy_id'] = $updatedBy;
        } else {
            $this->_db->where('iUserId', $eidtedId);
            $data = $this->_db->getOne('tms_users');
            $retuen ['updatedBy_name'] = $data ['vFirstName'] . " " . $data ['vLastName'];
            $retuen ['updatedBy_id'] = $data ['iUserId'];
        }
        return $retuen;
    }

    public function clientBasicIdCheck($info) {
        $ret = array();
        $id = $info ['updated_id'];
        $UpdatedBy_id = $info ['updatedBy_id'];
        $this->_db->where('iClientId', $id);
        $data = $this->_db->getone('tms_client');
        $editedId = $data ['iEditedBy'];
        if ($editedId == 0) {
            $this->_db->where('iClientId', $id);
            $this->_db->update('tms_client', array(
                'iEditedBy' => $UpdatedBy_id
            ));
            $this->_db->where('iUserId', $UpdatedBy_id);
            $data = $this->_db->getone('tms_users');
            $ret ['UpdatedBy_name'] = "You";
            $ret ['UpdatedBy_id'] = $UpdatedBy_id;
        } else if ($editedId != 0 && $UpdatedBy_id == $editedId) {
            $ret ['UpdatedBy_name'] = "You";
            $ret ['UpdatedBy_id'] = $UpdatedBy_id;
        } else {
            $this->_db->where('iUserId', $editedId);
            $data = $this->_db->getone('tms_users');
            $ret ['UpdatedBy_name'] = $data ['vUserName'];
            $ret ['UpdatedBy_id'] = $data ['iUserId'];
        }
        return $ret;
    }

    public function basicClientUpdated($info) {
        $this->_db->where('iEditedBy', $info);
        $this->_db->update('tms_client', array(
            'iEditedBy' => 0
        ));
    }

    public function indirect_UpdateClient_id($id) {
        $data ['iEditedBy'] = 0;
        $this->_db->where('iEditedBy', $id);
        $id = $this->_db->update('tms_client_indirect', $data);
    }

    public function updateClientId($data) {
        $info ['iEditedBy'] = 0;
        $this->_db->where('iClientId', $data);
        $id = $this->_db->update('tms_client_indirect', $info);
    }

    public function clientIddata($id, $info) {
        $this->_db->where('iClientId', $id);
        $id = $this->_db->update('tms_client', array(
            'iEditedBy' => $info
        ));
        if ($id) {
            $return ['status'] = 200;
            $return ['msg'] = 'Updated Successfully.';
            $return ['iClientId'] = $id;
            $return ['clientData'] = $this->getClientId($id);
        } else {
            $return ['status'] = 422;
            $return ['msg'] = 'Not Updated.';
        }
        return $return;
    }

    public function clientlistindirect_show() {
        $data = $this->_db->get('tms_client_indirect');
        return $data;
    }

    public function checkemailaddress($data) {
        $this->_clientemail = $data;
        if ($this->getClientEmail()) {
            $return ['status'] = 422;
            $return ['msg'] = 'Email address already exists.';
        } else {
            $return ['status'] = 200;
            $return ['msg'] = 'Ok';
        }
        return $return;
    }

    public function saveClient($info) {
        if (isset($info['vEmailAddress'])) {
            $this->_clientemail = $info['vEmailAddress'];
        }
        if ($this->getClientEmail()) {
            $return ['status'] = 422;
            $return ['msg'] = 'Email address already exists.';
        } else {
            $info ['dtCreationDate'] = date('Y-m-d H:i:s');
            $info ['dtCreatedDate'] = date('Y-m-d H:i:s');
            $info ['dtUpdatedDate'] = date('Y-m-d H:i:s');
            if (isset($info ['image'])) {
                $info ['vLogo'] = $this->uploadimage($info);
                unset($info ['image']);
            }
            $id = $this->_db->insert('tms_client', $info);
            if ($id) {
                $data['created_date'] = date('Y-m-d H:i:s');
                $data['updated_date'] = date('Y-m-d H:i:s');
                //$data['name'] = 'cd' . str_pad($info['vClientNumber'], 4);
                $data['name'] = str_replace(' ','',strtolower($info['vUserName'])).'-'.str_pad($info['vClientNumber'],3,"0", STR_PAD_LEFT);
                $data['client_id'] = $id;
                $FileManagerId = $this->_db->insert('tms_filemanager', $data);

                //Default Folder Added When New Direct Client Is Added.
                if($FileManagerId){
                    $FolderNames = array('Data','Projects','Invoice');
                    foreach($FolderNames As $FolderName) {
                        $in['name'] = $FolderName;
                        $in['parent_id'] = $FileManagerId;
                        $in['is_default_folder'] = 1;
                        $in['created_date'] = date('Y-m-d H:i:s');
                        $in['updated_date'] = date('Y-m-d H:i:s');
                        $DefaultFolderInsert = $this->_db->insert('tms_filemanager',$in);
                    }
                    $return ['status'] = 200;
                    $return ['msg'] = 'Inserted Successfully.';
                    $return ['iClientId'] = $id;
                    $return ['clientData'] = $this->getClientId($id);
                }
            } else {
                $return ['status'] = 422;
                $return ['msg'] = 'Not inserted.';
            }
        }
        return $return;
    }

    public function clientsaveindirect($info) {
        $fileId = $info['fileId'];
        if ($this->getClientEmail()) {
            $return ['status'] = 422;
            $return ['msg'] = 'Email address already exists.';
        } else {
            $info ['dtCreationDate'] = date('Y-m-d H:i:s');
            $info ['dtUpdatedDate'] = date('Y-m-d H:i:s');
            unset($info ['image'], $info['fileId']);
            $id = $this->_db->insert('tms_client_indirect', $info);
            if ($id) {
                $data['parent_id']  =  $fileId;
                $data['in_client_id'] = $id;
                //$data['name'] = 'ci' . str_pad($info['vClientNumber'], 4);
                $data['name'] = str_replace(' ','',strtolower($info['vUserName'])).'-'.str_pad($info['vClientNumber'], 3);
                $data ['created_date'] = date('Y-m-d H:i:s');
                $data ['updated_date'] = date('Y-m-d H:i:s');
                $this->_db->insert('tms_filemanager', $data);
                $return ['status'] = 200;
                $return ['msg'] = 'Inserted Successfully.';
                $return ['iClientId'] = $id;
                $return ['clientData'] = $this->getClientId($id);
                //print_r($return ['clientData']);exit;
            } else {
                $return ['status'] = 422;
                $return ['msg'] = 'Not inserted.';
            }
        }
        return $return;
    }

    public function saveClientdirectlogin($info) {
        $this->_clientemail = $info ['vEmail'];

        if ($this->getClientEmail()) {
            $return ['status'] = 422;
            $return ['msg'] = 'Email address already exists.';
        } else {
            $info ['vPassword'] = $info ['vPassword'];
            $info ['v_pass'] = md5($info ['vPassword']);
            $info ['dtCreatedDate'] = date('Y-m-d H:i:s');
            $info ['dtUpdatedDate'] = date('Y-m-d H:i:s');
            
            $this->_db->where('vEmail',$info ['vEmail']);
            $match = $this->_db->getOne('tms_directclientlogin');
            if($match){
                $return ['status'] = 422;
                $return ['msg'] = 'User name already exists.';
            }else{
                $id = $this->_db->insert('tms_directclientlogin', $info);
                if ($id) {
                    $return ['status'] = 200;
                    $return ['msg'] = 'Inserted Successfully.';
                    $return ['iClientId'] = $id;
                    $return ['clientData'] = $this->getClientId($id);
                } else {
                    $return ['status'] = 422;
                    $return ['msg'] = 'Not inserted.';
                }    
            }
            
        }
        return $return;
    }
    
    public function updateClient($id, $info) {
        //echo '<pre>'; print_r($info); echo '</pre>';exit;
        /*$email = $info['vEmailAddress'];
        $cId = $info['iClientId'];
        $emailExsists = $this->_db->rawQuery("SELECT * FROM tms_client WHERE vEmailAddress = '$email'  AND iClientId != $cId");
        echo '<pre>'; print_r($emailExsists); echo '</pre>';*/
        $emailExsists = false;
        if($emailExsists){
                $return['status'] = 422;
                $return['msg'] = 'Email address already exists.';
        }else{
            if (isset($info ['image'])) {
                if (isset($id)) {
                    $this->_db->where('iClientId', $id);
                    $data = $this->_db->getOne('tms_client');
                    $image = $data['vLogo'];
                    if (isset($image)) {
                        $path = "../../uploads/logo/";
                        $images = glob($path . $image);
                        if ($images && $image) {
                            unlink($path . $image);
                        } else {
                            $image = $data['vLogo'];
                        }
                    }
                }
                $info ['vLogo'] = $this->uploadimage($info);
            }
            
            $info ['dtCreatedDate'] = date('Y-m-d H:i:s');
            $info ['dtUpdatedDate'] = date('Y-m-d H:i:s');
            unset($info ['image']);
            unset($info ['created_id']);
            $this->_db->where('iClientId', $id);
            $id = $this->_db->update('tms_client', $info);
            if ($id) {
                $return ['status'] = 200;
                $return ['msg'] = 'Updated Successfully.';
                $return ['iClientId'] = $id;
                $return ['clientData'] = $this->getClientId($id);
            } else {
                $return ['status'] = 422;
                $return ['msg'] = 'Not Updated.';
            }
        }
        
        return $return;
    }

    public function clientIndirectdata($id, $info) {
        $this->_db->where('iClientId', $id);
        $id = $this->_db->update('tms_client_indirect', array('iEditedBy' => $info));
        // echo $this->_db->getLastQuery();exit;
        if ($id) {
            $return ['status'] = 200;
            $return ['msg'] = 'Updated Successfully.';
            $return ['iClientId'] = $id;
            $return ['clientData'] = $this->getClientId($id);
        } else {
            $return ['status'] = 422;
            $return ['msg'] = 'Not Updated.';
        }
        return $return;
    }

    public function indirect_clientupdate($id, $info) {
        $info ['dtCreationDate'] = date('Y-m-d H:i:s');
        $info ['dtUpdatedDate'] = date('Y-m-d H:i:s');
        $this->_db->where('iClientId', $id);
        $id = $this->_db->update('tms_client_indirect', $info);
        // echo $this->_db->getLastQuery();exit;
        if ($id) {
            $return ['status'] = 200;
            $return ['msg'] = 'Updated Successfully.';
            $return ['iClientId'] = $id;
            $return ['clientData'] = $this->getClientId($id);
        } else {
            $return ['status'] = 422;
            $return ['msg'] = 'Not Updated.';
        }
        return $return;
    }

    public function update_directclientlogin($id, $info) {
        if (!preg_match('/^[a-f0-9]{32}$/', $info ['vPassword'])) {
            $info ['vPassword'] = $info ['vPassword'];
            $info ['v_pass'] = md5($info ['vPassword']);
        }
        
        $info ['dtCreatedDate'] = date('Y-m-d H:i:s');
        $info ['dtUpdatedDate'] = date('Y-m-d H:i:s');

        $this->_db->where('vEmail', $info['vEmail']);
        $match = $this->_db->getOne('tms_directclientlogin');
        //print_r($match);
        if($match && $match['iClientId'] != $id){
            $return ['status'] = 422;
            $return ['msg'] = 'User name already exists.';
        }else{
            $this->_db->where('iClientId', $id);
            $id = $this->_db->update('tms_directclientlogin', $info);
            if ($id) {
                $return ['status'] = 200;
                $return ['msg'] = 'Updated Successfully.';
                $return ['iClientId'] = $id;
                $return ['clientData'] = $this->getClientId($id);
            } else {
                $return ['status'] = 422;
                $return ['msg'] = 'Not Updated.';
            }
        }    
        return $return;
    }

    function isValidMd5($md5 = '') {
        return preg_match('/^[a-f0-9]{32}$/', $md5);
    }
    
    public function uploadimage($data) {
        $result = explode(',', $data ['image']);
        
        $getFileType = explode(';',explode(':',$result[0])[1]);
        
        $finalstring = base64_decode($result[1]);

        $mimetype = self::getImageMimeType($finalstring,$getFileType[0]);
        if($mimetype == 'jpeg'){
            $mimetype = 'jpg';
        }
        $filename = time() . "." . $mimetype;
        $output_file = UPLOADS_ROOT . "logo/" . $filename;
        $ifp = fopen($output_file, "wb");
        fwrite($ifp, $finalstring);
        fclose($ifp);

        return $filename;
    }
    
    public static function getBytesFromHexString($hexdata) {
        for ($count = 0; $count < strlen($hexdata); $count += 2)
            $bytes [] = chr(hexdec(substr($hexdata, $count, 2)));
        return implode($bytes);
    }
    
    public static function changeDateFormate($data) {
        $d = explode(" ", $data);
        $date = explode("/", $d [0]);
        
        if (isset($d [1]))
            return $final_date = $date [2] . "-" . $date [0] . "-" . $date [1] . " " . $d [1];
        else
            return $final_date = $date [2] . "-" . $date [0] . "-" . $date [1];
    }

    public static function getImageMimeType($imagedata,$FileType) {
        // $imagemimetypes = array(
        //     "jpeg" => "FFD8",
        //     "png" => "89504E470D0A1A0A",
        //     "gif" => "474946",
        //     "bmp" => "424D",
        //     "tiff" => "4949",
        //     "tiff" => "4D4D",
        //     "zip" => "630000000000",
        //     "doc" => "CFAD12FE",
        //     "docx" => "504B030414000600",
        //     "doc" => "D0CF11E0A1B11AE100",
        //     "pdf" => "255044462D312E",
        //     "text" => "EFBBBF",
        //     "xlsx" => "504B030414000600",
        //     "rar" => "526172211A0700"
        // );
        $imagemimetypes =
array(  
    array('jpeg',   'FFD8',                 'image/jpeg',                                                                   ),
    array('png',    '89504E470D0A1A0A',     'image/png',                                                                    ),
    array('gif',    '474946',               'image/gif',                                                                 ),
    array('bmp',    '424D',                 'image/bmp',                                                                             ),
    array('tiff',   '4949',                 'image/tiff',                                                                             ),
    array('zip',    '504B0304',             'application/zip',                                                                             ),
    array('pdf',    '255044462D312E',       'application/pdf',                                                                             ),
    array('text',   'EFBBBF',               'text/plain',                                                                   ),
    array('rar',    '526172211A0700',       '',                                                                             ),
    array('xlsx',   '504B030414000600',     'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',            ),
    array('docx',   '504B0506',     'application/vnd.openxmlformats-officedocument.wordprocessingml.document',      ),
    array('pptx',   '504B030414000600',     'application/vnd.openxmlformats-officedocument.presentationml.presentation',    ),
);
        foreach ($imagemimetypes as $row) {
            $mime=$row[0];
            $hexbytes=$row[1];
            $bytes = self::getBytesFromHexString($hexbytes);
            if (substr($imagedata, 0, strlen($bytes)) == $bytes){
                if(empty($FileType)){
                    return $mime;
                }
                elseif($FileType == $row[2]){
                    return $mime;
                }
            }
        }

        return NULL;
    }

    public function deleteClientindirect($id) {
        
        $this->_db->where('indirect_customer', $id);
        $indirectClientAssignToCustomer = $this->_db->getOne('tms_customer');
        if($indirectClientAssignToCustomer){
            $return ['status'] = 422;
            $return ['msg'] = 'You can not delete this account.';
        }else{
            $this->_db->where('in_client_id', $id);
            $data = $this->_db->getOne('tms_filemanager');
            

            if($data){
                $this->_db->where('parent_id',$data['fmanager_id']);
                $aa = $this->_db->get('tms_filemanager');
            }
            
            $this->_db->where('in_client_id', $id);
            $this->_db->delete('tms_filemanager');

            $this->_db->where('iClientId', $id);
            $id = $this->_db->delete('tms_client_indirect');

            if ($id) {
                $return ['status'] = 200;
                $return ['msg'] = 'Deleted Successfully.';
            } else {
                $return ['status'] = 422;
                $return ['msg'] = 'Not Deleted.';
            }
        }
        

        return $return;
    }

    public function deleteclientfield($field, $id, $table) {
        $this->_db->where($field, $id);
        $data = $this->_db->delete($table);
        return $data;
    }

    public function deleteClient($id, $image) {
        $this->_db->where('client', $id);
        $cuData = $this->_db->getOne('tms_customer');
        

        $this->_db->where('order_id', $cuData['order_id']);
        $orData = $this->_db->getOne('tms_order');
        

        $this->_db->where('order_id', $orData['order_id']);
        $geData = $this->_db->getOne('tms_general');
        
        
        $cuData = false;
        $orData = false;
        $geData = false;

        if($cuData || $orData || $geData){
            return false;
        }else{
            $path = "../../uploads/logo/";
            $images = glob($path . $image);
            if ($images) {
                $image;
                unlink($path . $image);
            } else {
                $image;
            }

            self::deleteclientfield('client_id', $id, 'tms_filemanager');

            $id = self::deleteclientfield('iClientId', $id, 'tms_client');

            self::deleteclientfield('client', $id, 'tms_customer');

            self::deleteclientfield('iUserId', $id, 'tms_directclientlogin');

            self::deleteclientfield('iClientId', $id, 'tms_payment');

            return $id;
        }

    }

    public function deleteInvoiceId($id) {
        $this->_db->where('iClientId', $id);
        $id = $this->_db->delete('tms_client');
    }

    public function clientMessageGet($id) {
        $this->_db->where('iClientId', $id);
        $data = $this->_db->getOne('tms_client');
        return $data;
    }

    public function clientIndirectMessageGet($id) {
        $this->_db->where('iClientId', $id);
        $data = $this->_db->getOne('tms_client_indirect');
        if ($data) {
            $this->_db->where('is_active', 1);
            $info = $this->_db->getone('tms_email_sign');
        }
        $result['data'] = $data;
        $result['info'] = $info;
        return $result;
    }

    public function clientdirect_login_getone($id) {
        $this->_db->where('iClientId', $id);
        $data = $this->_db->getOne('tms_directclientlogin');
        return $data;
    }

    public function deleteClientdata($id) {
        $this->_db->where('iClientId', $id);
        $id = $this->_db->delete('tms_directclientlogin');
        if ($id) {
            $return ['status'] = 200;
            $return ['msg'] = 'Deleted Successfully.';
        } else {
            $return ['status'] = 422;
            $return ['msg'] = 'Not Deleted.';
        }
        return $return;
    }

    public function viewdirectdataget($id) {
        $user = new users();
        $id = self::getallclient('iClientId', $id);
        if (isset($id['vProjectCoordinator'])) {
            $coordinator = $user->getUserAllfile('iUserId', $id['vProjectCoordinator']);
            $id['vProjectCoordinator'] = $coordinator['vUserName'];
        }
        if (isset($id['vProjectManager'])) {
            $manager = $user->getUserAllfile('iUserId', $id['vProjectManager']);
            $id['vProjectManager'] = $manager['vUserName'];
        }
        if (isset($id['vQASpecialist'])) {
            $QAspecialist = $user->getUserAllfile('iUserId', $id['vQASpecialist']);
            $id['vQASpecialist'] = $QAspecialist['vUserName'];
        }
        if (isset($id['iBussinessDeveloper'])) {
            $this->_db->where('iUserId', $id['iBussinessDeveloper']);
            $data = $this->_db->getone('tms_users');
            $id['iBussinessDeveloper'] = $data['vUserName'];
        }
        if (isset($id['vStatus'])) {
            $this->_db->where('status_type', 2);
            $this->_db->where('status_id', $id['vStatus']);
            $data = $this->_db->getone('tms_user_status');
            $id['vStatus'] = $data['status_name'];
        }
        if (isset($id['created_id'])) {
            $created = $user->getUserAllfile('iUserId', $id['created_id']);
            $id['createdBy'] = $created['vUserName'];
        }
        if (isset($id['modified_id'])) {
            $modified = $user->getUserAllfile('iUserId', $id['modified_id']);
            $id['modifiedBy'] = $modified['vUserName'];
        }
        return $id;
    }

    public function clientDirectMessageEmailIdGet() {
        $this->_db->where('is_active', 1);
        $data = $this->_db->getone('tms_email_sign');
        return $data;
    }

    public function clientProfileNumberGet($id) {
        if ($id == 1) {
            $this->_db->orderBy('vClientNumber', 'desc');
            $data = $this->_db->get('tms_client', 1);
            if ($data) {
                $return = $data[0]['vClientNumber'] + 1;
            } else {
                $return = 1;
            }
            return $return;
        } else {
            $this->_db->orderBy('vClientNumber', 'desc');
            $data = $this->_db->get('tms_client_indirect', 1);
            if ($data) {
                $return = $data[0]['vClientNumber'] + 1;
            } else {
                $return = 1;
            }
            return $return;
        }
    }

    public function IndirectCustomerFileCheck() {
        $file  = new filemanager();
        $data  = $file->fileCustomeget('Indirect client', 'name');
        if(!$data) {
            $info['created_date'] = date('Y-m-d');
            $info['updated_date'] = date('Y-m-d');
            $info['name'] = "Indirect client";
            return $this->_db->insert('tms_filemanager', $info);
        } else {
            return $data['fmanager_id'];
        }

    }

    public function getClientCurrency($id) {
        $this->_db->where('iContactId', $id);
        $data = $this->_db->getone('tms_client_contact');
        if($data){
            $this->_db->where('iClientId', $data['iClientId']);
            $clientData = $this->_db->getone('tms_client');
            return $clientData;
        }
    }

    public function getIndirectClient($id) {
        $this->_db->where('iClientId', $id);
        $data = $this->_db->getone('tms_client_indirect');
        return $data;
    }

}
