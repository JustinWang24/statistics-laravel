<?php
return [
    'user'=>[
        'agent'=>[
            'type'=>[                               // User device types
                'DESKTOP'=>1,
                'TABLET'=>2,
                'MOBILE'=>3,
            ]
        ]
    ],
    'cookie_identifier_name'=>'uuid',

    /**
     * Use Laravel Auth to retrieve the login user data.
     * If not, please give the session key.
     * 如果使用了 Laravel Auth, 则设置为 true.
     * 如果没有使用, 则指定用来提取当前登陆用户的 session key
     */
    'use_laravel_auth'=>true,

    /**
     * The key to retrieve login user data from session
     * 用来提取当前登陆用户的ID的 session key: 如果是数组形式保存, 则使用 user_data.user_id ....
     */
    'login_user_data_session_key'=>'user_data',

    /**
     * The User model name
     * 用来保存用户的模型类的全名
     */
    'user_model_name' => 'App\User',

    /**
     * Ignore the following URI
     * 下面的 URI 会被忽略, 不保存到统计数据中
     */
    'ignores' => [
        'GET' => [
            'group_names'=>['api'],
            'uri_names'=>['/c/a/p']
        ],
        'POST' => [
            'group_names'=>['api'],
            'uri_names'=>['/c/a/p']
        ]
    ],

    'methods_to_record' => ['GET','POST'],          // All methods shall be recorded
    'methods_to_ignore' => [],                      // Methods shall be ignored
];