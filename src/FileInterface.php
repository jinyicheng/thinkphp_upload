<?php

namespace jinyicheng\upload;

interface FileInterface
{
    /**
     * @param \Think\file $file_data
     * @param $is_attachment
     * @param $status
     * @param $related_object
     * @param $related_id
     * @return mixed
     */
    public function upload($file_data, $is_attachment, $status, $related_object, $related_id);

    /**
     * @param $file_name
     * @param $key
     * @return bool|mixed
     */
    public function delete($file_name, $key);
}