<?php

namespace jinyicheng\upload\implement;

use app\admin\validate\FileValidate;
use jinyicheng\upload\interfaces\FileInterface;
use Oss;
use OSS\Core\OssException;
use Str;
use think\Config;
use think\Db;
use think\Image as ImageEditor;
use think\Request;

//todo:图片更新后需要同步cdn

class ImageImplement implements FileInterface
{
    private $error = '';

    public function getError()
    {
        return $this->error;
    }

    public $config = [
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
        'save_real_path' => '/home/wwwroot/ssp_v1/public/upload/image',
        'save_relative_path' => '/upload/image',
        'create_thumb' => true
    ];

    public function __construct($config = [])
    {
        if ((!is_null(Config::get('upload')))) {
            $this->config = array_merge($this->config, Config::get('upload.image'), $config);
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
     * @throws OssException
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
            $image_info = ImageEditor::open($upload->getRealPath());
            $data['f_original_name'] = $upload->getInfo('name');
            $data['f_file_name'] = $upload->getFilename();
            $data['f_ext'] = $upload->getExtension();
            $data['f_save_name'] = $upload->getSaveName();
            $data['f_size'] = $upload->getSize();
            $data['f_height'] = $image_info->height();
            $data['f_width'] = $image_info->width();
            $data['f_mime'] = $upload->getMime();
            $data['f_type'] = $image_info->type();
            $data['f_md5'] = md5_file($upload->getRealPath());
            $data['f_path'] = $this->config['save_relative_path'] . DS . $data['f_save_name'];

            if ($this->config['create_thumb']) {
                /**
                 * 生成缩略图
                 */
                $data['f_thumb_file_name'] = rtrim($data['f_file_name'], $data['f_ext']) . 'thumb.' . $data['f_ext'];
                $data['f_thumb_save_name'] = str_replace($data['f_file_name'], $data['f_thumb_file_name'], $data['f_save_name']);
                $thumb_pathname = $upload->getPath() . DS . $data['f_thumb_file_name'];
                $thumb_info = ImageEditor::open($upload->getRealPath())
                    ->thumb(150, 150, ImageEditor::THUMB_FILLED)
                    ->save($thumb_pathname);
                $data['f_thumb_ext'] = pathinfo($thumb_pathname, PATHINFO_EXTENSION);
                $data['f_thumb_mime'] = $thumb_info->mime();
                $data['f_thumb_size'] = filesize($thumb_pathname);
                $data['f_thumb_height'] = $thumb_info->height();
                $data['f_thumb_width'] = $thumb_info->width();
                $data['f_thumb_type'] = $thumb_info->type();
                $data['f_thumb_path'] = $this->config['save_relative_path'] . DS . $data['f_thumb_save_name'];
                $data['f_thumb_md5'] = md5_file($thumb_pathname);
                $data['f_total_size'] = $data['f_size'] + $data['f_thumb_size'];
            } else {
                $data['f_total_size'] = $data['f_size'];
            }
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
                ->where('f_file_name','eq',$f_file_name)
                ->where('f_key','eq',$f_key)
                ->find();
            Db::name('file')
                ->where('f_file_name','eq',$f_file_name)
                ->where('f_key','eq',$f_key)
                ->delete();
            if (!is_null($fileDb_findResult)) {
                unlink($this->config['save_real_path'] . DS . $fileDb_findResult['f_save_name']);
                unlink($this->config['save_real_path'] . DS . $fileDb_findResult['f_thumb_save_name']);
            }
        });
    }
}