<?php

namespace app\admin\controller;

use app\extra\Qiniu;
use think\Cookie;
use think\Db;
use \app\admin\model\Admin;
use think\Session;

class Index extends  YHYCommon
{
    
    /**
     * 数据概况
     * @return \think\response\View
     */
    public function index()
    {
        return view();
        
    }
    
    /**
     * @return \think\response\View
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @throws \think\exception\PDOException
     */
    public  function  menu()
    {
    
      /*  if (request()->post('do') == 'updtitle') {
            
            $post = request()->post();
    
            $id = empty( $post['id'] ) ?  exit(false)  :  intval( $post['id'] );
            jsondebug($post);
    
            $result = Db::name('menu')->where('id', $id )->update(['title' => trim($post['title'])]);
            
            if(!$result ) {
        
                exit(false);
            }
            
            exit(return_json());
        
        }*/
        if (request()->post('point') == '_delete') {
            
            $id = empty( request()->post('id')  ) ?  exit(false)  :  intval(request()->post('id') );
            
            $result =  Db::name('menu')->where('id', $id)->delete();
            
            if(!$result ) {
                
                exit(false);
            }
            exit(return_json());
        }
        
        
        if (request()->post('do') == '_status') {
            
            if(   empty( request()->post('id')  || empty( request()->post('field')))) {
                exit(false);
            };
            
            $id =  intval(request()->post('id') );
            $field =  trim(request()->post('field') );
            
            $data [$field] = request()->post('value');
            
            $result =  Db::name('menu')->where('id', $id)->update($data);
            
            if(!$result ) {
                
                exit(false);
            }
            
            exit(return_json(url('admin/index/menu')));
        }
        
        if (   !empty( $_GET['point'] )  && trim($_GET['point'] ) == '_add'  ) {
            
            //查询所有分类
            $menucate = Db::name('menu')->where(['status' => 1])->select();
            if (!empty( $menucate )) {
                $this->assign('menucate', $menucate);
            }

            //add页面
            if ( !empty(  request()->post() ) ) {
                $post =  request()->post();
    
                if (empty($post['title'])) {
                    exit(return_json('', 400, false, '名称不能为空！'));
                }
                if (empty($post['icon'])) {
                    exit(return_json('', 400, false, '请上传菜单图标！'));
                }
    
                $data = [
                    'status' => 1,
                    'is_del' => 1,
                    'sort' => trim($post['sort']),
                    'title' => trim($post['title']),
                    'url' => trim($post['url']),
                    'icon' => trim($post['icon']),
                    'parentid' =>  intval($post['pid'])
                ];
                
                if (empty($post['id'])) {
                    $data['create_time'] = time();
                    $result    =     Db::name('menu')->insert($data);
                } else {
                    $data['update_time'] = time();
                    $id = intval($post['id']);
                    $result    =     Db::name('menu')->where('id', $id )->update($data);
                    
                }
                
                if ($result ) {
                    
                    exit( return_json(url('admin/index/menu')));
                    
                }
                exit(false);
            }
            
            if (!empty(request()->get('id') )) {
                $id = request()->get('id');
                $find_data = Db::name('menu')->where('id', $id)->find();
                $this->assign('find_data', $find_data);
            }

            return view('index/addmenu');
        }
        
        //list
        $list = Db::name('menu')->where('is_del',1)->paginate(10, true, ['var_page' => 'page'  ]);
        
        $co = Db::name('menu')->where('is_del',1)->field('count(id) as total')->find();
    
        $pages = $list->render();
        
        $total = intval($co['total'] );
        
        $this->assign('total', $total);
    
        $list  = tree($list);
    
        $this->assign('list', $list);
        
        $page = request()->get('page') ? request()->get('page') : 1;
        
        $this->assign('page', $page);
        $this->assign('pages', $pages);
        
        return view();
        
    }
    
    /**
     * @return \think\response\View
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @throws \think\exception\PDOException
     */
    
    public  function  banner()
    {
        if (request()->post('point') == '_delete') {
            
            $id = empty( request()->post('id')  ) ?  exit(false)  :  intval(request()->post('id') );
            
            $result =  Db::name('banner')->where('id', $id)->delete();
            
            if(!$result ) {
                
                exit(false);
            }
            exit(return_json());
        }
        
        
        if (request()->post('do') == '_status') {
            
            if(   empty( request()->post('id')  || empty( request()->post('field')))) {
                exit(false);
            }
            
            $id =  intval(request()->post('id') );
            $field =  trim(request()->post('field') );
            
            $data [$field] = request()->post('value');
            
            $result =  Db::name('banner')->where('id', $id)->update($data);
            
            if(!$result ) {
                
                exit(false);
            }
            
            exit(return_json(url('admin/index/banner')));
        }
        
        if (   !empty( $_GET['point'] )  && trim($_GET['point'] ) == '_add'  ) {
                //add页面
            $post =  request()->post();
            if ( !empty( $post) ) {
                if (empty($post['intro'])) $post['intro'] = null;
                if (empty($post['keywords'])) $post['keywords'] = null;

                if (empty($post['title'])) {
                    exit(return_json('', 400, false, '名称不能为空！'));
                }
                 if (empty($post['content'])) {
                    exit(return_json('', 400, false, '详情不能为空！'));
                }
                if (empty($post['pic'])) {
                    exit(return_json('', 400, false, '请上传幻灯片！'));
                }
                
                $data = [
                    'title' => trim($post['title']),
                    'intro' => trim($post['intro']),
                    'keywords' => trim($post['keywords']),
                    'status' => 1,
                    'is_del' => 1,
                    'sort' => trim($post['sort']),
                    'content' => htmlspecialchars($post['content']),
                    'pic' => trim($post['pic']),
                ];

                if (empty($post['id'])) {
                    $data['create_time'] = time();
                    $result    =     Db::name('banner')->insert($data);
                } else {
                    $data['update_time'] = time();
                    $id = intval($post['id']);
                    $result    =     Db::name('banner')->where('id', $id )->update($data);
                    
                }
                
                if ($result ) {
                    
                    exit( return_json(url('admin/index/banner')));
                    
                }
                exit(false);
            }
            if (!empty(request()->get('id') )) {
                $id = request()->get('id');
                $find_data = Db::name('banner')->where('id', $id)->find();
                $find_data['content'] = html_entity_decode($find_data['content']);
                $this->assign('find_data', $find_data);
            }
            
            return view('index/addbanner');
        }
        
        //list
        $list = Db::name('banner')->where('is_del',1)->paginate(10, true, ['var_page' => 'page'  ]);
        
        $co = Db::name('banner')->where('is_del',1)->field('count(id) as total')->find();
        
        $total = intval($co['total'] );
        
        $this->assign('total', $total);
        
        $this->assign('list', $list);
        
        $page = request()->get('page') ? request()->get('page') : 1;
        
        $this->assign('page', $page);
        
        return view();
        
    }
    
    
    
    
    /**
     * @return \think\response\View
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @throws \think\exception\PDOException
     */
    public function contact()
    {
        $tablename = 'contact';
        $post =  request()->post();
        if ( !empty( $post ) ) {
            if (empty($post['title'])) $post['title'] = null;
            if (empty($post['intro'])) $post['intro'] = null;
            if (empty($post['keywords'])) $post['keywords'] = null;

            if ( empty( $post['contacts_name']) ) {
            exit(return_json('', 400, false, '联系人不能为空！'));
            }
             if ( empty( $post['corporate_culture']) ) {
            exit(return_json('', 400, false, '联系人不能为空！'));
            }

            $preg_email='/^[a-zA-Z0-9]+([-_.][a-zA-Z0-9]+)*@([a-zA-Z0-9]+[-.])+([a-z]{2,5})$/ims';
            if(!preg_match($preg_email, trim($post['email']))) {
                exit(return_json('', 400, false, '邮箱为空或格式不正确！'));
            }
            
            $data = [
                'title' => trim($post['title']),
                'intro' => trim($post['intro']),
                'keywords' => trim($post['keywords']),
                'contacts_name' => trim($post['contacts_name']),
                'wechat_code' => trim($post['wechat_code']),
                'update_time' => time(),
                'contacts_tel' => $post['contacts_tel'],
                'email' => trim($post['email']),
                'corporate_culture' =>htmlspecialchars( $post['corporate_culture']),
                'content' =>htmlspecialchars( $post['content']),
            ];

            if (empty($post['id'])) {
                $result    =     Db::name($tablename)->insert($data);
            } else {
                $id = intval($post['id']);
                $result    =     Db::name($tablename)->where('id', $id )->update($data);
            }
        
            if ($result ) {
                delete_cache($tablename);
                exit( return_json(url('admin/index/contact')));
            
            }
            exit(false);
        }
        
        $contact = Db::name($tablename)->find();
        $contact['corporate_culture'] = html_entity_decode($contact['corporate_culture']);
        $contact['content'] = html_entity_decode($contact['content']);

        if (!empty($contact)) {
            $this->assign('data',  $contact );
        }
        
        return view();
        
    }

