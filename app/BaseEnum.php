<?php

namespace App;

use Exception;
use BenSampo\Enum\Contracts\LocalizedEnum;
use BenSampo\Enum\Enum;

/**
 * Class BaseEnum
 *
 * @package App
 */
abstract class BaseEnum extends Enum implements LocalizedEnum
{
    /**
     * Magic getter to retrieve some additional values for the current language.
     *
     * @param  string $attrName The name of the attribute that is asked for.
     *
     * @return mixed
     *
     * @throws Exception If there is no getter method or property defined for the attribute.
     */
    public function __get($attrName)
    {
        if (method_exists($this, $attrName)) {
            return $this->{$attrName}();
        }

        $className = get_class($this);

        throw new Exception("Property or method [{$attrName}] does not exist on [{$className}]");
    }
}
