<?php  
namespace Src\Helpers;  
class Response{  
  public static function json($data,$code=200){  
    header('Content-Type: application/json');  
    http_response_code($code);  
    echo json_encode(['success'=>$code>=200&&$code<300,'data'=>$data], JSON_UNESCAPED_SLASHES);  
    exit;  
  }  
  public static function jsonError($code,$message,$errors=[]){  
    header('Content-Type: application/json');  
    http_response_code($code);  
    echo json_encode(['success'=>false,'error'=>['code'=>$code,'message'=>$message,'errors'=>$errors]], JSON_UNESCAPED_SLASHES);  
    exit;  
  }  
}
