<?php
class Validate{
  
  public function empty_check($input,$word){
    if(empty($input)){
      $error=$word."を入力して下さい";
      return $error;
    }
  }

  public function mailformat_check($mail){
    if(!preg_match("/^([a-zA-Z0-9])+([a-zA-Z0-9\?\*\[|\]%'=~^\{\}\/\+!#&\$\._-])*@([a-zA-Z0-9_-])+\.([a-zA-Z0-9\._-]+)+$/",$mail)){
      $error="正しいメールアドレスを入力して下さい";
      return $error;
    }
  }

  public function max_check($input,$int,$word){
    if(mb_strlen($input)>$int){
      $error=$word."は".$int."文字以内にしてください。";
      return $error;
    }
  }

}
?>