<?php

class mail_format {

    protected $_db;

    public function __construct() {
        $this->_db = db::getInstance();
    }

    public function get_mail_formats() {

        //$this->_db->where('company_id', $company_id);
        $data = $this->_db->get(TBL_MAIL_FORMAT);

        return $data;
    }

    public function get_mail_format($mail_id, $field_name = "") {

        $this->_db->where('mail_id', $mail_id);
        $data = $this->_db->get(TBL_MAIL_FORMAT);

        if ($field_name == "") {
            return $data;
        } else {
            $field_names = explode(',', $field_name);
            $return_data = "";
            foreach ($field_names as $field) {
                $return_data.= $data[0][$field] . ' ';
            }
            return trim($return_data);
        }
    }

    public function replace_placeholder($placeholders, $data) {
        $return = array();
        $replace_key = array_change_key_case($data, CASE_UPPER);
        foreach ($placeholders as $key => $val) {
            if (array_key_exists($val, $replace_key)) {
                $return[] = $replace_key[$val];
            }
        }
        return $return;
    }

    public function render_subject($mail_id, $data) {

        //$this->_db->where('company_id', $company_id);
        $mail_format = $this->get_mail_format($mail_id);
        $subject = $mail_format[0]['subject'];
        $placeholders = explode(',', $mail_format[0]['placeholder']);
        $replace_value = $this->replace_placeholder($placeholders, $data);
        $return = str_replace($placeholders, $replace_value, $subject);
        return $return;
    }

    public function render_message($mail_id, $data) {

        //$this->_db->where('company_id', $company_id);
        $mail_format = $this->get_mail_format($mail_id);
        $message = $mail_format[0]['message'];
        $placeholders = explode(',', $mail_format[0]['placeholder']);
        $replace_value = $this->replace_placeholder($placeholders, $data);
        $return = str_replace($placeholders, $replace_value, $message);
        return $return;
    }

}
