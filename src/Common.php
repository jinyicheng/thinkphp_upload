<?php

namespace jinyicheng\upload;

abstract class Common
{
    protected $impl_code;
    protected $impl_message;
    protected $impl_error;

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
    public function getImplError(){
        return $this->impl_error;
    }
}