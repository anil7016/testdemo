<?php

require_once 'functions.class.php';
require_once 'mail_format.class.php';
require_once 'userstatus.class.php';

class filemanager {

    protected $_db;
    protected $_useremail;
    protected $_username;
    protected $_userid;
    private $parentId;
    public $parents_data=array();
    public $treeArr_data=array();

    public function __construct() {
        $this->_db = db::getInstance();
    }
    
    public function fileCustomeget($id, $field) {
        $this->_db->where($field,$id);
        return $this->_db->getOne('tms_filemanager');
    }

    public function filegetByjobId($id) {
        $this->_db->where('job_id',$id);
        return $this->_db->getOne('tms_filemanager');
    }

    public function filegetByfield($field,$id) {
        $this->_db->where($field,$id);
        return $this->_db->getOne('tms_filemanager');
    }

    public function filegetByfmanagerId($id) {
        $this->_db->where('fmanager_id',$id);
        return $this->_db->getOne('tms_filemanager');
    }

    public function filegetByparentId($id) {
        $this->_db->where('parent_id',$id);
        return $this->_db->get('tms_filemanager');
    }

    public function fileManagerGet(){
        $this->_db->where('parent_id',0);
        $data = $this->_db->get('tms_filemanager');
        return $data;
    }

    // folder structure tree view
    public function buildTree(array $elements, $parentId = 0) {
        $branch = array();
        foreach ($elements as $element) {
            //if($element['parent_id'] == $parentId)
            if ($element['parent_id'] == $parentId) {
                $children = Self::buildTree($elements, $element['fmanager_id']);
                $element['countchild'] = count($children);
                if ($children) {
                    $element['countChild'] = count($children);
                    $element['categories'] = $children;
                    //$element['categoriesCount']++;
                    //$element['counts'] = count($children);
                }
                $branch[] = $element;
            }
        }
        return $branch;
    }

    // to get folder url from tree view flat array
    public function buildTreePath(array $elements, $parentId = 0) {
        $branch = array();

        foreach ($elements as $key => $element) {
            
            if ($element['parent_id'] == $parentId) {
                        $branch[] = $element;
                        //if(count($element['fmanager_id']) > 0) {
                        if($element['fmanager_id']) {
                            $branch = array_merge($branch,Self::buildTreePath($elements, $element['fmanager_id']));
                        }
            }
        }

        return $branch;
    }

    public function find_parents($input, $id, $empty_id, array $fparentArray) {
        
    //global $parents_data = [];

        if(is_array($input)) {

            foreach($input as $k => $val) {
                if($val['fmanager_id'] == $id  && $val['parent_id'] != 0) {
                    if($val['ext']==''){
                        array_push($this->parents_data, array('id'=>$val['fmanager_id'],'name'=>$val['name']) );
                    }
                        Self::find_parents($fparentArray, $val['parent_id'],$empty_id,$fparentArray );
                    
                }
            }
        }
        if($empty_id>0){
                return $this->parents_data;
        }else{
            $this->parents_data = [];
            return 0;
        }
    }

    public function folder_pathurl($mydata){
        $mydata_new = array();
        $folderurl = '';

        //$mydata = array_map("unserialize", array_unique(array_map("serialize", $mydata)));
             
        foreach ($mydata as $p_key => $row){
                $mydata_new[$p_key] = $row['id'];
            }
            array_multisort($mydata_new, SORT_ASC, $mydata);

            foreach ($mydata as $key => $row)
            {
                $folderurl .= $row['name'].'/';
            }
        return $folderurl;  
    }

    public function buildTreearraydata_new(array $elements, $parentId = 0, array $staticFullarray, $staticParentId) {
        $branch = array();
        //$arrAll2 = array();
        $folderurl ='';
        $arrAll2 = $this->treeArr_data;
        global $parents_data;

        //$arrAll2 = Self::buildTreePath($staticFullarray,$staticParentId);
        foreach ($elements as $key => $element) {
            //if($element['parent_id'] == $parentId)
            if ($element['parent_id'] == $parentId) {
                
                $mydata = Self::find_parents($arrAll2, $element['fmanager_id'], 1, $arrAll2);
                $folderurl = '';
                if($mydata){
                    $folderurl = Self::folder_pathurl($mydata);
                }
                $element['folderurl'] = $folderurl;
                Self::find_parents($arrAll2, $element['fmanager_id'] ,0 ,$arrAll2);
                $branch[] = $element;
                    //if(count($element['fmanager_id']) > 0) {
                        $branch = array_merge($branch,Self::buildTreearraydata_new($elements, $element['fmanager_id'],$staticFullarray, $staticParentId));
                    //}
            }
        }
        return $branch;
    }


