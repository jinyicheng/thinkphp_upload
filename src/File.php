<?php
// +----------------------------------------------------------------------
// | 项目：广告管理系统
// +----------------------------------------------------------------------
// | 说明：文件管理控制器
// +----------------------------------------------------------------------
// | 作者：金毅成  234067496@qq.com
// +----------------------------------------------------------------------

namespace jinyicheng\upload;

use InvalidArgumentException;
use jinyicheng\thinkphp_status\Status;
use jinyicheng\upload\implement\CompressedPackageImplement;
use jinyicheng\upload\implement\DocumentImplement;
use jinyicheng\upload\implement\ImageImplement;
use jinyicheng\upload\implement\MediaImplement;
use jinyicheng\upload\implement\ProgramImplement;
use jinyicheng\upload\interfaces\FileInterface;
use think\Config;
use think\Exception;
use think\Request;

class File extends Common
{
    private $config = [
        // +----------------------------------------------------------------------
        // | 上传设置
        // +----------------------------------------------------------------------
        'image' => [
            'allow_max_size' => 16777216,
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
            'save_real_path' => ROOT_PATH . 'public' . DS . 'upload' . DS . 'image',
            'save_relative_path' => DS . 'upload' . DS . 'image',
            'create_thumb' => true
        ],
        'document' => [
            'allow_max_size' => 16777216,
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
            'save_real_path' => ROOT_PATH . 'public' . DS . 'upload' . DS . 'document',
            'save_relative_path' => DS . 'upload' . DS . 'document'
        ],
        'media' => [
            'allow_max_size' => 16777216,
            'allow_ext' => 'flv,swf,mkv,avi,rm,rmvb,mpeg,mpg,ogg,ogv,mov,wmv,mp4,webm,mp3,wav,mid',
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
            'save_real_path' => ROOT_PATH . 'public' . DS . 'upload' . DS . 'media',
            'save_relative_path' => DS . 'upload' . DS . 'media'
        ],
        'program' => [
            'allow_max_size' => 16777216,
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
            'save_real_path' => ROOT_PATH . 'public' . DS . 'upload' . DS . 'program',
            'save_relative_path' => DS . 'upload' . DS . 'program'
        ],
        'compressed_package' => [
            'allow_max_size' => 16777216,
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
            'save_real_path' => ROOT_PATH . 'public' . DS . 'upload' . DS . 'compressed_package',
            'save_relative_path' => DS . 'upload' . DS . 'compressed_package'
        ]
    ];
    private static $instance = [];

    /**
     * File constructor.
     * @param array $config
     */
    private function __construct($config = [])
    {
        if ((!is_null(Config::get('upload')))) {
            $this->$config = array_merge($this->$config, Config::get('upload'), $config);
        } else {
            $this->$config = array_merge($this->$config, $config);
        }
    }

    /**
     * @param array $config
     * @return File
     */
    public static function getInstance($config = [])
    {
        if ($config === false || $config === []) throw new InvalidArgumentException('upload配置不存在');
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
     * @param \Think\file $file_data
     * @param bool $is_attachment
     * @param bool $status
     * @param string $related_object
     * @param string $related_id
     * @return array|mixed
     * @throws Exception
     */
    public function upload($file_data, $is_attachment = false, $status = false, $related_object = '', $related_id = '')
    {
        $extension = strtolower(pathinfo($file_data->getInfo('name'), PATHINFO_EXTENSION));
        $typeOfExtension = $this->typeOfExtension($extension);
        if (is_null($typeOfExtension)) {
            return [
                'code' => Status::get('#4031.code'),
                'message' => '文件类型' . $extension . '未经许可！',
                'data' => null,
            ];
        }
        $handle = self::handle($typeOfExtension);
        /** @var FileInterface $handle */
        return $handle->upload(
            $file_data,
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
     * @return CompressedPackageImplement|DocumentImplement|ImageImplement|MediaImplement|ProgramImplement
     * @throws Exception
     */
    public static function handle($type, $config = [])
    {
        switch ($type) {
            case 'image':
                return ImageImplement::getInstance($config['image']);
                break;
            case 'document':
                return DocumentImplement::getInstance($config['document']);
                break;
            case 'media':
                return MediaImplement::getInstance($config['media']);
                break;
            case 'program':
                return ProgramImplement::getInstance($config['program']);
                break;
            case 'compressed_package':
                return CompressedPackageImplement::getInstance($config['compressed_package']);
                break;
            default:
                throw new Exception($type . '文件处理接口未实现。');

        }
    }
}
