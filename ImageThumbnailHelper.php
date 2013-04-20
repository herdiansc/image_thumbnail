<?php
/**
 * Image Thumbnail helper for CakePHP.
 *
 * @author Herdian Sc<herdiansc@gmail.com> 
 * @version 1.0
 */

App::uses('AppHelper', 'View/Helper');

/**
 * The thumbnail helper class.
 */
class ImageThumbnailHelper extends AppHelper {
    /**
     * Helpers used within this helper.
     */
    public $helpers = array('Html');
    
    /**
     * Path to source images.
     *
     * @var string
     */
    private $files_dir = 'files/';
    
    /**
     * Path for saved thumbnail images.
     *
     * @var string
     */
    private $thumbs_dir = 'thumbs/';
    
    /**
     * Width of thumbnail image.
     *
     * @var integer
     */
    private $width;
    
    /**
     * Height of thumbnail image.
     *
     * @var integer
     */
    private $height;
    
    /**
     * Renders a thumbnail image.
     *
     * @param  string $filename
     * @param  array  $options
     * @param  array  $imgOptions
     * @return string
     */
    public function render($filename, $options = array(), $imgOptions = array()) {
        $this->width = isset($options['width']) ? intval($options['width']) : 100;
        $this->height = isset($options['height']) ? intval($options['height']) : 75;
        
        $path = IMAGES.$options['folder'].DS;

        list($width, $height) = getimagesize($path.$filename);
        
        if (!is_file($path . $this->width . 'x' . $this->height.'_'.$filename) || !$options['cache']) {
            $canvas = imagecreatetruecolor($this->width, $this->height);
            $image = $this->imagecreatefromfile($path.$filename);
            imagecopyresampled($canvas, $image, 0, 0, 0, 0, $this->width, $this->height, $width, $height);
            imagejpeg($canvas, $path . $this->width . 'x' . $this->height . '_' . $filename, 100);
        }
        
        return $this->Html->image(
            $options['folder'].'/'. $this->width . 'x' . $this->height . '_' . $filename,
            $imgOptions
        );
    }
    
    function imagecreatefromfile($path, $user_functions = false) {
        $info = @getimagesize($path);
        
        if(!$info) {
            return false;
        }
        
        $functions = array(
            IMAGETYPE_GIF => 'imagecreatefromgif',
            IMAGETYPE_JPEG => 'imagecreatefromjpeg',
            IMAGETYPE_PNG => 'imagecreatefrompng',
            IMAGETYPE_WBMP => 'imagecreatefromwbmp',
            IMAGETYPE_XBM => 'imagecreatefromwxbm',
        );
        
        if($user_functions) {
            $functions[IMAGETYPE_BMP] = 'imagecreatefrombmp';
        }
        
        if(!$functions[$info[2]]) {
            return false;
        }
        
        if(!function_exists($functions[$info[2]])) {
            return false;
        }
        
        return $functions[$info[2]]($path);
    }
}
