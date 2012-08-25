using System;
using System.Collections.Generic;
using System.Linq;
using System.Text;
using System.Collections;

namespace Dionise.Parser
{
    public class Parser
    {
        private Predicate p;

        public Predicate Predicate { get { return p; } }

        private string predicateText;

        private int objectTypePosition;

        private int schemaDotPosition;

        public Parser(Languages language, string predicate)
        {
            p = new Predicate(language);
            predicateText = predicate;

            p.ObjectType = GetObjectType();
            if (p.ObjectType == ObjectTypes.Undefined)
                throw new ArgumentException("Cannot determine object type for current predicate.");

            p.SchemaName = GetSchemaName();

            p.ObjectName = GetObjectName();
            if (String.IsNullOrEmpty(p.ObjectName) || String.IsNullOrWhiteSpace(p.ObjectName))
                throw new ArgumentException("Cannot determine object name for current predicate.");

            p.Parameters = GetParameters();

            p.ReturnClause = GetReturnClause();

            p.ReturnType = GetReturnValueType();
        }

        private string ObjectTypeStringToEnum(ObjectTypes ot)
        {
            return Enum.GetName(typeof(ObjectTypes), ot);
        }

        private ObjectTypes GetObjectType()
        {
            string PROCEDURE = "PROCEDURE";
            string FUNCTION = "FUNCTION";

            int otProcedurePosition = predicateText.IndexOf(PROCEDURE, 0, StringComparison.InvariantCultureIgnoreCase);
            int otFunctionPosition = predicateText.IndexOf(FUNCTION, 0, StringComparison.InvariantCultureIgnoreCase);

            if (otProcedurePosition >= 0 && otProcedurePosition < predicateText.IndexOf("(", 0))
            {
                objectTypePosition = otProcedurePosition + PROCEDURE.Length;
                return ObjectTypes.Procedure;
            }
            else if (otFunctionPosition >= 0 && otFunctionPosition < predicateText.IndexOf("(", 0))
            {
                objectTypePosition = otFunctionPosition + FUNCTION.Length;
                return ObjectTypes.Function;
            }
            else
            {
                return ObjectTypes.Undefined;
            }
        }

        private string GetSchemaName()
        {
            string objectType = ObjectTypeStringToEnum(p.ObjectType);
            int objectTypeLength = objectType.Length;
            int startPosition = objectTypePosition + objectTypeLength + 1;
            int dotPosition = predicateText.IndexOf(".");
            if (dotPosition >= 0 && dotPosition < predicateText.IndexOf("(", startPosition))
            {
                schemaDotPosition = dotPosition;
                return predicateText.Substring(startPosition, predicateText.IndexOf(".", startPosition) - startPosition).Trim();
            }
            else
            {
                return "";
            }
        }

        private string GetObjectName()
        {
            try
            {
                if (p.SchemaName != "")
                {
                    return predicateText.Substring(schemaDotPosition + 1, predicateText.IndexOf("(", schemaDotPosition + 1) - schemaDotPosition - 1).Trim();
                }
                else
                {
                    return predicateText.Substring(objectTypePosition + 1, predicateText.IndexOf("(", objectTypePosition + 1) - objectTypePosition - 1).Trim();
                }
            }
            catch (Exception)
            {
                return "";
            }
        }

        private List<Parameter> GetParameters()
        {
            var parameters = new List<Parameter>();
            int openingParen = predicateText.IndexOf("(") + 1;
            int closingParen = predicateText.IndexOf(")");

            string[] paramArray = predicateText.Substring(openingParen, closingParen - openingParen).Split(',');

            foreach (string parameter in paramArray)
            {
                parameters.Add(GetParameter(parameter));
            }

            return parameters;
        }

        private Directions DirectionStringToEnum(string direction)
        {
            return (Directions)Enum.Parse(typeof(Directions), direction, true);
        }

        private ObjectTypes ObjectTypeStringToEnum(string objectType)
        {
            return (ObjectTypes)Enum.Parse(typeof(ObjectTypes), objectType, true);
        }

