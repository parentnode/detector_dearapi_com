<?php

class ImageTools {

	/**
	* Scale image-file from a dimension to another and new mimetype if given.
	* The function searches a folder to find the file that matches the 
	* @param string $org_path path pointing to a folder containing imagefiles. 
	* @param string $new_path foldername to save scaled and converted file in
	* @param int $new_width width to scale to
	* @param int $new_height height to scale to 
	* @param int $new_mimetype Optional parameter new Mimetype
	*/
	function autoScaleAndConvert($org_path, $new_path, $new_width, $new_height, $new_mimetype=false) {
		$new_file = false;
		for($i = 5; $i < 21 && !$new_file; $i = $i+5) {
			if($handle = opendir($org_path)) {
					while (false !== ($file = readdir($handle))) {
					if($file != "." && $file != "..") {
						if(is_file($org_path.$file) && substr($file, -3) == $new_mimetype) {
							$new_file = $this->scaleImage($org_path.$file, $new_path, $new_width, $new_height, $i);
							if($new_file) {
								return $new_file;
							}
						}
					}
				}
				closedir($handle);
			}
		}
		for($i = 5; $i < 21 && !$new_file; $i = $i+5) { 
			if($handle = opendir($org_path)) {
				while(false !== ($file = readdir($handle))) {
					if($file != "." && $file != "..") {
						if(is_file($org_path.$file) && substr($file, -3) !== $new_mimetype) {
							$new_file = $this->scaleImage($org_path.$file, $new_path, $new_width, $new_height, $i);
							if($new_file) {
								$converted_file = $this->convertImage($org_path.$file, $new_mimetype, $new_path);
								if($converted_file) {
									unlink($new_path."/".$new_file);
									return $converted_file;
								}
							}
						}
					}
				}
				closedir($handle);
			}
		}
		return "false";
	}

	/**
	* Scale image-file from a dimension to another
	*
	* @param string $org_img file to convert from
	* @param string $new_img_path foldername to save scaled and converted file in
	* @param int $new_height height to scale to 
	* @param int $new_width width to scale to
	* @param int $max_stretch maximum stretch percent
	* @param int $new_mimetype New Mimetype
	* @param int $new_img_name Optional parameter - to give the scaled and converted fil a new name
	*/
	function scaleAndConvertImage($org_img, $new_img_path, $new_width, $new_height, $max_stretch, $new_mimetype, $new_img_name=false) {
		$new_image = $this->scaleImage($file, $new_img_path, $new_height, $new_width, $max_stretch, $new_img_name);
		if($scaled_image) {
			$converted_image = $this->convertFile($new_img_path."/".$scaled_image, $new_mimetype, $new_img_path);
			unlink($new_img_path."/".$scaled_image);
			if($converted_image) {
				if($new_img_name) {
					copy($new_img_path."/".$converted_image, $new_img_path."/".$new_img_name);
					unlink($new_img_path."/".$converted_image);
					$converted_image = $new_img_name;
				}
				return $converted_image;
			}
			else {
				return false;
			}
		}
		else {
			return false;
		}
	}

