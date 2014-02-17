<?php

namespace MonadPHP;

abstract class Monad {

    protected $value;

    public function __construct($value) {
        $this->value = $value;
    }

    public static function unit($value) {
        if ($value instanceof static) {
            return $value;
        }
        return new static($value);
    }

    public function extract() {
        if ($this->value instanceof self) {
            return $this->value->extract();
        }
        return $this->value;
    }

    public function __call($name, $arguments)
    {
        if ($name == 'bind') {
            $function = array_shift($arguments);
            $args = empty($arguments) ? array() : array_shift($arguments);
            return $this::unit($this->runCallback($function, $this->value, $args));
        }
        throw new \BadMethodCallException('Call to undefined method '.$name);
    }

    protected function runCallback($function, $value, array $args = array()) {
        if ($value instanceof self) {
            return $value->bind($function, $args);
        }
        array_unshift($args, $value);
        return call_user_func_array($function, $args);
    }

}
