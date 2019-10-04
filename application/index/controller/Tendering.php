<?php
    /**
     * Created by PhpStorm.
     * User: Administrator
     * Date: 2019/3/1 0001
     * Time: 下午 2:19
     */
    
    namespace app\index\controller;


    use think\Db;

    class tendering extends Common
    {


        public  function  index()
        {
            //商品
            $tablename = 'zb_goods';

            $keywords =  request()->get('keywords');

            if (!empty(  $keywords )) {

                $keywords = trim(request()->get('keywords'));

                $this->assign('keywords', $keywords);

            }

            $goods = Db::name($tablename)->where(['status' => 1, 'is_del' =>1 ])->where('title','like', $keywords.'%')->order('id deac')->select();

            
            if (!empty($goods)) {
                
                
                
                foreach($goods as &$val ) {
                    
                    
                    $val['specifications'] = unserialize($val['specifications']);
                    
                    
                    
                    $val['time'] = date('Y/m/d H:i:s',      $val['time'] );
                    
                    
                }
                

                $this->assign('goods', $goods);
                
                
                
            }
    
            //提交
    
            if (request()->get('do') == 'submitting') {


                if (request()->isAjax()) {

                    $post = request()->post();

                    if (empty($post['goods_id'])) {
                        exit(return_json('', 400, false, '此项可能已下架， 请重新回到进行列表选择！'));
                    }
                    if ( empty( $post['merchant_name']) ) {
                        exit(return_json('', 400, false, '请填写厂家联系人姓名！'));
                    }
                    if ( empty( $post['manufactor']) ) {
                        exit(return_json('', 400, false, '请填写厂家完整名称！'));
                    }
                    if ( empty( $post['merchant_address']) ) {
                        exit(return_json('', 400, false, '请填写厂家详细地址！'));
                    }
                    if ( empty( $post['merchant_tel']) ) {
                        exit(return_json('', 400, false, '请填写厂家联系方式！'));
                    }
                    if ( empty( $post['num']) || !is_numeric($post['num'])) {
                        exit(return_json('', 400, false, '请填写正确数量！'));
                    }

                  /*  if ( empty( $post['picArr'])  || !is_array($post['picArr'])) {
                        exit(return_json('', 400, false, '请上传资质'));
                    }*/

                    if ( empty( $post['single_price'])  || !is_numeric($post['single_price'])) {
                        exit(return_json('', 400, false, '请输入正确的价格'));
                    }
                    $preg_phone='/^1[34578]\d{9}$/ims';
                    if(!preg_match($preg_phone,trim($post['merchant_tel']))){
                        exit(return_json('', 400, false, '联系方式格式填写不正确！'));
                    }

                    $data = [
                        'goods_id' => intval($post['goods_id']),
                        'manufactor' => trim($post['manufactor']),//厂商名称
                        'merchant_address' => trim($post['merchant_address']),
                        'merchant_name' => trim($post['merchant_name']),
                        'merchant_tel' => trim($post['merchant_tel']),
                        'num' => intval($post['num']),//厂商名称
                        'picarr' => !empty($post['picArr'] )?  serialize($post['picArr']) : [],//厂商名称
                        'single_price' => trim($post['single_price']),
                        'specifications' => serialize( explode(',', $post['specifications'])),//多规格
                        'company' => trim($post['company']),//厂商名称
                        'sub_time' => time(),
                    ];

                    $result    =     Db::name('zb_merchant_msg')->insert($data);

                    if (!empty($result)) {
                        exit( return_json(url('tendering/index')));
                    }


                    exit(false);

                }


                $goods_id = request()->get('goods_id');




                $goods_info = Db::name('zb_goods')->where('id', $goods_id)->find();




                $goods_info['specifications'] =  implode(',',unserialize($goods_info['specifications']));


                if (!empty($goods_info)) {

                    $this->assign('goods_info', $goods_info);

                }




                return view('tendering/submitting');
                
                
                
            }
            
            
            return view();
            
        }




        public function  uploads()
        {


            if (!empty($_FILES)) {


                upload($_FILES);


            }




        }






    }