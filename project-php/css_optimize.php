<?php 
$css_cache_file = 'nina@#cache/styles.css';
$css_path = '';
$css_files = array(
    "css/font_awesome/css/font-awesome.css",
    "css/bootstrap/bootstrap.min.css",
    "css/fonts.css",
    "css/style.css",
    "css/style_media.css",
    "css/owl2/owl.carousel.min.css",
    "css/owl2/owl.theme.default.min.css",
    "css/mmenu/jquery.mmenu.all.css",
    "css/magiczoomplus/magiczoomplus.css",
    "css/simplyscroll/jquery.simplyscroll.css",
    "css/wow/animate.min.css",
    "css/jBox.css",
    "css/jBox.Confirm.css",
    "css/fancybox3/jquery.fancybox.min.css"
);
function update() {
  global $css_path, $css_cache_file, $css_files;
  if (file_exists($css_cache_file)) {
    $cache_time = filemtime($css_cache_file);
    foreach ($css_files as $file) {
      if (file_exists($css_path.$file)) {
        $time = filemtime($css_path.$file);
        if ($time > $cache_time) {
          return joinCSSFiles();
          break;
        }
      }
    }
  } else {
    return joinCSSFiles();
  }
  return file_get_contents($css_cache_file);
}
function compressCSS($file) {
  $filedata = file_get_contents($file);
  $filedata = str_replace(array("\r\n", "\r", "\n", "\t", '  ', '    ', '    '), '', $filedata);
  $filedata = preg_replace('!/\*[^*]*\*+([^/][^*]*\*+)*/!', '', $filedata);
    $filedata = str_replace('{ ', '{', $filedata);
    $filedata = str_replace(' }', '}', $filedata);
    $filedata = str_replace('; ', ';', $filedata);
    $filedata = str_replace(', ', ',', $filedata);
    $filedata = str_replace(' {', '{', $filedata);
    $filedata = str_replace('} ', '}', $filedata);
    $filedata = str_replace(': ', ':', $filedata);
    $filedata = str_replace(' ,', ',', $filedata);
    $filedata = str_replace(' ;', ';', $filedata);  
  return $filedata;
}
function joinCSSFiles() {
  global $css_cache_file, $css_files, $css_path;
  $data = '';
  foreach ($css_files as $file) {
    if (file_exists($css_path.$file)) {
      $data .= compressCSS($css_path.$file);
    }
  }
  file_put_contents($css_cache_file, $data);
  return $data;
}
ob_start ("ob_gzhandler");
ob_start("compress");
header("Content-type: text/css;charset: UTF-8");
echo update();
?>