    public function buildTreearraydata(array $elements, $parentId = 0) {
        $branch = array();
        foreach ($elements as $key => $element) {
            //if($element['parent_id'] == $parentId)
            if ($element['parent_id'] == $parentId) {
                $mydata = find_parents($arrAll2, $element['fmanager_id'], 1);
                $branch[] = $element;
                    //if(count($element['fmanager_id']) > 0) {
                        $branch = array_merge($branch,Self::buildTreearraydata($elements, $element['fmanager_id']));
                    //}
            }
        }
        return $branch;
    }


   public function filefolderGet($id,$root,$externalResourceUserId){
        if($externalResourceUserId !='null'){
            $chkIfRoot = $this->_db->rawQuery("SELECT * FROM tms_filemanager where fmanager_id = $id AND is_ex_project_folder =1 AND is_default_folder =1");
            if($chkIfRoot){
                $chkAssignResource = $this->_db->rawQuery("SELECT * FROM tms_summmery_view where resource = $externalResourceUserId");
                
                        $chkAssignResource = $this->_db->rawQuery("SELECT * FROM tms_summmery_view where resource = $externalResourceUserId");
                        if($chkAssignResource){
                            $exUserPfolder = $this->_db->rawQuery("SELECT * FROM tms_filemanager where user_id = $externalResourceUserId");
                            $pId = $exUserPfolder[0]['fmanager_id'];
                            $exUserPfolder1 = $this->_db->rawQuery("SELECT * FROM tms_filemanager where parent_id = $pId AND is_ex_project_folder = 1");
                        $filemanagerWhereSql = 'WHERE parent_id = "'.$id.'" AND is_project_folder = 1 OR parent_id = "'.$exUserPfolder1[0]['fmanager_id'].'"';
                        foreach ($chkAssignResource as $key => $value) {
                            $orderId = $value['order_id'];
                            
                            $chkIsProjectFolder = $this->_db->rawQuery("SELECT * FROM tms_filemanager where order_id = $orderId AND is_project_folder = 1");
                            
                            if($chkIsProjectFolder){
                                foreach($chkIsProjectFolder as $chkIsProjectFolder_row){
                                    $filemanagerWhereSql .= ' OR fmanager_id = "'.$chkIsProjectFolder_row['fmanager_id'].'"';
                                }
                            }
                        }
                        
                        $filemanagerSql = "SELECT * FROM tms_filemanager " . $filemanagerWhereSql;
                        $data = $this->_db->rawQuery($filemanagerSql);
                       
                    }else{
                        $this->_db->where('parent_id',$id);
                        $data = $this->_db->get('tms_filemanager');
                    }
            }else{
                    $chkForProjectFolder = $this->_db->rawQuery("SELECT * FROM tms_filemanager where fmanager_id = $id AND  name= 'Projects'");
                    if($chkForProjectFolder){
                       $chkAssignResource = $this->_db->rawQuery("SELECT * FROM tms_summmery_view where resource = $externalResourceUserId");
                    foreach ($chkAssignResource as $key => $value) {
                        $orderId = $value['order_id'];
                        $filemanagerWhereSql = 'WHERE parent_id = "'.$id.'" AND order_id="'.$value['order_id'].'"';
                        
                        $chkIsProjectFolder = $this->_db->rawQuery("SELECT * FROM tms_filemanager where order_id = $orderId AND is_project_folder = 1");
                        
                        if($chkIsProjectFolder){
                            foreach($chkIsProjectFolder as $chkIsProjectFolder_row){
                                $filemanagerWhereSql .= ' OR fmanager_id = "'.$chkIsProjectFolder_row['fmanager_id'].'"';
                            }
                        }
                    }
                    
                    $filemanagerSql = "SELECT * FROM tms_filemanager " . $filemanagerWhereSql;
                    $data = $this->_db->rawQuery($filemanagerSql);
                }else{
                    $this->_db->where('parent_id',$id);
                    $data = $this->_db->get('tms_filemanager');
                }
            }
            
        }else{
            $this->_db->where('parent_id',$id);
            $data = $this->_db->get('tms_filemanager');    
            $dataArr = $this->_db->get('tms_filemanager');    
            $i = 0;
            foreach($data as $data1){
                $data[$i]['countchild'] = 0;
                $data[$i]['categories'] = [];
                if($data1['ext'] == ''){
                    $data[$i]['categories'] = self::buildTree($dataArr,$data[$i]['fmanager_id']);
                } 
                $i++;
            }
        }
        return $data;
    }
    
