<?php

namespace linkphp\verify;

class Captcha
{

    //字体文件
    private $font_file = EXTRA_PATH . 'fonts/1.ttf';

    //字体大小
    private $size = 20;

    //画布宽度
    private $width = 120;

    //画布高度
    private $height = 40;

    //验证码长度
    private $length = 4;

    //画布资源
    private $image = null;

    //干扰元素
    //雪花个数
    private $snow = 50;

    //像素个数
    private $pixel = 10;

    //线条数
    private $line = 3;

    // 验证码字体，不设置随机获取
    private $bg = [243, 251, 254];

    //验证码字体颜色
    private $color;

    public function __construct($config = [])
    {
        //检测字体文件是否存在并且刻度
        if(isset($config['font_file'])&&is_file($config['font_file'])&&is_readable($config['font_file'])){
            $this->font_file = $config['font_file'];
        }
        //检测是否设置画布宽
        if(isset($config['width'])&&$config['width']>0){
            $this->width = (int)$config['width'];
        }
        //检测是否设置画布高
        if(isset($config['height'])&&$config['height']>0){
            $this->height = (int)$config['height'];
        }
        //检测是否设置验证码长度
        if(isset($config['length'])&&$config['length']>0){
            $this->length = (int)$config['length'];
        }
        //配置干扰元素
        if(isset($config['snow'])&&$config['snow']>0){
            $this->snow = (int)$config['snow'];
        }
        if(isset($config['pixel'])&&$config['pixel']>0){
            $this->pixel = (int)$config['fixel'];
        }
        if(isset($config['line'])&&$config['line']>0){
            $this->line = (int)$config['line'];
        }
        $this->image = imagecreatetruecolor($this->width, $this->height);
    }

    public function getCaptcha()
    {
        /**
         * 得到验证码
         */
        imagecolorallocate($this->image, $this->bg[0], $this->bg[1], $this->bg[2]);
        // 验证码字体随机颜色
        $this->color = imagecolorallocate($this->image, mt_rand(1, 150), mt_rand(1, 150), mt_rand(1, 150));
        //填充矩形
        imagefilledrectangle($this->image, 0, 0, $this->width, $this->height, $this->color);
        //生成验证码
        $str = $this->_generateStr($this->length);
        if(false === $str){
            return FALSE;
        }
        //绘制验证码
        for($i=0;$i<$this->length;$i++){
            $size = $this->size;
            $angle = mt_rand(-30,30);
            $x = ceil($this->width/$this->length)*$i+mt_rand(5,10);
            $y = ceil($this->height/1.5);
            $color = $this->_getRandColor();
            $text = $str{$i};
            imagettftext($this->image, $size, $angle, $x, $y, $color, $this->font_file, $text);
        }
        //雪花、像素、线段
        if($this->snow){
            //使用雪花当做干扰元素
            $this->_getSnow();
        } else {
            if($this->pixel){
                $this->_getPixel();
            }
            if($this->line){
                $this->_getLine();
            }
        }
        //输出图像
        header('Content-Type:image/png');
        imagepng($this->image);
        imagedestroy($this->image);
    }

    /**
     * 产生验证码字符
     * @param integer $length 验证码长度
     * @param string 随机字符
     * @return bool|string
     */
    private function _generateStr($length=4)
    {
        if($length<1 || $length>30){
            return FALSE;
        }
        $chars = ['a','b','c','d','f','g','2','3','4'];
        $str = join('',array_rand(array_flip($chars),$length));
        return $str;
    }

    /**
     * 产生雪花
     */
    private function _getSnow()
    {
        for($i=1;$i<=$this->snow;$i++){
            imagestring($this->image, mt_rand(1,5), mt_rand(0,$this->width), mt_rand(0,$this->height),
                '*', $this->_getRandcolor());
        }
    }

    /**
     * 绘制像素
     */
    private function _getPixel()
    {
        for($i=1;$i<=$this->pixel;$i++){
            imagesetpixel($this->image, mt_rand(0,$this->width), mt_rand(0,$this->height), $this->_getRandColor());
        }
    }
    /**
     * 绘制线段
     */
    private function _getLine()
    {
        for($i=1;$i<=$this->line;$i++){
            imageline($this->image, mt_rand(0,$this->width), mt_rand(0,$this->height), mt_rand(0,$this->width), mt_rand(0,$this->height), $this->_getRandColor());
        }
    }
    /**
     * 随机颜色
     * @return int
     */
    private function _getRandColor()
    {
        return imagecolorallocate($this->image, mt_rand(0,255), mt_rand(0,255), mt_rand(0,255));
    }

}