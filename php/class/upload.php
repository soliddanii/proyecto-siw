<?php
   
class Upload {

    /*
    *   Return:
    *       0 : No hay imagenes o las imagenes se han guardado correctamente
    *       4 : El formato de alguna de las imagenes no es valido
    *       5 : El tamaño de la imagen es mayor que el permitido
    */
    public function processUploads($idAnuncio){
    
        $error = '0';
        $imageInfo = array();
        
        if (!empty($_FILES)) {
            $ds = DIRECTORY_SEPARATOR;
            $storeFolder = dirname(dirname(dirname( __FILE__ ))).$ds.'images'.$ds.'uploads'.$ds.'anuncio-'.$idAnuncio;
            
            //Numero de archivos
            $file_count = count($_FILES['file']['name']);
 
            // Loop through each file
            for($i=0; $i<$file_count; $i++) {
                    
                //Check file size (MAX 10MB)
                if ($_FILES["file"]["size"][$i] > 10000000) {
                    $error = '5';
                    continue;
                }
                
                //Check the extension
                $ext = pathinfo($_FILES['file']['name'][$i], PATHINFO_EXTENSION);
                $allowed =  array('gif','png','jpg','jpeg');
                if(!in_array($ext,$allowed) ) {
                    $error = '4';
                    continue;
                }
                
                //Crear el directorio para cada imagen si no existe
                if (!file_exists($storeFolder)) {
                    mkdir($storeFolder, 0777, true);
                }
                    
                $pathI1 = $this->resizeAndSave(160, 220, $storeFolder, 'small', $ext, $i);
                $pathI2 = $this->resizeAndSave(480, 640, $storeFolder, 'medium', $ext, $i);
                $pathI3 = $this->resizeAndSave(720, 1280, $storeFolder, 'big', $ext, $i);
                
                $temp = array($i,$pathI1,$pathI2,$pathI3);
                array_push($imageInfo, $temp);
                
            }             
        }
        
        //Prepare the return informacion (error info + image path info)
        $returnArray = array('error' => $error, 'info' => $imageInfo);
        return $returnArray;
    }
    
    /*
    * Redimensiona una imagen
    * @param int $width
    * @param int $height
    * @param string $path: directorio donde guardar la imagen
    * @param string $name: big, small, medium...
    * @param int $idx: indice de la imagen
    */
    function resizeAndSave($height, $width, $path, $name, $ext, $idx){
        
        $ds = DIRECTORY_SEPARATOR;
        
        // Get original image x y
        list($w, $h) = getimagesize($_FILES['file']['tmp_name'][$idx]);
  
        // Calculate new image size with ratio 
        $ratio = max($width/$w, $height/$h);
        $h = ceil($height / $ratio);
        $x = ($w - $width / $ratio) / 2;
        $w = ceil($width / $ratio);
   
        // New File Path [ ejemplo: path/anuncio-x/i_big.jpg ]
        $newFilePath = $path.$ds.$idx.'_'.$name.'.'.$ext; 

        /* read binary data from image file */
        $imgString = file_get_contents($_FILES['file']['tmp_name'][$idx]);
        
        /* create image from string */
        $image = imagecreatefromstring($imgString);
        $tmp = imagecreatetruecolor($width, $height);
        imagecopyresampled($tmp, $image, 0, 0, $x, 0, $width, $height, $w, $h);
        
        /* Save image */
        switch ($_FILES['file']['type'][$idx]) {
            case 'image/jpeg':
                imagejpeg($tmp, $newFilePath, 100);
                break;
            case 'image/png':
                imagepng($tmp, $newFilePath, 0);
                break;
            case 'image/gif':
                imagegif($tmp, $newFilePath);
                break;
            default:
                exit;
                break;
        }
        
        //ChromePhp::log($idx.' '.$tmp);
        //ChromePhp::log($idx.' '.$newFilePath);
    
        /* cleanup memory */
        imagedestroy($image);
        imagedestroy($tmp);
        
        return $newFilePath;
    }
    
}
    //http://www.tutorialrepublic.com/php-tutorial/php-file-upload.php
    //https://stackoverflow.com/questions/2704314/multiple-file-upload-in-php
    //http://www.w3bees.com/2013/03/resize-image-while-upload-using-php.html
?>  