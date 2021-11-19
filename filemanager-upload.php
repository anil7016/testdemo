<?php

//to get size of file
function formatSizeUnits($bytes)
    {
        if ($bytes >= 1073741824)
        {
            $bytes = number_format($bytes / 1073741824, 2) . ' GB';
        }
        elseif ($bytes >= 1048576)
        {
            $bytes = number_format($bytes / 1048576, 2) . ' MB';
        }
        elseif ($bytes >= 1024)
        {
            $bytes = number_format($bytes / 1024, 2) . ' KB';
        }
        elseif ($bytes > 1)
        {
            $bytes = $bytes . ' bytes';
        }
        elseif ($bytes == 1)
        {
            $bytes = $bytes . ' byte';
        }
        else
        {
            $bytes = '0 bytes';
        }

        return $bytes;
}

//If directory doesnot exists create it.
$output_dir = "uploads/fileupload/";

if(isset($_FILES["myfile"]))
{
	$ret = array();
	$error =$_FILES["myfile"]["error"];
   {
    	if(!is_array($_FILES["myfile"]['name'])) //single file
    	{
          $ex = str_replace(' ','_',$_FILES["myfile"]["name"]);
          $filename = rand(0,1000).'_'.$ex;
       	 	move_uploaded_file($_FILES["myfile"]["tmp_name"],$output_dir. $filename);
       	 	 //echo "<br> Error: ".$_FILES["myfile"]["error"];

	       	 	//$ret[$fileName]= $output_dir.$fileName;
            $checkext = explode('.', $filename);
            $ret['ext'] = end($checkext);
            $size = $_FILES['myfile']['size'];
            $ret['size'] = formatSizeUnits($size);
            $ret['name']= $filename;
    	}
    	else
    	{
    	    $fileCount = count($_FILES["myfile"]['name']);
    		  for($i=0; $i < $fileCount; $i++)
    		  {
    		  	//$fileName = $_FILES["myfile"]["name"][$i];
	       	 	//$ret[$fileName]= $output_dir.$fileName;
    		    $ex = str_replace(' ','_',$_FILES["myfile"]["name"][$i]);
            $filename = rand(0,1000).'_'.$ex;
          
            move_uploaded_file($_FILES["myfile"]["tmp_name"][$i],$output_dir.$filename );

            $checkext = explode('.', $filename);
            
            $ret['ext'][$i] = end($checkext);
            $size = $_FILES['myfile']['size'][$i];
            $ret['size'][$i] = formatSizeUnits($size);
            $ret['name'][$i] = $filename;

    		  }
    	}
    }
    echo json_encode($ret);
}

?>