    public function higherfolderGet($id,$exteruserId){
        $this->_db->where('fmanager_id',$id);
        $info = $this->_db->getOne('tms_filemanager');

        $this->_db->where('fmanager_id',$info['parent_id']);
        $fname = $this->_db->getOne('tms_filemanager');
        
        $dataArr = $this->_db->get('tms_filemanager');
        if($exteruserId == 'null'){
            $this->_db->where('parent_id',$info['parent_id']);
            $dat = $this->_db->get('tms_filemanager');
            $data['info'] = $info;
            $data['data'] = $dat;
            $data['fname'] = $fname['name'];
            $i = 0;
            foreach($data['data'] as $data1){
                $data['data'][$i]['categories'] = [];
                $data['data'][$i]['countchild'] = 0;
                if($data1['ext'] == ''){
                    $data['data'][$i]['categories'] = self::buildTree($dataArr,$data['data'][$i]['fmanager_id']);
                } 
                $i++;
            }
            return $data;
        }else{
            $chkIfRoot = $this->_db->rawQuery("SELECT * FROM tms_filemanager where fmanager_id = $id AND is_ex_project_folder =1 AND is_default_folder =1");
            if($chkIfRoot){
               $chkAssignResource = $this->_db->rawQuery("SELECT * FROM tms_summmery_view where resource = $exteruserId");
                $filemanagerWhereSql = 'WHERE parent_id = "'.$id.'"';
                foreach ($chkAssignResource as $key => $value) {
                    $orderId = $value['order_id'];
                    
                    $chkIsProjectFolder = $this->_db->rawQuery("SELECT * FROM tms_filemanager where order_id = $orderId AND is_project_folder = 1");
                    
                    if($chkIsProjectFolder){
                        foreach($chkIsProjectFolder as $chkIsProjectFolder_row){
                            $filemanagerWhereSql .= ' OR fmanager_id = "'.$chkIsProjectFolder_row['fmanager_id'].'"';
                        }
                    }
                }
                
                $filemanagerSql = "SELECT * FROM tms_filemanager " . $filemanagerWhereSql;
                $dat = $this->_db->rawQuery($filemanagerSql);
                if($dat){
                    $data['data'] = $dat;
                }else{
                    $this->_db->where('parent_id',$info['parent_id']);
                    $dat = $this->_db->get('tms_filemanager');
                    $data['data'] = $dat;
                }
                $data['info'] = $info;
                $data['fname'] = $fname['name'];
                $i = 0;
                foreach($data['data'] as $data1){
                    $data['data'][$i]['categories'] = [];
                    $data['data'][$i]['countchild'] = 0;
                    if($data1['ext'] == ''){
                        $data['data'][$i]['categories'] = self::buildTree($dataArr,$data['data'][$i]['fmanager_id']);
                    } 
                    $i++;
                }
        
                return $data;
            }else{  
                $chkIfRoot = $this->_db->rawQuery("SELECT * FROM tms_filemanager where fmanager_id = $id  AND is_ex_project_folder =1");
                    if($chkIfRoot){
                        $chkAssignResource = $this->_db->rawQuery("SELECT * FROM tms_summmery_view where resource = $exteruserId");
                        $exUserPfolder = $this->_db->rawQuery("SELECT * FROM tms_filemanager where user_id = $exteruserId");
                        $pId = $exUserPfolder[0]['fmanager_id'];
                        $exUserPfolder1 = $this->_db->rawQuery("SELECT * FROM tms_filemanager where parent_id = $pId AND is_ex_project_folder = 1");
                    $filemanagerWhereSql = 'WHERE parent_id = "'.$id.'" AND is_project_folder = 1 OR parent_id = "'.$exUserPfolder1[0]['fmanager_id'].'"';
                    foreach ($chkAssignResource as $key => $value) {
                        $orderId = $value['order_id'];
                        
                        $chkIsProjectFolder = $this->_db->rawQuery("SELECT * FROM tms_filemanager where order_id = $orderId AND is_project_folder = 1");
                        
                        if($chkIsProjectFolder){
                            foreach($chkIsProjectFolder as $chkIsProjectFolder_row){
                                $filemanagerWhereSql .= ' OR fmanager_id = "'.$chkIsProjectFolder_row['fmanager_id'].'"';
                            }
                        }
                    }
                    
                    $filemanagerSql = "SELECT * FROM tms_filemanager " . $filemanagerWhereSql;
                    $dat = $this->_db->rawQuery($filemanagerSql);
                    $data['info'] = $info;
                    $data['data'] = $dat;
                    $data['fname'] = $fname['name'];
                    $i = 0;
                    foreach($data['data'] as $data1){
                        $data['data'][$i]['categories'] = [];
                        $data['data'][$i]['countchild'] = 0;
                        if($data1['ext'] == ''){
                            $data['data'][$i]['categories'] = self::buildTree($dataArr,$data['data'][$i]['fmanager_id']);
                        } 
                        $i++;
                    }
        
                    return $data;
                }else{
                    
                        $Pfolder = $this->_db->rawQuery("SELECT * FROM tms_filemanager where fmanager_id = $id && name='Projects' && is_default_folder = 1");
                        
                        if($Pfolder){
                                $getUserFid = $this->_db->rawQuery("SELECT * FROM tms_filemanager where user_id = $exteruserId");
                                if($getUserFid){
                                    $this->_db->where('parent_id',$getUserFid[0]['fmanager_id']);
                                    $dat = $this->_db->get('tms_filemanager');
                                    $data['info'] = $info;
                                    $data['data'] = $dat;
                                    $data['fname'] = $fname['name'];

                                    $i = 0;
                                    foreach($data['data'] as $data1){
                                        $data['data'][$i]['categories'] = [];
                                        $data['data'][$i]['countchild'] = 0;
                
                                        if($data1['ext'] == ''){
                                            $data['data'][$i]['categories'] = self::buildTree($dataArr,$data['data'][$i]['fmanager_id']);
                                        } 
                                        $i++;
                                    }
                                    return $data;
                                }else{
                                    $this->_db->where('parent_id',$info['parent_id']);
                                    $dat = $this->_db->get('tms_filemanager');
                                    $data['info'] = $info;
                                    $data['data'] = $dat;
                                    $data['fname'] = $fname['name'];
                                    $i = 0;
                                    foreach($data['data'] as $data1){
                                        $data['data'][$i]['categories'] = [];
                                        $data['data'][$i]['countchild'] = 0;
                                        if($data1['ext'] == ''){
                                            $data['data'][$i]['categories'] = self::buildTree($dataArr,$data['data'][$i]['fmanager_id']);
                                        } 
                                        $i++;
                                    }
                                    return $data;
                                }
                        }else{
                            $Pfolder = $this->_db->rawQuery("SELECT * FROM tms_filemanager where fmanager_id = $id && is_project_folder = 1");
                            if($Pfolder){
                                $chkAssignResource = $this->_db->rawQuery("SELECT * FROM tms_summmery_view where resource = $exteruserId");
                                    $exUserPfolder = $this->_db->rawQuery("SELECT * FROM tms_filemanager where user_id = $exteruserId");
                                    $pId = $exUserPfolder[0]['fmanager_id'];
                                    $exUserPfolder1 = $this->_db->rawQuery("SELECT * FROM tms_filemanager where parent_id = $pId AND is_ex_project_folder = 1");
                                $filemanagerWhereSql = 'WHERE parent_id = "'.$id.'" AND is_project_folder = 1 OR parent_id = "'.$exUserPfolder1[0]['fmanager_id'].'"';
                                foreach ($chkAssignResource as $key => $value) {
                                    $orderId = $value['order_id'];
                                    
                                    $chkIsProjectFolder = $this->_db->rawQuery("SELECT * FROM tms_filemanager where order_id = $orderId AND is_project_folder = 1");
                                    
                                    if($chkIsProjectFolder){
                                        foreach($chkIsProjectFolder as $chkIsProjectFolder_row){
                                            $filemanagerWhereSql .= ' OR fmanager_id = "'.$chkIsProjectFolder_row['fmanager_id'].'"';
                                        }
                                    }
                                }
                                
                                $filemanagerSql = "SELECT * FROM tms_filemanager " . $filemanagerWhereSql;
                                $dat = $this->_db->rawQuery($filemanagerSql);
                                $data['info'] = $info;
                                $data['data'] = $dat;
                                $data['fname'] = $fname['name'];

                                $i = 0;
                                foreach($data['data'] as $data1){
                                    $data['data'][$i]['categories'] = [];
                                    $data['data'][$i]['countchild'] = 0;
                                    if($data1['ext'] == ''){
                                        $data['data'][$i]['categories'] = self::buildTree($dataArr,$data['data'][$i]['fmanager_id']);
                                    } 
                                    $i++;
                                }
        
                                return $data;
                            }else{
                                $this->_db->where('parent_id',$info['parent_id']);
                                $dat = $this->_db->get('tms_filemanager');
                                $data['info'] = $info;
                                $data['data'] = $dat;
                                $data['fname'] = $fname['name'];

                                $i = 0;
                                foreach($data['data'] as $data1){
                                    $data['data'][$i]['categories'] = [];
                                    $data['data'][$i]['countchild'] = 0;
                                    if($data1['ext'] == ''){
                                        $data['data'][$i]['categories'] = self::buildTree($dataArr,$data['data'][$i]['fmanager_id']);
                                    } 
                                    $i++;
                                }
        
                                return $data;
                            }

                        }
                    
                }
            }
        }
    }
        
