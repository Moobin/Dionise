<?php
namespace Dionise\Parser;

require("Types/MySQL.php");
require("Types/PLSQL.php");
require("Types/TSQL.php");
require("Directions.php");
require("Languages.php");
require("ObjectTypes.php");
require("Parameter.php");
require("Predicate.php");

class Parser
{
    private $_p = null;

    public function Predicate()
    {
        return $this->_p;
    }

    private $_predicateText = "";

    private $_objectTypePosition = -1;

    private $_schemaDotPosition = -1;

    private $isPLSQL;
    private $isMySQL;
    private $isTSQL;
    private $isProcedure;
    private $isFunction;

    public function __construct($language, $predicate)
    {
        $this->_p = new Predicate($language);
        $this->_predicateText = $predicate;

        $this->Predicate()->ObjectType($this->_GetObjectType());
        if ($this->Predicate()->ObjectType() == ObjectTypes\Undefined)
        {
            throw new Exception("Cannot determine object type for current predicate.");
        }

        $this->Predicate()->SchemaName($this->_GetSchemaName());

        $this->Predicate()->ObjectName($this->_GetObjectName());
        if ($this->Predicate()->ObjectName() == "")
        {
            throw new Exception("Cannot determine object name for current predicate.");
        }

        $this->isPLSQL = $this->Predicate()->Language() == Languages\PLSQL;
        $this->isMySQL = $this->Predicate()->Language() == Languages\MySQL;
        $this->isTSQL = $this->Predicate()->Language() == Languages\TSQL;

        $this->isFunction = $this->Predicate()->ObjectType() == ObjectTypes\Func;
        $this->isProcedure = $this->Predicate()->ObjectType() == ObjectTypes\Procedure;

        $this->Predicate()->Parameters($this->_GetParameters());

        $this->Predicate()->ReturnClause($this->_GetReturnClause());

        $this->Predicate()->ReturnType($this->_GetReturnValueType());
    }

    private function _GetObjectType()
    {
        $PROCEDURE = "PROCEDURE";
        $FUNCTION = "FUNCTION";

        $otProcedurePosition = stripos($this->_predicateText, $PROCEDURE);
        $otFunctionPosition = stripos($this->_predicateText, $FUNCTION);

        if ($otProcedurePosition !== false && $otProcedurePosition >= 0 && $otProcedurePosition < strpos($this->_predicateText, "("))
        {
            $this->_objectTypePosition = $otProcedurePosition + strlen($PROCEDURE);
            return ObjectTypes\Procedure;
        }
        else if ($otFunctionPosition !== false && $otFunctionPosition >= 0 && $otFunctionPosition < strpos($this->_predicateText, "("))
        {
            $this->_objectTypePosition = $otFunctionPosition + strlen($FUNCTION);
            return ObjectTypes\Func;
        }
        else
        {
            return ObjectTypes\Undefined;
        }
    }

    private function _GetSchemaName()
    {
        $objectType = $this->Predicate()->ObjectTypeToString();
        $objectTypeLength = strlen($objectType);
        $startPosition = $this->_objectTypePosition + 1;
        $dotPosition = strpos($this->_predicateText, ".");
        if ($dotPosition >= 0 && $dotPosition < strpos($this->_predicateText, "(", $startPosition))
        {
            $this->_schemaDotPosition = $dotPosition;
            list($schema) = explode(".", trim(substr(
                $this->_predicateText,
                $startPosition,
                strpos($this->_predicateText, "(", $startPosition) - $startPosition
            )));
            return $schema;
        }
        else
        {
            return "";
        }
    }

    private function _GetObjectName()
    {
        if ($this->Predicate()->SchemaName() != "")
        {
            return trim(substr($this->_predicateText, $this->_schemaDotPosition + 1, stripos($this->_predicateText, "(", $this->_schemaDotPosition + 1) - $this->_schemaDotPosition - 1));
        }
        else
        {
            return trim(substr($this->_predicateText, $this->_objectTypePosition + 1, stripos($this->_predicateText, "(", $this->_objectTypePosition + 1) - $this->_objectTypePosition - 1));            
        }
    }

