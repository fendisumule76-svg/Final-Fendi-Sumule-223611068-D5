<?php
namespace Src\Controllers;
use Src\Helpers\Response;

class BaseController {
    protected array $cfg;

    public function __construct(array $cfg){
        $this->cfg = $cfg;
    }

    protected function ok($data = [], $code = 200){
        Response::json($data, $code);
    }

    protected function error($code, $msg, $errors = []){
        Response::jsonError($code, $msg, $errors);
    }
}