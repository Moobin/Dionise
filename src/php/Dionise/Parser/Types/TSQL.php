<?php
namespace Dionise\Parser\Types\TSQL;

const BIGINT            = 1;
const BIT               = 2;
const DECIMAL           = 3;
const INT               = 4;
const MONEY             = 5;
const NUMERIC           = 6;
const SMALLINT          = 7;
const SMALLMONEY        = 8;
const TINYINT           = 9;
const FLOAT             = 10;
const REAL              = 11;
const DATE              = 12;
const DATETIMEOFFSET    = 13;
const DATETIME2         = 14;
const SMALLDATETIME     = 15;
const DATETIME          = 16;
const TIME              = 17;
const CHAR              = 18;
const TEXT              = 19;
const VARCHAR           = 20;
const NCHAR             = 21;
const NVARCHAR          = 22;
const NTEXT             = 23;
const BINARY            = 24;
const VARBINARY         = 25;
const IMAGE             = 26;
const CURSOR            = 27;
const TIMESTAMP         = 28;
const HIERARCHYID       = 29;
const UNIQUEIDENTIFIER  = 30;
const SQL_VARIANT       = 31;
const XML               = 32;
const TABLE             = 33;

function TypeStringToID($type)
{
    return constant("Dionise\\Parser\\Types\\TSQL\\" . strtoupper($type));
}