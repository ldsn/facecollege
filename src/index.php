<?php
$hostsae = 'http://facecollege-o.stor.sinaapp.com/';
if(empty($_FILES['face'])){
    echo 'Welcome to <a href="http://sailboat.ldustu.com">LDSN</a> .';
    return;
} else {
    $filename = $_FILES['face']['name'];
    $tmp_name = $_FILES['face']['tmp_name'];
    if(!stripos($_FILES['face']['type'],'image')){
        return;
    }

    $tmp_file_name_arr  = explode('.',$filename);
    $new_file_name      = $tmp_file_name_arr[0].'_'.time().'.'.$tmp_file_name_arr[1];
    if(file_exists($_FILES['face']['tmp_name'])){
        //file_put_contents('uploads/'.$new_file_name, file_get_contents($_FILES['face']['tmp_name']));
        savefile($tmpname, $new_file_name);
    } else {
        echo 'no';
        return;
    }
    
    //$url                = 'http://'.$_SERVER["HTTP_HOST"].'/uploads/'.$new_file_name;
    $url = $hostsae . $new_file_name;
    $result             = array('status'=>1, 'msg'=>'ok', 'info'=>array('url'=>$url));
    if(!stripos($url, 'localhost')){
        $r                  = detect($url);
        if($r['status']==1){
            $result['info']['output']   = $r['output'];
        }
    }   
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
    $ch = curl_init($url) ;  
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true) ; // 获取数据返回  
    curl_setopt($ch, CURLOPT_BINARYTRANSFER, true) ; // 在启用 CURLOPT_RETURNTRANSFER 时候将获取数据返回  
    $output = curl_exec($ch) ;
    return array('status'=>1, 'output'=>$output );
}


function savefile($f,$f1){
storage = new SaeStorage();
$domain = 'o';
$attr = array('encoding'=>'gzip');
$result = $storage->upload($domain,$f, $f1, -1, $attr, true);
}






