<?php

namespace herosphp\string;

/**
 * string buffer utils.
 * @author yangjian<yangjian102621@gmail.com>
 *
 */
class StringBuffer {

    private $strMap = array();

    public function __construct($str)
    {
        if ( $str ) $this->append($str);
    }

    public function isEmpty() {
        return count($this->strMap) == 0;
    }

    //append content
    public function append($str) {

        array_push($this->strMap, $str);

    }

    //append line
    public function appendLine($str) {
        $this->append($str."\n");
    }

    //append line with tab symbol
    public function appendTab($str, $tabNum=1) {

        $tab = "";
        for ( $i = 0; $i < $tabNum; $i++ ) {
            $tab .= "\t";
        }
        $this->appendLine($tab.$str);

    }

    public function toString() {
        return implode("", $this->strMap);
    }

}
