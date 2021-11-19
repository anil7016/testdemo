<?php

require_once 'functions.class.php';
require_once 'sendmail/class.phpmailer.php';

class message {
    
    public function __construct() {
        $this->_db = db::getInstance();
        $this->_status = new userstatus ();
    }

    public function send_email($id, $data) {
        if (isset($data['data']['messageData'])) {
            $this->_db->where('is_active', 1);
            $emailSign = $this->_db->getone('tms_email_sign');   
            $emailsignData = $emailSign['sign_detail'];
            $emailImageData = UPLOADS_ROOT . "attatchment/" . $emailSign['sign_picture'];
        } else {
            $emailsignData = " ";
            $emailImageData = " ";
        }
        if(isset($data['file'])) {
            //echo 'a';exit;
            $encoded_content = $this->uploadimage($data['file']);    
        } else {
            //echo 'b';exit;
            $encoded_content = '';    
        }
        //exit;
        
        if (isset($data['data']['cc'])) {
            $cc = $data['data']['cc'];
        } else {
            $cc = "";
        }
        if (isset($data['data']['bcc'])) {
            $bcc = $data['data']['bcc'];
        } else {
            $bcc = "";
        }
        if (isset($data['data']['messageData'])) {
            $str = $data['data']['messageData'];
            $message = str_replace($emailsignData,"",$str);
        } else {
            $message = " ";
        }
        
        $body = "<p>" . $message . "</p>";
        $body .= "<p>" . $emailsignData . "</p>";
        $body .= "<p><img src='cid:logo_2u' width='80px'></p>";
        $subject = "Information";
        $Username = $data['data']['vUserName'];
        $to = $data['data']['vEmailAddress'];
        $from = $data['data']['vEmailAddress'];
        $this->_mailer = new PHPMailer();
        
        $this->_mailer->IsSMTP();
        $this->_mailer->Host = "ssl://smtp.gmail.com";
        $this->_mailer->SMTPAuth = "true";
        $this->_mailer->Port = "465";
        $this->_mailer->Username = "ChrisGilesItWorks@gmail.com";
        $this->_mailer->Password = "Admin@Kanhasoft@123";

        $this->_mailer->From = "Kanhasoft.com";
        $this->_mailer->FromName = "Kanhasoft";
        $this->_mailer->Subject = $subject;
        $this->_mailer->Body = $body;
        $this->_mailer->WordWrap = 50;
        $this->_mailer->AddAddress($to);
        //$this->_mailer->AddEmbeddedImage($emailImageData, 'logo_2u');
        $this->_mailer->IsHTML(true);
        //
        if ($cc != "") {
            $cCAddresses = explode(',',$cc);
            foreach ($cCAddresses as $cCAddress) {
                $this->_mailer->AddCC(trim($cCAddress));
            }
        }

        if ($bcc != "") {
            $bcCAddresses = explode(',',$bcc);
            foreach ($bcCAddresses as $bcCAddress) {
                $this->_mailer->AddBCC(trim($bcCAddress));
            }
        }

        if ($encoded_content != '') {
            $this->_mailer->AddAttachment($encoded_content);
        }
        if ($this->_mailer->Send()) { //output success or failure messages
            $result['status'] = 200;
            $result['msg'] = 'Thank you for your email';
        } else {
            $result['status'] = 400;
            $result['msg'] = 'Could not send mail!';
        }
        /*if(isset($result['status'])) {
            if(isset($emailImageData)) {
                $img = explode("/",$emailImageData);
                $image = end($img);
                if($img[0] != " ") {
                    $path = "../../uploads/attatchment/";
                    $images = glob($path.$image);
                    // if($images){
                    //     unlink($path.$image);
                    // }    
                }    
            }
            if(isset($encoded_content)) {
                $img = explode("/",$encoded_content);
                $image = end($img);
                if($img[0] != " ") {
                    $path = "../../uploads/attatchment/";
                    $images = glob($path.$image);
                    if($images){
                        unlink($path.$image);
                    }    
                }       
            }
        }*/
        return $result;
    }

