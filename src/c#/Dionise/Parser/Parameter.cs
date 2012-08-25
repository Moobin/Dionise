using System;
using System.Collections.Generic;
using System.Linq;
using System.Text;

namespace Dionise.Parser
{
    public class Parameter
    {
        public string Name { get; set; }
        public Directions Direction { get; set; }
        public object Type { get; set; }
        public int Size { get; set; }
        public int Precision { get; set; }
    }
}
