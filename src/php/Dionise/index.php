<?php
require("Parser/Parser.php");

$statement = <<<statement
CREATE OR REPLACE FUNCTION SCOTT.tax_rate (ssn IN NUMBER, salary IN NUMBER) RETURN NUMBER IS
   sal_out NUMBER;
   BEGIN
      sal_out := salary * 1.1;
   END;
statement;

print_r(new Dionise\Parser\Parser(Dionise\Parser\Languages\PLSQL, $statement));