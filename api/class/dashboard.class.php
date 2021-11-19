<?php
class dashboard {

    public function __construct() {
        $this->_db = db::getInstance();
    }

    public function OrderGet() {
        //$qry = "SELECT gen.heads_up,gen.order_no AS orderNumber,gen.due_date AS DueDate,gen.order_id AS orderId, cust.created_date AS orderDate, cust.client AS customer,gen.project_name AS projectName, c.vUserName AS contactName,stus.status_name AS clientStatus,c.vlogo AS clientlogo,c.tPoInfo AS ponumber,gen.company_code AS companyCode,gen.project_price,gen.expected_start_date,cust.contact AS contactPerson,its.item_status AS itemStatus,gen.project_status AS projectStatus,gen.project_type AS projectType,plang.source_lang AS sourceLanguage,plang.target_lang AS targetLanguage,its.total_amount AS totalAmount,inc.vUserName as accountname,tu.vUserName as pm_name ,ps.project_status_name as projectstatus_name ,ps.status_color as projectstatus_color FROM tms_general AS gen LEFT JOIN tms_customer AS cust ON gen.order_id=cust.order_id LEFT JOIN tms_items AS its ON gen.order_id=its.order_id LEFT JOIN tms_proj_language As plang ON gen.order_id = plang.order_id LEFT JOIN tms_client AS c ON cust.client = c.iClientId LEFT JOIN tms_user_status AS stus ON c.vStatus = stus.status_id LEFT JOIN tms_client_indirect as inc ON inc.iClientId = cust.indirect_customer LEFT JOIN tms_users as tu ON tu.iUserId = cust.project_manager LEFT JOIN tms_project_status as ps ON ps.pr_status_id = gen.project_status group by gen.order_no";
        $qry = "SELECT gen.heads_up, gen.order_no AS orderNumber, gen.due_date AS DueDate, gen.order_id AS orderId, cust.created_date AS orderDate, cust.client AS customer, gen.project_name AS projectName, c.vUserName AS contactName, stus.status_name AS clientStatus, c.vlogo AS clientlogo, c.tPoInfo AS ponumber, gen.company_code AS companyCode, gen.project_price, gen.expected_start_date, cust.contact AS contactPerson, its.item_status AS itemStatus, its.po_number AS itemPonumber, its.start_date AS itemStartdate, its.due_date AS itemDuedate,its.source_lang AS itemsSourceLang, its.target_lang AS itemsTargetLang, gen.project_status AS projectStatus, gen.project_type AS projectType, plang.source_lang AS sourceLanguage, plang.target_lang AS targetLanguage, its.total_amount AS totalAmount, inc.vUserName AS accountname, tu.vUserName AS pm_name, ps.project_status_name AS projectstatus_name, ps.status_color AS projectstatus_color FROM tms_items AS its LEFT JOIN tms_general AS gen ON its.order_id = gen.order_id LEFT JOIN tms_customer AS cust ON its.order_id = cust.order_id LEFT JOIN tms_proj_language AS plang ON its.order_id = plang.order_id LEFT JOIN tms_client AS c ON cust.client = c.iClientId LEFT JOIN tms_user_status AS stus ON c.vStatus = stus.status_id LEFT JOIN tms_client_indirect AS inc ON inc.iClientId = cust.indirect_customer LEFT JOIN tms_users AS tu ON tu.iUserId = cust.project_manager LEFT JOIN tms_project_status AS ps ON ps.pr_status_id = gen.project_status";
        $data = $this->_db->rawQuery($qry);

        foreach($data AS $key=>$value) {
            $qry1 = "SELECT itemId,order_id, source_lang,target_lang FROM tms_items where order_id = ".$value['orderId']  ;
            $itemsdata = $this->_db->rawQuery($qry1);
            /*foreach($itemsdata AS $key1=>$fdata) {
                $ddata = $fdata;
                array_push($data[$key], $ddata);
            }*/
            $data[$key]['items'] = $itemsdata;
        }
        return $data;
    }
    
    // public function ProjectsOrderGet($id) {
    //     $qry = "SELECT gen.heads_up,gen.order_no AS orderNumber,gen.due_date AS DueDate,gen.order_id AS orderId, cust.created_date AS orderDate, cust.client AS customer,gen.project_name AS projectName, c.vUserName AS contactName,stus.status_name AS clientStatus,c.vlogo AS clientlogo,c.tPoInfo AS ponumber,gen.company_code AS companyCode,gen.project_price,gen.expected_start_date,cust.contact AS contactPerson,its.item_status AS itemStatus,gen.project_status AS projectStatus,gen.project_type AS projectType,plang.source_lang AS sourceLanguage,plang.target_lang AS targetLanguage,its.total_amount AS totalAmount,inc.vUserName as accountname,tu.vUserName as pm_name ,ps.project_status_name as projectstatus_name ,ps.status_color as projectstatus_color, count(td.order_id) as comment_id FROM tms_general AS gen LEFT JOIN tms_customer AS cust ON gen.order_id=cust.order_id LEFT JOIN tms_items AS its ON gen.order_id=its.order_id LEFT JOIN tms_proj_language As plang ON gen.order_id = plang.order_id LEFT JOIN tms_client AS c ON cust.client = c.iClientId LEFT JOIN tms_user_status AS stus ON c.vStatus = stus.status_id LEFT JOIN tms_client_indirect as inc ON inc.iClientId = cust.indirect_customer LEFT JOIN tms_users as tu ON tu.iUserId = cust.project_manager LEFT JOIN tms_project_status as ps ON ps.pr_status_id = gen.project_status LEFT JOIN tms_discussion AS td ON td.order_id = gen.order_id group by gen.order_no";
    //     $data = $this->_db->rawQuery($qry);
    //     foreach($data AS $key=>$value) {
    //         $qry1 = "SELECT itemId,order_id, source_lang,target_lang FROM tms_items where order_id = ".$value['orderId']  ;
    //         $itemsdata = $this->_db->rawQuery($qry1);
    //         /*foreach($itemsdata AS $key1=>$fdata) {
    //             $ddata = $fdata;
    //             array_push($data[$key], $ddata);
    //         }*/
    //         $data[$key]['items'] = $itemsdata;

