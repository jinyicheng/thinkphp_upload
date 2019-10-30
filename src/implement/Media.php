<?php

namespace jinyicheng\thinkphp_upload\implement;

use think\Exception;

class Media
{
    /**
     * @param array $config
     * @return local\MediaImplement|oss\MediaImplement
     */
    public static function getInstance($config = [])
    {
        switch ($config['save_mode']) {
            case 'oss':
                return oss\MediaImplement::getInstance($config);
                break;
            case 'local':
                return local\MediaImplement::getInstance($config);
                break;
            default:
                throw new Exception($config['save_mode'] . '文件处理接口未实现。');
        }
    }
}