<?php
/**
 *  Tous droits réservés NDKDESIGN
 *
 *  @author    Hendrik Masson <postmaster@ndk-design.fr>
 *  @copyright Copyright 2013 - 2022 Hendrik Masson
 *  @license   Tous droits réservés
*/

include(dirname(__FILE__).'/../../config/config.inc.php');
include(dirname(__FILE__).'/../../init.php');

$loc = Tools::getValue('loc');
$rot = -Tools::getValue('rotate');
$top = Tools::getValue('top');
$left = Tools::getValue('left');
$path = ''; // $_SERVER['DOCUMENT_ROOT'] ?
$source = NULL;
$width = Tools::getValue('width');
$height = Tools::getValue('height');
$imgWidth = Tools::getValue('imgwidth');

//var_dump($image_info);




 //$source = imagecreatefrompng($path . $loc);
$source = imagecreatefrompng($path . $loc);
imagealphablending($source, false);
imagesavealpha($source, true);

$img = imagerotate($source, $rot, imageColorAllocateAlpha($source, 0, 0, 0, 127));
imagealphablending($img, false);
imagesavealpha($img, true);

//find the original image size
$image_info = getimagesize($loc);
$original_width = $image_info[0];
$original_height = $image_info[1];
$ratio = $original_width/$imgWidth;

$dx = 0;
$dy = 0;

$crop_x = $original_width/2 ;
$crop_y = $original_height/2;


$new_image = imagecreatetruecolor($width*$ratio, $height*$ratio);
imagealphablending($new_image, false);
imagesavealpha($new_image, true);
$trans_colour = imagecolorallocatealpha($new_image, 0, 0, 0, 127);
imagefill($new_image, 0, 0, $trans_colour);

//imagecopyresampled ( resource $dst_image , resource $src_image , int $dst_x , int $dst_y , int $src_x , int $src_y , int $dst_w , int $dst_h , int $src_w , int $src_h )
//$top = 0;
//$left = 0;
imagecopyresampled($new_image, $img, 0, 0, $left, -$top, $original_width, $original_height, $original_width, $original_height);

imagealphablending($new_image, false);
imagesavealpha($new_image, true);

header('Content-type: image/png');
imagepng($new_image);
?>