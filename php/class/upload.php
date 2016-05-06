<?php

    $ds = DIRECTORY_SEPARATOR;
    $storeFolder = '../images/uploads/';
 
    if (!empty($_FILES)) {
     
        $tempFile = $_FILES['file']['tmp_name'];            
        $targetFile =  $storeFolder. $_FILES['file']['name'];
        if(move_uploaded_file($tempFile,$targetFile)){
            //Insert file information into db table
        }
     
    }

?>  