<?php

function strrev_utf8($str) {
    return join("", array_reverse(
        preg_split("//u", $str)
    ));
}

function str_split_unicode($str, $l = 0) {
    if ($l > 0) {
        $ret = array();
        $len = mb_strlen($str, "UTF-8");
        for ($i = 0; $i < $len; $i += $l) {
            $ret[] = mb_substr($str, $i, $l, "UTF-8");
        }
        return $ret;
    }
    return preg_split("//u", $str, -1, PREG_SPLIT_NO_EMPTY);
}
/**
 * resizing given image file and put it on dst
 *
 * @param image gd $dst the object to put on the image
 * @param string $file source file
 * @param int $w needed width
 * @param int $h needed height
 * @param bool $crop wheather to crop image
 * @return unknown
 */
function resize_image(&$dst,$file, $w, $h, $crop=FALSE) {
    list($width, $height) = getimagesize($file);
    $r = $width / $height;
    if ($crop) {
        if ($width > $height) {
            $width = ceil($width-($width*($r-$w/$h)));
        } else {
            $height = ceil($height-($height*($r-$w/$h)));
        }
        $newwidth = $w;
        $newheight = $h;
    } else {
        if ($w/$h > $r) {
            $newwidth = $h*$r;
            $newheight = $h;
        } else {
            $newheight = $w/$r;
            $newwidth = $w;
        }
    }
    $src = imagecreatefromjpeg($file);
    imagecopyresampled($dst, $src, 0, 0, 0, 0, $newwidth, $newheight, $width, $height);

    return $dst;
}

/**
 * reverse text of hebrew language but not with the numbers
 *
 * @param string $txt input string
 * @param int $direction 0 for EN 1 for HE
 */
function rearrange_text($txt,$direction=0) {
	if ( $direction == 0 ) {
		return $txt;
	}
	$my_txt = str_split_unicode($txt);
	$section = array();
	$reverse = array();
	$stat_atot_prev = 0;
	define('HEBREW_LETTERS','א-ת');
	foreach ( $my_txt as $letter ) {
		$stat_atot = preg_match('/['.HEBREW_LETTERS.'\+\-\*\s\/\\\\]/',$letter);
		if ( $stat_atot != $stat_atot_prev && !empty($section) ) {
			$section[] = $letter;
			$reverse[] = $stat_atot;
		} else if ( !empty($section) ) {
			$section[count($section)-1] .= $letter;
		} else {
			$section[] = $letter;
			$reverse[] = $stat_atot;
		}
		$stat_atot_prev = $stat_atot;
	}
	$res = array();
	for ( $i=0;$i<count($section);$i++ ) {
		$txt = $section[$i];
		if ( $reverse[$i] == 1 ) {
			$txt = strrev_utf8($txt);
		}
		$res[] = $txt;
	}
	return implode('',array_reverse($res));
}
function get_random_color() {	
	return ((rand(10,245)<<16)|(rand(10,245)<<8)|rand(10,245));
}
function get_white() {
	return ((255<<16)|(255<<8)|255);
}
function get_random_grey() {
	$base = rand(0,255);
	return (($base<<16)|($base<<8)|$base);
}
function imagecolorallocate_int(&$dst,$color) {
	return imagecolorallocate($dst,($color>>16)&255,($color>>8)&255,$color&255);
}
/**
 * get array of angles and needed size and adjust  the size for every thing to go in
 * allocate letters on image
 * @param unknown_type $image image resource
 * @param array $angles array of integer of angles to switch letter
 * @param int $size
 * @param int $space space between letters
 */
