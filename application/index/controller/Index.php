<?php
namespace app\index\controller;

use think\Db;

class Index extends Common
{


    public  function   test()
    {
        return view();
    }


    /**
     * @return \think\response\View
     * 收费标准
     */
    public  function  consume()
    {
        return view();
    }

    public  function banner()
    {
        $table = 'banner';

        $id = request()->param('id');

        if (!empty($id)) {

            $find = Db::name($table)->where('id', $id)->find();

            if (!empty($find)) {
            $find['content'] = html_entity_decode($find['content']);
            $find['create_time']  = date('Y/m/d', $find['create_time']);
            $this->assign('data', $find);
            }

        }


        $banner_title = Db::name($table)->where('status' , 1)->field('id,title')->select();
        if (!empty($banner_title)) {
            $this->assign('banner_title', $banner_title);
        }


        //热门新闻
        $hotnews = Db::name('news')->where('is_recommend', 1)->where('status' , 1)->field('id, title, icon')->select();
        if (!empty($hotnews)) {
            $this->assign('hotnews', $hotnews);
        }


        return view();
    }

    /**
     * @return \think\response\View
     */
    public function home()
    {
        $banner = Db::name('banner')->where('status', 1)->order('sort asc')->select();

        if (!empty($banner)) {
            foreach ($banner as &$val ) {

                $val['content'] = html_entity_decode($val['content']);

                //$val['content'] =  mb_substr( $val['content'], 0, 100);
            }

            $this->assign('banner', $banner);
        }
        
        $m = input('m');
        $do = input('do');
        $id = input('id');
        
        //banner_detail
        if ($m == 'banner' && $do == 'detail') {
            if (empty($id)) {
                $this->error('数据有误， 请刷新后在访问');
                exit;
            }
            
            $id = intval($id);
    
            //分页
            $up_where = $order =  $limit = '';
            
            if (request()->get('point') == 'up_paging' ) {
                
                $min_id  =  Db::name('banner')->min('id');
                
                $up_where = 'id < '.$id;
                
                $order = 'id desc';
                
                $limit = '0, 1';
    
                if (intval($min_id+1) === intval($id )) {
        
                    $this->assign('up_disabled', true);
        
                }
                
            }
            
           if (request()->get('point') == 'down_paging' ) {
               
               $max_id  =  Db::name('banner')->max('id');
    
               if (intval($max_id-1) === intval($id )) {
                   $this->assign('down_disabled', true);
                   
               }
               
                $up_where = 'id > '.$id;
               
                $order = 'id asc';
                
                $limit = '0, 1';
            }
            
            //详情
           $point =  request()->get('point');
            $detail = Db::name('banner')->where($up_where)->order($order)->limit($limit)->where(empty($point ) ?  ['id'  => $id ] : null  )->find();
            
            if (!empty($detail)) {
                
                $detail['content'] = html_entity_decode($detail['content']);
                
                $detail['create_time'] = date('y/m/d H:i:s', $detail['create_time']);
                
                $this->assign('detail', $detail);
                
                
                //热门阅读
                $hot_asks = Db::name('asks')->where(['is_hot' => 1, 'status' => 1, 'is_del' => 1])->select();
                
                if (!empty($hot_asks)) {
                    $this->assign('hot_asks', $hot_asks);
                }
    
                return view('index/banner_detail');
            }
            
        }
    

        //关于我们
        $intro = Db::name('contact')->where('id', 1)->find();

        $intro['corporate_culture'] = html_entity_decode($intro['corporate_culture']);
        $intro['content'] = html_entity_decode($intro['content']);
        
        if (!empty($intro)) {
            $this->assign('intro', $intro);
        }
        
        
        //简介详情
        if ($m == 'intro' && $do == 'detail') {
            if (empty($id)) {
                $this->error('数据有误， 请刷新后在访问');
                exit;
            }
            
            $id = intval($id);
            
            $detail = Db::name('contact')->where('id', $id)->find();
            if (!empty($detail)) {
                
                $detail['content'] = html_entity_decode($detail['content']);
                $detail['update_time'] = date('Y/m/d H:i:s', $detail['update_time']);
                
                $this->assign('detail', $detail);
                
                return view('index/intro_detail');
                
            }

        }

        //TODO : 生活服务
        $activity = Db::name('activity')->where(['is_recommend' => 1, 'status' => 1])->limit(8)->field('id, title,intro, icon,create_time ')->select();

        if (!empty($activity)) {
            $this->assign('activity', $activity);
        }
        
        //服务项目详情
        if ($m == 'activity' && $do == 'detail') {
            
            if (empty($id)) {
                $this->error('数据有误， 请刷新后在访问');
                exit;
            }
            
            $id = intval($id);
            
            $title = Db::name('activity')->where('status' , 1)->field('title, id')->select();
            if (!empty($title)) {
                $this->assign('title', $title);
                $this->assign('id', $id);
            }
            
            $detail = Db::name('activity')->where('id', $id)->find();
            $detail['content'] = html_entity_decode($detail['content']);
            
            if (!empty($detail)) {
                $this->assign('detail', $detail);
                return view('index/activity_detail');
            }
            
        }
        
        //推荐新闻列表
        $recommend_news = Db::name('news')->where(['status'  =>1, 'is_recommend' =>1])->limit(8)->select();

        if (!empty($recommend_news)) {
            $this->assign('recommend_news', $recommend_news);
        }
        
        //推荐问答
        $recommend_asks = Db::name('asks')->where(['status'  =>1, 'is_recommend' =>1])->field('id, title, intro,create_time, icon')->limit(5)->select();
        if (!empty($recommend_asks)) {

            $this->assign('recommend_asks', $recommend_asks);
        }
    
        //环境
        $recommend_ambient = Db::name('ambient')->where(['status'  =>1, 'is_recommend' =>1])->field('id, icon')->limit(4)->select();
        if (!empty($recommend_ambient)) {
            $this->assign('recommend_ambient', $recommend_ambient);
        }

        //推荐医师团队
        $doctor_list =  Db::name('team')->where('is_recommend', 1)->field('name, intro,icon, id')->where('status', 1)->select();

        if (!empty($doctor_list)) {

            foreach ( $doctor_list as &$val ) {

            }

            $this->assign('doctor_list', $doctor_list);

        }

        //问答类
        $askscate = Db::name('asks_category')->where('status', 1)->limit(6)->field('title, id')->select();

        if (!empty($askscate)) {
            $this->assign('askscate', $askscate);
        }

        //问答： 默认显示全部推荐：

        return view();
    }

