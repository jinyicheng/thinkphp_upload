<?php
// +----------------------------------------------------------------------
// | 项目：广告管理系统
// +----------------------------------------------------------------------
// | 说明：文件管理控制器
// +----------------------------------------------------------------------
// | 作者：金毅成  234067496@qq.com
// +----------------------------------------------------------------------

namespace jinyicheng\thinkphp_upload;

use BadFunctionCallException;
use InvalidArgumentException;
use jinyicheng\thinkphp_status\Status;
use jinyicheng\thinkphp_upload\implement\CompressedPackage;
use jinyicheng\thinkphp_upload\implement\Document;
use jinyicheng\thinkphp_upload\implement\Image;
use jinyicheng\thinkphp_upload\implement\Media;
use jinyicheng\thinkphp_upload\implement\Program;
use think\Config;
use think\Exception;
use think\Request;

class File
{
    private $config = [
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
    private static $instance = [];

    /**
     * File constructor.
     * @param array $config
     */
    private function __construct($config = [])
    {
        if (!extension_loaded('fileinfo')) {
            throw new BadFunctionCallException('not support: fileinfo，请安装php_fileinfo扩展');      //判断是否有扩展
        }
        if (Config::has('upload')) {
            $this->config = array_merge($this->config, Config::get('upload'), $config);
        } else {
            $this->config = array_merge($this->config, $config);
        }
    }

    /**
     * @param array $config
     * @return File
     */
    public static function getInstance($config = [])
    {
        $hash = md5(json_encode($config));
        if (!isset(self::$instance[$hash])) {
            self::$instance[$hash] = new self($config);
        }
        return self::$instance[$hash];
    }

    /**
     * @param $extension
     * @return string|null
     */
    public function typeOfExtension($extension)
    {
        switch ($extension) {
            case in_array(
                $extension,
                explode(',', $this->config['image']['allow_ext'])
            ):
                return 'image';
                break;
            case in_array(
                $extension,
                explode(',', $this->config['document']['allow_ext'])
            ):
                return 'document';
                break;
            case in_array(
                $extension,
                explode(',', $this->config['compressed_package']['allow_ext'])
            ):
                return 'compressed_package';
                break;
            case in_array(
                $extension,
                explode(',', $this->config['media']['allow_ext'])
            ):
                return 'media';
                break;
            case in_array(
                $extension,
                explode(',', $this->config['program']['allow_ext'])
            ):
                return 'program';
                break;
            default:
                return null;
        }
    }

    /**
     * @param \think\File $file
     * @param bool $is_attachment
     * @param bool $status
     * @param string $related_object
     * @param string $related_id
     * @return array|mixed
     * @throws Exception
     */
    public function upload($file, $is_attachment = false, $status = false, $related_object = '', $related_id = '')
    {
        $extension = strtolower(pathinfo($file->getInfo('name'), PATHINFO_EXTENSION));
        $typeOfExtension = $this->typeOfExtension($extension);
        if (is_null($typeOfExtension)) {
            return [
                'code' => Status::get('#4031.code'),
                'message' => '文件类型' . $extension . '未经许可！',
                'data' => null,
            ];
        }
        $handle = self::handle($typeOfExtension, $this->config);
        return $handle->upload(
            $file,
            $is_attachment,
            $status,
            $related_object,
            $related_id
        );

    }

    /**
     * @param string $file_name
     * @param string $key
     * @return bool|mixed
     * @throws Exception
     */
    public function delete($file_name='',$key='')
    {
        $extension = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
        $typeOfExtension = $this->typeOfExtension($extension);
        $handle = self::handle($typeOfExtension, $this->config);
        return $handle->delete(
            $file_name,
            $key
        );
    }


    /**
     * @param $type
     * @param array $config
     * @return implement\local\CompressedPackageImplement|implement\local\DocumentImplement|implement\local\ImageImplement|implement\local\MediaImplement|implement\local\ProgramImplement|implement\oss\CompressedPackageImplement|implement\oss\DocumentImplement|implement\oss\ImageImplement|implement\oss\MediaImplement|implement\oss\ProgramImplement
     */
    public static function handle($type, $config = [])
    {
        switch ($type) {
            case 'image':
                return Image::getInstance($config['image']);
                break;
            case 'document':
                return Document::getInstance($config['document']);
                break;
            case 'media':
                return Media::getInstance($config['media']);
                break;
            case 'program':
                return Program::getInstance($config['program']);
                break;
            case 'compressed_package':
                return CompressedPackage::getInstance($config['compressed_package']);
                break;
            default:
                throw new Exception($type . '文件处理接口未实现。');

        }
    }
}
