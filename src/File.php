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
use jinyicheng\upload\implement\CompressedPackageImplement;
use jinyicheng\upload\implement\DocumentImplement;
use jinyicheng\upload\implement\ImageImplement;
use jinyicheng\upload\implement\MediaImplement;
use jinyicheng\upload\implement\ProgramImplement;
use think\Config;
use think\Exception;
use think\Request;
use think\response\Json;

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

    private function __construct($config = [])
    {
        if ((!is_null(Config::get('upload')))) {
            $this->$config = array_merge($this->$config, Config::get('upload'), $config);
        } else {
            $this->$config = array_merge($this->$config, $config);
        }
    }

    public static function getInstance($config = [])
    {
        if ($config === false || $config === []) throw new InvalidArgumentException('upload配置不存在');
        $hash = md5(json_encode($config));
        if (!isset(self::$instance[$hash])) {
            self::$instance[$hash] = new self($config);
        }
        return self::$instance[$hash];
    }

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
     * @param Request $request
     * @return Json
     * @throws Exception
     */
    public function upload(Request $request)
    {
        $upload = $request->file($request->param('field'));
        $extension = strtolower(pathinfo($upload->getInfo('name'), PATHINFO_EXTENSION));
        $typeOfExtension = $this->typeOfExtension($extension);
        if ($typeOfExtension === false) {
            switch ($request->get('plugin')) {
                case 'ueditor':
                    json([
                        'state' => '文件类型' . $extension . '未经许可！',
                        'url' => null,
                        'title' => null,
                        'original' => null,
                        'type' => null,
                        'size' => null,
                        'key' => null
                    ]);
                    break;
                default:
                    return json([
                        'code' => 0,
                        'msg' => '文件类型' . $extension . '未经许可！'
                    ]);
            }
        }
        $fileLogic = self::handle($typeOfExtension);
        $fileLogic_result = $fileLogic->upload(
            $request->param('field'),
            $request->param('is_attachment', false),
            $request->param('status', false),
            $request->param('related_object', ''),
            $request->param('related_id', '')
        );
        if ($fileLogic_result === false) {
            switch ($request->get('plugin')) {
                case 'ueditor':
                    json([
                        'state' => $fileLogic->getError(),
                        'url' => null,
                        'title' => null,
                        'original' => null,
                        'type' => null,
                        'size' => null,
                        'key' => null
                    ])->send();
                    break;
                default:
                    json([
                        'code' => 0,
                        'msg' => $fileLogic->getError()
                    ])->send();
            }
        } else {
            switch ($request->get('plugin')) {
                case 'ueditor':
                    json(
                        array_merge(
                            $fileLogic_result,
                            [
                                'state' => 'SUCCESS'
                            ])
                    )->send();
                    break;
                default:
                    json([
                        'code' => 1,
                        'msg' => '上传成功！',
                        'data' => $fileLogic_result
                    ]);
            }
        }
    }

    /**
     * @param Request $request
     * @return Json|void
     * @throws Exception
     */
    public function delete(Request $request)
    {
        $extension = strtolower(pathinfo($request->post('f_file_name'), PATHINFO_EXTENSION));
        $typeOfExtension = $this->typeOfExtension($extension);
        if ($typeOfExtension === false) {
            throw new Exception($extension . '文件处理接口未定义。');
        }
        $fileLogic = self::handle($typeOfExtension);
        $fileLogic_deleteResult = $fileLogic->delete(
            $request->post('f_file_name', ''),
            $request->post('f_key', '')
        );
        if ($fileLogic_deleteResult === false) {
            $this->error("删除失败！" . $fileLogic->getError());
        } else {
            $this->success("删除成功！");
        }
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
                return ImageImplement::getInstance($config);
                break;
            case 'document':
                return DocumentImplement::getInstance($config);
                break;
            case 'media':
                return MediaImplement::getInstance($config);
                break;
            case 'program':
                return ProgramImplement::getInstance($config);
                break;
            case 'compressed_package':
                return CompressedPackageImplement::getIntance($config);
                break;
            default:
                throw new Exception($type . '文件处理接口未实现。');

        }
    }
}
