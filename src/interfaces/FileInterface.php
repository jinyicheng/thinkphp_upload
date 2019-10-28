<?php

namespace jinyicheng\upload\interfaces;

interface FileInterface
{
    /**
     * @param $field
     * @param $is_attachment
     * @param $status
     * @param $related_object
     * @param $related_id
     * @return mixed
     */
    public function upload($field, $is_attachment, $status, $related_object, $related_id);

    /**
     * @param $f_file_name
     * @param $f_key
     * @return bool|mixed
     */
    public function delete($f_file_name, $f_key);
}