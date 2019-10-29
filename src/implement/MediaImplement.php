<?php

namespace jinyicheng\thinkphp_upload\implement;

use jinyicheng\thinkphp_upload\FileValidate;
use jinyicheng\thinkphp_status\Status;
use jinyicheng\thinkphp_upload\FileInterface;
use jinyicheng\toolbox\Unique;
use think\Config;
use think\Db;
use think\Request;


class MediaImplement implements FileInterface
{
    private $config = [
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
        'save_real_path' => '/home/wwwroot/ssp_v1/public/upload/media',
        'save_relative_path' => '/upload/media'
    ];

    private static $instance = [];

    /**
     * CompressedPackageImplement constructor.
     * @param array $config
     */
    private function __construct($config = [])
    {
        $this->$config = array_merge($this->$config, $config);
    }

    /**
     * @param array $config
     * @return CompressedPackageImplement
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
     * @param \Think\file $file_data
     * @param bool $is_attachment
     * @param bool $status
     * @param string $related_object
     * @param string $related_id
     * @return bool|array
     */
    public function upload($file_data, $is_attachment = false, $status = false, $related_object = '', $related_id = '')
    {
        $upload = $file_data
            ->validate([
                'size' => $this->config['allow_max_size'],
                'ext' => $this->config['allow_ext']
            ])
            ->rule($this->config['save_rule'])
            ->move($this->config['save_real_path']);
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
            $data['name'] = str_replace($data['type'],'',$data['original_name']);
            /**
             * 保存数据
             */
            Db::name('file')
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
            $fileDb_findResult = Db::name('file')
                ->find([
                    'file_name' => $file_name,
                    'key' => $key
                ]);
            if (!is_null($fileDb_findResult)) {
                @unlink($this->config['save_real_path'] . DS . $fileDb_findResult['save_name']);
            }
            Db::name('file')
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