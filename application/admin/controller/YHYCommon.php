<?php
    /**
     * Created by PhpStorm.
     * User: Administrator
     * Date: 2018/12/28 0028
     * Time: 下午 6:24
     */
    namespace app\admin\controller;
    use think\Config;
    use think\Controller;
    use think\Cookie;
    use think\Session;

    class YHYCommon extends  Controller
    {
    
        /**
         * 后台menu
         */
        public  function  _initialize()
        {
            if(!is_dir('./tmp/'))mkdir ('./tmp/', 0700);
            session_save_path('./tmp/');
            
            $search_url = 'login';
            if ( strrpos($_SERVER['PATH_INFO'],  $search_url  ) === false  ) {
        
                if (!islogin()):
            
                    $this->error('请登录', url('admin/index/login'), '',0);
            
                    exit;

                Endif;
        
            }

            if ( !empty(Session::get('userInfo'))) {
                $userInfo = Session::get('userInfo');
                $this->assign('userInfo', $userInfo );
            }
            
            $admin_nav = Config::get('nav_admin');


            $point = request()->param('point');
            if (!empty($point)) {
                $this->assign('point', $point);
            }


            
            $this->assign('admin_nav', $admin_nav );
            
            $this->assign('search_url', $search_url );
            $url  = ( $_SERVER['PHP_SELF'] );
            $this->assign('url', $url );
            return view( 'public/left' );
            
            

        }
    
    }