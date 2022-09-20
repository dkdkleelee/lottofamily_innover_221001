<?php
    // +----------------------------------------------------------------------+
    // | PHP version 4                                                        |
    // +----------------------------------------------------------------------+
    // | Copyright (c) 2005 Michal Migurski                                   |
    // +----------------------------------------------------------------------+
    // | This source file is subject to version 3.0 of the PHP license,       |
    // | that is bundled with this package in the file LICENSE, and is        |
    // | available through the world-wide-web at the following url:           |
    // | http://www.php.net/license/3_0.txt.                                  |
    // | If you did not receive a copy of the PHP license and are unable to   |
    // | obtain it through the world-wide-web, please send a note to          |
    // | license@php.net so we can mail you a copy immediately.               |
    // +----------------------------------------------------------------------+
    // | Author: Michal Migurski <mike@teczno.com>                            |
    // +----------------------------------------------------------------------+
    //
    // $Id: JSON.php,v 1.2 2005/01/28 06:25:03 migurski Exp $
    /* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

    define('JSON_SLICE',   1);
    define('JSON_IN_STR',  2);
    define('JSON_IN_ARR',  4);
    define('JSON_IN_OBJ',  8);
    define('JSON_IN_CMT', 16);
    define('JSON_PREFER_ARR', 10);
    define('JSON_PREFER_OBJ', 11);
    
   /** JSON
    * Conversion to and from JSON format.
    * See http://json.org for details.
    *
    * note all strings should be in ASCII or UTF-8 format!
    */
    class JSON
    {
       /** function JSON
        * constructor
        *
        * @param    use     int     object behavior: when encoding or decoding
        *                           {key:value} constructions, use associative
        *                           array or real object (the default).
        *                           in array encoding, arrays are checked to determine
        *                           whether they are regular (contain integer keys)
        *                           or associative (contain no integer keys).
        *
        *                           possible values:
        *                               JSON_PREFER_OBJ - prefer object
        *                               JSON_PREFER_ARR - prefer associative array
        */
        function JSON($use=JSON_PREFER_ARR) // modified by maroo, original value is JSON_PREFER_OBJ
        {
            $this->use = $use;
        }

       /** function encode
        * encode an arbitrary variable into JSON format
        *
        * @param    var     mixed   any number, boolean, string, array, or object to be encoded.
        *                           see argument 1 to JSON() above for array-parsing behavior.
        *                           if var is a strng, note that encode() always expects it
        *                           to be in ASCII or UTF-8 format!
        *
        * @return   string  JSON string representation of input var
        ******
        * simplified by maroo 2005-06-10
        */
        function encode($var)
        {
            switch(gettype($var)) {
                case 'boolean':
                    return $var ? 'true' : 'false';
                
                case 'NULL':
                    return 'null';
                
                case 'integer':
                    return sprintf('%d', $var);
                    
                case 'double':
                case 'float':
                    return sprintf('%f', $var);
                    
                case 'string': // STRINGS ARE EXPECTED TO BE IN ASCII OR UTF-8 FORMAT
                    $var = sprintf('"%s"', str_replace(
                        array('"',"\b","\t","\n","\f","\r","\\"),
                        array('\"','\b','\t','\n','\f','\r','\\'),
                    $var));
                   //return iconv('EUC-KR','UTF-8//ignore',$var); // XXX: 사이트 인코딩이 euc-kr 인것으로 가정하고 utf-8로 컨버팅하여 출력한다.
				   return $var;
				   // @header("Content-Type: text/html;charset=EUC-KR"); 후 출력하면(euc_kr웹페이지에서) 깨어지지 않는다.
				   //return iconv('EUC-KR','cp949',$var);
                    
                case 'array':
                    if($this->use == JSON_PREFER_ARR) { // check the arrays keys, since it might be an associative one that should be treated as an object
                        $is_associative = true;
                        foreach($var as $k => $v)
                            if(is_integer($k))
                                $is_associative = false;
                                
                        if($is_associative) {
                            array_walk($var, array($this, 'name_value'));
                            return sprintf('{%s}', join(',', $var));
                        
                        }
                    }

                    // not an associative array, treat it like a regular array
                    return sprintf('[%s]', join(',', array_map(array($this, 'encode'), $var)));
                    
                case 'object':
                    $vars = get_object_vars($var);
                    array_walk($vars, array($this, 'name_value'));
                    return sprintf('{%s}', join(',', $vars));
                    
                default:
                    return '';
            }
        }
        
       /** function enc
        * alias for encode()
        */
        function enc($var)
        {
            return $this->encode($var);
        }
        
       /** function name_value
        * array-walking function for use in generating JSON-formatted name-value pairs
        *
        * @param    value   mixed   reference to an array element to be encoded
        * @param    name    string  name of key to use
        *
        * @return   string  JSON-formatted name-value pair, like '"name":value'
        */
        function name_value(&$value, $name)
        {
            $value = sprintf("%s:%s", $this->encode($name), $this->encode($value));
        }
        
       /** function decode
        * decode a JSON string into appropriate variable
        *
        * @param    str     string  JSON-formatted string
        *
        * @return   mixed   number, boolean, string, array, or object
        *                   corresponding to given JSON input string.
        *                   see argument 1 to JSON() above for object-output behavior.
        *                   note that decode() always returns strings
        *                   in ASCII or UTF-8 format!
        */
        function decode($str)
        {
            $str = preg_replace('#\s*//(.+)$#m', '', $str); // eliminate single line comments in '// ...' form
            $str = preg_replace('#\s*/\*(.+)\*/#Us', '', $str); // eliminate multi-line comments in '/* ... */' form, at start of string
            $str = preg_replace('#/\*(.+)\*/\s*$#Us', '', $str); // eliminate multi-line comments in '/* ... */' form, at end of string
            $str = trim($str); // eliminate extraneous space
        
            switch(strtolower($str)) {
                case 'true':
                    return true;
    
                case 'false':
                    return false;
                
                case 'null':
                    return null;
                
                default:
                    if(is_numeric($str)) { // Lookie-loo, it's a number
                        // return (float)$str; // This would work on its own, but I'm trying to be good about returning integers where appropriate
                        return ((float)$str == (integer)$str)
                            ? (integer)$str
                            : (float)$str;
                        
                    } elseif(preg_match('/".+"$/s', $str)) { // STRINGS RETURNED IN UTF-8 FORMAT
                        $chrs = substr($str, 1, -1);
                        $utf8 = '';
                        
                        for($c = 0; $c < strlen($chrs); $c++) {
                        
                            if(substr($chrs, $c, 2) == '\b') {
                                $utf8 .= chr(0x08); $c+=1;
    
                            } elseif(substr($chrs, $c, 2) == '\t') {
                                $utf8 .= chr(0x09); $c+=1;
    
                            } elseif(substr($chrs, $c, 2) == '\n') {
                                $utf8 .= chr(0x0A); $c+=1;
    
                            } elseif(substr($chrs, $c, 2) == '\f') {
                                $utf8 .= chr(0x0C); $c+=1;
    
                            } elseif(substr($chrs, $c, 2) == '\r') {
                                $utf8 .= chr(0x0D); $c+=1;
    
                            } elseif((substr($chrs, $c, 2) == '\\"') || (substr($chrs, $c, 2) == '\\\\') || (substr($chrs, $c, 2) == '\\/')) {
                                $utf8 .= $chrs{++$c};
    
                            } elseif(preg_match('/\\\u[0-9A-F]{4}/i', substr($chrs, $c, 6))) { // single, escaped unicode character
                                $utf16 = chr(hexdec(substr($chrs, ($c+2), 2))) . chr(hexdec(substr($chrs, ($c+4), 2)));
                                $utf8 .= mb_convert_encoding($utf16, 'UTF-8', 'UTF-16');
                                $c+=5;
    
                            } elseif((ord($chrs{$c}) >= 0x20) && (ord($chrs{$c}) <= 0x7F)) {
                                $utf8 .= $chrs{$c};
    
                            }
                        
                        }
                        
                        return $utf8;
                    
                    } elseif(preg_match('/\[.+\]$/s', $str) || preg_match('/{.+}$/s', $str)) { // array, or object notation
    
                        if($str{0} == '[') {
                            $stk = array(JSON_IN_ARR);
                            $arr = array();
                        } else {
                            if($this->use == JSON_PREFER_ARR) {
                                $stk = array(JSON_IN_OBJ);
                                $obj = array();
                            } else {
                                $stk = array(JSON_IN_OBJ);
                                $obj = new ObjectFromJSON();
                            }
                        }
    
                        array_push($stk, array('what' => JSON_SLICE, 'where' => 0));
                        $chrs = substr($str, 1, -1);
                        
                        //print("\nparsing {$chrs}\n");
                        
                        for($c = 0; $c <= strlen($chrs); $c++) {
                        
                            $top = end($stk);
                        
                            if(($c == strlen($chrs)) || (($chrs{$c} == ',') && ($top['what'] == JSON_SLICE))) { // found a comma that is not inside a string, array, etc., OR we've reached the end of the character list
                                $slice = substr($chrs, $top['where'], ($c - $top['where']));
                                array_push($stk, array('what' => JSON_SLICE, 'where' => ($c + 1)));
                                //print("Found split at {$c}: [{$slice}]\n");
    
                                if(reset($stk) == JSON_IN_ARR) { // we are in an array, so just push an element onto the stack
                                    array_push($arr, $this->decode($slice));
    
                                } elseif(reset($stk) == JSON_IN_OBJ) { // we are in an object, so figure out the property name and set an element in an associative array, for now
                                    if(preg_match('/\s*(".+[\\\]")\s*:\s*(\S.*)$/Uis', $slice, $parts)) { // "name":value pair
                                        $key = $this->decode($parts[1]);
                                        $val = $this->decode($parts[2]);

                                        if($this->use == JSON_PREFER_ARR) {
                                            $obj[$key] = $val;
                                        } else {
                                            $obj->$key = $val;
                                        }
                                    }
    
                                }
    
                            } elseif(($chrs{$c} == '"') && ($top['what'] != JSON_IN_STR)) { // found a double-quote, and we are not inside a string
                                array_push($stk, array('what' => JSON_IN_STR, 'where' => $c));
                                //print("Found start of string at {$c}\n");
    
                            } elseif(($chrs{$c} == '"') && ($top['what'] == JSON_IN_STR) && ($chrs{$c - 1} != "\\")) { // found a comma, we're in a string, and it's not escaped
                                array_pop($stk);
                                //print("Found end of string at {$c}: {$slice}\n");
    
                            } elseif(($chrs{$c} == '[') && ($top['what'] == JSON_SLICE)) { // found a left-bracket, and we are not inside an array
                                array_push($stk, array('what' => JSON_IN_ARR, 'where' => $c));
                                //print("Found start of array at {$c}\n");
    
                            } elseif(($chrs{$c} == ']') && ($top['what'] == JSON_IN_ARR)) { // found a right-bracket, and we're in an array
                                array_pop($stk);
                                //print("Found end of array at {$c}: {$slice}\n");
    
                            } elseif(($chrs{$c} == '{') && ($top['what'] == JSON_SLICE)) { // found a left-brace, and we are not inside an object
                                array_push($stk, array('what' => JSON_IN_OBJ, 'where' => $c));
                                //print("Found start of object at {$c}\n");
    
                            } elseif(($chrs{$c} == '}') && ($top['what'] == JSON_IN_OBJ)) { // found a right-brace, and we're in an object
                                array_pop($stk);
                                //print("Found end of object at {$c}: {$slice}\n");
    
                            } elseif((substr($chrs, $c, 2) == '/*') && ($top['what'] == JSON_SLICE)) { // found a comment start, and we are not inside one already
                                array_push($stk, array('what' => JSON_IN_CMT, 'where' => $c));
                                $c++;
                                //print("Found start of comment at {$c}\n");
    
                            } elseif((substr($chrs, $c, 2) == '*/') && ($top['what'] == JSON_IN_CMT)) { // found a comment end, and we're in one now
                                array_pop($stk);
                                $c++;
                                
                                for($i = $top['where']; $i <= $c; $i++)
                                    $chrs = substr_replace($chrs, ' ', $i, 1);
                                
                                //print("Found end of comment at {$c}: {$slice}\n");
    
                            }
                        
                        }
                        
                        if(reset($stk) == JSON_IN_ARR) {
                            return $arr;
    
                        } elseif(reset($stk) == JSON_IN_OBJ) {
                            return $obj;
    
                        }
                    
                    }
            }
        }
        
       /** function dec
        * alias for decode()
        */
        function dec($var)
        {
            return $this->decode($var);
        }
        
    }

   /** ObjectFromJSON
    * Generic object wrapper, used in object returns from decode()
    */
    class ObjectFromJSON { function ObjectFromJSON() {} }
    
?>
