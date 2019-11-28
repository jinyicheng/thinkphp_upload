<?php

namespace jinyicheng\thinkphp_upload\implement\oss;

use BadFunctionCallException;
use InvalidArgumentException;
use jinyicheng\thinkphp_upload\FileValidate;
use jinyicheng\thinkphp_status\Status;
use jinyicheng\thinkphp_upload\FileInterface;
use jinyicheng\toolbox\Unique;
use think\Config;
use think\Db;
use think\Request;


class ProgramImplement implements FileInterface
{
    use OssTrait;
    private $config = [
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
        //oss配置
        'access_key_id'=>'',
        'access_key_secret'=>'',
        'end_point'=>'',
        'bucket'=>'',
        //保存上传记录的数据表名
        'db_table_name'=>'program'
    ];

    private static $instance = [];

    /**
     * ProgramImplement constructor.
     * @param array $config
     */
    private function __construct($config = [])
    {
        if (Config::has('oss')) {
            $this->config = array_merge($this->config, Config::get('oss'), $config);
        } else {
            $this->config = array_merge($this->config, $config);
        }
        if (!isset($this->config['access_key_id'])) throw new InvalidArgumentException('没有找到oss相关access_key_id设置');
        if (!isset($this->config['access_key_secret'])) throw new InvalidArgumentException('没有找到oss相关access_key_secret设置');
        if (!isset($this->config['end_point'])) throw new InvalidArgumentException('没有找到oss相关end_point设置');
        if (!isset($this->config['bucket'])) throw new InvalidArgumentException('没有找到oss相关bucket设置');
    }

    /**
     * @param array $config
     * @return ProgramImplement
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
     * 上传
     *
     * @param \think\File $file
     * @param bool $is_attachment
     * @param bool $status
     * @param string $related_object
     * @param string $related_id
     * @return bool|array
     */
    public function upload($file, $is_attachment = false, $status = false, $related_object = '', $related_id = '')
    {
        $upload = $file
            ->validate([
                'size' => $this->config['allow_max_size'],
                'ext' => $this->config['allow_ext']
            ])
            ->rule($this->config['save_rule'])
            ->move($this->config['save_real_path'],$this->config['save_rule']=='original'?false:true);
        if ($upload) {
            $data['original_name'] = $upload->getInfo('name');
            $data['file_name'] = $upload->getFilename();
            $data['ext'] = $upload->getExtension();
            $data['save_name'] = $upload->getSaveName();
            $data['size'] = $upload->getSize();
            $data['height'] = '';
            $data['width'] = '';
            $data['mime'] = $upload->getMime();
            $data['type'] = $upload->getType();
            $data['md5'] = md5_file($upload->getRealPath());
            $data['path'] = $this->config['save_relative_path'] . DS . $data['save_name'];
            $data['total_size'] = $data['size'];
            $data['related_object'] = $related_object;
            $data['related_id'] = $related_id;
            $data['create_time'] = date('Y-m-d H:i:s');
            $data['status'] = (int)$status;
            $data['attachment'] = (int)$is_attachment;
            $data['key'] = Unique::token();
            $data['name'] = rtrim($data['original_name'],'.'.$data['ext']);
            $file_save_real_path=$this->config['save_real_path']. DS .$data['save_name'];
            $this->uploadToOss($data['path'],$file_save_real_path);
            /**
             * 保存数据
             */
            Db::name($this->config['db_table_name'])
                ->insert($data);
            return [
                'code'=>Status::get('#200.code'),
                'data'=>$data,
                'message'=>'上传成功'
            ];
        } else {
            return [
                'code'=>Status::get('#4031.code'),
                'data'=>null,
                'message'=>$upload->getError()
            ];
        }
    }

    /**
     * 删除
     *
     * @param string $file_name 存储名
     * @param string $key 操作秘钥
     * @return bool|mixed
     */
    public function delete($file_name, $key)
    {
        //数据验证
        $fileValidate = new FileValidate();
        $fileValidate_checkResult = $fileValidate->scene('delete')->check([
            'file_name' => $file_name,
            'key' => $key
        ]);
        if ($fileValidate_checkResult === false) {
            return [
                'code'=>Status::get('#4031.code'),
                'data'=>null,
                'message'=>$fileValidate->getError()
            ];
        }

        //删除数据
        return Db::transaction(function () use ($file_name, $key) {
            $fileDb_findResult = Db::name($this->config['db_table_name'])
                ->find([
                    'file_name' => $file_name,
                    'key' => $key
                ]);
            if (!is_null($fileDb_findResult)) {
                $this->deleteFromOss($data['path']);
            }
            Db::name($this->config['db_table_name'])
                ->delete([
                    'file_name' => $file_name,
                    'key' => $key
                ]);
            return [
                'code'=>Status::get('#200.code'),
                'data'=>$fileDb_findResult,
                'message'=>'删除成功'
            ];
        });
    }
}