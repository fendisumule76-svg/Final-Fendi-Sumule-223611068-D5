<?php
namespace Src\Controllers;

class HealthController extends BaseController {
    public function show(){
        $this->ok([
            'status' => 'ok',
            'time' => date('c')
        ]);
    }
}