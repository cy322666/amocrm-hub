<?php


namespace App\Services\Salesforce\Traits;


use App\Services\Salesforce\Services\Auth;

trait Response
{
    public function parseResponse($data, $account)
    {
//        if($data) {
//        dd($data);
//            $code = $data->code();
//
//            if($code == 403) Auth::refresh_access($account);
//
//            if($code == 200) return $data->body();
//
//            if($code == 204) return null;
//
//        } else
//            return null;
    }
}
