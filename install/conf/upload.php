<?php

return [
    // +----------------------------------------------------------------------
    // | 上传设置
    // +----------------------------------------------------------------------
    'image' => [
        //文件最大尺寸（字节）
        'allow_max_size' => 16777216,
        //允许格式后缀
        'allow_ext' => 'png,jpg,gif,bmp,jpeg,webp',
        // +----------------------------------------------------------------------
        // | 保存规则
        // +----------------------------------------------------------------------
        //'default'：根据tp5官方默认
        //'original'：原始文件名
        //'uuid'：根据uuid
        //'date'：根据日期和微秒
        //'md5'：使用md5_file散列
        //'sha1'使用sha1_file散列
        //'uniqid':使用uniqid
        'save_rule' => 'default',
        //保存实际路径
        'save_real_path' => ROOT_PATH . 'public' . DS . 'upload' . DS . 'image',
        //保存相对路径，相对于域名访问而言
        'save_relative_path' => DS . 'upload' . DS . 'image',
        //存储模式（本地：local，阿里云OSS：oss，除本地存储外，其它模式下必须安装其它组件支持）
        'save_mode'=>'oss',
        //是否生成缩略图，是：true，否：false
        'create_thumb' => true,
        //缩略图高（单位：像素）
        'thumb_height'=>150,
        //缩略图宽（单位：像素）
        'thumb_width'=>150,
        //保存上传记录的数据表名
        'db_table_name'=>'image'
    ],
    'document' => [
        //文件最大尺寸（字节）
        'allow_max_size' => 16777216,
        //允许格式后缀
        'allow_ext' => 'doc,docx,xls,xlsx,ppt,pptx,pdf,txt',
        // +----------------------------------------------------------------------
        // | 保存规则
        // +----------------------------------------------------------------------
        //'default'：根据tp5官方默认
        //'original'：原始文件名
        //'uuid'：根据uuid
        //'date'：根据日期和微秒
        //'md5'：使用md5_file散列
        //'sha1'使用sha1_file散列
        //'uniqid':使用uniqid
        'save_rule' => 'default',
        //保存实际路径
        'save_real_path' => ROOT_PATH . 'public' . DS . 'upload' . DS . 'document',
        //保存相对路径，相对于域名访问而言
        'save_relative_path' => DS . 'upload' . DS . 'document',
        //存储模式（本地：local，阿里云OSS：oss，除本地存储外，其它模式下必须安装其它组件支持）
        'save_mode'=>'oss',
        //保存上传记录的数据表名
        'db_table_name'=>'document'
    ],
    'media' => [
        //文件最大尺寸（字节）
        'allow_max_size' => 16777216,
        //允许格式后缀
        'allow_ext' => 'ape,aac,aiff,amr,caf,flac,flv,swf,avi,rm,rmvb,mpeg,mpg,ogg,ogv,mkv,mov,m4a,wmv,mp4,webm,mp3,wav,wma,mid',
        // +----------------------------------------------------------------------
        // | 保存规则
        // +----------------------------------------------------------------------
        //'default'：根据tp5官方默认
        //'original'：原始文件名
        //'uuid'：根据uuid
        //'date'：根据日期和微秒
        //'md5'：使用md5_file散列
        //'sha1'使用sha1_file散列
        //'uniqid':使用uniqid
        'save_rule' => 'default',
        //保存实际路径
        'save_real_path' => ROOT_PATH . 'public' . DS . 'upload' . DS . 'media',
        //保存相对路径，相对于域名访问而言
        'save_relative_path' => DS . 'upload' . DS . 'media',
        //存储模式（本地：local，阿里云OSS：oss，除本地存储外，其它模式下必须安装其它组件支持）
        'save_mode'=>'oss',
        //保存上传记录的数据表名
        'db_table_name'=>'media'
    ],
    'program' => [
        //文件最大尺寸（字节）
        'allow_max_size' => 16777216,
        //允许格式后缀
        'allow_ext' => 'apk,dex',
        // +----------------------------------------------------------------------
        // | 保存规则
        // +----------------------------------------------------------------------
        //'default'：根据tp5官方默认
        //'original'：原始文件名
        //'uuid'：根据uuid
        //'date'：根据日期和微秒
        //'md5'：使用md5_file散列
        //'sha1'使用sha1_file散列
        //'uniqid':使用uniqid
        'save_rule' => 'default',
        //保存实际路径
        'save_real_path' => ROOT_PATH . 'public' . DS . 'upload' . DS . 'program',
        //保存相对路径，相对于域名访问而言
        'save_relative_path' => DS . 'upload' . DS . 'program',
        //存储模式（本地：local，阿里云OSS：oss，除本地存储外，其它模式下必须安装其它组件支持）
        'save_mode'=>'oss',
        //保存上传记录的数据表名
        'db_table_name'=>'program'
    ],
    'compressed_package' => [
        //文件最大尺寸（字节）
        'allow_max_size' => 16777216,
        //允许格式后缀
        'allow_ext' => 'rar,zip,7z',
        // +----------------------------------------------------------------------
        // | 保存规则
        // +----------------------------------------------------------------------
        //'default'：根据tp5官方默认
        //'original'：原始文件名
        //'uuid'：根据uuid
        //'date'：根据日期和微秒
        //'md5'：使用md5_file散列
        //'sha1'使用sha1_file散列
        //'uniqid':使用uniqid
        'save_rule' => 'default',
        //保存实际路径
        'save_real_path' => ROOT_PATH . 'public' . DS . 'upload' . DS . 'compressed_package',
        //保存相对路径，相对于域名访问而言
        'save_relative_path' => DS . 'upload' . DS . 'compressed_package',
        //存储模式（本地：local，阿里云OSS：oss，除本地存储外，其它模式下必须安装其它组件支持）
        'save_mode'=>'oss',
        //保存上传记录的数据表名
        'db_table_name'=>'compressed_package'
    ]
];
