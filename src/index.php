<?php
$college = array(
    0=>'文学院',    
	1=>'外国语学院',
    2=>'历史文化学院',
    3=>'马克思主义学院',
    4=>'法学院',
    5=>'教育科学学院',
    6=>'教师教育学院',
    7=>'商学院',
    8=>'数学与统计科学学院',
    9=>'物理与光电工程学院',
    10=>'化学与材料科学学院',
    11=>'生命科学学院',
    12=>'地理与规划学院',
    13=>'交通学院',
    14=>'土木工程学院',
    15=>'信息与电气工程学院',
    16=>'食品工程学院',
    17=>'农学院',
    18=>'艺术学院',
    19=>'体育学院',
    20=>'国际教育学院',
    21=>'蔚山船舶与海洋学院',
    22=>'大学外语教学部',
    23=>'中国思想文化研究院',
    24=>'环渤海发展研究院',
    25=>'菌物科学与技术研究院',
    26=>'胶东文化研究院'
);
$total_college = count($college);


$host_sae = 'http://facecollege-o.stor.sinaapp.com/uploads/';

if(empty($_FILES['face'])){
    echo 'Welcome to <a href="http://sailboat.ldustu.com">LDSN</a> .';
    return;
} else {
    $filename = $_FILES['face']['name'];
    $tmp_name = $_FILES['face']['tmp_name'];

    $tmp_file_name_arr  = explode('.',$filename);
    $new_file_name      = $tmp_file_name_arr[0].'_'.time().'.'.$tmp_file_name_arr[1];
    if(file_exists($_FILES['face']['tmp_name'])){
        savefile($tmp_name, $new_file_name);
    } else {
        $result             = array('status'=>-1, 'msg'=>'上传图片失败');
        echo json_encode( $result );
        return;
    }
    
    $url = $host_sae . $new_file_name;
    $result             = array('status'=>1, 'msg'=>'ok', 'info'=>array('url'=>$url));
    $r                  = detect($url);
    if($r['status']==1){
        if(empty($r['output']['face'])){
        	delfile($new_file_name);
            $result             = array('status'=>-2, 'msg'=>'上传的图片没有脸呀，亲');
            echo json_encode( $result );
            return;
        }
        $college_id = charge($r['output']);
        $result['info']['college']	= array( 'id' => $college_id, 'name'=> $college[$college_id] );
        $result['info']['face']   = $r['output']['face'];
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
    $output = file_get_contents($api_url);
    return array('status'=>1, 'output'=>json_decode($output,true) );
}


function savefile($f, $f1){
	$storage = new SaeStorage();
	$domain = 'o';
    $result = $storage->upload($domain,'/uploads/'.$f1, $f);
}

function delfile($f){
    $s = new SaeStorage();
    $domain = 'o';
    $result = $s->delete($domain, '/uploads/'.$f);
}

function charge($out){
    global $total_college;
    $f = $out['face'][0]['attribute'];
    $total = $f['age']['value'] + getInt($f['gender']['confidence']) + getInt($f['glass']['confidence']) + getInt($f['race']['confidence']) + getInt($f['smiling']['value']);
    return $total % $total_college;
}

function getInt($n){
    $t = intval( $n * 1000 );
    return (100000 - $t);
}
