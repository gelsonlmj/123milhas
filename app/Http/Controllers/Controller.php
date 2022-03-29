<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public function responseSuccess($msg = 'Operação realizada com sucesso.', $status = 200)
    {
        return response()->json(['message' => $msg, 'success' => true], $status);
    }

    public function responseDataSuccess($data = [], $msg = 'Operação realizada com sucesso.', $status = 200)
    {
        return response()->json(['data' => $data, 'message' => $msg, 'success' => true,], $status);
    }

    public function responseError($msg = 'Erro ao realizar operação.', $status = 400)
    {
        return response()->json(['message' => $msg, 'success' => false], $status);
    }

}