    //环境列表
    public function ambient()
    {
        $where = '';
        $tablename = 'ambient';
        $cate_tablename = 'ambient_category';
        if (request()->isAjax()) {
            
            $post = request()->post();
            
            $cateid = intval($post['cateid']);
            if (empty($cateid)) {
                exit(false);
            }
            $where =  ['cateid' => $cateid ];
    
            $ambient = Db::name($tablename)->where('status', 1 )->where($where)->field('id, title, icon')->where('is_del', 1 )->select();
            
            if (!empty($ambient)) {
                exit(json_encode( $ambient ));
            }
            exit(false);
            
        } else {
        
        $first_id =  Db::name($cate_tablename)->min('id');
        
            if (!empty($first_id)) {
                $ambient = Db::name($tablename)->where('cateid', $first_id )->select();
                
                if (!empty($ambient)) {
                    $this->assign('ambient', $ambient);
                }
            }
        
        }
        
        
        //分类
        $cate_ambient = Db::name($cate_tablename)->field('id, title')->order('id asc')->where(['status' => 1, 'is_del' => 1])->select();
     
        if (!empty($cate_ambient)) {
            $this->assign('cate_ambient', $cate_ambient );
        }
        
        return view();
    }
    
    
    //入院流程
    public function  process()
    {
        return view();
    }
    
    
      //入院流程
    public function  donation()
    {
        return view();
    }
    
    //床位列表
    public function bed()
    {
        $tablename = 'bed';
        if (request()->isAjax()) {
            $post = request()->post();
            if (trim($post['do']) == 'pobed') {
            
                $twin_bed = Db::name($tablename)->where('bed', intval($post['bed']))->field('id, title, icon, price')->select();
                if (!empty($twin_bed)) {

                    exit(json_encode($twin_bed));

                }

                exit(false);

            }
        
        }
    
        //求单人间
        $single_bed = Db::name($tablename)->where('bed', 1)->field('id, title, icon, price')->select();
        if (!empty($single_bed)) {
            $this->assign('single_bed', $single_bed);
        }
    
    
        //bed详情：
    
        if ( request()->get('do') == 'detail' ) {
            wl_debug(11);
            
            
            
            }
        
        
        return view();
    }
    