    /**
     * @return \think\response\View
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @throws \think\exception\PDOException
     */
    public  function  donation()
    {
        if (request()->post('point') == '_delete') {
          
            $id = empty( request()->post('id')  ) ?  exit(false)  :  intval(request()->post('id') );

            $result =  Db::name('donation')->where('id', $id)->delete();
                        
            if(!$result ) {
                
                exit(false);
            }
            exit(return_json());
        }
        if (request()->post('do') == '_status') {
            
            if(   empty( request()->post('id')  || empty( request()->post('field')))) {
                exit(false);
            };
         
            $id =  intval(request()->post('id') );
            $field =  trim(request()->post('field') );
            
            $data [$field] = request()->post('value');
            
            $result =  Db::name('donation')->where('id', $id)->update($data);
            
            if(!$result ) {
                
                exit(false);
            }
        
         exit(return_json(url('admin/index/donation')));
        }
        
        //status
        
        if (   !empty( $_GET['point'] )  && trim($_GET['point'] ) == '_add'  ) {
            //add页面
            //添加
            
            if ( !empty(  request()->post() ) ) {
                $post =  request()->post();

                if (empty($post['time'])) {
                    exit(return_json('', 400, false, '请填写捐助时间！'));
                }

                $d_money = serialize(['m_price' => $post['m_price'], 'm_comment' => $post['m_comment'] ?  trim( $post['m_comment']) : '']);
                if (empty($d_money)) {
                    exit(return_json('', 400, false, '请填写捐助项！'));
                }

                if (empty($post['title'])) $post['title'] = null;
                if (empty($post['intro'])) $post['intro'] = null;
                if (empty($post['keywords'])) $post['keywords'] = null;

                $data = [
                    'title' => trim($post['title']),
                    'intro' => trim($post['intro']),
                    'keywords' => trim($post['keywords']),
                    'status' => 1,
                    'is_del' => 1,
                    'time' => strtotime($post['time']),// 捐助时间
                    'user_name' => $post['user_name'],
                    'pic' => $post['img'],
                    'user_tel' => $post['user_tel'],
                    'd_money' => $d_money,
                ];

                if (empty($post['id'])) {
                    $result    =     Db::name('donation')->insert($data);
                } else {
                    $id = intval($post['id']);
                    $result    =     Db::name('donation')->where('id', $id )->update($data);

                }

                if ($result ) {
                
                  exit( return_json(url('admin/index/donation')));
                
                }
                 exit(false);
            }
            if (!empty(request()->get('id') )) {
                $id = request()->get('id');
                $find_data = Db::name('donation')->where('id', $id)->find();
                $find_data['time'] =  date( 'Y-m-d',  $find_data['time']);
                $find_data['d_money'] =  unserialize( $find_data['d_money']);
                $this->assign('find_data', $find_data);
            }
            
            return view('index/adddonation');
        }
    
        //list
        $list = Db::name('donation')->where('is_del',1)->paginate(10, true, ['var_page' => 'page'  ]);
        
        $co = Db::name('donation')->where('is_del',1)->field('count(id) as total')->find();
        
        $total = intval($co['total'] );
        
        $this->assign('total', $total);

        $this->assign('list', $list);
        
        $page = request()->get('page') ? request()->get('page') : 1;
        
        $this->assign('page', $page);
        
        return view();
    
    }
    
