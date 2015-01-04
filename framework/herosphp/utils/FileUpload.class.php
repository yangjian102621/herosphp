<?php
namespace common\utils;

/**
 * 文件上传类, 支持多文件上传不重名覆盖。支持base64编码文件上传
 * @author 	yangjian<yangjian102621@163.com>
 * @version  1.4
 * @since	2014.04.11
 * ******************************************************************************/
class FileUpload {

    /**
     * @var array 上传文件配置参数
     */
    protected $config = array(
        'upload_dir' => __DIR__,
        'allow_ext' => 'jpg|png|gif',
        'max_size' =>  2097152,     /* 文件的最大尺寸默认 2MB */
     );

    /**
     * @var array 上传文件的信息
     * local_name => 上传文件的客户端名称
     * file_size => 文件大小
     * file_name => 上传文件的新的文件名
     * file_ext => 上传文件的后缀名称
     * file_type => 本次上传的mine类型
     * is_image => 是否是图片
     * image_width => 图片宽度
     * image_height => 图片高度
     * image_size_str => 图片尺寸字符串 width="396" height="341"
     * file_path => 文件的绝对路径
     * file_name => 文件名(带后缀)
     * file_ext => 文件后缀
     * raw_name => 文件名(不带后缀)
     */
    protected $fileInfo = array();

    /**
     * @var string 上传文件的后缀名
     */
    protected $extension = '';

    /**
     * @var int 上传的错误代码
     */
    protected $errNum = 0;

    /**
     * @var array 上传的状态信息
     */
    protected static $_UPLOAD_STATES = array(
        '0'		=> 	'文件上传成功',
        '1'		=> 	'文件超出大小限制',
        '2'		=> 	'文件的大小超过了HTML表单指定值',
        '3'		=> 	'文件只有部分被上传',
        '4'		=> 	'文件没有被上传',
        '5'		=> 	'不允许的文件类型',
        '6'		=> 	'上传目录创建失败',
        '7'		=> 	'文件保存时出错',
        '8'		=> 	'base64解码IO错误'
	);

    /**
     * constructor
     * @param        array() $_config
     * @notice        $_config keys  upload_dir, allowExt, max_szie
     */
	public function __construct( $_config ) {

		if ( !isset($_config) ) return FALSE;

		$this->config = array_merge($this->config, $_config);
		ini_set('upload_max_filesize', ceil( $this->config['max_size'] / (1024*1024) ).'M');
	}

    /**
     * upload file method.
     * @param        sting $_field name of form elements.
     * @param        bool $_base64
     * @return       mixed        $_filename     filename string of uploaded file.
     */
	public function upload( $_field, $_base64 = false ) {
		
		if ( $_base64 ) {
			$_data = $_POST[$_field];
			return $this->makeBase64Image( $_data );
		}
		
		if ( !$this->checkUploadDir() ) {
			$this->errNum = 6;
			return false;
		}
		
		$_localFile = $_FILES[$_field]['name'];
		$_tempFile = $_FILES[$_field]['tmp_name'];
		$_error_no = $_FILES[$_field]['error'];
        $this->fileInfo['client_name'] = $_localFile;
        $this->fileInfo['file_size'] = floatval($_FILES[$_field]['size'] / 1024);

		$_filename = '';
        $this->errNum = $_error_no;
        if ( $this->errNum == 0 ) {
            $this->checkFileType($_localFile);
            if ( $this->errNum == 0 ) {
                $this->checkFileSize($_tempFile);
                if ( $this->errNum == 0 ) {
                    if ( is_uploaded_file($_tempFile) ) {
                        $_new_filename = $this->getFileName($_localFile);
                        $this->fileInfo['file_path'] = $this->config['upload_dir'].DIRECTORY_SEPARATOR.$_new_filename;
                        if ( move_uploaded_file($_tempFile, $this->fileInfo['file_path']) ) {

                            $_filename = $_new_filename;
                            $this->fileInfo['file_name'] = $_filename;
                            $pathinfo = pathinfo($this->fileInfo['file_path']);
                            $this->fileInfo['file_ext'] =  $pathinfo['extension'];
                            $this->fileInfo['raw_name'] = $pathinfo['filename'];

                        } else {
                            $this->errNum = 7;
                        }
                    }
                }
            }
        }
		return $_filename;

	}

