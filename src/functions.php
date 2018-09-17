<?php
function pre($data) {
   echo "<pre>";
      print_r($data);
   echo "</pre>";
}



function slugify($string){
        return strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $string), '-'));
    }