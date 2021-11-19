<?php

class authenticate {

    protected $_db;

    public function __construct() {
        $this->_db = db::getInstance();
    }

    /**
     * Checking user login
     * @param String $email User login email id
     * @param String $password User login password
     * @return boolean User login status success/fail
     */
    public function checkLogin($email, $password, $tbl_name) {
    // fetching user by email
        $getDBname = $this->getSiteFromEmail($email);
        if ($getDBname) {
            $this->_db = new db(DB_HOST, DB_USERNAME, DB_PASSWORD, str_replace('SITE', $getDBname['site'], DB_SITE));
            $this->_db->where('email', $email);
            $data = $this->_db->getOne($tbl_name, 'user_id as id,name,email,api_key,is_active,created_date,profile_pic,password');
    //        return $data;
            if ($this->_db->count > 0) {
                // Found user with the email
                if (PassHash::check_password($data['password'], $password)) {
                    // User password is correct
                    $data['site'] = $getDBname['site'];
                    unset($data['password']);
                    return $data;
                } else 
                {
                    // user password is incorrect
                    return FALSE;
                }
            } else {
                // user not existed with the email
                return FALSE;
            }
        } else {
            return FALSE;
        }
    }
    /**
     * Checking for duplicate user by email address
     * @param String $email email to check in db
     * @return boolean
     */
    private function getSiteFromEmail($email) {
//        $this->_db->select_db(DB_MAIN);
        $this->_db->where('email', $email);
        $site = $this->_db->getOne('acd_company_users', 'site');
//        $this->_db->select_db(DB_NAME);
        return $site;
    }

    /**
     * Checking for duplicate user by email address
     * @param String $email email to check in db
     * @return boolean
     */
    private function isUserExists($email) {
        $this->_db->where('email', $email);
        $user_id = $this->_db->getOne('acd_users', 'user_id');
        $num_rows = $this->_db->count;
        return $num_rows > 0;
    }

    /**
     * Checking for duplicate company by field value & field name pass
     * @param String $field_value value & $field_name field_name to check in db
     * @return boolean
     */
    public function checkField($field_value, $field_name) {
        if ($field_name == 'site') {
            $field_value.= '.arcadecrm.com';
        }
        $stmt = $this->_db->prepare("SELECT id from acd_subscription WHERE $field_name = ?");
        $stmt->bind_param("s", $field_value);
        $stmt->execute();
        $stmt->store_result();
        $num_rows = $stmt->num_rows;
        $stmt->close();
        return $num_rows;
    }

    /**
     * Fetching user by email
     * @param String $email User email user_id for super admin
     */
    public function getUserByEmail($email, $tbl_name) {
        $this->_db->where('email', $email);
        $data = $this->_db->getOne($tbl_name, 'id,name,email,api_key,is_active,created_date');
        if ($data) {
            return $data;
        } else {
            return NULL;
        }
    }
    /**
     * Fetching user by user_id
     * @param String $email User email user_id for super admin
     */
    public function getUser($user_id) {
        $this->_db->where('iUserId', $user_id);
        $data = $this->_db->getOne('tms_users');
        if ($data) {
            return $data;
        } else {
            return NULL;
        }
    }

    /**
     * Fetching user by email
     * @param String $email User email user_id for admin
     */
    public function getSubscriptionByEmail($email, $tbl_name) {
        $this->_db->where('email', $email);
        $data = $this->_db->getOne($tbl_name, 'user_id as id,name,email,api_key,is_active,created_date,profile_pic');
        if ($data) {
            return $data;
        } else {
            return NULL;
        }
    }

    /**
     * Update Device ID
     * If the device_id and user_id not exist then save in to database else update in database 
     * @param String $device_id user device_id
     * @param String $user_id user email
     * @return boolean
     */
    public function updateUserDevice($device_id, $user_id, $source_type) {
        if ($this->isDeviceExists($device_id)) {
            $this->_db->where('device_id', $device_id);
            $removeDevice = $this->_db->delete('acd_user_device');
            if ($removeDevice) {
                $addDevice = $this->_db->insert('acd_user_device', array('user_id' => $user_id, 'device_id' => $device_id, 'source_type' => $source_type, 'date' => date('Y-m-d H:i:s')));
            }
        } else {
            $addDevice = $this->_db->insert('acd_user_device', array('user_id' => $user_id, 'device_id' => $device_id, 'source_type' => $source_type, 'date' => date('Y-m-d H:i:s')));
        }
        return TRUE;
    }

    /**
     * Checking for duplicate user by email address
     * @param String $email email to check in db
     * @return boolean
     */
    private function isDeviceExists($device_id) {
        $this->_db->where('device_id', $device_id);
        $user_id = $this->_db->getOne('acd_user_device', 'user_id');
        $num_rows = $this->_db->count;
        return $num_rows > 0;
    }

    /**
     * Fetching user api key
     * @param String $user_id user user_id primary key in user table
     */
    public function getApiKeyById($user_id) {
        $this->_db->where('iUserId', $user_id);
        $api_key = $this->_db->getOne('tms_users', 'vPassword');
        if ($api_key) {
            return $api_key;
        } else {
            return NULL;
        }
    }

    /**
     * Fetching user user_id by api key
     * @param String $api_key user api key
     */
    public function getUserId($api_key) {
        $this->_db->where('vPassword', $api_key);
        $data = $this->_db->getOne('tms_users', 'iUserId');
        if ($data) {
            return $data['iUserId'];
        } else {
            return NULL;
        }
    }

    /**
     * Validating user api key
     * If the api key is there in db, it is a valid key
     * @param String $api_key user api key
     * @return boolean
     */
    public function isValidApiKey($api_key) {
        $this->_db->where('vPassword', $api_key);
        $this->_db->getOne('tms_users', 'iUserId');
        $num_rows = $this->_db->count;
        return $num_rows > 0;
    }

    /**
     * Reset password
     * If the email is exist then generate the random string of length and update in to database
     * @param String $email user email
     * @return boolean
     */
    public function forgetPassword($email) {
        if ($this->isUserExists(htmlspecialchars_decode($email))) {
            $password = $this->generateRandomString(10);
            $password_hash = PassHash::hash($password);
            $this->_db->where('email', "$email");
            if ($this->_db->update('tms_users', array('password' => $password_hash))) {
                return $password;
            } else {
                return FALSE;
            }
        } else {
            return FALSE;
        }
    }

    /**
     * Change password
     * If the $old_password mathed then update the $new_password given
     * @param number $user_id user user_id
     * @param number $old_password user old_password
     * @param number $new_password user new_password
     * @return boolean
     */
    public function changePassword($user_id, $old_password, $new_password) {
        //check old password match with given old password
        $this->_db->where('iUserId', $user_id);
        $data = $this->_db->getOne('tms_users', 'password');
//        return $data;
        // Found user with the email
        if (PassHash::check_password($data['password'], $old_password)) {
            $password_hash = PassHash::hash($new_password);
            $this->_db->where('iUserId', $user_id);
            if ($this->_db->update('tms_users', array('password' => $password_hash))) {
                return TRUE;
            } else {
                return FALSE;
            }
        } else {
            return FALSE;
        }
    }

    /**
     * Generating random Unique String for user password
     */
    public function generateRandomString($length) {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ$#@&';
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, strlen($characters) - 1)];
        }
        return $randomString;
    }

}
