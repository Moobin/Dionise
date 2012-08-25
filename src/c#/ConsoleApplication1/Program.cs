using System;
using System.Collections.Generic;
using System.Linq;
using System.Text;
using Dionise.Parser;

namespace Dionise.Cons
{
    class Program
    {
        static void Main(string[] args)
        {
            var parser = new Parser.Parser(Languages.PLSQL, @" FUNCTION NEIGHBOURHOOD_EXISTS(i_nid NUMBER) RETURN NUMBER
  IS
  o_exists NUMBER;
  BEGIN
    BEGIN
      SELECT 1
        INTO o_exists
        FROM DUAL
       WHERE EXISTS
       (
        SELECT 'x'
          FROM GEO.neighbourhoods n
         WHERE n.neighbourhood_id = i_nid
       );
    EXCEPTION
      WHEN NO_DATA_FOUND THEN
        o_exists := 0;
    END;
    
    RETURN o_exists;
  END NEIGHBOURHOOD_EXISTS;  ");

            var a = 2;
        }
    }
}
