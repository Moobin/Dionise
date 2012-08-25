<?php
namespace Dionise\Parser;

class Parameter
{
    private $_name;
    private $_direction;
    private $_type;
    private $_size;
    private $_precision;

    public function Name($name = null)
    {
        if (!isset($name))
        {
            return $this->_name;
        }
        else
        {
            $this->_name = $name;
        }
    }

    public function Direction($direction = null)
    {
        if (!isset($direction))
        {
            return $this->_direction;
        }
        else
        {
            if ($direction != Directions\IN
                &&
                $direction != Directions\INOUT
                &&
                $direction != Directions\OUT)
            {
                throw new Exception("Argument not valid: Direction must be In (1), InOut (2) or Out (3)");
            }
            else
            {
                $this->_direction = $direction;
            }
        }
    }

    public function Type($type = null)
    {
        if (!isset($type))
        {
            return $this->_type;
        }
        else
        {
            if (!(0 < $type && $type <= 34))
            {
                throw new Exception("Argument not valid: Type must be defined as CONSTANT in Types Namespace");
            }
            else
            {
                $this->_type = $type;
            }
        }
    }

    public function Size($size = null)
    {
        if (!isset($size))
        {
            return $this->_size;
        }
        else
        {
            $this->_size = $size;
        }
    }

    public function Precision($precision = null)
    {
        if (!isset($precision))
        {
            return $this->_precision;
        }
        else
        {
            $this->_precision = $precision;
        }
    }

}