    /**
     * @return \think\response\View
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @throws \think\exception\PDOException
     */
    public  function  activity()
    {
        if (request()->post('point') == '_delete') {
          
            $id = empty( request()->post('id')  ) ?  exit(false)  :  intval(request()->post('id') );

            $result =  Db::name('activity')->where('id', $id)->delete();
            
            if(!$result ) {
                
                exit(false);
            }
            exit(return_json());
        }
                if (request()->post('do') == '_status') {
                    
                    if(   empty( request()->post('id')  || empty( request()->post('field')))) {
                        exit(false);
                    };
    
                    $id =  intval(request()->post('id') );
                    $field = trim(  request()->post('field'));
                    $data [$field] = request()->post('value');
                    
                    $result =  Db::name('activity')->where('id', $id)->update($data);
                    
                    if(!$result ) {
                        
                        exit(false);
                    }
                
                 exit(return_json(url('admin/index/activity')));
        }
        
        if (   !empty( $_GET['point'] )  && trim($_GET['point'] ) == '_add'  ) {
            $post =  request()->post();

            if ( !empty( $post ) ) {
                if (empty($post['title'])) $post['title'] = null;
                if (empty($post['intro'])) $post['intro'] = null;
                if (empty($post['keywords'])) $post['keywords'] = null;
                $data = [
                    'title' => trim($post['title']),
                    'intro' =>  $post['intro'],
                    'keywords' => trim($post['keywords']),
                    'cateid' =>intval( $post['cateid']),
                    'status' => 1,
                    'is_del' => 1,
                    'icon' => trim($post['icon']),
                    'content' =>  htmlspecialchars( $post['content']),
                    'is_recommend' => intval($post['is_recommend']),
                ];

                if (empty($post['id'])) {
                    $data[ 'create_time']= time();
                    $result = Db::name('activity')->insert($data);
                } else {
                    $data[ 'update_time']= time();
                    $id = intval($post['id']);
                    $result = Db::name('activity')->where('id', $id )->update($data);
                }
    
                if ($result ) {
                
                  exit( return_json(url('admin/index/activity')));
                
                }
                 exit(false);
            }
            
            
            //分类、
            $cate_activity  =  Db::name('activity_category')->where('status' , 1)->field('title, id')->where('is_del', 1)->select();
            if (!empty($cate_activity)) {
                $this->assign('cate_activity', $cate_activity);
            }
            
           
            if (!empty(request()->get('id') )) {
                $id = request()->get('id');
                $find_data = Db::name('activity')->where('id', $id)->find();
                $find_data['content'] = html_entity_decode($find_data['content']);
                $this->assign('find_data', $find_data);
            }
            
            return view('index/addactivity');
        }
        
        //分类添加
        $cate_tablename = 'activity_category';
        if (request()->get('point') == 'category_add') {
            
            if (request()->isAjax()) {
                
                $post =  request()->post();
        
                if (empty($post['title'])) {
                    exit(return_json('', 400, false, '名称不能为空！'));
                }
        
                if (empty($post['image'])) {
                    exit(return_json('', 400, false, '图片不能为空！'));
                }
        
                $data = [
                    'title' => trim($post['title']),
                    'intr' => serialize($post['intr']),
                    'image' => trim($post['image']),
                    'status' => 1,
                    'is_del' => 1,
                    'add_time' => time(),
                    'sort'  => intval($post['sort'])
                ];
        
                if (empty($post['id'])) {
                    
                    $result    =     Db::name($cate_tablename)->insert($data);
                    
                } else {
                    
                    $id = intval($post['id']);
                    
                    $result    =     Db::name($cate_tablename)->where('id', $id )->update($data);
                    
                }
        
                if (!empty($result ) ) {
                    delete_cache($cate_tablename);
                    exit( return_json(url('admin/index/activity')));
                }
                exit(false);
            }
    
            if (!empty(request()->get('id') )) {
                $id = request()->get('id');
                $cate_find = Db::name($cate_tablename)->where('id', $id)->find();
                $cate_find['intr'] = unserialize($cate_find['intr']);
                $this->assign('cate_find', $cate_find);
            }
    
            return view('index/addactivitycategory');
         
        }
        
        /*cate_list*/
        $cate_list = Db::name($cate_tablename)->where('is_del',1)->paginate(10, true, ['var_page' => 'page'  ]);
    
        $cate_co = Db::name($cate_tablename)->where('is_del',1)->field('count(id) as total')->find();
    
        $cate_total = intval($cate_co['total'] );
    
        if (!empty($cate_total)) {
            $this->assign('cate_total', $cate_total);
        }
    
        if (!empty($cate_list)) {
            $this->assign('cate_list', $cate_list);
        }
    
        $cate_page = request()->get('page') ? request()->get('page') : 1;
    
        $this->assign('cate_page', $cate_page);
        
        //list
        $list = Db::name('activity')->where('is_del',1)->paginate(10, true, ['var_page' => 'page'  ]);

        $co = Db::name('activity')->where('is_del',1)->field('count(id) as total')->find();
        
        $total = intval($co['total'] );
        
        $this->assign('total', $total);
    
        $this->assign('list', $list);
    
        $page = request()->get('page') ? request()->get('page') : 1;
        
        $this->assign('page', $page);
        
        return view();
    
    }
    
    
    /**
     * @return \think\response\View
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @throws \think\exception\PDOException
     */
    public  function  team()
    {
        if (request()->post('point') == '_delete') {

            $id = empty( request()->post('id')  ) ?  exit(false)  :  intval(request()->post('id') );

            $result =  Db::name('team')->where('id', $id)->delete();

            if(!$result ) {

                exit(false);
            }
            exit(return_json());
        }
                if (request()->post('do') == '_status') {
                    
                    if(   empty( request()->post('id')  || empty( request()->post('field')))) {
                        exit(false);
                    };
    
                    $id =  intval(request()->post('id') );
                    $field = trim(  request()->post('field'));
                    $data [$field] = request()->post('value');
                    
                    $result =  Db::name('team')->where('id', $id)->update($data);
                    
                    if(!$result ) {
                        
                        exit(false);
                    }
                
                 exit(return_json(url('admin/index/team')));
        }
        
        //status
        if (   !empty( $_GET['point'] )  && trim($_GET['point'] ) == '_add'  ) {
            $post =  request()->post();
            if ( !empty($post) ) {
                if (empty($post['title'])) $post['title'] = null;
                if (empty($post['intro'])) $post['intro'] = null;
                if (empty($post['keywords'])) $post['keywords'] = null;
                $data = [
                    'title' => trim($post['title']),
                    'intro' => trim($post['intro']),
                    'keywords' => trim($post['keywords']),
                    'sort' => intval($post['sort']) ,
                    'name' => trim($post['name']) ,
                    'icon' => trim($post['icon']),
                    'status' => 1,
                    'is_del' => 1,
                    'position' =>  trim( $post['position']),
                    'Education_level' =>  trim( $post['Education_level']),
                    'description' =>  serialize( $post['description']),
                    'is_recommend' => intval($post['is_recommend']),
                ];

                if (empty($post['id'])) {
                    $data[ 'create_time']= time();
                    //wl_debug_log($data);
                    $result    =     Db::name('team')->insert($data);
                } else {
                    $data[ 'update_time']= time();
                    $id = intval($post['id']);
                    $result    =     Db::name('team')->where('id', $id )->update($data);
    
                }
    
                if ($result ) {
                
                  exit( return_json(url('admin/index/team')));
                
                }
                 exit(false);
            }
            if (!empty(request()->get('id') )) {
                $id = request()->get('id');
                $find_data = Db::name('team')->where('id', $id)->find();
                $find_data['description'] = unserialize($find_data['description']);
                $this->assign('find_data', $find_data);
            }
            
            return view('index/addteam');
        }
    
        //list
        $list = Db::name('team')->where('is_del',1)->order('sort asc')->paginate(10, true, ['var_page' => 'page'  ]);

        $co = Db::name('team')->where('is_del',1)->field('count(id) as total')->find();
        
        $total = intval($co['total'] );
        
        $this->assign('total', $total);
        
        $this->assign('list', $list);
        
        $page = request()->get('page') ? request()->get('page') : 1;
        
        $this->assign('page', $page);
        
        return view();
    
    }
    