    private function _GetParameters()
    {
        $parameters = array();
        $openingParen = strpos($this->_predicateText, "(") + 1;
        $closingParen = strpos($this->_predicateText, ")");

        $paramArray = explode(",", substr($this->_predicateText, $openingParen, $closingParen - $openingParen));

        foreach ($paramArray as $parameter)
        {
            $parameters[] = $this->_GetParameter($parameter);
        }

        return $parameters;
    }

    private function _GetParameter($parameter)
    {
        $blocks = explode(" ", trim($parameter));
        $param = new Parameter();

        $isMySQLFunction = $this->isMySQL && $this->isFunction;
        $isPLSQLWithOmittedIN = $this->isPLSQL && count($blocks) == 2;

        if ($isMySQLFunction)
        {
            $blocks = array($blocks[0], "", $blocks[1]);
        }

        if ($isPLSQLWithOmittedIN)
        {
            $blocks = array($blocks[0], "IN", $blocks[1]);
        }

        if ($this->isPLSQL)
        {
            $param->Name($blocks[0]);
            $param->Direction = Directions\TypeStringToID($blocks[1]);
        }
        else if ($this->isMySQL)
        {
            if ($this->isFunction)
            {
                $param->Direction(Directions\TypeStringToID($blocks[0]));
                $param->Name($blocks[1]);
            }
            else if ($this->isProcedure)
            {
                $param->Direction(Directions\IN);
                $param->Name($blocks[0]);
            }
        }
        else if ($this->isTSQL)
        {
            $param->Name($blocks[0]);
        }

        $typeBuffer = $blocks[2];
        $typeBufferParens = strpos($blocks[2], "(");

        if ($typeBufferParens !== false)
        {
            $typeBuffer = substr($blocks[2], $typeBufferParens - 1);

            if (strpos($blocks[2], ",") !== false)
            {
                $sizePrecision = explode(",", substr($blocks[2], typeBufferParens + 1, strpos($typeBuffer, ")") - 1 - $typeBufferParens));
                $param->Size = $sizePrecision[0];
                $param->Precision = $sizePrecision[1];
            }
            else
            {
                $param->Size = substr($blocks[2], typeBufferParens + 1, strpos($typeBuffer, ")") - 1 - $typeBufferParens);
            }
        }

        if ($this->isMySQL)
        {
            $param->Type(Types\MySQL\TypeStringToID($typeBuffer));
        }
        else if ($this->isPLSQL)
        {
            $param->Type(Types\PLSQL\TypeStringToID($typeBuffer));   
        }
        else if ($this->isTSQL)
        {
            $param->Type(Types\TSQL\TypeStringToID($typeBuffer));
        }

        return $param;
    }

    private function _GetReturnClause()
    {
        if (!$this->isFunction)
        {
            return "";
        }
        else
        {
            if ($this->isPLSQL)
            {
                return "RETURN";
            }
            else if ($this->isMySQL || $this->isTSQL)
            {
                return "RETURNS";
            }
            else
            {
                return "";
            }
        }
    }

    private function _GetReturnValueType()
    {
        if (!$this->isFunction)
        {
            return null;
        }
        else
        {
            $startPosition = stripos($this->_predicateText, $this->Predicate()->ReturnClause()) + strlen($this->Predicate()->ReturnClause()) + 1;
            list($type) = explode(" ", substr($this->_predicateText, $startPosition));
            if ($this->isMySQL)
            {
                return Types\MySQL\TypeStringToID($type);
            }
            else if ($this->isPLSQL)
            {
                return Types\PLSQL\TypeStringToID($type);   
            }
            else if ($this->isTSQL)
            {
                return Types\TSQL\TypeStringToID($type);
            }
        }
    }
}