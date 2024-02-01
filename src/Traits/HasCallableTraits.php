<?php

namespace src\Traits;

trait HasCallableTraits
{

    protected function callMethodInTraits(string $methodName) : array
    {
        $returns = [];

        foreach(get_declared_traits() as $trait)
        {
            $trait = explode("\\", $trait);
            $trait = end($trait);

            $method = $methodName . $trait;

            if(method_exists($this, $method)) {
                $returns[$trait] = $this->{$method}();
            }
        }

        return $returns;
    }

}