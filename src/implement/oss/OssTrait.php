<?php


namespace jinyicheng\thinkphp_upload\implement\oss;


use OSS\OssClient;

trait OssTrait
{
    /**
     * @param $oss_file_relative_path
     * @param $local_file_real_path
     * @throws \OSS\Core\OssException
     */
    private function uploadToOss($oss_file_save_real_path,$local_file_save_real_path)
    {
        $ossClient = new OssClient($this->config['access_key_id'], $this->config['access_key_secret'], $this->config['end_point']);
        $ossClient->uploadFile($this->config['bucket'],ltrim($oss_file_save_real_path,'/') ,$local_file_save_real_path );
        @unlink($local_file_save_real_path);
    }

    /**
     * @param $oss_file_save_real_path
     * @throws \OSS\Core\OssException
     */
    private function deleteFromOss($oss_file_save_real_path){
        $ossClient = new OssClient($this->config['access_key_id'], $this->config['access_key_secret'], $this->config['end_point']);
        $ossClient->deleteObject($this->config['bucket'],ltrim($oss_file_save_real_path,'/'));
    }
}