    //about
        public  function  about1()
    {

        //关于我们
        $about = Db::name('contact')->where('id', 1)->find();
    
        $about['corporate_culture'] = html_entity_decode($about['corporate_culture']);
        $about['content'] = html_entity_decode($about['content']);

        if (!empty($about)) {
            $this->assign('data', $about);
        }
        
        return view();
    }

    
    //服务项目
    public function  activity()
    {
        $tablename = 'activity';
        $activity = Db::name($tablename)->where(['status' => 1])->field('id,create_time,content, icon,title')->select();
        if (!empty($activity)) {

            $this->assign('activity', $activity);

        }

        if (request()->get('do') == 'activity_detail' ) {

            //热门阅读
            $hot_asks = Db::name('asks')->where(['is_hot' => 1, 'status' => 1, 'is_del' => 1])->select();

            if (!empty($hot_asks)) {
                $this->assign('hot_asks', $hot_asks);
            }

            $activity_id  = request()->get('activity_id');

            if (empty($activity_id)) {
                return false;
            }

            $activity_find = Db::name($tablename)->where('id', $activity_id)->find();
            $activity_find['create_time'] = date('Y/m/d H:i:s', $activity_find['create_time']);
            $activity_find['content'] = htmlspecialchars_decode($activity_find['content']);

            if (!empty($activity_find)) {
                $this->assign('activity_id', $activity_id);
                $this->assign('data', $activity_find);
            }

            return view('index/activityinfo');

        }

        return view();
        
    }
    
    //新闻
    public  function  news()
    {
        $id =intval( input('id'));
        
        if (!empty($id)) {

            //只有id
            if (empty(request()->get('point'))) {

                //下一篇标题
                $next_data = Db::name('news')->where('id', $id + 1 )->field('title')->where('status',1)->find();

                if (!empty($next_data)) {
                    $this->assign('next_data', $next_data);
                }

                //上一篇标题
                $upper_data = Db::name('news')->where('id', $id - 1 )->field('title')->where('status',1)->find();
                if (!empty($upper_data)) {
                    $this->assign('upper_data', $upper_data);
                }

            }

            $where2 = $order =  $limit = '';
            if (request()->get('do') == 'paging' ) {
    
                if (request()->get('point') == 'up_paging' ) {

                    //上一篇标题
                    $upper_data = Db::name('news')->where('id', $id - 2 )->field('title')->where('status',1)->find();
                    if (!empty($upper_data)) {
                        $this->assign('upper_data', $upper_data);
                    }

                    //下一篇标题
                    $next_data = Db::name('news')->where('id', $id  )->field('title')->where('status',1)->find();

                    if (!empty($next_data)) {
                        $this->assign('next_data', $next_data);
                    }

        
                    $min_id  =  Db::name('news')->min('id');
    
                    if ($min_id < $id  ) {
                        $where2 = 'id < '.$id;
                    }
        
                    $order = 'id desc';
        
                    $limit = '0, 1';
        
                    if (intval($min_id+1) === intval($id )) {
            
                        $this->assign('up_disabled', true);
            
                    }
        
                }
    
                if (request()->get('point') == 'down_paging' ) {


                    //下一篇标题
                    $next_data = Db::name('news')->where('id', $id   + 2   )->field('title')->where('status',1)->find();

                    if (!empty($next_data)) {
                        $this->assign('next_data', $next_data);
                    }

                    //上一篇标题
                    $upper_data = Db::name('news')->where('id', $id  )->field('title')->where('status',1)->find();
                    if (!empty($upper_data)) {
                        $this->assign('upper_data', $upper_data);
                    }



                    $max_id  =  Db::name('news')->max('id');

                    if (intval($max_id-1 ) === intval($id )) {

                        $this->assign('down_disabled', true);
            
                    }
                    
                    if($max_id  > $id ) {
                        $where2 = 'id > '.$id;
                    }

                    $order = 'id asc';
        
                    $limit = '0, 1';
                }
                
            }
    
            //详情
            $point =  request()->get('point');
    
            $newsinfo = Db::name('news')->where($where2)->order($order)->limit($limit)->where(empty($point ) ?  ['id'  => $id ] : null  )->find();

            if (!empty($newsinfo)) {
    
                $newsinfo['content'] = html_entity_decode($newsinfo['content']);
                
                $newsinfo['create_time'] = date('Y/m/d H:i:s', $newsinfo['create_time']);
                
                $this->assign('data', $newsinfo);
                
            }
            
            //热门阅读
            //热门阅读
            $hot_asks = Db::name('asks')->where(['is_hot' => 1, 'status' => 1, 'is_del' => 1])->select();
    
            if (!empty($hot_asks)) {
                $this->assign('hot_asks', $hot_asks);
            }
    
            return view('index/newsinfo');
        
        }
    
        //列表分页
        if (request()->post('do') == 'next_news' ) {
            $input_post = input("post.");

            if (isset($input_post) && !empty($input_post)) {
                $offect = intval( $input_post['offect']);
                $show_list = Db::name("news")->limit( $offect, 1)->select();
                foreach ($show_list as &$item  ) {
                    $item['intro'] =  mb_substr($item['intro'], 0, 35, 'utf-8') . '.....';
                }
                
                if(isset( $show_list ) && !empty( $show_list )) {
                 exit(json_encode($show_list));
                } else {
                    return false ;
                }
            }
         } else {
            
            $page_size  =  6;
            
            $news = Db::name('news')->where(['status' => 1, 'is_del' => 1])->order('id asc')->paginate($page_size ,  false );
    
            $page = request()->get('page') ? request()->get('page') : 1;
            
            if (!empty($news)) {
        
                $this->assign('news', $news);
        
                $this->assign('page', $page);
        
            }
        }
  
        return view();
    }
    