    public function sendClientIndirectMessage($id, $data) {
        if (isset($data['data']['emailSignId'])) {
            $emailsignId = $data['data']['emailSignId'];
            $this->_db->where('sign_id', $emailsignId);
            $emailSign = $this->_db->getone('tms_email_sign');   
            $emailsignData = $emailSign['sign_detail'];
            $emailImageData = UPLOADS_ROOT . "attatchment/" . $emailSign['sign_picture'];
        } else {
            $emailsignData = " ";
            $emailImageData = " ";
        }
        $encoded_content = $this->uploadimage($data['file']);
        if (isset($data['data']['cc'])) {
            $cc = $data['data']['cc'];
        } else {
            $cc = " ";
        }
        if (isset($data['data']['bcc'])) {
            $bcc = $data['data']['bcc'];
        } else {
            $bcc = " ";
        }
        if (isset($data['data']['messageData'])) {
            $str = $data['data']['messageData'];
            $message = str_replace('&nbsp '.$emailsignData,"",$str);
            
        } else {
            $message = " ";
        }

        $body = "<p>" . $message . "</p>";
        $body .= "<p>" . $emailsignData . "</br><img src='cid:logo_2u'></p>";
        $subject = "Information";
        $Username = $data['data']['vUserName'];

        $to = $data['data']['vEmailAddress'];
        //$from = $data['data']['vEmailAddress']
        $this->_mailer = new PHPMailer();
        //        $this->_mailer = 'ISO-8859-1';
        $this->_mailer->IsSMTP();
        $this->_mailer->Host = "ssl://smtp.gmail.com";
        $this->_mailer->SMTPAuth = "true";
        $this->_mailer->Port = "465";
        $this->_mailer->Username = "ChrisGilesItWorks@gmail.com";
        $this->_mailer->Password = "Admin@Kanhasoft@123";

        $this->_mailer->From = "Kanhasoft.com";
        $this->_mailer->FromName = "Kanhasoft";

        $this->_mailer->Subject = $subject;

        $this->_mailer->Body = $body . $this->_mailer->AddEmbeddedImage($emailImageData, 'logo_2u');
        $this->_mailer->WordWrap = 50;
        $this->_mailer->AddAddress($to);

        $this->_mailer->IsHTML(true);
        
        if ($cc != "") {
            $cCAddresses = explode(',',$cc);
            foreach ($cCAddresses as $cCAddress) {
                $this->_mailer->AddCC(trim($cCAddress));
            }
        }

        if ($bcc != "") {
            $bcCAddresses = explode(',',$bcc);
            foreach ($bcCAddresses as $bcCAddress) {
                $this->_mailer->AddBCC(trim($bcCAddress));
            }
        }

        if ($encoded_content != '') {
            $this->_mailer->AddAttachment($encoded_content);
        }
        ;
        if ($this->_mailer->Send()) { //output success or failure messages
            $result['status'] = 200;
            $result['msg'] = 'Thank you for your email';
        } else {
            $result['status'] = 200;
            $result['msg'] = 'Could not send mail!';
        }
        if(isset($result['status'])) {
            if(isset($emailImageData)) {
                $img = explode("/",$emailImageData);
                $image = end($img);
                if($img[0] != " ") {
                    $path = "../../uploads/attatchment/";
                    $images = glob($path.$image);
                    if($images){
                        //unlink($path.$image);
                    }    
                }    
            }
            if(isset($encoded_content)) {
                $img = explode("/",$encoded_content);
                $image = end($img);
                if($img[0] != " ") {
                    $path = "../../uploads/attatchment/";
                    $images = glob($path.$image);
                    if($images){
                        unlink($path.$image);
                    }    
                }       
            }
        }
        return $result;
    }

    public function uploadimage($data) {
        $result = explode(',', $data);

        $getFileType = explode(';',explode(':',$result[0])[1]);
        
        $finalstring = base64_decode($result[1]);

        $mimetype = self::getImageMimeType($finalstring,$getFileType[0]);
        if($mimetype == 'jpeg'){
            $mimetype = 'jpg';
        }
        $filename = time() . "." . $mimetype;
        $output_file = UPLOADS_ROOT . "attatchment/" . $filename;
        if($getFileType[0]=='application/vnd.openxmlformats-officedocument.wordprocessingml.document'){
            $output_file = $output_file.'docx';   
        }
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

    public static function urlsafe_b64decode($string) {
    $data = str_replace(array('-','_'),array('+','/'),$string);
    $mod4 = strlen($data) % 4;
    if ($mod4) {
        $data .= substr('====', $mod4);
    }
    return base64_decode($data);
}

}
?>