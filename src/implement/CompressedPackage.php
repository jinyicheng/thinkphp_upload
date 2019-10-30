<?php

namespace jinyicheng\thinkphp_upload\implement;

use think\Exception;

class CompressedPackage
{
    /**
     * @param array $config
     * @return local\CompressedPackageImplement|oss\CompressedPackageImplement
     */
    public static function getInstance($config = [])
    {
        switch ($config['save_mode']) {
            case 'oss':
                return oss\CompressedPackageImplement::getInstance($config);
                break;
            case 'local':
                return local\CompressedPackageImplement::getInstance($config);
                break;
            default:
                throw new Exception($config['save_mode'] . '文件处理接口未实现。');
        }
    }
}