function calc_width_boxes(&$image,$letters,$angles,$size,$space,$font,$font_color,$second_font_color) {

	do {
		$bboxes =  array();
		$total_width = 0;
		for ($i=0;$i<count($letters);$i++) {
			$bbox = imagettfbbox ($size,$angles[$i],$font,$letters[$i]);
			
			
			$minX = min(array($bbox[0],$bbox[2],$bbox[4],$bbox[6])); 
		    $maxX = max(array($bbox[0],$bbox[2],$bbox[4],$bbox[6])); 
		    $minY = min(array($bbox[1],$bbox[3],$bbox[5],$bbox[7])); 
		    $maxY = max(array($bbox[1],$bbox[3],$bbox[5],$bbox[7]));
		    $bbox = array_merge($bbox,compact('minX','minY','maxX','maxY'));
		    $total_width += $space + ($maxX-$minX)*(1+2*sin(deg2rad(abs($angles[$i]))));
		    $bboxes[] = $bbox;
		}
		$size--;
	} while ( $total_width > imagesx($image) );
	$size++;
	$result = array();
	$twidth = $total_width/2;
	$total_width = 0;
	if ( $bboxes[0]['minX'] < 0 ) {
		$total_width = $space -1*$bboxes[0]['minX']*(1+sin(deg2rad(abs($angles[0]))));
	}

	for ($i=0;$i<count($letters);$i++) {
		$bbox = $bboxes[$i];
		$x = $bbox['minX']*(1+sin(deg2rad(abs($angles[$i])))) + $total_width;
		$total_width +=($bbox['maxX'])*(1+sin(deg2rad(abs($angles[$i])))) + $space;
		$y = $bbox[1] + (imagesy($image) / 2) - ($bbox[5] / 2) - 5 + rand(-10,10);
		//if (DEBUG==1)echo floor($x).','.$y.'   '.var_export($bbox,true)."<br/>\n";

		imagettftext ($image,$size,$angles[$i],$x+2,$y+2,$second_font_color,$font,$letters[$i]);
		imagettftext ($image,$size,$angles[$i],$x,$y,$font_color,$font,$letters[$i]);
		
	}

}
function creatCaptchaMultiLnag($text,$direction, $width = 200,$height = 100,$options = array()){

    /*
    // remove by netanel for new salat.- reverse the hebrew
	$textArr=explode(' ',$text);
	$textArr[0]=strrev_utf8($textArr[0]);
	$text=implode(' ',$textArr);
    */
    if($options['font_size']==''){
		$fontSize = $width/mb_strlen($text);
	} else{
		$fontSize=$options['font_size'];
	}

	$im = imagecreatetruecolor($width,$height);
	$str = rearrange_text($text,1);

	$allocate_back = imagecolorallocate_int ($im, get_random_color()); 
	if ( empty($allocate_back) ) {
		die('allocate image failed: '.var_export($allocate_back,true));
	}
	imagefill ( $im , 0 , 0 , $allocate_back );
	
	if(!empty($options['background_image'])){
		resize_image($im,$options['background_image'],$width,$height);
		//white background
		//$num_color = hexdec($options['background_image']);
		//$red = ($num_color>>16)&255;
		//$green = ($num_color>>8)&255;
		//$blue = $num_color&255;
  		//$background_color = imagecolorallocate ($im, $red, $green, $blue);
	} else {
		$polyCols = array();
		for ($i=0;$i<10;$i++) {  
			$cpoly =  get_random_color();
			$polyCols[] = $cpoly;
			$col_poly = imagecolorallocate_int ($im, $cpoly); 
			$max_x = rand(1,$width  - 1); 
			$max_y = rand(1,$height  - 1); 
			$mid_y = $max_y>>1; 
			$mid_x = $max_x>>1;  
		    //start point
		    //end point    
			$co_ords_arr = 
						array(
								array( 
										0,$max_y,
										0,$mid_y,
										$mid_x,0,
										$max_x,$mid_y,
										$max_x,$max_y,
							    ),
							    array( 
										$width,0,
										$width,$height,
										$max_x,$mid_y,
										$max_x,$max_y,
							    ),
							    array( 
										0,0,
										0,$max_y,
										$max_x,$mid_y,
										$max_x,$max_y,
							    ),
							    array( 
										0,0,
										$max_x,$mid_y,
										$max_x,$max_y,
							    ),
							    array( 
										0,$mid_y,
										$max_x,$mid_y,
										$width,$height,
										0,$height,
							    ),
					    );
		    $co_ords = $co_ords_arr[rand(0,count($co_ords_arr)-1)];
		    
		    // Draw the polygon
			imagefilledpolygon($im, $co_ords,intval(count($co_ords)/2),$col_poly);
		}
		
		
	}
	$font_directory = '../_public/';
	$font = $font_directory."arial.ttf";
	if($direction==1){

		$font = $font_directory."arial.ttf";
	}else{
		if($options['font']){
			//change font
			if(is_file($font_directory.$options['font'])){
				$font = $font_directory.$options['font'];
			}
		}
	}
	$size = 35;
	if ( !empty($options['font_size']) && is_numeric($options['font_size']) ) {
		$size = intval($options['font_size']);
	}
//	do {
//		$size--;
//		$bbox = imagettfbbox ($size,0,$font,$str);
//	} while ( abs($bbox[0] - $bbox[4] - 20) > $width || abs($bbox[5] - $bbox[1]) > $height );
//	do {
//		$rand_font_color = get_random_color();
//	} while( in_array($rand_font_color,$polyCols) );
//	
//	$x = $bbox[0] + (imagesx($im) / 2) - ($bbox[4] / 2);
//	$y = $bbox[1] + (imagesy($im) / 2) - ($bbox[5] / 2) - 5;
//	imagettftext ($im,$size,0,$x,$y,$font_color,$font,$str);
	//$rand_font_color = get_random_grey();
	$font_color = imagecolorallocate_int ($im, !empty($options['fontColor'])?$options['fontColor']:0);
	$second_font_color = imagecolorallocate_int ($im, !empty($options['secondFontColor'])?$options['secondFontColor']:get_white());

	$letters = str_split_unicode($str);

	$space =12;
	$angles = array();
	foreach ( $letters as $letter ) {
		/*if ( !empty($letter) ) {
			$angles[] = rand(-20,20);
		} else {
			$angles[] = 0;
		}*/
		$angles[] = 0;
	}

	calc_width_boxes($im,$letters,$angles,$size,$space,$font,$font_color,$second_font_color);
	//$my_text = array();
	//$my_text = str_split_unicode($text);
	//if (DEBUG==0) {
		header("Content-Type: image/png"); 
		imagepng($im);
	//}
	//echo $letters[0]."<br/>\n";
	imagedestroy($im);
	
}


	function captcha_encrypt($decrypted, $password, $salt='!kQm*fF3pXe1Kbm%9') { 
 		// Build a 256-bit $key which is a SHA256 hash of $salt and $password.
 		$key = hash('SHA256', $salt . $password, true);
 		// Build $iv and $iv_base64.  We use a block size of 128 bits (AES compliant) and CBC mode.  (Note: ECB mode is inadequate as IV is not used.)
 		srand(); $iv = mcrypt_create_iv(mcrypt_get_iv_size(MCRYPT_RIJNDAEL_128, MCRYPT_MODE_CBC), MCRYPT_RAND);
 		if (strlen($iv_base64 = rtrim(base64_encode($iv), '=')) != 22) return false;
 		// Encrypt $decrypted and an MD5 of $decrypted using $key.  MD5 is fine to use here because it's just to verify successful decryption.
 		$encrypted = base64_encode(mcrypt_encrypt(MCRYPT_RIJNDAEL_128, $key, $decrypted . md5($decrypted), MCRYPT_MODE_CBC, $iv));
 		// We're done!
 		return $iv_base64 . $encrypted;
 	} 

 	/**
 	 * Enter description here...
 	 *
 	 * Based on: http://www.php.net/manual/en/book.mcrypt.php#107483
 	 * 
 	 * @param unknown_type $encrypted
 	 * @param unknown_type $password
 	 * @param unknown_type $salt
 	 * @return unknown
 	 */
	 function captcha_decrypt($encrypted, $password, $salt='!kQm*fF3pXe1Kbm%9') {
		// Build a 256-bit $key which is a SHA256 hash of $salt and $password.
		$key = hash('SHA256', $salt . $password, true);
		// Retrieve $iv which is the first 22 characters plus ==, base64_decoded.
		$iv = base64_decode(substr($encrypted, 0, 22) . '==');
		// Remove $iv from $encrypted.
		$encrypted = substr($encrypted, 22);
		// Decrypt the data.  rtrim won't corrupt the data because the last 32 characters are the md5 hash; thus any \0 character has to be padding.
		$decrypted = rtrim(mcrypt_decrypt(MCRYPT_RIJNDAEL_128, $key, base64_decode($encrypted), MCRYPT_MODE_CBC, $iv), "\0\4");
		// Retrieve $hash which is the last 32 characters of $decrypted.
		$hash = substr($decrypted, -32);
		// Remove the last 32 characters from $decrypted.
		$decrypted = substr($decrypted, 0, -32);
		// Integrity check.  If this fails, either the data is corrupted, or the password/salt was incorrect.
		if (md5($decrypted) != $hash) return false;
		// Yay!
		return $decrypted;
	}
	
?>