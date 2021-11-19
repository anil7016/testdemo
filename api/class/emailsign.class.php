<?php

require_once 'users.class.php';
require_once 'client.class.php';
require_once 'tasktype.class.php';

class emailsign {
	//price1 data manage
	    public function __construct() {
	        $this->_db = db::getInstance();	        
	    }

	    public function emailSignsave($data){
	    	if(isset($data['sign_picture'])){
                    $data['sign_image'] = $data['sign_picture'];
                    $info = $this->uploadimage($data['sign_picture']);
                    $a = explode("/",$info);
                    $data['sign_picture'] = end($a);
	    	}	    	

	    	$data['created'] = date('Y-m-d H:i:s');
    		$data['updated'] = date('Y-m-d H:i:s');
    		$id = $this->_db->insert('tms_email_sign', $data);

    		if ($id) {
                $return['status'] = 200;
    		    $return['msg'] = 'Successfully Inserted.';
    		} else {
    		    $return['status'] = 422;
    		    $return['msg'] = 'Not Saved.';
    		}
    		return $return;
	    }
	    
	    public function emailSignget() {
	    	return $data = $this->_db->get('tms_email_sign');
	    }
	    
	    public function emailSigngetone($id) {
	    	$this->_db->where('sign_id',$id);
	    	return $data = $this->_db->getone('tms_email_sign');
	    }
	    
	    public function emailSignupdate($id,$data) {
	    	if(isset($id)){
	    		$this->_db->where('sign_id',$id);
	    		$in = $this->_db->getOne('tms_email_sign');
	    	}
	    	//$data['created'] = date('Y-m-d H:i:s');
	    	$data['updated'] = date('Y-m-d H:i:s');
	    	if(isset($data['sign_picture'])){
	    			$image = $in['sign_picture'];
		    		$path = "../../uploads/attatchment/";
		    		$images = glob($path.$image);
		    		if($images){
		    			$data['sign_image'] = $data['sign_picture'];
		    			unlink($path.$image);
		    		}else{
		    			$image = $in['sign_picture'];
		    		}
		    			
                    $info = $this->uploadimage($data['sign_picture']);
                    $a = explode("/",$info);
                    $data['sign_picture'] = end($a);
	    	}
	    	$this->_db->where('sign_id',$id);
	    	$id = $this->_db->update('tms_email_sign', $data);
	    	if ($id) {
	    		$return['status'] = 200;
	    		$return['msg'] = 'Successfully Updated.';
	    	} else {
	    		$return['status'] = 422;
	    		$return['msg'] = 'Not Updated.';
	    	}
	    	return $return;
	    }

	    public function deleteSign($id,$image) {
	    	// $return['status'] = 422;
      //       $return['msg'] = 'Name already exists.'; 
	    	$this->_db->where('sign_id',$id);
	    	$row = $this->_db->getOne('tms_email_sign');
	    	if($row['is_active'] == 1){
	    		$return['status'] = 422;
            	$return['msg'] = 'You can not delete active record.'; 
	    	}
	    	else{
	    		$path = "../../uploads/attatchment/";
		    	$images = glob($path.$image);
		    	if($images){
		    		unlink($path.$image);
		    	}
		    	$this->_db->where('sign_id',$id);
		    	$data = $this->_db->delete('tms_email_sign');
		    	$return['status'] = 200;
            	$return['msg'] = 'Successfully detelted';
		    }
		    return $return;
	    	
	    }
	        
