<?php
    /**
     * Created by PhpStorm.
     * User: Administrator
     * Date: 2019/3/1 0001
     * Time: 下午 12:57
     */
    
    namespace app\admin\controller;

    use think\Db;

    class Tendering extends  YHYCommon
    {


        public  function test()
        {
            return view();
        }

        /**
         * @return \think\response\View
         * @throws \think\db\exception\DataNotFoundException
         * @throws \think\db\exception\ModelNotFoundException
         * @throws \think\exception\DbException
         */
        public function index()
        {
            echo 2322;exit;
            //今日新增报价量;
            $tablename = 'zb_merchant_msg';

            $cur_date = strtotime(date('Y-m-d'));

            $today_total = Db::name($tablename)->where("sub_time >= '{$cur_date}'")->field('count(id) as today_total')->find();

            $today_total = $today_total['today_total'] > 0 ? $today_total['today_total'] : 0;

            $this->assign('today_total', $today_total);

            //总报价数量
            $total_num = Db::name($tablename)->field('count(id) as total')->find();

            $total_num = $total_num['total'] > 0 ? $total_num['total'] : 0;

            $this->assign('total_num', $total_num);

            //查询今日报价数据
            $today_data = Db::name($tablename)->order('id desc')->select();

            if (!empty($today_data)) {

                foreach ($today_data as &$val ) {

                    $goodsinfo = Db::name('zb_goods')->field('title, pic,id')->where('id', $val['goods_id'])->find();

                    $val['goods_title'] = trim($goodsinfo['title']);

                    $val['specifications'] =  implode(',',unserialize($val['specifications']));

                    $val['sub_time'] = date('Y/m/d H:i:s',  $val['sub_time']);

                    $val['goods_pic'] = $goodsinfo['pic'];
                }

                $this->assign('today_data', $today_data);
            }

            //系统版本
            $system = [];

            $system['php_version'] = 'php/'.phpversion();

            $system['port'] = request()->port();//端口

            $system['run_type'] = $_SERVER['SERVER_SOFTWARE'].'2.4';//服务器环境运行环境

            $system['website'] = $_SERVER['SERVER_NAME'];//网站首页

            $system['mysql_version'] ='MySQL 5.6';//数据库版本

            $system['catche_type'] ='redis';//缓存类型

            $this->assign('system', $system);

            return view();

        }

        //商品管理
        public  function  zb_goods()
        {
            $tablename = 'zb_goods';
            //shop
            if (request()->post('point') == '_delete') {
        
                $id = empty( request()->post('id')  ) ?  exit(false)  :  intval(request()->post('id') );
        
                $result =  Db::name($tablename)->where('id', $id)->delete();
        
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
        
                $result =  Db::name($tablename)->where('id', $id)->update($data);
        
                if(!$result ) {
            
                    exit(false);
                }
        
                exit(return_json(url('admin/tendering/zb_goods')));
            }
    
            if (   !empty( $_GET['point'] )  && trim($_GET['point'] ) == '_add'  ) {
        
                if ( !empty(  request()->post() ) ) {
            
                    $post =  request()->post();
            
                    if (empty($post['title'])) {
                        exit(return_json('', 400, false, '名称不能为空！'));
                    }
            
                    if (empty($post['num'])) {
                        exit(return_json('', 400, false, '数量不能为空！'));
                    }
                    if (empty($post['specifications'])) {
                        exit(return_json('', 400, false, '规格不能为空！'));
                    }
                    if (empty($post['company'])) {
                        exit(return_json('', 400, false, '请输入单位！'));
                    }
                    
                    $data = [
                        'status' => 1,
                        'is_del' =>1,
                        'title' => trim($post['title']),
                        'num' => intval($post['num']),//所需要数量
                        'pic' => trim($post['pic']),
                        'specifications' =>serialize( explode(',',  trim($post['specifications']))),//多规格
                        'company' => trim($post['company']),//单位
                        'time' => time(),//单位
                    ];
                    
                    if (empty($post['id'])) {
                        $result    =     Db::name($tablename)->insert($data);
                    } else {
                        $id = intval($post['id']);
                        $result    =     Db::name($tablename)->where('id', $id )->update($data);
                
                    }
            
                    if ($result ) {
                
                        exit( return_json(url('admin/tendering/zb_goods')));
                
                    }
                    exit(false);
                }
        
                if (!empty(request()->get('id') )) {

                    $id = request()->get('id');

                    $find_data = Db::name($tablename)->where('id', $id)->find();

                    $find_data['specifications'] =  implode(',',unserialize($find_data['specifications']));

                    $this->assign('find_data', $find_data);

                }
        
                return view('tendering/addgoods');
            }
    
    
            $like = '';
            if (!empty(request()->get('keywords'))) {

                $like = trim(request()->get('keywords'));

                $this->assign('like', $like);

            }
            
            //list
            $list = Db::name($tablename)->where('is_del', 1)->where('id|title','like', $like.'%')->paginate(10, true, ['var_page' => 'page'  ]);

            $co = Db::name($tablename)->where('is_del', 1)->where('id|title','like', $like.'%')->field('count(id) as total')->find();
    
            $total = intval($co['total'] );
    
            $this->assign('total', $total);
    
            $this->assign('list', $list);
    
            $page = request()->get('page') ? request()->get('page') : 1;
    
            $this->assign('page', $page);
    
            return view();
    
        }
        
        //商家提交信息
        public  function  merchant_msg()
        {
            $tablename = 'zb_merchant_msg';

            if (request()->post('point') == '_delete') {
                $id = request()->post('id');

                $id = empty( $id ) ?  exit(false)  :  intval( $id );

                $result =  Db::name($tablename)->where('id', $id)->delete();

                if(!$result ) {

                    exit(false);
                }
                exit(return_json());
            }


            $where = null;
            if (request()->get('do') == 'goods_sort') {

                $goods_id = intval(request()->get('goods_id'));

                if (!empty($goods_id)) {

                    $where = ['goods_id' => $goods_id ];

                    $this->assign('goods_id', $goods_id);

                }

            }


            $merchant_msg = Db::name($tablename)->order('id desc')->where($where )->select();

            if (!empty($merchant_msg)) {

                foreach ($merchant_msg as &$val ) {

                    $goodsinfo = Db::name('zb_goods')->field('title, pic,id')->where('id', $val['goods_id'])->find();
                    $val['goods_title'] = trim($goodsinfo['title']);
                    $val['specifications'] =  implode(',',unserialize($val['specifications']));
                    $val['sub_time'] = date('Y/m/d H:i:s',  $val['sub_time']);
                    $val['goods_pic'] = $goodsinfo['pic'];
                    $val['picarr'] = unserialize($val['picarr']);
                }
                $this->assign('merchant_msg', $merchant_msg);

            }

            //select
            $goods =  Db::name('zb_goods')->field('id, title')->select();
            if (!empty($goods)) {
                $this->assign('goods', $goods);
            }

            //资质查看器
            //当用户点击查看资质：将报价id传到后台， 然后返回该报价资质
            return view();
        }


    }