    //shop
    public  function  bed()
    {
        if (request()->post('point') == '_delete') {
        
            $id = empty( request()->post('id')  ) ?  exit(false)  :  intval(request()->post('id') );
        
            $result =  Db::name('bed')->where('id', $id)->delete();
        
            if(!$result ) {
            
                exit(false);
            }
            exit(return_json());
        }
    
        if (request()->post('do') == '_status') {
        
            if(   empty( request()->post('id')  || empty( request()->post('field')))) {
                exit(false);
            }
        
            $id =  intval(request()->post('id') );
            $field =  trim(request()->post('field') );
        
            $data [$field] = request()->post('value');
        
            $result =  Db::name('bed')->where('id', $id)->update($data);
        
            if(!$result ) {
            
                exit(false);
            }
        
            exit(return_json(url('admin/index/bed')));
        }
    
        if (   !empty( $_GET['point'] )  && trim($_GET['point'] ) == '_add'  ) {
            
            if ( !empty(  request()->post() ) ) {
                
                $post =  request()->post();
            
                if (empty($post['title'])) {
                    exit(return_json('', 400, false, '名称不能为空！'));
                }
                
               if (empty($post['price'])) {
                    exit(return_json('', 400, false, '价格不能为空！'));
                }
                
                 if (empty($post['intro'])) {
                    exit(return_json('', 400, false, '简介不能为空！'));
                }
    
                if (empty($post['picArr']) || !is_array($post['picArr'])) {
                    exit(return_json('', 400, false, '请上传床位图片！'));
                }
    
                if (empty($post['content'])) {
                    exit(return_json('', 400, false, '详情不能为空！'));
                }
                
                if (empty($post['icon'])) {
                    exit(return_json('', 400, false, '请上传图标！'));
                }
            
                $data = [
                    'status' => 1,
                    'is_del' => 1,
                    'sort' => trim($post['sort']),
                    'title' => trim($post['title']),
                    'bed' => intval($post['bed']),
                    'is_hot' => intval($post['is_hot']),
                    'is_recommend' => intval($post['is_recommend']),
                    'content' => htmlspecialchars($post['content']),
                    'pic' => serialize($post['picArr']),
                    'icon' => trim($post['icon']),
                    'price' => $post['price'],
                    'intro' => serialize($post['intro']),
                ];
                if (empty($post['id'])) {
                    $data['create_time'] = time();
                    $result    =     Db::name('bed')->insert($data);
                } else {
                    $data['update_time'] = time();
                    $id = intval($post['id']);
                    $result    =     Db::name('bed')->where('id', $id )->update($data);
                
                }
            
                if ($result ) {
                
                    exit( return_json(url('admin/index/bed')));
                
                }
                exit(false);
            }
            
            if (!empty(request()->get('id') )) {
                $id = request()->get('id');
                $find_data = Db::name('bed')->where('id', $id)->find();
                $find_data['content'] = html_entity_decode($find_data['content']);
                $find_data['intro'] = unserialize($find_data['intro']);
                $find_data['pic'] = unserialize($find_data['pic']);
                $this->assign('find_data', $find_data);
            }
        
            return view('index/addbed');
        }
    
    
        $like = '';
        if (!empty(request()->get('keywords'))) {
            $like = trim(request()->get('keywords'));
            $this->assign('like', $like);
        }
        //list
        $list = Db::name('bed')->where('is_del', 1)->where('id|title','like', $like.'%')->paginate(10, true, ['var_page' => 'page'  ]);
        
        $co = Db::name('bed')->where('is_del', 1)->where('id|title','like', $like.'%')->field('count(id) as total')->find();
    
        $total = intval($co['total'] );
    
        $this->assign('total', $total);
    
        $this->assign('list', $list);
    
        $page = request()->get('page') ? request()->get('page') : 1;
    
        $this->assign('page', $page);
    
        return view();
    
    }
    
    
    //环境
      public  function  ambient()
        {
        if (request()->post('point') == '_delete') {
          
            $id = empty( request()->post('id')  ) ?  exit(false)  :  intval(request()->post('id') );

            $result =  Db::name('ambient')->where('id', $id)->delete();
            
            if(!$result ) {
                
                exit(false);
            }
            exit(return_json());
        }
    
        //status
        if (request()->post('do') == '_status') {
            
            $tablename = 'ambient';
            if (request()->get('point') == 'cate_chang') {
                $tablename = 'ambient_category';
            }
    
            if(   empty( request()->post('id')  || empty( request()->post('field')))) {
                exit(false);
            };

            $id =  intval(request()->post('id') );
            $field = trim(  request()->post('field'));
            $data [$field] = request()->post('value');
            $result =  Db::name($tablename)->where('id', $id)->update($data);
            
            if(!$result ) {
                
                exit(false);
            }
        
         exit(return_json(url('admin/index/ambient')));
        }
        
        if (   !empty( $_GET['point'] )  && trim($_GET['point'] ) == '_add'  ) {

            $post =  request()->post();
            if ( !empty($post) ) {
                if (empty($post['intro'])) $post['intro'] = null;
                if (empty($post['keywords'])) $post['keywords'] = null;
                if (empty($post['title'])) exit(return_json('', 400, false, '名称不能为空！'));
                if ( empty($post['cateid']))  exit(return_json('', 400, false, '分类不能为空！'));
                $data = [
                    'intro' => trim($post['intro']),
                    'keywords' => trim($post['keywords']),
                    'cateid' => intval($post['cateid']),
                    'title' => trim($post['title']),
                    'icon' => trim($post['icon']),
                    'status' => 1,
                    'is_del' => 1,
                    'description' =>  serialize( $post['description']),
                    'is_recommend' => intval($post['is_recommend']),
                ];
                
                if (empty($post['id'])) {
                    $data[ 'create_time']= time();
                    $result = Db::name('ambient')->insert($data);
                } else {
                    $data[ 'update_time']= time();
                    $id = intval($post['id']);
                    $result = Db::name('ambient')->where('id', $id )->update($data);
    
                }
    
                if ($result ) {
                
                  exit( return_json(url('admin/index/ambient')));
                
                }
                 exit(false);
            }
    
            //分类
            $cate_ambient = Db::name('ambient_category')->where('status', 1)->where('is_del', 1)->field('id, title')->select();
            if (!empty($cate_ambient)) {
                $this->assign('cate_ambient', $cate_ambient);
            }
            
            //update
            if (!empty(request()->get('id') )) {
                
                $id = request()->get('id');
                
                $find_data = Db::name('ambient')->where('id', $id)->find();
                
                $find_data['description'] = unserialize($find_data['description']);
                
                $this->assign('find_data', $find_data);
                
            }
            
            return view('index/addambient');
        }
    
    
        //分类add
        $cate_tablename = 'ambient_category';
        if (!empty(request()->get('point') == 'category_add')) {
            if (request()->isAjax()) {
                $post =  request()->post();
            
                if (empty($post['title'])) {
                    exit(return_json('', 400, false, '名称不能为空！'));
                }
            
                if (empty($post['image'])) {
                    exit(return_json('', 400, false, '图片不能为空！'));
                }
            
                
                $data = [
                    'title' => trim($post['title']),
                    'intr' => serialize($post['intr']),
                    'image' => trim($post['image']),
                    'status' => 1,
                    'is_del' => 1,
                    'add_time' => time(),
                    'sort'  => intval($post['sort'])
                ];
            
                if (empty($post['id'])) {
                    $result    =     Db::name($cate_tablename)->insert($data);
                } else {
                    $id = intval($post['id']);
                    $result    =     Db::name($cate_tablename)->where('id', $id )->update($data);
                }
            
                if (!empty($result ) ) {
                    delete_cache($cate_tablename);
                    exit( return_json(url('admin/index/ambient')));
                }
                exit(false);
            }
    
    
            if (!empty(request()->get('id'))) {
                $icate_d = intval(request()->get('id'));
                $cate_find = Db::name($cate_tablename)->where('id', $icate_d)->find();
                if (!empty($cate_find)) {
                    $cate_find['intr'] = unserialize($cate_find['intr']);
                    $this->assign('cate_find', $cate_find);
                }
            }
            
        
            return view('index/addambientcategory');
        
        }
    
        //cate_list
        $cate_list = Db::name($cate_tablename)->where('is_del', 1 )->paginate(10,  true,  ['var_page' => 'page'  ]);
    
        $cate_co = Db::name($cate_tablename)->where('is_del',1)->field('count(id) as total')->find();
    
        $cate_total = intval($cate_co['total'] );
    
        if (!empty($cate_total)) {
            
            $this->assign('cate_total', $cate_total);
            
        }
    
        if (!empty($cate_list)) {
            
            $this->assign('cate_list', $cate_list);
            
        }
    
        $cate_page = request()->get('page') ? request()->get('page') : 1;
        
        if (!empty($cate_page)) {
            
            $this->assign('cate_page', $cate_page);
            
        }
    
        //list
        $list = Db::name('ambient')->where('is_del',1)->paginate(10,  true,  ['var_page' => 'page'  ]);

        $co = Db::name('ambient')->where('is_del',1)->field('count(id) as total')->find();
        
        $total = intval($co['total'] );
        
        $this->assign('total', $total);
        
        if (!empty($list)) {
            $this->assign('list', $list);
        }
        
        $page = request()->get('page') ? request()->get('page') : 1;
        
        $this->assign('page', $page);
        
        return view();
    
    }
    
    
    
    
      public  function  process()
    {
        if (request()->post('point') == '_delete') {
          
            $id = empty( request()->post('id')  ) ?  exit(false)  :  intval(request()->post('id') );

            $result =  Db::name('process')->where('id', $id)->delete();
            
            if(!$result ) {
                
                exit(false);
            }
            exit(return_json());
        }
        
        //status
        if (request()->post('do') == '_status') {
            
            if(   empty( request()->post('id')  || empty( request()->post('field')))) {
                exit(false);
            };

            $id =  intval(request()->post('id') );
            $field = trim(  request()->post('field'));
            $data [$field] = request()->post('value');
            
            $result =  Db::name('process')->where('id', $id)->update($data);
            
            if(!$result ) {
                
                exit(false);
            }
        
         exit(return_json(url('admin/index/process')));
        }
        
        //status
        
        if (   !empty( $_GET['point'] )  && trim($_GET['point'] ) == '_add'  ) {
            //添加
            if ( !empty(  request()->post() ) ) {
                
                $post =  request()->post();
                
                $data = [
                    'title' => trim($post['title']) ,
                    'icon' => trim($post['icon']),
                    'status' => 1,
                    'is_del' => 1,
                    'content' =>  serialize( $post['content']),
                ];
                
                //wl_debug_log($post,   'set_');
                
                if (empty($post['id'])) {
                    $data[ 'create_time']= time();
                    $result    =     Db::name('process')->insert($data);
                } else {
                    $data[ 'update_time']= time();
                    $id = intval($post['id']);
                    $result    =     Db::name('process')->where('id', $id )->update($data);
    
                }
    
                if ($result ) {
                
                  exit( return_json(url('admin/index/process')));
                
                }
                 exit(false);
            }
            
            //update
            if (!empty(request()->get('id') )) {
                
                $id = request()->get('id');
                
                $find_data = Db::name('process')->where('id', $id)->find();
                
                $find_data['content'] = unserialize($find_data['content']);
                
                $this->assign('find_data', $find_data);
                
            }
            
            return view('index/addprocess');
        }
    
        //list
        $list = Db::name('process')->where('is_del',1)->paginate(10,  true,  ['var_page' => 'page'  ]);

        $co = Db::name('process')->where('is_del',1)->field('count(id) as total')->find();
        
        $total = intval($co['total'] );
        
        $this->assign('total', $total);
        
        $this->assign('list', $list);
        
        $page = request()->get('page') ? request()->get('page') : 1;
        
        $this->assign('page', $page);
        
        return view();
    
    }
    
    
    /**
     * @return \think\response\View
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @throws \think\exception\PDOException
     */
      public  function  news()
    {
        if (request()->post('point') == '_delete') {
          
            $id = empty( request()->post('id')  ) ?  exit(false)  :  intval(request()->post('id') );

            $result =  Db::name('news')->where('id', $id)->delete();
            
            if(!$result ) {
                
                exit(false);
            }
            exit(return_json());
            
        }
        
        //status
        if (request()->post('do') == '_status') {
            
            if(   empty( request()->post('id')  || empty( request()->post('field')))) {
                exit(false);
            };

            $id =  intval(request()->post('id') );
            $field = trim(  request()->post('field'));
            $data [$field] = request()->post('value');
            
            $result =  Db::name('news')->where('id', $id)->update($data);
            
            if(!$result ) {
                
                exit(false);
            }
        
         exit(return_json(url('admin/index/news')));
        }
        
        //status
        if ( !empty( $_GET['point'] )  && trim($_GET['point'] ) == '_add'  ) {

            $post =  request()->post();
            //添加
            if ( !empty( $post) ) {
                if (empty($post['title'])) $post['title'] = null;
                if (empty($post['intro'])) $post['intro'] = null;
                if (empty($post['keywords'])) $post['keywords'] = null;
                $data = [
                    'title' => trim($post['title']),
                    'intro' => trim($post['intro']),
                    'keywords' => trim($post['keywords']),
                    'icon' => trim($post['icon']),
                    'is_recommend' => intval($post['is_recommend']),
                    'status' => 1,
                    'is_del' => 1,
                    'content' =>  htmlspecialchars( $post['content']),
                ];
                
                if (empty($post['id'])) {
                    $data[ 'create_time']= time();
                    $result    =     Db::name('news')->insert($data);
                } else {
                    $data[ 'update_time']= time();
                    $id = intval($post['id']);
                    $result    =     Db::name('news')->where('id', $id )->update($data);
    
                }
    
                if ($result ) {
                
                  exit( return_json(url('admin/index/news')));
                
                }
                 exit(false);
            }
            
            //update
            if (!empty(request()->get('id') )) {
                
                $id = request()->get('id');
                
                $find_data = Db::name('news')->where('id', $id)->find();
                
                $find_data['content'] = html_entity_decode($find_data['content']);
                
                $this->assign('find_data', $find_data);
                
            }
            
            return view('index/addnews');
        }
    
        //分类add
        $tablename = 'news_category';
        if (!empty(request()->get('point') == 'category_add')) {
            
            if (request()->isAjax()) {
                $post =  request()->post();

                if (empty($post['title'])) {
                    exit(return_json('', 400, false, '名称不能为空！'));
                }
        
             if (empty($post['image'])) {
                 exit(return_json('', 400, false, '图片不能为空！'));
             }
    
             $data = [
                 'parentid' => intval($post['pid']),
                 'title' => trim($post['title']),
                 'intr' => serialize($post['intr']),
                 'image' => trim($post['image']),
                 'status' => 1,
                 'is_del' => 1,
                 'add_time' => time(),
                 'sort'  => intval($post['sort'])
             ];
             
                if (empty($post['id'])) {
                    $result    =     Db::name($tablename)->insert($data);
                } else {
                    $id = intval($post['id']);
                    $result    =     Db::name($tablename)->where('id', $id )->update($data);
                }

                if ($result ) {
                    delete_cache($tablename);
                    exit( return_json(url('admin/index/news')));
                }
                exit(false);
            }
            
            $newscate =  Db::name($tablename)->where('is_del', 1)->select();
            if (!empty($newscate)) {
                $newscate  = tree($newscate);
                $this->assign('newscate', $newscate);
            }
           
           return view('index/addnewscategory');
            
        }
    
        if (!empty(request()->param('point') == 'newchildcate')) {
            
            $id =  intval(request()->param('id'));
    
            if (empty($id)) {
                exit(false);
            }

            //child_cate
            $news_child_cate = Db::name($tablename)->where('is_del',1)->where('parentid', $id)->paginate(10,  true,  ['var_page' => 'page'  ]);
    
            $cateco = Db::name($tablename)->where('is_del',1)->where('parentid', $id )->field('count(id) as catetotal')->find();
    
            $catetotal = intval($cateco['catetotal'] );
    
            $catepages = $news_child_cate->render();
    
            $this->assign('catepages', $catepages);
    
            $this->assign('catetotal', $catetotal);
    
            $this->assign('news_child_cate', $news_child_cate);
    
            $catepage = request()->get('catepage') ? request()->get('catepage') : 1;
    
            $this->assign('catepage', $catepage);
            
            if (!empty($news_child_cate)) {
                $this->assign('news_child_cate', $news_child_cate);
            }
            return view('index/news_child_cate');
            
            
        }
        
    
        //lcate
        $catelist = Db::name($tablename)->where('is_del',1)->where('parentid', 0)->paginate(10,  true,  ['var_page' => 'page'  ]);

        $cateco = Db::name($tablename)->where('is_del',1)->where('parentid', 0)->field('count(id) as catetotal')->find();
    
        $catetotal = intval($cateco['catetotal'] );
        
        $catepages = $catelist->render();
        
        $this->assign('catepages', $catepages);
        
        $this->assign('catetotal', $catetotal);
    
        $catelist = tree($catelist);
        
        $this->assign('catelist', $catelist);
    
        $catepage = request()->get('catepage') ? request()->get('catepage') : 1;
    
        $this->assign('catepage', $catepage);
        
        
        
        //list
        $list = Db::name('news')->where('is_del',1)->paginate(10,  true,  ['var_page' => 'page'  ]);

        $co = Db::name('news')->where('is_del',1)->field('count(id) as total')->find();
        
        $total = intval($co['total'] );
        
        $this->assign('total', $total);
        
        $this->assign('list', $list);
        
        $page = request()->get('page') ? request()->get('page') : 1;
        
        $this->assign('page', $page);
        
        return view();
    
    }
    
    
    /**
     * @return \think\response\View
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @throws \think\exception\PDOException
     */
        public  function  asks()
        {
        $tablename = 'asks';
        
        if (request()->post('point') == '_delete') {
            
            $id = empty( request()->post('id')  ) ?  exit(false)  :  intval(request()->post('id') );
            
            $result =  Db::name($tablename)->where('id', $id)->delete();
            
            if(!$result ) {
                
                exit(false);
            }
            exit(return_json());
            
        }
        
        //status
        if (request()->post('do') == '_status' &&  empty( request()->get('point'))) {
            
            if(   empty( request()->post('id')  || empty( request()->post('field')))) {
                exit(false);
            };
            
            $id =  intval(request()->post('id') );
            $field = trim(  request()->post('field'));
            $data [$field] = request()->post('value');
            
            $result =  Db::name($tablename)->where('id', $id)->update($data);
            
            if(!$result ) {
                
                exit(false);
            }
            
            exit(return_json(url('admin/index/asks')));
        }
    
    
        //cate_status
        if (request()->post('do') == '_status' && request()->get('point') == 'category_add') {
        
            if(   empty( request()->post('id')  || empty( request()->post('field')))) {
                exit(false);
            };
        
            $id =  intval(request()->post('id') );
            $field = trim(  request()->post('field'));
            $data [$field] = request()->post('value');
        
            $result =  Db::name('asks_category')->where('id', $id)->update($data);
        
            if(!$result ) {
            
                exit(false);
            }
        
            exit(return_json(url('admin/index/asks')));
        }
    
        //status
        if (   !empty( $_GET['point'] )  && trim($_GET['point'] ) == '_add'  ) {
            
            //添加
            $post =  request()->post();
            if ( !empty($post) ) {
                if (empty($post['intro'])) $post['intro'] = null;
                if (empty($post['keywords'])) $post['keywords'] = null;

                if (empty($post['title'])) {
                    exit(return_json('', 400, false, '名称不能为空！'));
                }
                if (empty($post['cateid'])) {
                    exit(return_json('', 400, false, '请选择所属分类！'));
                }

                $data = [
                    'title' => trim($post['title']) ,
                    'intro' => trim($post['intro']),
                    'keywords' => trim($post['keywords']),
                    'icon' => trim($post['icon']),
                    'is_recommend' => intval($post['is_recommend']),
                    'is_hot' => intval($post['is_hot']),
                    'cateid' => intval($post['cateid']),
                    'status' => 1,
                    'is_del' => 1,
                    'content' =>  htmlspecialchars( $post['content']),
                ];

                if (empty($post['id'])) {
                    $data[ 'create_time']= time();
                    $result    =     Db::name($tablename)->insert($data);
                } else {
                    $data[ 'update_time']= time();
                    $id = intval($post['id']);
                    $result    =     Db::name($tablename)->where('id', $id )->update($data);
                    
                }
                
                if ($result ) {
                    
                    exit( return_json(url('admin/index/asks')));
                    
                }
                exit(false);
            }


            //分类：
            $asks_category = Db::name('asks_category')->where('status', 1)->select();
           if(!empty($asks_category)) {
               $this->assign('asks_category', $asks_category);
           }
            //update
            if (!empty(request()->get('id') )) {
                $id = request()->get('id');
                $find_data = Db::name($tablename)->where('id', $id)->find();
                $find_data['content'] = html_entity_decode($find_data['content']);
                $this->assign('find_data', $find_data);
            }



            return view('index/addasks');
        }
        
        //分类add
        $asks_catetablename = 'asks_category';
        if (!empty(request()->get('point') == 'category_add')) {
            
            if (request()->isAjax()) {
                $post =  request()->post();
                
                if (empty($post['title'])) {
                    exit(return_json('', 400, false, '名称不能为空！'));
                }
                
                if (empty($post['image'])) {
                    exit(return_json('', 400, false, '图片不能为空！'));
                }
                
                $data = [
                    'title' => trim($post['title']),
                    'intr' => serialize($post['intr']),
                    'image' => trim($post['image']),
                    'status' => 1,
                    'is_del' => 1,
                    'add_time' => time(),
                    'sort'  => intval($post['sort'])
                ];
                
                if (empty($post['id'])) {
                    $result    =     Db::name($asks_catetablename)->insert($data);
                } else {
                    $id = intval($post['id']);
                    $result    =     Db::name($asks_catetablename)->where('id', $id )->update($data);
                }
                
                if ($result ) {
                    delete_cache($asks_catetablename);
                    exit( return_json(url('admin/index/asks')));
                }
                exit(false);
            }
    
            if (!empty(request()->get('id') )) {
        
                $id = request()->get('id');
        
                $find_data = Db::name($asks_catetablename)->where('id', $id)->find();
        
                if (is_serialized_string($find_data['intr'])) {
                    $find_data['intr'] = unserialize($find_data['intr']);
                }
        
                $this->assign('find_data', $find_data);
        
            }
    
    
            return view('index/addaskscategory');
            
        }
        
    
            $cate_like = '';
            if (!empty(request()->get('cate_keywords'))) {
                $cate_like = trim(request()->get('cate_keywords'));
                $this->assign('cate_like', $cate_like);
            }
            
        //lcate
        $catelist = Db::name($asks_catetablename)->where('is_del',1)->where('id|title','like', $cate_like.'%')->paginate(10,  true,  ['var_page' => 'page'  ]);
        
        $cateco = Db::name($asks_catetablename)->where('is_del',1)->where('id|title','like', $cate_like.'%')->field('count(id) as catetotal')->find();
        
        $catetotal = intval($cateco['catetotal'] );
        
        $catepages = $catelist->render();
        
        $this->assign('catepages', $catepages);
        
        $this->assign('catetotal', $catetotal);
        
        $this->assign('catelist', $catelist);
        
        $catepage = request()->get('catepage') ? request()->get('catepage') : 1;
        
        $this->assign('catepage', $catepage);
    
    
        $like = '';
        if (!empty(request()->get('keywords'))) {
            $like = trim(request()->get('keywords'));
            $this->assign('like', $like);
        }
        
        //list
        $list = Db::name($tablename)->where('is_del',1)->where('id|title','like', $like.'%')->paginate(10,  true,  ['var_page' => 'page'  ])
            ->each(function($item, $key){

                $askscate =  Db::name('asks_category')->field('title')->where('id', $item['cateid'])->find();

                $item['cate_title'] = $askscate['title'];

                return $item;

        });

        $co = Db::name($tablename)->where('is_del',1)->field('count(id) as total')->find();
        
        $total = intval($co['total'] );
        
        $this->assign('total', $total);

        $this->assign('list', $list);
        
        $page = request()->get('page') ? request()->get('page') : 1;
        
        $this->assign('page', $page);
        
        return view();
        
    }
    
    
    /**
     *  comon
     */
 /*   public function  uploads()
    {
        if ($_FILES) {
            upload($_FILES);
        }
    }*/

