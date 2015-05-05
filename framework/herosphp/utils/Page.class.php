<?php

namespace herosphp\utils;

/*---------------------------------------------------------------------
 * 此类实现分页, 不依赖数据数的分页 <br /> content paging class.
 * ---------------------------------------------------------------------
 * Copyright (c) 2013-now http://blog518.com All rights reserved.
 * ---------------------------------------------------------------------
 * Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
 * ---------------------------------------------------------------------
 * Author: <yangjian102621@163.com>
 *-----------------------------------------------------------------------*/

//定制分页打印页数
define( 'PAGE_TOTAL_NUM', 1<<0 );        //打印总页数
define( 'PAGE_PREV', 1<<1 );        //打印上一页
define( 'PAGE_DOT', 1<< 2);         //打印省略号
define( 'PAGE_LIST', 1<<3 );        //打印列表页
define( 'PAGE_NEXT', 1<<4 );        //打印下一页
define( 'PAGE_INPUT', 1<<5 );       //打印跳转页
define( 'DEFAULT_PAGE_STYLE', PAGE_TOTAL_NUM | PAGE_PREV | PAGE_LIST | PAGE_NEXT );  //默认样式
define( 'FULL_PAGE_STYLE', DEFAULT_PAGE_STYLE | PAGE_INPUT  );

class Page {
    /* 总记录数 */
    private $_rows_num;
    
    /* 每页记录数 */
    private $_pagesize = 20;
    
    /* 当前页 */
    private $_pagenow;
    
    //总页数
    private $_pagenum;
    
    //请求url
    private $_url;
    
    /* 以pageNow为基准，两边各输出$_out_page页 */
    private $_out_page=3;
	
	private $_page_prev_txt = '<<';		//上一页
	private $_page_next_txt = '>>';		//上一页
	private $_page_total_txt = '总记录：';
	private $_page_goto_txt = 'GO';
    
    private $_limit = ' LIMIT 0, 10';
    
    public function __construct( $rows_num, $pagesize, $page_now, $_out_page=3 ) {
        
        $this->_rows_num = $rows_num;
        $this->_pagesize = $pagesize;
        $this->_pagenow = intval($page_now);
        $this->_out_page = intval($_out_page);
        $this->_pagenum = ceil($rows_num/$pagesize);
        $this->format_pagenow();
        $this->_url = $this->getUrl();
        $this->_limit = ($this->_pagenow - 1) * $this->_pagesize .", {$this->_pagesize}";

    }
    
    /* 获取 LIMIT字符串 */
    public function getLimit() {
        return $this->_limit;
    }
    
    /* 提供一个魔术方法获取limit */
    public function __get( $_var ) {
        if ( $_var == 'limit' ) return $this->_limit; 
    }
    
    /* 格式化当前页 */
    private function format_pagenow() {
        if ( $this->_pagenow > $this->_pagenum ) $this->_pagenow = $this->_pagenum;
        if ( $this->_pagenow <= 0 ) $this->_pagenow = 1;
    }
    
    /**
     * 获取url
     * @return string
     */
    private function getUrl() {
        $url = url($_SERVER['REQUEST_URI']);
        $url = removeUrlArgs($url, 'page');
        $url = trim($url, '/');
        $pos = strrpos($url, '/');
        //判断是否还有其他参数
        if ( $pos === false ) {
            return '/'.$url.'/page-';
        } else {
            return '/'.$url.'page-';
        }
    }
    
    /* 打印上一页 */
    private function prevPage() {
        $_str = '';
        if ( $this->_pagenow > 1 ) {
            $_str .= '<li><a href="'.url($this->_url.($this->_pagenow - 1)).'">'.$this->_page_prev_txt.'</a></li>';
        } else {
            $_str .= '<li class="disabled"><a href="#">'.$this->_page_prev_txt.'</a></li>';
        }   
        return $_str;
    }
    
    /* 打印下一页 */
    private function nextPage() {
        $_str = '';
        if ( $this->_pagenow < $this->_pagenum ) {
            $_str .= '<li><a href="'.url($this->_url.($this->_pagenow + 1)).'">'.$this->_page_next_txt.'</a></li>';
        } else {
            $_str .= '<li class="disabled"><a href="#">'.$this->_page_next_txt.'</a></li>';
        }   
        return $_str;
    }

