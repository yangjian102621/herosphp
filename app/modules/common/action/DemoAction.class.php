<?php
namespace common\action;

use herosphp\http\HttpRequest;
use herosphp\utils\AjaxResult;
use herosphp\utils\FileUpload;
use herosphp\utils\FileUtils;
use Workerman\Protocols\Http;

/**
 * demo action
 * @package commom\action
 * @author yangjian<yangjian102621@gmail.com>
 */
class DemoAction extends CommonAction {

    protected $serviceBean = "user.news.service";

    public function index(HttpRequest $request) {
        $this->setView("index");
        $this->assign("title", "后台管理中心-首页");
    }

    //空白页
    public function blank() {

        $this->setView("blank");
        $this->assign("title", "空白页");

    }

    //内容列表页
    public function clist(HttpRequest $request) {

        $title = $request->getParameter("title", "trim");
        $conditions = array();
        if ( $title != "" ) {
            $conditions["title"] = "%{$title}%";
        }
        $this->setConditions($conditions);
        parent::index($request);
        $this->assign("title", "文章列表");
        $this->assign("bread", array("用户管理", "文章管理", "文章列表"));
        $this->setView("clist");
    }

    //内容添加页
    public function cadd() {

        $this->assign("title", "文章添加");
        $this->assign("bread", array("用户管理", "文章管理", "文章添加"));
        $this->setView("cadd");

    }

    //文件上传
    public function upload() {

        $this->assign("title", "文件上传");
        $this->assign("bread", array("用户管理", "文章管理", "文件上传"));
        $this->setView("upload");

    }

    public function doUpload() {
        $dir = "upload/".date('Y')."/".date('m');
        $config = array(
            "upload_dir" => RES_PATH.$dir,
            //允许上传的文件类型
            'allow_ext' => 'jpg|jpeg|png|gif|txt|pdf|rar|zip|swf|bmp|c|java|mp3',
            //图片的最大宽度, 0没有限制
            'max_width' => 0,
            //图片的最大高度, 0没有限制
            'max_height' => 0,
            //文件的最大尺寸
            'max_size' =>  2*1024*1024,     /* 文件size的最大 2MB */
        );
        $upload = new FileUpload($config);
        $filename = $upload->upload('src');
        if ( $filename ) {
            AjaxResult::ajaxResult(0, "/res/{$dir}/{$filename["file_name"]}");
        } else {
            AjaxResult::ajaxResult(101, $upload->getUploadMessage());
        }
    }

    public function imageList(HttpRequest $request) {
        $page = $request->getParameter('page', 'intval');
        $offset = ($page - 1) * 15;
        $image_dir = RES_PATH."upload";
        $__files = FileUtils::dirTraversal($image_dir);
        $files = array();
        $counter = 0;
        foreach ( $__files as $value ) {
            if ( $counter <= $offset ) {
                $counter++;
                continue;
            }
            $filename = "/res/upload/".$value;
            $size = getimagesize(APP_ROOT.$filename);
            $files[] = array(
                "thumbURL" => $filename,
                "oriURL" => $filename,
                "width" => intval($size[0]),
                "height" => intval($size[1]));
            if ( $counter > $offset + 15 ) break;
        }
        $code = empty($files) ? 1 : 0;
        AjaxResult::ajaxResult($code, "200 OK", $files);
    }

    //图片搜索
    public function imageSearch(HttpRequest $request) {

        set_time_limit(0);
        $page = $request->getParameter("page", "intval");
        $kw = $request->getParameter("kw", "trim");
        $apiUrl = "http://image.baidu.com/search/avatarjson?tn=resultjsonavatarnew&ie=utf-8&word={$kw}&pn={$page}&rn=15";
        $content = file_get_contents($apiUrl);
        $data = json_decode(mb_convert_encoding($content, 'UTF-8','GBK,UTF-8'), true);
        $files = array();
        if ( is_array($data["imgs"]) ) {
            foreach ( $data["imgs"] as $value ) {
                $filename = basename($value["objURL"]);
                $files[] = array(
                    "thumbURL" => "/common/demo/imageShow/?img_url={$value["objURL"]}&img_path=temp/".$filename,
                    "oriURL" => "/res/upload/temp/".$filename,
                    "width" => $value["width"],
                    "height" => $value["height"]);
            }
        }

        $code = empty($files) ? 1 : 0;
        AjaxResult::ajaxResult($code, "200 OK", $files);

    }

    //抓取并显示图片，解决图片的防盗链
    public function imageShow(HttpRequest $request) {

        $img_url = $request->getParameter("img_url", "trim");
        $img_path = $request->getParameter("img_path", "trim");
        $filename = RES_PATH."upload/".$img_path;
        $image_dir = dirname($filename);
        if ( !file_exists($image_dir) ) {
            FileUtils::makeFileDirs($image_dir);
        }
        if ( $img_path != "" && $img_url != "" )  {

            $image = file_get_contents($img_url);
            if ( $image ) {
                @file_put_contents(RES_PATH."upload/".$img_path, $image);
                $this->show_image(imagecreatefromstring($image), $img_url);
            } else {
                die("图片加载失败！");
            }
        }

    }

    //显示图片
    private function show_image($image, $img_url) {

        $info = pathinfo($img_url);
        switch ( strtolower($info["extension"]) ) {
            case "jpg":
            case "jpeg":
                header('content-type:image/jpg;');
                imagejpeg($image);
                break;

            case "gif":
                header('content-type:image/gif;');
                imagegif($image);
                break;

            case "png":
                header('content-type:image/png;');
                imagepng($image);
                break;

            default:
                header('content-type:image/wbmp;');
                image2wbmp($image);
        }

    }

    //插入操作
    public function insert(HttpRequest $request) {

        AjaxResult::ajaxSuccessResult();
        $data = $request->getParameter("data");
        parent::insert($data);

    }

    //更新操作
    public function update(HttpRequest $request) {

        $data = $request->getParameter("data");
        parent::update($data, $request);
    }

    public function delete(HttpRequest $request) {
        AjaxResult::ajaxSuccessResult();
    }
}