    public function fileManagerUpdate($id,$data){
        $path = "../../uploads/fileupload/";
        $images = glob($path.$data['image']);
        if(isset($images[0])) {
            $file = basename($images[0]);
            $ext = explode('.',$file);
            $extension = end($ext);
            $rename =  rename($images[0], $path.$data['name'].'.'.$extension);
            $data['name'] = $data['name'].'.'.$extension;
        }
        unset($data['image']);
        $data['created_date'] = date('Y-m-d H:i:s');
        $this->_db->where('fmanager_id',$id);
        $info =  $this->_db->update('tms_filemanager',$data);
        if ($info) {
            $return['status'] = 200;
            $return['msg'] = 'Successfully Rename.';
        } else {
            $return['status'] = 422;
            $return['msg'] = 'Not Rename.';
        } 
        return $return;
    }

    public function fileManagerPaste($id,$data){
        foreach ($data as $value) {
            $this->_db->where('fmanager_id',$value['id']);
            $info = $this->_db->getOne('tms_filemanager');
            unset($info['fmanager_id']);
            $fileName = "../../uploads/fileupload/".$info['name'];
            $ext = pathinfo($fileName, PATHINFO_EXTENSION);
            $fName = explode('.', strrev($info['name']), 2);
            $fileNameWithoutExt = strrev($fName[1]);
            
            $newFileName = "../../uploads/fileupload/".$fileNameWithoutExt.'_copy'.'.'.$ext;
            copy($fileName, $newFileName);
            $info['name'] = $fileNameWithoutExt.'_copy'.'.'.$ext;
            $info['parent_id'] = $value['parent'];
            $pastedData = $this->_db->insert('tms_filemanager',$info);
        }
    }

