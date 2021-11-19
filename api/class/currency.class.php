<?php

class currency {

    public function __construct() {
        $this->_db = db::getInstance();
    }

    public function currencyConverts($amount,$from_Currency,$to_Currency) {
        if($from_Currency!= $to_Currency) {
            $amount = urlencode($amount);
            $from_Currency = urlencode($from_Currency);
            $to_Currency = urlencode($to_Currency);
            $get = file_get_contents("https://finance.google.com/finance/converter?a=$amount&from=$from_Currency&to=$to_Currency");
            $get = explode("<span class=bld>",$get);
            $get = explode("</span>",$get[1]);  
            $converted_amount = preg_replace("/[^0-9.]/", null, $get[0]);
            $data = round($converted_amount,2);
            return $data;
        } else {
            return 1;
        }
    }

    public function save($data) {
        $this->_db->where('country_name',$data['country_name']);
        $exists = $this->_db->getOne('tms_currency');
        if($exists){
            $return['status'] = 422;
            $return['msg'] = 'Currency already exists.';
        }else{

            /*$default = self::currencyConverts(1,$data['curDef'],$data['country_name']);
            $current = self::currencyConverts($data['rate'],$data['country_name'],$data['curDef']);
            $data['c_current'] = $current;
            $data['c_default'] = $default;*/

            $xref  = simplexml_load_file('http://www.ecb.europa.eu/stats/eurofxref/eurofxref-daily.xml');
            $nodes = $xref->xpath('//*[@currency="'.$data['country_name'].'"]');
            
            $data['current_curency_rate'] = ((array) $nodes[0]['rate']);
            $data['current_curency_rate'] = $data['current_curency_rate'][0];
            
            $data['created_date'] = date('Y-m-d H:i:s');
            $data['updated_date'] = date('Y-m-d H:i:s');
            unset($data['curDef']);
            $id = $this->_db->insert('tms_currency', $data);
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
        $this->_db->where('country_name',$data['country_name']);
        $exists = $this->_db->getOne('tms_currency');
        if($exists && $exists['currency_id'] != $id){
            $return['status'] = 422;
            $return['msg'] = 'Currency already exists.';
        }else{
           /* if($data['curDef']!='default') {
                $default = self::currencyConverts(1,$data['curDef'],$data['country_name']);
                $current = self::currencyConverts($data['rate'],$data['country_name'],$data['curDef']);
                $data['c_current'] = $current;
                $data['c_default'] = $default;
            }*/
            $xref  = simplexml_load_file('http://www.ecb.europa.eu/stats/eurofxref/eurofxref-daily.xml');
            $nodes = $xref->xpath('//*[@currency="'.$data['country_name'].'"]');
            
            $data['current_curency_rate'] = ((array) $nodes[0]['rate']);
            $data['current_curency_rate'] = $data['current_curency_rate'][0];

            $data['updated_date'] = date('Y-m-d H:i:s');
            unset($data['curDef']);
            $this->_db->where('currency_id', $id);
            $id = $this->_db->update('tms_currency', $data);
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

    public function currencyUpdate($id, $data) {
        if(isset($data['curDef'])) {
            $info = self::getAll();
            foreach($info AS $in) {
                if($in['default_front']!=1) {
                    $default = self::currencyConverts(1,$data['curDef'],$in['country_name']);
                    $current = self::currencyConverts($in['rate'],$in['country_name'],$data['curDef']);
                    $inc['c_current'] = $current;
                    $inc['c_default'] = $default;
                    $inc['updated_date'] = date('Y-m-d H:i:s');
                    $this->_db->where('currency_id', $in['currency_id']);
                    $this->_db->update('tms_currency', $inc);
                }
            }
        }
        unset($data['curDef']);
        $this->_db->where('currency_id',$id);
        $id = $this->_db->update('tms_currency', $data);
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
        $results = $this->_db->get('tms_currency');
        return $results;
    }
    
    public function delete($id) {
        $this->_db->where('currency_id', $id);
        //$id = $this->_db->delete('tms_currency');

        $data = $this->_db->getOne('tms_currency');
        $currency = $data['country_name'];


        //Checking currency in tms_client table
        $queryMatch = "select client_currency from tms_client where client_currency like '$currency%'";
        $exeQueryMatch = $this->_db->rawQuery($queryMatch);

        //Checking currency in tms_client table
        $queryMatch1 = "select price_currency from tms_customer_price_list where price_currency like '$currency%'";
        $exeQueryMatch1 = $this->_db->rawQuery($queryMatch1);


        //echo count($exeQueryMatch1).'---'.count($exeQueryMatch);exit;
        if(count($exeQueryMatch1) > 1 && count($exeQueryMatch) > 1){
            // echo 'if';exit;
            $return['status'] = 422;
            $return['msg'] = 'you can not delete currency it is in use.';
        }else{
            // echo 'else';exit;
            $this->_db->where('currency_id', $id);
            $id = $this->_db->delete('tms_currency');
            $return['status'] = 200;
            $return['msg'] = 'Deleted Successfully.';
        }
        // if ($id) {
        //     $return['status'] = 200;
        //     $return['msg'] = 'Deleted Successfully.';
        // } else {
        //     $return['status'] = 422;
        //     $return['msg'] = 'Not inserted.';
        // }
        return $return;
    }

    public function getTypeById($id) {
        $this->_db->where('currency_id', $id);
        $result = $this->_db->getOne('tms_currency');
        return $result;
    }
    
    public function getActive() {
        $this->_db->where('is_active', 1);
        $result = $this->_db->get('tms_currency');
        return $result;
    }

    public function currencyDefaultData() {
        $this->_db->where('currency_id',1);
        $result = $this->_db->getOne('tms_currency');
        return $result['country_name'];
    }

    //currency exchange rate automatically at night OLD
    public function currencyrateAutomaticaly() {
        $data['curDef'] = self::currencyDefaultData();
        if(isset($data['curDef'])) {
            $info = self::getAll();
            foreach($info AS $in) {
                if($in['default_front']!=1) {
                    $default = self::currencyConverts(1,$data['curDef'],$in['country_name']);
                    $current = self::currencyConverts($in['rate'],$in['country_name'],$data['curDef']);
                    $inc['c_current'] = $current;
                    $inc['c_default'] = $default;
                    $inc['updated_date'] = date('Y-m-d H:i:s');
                    $this->_db->where('currency_id', $in['currency_id']);
                    $result = $this->_db->update('tms_currency', $inc);
                    //echo $this->_db->getLastQuery();
                }
            }
        }
        if($result) {
            $return['status'] = 200;
            $return['msg'] = 'Insert Successfully.';
        } else {
            $return['status'] = 422;
            $return['msg'] = 'Not inserted.';    
        }
        return $return;
    }

    public function defaultCurrencyGet() {
        $this->_db->where('default_front',1);
        $data = $this->_db->getOne('tms_currency');
        return $data;
    }

    public function currencyrateAll() {
        $from = 'EUR';
        $to = 'GBP';
        $url = 'http://finance.yahoo.com/d/quotes.csv?f=l1d1t1&s='.$from.$to.'=X';
        $handle = fopen($url, 'r');
        if ($handle) {
            $result = fgetcsv($handle);
            fclose($handle);
        }
        print_r($result);

        //echo '1 '.$from.' is worth '.$result[0].' '.$to.' Based on data on '.$result[1].' '.$result[2];exit;
    }

    public function currencyCodeGet() {
            $currency_info = array(
            'BGN'=>array('name' => 'Bulgarian Lev', 'symbol'=>'лв'),
            'CAD'=>array('name' => 'Canadian Dollar', 'symbol'=>'$', 'hex'=>'&#x24;'),
            'EUR'=>array('name' => 'European Union (Euro)', 'symbol'=>'€', 'hex'=>'&#x20ac;'),
            'GBP'=>array('name' => 'British Pound', 'symbol'=>'£', 'hex'=>'&#xa3;'),
            'NOK'=>array('name' => 'Norwegian Kroner', 'symbol'=>'kr', 'hex'=>'&#x6b;&#x72;'),
            'USD'=>array('name' => 'US Dollar', 'symbol'=>'$', 'hex'=>'&#x24;')
            // 'AED'=>array('name' => 'United Arab EMIRATES Dirham', 'symbol'=>'د.إ', 'hex'=>'&#x62f;&#x2e;&#x625;'),
            // 'ANG'=>array('name' => 'NL Antillian Guilder', 'symbol'=>'ƒ', 'hex'=>'&#x192;'),
            // 'ARS'=>array('name' => 'Argentine Peso', 'symbol'=>'$', 'hex'=>'&#x24;'),
            // 'AUD'=>array('name' => 'Australian Dollar', 'symbol'=>'A$', 'hex'=>'&#x41;&#x24;'),
            // 'BRL'=>array('name' => 'Brazilian Real', 'symbol'=>'R$', 'hex'=>'&#x52;&#x24;'),
            // 'BSD'=>array('name' => 'Bahamian Dollar', 'symbol'=>'B$', 'hex'=>'&#x42;&#x24;'),
            // 'CAD'=>array('name' => 'Canadian Dollar', 'symbol'=>'$', 'hex'=>'&#x24;'),
            // 'CHF'=>array('name' => 'Swiss Franc', 'symbol'=>'CHF', 'hex'=>'&#x43;&#x48;&#x46;'),
            // 'CLP'=>array('name' => 'Chilean Peso', 'symbol'=>'$', 'hex'=>'&#x24;'),
            // 'CNY'=>array('name' => 'Chinese Yuan Renminbi', 'symbol'=>'¥', 'hex'=>'&#xa5;'),
            // 'COP'=>array('name' => 'Colombian Peso', 'symbol'=>'$', 'hex'=>'&#x24;'),
            // 'CZK'=>array('name' => 'Czech Koruna', 'symbol'=>'Kč', 'hex'=>'&#x4b;&#x10d;'),
            // 'DKK'=>array('name' => 'Danish Krone', 'symbol'=>'kr', 'hex'=>'&#x6b;&#x72;'),
            // 'EUR'=>array('name' => 'Euro', 'symbol'=>'€', 'hex'=>'&#x20ac;'),
            // 'FJD'=>array('name' => 'Fiji Dollar', 'symbol'=>'FJ$', 'hex'=>'&#x46;&#x4a;&#x24;'),
            // 'GBP'=>array('name' => 'British Pound', 'symbol'=>'£', 'hex'=>'&#xa3;'),
            // 'GHS'=>array('name' => 'Ghanaian New Cedi', 'symbol'=>'GH₵', 'hex'=>'&#x47;&#x48;&#x20b5;'),
            // 'GTQ'=>array('name' => 'Guatemalan Quetzal', 'symbol'=>'Q', 'hex'=>'&#x51;'),
            // 'HKD'=>array('name' => 'Hong Kong Dollar', 'symbol'=>'$', 'hex'=>'&#x24;'),
            // 'HNL'=>array('name' => 'Honduran Lempira', 'symbol'=>'L', 'hex'=>'&#x4c;'),
            // 'HRK'=>array('name' => 'Croatian Kuna', 'symbol'=>'kn', 'hex'=>'&#x6b;&#x6e;'),
            // 'HUF'=>array('name' => 'Hungarian Forint', 'symbol'=>'Ft', 'hex'=>'&#x46;&#x74;'),
            // 'IDR'=>array('name' => 'Indonesian Rupiah', 'symbol'=>'Rp', 'hex'=>'&#x52;&#x70;'),
            // 'ILS'=>array('name' => 'Israeli New Shekel', 'symbol'=>'₪', 'hex'=>'&#x20aa;'),
            // 'INR'=>array('name' => 'Indian Rupee', 'symbol'=>'₹', 'hex'=>'&#x20b9;'),
            // 'ISK'=>array('name' => 'Iceland Krona', 'symbol'=>'kr', 'hex'=>'&#x6b;&#x72;'),
            // 'JMD'=>array('name' => 'Jamaican Dollar', 'symbol'=>'J$', 'hex'=>'&#x4a;&#x24;'),
            // 'JPY'=>array('name' => 'Japanese Yen', 'symbol'=>'¥', 'hex'=>'&#xa5;'),
            // 'KRW'=>array('name' => 'South-Korean Won', 'symbol'=>'₩', 'hex'=>'&#x20a9;'),
            // 'LKR'=>array('name' => 'Sri Lanka Rupee', 'symbol'=>'₨', 'hex'=>'&#x20a8;'),
            // 'MAD'=>array('name' => 'Moroccan Dirham', 'symbol'=>'.د.م', 'hex'=>'&#x2e;&#x62f;&#x2e;&#x645;'),
            // 'MMK'=>array('name' => 'Myanmar Kyat', 'symbol'=>'K', 'hex'=>'&#x4b;'),
            // 'MXN'=>array('name' => 'Mexican Peso', 'symbol'=>'$', 'hex'=>'&#x24;'),
            // 'MYR'=>array('name' => 'Malaysian Ringgit', 'symbol'=>'RM', 'hex'=>'&#x52;&#x4d;'),
            // 'NOK'=>array('name' => 'Norwegian Kroner', 'symbol'=>'kr', 'hex'=>'&#x6b;&#x72;'),
            // 'NZD'=>array('name' => 'New Zealand Dollar', 'symbol'=>'$', 'hex'=>'&#x24;'),
            // 'PAB'=>array('name' => 'Panamanian Balboa', 'symbol'=>'B/.', 'hex'=>'&#x42;&#x2f;&#x2e;'),
            // 'PEN'=>array('name' => 'Peruvian Nuevo Sol', 'symbol'=>'S/.', 'hex'=>'&#x53;&#x2f;&#x2e;'),
            // 'PHP'=>array('name' => 'Philippine Peso', 'symbol'=>'₱', 'hex'=>'&#x20b1;'),
            // 'PKR'=>array('name' => 'Pakistan Rupee', 'symbol'=>'₨', 'hex'=>'&#x20a8;'),
            // 'PLN'=>array('name' => 'Polish Zloty', 'symbol'=>'zł', 'hex'=>'&#x7a;&#x142;'),
            // 'RON'=>array('name' => 'Romanian New Lei', 'symbol'=>'lei', 'hex'=>'&#x6c;&#x65;&#x69;'),
            // 'RSD'=>array('name' => 'Serbian Dinar', 'symbol'=>'RSD', 'hex'=>'&#x52;&#x53;&#x44;'),
            // 'RUB'=>array('name' => 'Russian Rouble', 'symbol'=>'руб', 'hex'=>'&#x440;&#x443;&#x431;'),
            // 'SEK'=>array('name' => 'Swedish Krona', 'symbol'=>'kr', 'hex'=>'&#x6b;&#x72;'),
            // 'SGD'=>array('name' => 'Singapore Dollar', 'symbol'=>'S$', 'hex'=>'&#x53;&#x24;'),
            // 'THB'=>array('name' => 'Thai Baht', 'symbol'=>'฿', 'hex'=>'&#xe3f;'),
            // 'TND'=>array('name' => 'Tunisian Dinar', 'symbol'=>'DT', 'hex'=>'&#x44;&#x54;'),
            // 'TRY'=>array('name' => 'Turkish Lira', 'symbol'=>'TL', 'hex'=>'&#x54;&#x4c;'),
            // 'TTD'=>array('name' => 'Trinidad/Tobago Dollar', 'symbol'=>'$', 'hex'=>'&#x24;'),
            // 'TWD'=>array('name' => 'Taiwan Dollar', 'symbol'=>'NT$', 'hex'=>'&#x4e;&#x54;&#x24;'),
            // 'USD'=>array('name' => 'US Dollar', 'symbol'=>'$', 'hex'=>'&#x24;'),
            // 'VEF'=>array('name' => 'Venezuelan Bolivar Fuerte', 'symbol'=>'Bs', 'hex'=>'&#x42;&#x73;'),
            // 'VND'=>array('name' => 'Vietnamese Dong', 'symbol'=>'₫', 'hex'=>'&#x20ab;'),
            // 'XAF'=>array('name' => 'CFA Franc BEAC', 'symbol'=>'FCFA', 'hex'=>'&#x46;&#x43;&#x46;&#x41;'),
            // 'XCD'=>array('name' => 'East Caribbean Dollar', 'symbol'=>'$', 'hex'=>'&#x24;'),
            // 'XPF'=>array('name' => 'CFP Franc', 'symbol'=>'F', 'hex'=>'&#x46;'),
            // 'ZAR'=>array('name' => 'South African Rand', 'symbol'=>'R', 'hex'=>'&#x52;')
        );
        return $currency_info;
    }

    //currency exchange rate automatically at night New
    public function currencyExchange(){
        $rowQuery = 'UPDATE `tms_currency` SET `is_active`=1';
        $this->_db->rawQuery($rowQuery);

        $info = self::getAll();
        foreach ($info as $key => $value) {
            if($value['ob_exchange_rate_auto'] == 1 && $value['country_name'] != 'EUR'){
                $xref  = simplexml_load_file('http://www.ecb.europa.eu/stats/eurofxref/eurofxref-daily.xml');
                $nodes = $xref->xpath('//*[@currency="'.$value['country_name'].'"]');
                
                $data['current_curency_rate'] = ((array) $nodes[0]['rate']);
                $data['current_curency_rate'] = $data['current_curency_rate'][0];
                if($value['current_curency_rate'] != $data['current_curency_rate']){
                    $data['updated_date'] = date('Y-m-d H:i:s');
                    $this->_db->where('currency_id', $value['currency_id']);
                    $id = $this->_db->update('tms_currency', $data);
                }
                
            }
        }
        
        $return['status']   = 200;
        $return['msg']      = 'All currecy rates updated successfully';
        return $return; 
    }

}
