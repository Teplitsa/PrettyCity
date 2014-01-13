<?php
function get_rating_image($address = "Мира, 1", $rsize = 100){
  //дефолтные параментры
	$logo_dir = $_SERVER["DOCUMENT_ROOT"]."/d/img/";
	$source   = $logo_dir."prettycity_green.png";
	$font     = "php/DaysOne-Regular.ttf";
	$fontSize = 70;

  //получаем рейтинг
	$raiting = func_getRaitingByAddress( $address );
	$raiting = round( $raiting['raiting'] );

  //загружаем болванку c лого.
   	if ( $raiting >= 75 )
   		$source = $logo_dir."prettycity_green.png";
   	if ( $raiting < 75 and $raiting >= 26 )
   		$source = $logo_dir."prettycity_brown.png";
   	if ( $raiting < 26 )
   		$source = $logo_dir."prettycity_gray.png";
	if (!file_exists($source) )
		throw new Exception( "source file not found: ".$source );
	$image = imagecreatefrompng( $source );
	imageantialias($image, true);
	imagealphablending($image, true);
	imagesavealpha($image, true);
	list($imwidth,$imheight,$type,$attr) = getimagesize($source);


  //создаем болванку текста
	$textim  = imagecreatetruecolor($imwidth, $imheight);
	imageantialias($textim, true);
	imagealphablending($textim, true);
	imagesavealpha($textim, false);
	$alpha   = imagecolorallocatealpha($textim, 0, 0, 0, 127);
	$textcol = imagecolorallocatealpha($textim, 255, 255, 255, 0);
	imagefill($textim, 0, 0, $alpha);
	if (! file_exists( $font ) ) 
		throw new Exception("font file not found");
	$text_props = imagettftext($textim, $fontSize, 0, 0, 100, $textcol, $font, $raiting."%");

  //сливаем текст и лого
	$dst_im = $image;
	$src_im = $textim;
	$src_x  = $text_props[6];
	$src_y  = $text_props[7];
	$src_w  = $text_props[2] - $text_props[0] + 10;
	$src_h  = $text_props[3] - $text_props[5];
	$dst_w  = $imwidth;
	$dst_h  = $imheight;
	$dst_x  = ($dst_w - $src_w) / 2;
	$dst_y  = ($dst_h - $src_h) / 2 + 15;
	imagecopy($dst_im, $src_im, $dst_x, $dst_y, $src_x, $src_y, $src_w, $src_h);

  //ресайзим ресайзу чгоблин картинку
	$res_im = imagecreatetruecolor($rsize, $rsize);
	imageantialias($res_im, true);
	imagealphablending($res_im, false);
	imagesavealpha($res_im, true);
	imagecopyresampled(
		$dst_image = $res_im, 
		$src_image = $dst_im, 
		$dst_x = 0, 
		$dst_y = 0, 
		$src_x = 0, 
		$src_y = 0, 
		$dst_wi = $rsize, 
		$dst_hi = $rsize, 
		$src_wi = $imwidth , 
		$src_hi = $imheight);
	$dst_im = $res_im;

  //отдаем картинку
	return $dst_im;

}
	
?>