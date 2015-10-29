<?php
/**
 * php中文分词工具,基于词库的中文分词
 * @author yangjian<yangjian102621@gmail.com>
 * @since 2014-07-29

 *
 */
class WordSplit {

    /**
     * @var string 词库的路径
     */
    private static $dictFile;

    /**
     * @var int 最大词长
     */
    private static $maxWordLength = 4;

    /**
     * 词库hash数组
     * @var null
     */
    private static $DIC_HASH = null;

    /**
     * @var array 中文标点符号
     */
    public static $puntuation = array(
            '！'=>1, '￥'=>1, '……'=>1, '×'=>1, '（'=>1, '）'=>1, '——'=>1, '［'=>1,
            '］'=>1, '；'=>1, '：'=>1, '“'=>1, '”'=>1, '‘'=>1, '|'=>1, '、'=>1,
            '《'=>1, '》'=>1, '，'=>1, '。'=>1);


    /**
     * 分词
     * @param   string $text 要分词字符串
     * @param  null $dict_file 词库路径
     * @return string
     */
    public static function split( $text, $dict_file=null ) {

        if ( $dict_file ) {
            self::$dictFile = $dict_file;
        } else {
            self::$dictFile = __DIR__.'/lex.hash.php';
        }
        //加载词库
        if ( self::$DIC_HASH == null ) {
            self::$DIC_HASH = include self::$dictFile;
        }

        $offset = 0;
        $length = strlen($text);
        $_result = array();
        while ( $offset < $length ) {
            $w = '';
            //基本字符和英文
            if ( ord($text[$offset]) <= 127 ) {
                for ( ;
                    ($u = ord($text[$offset])) <= 127 && $offset < $length; ) {
                    if ( self::is_en_punctuation( $u ) ) {
                        $offset++;
                        break;
                    }
                    $w .= $text[$offset++];
                }
                if ( strlen($w) > 1 ) {
                    $_result[] = $w;
                }

            } else {
                //中文标点
                $w = substr($text, $offset, 3);
                if ( self::is_cn_punctuation($w) ) {
                    $offset += 3;
                    continue;
                }

                //正向最大化匹配
                for ( $i = self::$maxWordLength; $i > 0; $i-- ) {
                    $w = substr($text, $offset, $i * 3 );
                    if ( self::$DIC_HASH[$w] == 1 ) {
                        $offset += $i * 3;
                        $_result[] = $w;
                        break;

                    } else if ( $i == 1 ) {		//如果没有在词库找到，则提取一元词
                        //$_result .= $_result==''? $w : '/ ' . $w;
                        $offset += 3;
                    }
                }
            }
        }
        return $_result;
    }

    /**
     * 判断是否是中文标点符号
     * @param $char
     * @return bool
     */
    public static function is_cn_punctuation( $char ) {
        return isset( self::$puntuation[$char] );
    }

    /**
     * 判断是否是英文标点符号
     * @param $u
     * @return bool
     */
    public static function is_en_punctuation( $u ) {
        return ( ($u >= 32 && $u < 48)
            || ( $u > 57 && $u < 65 )
            || ( $u > 122 && $u < 126 ) );
    }

}
