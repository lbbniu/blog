<?php
class ZywxappPhpThumbResizer
{
    private $newHeight = 0;
    
    private $thumb = null;
    
    private $newWidth = 0;
    
    public function getNewWidth()
    {
         return $this->newWidth;
    }
    
    public function getNewHeight()
    {
        return $this->newHeight;            
    }
        
    public function load($image, $calc_size = TRUE)
    {
        $basePath = dirname(__FILE__) . '/../../libs/';
        require_once $basePath . 'phpThumb/ThumbLib.inc.php';
        
        ZywxappLog::getInstance()->write('INFO', 'Before thumb create: ' . $image, 'ZywxappPhpThumbResizer.load');

        try {
            $thumb = PhpThumbFactory::create($image);
            ZywxappLog::getInstance()->write('INFO', 'Thumb object created: ' . $image, 'ZywxappPhpThumbResizer.load');
            $this->thumb = $thumb;
            $size = $thumb->getCurrentDimensions();
        } catch (Exception $e) {
            ZywxappLog::getInstance()->write('ERROR', 'GD failed to create or get size of image for thumb, error: ' . $e->getMessage(), 'ZywxappPhpThumbResizer.load');
        }

        ZywxappLog::getInstance()->write('INFO', 'After thumb create: ' . $image . ' with size: ' . $size, 'ZywxappPhpThumbResizer.load');
        
        if ($calc_size){
            $this->newHeight = $size['height'];
            $this->newWidth = $size['width'];   
        }
    }
    
    public function resize($image, $file, $width, $height, $type, $allow_up = false, $save_image = true)
    {
    	ZywxappLog::getInstance()->write('INFO', 'About to resize the image: ' . $image, 'ZywxappPhpThumbResizer.resize');
        $basePath = dirname(__FILE__) . '/../../libs/';
        $url = '';
        require_once $basePath . 'phpThumb/ThumbLib.inc.php';
        $options = array();
        if ($allow_up){
            $options['resizeUp'] = true;
        }
        
        try {
            ZywxappLog::getInstance()->write('INFO', 'Before thumb resize: ' . $image, 'ZywxappPhpThumbResizer.resize');
            $thumb = PhpThumbFactory::create($image, $options);
            ZywxappLog::getInstance()->write('INFO', 'Thumb object created: ' . $image, 'ZywxappPhpThumbResizer.resize');
            //$thumb->$type($width, $height);
            if ( $type == 'perspectiveResize' ){
                $type = 'resize';
                //获取图片大小
                $dim = $thumb->getCurrentDimensions();
                $currWidth = $dim['width'];
                $currHeight = $dim['height'];
                if ( $currWidth > $currHeight ){
                    // This is a wide image, make sure the height will fit
                    $width = ceil(($height / $currHeight) * $currWidth);
                } else {
                    // This is a high image, make sure the width will fit
                    $height = ceil(($width / $currWidth) * $currHeight);

                }
                ZywxappLog::getInstance()->write('INFO', "Resizing from width: {$currWidth} to: {$width} and from height: {$currHeight} to: {$height}",'ZywxappPhpThumbResizer.resize');
            } else {
                if ($height == 0){
                    $type = 'resize';
                    // Calc the new height based of the need to resize
                    //获取图片大小
                    $dim = $thumb->getCurrentDimensions();
                    $currWidth = $dim['width'];
                    $currHeight = $dim['height'];

                    if ($currWidth > $width){
                        $height = ($width / $currWidth) * $currHeight;
                    } else {
                        $height = $currHeight;
                    }

                    ZywxappLog::getInstance()->write('INFO', "Resizing from width: {$currWidth} to: {$width} and therefore from height: {$currHeight} to: {$height}",'ZywxappPhpThumbResizer.resize');
                } elseif ($width == 0) {
                    $type = 'resize';
                    // Calc the new height based of the need to resize
                    //获取图片大小
                    $dim = $thumb->getCurrentDimensions();
                    $currWidth = $dim['width'];
                    $currHeight = $dim['height'];

                    if ($currHeight > $height){
                        $width = ($height / $currHeight) * $currWidth;
                    } else {
                        $width = $currWidth;
                    }

                    ZywxappLog::getInstance()->write('INFO', "Resizing from height: {$currHeight} to: {$height} and therefore from width: {$currWidth} to: {$width}",'ZywxappPhpThumbResizer.resize');
                }
            }
            ZywxappLog::getInstance()->write('INFO', 'About to resize' . $image.' with:: '.$type, 'ZywxappPhpThumbResizer.resize');
			$thumb->$type($width, $height);
            ZywxappLog::getInstance()->write('INFO', 'Resized object for: ' . $image.' is ready', 'ZywxappPhpThumbResizer.resize');
            
            $size = $thumb->getCurrentDimensions();
            $this->newHeight = $size['height'];
            $this->newWidth = $size['width'];
            
            $this->thumb = $thumb;

            if ( $save_image ){
            	ZywxappLog::getInstance()->write('INFO', 'Saving the image ' . $image.' to the file:: '.$file, 'ZywxappPhpThumbResizer.resize');
                $thumb->save($file);

                // Convert the cache filesystem path to a public url
                $url = str_replace(ZYWX_ABSPATH, get_bloginfo('wpurl') . '/', $file);
                $url = str_replace('\\', '/', $url);
            } else {
                $url = FALSE;
            }

            ZywxappLog::getInstance()->write('info', 'After thumb resize: ' . $image, 'ZywxappPhpThumbResizer.resize');
        }
        catch (Exception $e) {
             ZywxappLog::getInstance()->write('error', 'Error resizing: ' . $e->getMessage(),'ZywxappPhpThumbResizer.resize');
        }
        ZywxappLog::getInstance()->write('INFO', 'Done resizing the image::' . $image.' the url should be:: '.$url, 'ZywxappPhpThumbResizer.resize');
        return $url;
    } 
    
    public function show()
    {
        if ($this->thumb != null){
            $this->thumb->show();
        } else {
            ZywxappLog::getInstance()->write('ERROR', 'can not show blank thumbnail', 'ZywxappPhpThumbResizer.show');
        }
    }   
}