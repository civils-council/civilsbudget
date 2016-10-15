<?php

namespace AppBundle\Helper;

class ImageWorker
{
    public $image;
    public $image_type;

    /**
     * @param $filename
     */
    public function load($filename)
    {
        $this->image_type = $this->getType($filename);

        switch ($this->image_type) {
            case IMAGETYPE_JPEG: $this->image = imagecreatefromjpeg($filename); break;
            case IMAGETYPE_GIF: $this->image = imagecreatefromgif($filename); break;
            case IMAGETYPE_PNG: $this->image = imagecreatefrompng($filename); break;
            case IMAGETYPE_BMP: $this->image = $this->imagecreatefrombmp($filename); break;
            case IMAGETYPE_WBMP: $this->image = imagecreatefromwbmp($filename); break;
            case IMAGETYPE_XBM: $this->image = imagecreatefromxbm($filename); break;
        }
    }

    /**
     * @param string $filename
     * @return integer
     */
    public function getType($filename){
        $image_info = getimagesize($filename);
        return $image_info[2];
    }

    /**
     * @param resource $thumb
     */
    public function transparentToColor($thumb)
    {
        $im = imagecreatetruecolor($this->getWidth(), $this->getHeight());
        $color = imagecolorallocate($im, 255, 255, 255);
        imagefill($im, 0, 0, $color);
        imagecopy($thumb, $im, 0, 0, 0, 0, imagesx($im), imagesy($im));
    }

    /**
     * @param string $filename
     * @param int $image_type
     * @param int $compression
     * @param null $permissions
     */
    public function save($filename, $image_type = IMAGETYPE_JPEG, $compression = 95, $permissions = null)
    {
        if ($image_type == IMAGETYPE_JPEG) {
            imagejpeg($this->image, $filename, $compression);
        } elseif ($image_type == IMAGETYPE_GIF) {
            imagegif($this->image, $filename);
        } elseif ($image_type == IMAGETYPE_PNG) {
            imagepng($this->image, $filename);
        }
        if ($permissions != null) {
            chmod($filename, $permissions);
        }
    }

    public function output($image_type = IMAGETYPE_JPEG)
    {
        if ($image_type == IMAGETYPE_JPEG) {
            imagejpeg($this->image);
        } elseif ($image_type == IMAGETYPE_GIF) {
            imagegif($this->image);
        } elseif ($image_type == IMAGETYPE_PNG) {
            imagepng($this->image);
        }
    }

    /**
     * @return integer
     */
    public function getWidth()
    {
        return imagesx($this->image);
    }

    /**
     * @return integer
     */
    public function getHeight()
    {
        return imagesy($this->image);
    }

    /**
     * @param string $height
     */
    public function resizeToHeight($height)
    {
        $ratio = $height / $this->getHeight();
        $width = $this->getWidth() * $ratio;
        $this->resize($width, $height);
    }

    /**
     * @param string $width
     */
    public function resizeToWidth($width)
    {
        $ratio = $width / $this->getWidth();
        $height = $this->getheight() * $ratio;
        $this->resize($width, $height);
    }

    /**
     * @param $scale
     */
    public function scale($scale)
    {
        $width = $this->getWidth() * $scale / 100;
        $height = $this->getheight() * $scale / 100;
        $this->resize($width, $height);
    }

    /**
     * @param $thumb_width
     * @param $thumb_height
     */
    public function cropMiddle($thumb_width, $thumb_height)
    {
        $width = $this->getWidth();
        $height = $this->getHeight();

        $original_aspect = $width / $height;
        $thumb_aspect = $thumb_width / $thumb_height;

        if ($original_aspect >= $thumb_aspect) {
            // If image is wider than thumbnail (in aspect ratio sense)
            $new_height = $thumb_height;
            $new_width = $width / ($height / $thumb_height);
        } else {
            // If the thumbnail is wider than the image
            $new_width = $thumb_width;
            $new_height = $height / ($width / $thumb_width);
        }
        $thumb = imagecreatetruecolor($thumb_width, $thumb_height);
        $this->transparentToColor($thumb);

        imagecopyresampled($thumb,
            $this->image,
            0 - ($new_width - $thumb_width) / 2, // Center the image horizontally
            0 - ($new_height - $thumb_height) / 2, // Center the image vertically
            0, 0,
            $new_width, $new_height,
            $width, $height
        );

        $this->image = $thumb;
    }

    /**
     * @param string $width
     * @param string $height
     * @param int $left
     * @param int $top
     */
    public function resize($width, $height, $left = 0, $top = 0)
    {
        $new_image = imagecreatetruecolor($width, $height);
        imagecopyresampled($new_image, $this->image, 0, 0, $left, $top, $width, $height, $this->getWidth(), $this->getHeight());
        $this->image = $new_image;
    }

    public function imagecreatefrombmp($p_sFile)
    {
        $file = fopen($p_sFile, 'rb');
        $read = fread($file, 10);
        while (!feof($file) && ($read != '')) {
            $read    .=    fread($file, 1024);
        }
        $temp = unpack('H*', $read);
        $hex = $temp[1];
        $header = substr($hex, 0, 108);
        if (substr($header, 0, 4) == '424d') {
            $header_parts = str_split($header, 2);
            $width = hexdec($header_parts[19].$header_parts[18]);
            $height = hexdec($header_parts[23].$header_parts[22]);
            unset($header_parts);
        }
        $x = 0;
        $y = 1;
        $image = imagecreatetruecolor($width, $height);
        $body = substr($hex, 108);
        $body_size = (strlen($body) / 2);
        $header_size = ($width * $height);
        $usePadding = ($body_size > ($header_size * 3) + 4);
        for ($i = 0;$i < $body_size;$i += 3) {
            if ($x >= $width) {
                if ($usePadding) {
                    $i    +=    $width % 4;
                }
                $x = 0;
                ++$y;
                if ($y > $height) {
                    break;
                }
            }
            $i_pos = $i * 2;
            $r = hexdec($body[$i_pos + 4].$body[$i_pos + 5]);
            $g = hexdec($body[$i_pos + 2].$body[$i_pos + 3]);
            $b = hexdec($body[$i_pos].$body[$i_pos + 1]);
            $color = imagecolorallocate($image, $r, $g, $b);
            imagesetpixel($image, $x, $height - $y, $color);
            ++$x;
        }
        unset($body);

        return $image;
    }
}
