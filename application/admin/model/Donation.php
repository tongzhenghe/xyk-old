<?php
    /**
     * Created by PhpStorm.
     * User: Administrator
     * Date: 2018/12/31 0031
     * Time: 下午 3:21
     */
    
    
    namespace app\admin\model;
    
    use think\Model;

    class Donation extends Model
    {
        protected $insert = ['status' => 1];
        
    }