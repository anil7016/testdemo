<?php
class Funnypic {
	//price1 data manage
    public function __construct() {
        $this->_db = db::getInstance();   
    }
    
    public function getPicLikeByid($field, $id, $table) {
        $this->_db->where($field, $id);
        return $this->_db->getOne($table);
    }
    
    public function pictureSave($data) {
        $data['funny_pic'] = $this->uploadimage($data['funny_pic']);
        //$data['created_date'] = date('Y-m-d');
        $data['modified_date'] = date('Y-m-d');
        $id = $this->_db->insert('tms_knowledge_pic', $data);
        if($id) {
            $result['status'] = 200;
            $result['msg'] = "Successfully inserted";
        } else {
            $result['status'] = 401;
            $result['msg'] = "Not inserted";
        }
    }

    public function pictureGet($id, $limit) {
        $qry = "select tu.vUserName As uploadedUserName,tkpl.*, tkp.*,tkp.pic_id AS picID,tkp.created_date AS UploadedDate,tkpc.*, tu.vUserName FROM `tms_knowledge_pic` tkp LEFT JOIN tms_know_pic_like tkpl ON tkpl.`pic_id` = tkp.pic_id LEFT JOIN (SELECT COUNT(pic_id) AS NumberOfComment, pic_id FROM `tms_know_pic_comment` GROUP BY pic_id) tkpc ON tkpc.pic_id = tkp.pic_id INNER JOIN tms_users tu ON tkp.`user_id` = tu.iUserId where tkp.status = '1' AND  tu.eUserStatus = '3'  GROUP BY tkp.pic_id ORDER BY tkp.pic_id DESC LIMIT 0,$limit";
        $data = $this->_db->rawQuery($qry);
        
        foreach ($data as $key => &$value) {
            $uid = $id;
            $pickId = $value['picID'];
            $this->_db->where('userId',$uid);
            $this->_db->where('pic_id',$pickId);
            $userLikePick = $this->_db->getOne('tms_know_pic_like');
            if($userLikePick){
                $value['userLikeStatus'] = 'true';    
            }else{
                $value['userLikeStatus'] = 'false';    
            }
        }

        return $data;
    }

    //picture like
    public function pictureLikeSave($data) {
        $data['created_date'] = date('Y-m-d');
        $data['modified_date'] = date('Y-m-d');
        $info['flag_like'] = $data['pic_flage_like'];
        
        $this->_db->where('pic_id', $data['pic_id']);
        $tmsKnowledgePic = $this->_db->update('tms_knowledge_pic', $info);
        unset($data['pic_flage_like']);
        
        if($data){
            $this->_db->where('userId', $data['userId']);
            $this->_db->where('pic_id', $data['pic_id']);
            $userLikeExists = $this->_db->getOne('tms_know_pic_like');
            if(!$userLikeExists){
                $data['pic_like'] = 1;
                $id = $this->_db->insert('tms_know_pic_like', $data);
            }else{
                
                $this->_db->where('userId', $data['userId']);
                $this->_db->where('pic_id', $data['pic_id']);
                $id = $this->_db->delete('tms_know_pic_like');
                
                $info['flag_like'] = $info['flag_like'] - 2;
                $this->_db->where('pic_id', $data['pic_id']);
                $this->_db->update('tms_knowledge_pic', $info);                
            }
        }

        // //pic_like
        // if(isset($data['pic_like'])) {
        //     $id = $this->_db->insert('tms_know_pic_like', $data);
            
        // } else {

        //     $in['pic_like'] = 0;
        //     $in['modified_date'] = date('Y-m-d');
        //     $this->_db->where('pic_id', $data['pic_id']);
        //     $id = $this->_db->delete('tms_know_pic_like');
        // }

        if($id) {
            $result['status'] = 200;
            $result['msg'] = "Successfully Inserted";
        } else {
            $result['status'] = 401;
            $result['msg'] = "Not Inserted";
        }
    }
    
    //picture comment
    public function picCommentSave($data) {
        //$data['created_date'] = date('Y-m-d h:i:s');
        $data['modified_date'] = date('Y-m-d h:i:s');
        $info['flag_comment'] = $data['flag_comment'];
        $this->_db->where('pic_id', $data['pic_id']);
        $this->_db->update('tms_knowledge_pic', $info);
        unset($data['flag_comment']);
        //pic_comment
        if(isset($data['comment'])) {
            $id = $this->_db->insert('tms_know_pic_comment', $data);
        } else {
            // $this->_db->where('pic_id', $data['pic_id']);
            // $this->_db->delete('tms_know_pic_like');
        }
    }

    //chkUserLike
    public function chkUserLike($pickId, $userId) {
        // /echo $pickId.'->'.$userId;
        $this->_db->where('pic_id',$pickId);
        $this->_db->where('userId',$userId);

        $rowFound = $this->_db->getOne('tms_know_pic_like');
        if($rowFound){
            $result['status'] = 200;
            $result['msg'] = "Row Found";
        }
        else{
            $result['status'] =401;
            $result['msg'] = "Row Not Found";
        }
        return $result;
    }
    
    //pictute comment getOne
    public function pictureComentEdit($id) {
        $this->_db->where('pic_id', $id);
        return $this->_db->get('tms_know_pic_comment');
    }
    
    public function picCommentUpdate($data) {
        $id = $data['id'];
        unset($data['id'], $data['flag_comment']);
        $data['modified_date'] = date('Y-m-d');
        $this->_db->where('comment_id', $id);
        $id = $this->_db->update('tms_know_pic_comment', $data);
        if($id) {
            $result['status'] = 200;
            $result['msg'] = "Successfully Updated";
        } else {
            $result['status'] = 401;
            $result['msg'] = "Not Updated";
        }
        return $result;
    }
    
    //base64 image decode
    public function uploadimage($data) {
        $result = explode(',', $data);
        
        $getFileType = explode(';',explode(':',$result[0])[1]);
        
        $finalstring = base64_decode($result[1]);

        $mimetype = self::getImageMimeType($finalstring,$getFileType[0]);
        
        $filename = time() . "." . $mimetype;
        $output_file = UPLOADS_ROOT . $filename;
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

    public function reportsave($data) {
        $data['created_date'] = date('Y-m-d h:i:s');
        $id = $this->_db->insert('tms_report_image', $data);
        if($id) {
            $result['status'] = 200;
            $result['msg'] = "Successfully Updated";
        } else {
            $result['status'] = 401;
            $result['msg'] = "Not Updated";
        }
        return $result;
    }
}