	/**
	 * 接收base64位参数，转存图片
	 */
	protected function makeBase64Image( $_base64_data ) {

		$_img = base64_decode($_base64_data);
		$_filename = time().rand( 1 , 1000 ).".png";
		if ( file_put_contents($this->config['upload_dir'].DIRECTORY_SEPARATOR.$_filename, $_img) ) {
			return $_filename;
		}
		$this->errNum = 8;

	}

    /**
     * 获取新的文件名
     * @param $filename
     * @return string
     */
    protected function getFileName($filename) {

		$_ext = $this->getFileExt($filename);
		list($msec, $sec) = explode(' ', microtime());
		return $sec.'-'.substr($msec, 2, 2).'-'.mt_rand(1000, 9999).'.'.$_ext;

	}

    /**
     * 检测上传目录
     * @return bool
     */
    protected function checkUploadDir() {
		if ( !file_exists($this->config['upload_dir']) ) {
			return self::makeFileDirs($this->config['upload_dir']);
		}
		return true;
	}

    /**
     * 创建多级目录
     * @param $path
     * @return bool
     */
    public static function makeFileDirs( $path ) {
        //必须考虑 "/" 和 "\" 两种目录分隔符
        $files = preg_split('/[\/|\\\]/s', $path);
        $_dir = '';
        foreach ($files as $value) {
            $_dir .= $value.DIRECTORY_SEPARATOR;
            if ( !file_exists($_dir) ) {
                mkdir($_dir);
            }
        }
        return true;
    }

    /**
     * 检测文件类型是否合法
     * @param $filename
     */
    protected function checkFileType( $filename ) {

		$_ext = self::getFileExt($filename);
        $_allow_ext = explode("|", $this->config['allow_ext']);
		if ( !in_array($_ext, $_allow_ext) ) {
			$this->errNum = 5;
		}
	}

    /**
     * 获取文件名后缀
     * @param $filename
     * @return string
     */
    protected function getFileExt( $filename ) {
        $_pos = strrpos( $filename, '.' );
        return strtolower( substr( $filename , $_pos+1 ) );
    }

    /**
     * 检查文件大小是否合格
     * @param $filename
     */
    protected function checkFileSize( $filename ) {

        if ( filesize($filename) > $this->config['max_size'] ) {
            $this->errNum = 1;
        }

        if ( function_exists('finfo_open') ) {
            $finfo = finfo_open(FILEINFO_MIME);
            $this->fileInfo['file_type'] = finfo_file($finfo, $filename);
        } else if ( function_exists('mime_content_type') ) {
            $this->fileInfo['file_type'] = mime_content_type($filename);
        }

        if ( strpos($this->fileInfo['file_type'], 'image') !== FALSE ) {

            $this->fileInfo['is_image'] = 1;
            $size = getimagesize($filename);
            if ( ($this->config['max_width'] > 0 && $size[0] > $this->config['max_width'])
                || ($this->config['max_height'] > 0 && $size[1] > $this->config['max_height']) )  {
                $this->errNum = 9;
            }
            $this->fileInfo['image_width'] = $size[0];
            $this->fileInfo['image_height'] = $size[1];
            $this->fileInfo['image_size_str'] = $size[3];

        } else {

            $this->fileInfo['is_image'] = 0;
        }
	}

    /**
     * 获取上传文件信息
     */
    public function getFileInfo() {

        return $this->fileInfo;
    }

    /**
     * get upload message
     * @return       string
     */
	public function getUploadMessage() {
		return self::$_UPLOAD_STATES[$this->errNum];
	}
	
}
?>