<?php

namespace maidea;

class helpers
{
    public function fetchFile($url, $params)
    {
        //TODO check status
        $conn = curl_init($url);
        curl_setopt($conn, CURLOPT_POSTFIELDS, $params);
        curl_setopt($conn, CURLOPT_RETURNTRANSFER, true);
        $ret = curl_exec($conn);
        curl_close($conn);
        return $ret;
    }
}