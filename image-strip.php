<?php
/*
Plugin Name: Image Strip
Plugin URI: http://pablinos.github.com/
Description: Changes WordPress to proportionally scale and crop images to the same width or height
Author: Paul Bunkham
Version: 0.1
Author URI: http://pablinos.github.com/imagestrip/
*/


new ImageStrip;

class ImageStrip {
  protected $last_uploaded_image_size;

  public function ImageStrip(){
    $this->__construct();
  }

  public function __construct(){  
    add_filter('intermediate_image_sizes_advanced', array(&$this,'handle_image_sizes'));
    add_filter('wp_handle_upload', array(&$this,'upload_handler'));
    //add_filter('image_downsize', array(&$this,'custom_downsizer'), 11,3);
    add_action( 'admin_init', array(&$this,'adminSetup') );
  }  

  function adminSetup(){
    //wp_deregister_script( 'jquery' );
    //wp_register_script(	'jquery', 'http://ajax.googleapis.com/ajax/libs/jquery/1/jquery.min.js','1.x','20101125',0);
    wp_enqueue_script('imagestripadminjs', plugins_url('/js/admin.js', __FILE__), array('jquery'),'1.0',1);

    add_settings_field( 'image-strip-medium', 'Fix medium images dimension', array($this,'optionSelectMedium'), 'media', 'default' );
    add_settings_field( 'image-strip-large', 'Fix large images dimension', array($this,'optionSelectLarge'), 'media', 'default' );
    register_setting('media','image-strip-medium');
    register_setting('media','image-strip-large');
  }

  function optionSelectMedium(){
    $this->outputSelect('image-strip-medium');
  }

  function optionSelectLarge(){
    $this->outputSelect('image-strip-large');
  }

  function outputSelect($size){
    $opt = $this->getSizeOption($size);
    echo "<select name=\"$size\" id=\"$size\">";
    echo "<option value=\"0\" ".($opt==0?'selected':'').">None</option>";
    echo "<option value=\"1\" ".($opt==1?'selected':'').">Width</option>";
    echo "<option value=\"2\" ".($opt==2?'selected':'').">Height</option>";
    echo "</select>";
  }

  function getSizeOption($size){
    $opt=get_option("$size");
    if(empty($opt)) $opt=0;
    return $opt;
  }

  function handle_image_sizes($sizes){

    $lopt = $this->getSizeOption('image-strip-large');
    $mopt = $this->getSizeOption('image-strip-medium');

    if(!($lopt || $mopt)) return $sizes;

    $width = $this->last_uploaded_image_size[0];
    $height = $this->last_uploaded_image_size[1];

    //echo "Width:".$width." height:".$height;

    $mwidth=$sizes['medium']['width'];
    $lwidth=$sizes['large']['width'];
    $mheight=$sizes['medium']['height'];
    $lheight=$sizes['large']['height'];


    if($mopt== 1 && $width>$mwidth){
      $sizes['medium']['height']=intval(($mwidth/$width)*$height);
      $sizes['medium']['crop']=1;
    }

    if($lopt==1 && $width>$lwidth){
      $sizes['large']['height']=intval(($lwidth/$width)*$height);
      $sizes['large']['crop']=1;
    }

    if($mopt== 2 && $height>$mheight){
      $sizes['medium']['width']=intval(($height/$height)*$width);
      $sizes['medium']['crop']=1;
    }

    if($lopt==2 && $height>$lheight){
      $sizes['large']['width']=intval(($lheight/$height)*$width);
      $sizes['large']['crop']=1;
    }

    //print_r($sizes);
    return $sizes;
  }

  function upload_handler($file){
    if ( preg_match('!^image/!', $file['type']) && file_is_displayable_image($file['file']) ) {
      $this->last_uploaded_image_size = getimagesize( $file['file'] );
    }
    return $file;
  } 

  function custom_downsizer($var, $id, $size ) {
    //echo "Downsize:".$size;
    if ( !in_array($size, array('left-image','right-image')) ) {
      return false;
    }
    if( !is_array($imgdata = wp_get_attachment_metadata($id))){
      return array( wp_get_attachment_url($id), '', '' );
    }else{
      //echo "Here:". $imgdata['sizes'][$size]['width']."-".$imgdata['sizes'][$size]['height'];
      //print_r($imgdata['sizes'][$size]);
      return array(wp_get_attachment_url($id), $imgdata['sizes'][$size]['width'],$imgdata['sizes'][$size]['height'],true );
    }
  }


}

?>
