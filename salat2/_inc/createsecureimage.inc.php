<?php

$allowedRefferers = Array($_SERVER['HTTP_HOST']);

if (!in_array($_SERVER['HTTP_HOST'], $allowedRefferers)) { error_log("Trying to activate secured image script with no permission",null,null,$_SERVER['HTTP_HOST']); die(); }

if ($_GET['rnd']) {
	$_GET['rnd'] = (int)$_GET['rnd']; // random number for code
	$_GET['cl'] = (int)$_GET['cl']; // code length
	$_GET['type'] = (int)$_GET['type']; // should contain "1" for small\simple image

	$datekey = date("F j");
	$rcode = hexdec(md5($_SERVER['HTTP_USER_AGENT'].'1'.$_GET['rnd'].$datekey));
	$code = substr($rcode, 4, $_GET['cl']);
	
	if ($_GET['type']!=1){
		$fontSize = 5; // Trebuchet MS :: look at http://il2.php.net/gd for "font size"
		$imageQuality = 90;
		$w = ImageFontWidth($fontSize) * $_GET['cl'];
		$Timage = imagecreatefromgif("secureimageraw.gif");
		$size = getimagesize ("secureimageraw.gif");
		$image_w = ($w+24);
		$image_w = ($image_w<$size[0] ? $image_w : $size[0]);
		$image_h = $size[1];
		$image = ImageCreate($image_w, $image_h);
		imagecopyresized ($image,$Timage,0,0,0,0,$image_w,$image_h,$image_w,$image_h);
		ImageDestroy($Timage);
		
		$clr_r = rand(1,200);
		$clr_g = rand(1,150);
		$clr_b = rand(1,200);
		$text_color = ImageColorAllocate($image, $clr_r, $clr_g, $clr_b);
		
		$numberoflines = 1;
		for ($i=0 ; $i<$numberoflines ; $i++){
			$r_s_x = rand(1,($image_w/2));
			$r_s_y = rand(1,($image_h/2));
			$r_e_x = rand(($image_w/2),$image_w);
			$r_e_y = rand(($image_h/2),$image_h);
			$clr_r = rand(1,200);
			$clr_g = rand(1,150);
			$clr_b = rand(1,200);
			$line_color=imagecolorallocate($image,$clr_r,$clr_g,$clr_b);
			imageline($image,$r_s_x,$r_s_y,$r_e_x,$r_e_y,$line_color);
		}
		
		$numberofdots = 10;
		for ($i=0 ; $i<$numberofdots ; $i++){
			$r_s_x = rand(1,$image_w);
			$r_s_y = rand(1,$image_h);
			$clr_r = rand(20,200);
			$clr_g = rand(15,150);
			$clr_b = rand(20,200);
			imagesetpixel($image,$r_s_x,$r_s_y,imagecolorallocate($image,$clr_r,$clr_g,$clr_b));
		}
		
		$numberofrects = 0;
		for ($i=0 ; $i<$numberofrects ; $i++){
			$r_s_x = rand(1,($image_w/2));
			$r_s_y = rand(1,($image_h/2));
			$r_e_x = rand(($image_w/2),$image_w);
			$r_e_y = rand(($image_h/2),$image_h);
			$clr_r = rand(1,200);
			$clr_g = rand(1,150);
			$clr_b = rand(1,200);
			$line_color=imagecolorallocate($image,$clr_r,$clr_g,$clr_b);
			imagerectangle($image,$r_s_x,$r_s_y,$r_e_x,$r_e_y,$line_color);
		}
		
		imagestring($image, $fontSize, rand(5,15), rand(5,15), $code, $text_color);
	}else{
		$imageQuality = 80;
		$fontSize = 4;
		$image_w = 70;
		$image_h = 20;
		
		$image = ImageCreate($image_w, $image_h);
		imagefill($image,0,0,imagecolorallocate($image,255,255,255));
		
		$clr_r = rand(1,200);
		$clr_g = rand(1,150);
		$clr_b = rand(1,200);
		$text_color = ImageColorAllocate($image, $clr_r, $clr_g, $clr_b);
		
		$numberofdots = 20;
		for ($i=0 ; $i<$numberofdots ; $i++){
			$r_s_x = rand(1,$image_w);
			$r_s_y = rand(1,$image_h);
			$clr_r = rand(20,200);
			$clr_g = rand(15,150);
			$clr_b = rand(20,200);
			imagesetpixel($image,$r_s_x,$r_s_y,imagecolorallocate($image,$clr_r,$clr_g,$clr_b));
		}
		
		imagestring($image, $fontSize, rand(1,30), rand(1,7), $code, $text_color);
	}
	
	header("Content-type: image/jpeg");
	ImageJPEG($image, '', $imageQuality);
	ImageDestroy($image);
}

?>