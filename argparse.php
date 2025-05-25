<?php
/*
 * argparse.php
 *
 * PHP implementation of python argparse module. The argparse module makes it easy to write user-friendly
 * command line interfaces. The module defines what arguments it requires, and argparse will figure out
 * how to parse those out of $argv. The argparse module also automatically generates help and usage
 * messages and issues errors when users give the module invalid arguments.
 *
 * Written by Conrad Shyu (conradshyu@yahoo.com)
*/

class ArgParser {
    private $file = "";
    private $help = "";
    private $epilog = "";
    private $opts = array();        // command line arguments

    public function __construct($file, $help = "", $epilog = "") {
        $this->file = $file; $this->help = $help; $this->epilog = $epilog;
    }   // constructor

    public function add($short, $long, $name, $type, $require = false, $help = "") {
        $arg = new class {
            public $help = "";          // help text
            public $require = false;    // required or optional
            public $key = array();      // short and long options
            public $value = null;       // argument value; default null
        };  // anonymous class; struct for arguments

        $arg->key[$short] = 1;          // short option name
        $arg->key[$long] = 1;           // long option name
        $arg->require = $require;       // required or optional
        $arg->help = $help;             // help text
        $arg->value = $type;            // set the default value
        $this->opts[$name] = $arg;      // save the values

        return($this->opts[$name]);     // return the struct
    }   // add new argument

    public function get($name) {
        return(array_key_exists($name, $this->opts) ? ($this->opts[$name])->value : null);
    }   // retrieve a variable

    public function dump() {
        return($this->opts);
    }   // dump the data structure

    public function help() {
        $default = function($a) {
            $x = "";

            if (is_array($a)) {         // data type: array
                $x = empty($a) ? "None" : sprintf("[%s]", implode(", ", $a));
            } elseif (is_bool($a)) {
                $x = $a ? "true" : "false";
            } else {                    // data type: boolean
                $x = sprintf("%s", empty($a) ? "None" : $a);
            }   // data type: integer, double and string

            return(sprintf("(Default: %s).", $x));
        };  // anonymous function; string for default value

        printf("usage: %s [-h/--help] ", $this->file);

        foreach (array_keys($this->opts) as $i) {
            if (!($this->opts[$i])->require) {
                continue;
            }   // only process required options

            printf("%s %s ", implode("/", array_keys(($this->opts[$i])->key)), strtoupper($i));
        }   // print out required options

        printf("\n\n%s\n\noptional arguments:\n  %-32sShow this help message and exit.\n",
            $this->help, sprintf("%s", "-h, --help"));

        foreach (array_keys($this->opts) as $i) {
            printf("  %-32s%s %s %s\n",
                sprintf("%s %s, %s %s",     // options; short and long
                    array_keys(($this->opts[$i])->key)[0], strtoupper($i),
                    array_keys(($this->opts[$i])->key)[1], strtoupper($i)),
                ($this->opts[$i])->require ? "[Required]" : "[Optional]",
                ($this->opts[$i])->help,
                ($this->opts[$i])->require ? "": $default(($this->opts[$i])->value));
        }   // print out all options

        printf("\n%s\n", $this->epilog); return(true);
    }   // print out the help text

    private function assign($args) {
        $current = null;    // current option

        foreach ($args as $k) {
            if (($k[0] != "-") && !is_null($current)) {
                if (is_array($current->value)) {
                    $current->value[] = $k;
                } else {
                    $current->value = $k;
                }   // assign the value

                continue;
            }   // argument key

            $current = null;    // new option; reset variable

            foreach ($this->opts as $op) {
                if (array_key_exists($k, $op->key)) {
                    $current = $op;

                    if (is_bool($current->value)) {
                        $current->value = !$current->value;
                    }   // special case: toggle the boolean variable
                }   // found the key; assign structure
            }   // search for the key

            if (is_null($current)) {
                echo("Unknown option: $k\n"); return(false);
            }   // option not found
        }   // prase the arguments

        foreach ($this->opts as $op) {
            if ($op->require && (empty($op->value) || is_null($op->value))) {
                printf("Error: %s is required\n", implode("/", array_keys($op->key)));
                return(false);
            }   // required options are not set

            if (is_array($op->value)) {
                $op->value = array_unique($op->value, SORT_STRING);
            }   // only save unique elements
        }   // make sure required variables are set

        return(true);
    }   // parse and assign arguments to struct

    public function parse() {
        $s = array_slice($_SERVER["argv"], 1);  // remove the first element

        if (empty($s) || ($s[0] == "-h") || ($s[0] == "--help")) {
            $this->help(); return(false);
        }   // special case; print out the help text

        return($this->assign($s));
    }   // handle no command line arguments
}

/*
ArgParse()

add($short, $long, $name, $type, $require = false, $help = "") {
$short: required, short option, i.e., -i
$long: required, long option, i.e., --input
$name: required, name of the variable
$type: required, data type
$require: optional, is the option required, true or false, default false
$help: help text for the option, default none

$h =<<<"EOT"
PHP implementation of python argparse. The argparse module makes it easy to write
user-friendly command-line interfaces. The program defines what arguments it
requires, and argparse will figure out how to parse those out of sys.argv. The
argparse module also automatically generates help and usage messages and issues
errors when users give the program invalid arguments.

Written by Conrad Shyu (conradshyu@yahoo.com)
EOT;

$a = new ArgParser(basename(__FILE__), $h);
$a->add("-i", "--input", "input", [], true, "Input file(s)");
$a->add("-j", "--join", "join", "", true, "Join the file(s)");
$a->add("-b", "--base", "base", false, false, "Baseline");
$a->parse();
print_r($a->dump());
*/
?>
