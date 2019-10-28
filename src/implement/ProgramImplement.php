<?php

namespace jinyicheng\upload\implement;

use app\admin\validate\FileValidate;
use jinyicheng\upload\interfaces\FileInterface;
use Oss;
use Str;
use think\Config;
use think\Db;
use think\Request;


class ProgramImplement implements FileInterface
{
    private $error = '';

    public function getError()
    {
        return $this->error;
    }

    public $config = [
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
        'save_real_path' => '/home/wwwroot/ssp_v1/public/upload/program',
        'save_relative_path' => '/upload/program'
    ];

    public function __construct($config = [])
    {
        if ((!is_null(Config::get('upload')))) {
            $this->config = array_merge($this->config, Config::get('upload.program'), $config);
        } else {
            $this->config = array_merge($this->config, $config);
        }
    }

    /**
     * 上传
     *
     * @param $field
     * @param bool $is_attachment
     * @param bool $status
     * @param string $related_object
     * @param string $related_id
     * @return bool|array
     */
    public function upload($field, $is_attachment = false, $status = false, $related_object = '', $related_id = '')
    {
        $request = Request::instance();
        $upload = $request->file($field)
            ->validate([
                'size' => $this->config['allow_max_size'],
                'ext' => $this->config['allow_ext']
            ])
            ->rule($this->config['save_rule'])
            ->move($this->config['save_real_path']);
        if ($upload) {
            $data['f_original_name'] = $upload->getInfo('name');
            $data['f_file_name'] = $upload->getFilename();
            $data['f_ext'] = $upload->getExtension();
            $data['f_save_name'] = $upload->getSaveName();
            $data['f_size'] = $upload->getSize();
            $data['f_height'] = '';
            $data['f_width'] = '';
            $data['f_mime'] = $upload->getMime();
            $data['f_type'] = $upload->getType();
            $data['f_md5'] = md5_file($upload->getRealPath());
            $data['f_path'] = $this->config['save_relative_path'] . DS . $data['f_save_name'];
            $data['f_total_size'] = $data['f_size'];
            $data['f_related_object'] = $related_object;
            $data['f_related_id'] = $related_id;
            $data['f_create_time'] = date('Y-m-d H:i:s');
            $data['f_status'] = (int)$status;
            $data['f_attachment'] = (int)$is_attachment;
            $data['f_key'] = Str::keyGen();
            /**
             * 保存数据
             */
            Db::name('file')
                ->insert($data);
            return $data;
        } else {
            $this->error = $upload->getError();
            return false;
        }
    }

    /**
     * 删除
     *
     * @param string $f_file_name 存储名
     * @param string $f_key 操作秘钥
     * @return bool|mixed
     */
    public function delete($f_file_name, $f_key)
    {
        //数据验证
        $fileValidate = new FileValidate();
        $fileValidate_checkResult = $fileValidate->scene('logic_file_delete')->check([
            'f_file_name' => $f_file_name,
            'f_key' => $f_key
        ]);
        if ($fileValidate_checkResult === false) {
            $this->error = $fileValidate->getError();
            return false;
        }

        //删除数据
        return Db::transaction(function () use ($f_file_name, $f_key) {
            $fileDb_findResult = Db::name('file')
                ->find([
                    'f_file_name' => $f_file_name,
                    'f_key' => $f_key
                ]);
            if ($fileDb_findResult != null) {
                unlink(Config::get('upload.image')['save_real_path'] . DS . $fileDb_findResult['f_save_name']);
            }
            Db::name('file')
                ->delete([
                    'f_file_name' => $f_file_name,
                    'f_key' => $f_key
                ]);

        });
    }
}