    public function filemanagerfolderDelete($id,$image){
        $path = "../../uploads/fileupload/";
        $images = glob($path.$image);
        if($images){
            if(isset($images[0])) {
                unlink($path.$image);
            }
        }
        $this->_db->where('fmanager_id',$id);
        $data = $this->_db->delete('tms_filemanager');
        if($data) {
            $info = self::filegetByparentId($id);
            foreach($info AS $key => $value) {
                if(isset($value['name']) || isset($value['ext'])) {
                    $path = "../../uploads/fileupload/";
                    $images = glob($path.$value['name']);
                    if($images){
                        if(isset($images)) {
                            unlink($path.$value['name']);
                        }
                    }
                }
                $this->_db->where('parent_id',$value['parent_id']);
                $this->_db->delete('tms_filemanager');
            }    
            
        }
        return $data;
    }
    public function filemanagerfolderDeleteMultiple($id,$data){
        $path = "../../uploads/fileupload/";
        foreach ($data as $value) {
            $this->_db->where('fmanager_id',$value['id']);
            $info = $this->_db->getOne('tms_filemanager');
            $images = glob($path.$info['name']);
            if(isset($images)) {
                unlink($path.$info['name']);
            }
            
            $this->_db->where('fmanager_id',$value['id']);
            $this->_db->delete('tms_filemanager');
        }
        
    }

