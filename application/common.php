<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 流年 <liu21st@gmail.com>
// +----------------------------------------------------------------------
    
    use think\Cache;

// 应用公共文件
    
    /**
     * @param $value
     */

if (request()->isMobile()) {
    \think\Route::domain('xinyikangyl.com', 'index/ukie');
}

\think\Route::domain('wap.xinyikangyl.com', 'index/ukie');

    function wl_debug( $value )
    {
        echo '<pre>';
        print_r( $value );
        echo '<pre>';
        exit;
    }
    

    function  wl_debug_log( $files  , $key  = '' )
    {
        $files = [$key ? $key : date('Y-m-d H : s', time()) => $files];
        $dir = fopen("../application/admin/error.txt", "w") or die("Unable to open file!");
        
        fwrite($dir,  print_r($files, true ) );
        
        fclose($dir);

    }
    
    function jsondebug( $array )
    {
        exit(json_encode(['json' => $array]));
    }
    


    /**
     * @param $file
     * @return bool
     */
    function upload($file) {
        $file = $file['file'];
        //目录存储
        $up_loadpath = './';
        
        $sub_dir = 'uploads';
        
        $date = date('Ymd');
    
        if(!is_dir($up_loadpath.$sub_dir )) {
            
            mkdir($up_loadpath.$sub_dir,  0777);
            
        }
        
        $newPath = $up_loadpath.$sub_dir.'/'.$date;
    
        if(!is_dir(  $newPath )) {
            
            mkdir( $newPath, 0777);
            
        }
        
        $prefix = 'xy_';
        
        $ext = strtolower(strrchr($file['name'],'.'));
        
        $name = str_replace('.',  '', uniqid($prefix,true)).$ext;
        
        $name = trim($name);
        
        if(move_uploaded_file($file['tmp_name'],$newPath . '/' . $name)) {
            
            $newPath = str_replace('.', '', $newPath);
            
            exit( json_encode( ['file' => $newPath.'/'.$name ]));
            
        }  else {
            
            return false;
            
        }
    }
    
    /**
     * @param null $url
     * @param int $code
     * @param bool $status
     * @param string $msg
     * @return false|string
     */
    function return_json( $url = null , $code = 200,  $status = true, $msg = '提交成功')
    {
        
        $data = [
            'url' => $url,
            'code' =>$code,
            'status' =>  $status,
            'msg' => $msg,
        ];
        $json =  json_encode($data);
        
     return $json;
        
    }
    
    
    
    /**
     * @param $key
     * @return mixed
     */
    function get_cache( $key )
    {
        $data =   Cache::get( $key );
        
        return $data;
    }
    
    /**
     * @param $key
     * @return bool
     */
    function delete_cache( $key )
    {
        $result =  Cache::rm( $key );
        
        return $result;
        
    }
    
    /**
     * @param $key
     * @param $value
     * @param int $exprie
     */
    function set_cache( $key, $value, $exprie = 0 )
    {
        
     $result =    Cache::set( $key,  $value, $exprie );
     
     return $result;
     
    }
    
    /**
     * @return string
     */
    function get_ip() {
        //strcasecmp 比较两个字符，不区分大小写。返回0，>0，<0。
        if(getenv('HTTP_CLIENT_IP') && strcasecmp(getenv('HTTP_CLIENT_IP'), 'unknown')) {
            $ip = getenv('HTTP_CLIENT_IP');
        } elseif(getenv('HTTP_X_FORWARDED_FOR') && strcasecmp(getenv('HTTP_X_FORWARDED_FOR'), 'unknown')) {
            $ip = getenv('HTTP_X_FORWARDED_FOR');
        } elseif(getenv('REMOTE_ADDR') && strcasecmp(getenv('REMOTE_ADDR'), 'unknown')) {
            $ip = getenv('REMOTE_ADDR');
        } elseif(isset($_SERVER['REMOTE_ADDR']) && $_SERVER['REMOTE_ADDR'] && strcasecmp($_SERVER['REMOTE_ADDR'], 'unknown')) {
            $ip = $_SERVER['REMOTE_ADDR'];
        }
        $res =  preg_match ( '/[\d\.]{7,15}/', $ip, $matches ) ? $matches [0] : '';
        return  $res;
    }
    
    function is_md5($password) {
        return preg_match("/^[a-z0-9]{32}$/", $password);
    }
    
     function  islogin()
    {
        if (empty(\think\Session::get('userInfo'))) {
            if (empty( \think\Cookie::get('username') ) || empty( \think\Cookie::get('password') )) {
                \think\Cookie::delete('username');
                \think\Cookie::delete('password');
                 return false;
            }
            return false;
        }
        return true;
        
    }
    
    if (request()->isMobile()) {
        config('template.view_path','../application/index/view/wap/');
    } else {
        config('template.view_path','');
    }
    
    
    /**
     * @param $data
     * @param int $pid
     * @param int $deep
     * @return array
     */
    function tree($data, $pid = 0, $deep = 0 )
    {
        static $arr = [];
        foreach ($data as $val) {
            if ($val['parentid'] == $pid ) {
                $val['deep'] = $deep;
                $val['html'] = str_repeat('|—',$deep);
                $arr[] = $val;
                $arr = tree($data, $val['id'], $deep+1 );
            }
            
        }
        return $arr;
        
    }
    
    
    /**
     * @param $data
     * @return bool
     */
    function is_serialized_string( $data ) {
        $data = trim( $data );
        if ( preg_match( '/^s:[0-9]+:.*;$/s', $data ) )
            return true;
        return false;
    }


function timeTran($time)
{
    $t = time() - $time;
    $f = array(
        '31536000' => '年',
        '2592000' => '个月',
        '604800' => '星期',
        '86400' => '天',
        '3600' => '小时',
        '60' => '分钟',
        '1' => '秒'
    );
    foreach ($f as $k => $v) {
        if (0 != $c = floor($t / (int)$k)) {
            return $c . $v  .'前';
        }
    }
}

function utf8_sub_str( $text, $statr = 0, $end = 10 )
{
    if (empty($text) ) return false;

    return mb_substr($text, $statr, $end, 'utf-8');

}