<?php
namespace LazyRecord\OperationResult;

class OperationResult
{
    public $id;
    public $code;
    public $success;
    public $message;
    public $sql;

    public function __construct($message = null, $extra = array() )
    {
        $this->message = $message;
        foreach( $extra as $k => $v ) {
            $this->$k = $v;
        }
    }
}



