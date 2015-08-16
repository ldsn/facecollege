<?php
if(empty($_FILES['face'])){
    echo 'Welcome to <a href="http://sailboat.ldustu.com">LDSN</a> .';
    return;
} else {
    $filename = $_FILES['name'];
    $tmp_name = $_FILES['tmp_name'];


    $tmp_file_name_arr  = explode('.',$filename);
    $new_file_name      = $tmp_file_name_arr[0].'_'.time().'.'.$tmp_file_name_arr[1];
    rename($tmp_name, './uploads/'.$new_file_name);

    $url                = 'http://'.$_SERVER["HTTP_HOST"].'/uploads/'.$new_file_name;
    $result             = array('status'=>1, 'msg'=>'ok', 'info'=>array('url'=>$url));
    if(!stripos($url, 'localhost')){
        $r                  = detect($url);
        if($r['status']==1){
            $result['info']['output']   = $r['output'];
        }
    }   
    header('Content-Type', 'application/json');
    echo json_encode($result);
    return;
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