    public function fileparentNameGet($id) {
        $data = self::filegetByfmanagerId($id);
        return $data;    
    }

    public function Newfoldermake($id,$name,$role) {
        $data['updated_date'] = date('Y-m-d H:i:s');
        $data['created_date'] = date('Y-m-d H:i:s');
        $data['name'] = $name;
        $data['role_id'] = $role;
        $data['parent_id'] = $id;
        $info =  $this->_db->insert('tms_filemanager',$data);
    }

    public function fileAdd($data) {
        // start - previous code
        /*$data['name'] = self::uploadimage($data);
        $checkext = explode('.', $data['name']);
        $data['ext'] = end($checkext);
        
        $data['updated_date'] = date('Y-m-d H:i:s');
        $data['created_date'] = date('Y-m-d H:i:s');        
        unset($data['filename']);
        unset($data['filetype']);
        $info =  $this->_db->insert('tms_filemanager',$data);*/
        //end previos code

        $num=0;
        if(count($data)>0){
            foreach($data as $key => $val){
                $data[$num]['updated_date'] = date('Y-m-d H:i:s');
                $data[$num]['created_date'] = date('Y-m-d H:i:s');  
                $info =  $this->_db->insert('tms_filemanager',$data[$num]);
                $num++;
            }
        }
        if ($info) {
            $return['status'] = 200;
            $return['msg'] = 'Successfully created.';
        } else {
            $return['status'] = 422;
            $return['msg'] = 'Not created folder.';
        } 
        return $return;
    }
    public function fileAddScoop($data) {
        $num=0;
        if(count($data)>0){
            foreach($data as $key => $val){
                //$data['name'] = self::uploadimage($data);
                //$newData = self::uploadimage_new2($data[$num]);
                $data[$num]['updated_date'] = date('Y-m-d H:i:s');
                $data[$num]['created_date'] = date('Y-m-d H:i:s');  
                $fileUpld =  $this->_db->insert('tms_filemanager',$data[$num]);
                $num++;
            }
        }    
        if ($fileUpld) {
            $return['status'] = 200;
            $return['msg'] = 'Successfully created.';
        } else {
            $return['status'] = 422;
            $return['msg'] = 'Not created folder.';
        } 
        return $return;
    }
    
    public function uploadimage_new2($data) {
        $my_newdata =$data; 
        $result = explode(',', $data['name']);
        $finalstring = base64_decode($result[1]);
        $ex = str_replace(' ','_',$data['filename']);
        
        $img = $data['name'];
        $img = str_replace('data:'.$data['filetype'].';base64,', '', $img);
        $img = str_replace(' ', '+', $img);
        $data = base64_decode($img);
        $filename = rand(0,1000).'_'.$ex;
        
        $output_file = UPLOADS_ROOT . "fileupload/" . $filename;
        $ifp = fopen($output_file, "wb");
        $ifw = fwrite($ifp, $finalstring);
        fclose($ifp);

        // data to insert
        $info = false;
        if($ifw){
            $data_ins['name'] = $filename;
            $checkext = explode('.', $filename);
            $data_ins['ext'] = end($checkext);
            
            $data_ins['role_id'] = $my_newdata['role_id'];
            $data_ins['parent_id'] = $my_newdata['parent_id'];
            $data_ins['size'] = $my_newdata['size'];
            $data_ins['f_id'] = $my_newdata['f_id'];
            $data_ins['updated_date'] = date('Y-m-d H:i:s');
            $data_ins['created_date'] = date('Y-m-d H:i:s');        
            unset($data_ins['filename']);
            unset($data_ins['filetype']);
            
            $info =  $this->_db->insert('tms_filemanager',$data_ins);
            
        }    
        // end data
        return $info;
        //return $filename;
    }


    public function filefrontget($id) {
        $this->_db->where('order_id',$id);
        $this->_db->where('parent_id',0);
        $data = $this->_db->getOne('tms_filemanager');
        return $data;
    }

