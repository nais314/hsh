<?php
/**
 * VER 2019.11.04.
 */
namespace bony;
class bonyutils {
  /**
   * find the base uri - to compose new uri
   */
  public static function baseURI(){
    $puri = parse_url($_SERVER['REQUEST_URI']);
    //$puri['/'] = strpos($_SERVER['REQUEST_URI'],'/');
    $puri['?'] = strpos($_SERVER['REQUEST_URI'],'?');
    $puri['/?'] = strpos($_SERVER['REQUEST_URI'],'/?');

    //echo '<pre>'.var_export($puri,true).'</pre>';

    if($puri['?'] and !$puri['/?']){
      //echo dirname($puri['path']).'/';
      return dirname($puri['path']).'/';
    }else{
      //echo $puri['path'];
      return substr($puri['path'],0,strlen($puri['path'])-1);
    }
  }

  /**
  genid >=PHP7
  */
  public static function genid($len) {
    $a = range('A','Z');
    return $a[array_rand($a)] . bin2hex(random_bytes($len));
  }

    
  public static function toSafeFloat($n) {
      return filter_var($n, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION );
    }
    
    public static function toSafeUrl($url) {
      return filter_var($url,  FILTER_SANITIZE_URL);
    }
    
    public static function toSafeInt($int) {
      return filter_var($int, FILTER_SANITIZE_NUMBER_INT);
    }
    
    
  public static function replace_accents($string)
  {
    return str_replace( array('à','á','â','ã','ä', 'ç', 'è','é','ê','ë', 'ì','í','î','ï', 'ñ', 'ò','ó','ô','õ','ö','ő', 'ù','ú','û','ü','ű', 'ý','ÿ', 'À','Á','Â','Ã','Ä', 'Ç', 'È','É','Ê','Ë', 'Ì','Í','Î','Ï', 'Ñ', 'Ò','Ó','Ô','Õ','Ö','Ő', 'Ù','Ú','Û','Ü','Ű', 'Ý'), array('a','a','a','a','a', 'c', 'e','e','e','e', 'i','i','i','i', 'n', 'o','o','o','o','o','o', 'u','u','u','u','u', 'y','y', 'A','A','A','A','A', 'C', 'E','E','E','E', 'I','I','I','I', 'N', 'O','O','O','O','O','O', 'U','U','U','U','U', 'Y'), $string);
  }


  /* 
  function toSafeFilename($str) {
      return preg_replace('/[\x00-\x1F\x7F]/', '', 
      str_replace(array('?','*','\\','/',"..",'<','>',' ',"'",'"'),'_',
        htmlspecialchars($str,ENT_QUOTES)
      )
      );
    };//&quot;Árvíztűrő__tük_örfúrógx00ép_@_()%_#__.exe
  */
  public static function toSafeFilename($str) {
    return strtr(
          mb_convert_encoding(
            self::replace_accents(
              preg_replace('/[\x00-\x1F\x7F]/', '', $str)
            ),'ASCII'),
      ' ,;:?*#!§$%&/(){}<>=`´|\\\'"',
      '____________________________');
  }


}
?>