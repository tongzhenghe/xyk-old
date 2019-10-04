<?php
    /**
     * Created by PhpStorm.
     * User: Administrator
     * Date: 2019/1/6 0006
     * Time: 上午 10:34
     */
    
    namespace app\admin\model;
    
    use think\Cookie;
    use think\Db;
    use think\Model;
    use think\Session;
    class Admin extends  Model
    {
      
      static  public  function  valid_admin( $data  )
        {
            $username = trim( $data['username']);
            $password = trim($data['password']);
            $repassword = trim($data['repassword']);
    
            if (strlen($username) >= 5 ) {
                
                if( preg_match("/^[A-Za-z0-9]+$/", $username)) {
                    if ($password  === $repassword) {
                        
                        return true;
                        
                    } else {
    
                        exit(return_json('', 400, false, '两次密码输入不一致'));
                        
                    }
    
                }  else {
                    
                    exit(return_json('', 400, false, '用户名不能包含特殊字符！'));
                    
                }
            
            } else {
                
                exit(return_json('', 400, false, '用户名长度不够！'));
                
            }
            
        }
        
        
        static  public  function  update_admin( $userInfo )
        {
           $oldpassword = md5( $userInfo['oldpassword']);
            $userInfo  = Db::name('admin')->where( 'username' , trim( $userInfo['username']))->find();

            if ( !empty($userInfo) && $userInfo['password'] === $oldpassword ) {
                
                return true;
                
            } else {
                
                exit( return_json('', 400, false, '旧密码验证失败！'));
            }
        
        }
        
        static  public  function  check_login( $_user )
        {
            $second  = Session::get('second');
            
            if ( (int)$second  > 5 ) {

                $second_time = Session::get('second_time');

                if ($second_time  +3600 <= time() ) {

                    Session::delete('second_time');
                    Session::delete('second');

                }

                exit( return_json('', 400, false, '您已超出登录次数， 请于1小时后在尝试登录！'));
            }
        
            if ( !empty($_user)  || is_array( $_user )) {
                //验证
                $userInfo = Db::name('admin')->where('username',  trim($_user['username']))->find();

                if (!empty($userInfo)) {
    
                    if ( md5( $_user['password'] )  === $userInfo['password']) {
                        
                        Session::set('userInfo', $userInfo );
                        //记住密码？
                        if ( intval( $_user['is_remember'] ) == 1 ) {
                            
                            Cookie::set('username', $_user['username'], time() + 3600 * 7 );
                            Cookie::set('password', md5( $_user['password'] ),  time() + 3600 * 7 );
                            Cookie::set('is_remember', $_user['is_remember'] ,  time() + 3600 * 7 );
                        } else {
                            Cookie::delete('username');
                            Cookie::delete('password');
                            Cookie::delete('is_remember' );
                        }
                        return true;
                        
                    } else {
        
                        Session::set('second', $second +1);
                        Session::set('second_time',  time());
        
                        exit( return_json('', 400, false, '密码输入错误！'));
                    }
                } else {
    
                    exit( return_json('', 400, false, '用户不存在！'));
                }
            } else {
                
                exit( return_json('', 400, false, '数据错误！'));
                
            }
        }
        
    }