<?php

namespace App\Traits;

trait GetArray
{
    public function getRoles()
    {
        return [1=>"admin", 2=>"customer", 3=>'vendor'];        
    }
}
