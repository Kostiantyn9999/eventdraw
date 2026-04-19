<?php
    function random_string($length) {
        $key = '';
        $keys = array_merge(range(0, 9), range('a', 'z'));
    
        for ($i = 0; $i < $length; $i++) {
            $key .= $keys[array_rand($keys)];
        }
    
        return $key;
    }

    $data = [
        "success" => false,
        "error" => ""
    ];

    $target_dir = __DIR__. "/uploads_scenes/";
    $random_prefix = random_string(50);

    $real_target = $target_dir.$random_prefix;

    // $scene_data = json_decode(file_get_contents('php://input'), true);
    $scene_data = file_get_contents('php://input');
    $real_target = $target_dir . $random_prefix .'.eventdraw';

    if (file_put_contents($real_target, $scene_data)) {
        $data['link'] = $random_prefix;
        $data['success'] = true;
    } else {
        $data['error'] =  $real_target;
    }
    
    echo json_encode($data);
?>