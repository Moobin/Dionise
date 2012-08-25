using System;
using System.Collections.Generic;
using System.Linq;
using System.Text;

namespace Dionise.Parser
{
    public class Predicate
    {
        public Languages Language { get; set; }
        public ObjectTypes ObjectType { get; set; }
        public string ObjectName { get; set; }
        public string SchemaName { get; set; }
        public string ReturnClause { get; set; }
        public object ReturnType { get; set; }
        public List<Parameter> Parameters { get; set; }

        public Predicate(Languages language)
        {
            Language = language;
            Parameters = new List<Parameter>();
        }
    }
}