    /**
     * 打印中间页码列表，以当前页为基准， 两边各输出 $_out_page 页
     * @param int $_style 分页样式
     * @param string $type 返回的数据类型
     * @return string|array
     */
    private function printPageList( $_style, $type='string' ) {
    	
        $_page_list = '';
        $pageData = array();
        //打印左边页码
        $_left = '';
        if ( ($this->_pagenow - $this->_out_page) > 2  ) {
            $_left .= '<li><a href="'.url($this->_url.'1').'" class="page_list page_Rounded5">1</a></li>';
            //$_left .= '<li><a href="'.url($this->_url.'2').'" class="page_list page_Rounded5">2</a></li>';

            $pageData['1'] = url($this->_url.'1');
            
            //打印左边省略号
            if ( $_style & PAGE_DOT ) $_left .= '<li class="disabled"><a href="#">...</a></li>';
            
            for ( $i = ($this->_pagenow - $this->_out_page); $i < $this->_pagenow; $i++ ) {
                $_left .= '<li><a href="'.url($this->_url.$i).'">'.$i.'</a></li>';
                $pageData[$i] = url($this->_url.$i);
            }
        } else {
            for ( $i = 1; $i < $this->_pagenow; $i++ ) {
                $_left .= '<li><a href="'.url($this->_url.$i).'">'.$i.'</a></li>';
                $pageData[$i] = url($this->_url.$i);
            }   
        }
        $_page_list .= $_left;
        
        //打印当前页
        $_page_list .= '<li class="active"><a href="#">'.$this->_pagenow.'</a></li>';
        $pageData[$this->_pagenow] = '#';
        
        /* 打印pageNow 右边的页码 */
        $_right = '';   
        if ( $this->_pagenum >= ($this->_pagenow + $this->_out_page + 2) ) {
            for ( $i = $this->_pagenow+1; $i <= $this->_pagenow + $this->_out_page; $i++ ) {
                $_right .= '<li><a href="'.url($this->_url.$i).'">'.$i.'</a></li>';
                $pageData[$i] = url($this->_url.$i);
            }
            
            //打印右边省略号
            if ( $_style & PAGE_DOT ) $_right .= '<li class="disabled"><a href="#">...</a></li>';
            
            //$_right .= '<li><a href="'.url($this->_url.($this->_pagenum - 1)).'">'.($this->_pagenum - 1).'</a></li>';
            $_right .= '<li><a href="'.url($this->_url.$this->_pagenum).'">'.$this->_pagenum.'</a></li>';
            $pageData[$this->_pagenum] = url($this->_url.$this->_pagenum);

        } else {
            for ( $i = ($this->_pagenow + 1); $i <= $this->_pagenum; $i++  ) {
                $_right .=  '<li><a href="'.url($this->_url.$i).'">'.$i.'</a></li>';
                $pageData[$i] = url($this->_url.$i);
            }   
        }
        $_page_list .= $_right;
        
        if ( $type == 'string' ) {
            return $_page_list;
        } else if ( $type == 'array' ) {
            return $pageData;
        }
    }
    
    private function printInput() {
    	
		return '<li><input type="text" onblur="javascript:var page = this.value;
		document.getElementById(\'page_go_to\').href=\''.$this->_url.'\'+page;" 
		class="form-control my_page_input" value="'.$this->_pagenow.'"><a id="page_go_to" href="/customer/customer/index/?mid=20&amp;smid=27&amp;page=3">
		'.$this->_page_goto_txt.'</a></li>';
       
    }
	
	public function setPagePrevText( $_text ) {
		$this->_page_prev_txt = $_text;
	}
	
	public function setPageNextText( $_text ) {
		$this->_page_next_txt = $_text;
	}
	
	public function setPageTotalText( $_text ) {
		$this->_page_total_txt = $_text;
	}
	
	public function setPageGotoText( $_text ) {
		$this->_page_goto_txt = $_text;
	}

    /**
     * 打印分页列表
     * @param int $_style
     * @return string
     */
    public function showPageHandle( $_style = DEFAULT_PAGE_STYLE ) {

        if ( $this->_pagenum <= 1 ) return false;

        $_html = '';
        if ( $_style & PAGE_TOTAL_NUM ) $_html .= '<li class="disabled"><a href="#">总记录：'.$this->_rows_num.'</a></li>';
        if ( $_style & PAGE_PREV ) $_html .= $this->prevPage();
        if ( $_style & PAGE_LIST ) $_html .= $this->printPageList($_style);
        if ( $_style & PAGE_NEXT ) $_html .= $this->nextPage();
        if ( $_style & PAGE_INPUT ) $_html .= $this->printInput();
		
        return $_html;
		
    }

    /**
     * 获取分页数据
     * @param int $_style
     * @return array
     */
    public function getPageData( $_style = DEFAULT_PAGE_STYLE ) {

        if ( $this->_pagenum <= 1 ) return array();

        $pages = array('url' => $this->_url);
        if ( $_style & PAGE_TOTAL_NUM ) $pages['total'] = $this->_rows_num;
        if ( $_style & PAGE_PREV ) {
            if ( $this->_pagenow > 1 ) {
                $pages['prev'] = $this->_pagenow;
            } else {
                $pages['prev'] = '#';
            }
        }

        if ( $_style & PAGE_LIST ) $pages['list'] .= $this->printPageList($_style, 'array');
        if ( $_style & PAGE_NEXT ) {
            if ( $this->_pagenow < $this->_pagenum ) {
                $pages['prev'] = $this->_pagenow;
            } else {
                $pages['prev'] = '#';
            }
        }
        return $pages;

    }
}
?>