<?php
namespace Lazy\Schema;

class Column
{
    public $name;

    private $attributes = array();

    function __construct($name, & $attributes)
    {
        $this->name = $name;
        $this->attributes = $attributes;
    }

    public function __isset($name)
    {
        return isset( $this->attributes[ $name ] );
    }

    public function __get($name)
    {
        if( isset($this->attributes[$name]) )
            return $this->attributes[$name];
    }

	public function getValidValues()
	{
		if( $this->validValues ) {
			return $this->validValues;
		} elseif( $this->validValueBuilder ) {
			return call_user_func_array( $this->validValueBuilder );
		}
	}

    /** 
     * deflate value 
     **/
    public function deflate( $value )
    {
        // check self type, do type casting
        return \Lazy\Deflator::deflate( $value , $this->isa );
    }

}