    public function  uploads()
    {
        if ($_FILES) {
            Qiniu::image($_FILES['file']);
        }
    }

    /**
     * @return \think\response\View
     * @throws \think\Exception
     * @throws \think\exception\PDOException
     */
    public  function  update_admin()
    {
        if (request()->post()) {
            $post = request()->post();
            
            if  ( strlen($post['password']) < 5  ) {
                exit( return_json('', 400, false, '密码长度不够！'));
            }
            
            $password =  trim( $post['password']);
            $repassword =  trim( $post['repassword']);
            
            if  ( $password === $repassword ) {

                $result = Admin::update_admin($post);
    
                if ( true === $result ) {
                    //更新数据库
                    $data = [
                        'password' => md5( $password),
                        'userip' => get_ip(),
                        'update_time' => time()
                        ];

                    $update_result = Db::name('admin')->where('username',  trim($post['username']))->update($data);
                    if (!empty($update_result)) {
                      Cookie::delete('userinfo');
                      Session::delete('userinfo');
                      exit(return_json( url('admin/index/login') ));
                    }
                }
                
            } else {
                
                exit( return_json('', 400, false, '两次输入密码不一致！'));
                
            }
        }
        
        return view();
        
    }
    
    
    /**
     * @return \think\response\View
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @throws \think\exception\PDOException
     */
    public  function setting()
    {
        $tablename = 'setting';
        if ( !empty(  request()->post() ) ) {
            $post =  request()->post();
            if ( empty( $post['title']) ) {
                exit(return_json('', 400, false, '网站标题不能为空！'));
            }
            
            if ( empty( $post['keywords']) ) {
                exit(return_json('', 400, false, '关键词不能为空！'));
            }
            
          if ( empty( $post['description']) ) {
            exit(return_json('', 400, false, '网站描述不能为空！'));
            }
        
            $data = [
                'title' => serialize($post['title']),
                'keywords' => serialize($post['keywords']),
                'description' =>serialize($post['description']),
                'time' => time(),
            ];
        
            if (empty($post['id'])) {
                $result    =     Db::name($tablename)->insert($data);
            } else {
                $id = intval($post['id']);
                $result    =     Db::name($tablename)->where('id', $id )->update($data);
            }
        
            if ($result ) {
                delete_cache($tablename);
                exit( return_json(url('admin/index/setting')));
            }
            exit(false);
        }
    
        $setting = Db::name($tablename)->find();
        if (!empty($setting)) {
            $setting['title'] = unserialize($setting['title']);
            $setting['keywords'] = unserialize($setting['keywords']);
            $setting['description'] = unserialize($setting['description']);
            if (!empty($setting)) {
                $this->assign('setting',  $setting );
            }
        }
        
        return view();
    
    }
    
    
    public  function  message()
    {
        $message = Db::name('message')->select();
        if (!empty($message)) {
            $this->assign('message', $message);
        }
        return view();
        
    }
    
    
    
