<?php

namespace jinyicheng\upload;

abstract class Common
{
    protected $impl_code=null;
    protected $impl_message=null;
    protected $impl_data=null;

    /**
     * @return mixed
     */
    public function getImplCode(){
        return $this->impl_code;
    }

    /**
     * @return mixed
     */
    public function getImplMessage(){
        return $this->impl_message;
    }

    /**
     * @return mixed
     */
    public function getImplData(){
        return $this->impl_data;
    }
}