        private Types.PLSQL PLSQLTypeStringToEnum(string type)
        {
            return (Types.PLSQL)Enum.Parse(typeof(Types.PLSQL), type, true);
        }

        private Types.MySQL MySQLTypeStringToEnum(string type)
        {
            return (Types.MySQL)Enum.Parse(typeof(Types.MySQL), type, true);
        }

        private Types.TSQL TSQLTypeStringToEnum(string type)
        {
            return (Types.TSQL)Enum.Parse(typeof(Types.TSQL), type, true);
        }

        private Parameter GetParameter(string parameter)
        {
            string[] blocks = parameter.Trim().Split(' ');
            var param = new Parameter();

            if (p.Language == Languages.MySQL && p.ObjectType == ObjectTypes.Function)
            {
                blocks = new string[] { blocks[0], "", blocks[1] };
            }

            if (p.Language == Languages.PLSQL && blocks.Length == 2)
            {
                blocks = new string[] { blocks[0], "IN", blocks[1] };
            }

            if (p.Language == Languages.PLSQL)
            {
                param.Name = blocks[0];
                param.Direction = DirectionStringToEnum(blocks[1]);
            }
            else if (p.Language == Languages.MySQL)
            {
                if (p.ObjectType == ObjectTypes.Procedure)
                {
                    param.Direction = DirectionStringToEnum(blocks[0]);
                    param.Name = blocks[1];
                }
                else if (p.ObjectType == ObjectTypes.Function)
                {
                    param.Direction = Directions.In;
                    param.Name = blocks[0];
                }
            }
            else if (p.Language == Languages.TSQL)
            {
                param.Name = blocks[0];
            }

            string typeBuffer = blocks[2];

            if (blocks[2].Contains("("))
            {
                typeBuffer = blocks[2].Substring(0, blocks[2].IndexOf("(") - 1);
                if (p.Language == Languages.MySQL)
                {
                    param.Type = MySQLTypeStringToEnum(typeBuffer);
                }
                else if (p.Language == Languages.PLSQL)
                {
                    param.Type = PLSQLTypeStringToEnum(typeBuffer);
                }
                else if (p.Language == Languages.TSQL)
                {
                    param.Type = TSQLTypeStringToEnum(typeBuffer);
                }
                if (blocks[2].Contains(","))
                {
                    string[] sizePrecisionArray = blocks[2].Substring(blocks[3].IndexOf("(") + 1, blocks[2].IndexOf(")") - 1 - blocks[2].IndexOf("(")).Split(',');
                    param.Size = Convert.ToInt32(sizePrecisionArray[0]);
                    param.Precision = Convert.ToInt32(sizePrecisionArray[1]);
                }
                else
                {
                    param.Size = Convert.ToInt32(blocks[2].Substring(blocks[2].IndexOf("(") + 1, blocks[2].IndexOf(")") - 1 - blocks[2].IndexOf("(")));
                }
            }
            else
            {
                if (p.Language == Languages.MySQL)
                {
                    param.Type = MySQLTypeStringToEnum(typeBuffer);
                }
                else if (p.Language == Languages.PLSQL)
                {
                    param.Type = PLSQLTypeStringToEnum(typeBuffer);
                }
                else if (p.Language == Languages.TSQL)
                {
                    param.Type = TSQLTypeStringToEnum(typeBuffer);
                }
            }

            return param;
        }

        private string GetReturnClause()
        {
            if (p.ObjectType != ObjectTypes.Function)
            {
                return "";
            }
            else
            {
                if (p.Language == Languages.PLSQL)
                {
                    return "RETURN";
                }
                else if (p.Language == Languages.MySQL || p.Language == Languages.TSQL)
                {
                    return "RETURNS";
                }
                else
                {
                    return "";
                }
            }
        }

        private object GetReturnValueType()
        {
            if (p.ObjectType != ObjectTypes.Function)
            {
                return null;
            }
            else
            {
                int startPosition = predicateText.IndexOf(p.ReturnClause, 0, StringComparison.InvariantCultureIgnoreCase) + p.ReturnClause.Length + 1;
                return predicateText.Substring(startPosition, predicateText.IndexOf((char)13, startPosition) - startPosition);
            }
        }
    }
}