    //ask
    public  function  asks()
    {
        if (request()->isAjax()) {

            $cateid =  intval(request()->post('cateid'));

            $ajax_asks = Db::name('asks')->where('cateid', $cateid)->where('is_recommend', 1)->limit(4)->where('status', 1)->select();
            if (!empty($ajax_asks)) {

                foreach ($ajax_asks as &$val ) {
                    $val['date'] = date('m-d', $val['create_time']);
                    $val['year'] = date('Y', $val['create_time']);
                }

                exit(json_encode(['asks' => $ajax_asks]));
            }

        }

        $tablename = 'asks';
        $id =intval( input('id'));
    
        if (!empty($id)) {
            //分页



            //只有id
            if (empty(request()->get('point'))) {

                //下一篇标题
                $next_data = Db::name($tablename)->where('id', $id + 1 )->field('title')->where('status',1)->find();

                if (!empty($next_data)) {
                    $this->assign('next_data', $next_data);
                }

                //上一篇标题
                $upper_data = Db::name($tablename)->where('id', $id - 1 )->field('title')->where('status',1)->find();
                if (!empty($upper_data)) {
                    $this->assign('upper_data', $upper_data);
                }

            }

            $where2 = $order =  $limit = '';
            if (request()->get('do') == 'paging' ) {
            
                if (request()->get('point') == 'up_paging' ) {

                    //上一篇标题
                    $upper_data = Db::name($tablename)->where('id', $id - 2 )->field('title')->where('status',1)->find();
                    if (!empty($upper_data)) {
                        $this->assign('upper_data', $upper_data);
                    }

                    //下一篇标题
                    $next_data = Db::name($tablename)->where('id', $id  )->field('title')->where('status',1)->find();

                    if (!empty($next_data)) {
                        $this->assign('next_data', $next_data);
                    }


                    $min_id  =  Db::name($tablename)->min('id');
    
                    if ($min_id+1 < $id  ) {
                        $where2 = 'id < '.$id;
                    }

                
                    $order = 'id desc';
                
                    $limit = '0, 1';
                
                    if (intval($min_id+1) === intval($id )) {
                    
                        $this->assign('up_disabled', true);
                    
                    }
                
                }
            
                if (request()->get('point') == 'down_paging' ) {

                    //下一篇标题
                    $next_data = Db::name($tablename)->where('id', $id   + 2   )->field('title')->where('status',1)->find();

                    if (!empty($next_data)) {
                        $this->assign('next_data', $next_data);
                    }

                    //上一篇标题
                    $upper_data = Db::name($tablename)->where('id', $id  )->field('title')->where('status',1)->find();
                    if (!empty($upper_data)) {
                        $this->assign('upper_data', $upper_data);
                    }


                    $max_id  =  Db::name($tablename)->max('id');
                
                    if (intval($max_id-1) === intval($id )) {
                        $this->assign('down_disabled', true);
                    
                    }
    
                    if($max_id  > $id ) {
                        $where2 = 'id > '.$id;
                    }
                
                    $order = 'id asc';
                
                    $limit = '0, 1';
                }
            
            }
        
            //详情
            $point =  request()->get('point');
        
            $asksinfo = Db::name($tablename)->where($where2)->order($order)->limit($limit)->where(empty($point ) ?  ['id'  => $id ] : null  )->find();

            if (!empty($asksinfo)) {
            
                $asksinfo['content'] = html_entity_decode($asksinfo['content']);
            
                $asksinfo['create_time'] = date('Y/m/d H:i:s', $asksinfo['create_time']);
            
                $this->assign('data', $asksinfo);
            
            }
    
            //热门阅读
            $hot_asks = Db::name('asks')->where(['is_hot' => 1, 'status' => 1, 'is_del' => 1])->select();
    
            if (!empty($hot_asks)) {
                $this->assign('hot_asks', $hot_asks);
            }
            
        
            return view('index/asksinfo');
        
        }
    
        //列表分页
        if (request()->post('do') == 'next_asks' ) {
            $input_post = input("post.");
        
            if (isset($input_post) && !empty($input_post)) {
                
                $offect = intval( $input_post['offect']);
                
                $show_list = Db::name($tablename)->limit( $offect, 1)->select();
                
                foreach ($show_list as &$item  ) {
                    if (is_serialized_string(   $item['intro'] )) {
                        $item['intro']  =     unserialize($item['intro']);
                    }
                    $item['intro'] =  mb_substr(   $item['intro'] , 0, 35, 'utf-8') . '.....';
                }

                if(isset( $show_list ) && !empty( $show_list )) {
                    exit(json_encode($show_list));
                } else {
                    return false ;
                }
            }
        } else {
            
            $page_size = 6;
        
            $asks_list = Db::name($tablename)->where(['status' => 1, 'is_del' => 1])->order('id asc')->paginate($page_size ,  false );
        
            $page = request()->get('page') ? request()->get('page') : 1;
        
            if (!empty($asks_list)) {
            
                $this->assign('asks_list', $asks_list);
            
                $this->assign('page', $page);
            
            }
        }
    
        return view();
    }
    

