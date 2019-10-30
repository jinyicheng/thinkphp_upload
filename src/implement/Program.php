<?php

namespace jinyicheng\thinkphp_upload\implement;

use think\Exception;

class Program
{
    /**
     * @param array $config
     * @return local\ProgramImplement|oss\ProgramImplement
     */
    public static function getInstance($config = [])
    {
        switch ($config['save_mode']) {
            case 'oss':
                return oss\ProgramImplement::getInstance($config);
                break;
            case 'local':
                return local\ProgramImplement::getInstance($config);
                break;
            default:
                throw new Exception($config['save_mode'] . '文件处理接口未实现。');
        }
    }
}