	/**
	* Scale image-file from a dimension to another
	*
	* @param string $file file to convert from
	* @param string $save_dir foldername to save scaled file in
	* @param int $new_width width to scale to (if false, height is required)
	* @param int $new_height height to scale to (optional, default proportional to width) 
	* @param int $stretch maximum stretch percent
	* @param string $new_filename Optional parameter, new filename
	* @return String filename
	*/
	function scaleImage($file, $save_dir, $new_width, $new_height=false, $stretch=0, $new_filename=false) {
		list($org_width, $org_height, $type) = getimagesize($file);
		$cut_limit_percent = 5;
		$stretch_limit_percent = $stretch;

		$src_x = 0;
		$src_y = 0;
		// calculation scale proportions
		$org_ratio = $org_width/$org_height;
		
		if($new_width && !$new_height) {
			$new_height = $org_height / ($org_width/$new_width);
		}
		if($new_height && !$new_width) {
			$new_width = $org_width / ($org_height/$new_height);
		}
		$new_ratio = $new_width / $new_height;

//		print "width:". ($org_width / ($org_height/$new_height)) . "<br>";
//		print $new_height . "€€" . $new_width . "<br>";
//		print $org_ratio ."##". $new_ratio . "<BR>";

		// if scaling proportions have changed
		// this code has not been reworked
		if(round($org_ratio, 4) != round($new_ratio, 4)) {
			
			print "out of radius<br>";
/*
60x20 = 3

30x

60 / 30 = 2

30x (20 / (60 / 30)) = 10 = 3

8x

60 / 8 = 7.5

8x (20 / (60 / 8)) = 2,66666666666667 ~ 3 = 2.


x15

20 / 15 = 1.3333333

(60 / (20 / 15)) = 45 x15 = 3


x9

20 / 7 = 2,857142857142857

(60 / (20 / 11)) = 38.9 ~ 39 x11 = 
*/


			// new proportion is too high (stretching height)
			if($org_ratio > $new_ratio) {
				
				$x = $org_height - ($org_width*$new_ratio);
				if(round(($x/$org_height)*100) > $cut_limit_percent) {
					$x = round(($org_height/100)*5);
					$temp_height = $org_height -$x;
					$temp_ratio = $org_width/$temp_height;
					if(100-($new_ratio/$temp_ratio)*100 > $stretch_limit_percent) {
						return false;
					}
					else {
						$src_y = round($x/2);
						$org_height = $org_height - $x;
					}
				}
				else {
					$x = round($x);
					$src_y = round($x/2);
					$org_height = $org_height - $x;
				}
			}

			// new proportion is too wide (stretching width)
			else {
				$x = $org_width - ($org_height/$new_ratio);
				if(round(($x/$org_width)*100) > $cut_limit_percent) {
					$x = round(($org_width/100)*5);
	 				$temp_width = $org_width -$x;
					$temp_ratio = $temp_width/$org_height;

					print $temp_ratio . "##" . ($temp_ratio/$new_ratio) . "::" . (100-($temp_ratio/$new_ratio)*100) . "##" .  $stretch_limit_percent . "<br>";

					if(100-($temp_ratio/$new_ratio)*100 > $stretch_limit_percent) {

						return false;
					}
					else {
						$src_x = round($x/2);
						$org_width = $org_width - $x;
					}
				}
				else {
					$x = round($x);
					$src_x = round($x/2);
					$org_width = $org_width - $x;
				}
			}
		}
		
		

//		print "TYPE::".$type;
		// gif
		if($type == 1) {
			$new_name = $new_filename ? $save_dir.$new_filename : $save_dir.$new_width."x".$new_height.".gif";
			$image_p = imagecreatetruecolor($new_width, $new_height);
			$new_image = imagecreatefromgif($file);
			imagecopyresampled($image_p, $new_image, 0, 0, $src_x, $src_y, $new_width, $new_height, $org_width, $org_height);
			imagegif($image_p, $new_name);
			if($new_filename) {
				return $new_filename;
			}
			return $new_width."x".$new_height.".gif";
		}
		// jpg
		else if($type == 2) {
			$new_name = $new_filename ? $save_dir.$new_filename : $save_dir.$new_width."x".$new_height.".jpg";
			$image_p = imagecreatetruecolor($new_width, $new_height);
			$new_image = imagecreatefromjpeg($file);
			imagecopyresampled($image_p, $new_image, 0, 0, $src_x, $src_y, $new_width, $new_height, $org_width, $org_height);
			imagejpeg($image_p, $new_name, 100);
			if($new_filename) {
				return $new_filename;
			}
			return $new_width."x".$new_height.".jpg";
		}
		// png
		else if($type == 3) {
			$new_name = $new_filename ? $save_dir.$new_filename : $save_dir.$new_width."x".$new_height.".png";
			$image_p = imagecreatetruecolor($new_width, $new_height);
			$new_image = imagecreatefrompng($file);
			imagecopyresampled($image_p, $new_image, 0, 0, $src_x, $src_y, $new_width, $new_height, $org_width, $org_height);
			imagepng($image_p, $new_name);
			if($new_filename) {
				return $new_filename;
			}
			return $new_width."x".$new_height.".png";
		}
		else {
			//print "<script type=\"text/javascript\">alert('type: $type, org_width: $org_width, org_height: $org_width');</script>";	
			return false;
		}
	}

	/**
	* convert file from mimetype to mimetype and save in new dir
	*
	* @param string $file file to convert from
	* @param string $to_mimetype mimetype to convert to
	* @param string $save_dir foldername to save converted file in
	* @return string|bool new filename or false if unknown itemtype
	* 
	*/
	function convertImage($file, $to_mimetype, $save_dir) {
		list($width, $height, $type) = getimagesize($file);
		$new_name = $save_dir.$width."x".$height;
		$image_p = imagecreatetruecolor($width, $height);

		//file is gif: we want to convert to bmp and wbmp
		if($type == 1) {
			$new_image = imagecreatefromgif($file);
			imagecopyresampled($image_p, $new_image, 0, 0, 0, 0, $width, $height, $width, $height);
			if($to_mimetype == "bmp") {

			}
		}
		//file is jpg: we dont want to convert
		else if($type == 2) {
			
		}
		//file is png: we want to convert to gif, bmp and wbmp
		else if($type == 3) {
			$new_image = imagecreatefrompng($file);
			imagecopyresampled($image_p, $new_image, 0, 0, 0, 0, $width, $height, $width, $height);
			if($to_mimetype == "gif") {
				$new_name = $new_name.".gif";
				if(!file_exists($new_name)) {
					imagegif($image_p, $new_name);
					return $width."x".$height.".gif";
				}
			}
			else if($to_mimetype == "bmp") {
				//$new_name .= ".bmp";
				//return $width."x".$height.".bmp";
			}
			else if($to_mimetype == "wbmp") {
				$new_name = $new_name.".wbmp";
				png2wbmp($file, $new_name, $height, $width);
				return $width."x".$height.".wbmp";
			}
		}
		else {
			return false;
		}
	}

}

?>