    /**
     * 用户留言
     */
    public  function  msg()
    {
        if (request()->isAjax()) {
    
            $name = trim( input('name'));
            $age = trim( input('age'));
            $tel = trim( input('tel'));
            $content = trim( input('content'));
            $sex = intval( input('sex')) == 1 ? '男' : '女';
            
            if (empty( $name  )) {
                exit(return_json('', 400, false, '请填写您的姓名！'));
            }
               if (empty( $sex  )) {
                exit(return_json('', 400, false, '请填写您的性别！'));
            }
            
            if (empty( $age  ) || !is_numeric($age )) {
                exit(return_json('', 400, false, '年龄为空或者格式不正确！'));
            }
            $preg_phone='/^1[34578]\d{9}$/ims';
            if(!preg_match( $preg_phone,$tel )){
                exit(return_json('', 400, false, '手机为空或格式不正确！'));
            }
            if (  empty($content )) {
                exit(return_json('', 400, false, '留言不能为空！'));
            }
            
            $data = [
                'status' => 1,
                'is_del' => 1,
                'user_name' => $name,
                'user_ip' => get_ip(),
                'time' => time(),
                'tel' => $tel,
                'msg' => $content,
                'age' => $age
            ];
            
          $result = Db::name('message')->insert($data);
            if (!empty($result)) {
                exit(return_json(url('index/index/home'), 200, true, '提交成功'));
            }
            
        }
    }
    

    //医师团队
    public  function  doctor()
    {
        $table = 'team';

        $params = request()->param();

        $do = trim($params['do']);

        if ($do == 'detail') {

            if (!empty($params['id'])) {

                $id = intval($params['id']);

                $doctorinfo = Db::name($table)->where('id', $id)->find();

                //热门阅读
                $hot_asks = Db::name('asks')->where(['is_hot' => 1, 'status' => 1, 'is_del' => 1])->select();

                if (!empty($hot_asks)) {
                    $this->assign('hot_asks', $hot_asks);
                }

                //医师团队
                $doctor_title =  Db::name('team')->where('status', 1)->field('name, id')->select();

                if (!empty($doctor_title)) {
                    $this->assign('doctor_title', $doctor_title);

                }

                if (!empty($doctorinfo)) {

                $doctorinfo['description'] = unserialize( $doctorinfo['description'] );

                $this->assign('data', $doctorinfo);

                }

                return view('index/doctorinfo');
            }

        }


    }


    //联系我们
    public  function  contact()
    {

        return view();

    }


}
