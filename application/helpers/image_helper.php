<?php
function createImage($imageObject, $path, $orientation = 1)
{
	if( ! empty($imageObject))
	{
		$img = explode(',', $imageObject);
		$count = count($img);
		if($count == 1)
		{
			$imageData = base64_decode($img[0]);
		}
		else
		{
			$imageData = base64_decode($img[1]);
		}

		$image = imagecreatefromstring($imageData);
		$image_rotate = ($orientation == 3 ? 180 : ($orientation == 6 ? -90 : ($orientation == 8 ? 90 : NULL)));
		$image_width = imagesx($image);
		$image_height = imagesy($image);
		$ratio = $image_height / $image_width;
		$new_width = 800;
		$new_height = intval($ratio * $new_width);
		$thumb = imagecreatetruecolor($new_width, $new_height);
		imagecopyresampled($thumb, $image, 0, 0, 0, 0, $new_width, $new_height, $image_width, $image_height);
		$source = $image_rotate != NULL ? imagerotate($thumb, $image_rotate, 0) : $thumb;
		imagejpeg($source, $path, 100);
		imagedestroy($image);
		return TRUE;
	}

	return FALSE;
}

function readImage($path)
{
	$type = pathinfo($path, PATHINFO_EXTENSION);
	$data = file_get_contents($path);
	$base64 = 'data:image/' . $type . ';base64,' . base64_encode($data);

	return $base64;
}


function get_image_path($name, $folder = 'sales_order')
{
	$ci =& get_instance();
  $path = $ci->config->item('image_path').$folder;
	$image_path = base_url().$path."/{$name}.jpg";
	$file = $ci->config->item('image_file_path')."{$folder}/{$name}.jpg";

	return file_exists($file) ? $image_path : no_image_path();

}


function get_image_url($name, $folder = 'payments')
{
  $ci =& get_instance();
	$link = base_url()."images/{$folder}/{$name}.jpg";

  $file = $ci->config->item('image_file_path')."{$folder}/{$name}.jpg";

	if( ! file_exists($file) )
	{
		return NULL;
	}

	return $link;
}


function delete_image($name, $folder = "sales_order")
{
	$sc = TRUE;

  $ci =& get_instance();
  $path = $ci->config->item('image_file_path').$folder;
  $image_path = "{$path}/{$name}.jpg";

	if(file_exists($image_path))
	{
		if(! unlink($image_path))
		{
			$sc = FALSE;
		}
	}

	return $sc;
}


function no_image_path()
{
  $ci =& get_instance();
  $path = $ci->config->item('image_path');
  $no_image_path = base_url().$path.'no_image.jpg';
  return $no_image_path;
}
?>
