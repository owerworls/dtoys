<?php

$images = glob('*.jpeg');

var_dump(extension_loaded ('imagick'));
foreach($images as $image)
{


    try
    {
        $img = new Imagick($image);
        $img->setImageCompressionQuality(50);
        $img->stripImage();
        $img->writeImage($image);
        $img->clear();
        $img->destroy();

        echo "Removed EXIF data from $image. \n";

    } catch(Exception $e) {
        echo 'Exception caught: ',  $e->getMessage(), "\n";
    }
}
