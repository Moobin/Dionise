<?php
namespace Dionise\Parser\Types\PLSQL;

const BINARY_INTEGER    = 1;
const DEC               = 2;
const DECIMAL           = 3;
const DOUBLE            = 4;
const FLOAT             = 5;
const INT               = 6;
const INTEGER           = 7;
const NATURAL           = 8;
const NATURALN          = 9;
const NUMBER            = 10;
const NUMERIC           = 11;
const PLS_INTEGER       = 12;
const POSITIVEN         = 13;
const POSITIVE          = 14;
const REAL              = 15;
const SIGNTYPE          = 16;
const SMALLINT          = 17;
const CHAR              = 18;
const CHARACTER         = 19;
const LONG              = 20;
const NCHAR             = 21;
const NVARCHAR2         = 22;
const RAW               = 23;
const ROWID             = 24;
const STRING            = 25;
const UROWID            = 26;
const VARCHAR           = 27;
const VARCHAR2          = 28;
const REF_CURSOR        = 29;
const SYS_REFCURSOR     = 30;
const BFILE             = 31;
const BLOB              = 32;
const CLOB              = 33;
const NCLOB             = 34;

function TypeStringToID($type)
{
    return constant("Dionise\\Parser\\Types\\PLSQL\\" . strtoupper($type));
}