    public function filefrontroot($id,$root) {
        $this->_db->where('order_id',$id);
        $this->_db->where('name','$'.$root);
        $data = $this->_db->getOne('tms_filemanager');
        return $data;   
    }

    public function jobfilefrontroot($orderid,$jobid,$root) {
        if(isset($root)) {
            if($root == 'source') {
                $root = '_in';
            } else if($root == 'target') {
                $root = '_out';
            } else {
                $root = ' ';
            }
        }
        $data = $this->_db->rawQuery("SELECT * FROM tms_filemanager t1 INNER JOIN tms_filemanager t2 ON t1.fmanager_id = t2.parent_id WHERE t1.order_id = '".$orderid."' AND t1.job_id = '".$jobid."' AND t2.name = '".$root."'");
        return $data;
    }

    public function jobfileuserfrontroot($jobid,$root) {
        if(isset($root)) {
            if($root == 'source') {
                $root = '_in';
            } else if($root == 'target') {
                $root = '_out';
            } else {
                $root = ' ';
            }
        }
        $data = $this->_db->rawQuery("SELECT * FROM tms_filemanager t1 INNER JOIN tms_filemanager t2 ON t1.fmanager_id = t2.parent_id WHERE  t1.job_id = '".$jobid."' AND t2.name = '".$root."'");
        return $data;
    }

    public function jobfileuserrootSt($stid,$root) {
        $data = self::filegetByjobId($stid);
        return $data;
    }

    public function Clientfilefront($client,$status) {
        if($status == 'direct') {
            $this->_db->where('client_id',$client);
            $this->_db->where('parent_id',0);
            $data = $this->_db->getOne('tms_filemanager');    
        } else {
            $id = self::fileCustomeget('Indirect client', 'name');
            $this->_db->where('in_client_id',$client);
            $this->_db->where('parent_id',$id['fmanager_id']);
            $data = $this->_db->getOne('tms_filemanager');
            if(!$data){
                $this->_db->where('in_client_id',$client);
                $this->_db->where('parent_id',0);
                $data = $this->_db->getOne('tms_filemanager');
            }
            //echo '<pre>'; print_r($data); echo '</pre>';exit;
        }
        return $data;
    }

    public function Itemfilefront($orderId,$itemId) {
        $this->_db->where('order_id', $orderId);
        $this->_db->where('item_id', $itemId);
        $data = $this->_db->getOne('tms_filemanager');
        return $data;
    }

    public function Userfilefront($user) {
        $this->_db->where('user_id',$user);
        $this->_db->where('parent_id',0);
        $data = $this->_db->getOne('tms_filemanager');
        return $data;
    }

    public function filefolderstget($id,$root) {
        $data = $this->_db->rawQuery("SELECT * FROM tms_filemanager AS t1 WHERE t1.parent_id = '".$id."'");
        if($data) {
            foreach ($data as $key => $value) {
                if($value['name'] == '_in') {
                    $source = self::filegetByparentId($value['fmanager_id']);
                } else {
                    $target = self::filegetByparentId($value['fmanager_id']);
                }   
            }
            $info['source'] = count($source);
            $info['target'] = count($target);
        }
        return $info;
    }

    public function filemanagerApproveSend($aid,$rid) {
        //update approved status
        $info['appr_id'] = 1;
        $info['updated_date'] = date('Y-m-d H:i:s');
        $this->_db->where('job_summmeryId',$aid);
        $this->_db->update('tms_summmery_view',$info);
        $ajob = self::filegetByjobId($aid);
        if($ajob) {
            //find file in file manager
            if(isset($ajob['fmanager_id'])) {
                $a_out = self::filegetByparentId($ajob['fmanager_id']);
                if(isset($a_out[0]['fmanager_id'])) {
                    $data = self::filegetByparentId($a_out[0]['fmanager_id']);
                    $rjob = self::filegetByjobId($rid);
                    if($rjob) {
                        if(isset($rjob['fmanager_id'])) {
                            $r_out = self::filegetByparentId($rjob['fmanager_id']);
                            if(isset($r_out[1]['fmanager_id'])) {
                                $rOut = $r_out[1]['fmanager_id'];
                            }
                        }
                    }
                    foreach ($data as $key => $value) {
                        //copy same directory in file
                        $ext = explode('.',$value['name']);
                        $extension = end($ext);
                        $file = chop($value['name'],'.'.$extension);
                        $path = "../../uploads/fileupload/";
                        $fileName = $path.$value['name'];
                        $newfile = $path.$file.'_thumb.'.$extension;
                        $nfile = $file.'_thumb.'.$extension;
                        if (!copy($fileName, $newfile)) {
                            echo "failed to copy $file...\n";
                        }
                        //approved to send request
                        if($value) {
                            unset($value['fmanager_id']);
                            $value['parent_id'] = $rOut;
                            $value['name'] = $nfile;
                            $value['updated_date'] = date('Y-m-d H:i:s');
                            $value['created_date'] = date('Y-m-d H:i:s');
                            $info = $this->_db->insert('tms_filemanager',$value);
                        }
                        
                    }
                }
            }
        }
        return $info;
    }

