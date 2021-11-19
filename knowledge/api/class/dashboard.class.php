<?php

class Dashboard {
    
    public function __construct() {
        $this->_db = db::getInstance();   
    }
    
    public function userBirthDate() {
        $query = "SELECT * FROM  tms_users WHERE  DATE_ADD( dtBirthDate,INTERVAL YEAR(CURDATE())-YEAR(dtBirthDate)+ IF(DAYOFYEAR(CURDATE()) >= DAYOFYEAR(dtBirthDate),1,0)
                YEAR)BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 2 MONTH) AND eUserStatus = 3 AND activation_status = 1 ORDER BY dtBirthDate ASC";
        /*$query = "SELECT * FROM tms_users WHERE DATE_ADD(`dtBirthDate`, INTERVAL YEAR(CURDATE())-YEAR(`dtBirthDate`) YEAR) BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 2 MONTH) AND eUserStatus = 3 AND activation_status = 1 ORDER BY dtBirthDate ASC";*/
        return $data = $this->_db->rawQuery($query);
    }

    public function knowledgeEventGetAll() {
    	return $this->_db->get('tms_knowledge_events');
    }
    public function holidayGet($id) {
        $countries = array('norway','sweden','denmark','finland');
        $ch0 = array();
        $ch1 = array();
        $ch2 = array();
        $ch3 = array();
        foreach($countries as $key => $value) {
            $holidayData = self::getholidayList($value);
            if($key == 0){
                $ch0 = $holidayData;
            }elseif($key == 1){
                $ch1 = $holidayData;
            }elseif($key == 2){
                $ch2 = $holidayData;
            }else{
                $ch3 = $holidayData;
            }
        }
        $finalCountryDataAll = array_merge($ch0,$ch1,$ch2,$ch3);
        
        $finalCountryDataUnique = array_map("unserialize", array_unique(array_map("serialize",$finalCountryDataAll)));
        
        return $finalCountryDataUnique;
    }
    public function getholidayList($country){
        $url = 'https://www.timeanddate.com/holidays/'.$country.'/';
        $cSession = curl_init();
        curl_setopt($cSession,CURLOPT_URL,$url);
        curl_setopt($cSession, CURLOPT_HTTPHEADER, ['Accept-Language: en']);
        curl_setopt($cSession,CURLOPT_RETURNTRANSFER,true);
        curl_setopt($cSession,CURLOPT_HEADER, false);
        $html=curl_exec($cSession);
        curl_close($cSession);

        $dom = new DOMDocument();
        # The @ before the method call suppresses any warnings that
        # loadHTML might throw because of invalid HTML in the page.
            @$dom->loadHTML($html);
            $holidays=array();
            $items = $dom->getElementsByTagName('tr');
            

            foreach ($items as $key=>$node) {
                $holidays[]=self::tdrows($node->childNodes);
            }
            //The first and second items in the array were the titles of the table and a blank row 
            //so we unset them
            unset($holidays[0]);
            unset($holidays[1]);
            //then reindex the array
            $holidays = array_values($holidays);
            foreach ($holidays as $key => $subArr) {
                unset($subArr['1']);
                unset($subArr['3']);
                $holidays[$key] = $subArr;  
            }
            $hData = [];

            foreach ($holidays as $key => $subArr) {
                //print_r($subArr[2]);

                 $splitDate = explode(' ',$subArr[0]);
                 $month = $splitDate[1]; 
                 $date = $splitDate[0];
                 if(strlen($date)==1){
                    $date = str_pad($date,2,"0", STR_PAD_LEFT);
                 } 
                 $year = date('Y');
                 $finalDate = $year.'-'.$month.'-'.$date;
                 $subArr[0] = date('Y-m-d', strtotime($finalDate));
                 $hData = array(
                    "holidayDate" => $subArr[0],
                    "holidayName" => $subArr[2]
                );

                 $holidays[$key] = $hData;
            }
        
        return $holidays;
    }

    public function tdrows($elements) {
        $str = "";
        foreach ($elements as $element) {
            $str .= $element->nodeValue . ", ";
        }

        //This pplaces the items into an array
        $tempArray=explode(',',$str);
        //This gets rid of empty array elements
        unset($tempArray[4]);
        unset($tempArray[5]);
        return $tempArray;
    }

    public function unique_multidim_array($array, $key) { 
        $temp_array = array(); 
        $i = 0; 
        $key_array = array(); 
        
        foreach($array as $val) { 
            if (!in_array($val[$key], $key_array)) { 
                $key_array[$i] = $val[$key]; 
                $temp_array[$i] = $val; 
            } 
            $i++; 
        } 
        return $temp_array; 
    } 
 
}