    /**
     * @return \think\response\View
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public  function  admin()
    {
        $userInfo = Db::name('admin')->where('status', 1 )->find();
        
        if ( !empty( $userInfo )) {
            $this->assign('userInfo', $userInfo);
        }
        
        if (request()->post()) {
            
            $post = request()->post();
            
            $result = Admin::valid_admin($post);
            
            if ( true === $result) {
                
                $data = [
                    'status' => 1,
                    'is_del' => 1,
                    'username' => $post['username'],
                    'password' => md5( $post['password'] ),
                    'userip' => get_ip(),
                ];
    
                if (!empty( $post['icon'] )) {
                    $data['icon'] = trim($post['icon']);
                }
                
                if (empty($userInfo)) {
                    $data[ 'create_time']= time();
                    $result    =     Db::name('admin')->insert($data);
                    if ($result ) {
                        exit( return_json());
                    }
                    exit(false);
                }
            }
    
        }
        return view();
        
    }
    
    /**
     * @return \think\response\View
     */
    
    public  function  login()
    {
        $username  = Cookie::get('username');
        $password  = Cookie::get('password');
        
        $is_remember  = Cookie::get('is_remember');

        if ( true === request()->isPost()) {
            
            $user_info = request()->post();
            
            $captcha = trim($user_info['captcha']);
    
            if (empty( $user_info['username'] )) {
                exit(return_json('', 400,  false, '用户名不能为空！'));
            }

             if (empty( $user_info['password'] )) {
                exit(return_json('', 400,  false, '密码不能为空！'));
            }
            
//            if(!captcha_check($captcha)) {
//                //验证失败
//                exit(return_json('', 400,  false, '验证码输入错误'));
//            };
    
            if ( is_md5( $user_info['password'])) {
    
                if (!empty( $password) && !empty($username)) {
    
                    $find = Db::name('admin') ->where(['username' => $username, 'password' => $password])->find();
    
                    if (!empty( $find )) {
                        Session::set('userInfo', $find);
                        exit(return_json(url('admin/index/index'), 200,  true,  '欢迎'. $user_info['username']. '回来!'));
                        
                    } else {
                        exit(return_json('', 400,  false, '请重新输入账号登陆'));
                    }
                    
                }
                
            }
            
            $result   =  Admin::check_login( $user_info );
            
            if (true === $result ) {
                //记录log
                exit(return_json(url('admin/index/index'), 200,  true,  '欢迎'. $user_info['username']. '回来!'));
                
            } else {
    
                exit(return_json('', 400,  false, '系统错误！'));
                
            }
            
        }
    
        if ($username && $password ) {
            $_user['username'] = $username;
            $_user['password'] = $password;
            $_user['is_remember'] = $is_remember;
            $this->assign('_user', $_user);
        }
        
        return view();
        
    }
    
    public  function  logout()
    {
        if ( true == request()->post('logout')) {
            //清除session
            Session::delete('userInfo');
            exit(return_json(url('admin/index/login'), 200, true, '正在退出...'));
        }
       
    }

    
    //统计
    public  function  clean()
    {
    
        if (request()->isAjax()) {
            $do = request()->get('do');
    
            if (empty($do)) {
                exit(false);
            }
    
         switch ($do) {
             
             case 'clean_cache': //清除缓存
                 exit(return_json());
                 break;
             case 'clean_log':
                 exit(return_json());
                 break;
                 
             default :
                 exit(false);
             
         }
         
        }
    
        return view();
    
    
    
    }
    
    
}