    //         $qry2 = "SELECT count(id) as comment_status FROM tms_discussion where order_id = ".$value['orderId']. " AND NOT FIND_IN_SET(".$id.",read_id)"  ;
    //         $commentdata = $this->_db->rawQuery($qry2);
    //         $data[$key]['comment'] = $commentdata;
            
    //     }

    //     return $data;
    // }

    public function ProjectsOrderGet($id) {
        $qry = "SELECT gen.heads_up, gen.order_no AS orderNumber, gen.due_date AS DueDate, gen.order_id AS orderId, cust.created_date AS orderDate, cust.client AS customer, gen.project_name AS projectName, c.vUserName AS contactName, stus.status_name AS clientStatus, c.vlogo AS clientlogo, c.tPoInfo AS ponumber, gen.company_code AS companyCode, gen.project_price, gen.expected_start_date, cust.contact AS contactPerson,its.item_number, its.item_status AS itemStatus, its.po_number AS itemPonumber, its.start_date AS itemStartdate, its.due_date AS itemDuedate,its.source_lang AS itemsSourceLang, its.target_lang AS itemsTargetLang, gen.project_status AS projectStatus, gen.project_type AS projectType, plang.source_lang AS sourceLanguage, plang.target_lang AS targetLanguage, its.total_amount AS totalAmount, inc.vUserName AS accountname, tu.vUserName AS pm_name, ps.project_status_name AS projectstatus_name, ps.status_color AS projectstatus_color FROM tms_items AS its LEFT JOIN tms_general AS gen ON its.order_id = gen.order_id LEFT JOIN tms_customer AS cust ON its.order_id = cust.order_id LEFT JOIN tms_proj_language AS plang ON its.order_id = plang.order_id LEFT JOIN tms_client AS c ON cust.client = c.iClientId LEFT JOIN tms_user_status AS stus ON c.vStatus = stus.status_id LEFT JOIN tms_client_indirect AS inc ON inc.iClientId = cust.indirect_customer LEFT JOIN tms_users AS tu ON tu.iUserId = cust.project_manager LEFT JOIN tms_project_status AS ps ON ps.pr_status_id = gen.project_status";
        $data = $this->_db->rawQuery($qry);
        foreach($data AS $key=>$value) {
            // $qry1 = "SELECT itemId,order_id, source_lang,target_lang FROM tms_items where order_id = ".$value['orderId']  ;
            // $itemsdata = $this->_db->rawQuery($qry1);
            // $data[$key]['items'] = $itemsdata;
            $qry3 = "SELECT count(id) as comment_id FROM tms_discussion where order_id = ".$value['orderId']  ;
            $comments = $this->_db->rawQuery($qry3);
            $data[$key]['comment_id'] = $comments;

            $qry2 = "SELECT count(id) as comment_status FROM tms_discussion where order_id = ".$value['orderId']. " AND NOT FIND_IN_SET(".$id.",read_id)"  ;
            $commentdata = $this->_db->rawQuery($qry2);
            $data[$key]['comment'] = $commentdata;
            
        }
        return $data;
    }

    public function userActivityGet($data) {
        $info = array();
        foreach($data AS $key=>$value) {
            $this->_db->where('iUserId', $value);
            $info[$key] = $this->_db->getOne('tms_users');
        }
        return $info;
    }
    
    public function userActivityGetOne($id) {
        $this->_db->where('iUserId', $id);
        $data = $this->_db->get('tms_users');
        return $data;
    }
    
    public function  jobActivityGet($data) {
        $info = array();
        foreach($data AS $key=>$value) {
            $this->_db->where('job_summmeryId', $value);
            $info[$key] = $this->_db->getOne('tms_summmery_view');
        }
        return $info;
    }

    public function taskJobget($id) {
        $currentDate = date("Y-m-d");
        $this->_db->orderBy("tsv.due_date","asc");
        $this->_db->where("tsv.due_date",$currentDate,">=");
        $this->_db->where("tc.project_manager",$id);
        $this->_db->join('tms_customer tc','tsv.order_id = tc.order_id','INNER');
        return $data = $this->_db->get('tms_summmery_view tsv');
    }
    
    public function holidayGet($country) {
        $url = 'https://www.timeanddate.com/holidays/'.strtolower($country).'/';
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
            function tdrows($elements) {
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

            foreach ($items as $key=>$node) {
                $holidays[]=tdrows($node->childNodes);
            }
        //The first and secone items in the array were the titles of the table and a blank row 
        //so we unset them
        unset($holidays[0]);
        unset($holidays[1]);
        //then reindex the array
        $holidays = array_values($holidays);
        return $holidays;
    }

    public function getCountry () {
        return $this->_db->get('tms_country');
    }

}