	    public function uploadimage($data) {
	    	$result = explode(',', $data);
	    	$finalstring = base64_decode($result[1]);
	    	$mimetype = self::getImageMimeType($finalstring);
	    	if($mimetype == 'jpeg'){
	            $mimetype = 'jpg';
	        }
	    	$filename = time() . "." . $mimetype;
	    	$output_file = UPLOADS_ROOT . "attatchment/" . $filename;
	    	$ifp = fopen($output_file, "wb");
	    	fwrite($ifp, $finalstring);
	    	fclose($ifp);
	    	return $output_file;
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

	    public function emailSigngetdata(){
	    	$this->_db->where('is_active',1);
	    	return $data = $this->_db->get('tms_email_sign');
	    }
            
        public function emailSignCheck(){
            $this->_db->where('is_active',1);
    		$data = $this->_db->getone('tms_email_sign');
            return $data['is_active'];
        }

        public function emailTemplatePlaceHolder($id) {
        	/*switch ($id) {
			    case '1':
			        $data = array(
			        			'1'=>'[PROJECTNUMBER]',
			        			'2'=>'[ORDERDATE]',
			        			'3'=>'[CLIENTNAME]'
			        		);
			        return $data;
			        break;
			    case "2":
			        $data = array(
			        			'1'=>'[INVOICENUMBER]',
			        			'2'=>'[INVOICEDATE]',
			        			'3'=>'[FREELANCERNAME]'
			        		);
			        return $data;
			        break;
			    case "3":
			        $data = array(
			        			'1'=>'[USERNAME]',
			        			'2'=>'[USEREMAIL]',
			        			'3'=>'[USERPASSWORD]'
			        		);
			        return $data;
			        break;
			    default:
			        echo "default";
			}*/
			switch ($id) {
			    case '1':
			        $data = array(
			        			'1'=>'[JOBNO]',
			        			'2'=>'[LANGUAGES]',
			        			'3'=>'[RESOURCENAME]',
			        			'4'=>'[JOBREQUESTCOMMENT]'
			        		);
			        return $data;
			        break;
			    case "2":
			        $data = array(
			        			'1'=>'[JOBNO]',
			        			'2'=>'[PROJECTNO]',
			        			'3'=>'[LANGUAGES]',
			        			'4'=>'[DEADLINE]',
			        			'5'=>'[REJECTREASON]'
			        		);
			        return $data;
			        break;
			    case "3":
			        $data = array(
			        			'1'=>'[USERNAME]',
			        			'2'=>'[USEREMAIL]',
			        			'3'=>'[USERPASSWORD]'
			        		);
			        return $data;
			        break;
			    case "4":
			        $data = array(
			        			'1'=>'[USERNAME]',
			        			'2'=>'[USEREMAIL]',
			        			'3'=>'[USERPASSWORD]',
			        			'4'=>'[REGISTRATIONLINK]',
			        			'5'=>'[BUSINESSMANAGERLINK]'
			        		);
			        return $data;
			        break;
			    case "5":
			        $data = array(
			        			'1'=>'[JOBNO]',
			        			'2'=>'[NAMEJOBRECEIVER]',
			        			'3'=>'[CUSTOMERNAME]',
			        			'4'=>'[PROJECTNAME]',
			        			'5'=>'[JOBSERVICE]',
			        			'5'=>'[DEADLINE]'
			        		);
			        return $data;
			        break;
			     case "6":
			        $data = array(
			        			'1'=>'[JOBNO]',
			        			'2'=>'[JOBDESCRIPTION]',
			        			'3'=>'[DELIVERYNOTE]'
			        		);
			        return $data;
			        break;
			    default:
			        return null;
			}
			exit;
	        
	  	}
        public function saveEmailTemplate($data){
        	$data['created_date'] = date('Y-m-d H:i:s');
            $data['updated_date'] = date('Y-m-d H:i:s');
            
            $id = $this->_db->insert('tms_email_templates', $data);

            if ($id) {
                $return['status'] = 200;
                $return['msg'] = 'Successfully Inserted.';
            } else {
                $return['status'] = 422;
                $return['msg'] = 'Not Saved.';
            }
            return $return;
        }
        public function emailTemplateGetOne($id){
        	$this->_db->where('template_id',$id);
	    	return $data = $this->_db->getone('tms_email_templates');
        }

        public function emailTemplateGetAll(){
        	$this->_db->orderBy('template_id','DESC');
	    	$data = $this->_db->get('tms_email_templates');
	    	return $data; 
	    }

	    public function emailTemplateUpdate($id,$data){
	    	$this->_db->where('template_id',$id);
	    	$data['updated_date'] = date('Y-m-d H:i:s');
	    	$id = $this->_db->update('tms_email_templates', $data);
	    	if ($id) {
	    		$return['status'] = 200;
	    		$return['msg'] = 'Successfully Updated.';
	    	} else {
	    		$return['status'] = 422;
	    		$return['msg'] = 'Not Updated.';
	    	}
	    	return $return;
	    }

	    public function deleteEmailTemplate($id){
	    	$this->_db->where('template_id',$id);
	    	$this->_db->where('is_active',0);
	    	$id = $this->_db->delete('tms_email_templates');
	    	if ($id) {
	            $return['status'] = 200;
	            $return['msg'] = 'Delete Successfully.';
	        } else {
	            $return['status'] = 422;
	            $return['msg'] = 'you can not delete active template.';
	        }
	    	return $return;
	    }
}
?>