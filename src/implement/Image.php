<?php

namespace jinyicheng\thinkphp_upload\implement;

use think\Exception;

class Image
{
    /**
     * @param array $config
     * @return local\ImageImplement|oss\ImageImplement
     */
    public static function getInstance($config = [])
    {
        switch ($config['save_mode']) {
            case 'oss':
                return oss\ImageImplement::getInstance($config);
                break;
            case 'local':
                return local\ImageImplement::getInstance($config);
                break;
            default:
                throw new Exception($config['save_mode'] . '文件处理接口未实现。');
        }
    }
}