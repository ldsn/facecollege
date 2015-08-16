<?php
$http = new swoole_http_server("0.0.0.0", 80);
$http->on('request', function ($request, $response) {
    $header         = $request->header;
    $server         = $request->server;
    switch ($server['request_method']) {
        case 'GET':
            get($request, $response);
            return;
            break;
        case 'POST':
            post($request, $response);
            return;
            break;
    }
});

function get($request, $response){
    //static resource
    $request_uri        = $request->server['request_uri'];
    if(stripos($request_uri, 'uploads')){
        $arr                = explode('/', $request_uri);
        $static_file_name   = $arr[2];
        $mime_type          = _mime_content_type($static_file_name);
        $response->header('Content-Type', $mime_type);
        $static_f           = fopen('./uploads/'.$static_file_name, 'r');
        while (!feof($static_f)) {
            $response->write( fread($static_f, 8192) );
        }
        fclose($static_f);
        $response->end();
        return;
    }
}

function post($request, $response){
    $f = $request->files;
    if(empty($f['faces'])||empty($f['faces']['tmp_name'])){
        $result         = array('status'=>0, 'msg'=>'empty');
        $response->end(json_encode($result));
    } else {
        $tmp_file_name_arr  = explode('.',$f['faces']['name']);
        $new_file_name      = $tmp_file_name_arr[0].'_'.time().'.'.$tmp_file_name_arr[1];
        rename($f['faces']['tmp_name'], './uploads/'.$new_file_name);
        $host_name          = $request->header['host'];
        $url                = 'http://'.$host_name.'/uploads/'.$new_file_name;
        $result             = array('status'=>1, 'msg'=>'ok', 'info'=>array('url'=>$url));
        if(!stripos($url, 'localhost')){
            $r                  = detect($url);
            if($r['status']==1){
                $result['info']['output']   = $r['output'];
            }
        }   
        $response->header('Content-Type', 'application/json');
        $response->end(json_encode($result));
    }
}

function detect($url){
    if(!$url)return array('status'=>-1);
    // your api_key and api_secret
    $api_key = "aae629ef050c44375885b3f053395c17";
    $api_secret = "QoDCrBniMxKnNnV_xaQGIxoRXLMnmbVx";
    $api_url = 'http://apicn.faceplusplus.com/v2/detection/detect';
    $api_url .= '?api_key='.$api_key.'&api_secret='.$api_secret;
    $api_url .= '&url='.urlencode($url);
    $api_url .= '&attribute=glass,pose,gender,age,race,smiling';

    return array('status'=>1, 'output'=>file_get_contents($api_url) );
}

function _mime_content_type($filename) {
    $t = explode('.', $filename);
    switch (strtolower($t[1])) {
        case 'jpg':
            return 'image/jpeg';
            break;
        case 'jpeg':
            return 'image/jpeg';
            break;
        case 'png':
            return 'image/png';
            break;
        
        default:
            return 'image/png';
            break;
    }
}

$http->start();


