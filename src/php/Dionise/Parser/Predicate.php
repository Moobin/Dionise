<?php
namespace Dionise\Parser;

class Predicate
{
    private $_language;
    private $_objectType;
    private $_objectName;
    private $_schemaName;
    private $_returnClause;
    private $_returnType;
    private $_parameters;

    public function Language($language = null)
    {
        if (!isset($language))
        {
            return $this->_language;
        }
        else
        {
            if ($language != Languages\PLSQL
                &&
                $language != Languages\TSQL
                &&
                $language != Languages\MySQL)
            {
                throw new \Exception("Argument not valid: Supported languages are PL/SQL, T-SQL and MySQL.");
            }
            else
            {
                $this->_language = $language;
            }
        }
    }

    public function ObjectType($objectType = null)
    {
        if (!isset($objectType))
        {
            return $this->_objectType;
        }
        else
        {
            if ($objectType != ObjectTypes\Procedure
                &&
                $objectType != ObjectTypes\Func)
            {
                $this->_objectType = ObjectTypes\Undefined;
                throw new \Exception("Argument not valid: ObjectType must be FUNCTION or PROCEDURE.");
            }
            else
            {
                $this->_objectType = $objectType;
            }
        }
    }

    public function ObjectTypeToString()
    {
        switch ($this->_objectType)
        {
            case ObjectTypes\Procedure:
                return "PROCEDURE";
            case ObjectTypes\Func:
                return "FUNCTION";
            default:
                return "";
        }
    }

    public function ObjectName($objectName = null)
    {
        if (!isset($objectName))
        {
            return $this->_objectName;
        }
        else
        {
            $this->_objectName = $objectName;
        }
    }
   
    public function SchemaName($schemaName = null)
    {
        if (!isset($schemaName))
        {
            return $this->_schemaName;
        }
        else
        {
            $this->_schemaName = $schemaName;
        }
    }

    public function ReturnClause($returnClause = null)
    {
        if (!isset($returnClause))
        {
            return $this->_returnClause;
        }
        else
        {
            $this->_returnClause = $returnClause;
        }
    }

    public function ReturnType($returnType = null)
    {
        if (!isset($returnType))
        {
            return $this->_returnType;
        }
        else
        {
            if (!(0 < $returnType && $returnType <= 34))
            {
                throw new \Exception("Argument not valid: Type must be defined as CONSTANT in Types Namespace");
            }
            else
            {
                $this->_returnType = $returnType;
            }
        }
    }

    public function Parameters($parameters = null)
    {
        if (!isset($parameters))
        {
            return $this->_parameters;
        }
        else
        {
            $this->_parameters = $parameters;
        }
    }

    public function __construct($language)
    {
        $this->Language($language);
        $this->Parameters(array());
    }
}