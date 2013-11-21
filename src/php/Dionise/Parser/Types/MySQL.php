<?php
namespace Dionise\Parser\Types\MySQL;

const SMALLINT      = 1;
const INT           = 2;
const BIGINT        = 3;
const FLOAT         = 4;
const DOUBLE        = 5;
const DECIMAL       = 6;
const BIT           = 7;
const CHAR          = 8;
const VARCHAR       = 9;
const TINYTEXT      = 10;
const TEXT          = 11;
const MEDIUMTEXT    = 12;
const LONGTEXT      = 13;
const BINARY        = 14;
const VARBINARY     = 15;
const TINYBLOB      = 16;
const BLOB          = 17;
const MEDIUMBLOB    = 18;
const LONGBLOB      = 19;
const ENUM          = 20;
const SET           = 21;
const DATE          = 22;
const DATETIME      = 23;
const TIME          = 24;
const TIMESTAMP     = 25;
const YEAR          = 26;
const TINYINT       = 27;

function TypeStringToID($type)
{
    return constant("Dionise\\Parser\\Types\\MySQL\\" . strtoupper($type));
}