    public function uploadimage($data) {
        $result = explode(',', $data['name']);
        $finalstring = base64_decode($result[1]);
        $ex = str_replace(' ','_',$data['filename']);
        
        $img = $data['name'];
        $img = str_replace('data:'.$data['filetype'].';base64,', '', $img);
        $img = str_replace(' ', '+', $img);
        $data = base64_decode($img);
        $filename = rand(0,1000).'_'.$ex;
        $output_file = UPLOADS_ROOT . "fileupload/" . $filename;
        $ifp = fopen($output_file, "wb");
        fwrite($ifp, $finalstring);
        fclose($ifp);
        return $filename;
    }

    public static function getBytesFromHexString($hexdata) {
        for ($count = 0; $count < strlen($hexdata); $count+=2)
            $bytes[] = chr(hexdec(substr($hexdata, $count, 2)));
        return implode($bytes);
    }

   public static function getImageMimeType($imagedata,$FileType='') {
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
    public function isParent($id,$data) {
        $this->_db->where('fmanager_id',$id);
        $data = $this->_db->getOne('tms_filemanager');
        return $data;
    }
    public function getRootFolder($id,$data) {
        $split = explode('_',$id);
        $id = $split[0];
        
        $this->_db->where('fmanager_id',$id);
        $data = $this->_db->getOne('tms_filemanager');
        
        return $data;
    }
    public function checkRootFromFiles($id,$externalResourceUserId) {
        if($externalResourceUserId !='null'){
            $chkForProjectFolder = $this->_db->rawQuery("SELECT * FROM tms_filemanager where fmanager_id = $id AND  name= 'Projects'");
            if($chkForProjectFolder){
                $getResourceFileManager = $this->_db->rawQuery("SELECT * FROM tms_filemanager where user_id = $externalResourceUserId AND  parent_id= 0");    
                if($getResourceFileManager){
                    return $getResourceFileManager;
                }
            }else{
                return $return['status'] = 404;
            }
        }
    }

    public function checkDefaultFolderProjectExternal($id) {
        $chkForProjectFolder = $this->_db->rawQuery("SELECT * FROM tms_filemanager where fmanager_id = $id AND  name= 'Projects'");
        if($chkForProjectFolder){
           return $return['status'] = 200;
        }else{
            return $return['status'] = 404;
        }
    }

    public function filemanagerfolderDownload($id){
        $this->treeArr_data = array();
        $this->_db->where('parent_id',$id);
        $data = $this->_db->get('tms_filemanager');    
        $dataArr = $this->_db->get('tms_filemanager');    
        $i = 0;
        $data['countchild']=0;
        $data['childfile'] = [];   
        $this->treeArr_data = self::buildTreePath($dataArr,$id);
        $data = self::buildTreearraydata_new($dataArr,$id,$dataArr,$id);
                
        foreach($data as $data1){
            $data[$i]['countchild'] = 0;
            $data[$i]['childfile'] = [];
            if($data1['ext'] == ''){
                //$data[$i]['childfile'] = self::buildTreearraydata($dataArr,$data[$i]['fmanager_id']);
                //$data[$i]['childfile'] = self::buildTreearraydata_new($dataArr,$data[$i]['fmanager_id'],$dataArr,$data[$i]['fmanager_id']);
                //echo "<pre>";
                //print_r($data);
            }

            $i++;
        }
        return $data;
    }

}
