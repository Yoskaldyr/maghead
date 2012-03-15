<?php
namespace tests {

    use Lazy\Schema\SchemaDeclare;
    use Lazy\Schema;

    class NameSchema extends SchemaDeclare
    {
        function schema()
        {

            $this->column('id')
                ->type('integer')
                ->primary()
                ->autoIncrement();

            $this->column('name')
                ->isa('str')
                ->required()
                ->varchar(128);

			$this->column('description')
				->isa('str')
				->varchar(128);

			$this->column('category_id')
				->isa('int')
				->integer();

            $this->column('address')
                ->isa('str')
                ->validator( function($val,$args,$record) { 
                    if( preg_match( '/fuck/', $val ) )
                        return array( false , "Please don't" );
                    return array( true , "Good" );
                })
				->filter( function($val,$args,$record)  { 
					return str_replace( 'John' , 'XXXX' , $val );
				})
                ->defaultBuilder( function() { 
                    return 'Default Address';
                })
                ->varchar(256);

            $this->column('country')
                ->isa('str')
                ->required()
                ->validValues( array( 'Taiwan' , 'Taipei' , 'Tokyo' ));

            $this->column('type')
                ->isa('str')
                ->validValuesBuilder(function() { 
                    return array('type-a','type-b','type-c');
                });

            $this->column('confirmed')
                ->isa('bool')
                ->boolean();

        }
    }

}
