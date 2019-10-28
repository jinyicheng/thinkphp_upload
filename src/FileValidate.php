<?php

namespace jinyicheng\upload;
use think\Validate;
class FileValidate extends Validate
{
    public function __construct(array $rules = [], $message = [], $field = [])
    {
        parent::__construct($rules, $message, $field);

        $this->regex = array_merge($this->regex, []);

        //验证规则
        $this->rule = array_merge($this->rule, [
            'id'=>'',
            'original_name'=>'',
            'file_name'=>'',
            'ext'=>'',
            'save_name'=>'',
            'size'=>'',
            'height'=>'',
            'width'=>'',
            'mime'=>'',
            'type'=>'',
            'path'=>'',
            'md5'=>'',
            'thumb_file_name'=>'',
            'thumb_ext'=>'',
            'thumb_save_name'=>'',
            'thumb_size'=>'',
            'thumb_height'=>'',
            'thumb_width'=>'',
            'thumb_mime'=>'',
            'thumb_type'=>'',
            'thumb_path'=>'',
            'thumb_md5'=>'',
            'related_object'=>'',
            'related_id'=>'',
            'create_time'=>'',
            'status'=>'',
            'attachment'=>'',
            'key'=>'',
            'total_size'=>'',
            'name'=>''
        ]);

        $this->field = array_merge($this->field, [
            'id'=>'文件ID',
            'original_name'=>'文件原始名',
            'file_name'=>'文件存储名',
            'ext'=>'文件扩展名',
            'save_name'=>'文件类型相对存储路径',
            'size'=>'文件大小',
            'height'=>'高',
            'width'=>'宽',
            'mime'=>'文件MIME',
            'type'=>'文件类型',
            'path'=>'文件项目相对路径',
            'md5'=>'文件md5',
            'thumb_file_name'=>'缩略图存储名',
            'thumb_ext'=>'缩略图扩展名',
            'thumb_save_name'=>'缩略图类型相对存储路径',
            'thumb_size'=>'缩略图大小',
            'thumb_height'=>'缩略图高',
            'thumb_width'=>'缩略图宽',
            'thumb_mime'=>'缩略图MIME',
            'thumb_type'=>'缩略图类型',
            'thumb_path'=>'缩略图项目相对路径',
            'thumb_md5'=>'缩略图md5',
            'related_object'=>'文件关联对象',
            'related_id'=>'文件关联对象ID',
            'create_time'=>'文件创建时间',
            'status'=>'文件状态',
            'attachment'=>'附件标识',
            'key'=>'文件操作秘钥',
            'total_size'=>'文件(含缩略图)总大小',
            'name'=>'文件名'
        ]);

        $this->scene = [
            'delete' => [
                'file_name' => 'require',
                'key' => 'require'
            ]
        ];
    }
}