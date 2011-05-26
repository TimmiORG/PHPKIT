<?php
# PHPKIT WCMS | Web Content Management System
#
#
# YOU ARE NOT AUTHORISED TO CREATE ILLEGAL COPIES OF THIS
# FILE AND/OR TO REMOVE THIS INFORMATION
#
# SIE SIND NICHT BERECHTIGT, UNRECHTMÄSSIGE KOPIEN DIESER
# DATEI ZU ERSTELLEN UND/ODER DIESE INFORMATIONEN ZU ENTFERNEN
#
# This file / the PHPKIT software is no freeware! For further 
# information please visit our website or contact us via email:
#
# Diese Datei / die PHPKIT Software ist keine Freeware! Für weitere
# Informationen besuchen Sie bitte unsere Website oder kontaktieren uns per E-Mail:
#
# email     : info@phpkit.com
# website   : http://www.phpkit.com
# licence   : http://www.phpkit.com/licence
# copyright : Copyright (c) 2002-2009 mxbyte gbr | http://www.mxbyte.com


if(!defined('pkFRONTEND'))
	die('Direct access to this location is not permitted.');

if(!in_array(pkFRONTEND,array('captcha','style','rsimg')))
	die('Direct access to this location is not permitted.');


switch(pkFRONTEND)
	{
	case 'captcha' :
		pkLoadClass($SESSION,'session');
	
		$length=5;													#string length
		$imagefile=pkDIRGDCAPTCHA.'default'.rand(1,6).'.png';		#background image
		$fontface=pkDIRGDFONTS.'xfiles.ttf';						#font face
		$fontsize=20; 												#font size
		
		#the random string
		$string=pkStringRandom($length);

		$SESSION->start();
		$SESSION->set(pkCAPTCHAVARNAME,$string);

		
		header('Content-type: image/png');
		
		$image=ImageCreateFromPNG($imagefile); 
		$color=ImageColorAllocate($image,0,0,0);
		$angle=rand(-4,4); 
		$x=rand(10,45);
		$y=30; 
		
		imagettftext($image,$fontsize,$angle,$x,$y,$color,$fontface,$string);
		imagepng($image);
		imagedestroy($image);
		break;
		#END case captcha
	case 'style' :
		pkLoadClass($STYLE,'style');
		
		if(!$STYLE->connected())
			die('No Database Connection available in fx::media style');

		if(!$STYLE->load())
			die('No valid style could be loaded');
		
		$STYLE->parse();
		
		header('Content-type: text/css');
		echo $STYLE->getcss();
		break;
	case 'rsimg' : #resize (external) images
		#create a database connection
		#to get some config values
		pkLoadClass($SQL,'sql');
		
		if(!$SQL->connect())
			{
			die('No Database connection.');
			}
		
		$query = $SQL->query("SELECT id,value FROM ".pkSQLTAB_CONFIG."
			WHERE id IN('image_resize_width','image_resize_height')");
		while(list($id,$value) = $SQL->fetch_row($query))
			{
			$config[$id] = unserialize($value);
			}
		
		
		#some magic numbers and pre-defintions
		$types 		= array(1=>'gif',2=>'jpeg',3=>'png');
		$quality	= 80; #preset for jpgs
		$content_type = '';	#image type


		# width/height of the thumbnail image
		$thumbwidth	= $config['image_resize_width'] ? $config['image_resize_width'] : 350;
		$thumbheight = $config['image_resize_height'] ? $config['image_resize_height'] : 350;

		
		#get infos about the raw img		
		$src = $ENV->_get('src');
		$src = base64_decode($src);
		$data = @getimagesize($src);

		if(!$data)
			{
			die('Invalid - there isnt an image');
			}

		list($width,$height,$type) = $data;
				
		if($type<1 || $type>3)
			{
			die('Invalid - does not seams to be an image');
			}
			
		$type = $types[$type]; #rewrite the numeric identifier to a representing string (jpeg,png,gif)

		if($height<=$thumbheight && $width<=$thumbwidth)
			{
			pkHeaderFxCache($data['mime']);
			exit(file_get_contents($src));
			}
			
		#the image is too hugh - resize it
		if($width==$height)
			{
			$thumbheight = $thumbwidth;
			}
		elseif($width>$height)
			{
			$thumbheight = floor($height/($width/$thumbwidth));
			}
		elseif($height>$width)
			{
			$thumbwidth = floor($width/($height*$thumbwidth));
			}

		#create the thumb (respectively a smaller version of the image)
		#dynamic function call
		$funcname = 'imagecreatefrom'.$type; #...createfromPNG / GIF / JPEG
	
		#check if the function is supported
		if(!function_exists($funcname)) 
			{
			die('unsopprted image type by gdlib or no gdlib');
			}

		#create an image from the source
		$image = $funcname($src); #image is a ressource

		if(!$image)
			{
			die('Invalid - does not seams to be an supported image');
			}
		
		#create the new image
		if($type != 'jpeg')
			{
			$thumb = imagecreate($thumbwidth,$thumbheight); #doesnt work with gifs
			}
		else
			{
			$thumb = imagecreatetruecolor($thumbwidth,$thumbheight); #doesnt work with gifs
			}
			
		if(!$thumb)
			{
			die('Anything is wrong - couldnt create the image');
			}


		if($transparency=imagecolortransparent($image))
			{
			imagecolortransparent($image,imagecolorallocate($image, 0, 0, 0));
			}

		if($transparency)
			{
			imagealphablending($thumb, true); #setting alpha blending on
			imagesavealpha($thumb, true); # save alphablending setting
			}


		imagecopyresized($thumb,$image,0,0,0,0,$thumbwidth,$thumbheight,$width,$height); # resizes the image


		#prepare output
		$funcname = 'image'.$type;

		#output
		if(isset($data['mime']) && !empty($data['mime']))	
			{
			pkHeaderFxCache($data['mime']);
			}
		else
			{
			pkHeaderFxCache('image/'.$type);
			}
	
	
		#finaly	puts the image out
		if($type == 'jpeg')
			{
			$funcname($thumb,'',$quality); #no filename (second param) - a filename force to write the image into a file
			}
		else
			{
			#if an png is created the quality has the be spared otherwise the image will be corupted
			$funcname($thumb);
			}
		break;
	}#END switch(pkFRONTEND)

exit;
?>