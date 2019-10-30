<?php

namespace jinyicheng\thinkphp_upload\implement;

use think\Exception;

class Document
{
    /**
     * @param array $config
     * @return local\DocumentImplement|oss\DocumentImplement
     */
    public static function getInstance($config = [])
    {
        switch ($config['save_mode']) {
            case 'oss':
                return oss\DocumentImplement::getInstance($config);
                break;
            case 'local':
                return local\DocumentImplement::getInstance($config);
                break;
            default:
                throw new Exception($config['save_mode'] . '文件处理接口未实现。');
        }
    }
}