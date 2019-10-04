<?php
    /**
     * Created by PhpStorm.
     * User: Administrator
     * Date: 2018/12/28 0028
     * Time: 下午 6:16
     */
    
    $helper =  '../thinkphp/helper.php';
    if (file_exists( $helper )) {
        require   ($helper);
    } else {
        exit('获取数据失败');
    }
 
    return [
    
    
        //招标管理
        [
            'name' => '招标管理',
            'url' => 'javascript:;',
            'children' => [
                [
                    'name' => '数据统计',
                    'url' => url(  'admin/tendering/index'),
                ] ,
                [
                    'name' => '商品',
                    'url' => url('admin/tendering/zb_goods'),
                ],
                [
                    'name' => '商家提交信息',
                    'url' => url('admin/tendering/merchant_msg'),
                ]
            ],
        ],
    
        //数据分析
//        [
//            'name' => '数据分析',
//            'url' => 'javascript:;',
//            'item' => 'donation.html',
//            'children' => [
//                [
//                    'name' => '数据概况',
//                    'url' => url(  'admin/index/index'),
//                ]
//            ],
//        ],
    
        //社会捐助
        [
            'name' => '导航',
            'url' => 'javascript:;',
            'children' => [
                [
                    'name' => '前台导航',
                    'url' => url('admin/index/menu'),
                ] ,
            ],
        ],
            //社会捐助
        [
            'name' => '幻灯片管理',
            'url' => 'javascript:;',
            'children' => [
                [
                    'name' => '幻灯片列表',
                    'url' => url('admin/index/banner'),
                ] ,
            ],
        ],
        //辅助栏目
        [
            'name' => '辅助栏目',
            'url' => 'javascript:;',
            'children' => [
                [
                    'name' => '社会捐助',
                    'url' => url('admin/index/donation'),
                ] ,
                [
                    'name' => '专家团队',
                    'url' => url('admin/index/team'),
                ] ,
                [
                    'name' => '服务项目',
                    'url' => url('admin/index/activity'),
                ] ,
            ],
        ],
        
        //shopping
//        [
//            'name' => '养老商城',
//            'url' => 'javascript:;',
//            'children' => [
//                [
//                        'name' => '床位',
//                    'url' => url('admin/index/bed'),
//                ] ,
//                [
//                    'name' => '上门服务',
//                    'url' => url('admin/index/d19218'),
//                ] ,
//                [
//                    'name' => '养老产品',
//                    'url' => url('admin/index/goods'),
//                ] ,
//            ],
//        ],
        
        //关于
         [
            'name' => '关于心怡康',
            'url' => 'javascript:;',
            'children' => [
                [
                    'name' => '关于我们',
                    'url' => url('admin/index/contact'),
                ] ,
                [
                    'name' => '环境设施',
                    'url' => url('admin/index/ambient'),
                ] ,
                [
                    'name' => '入院流程',
                    'url' => url('admin/index/process'),
                ] ,
            ],
        ],
    
        //新闻资讯
        [
            'name' => '新闻资讯',
            'url' => 'javascript:;',
            'children' => [
                [
                    'name' => '资讯列表',
                    'url' => url('admin/index/news'),
                ] ,
                [
                    'name' => '一问一题',
                    'url' => url('admin/index/asks'),
                ] ,
                [
                    'name' => '留言管理',
                    'url' => url('admin/index/message'),
                ]
            ],
        ],

        //评论管理
/*        [
            'name' => '评论管理',
            'url' => 'javascript:;',
            'children' => [
                [
                    'name' => '评论列表',
                    'url' => url('admin/index/'),
                ] ,
            ],
        ],*/

   //设置
        [
            'name' => '设置',
            'url' => 'javascript:;',
            'children' => [
                [
                    'name' => '管理账号',
                    'url' => url('admin/index/admin'),
                ] ,
                [
                    'name' => '刷新缓存',
                    'url' => url('admin/index/clean'),
                ]
            ],
        ],

    ];