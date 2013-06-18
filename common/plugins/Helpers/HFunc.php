<?php namespace Helpers;
class HFunc{
   /*
    * Вспомогательные функции
    */
   public static function truncate_phrase(
             $string, 
             $length = 80,
             $etc = '...',
             $charset='UTF-8',
             $break_words = false,
             $middle = false) {

     if ($length == 0) return '';
  
     if (strlen($string) > $length) {
         $length -= min($length, strlen($etc));
         if (!$break_words && !$middle) {
             $string = preg_replace('/\s+?(\S+)?$/', '', 
                              mb_substr($string, 0, $length+1, $charset));
         }
         if(!$middle) {
             return mb_substr($string, 0, $length, $charset) . $etc;
         } else {
             return mb_substr($string, 0, $length/2, $charset) . 
                              $etc . 
                              mb_substr($string, -$length/2, $charset);
         }
     } else {
         return $string;
     }
   }
   
   public static function getSanizitedTitleId($title)
    {
        return preg_replace('/[^a-z0-9\-]/', '', $title);
    }
}

?>