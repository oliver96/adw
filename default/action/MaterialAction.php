<?php
import("action.CommonAction");
import("model.MaterialModel");
import("com.zcx.utils.File");
import('com.zcx.net.FileUpload');
class MaterialAction extends CommonAction {
    public function rows() {
        $totalPage  = 0;
        $page       = $this->request->getParameter('page');
        $pageSize   = $this->request->getParameter('pageSize');
        
        if(empty($page) || $page < 0) $page = 1;
        if(empty($pageSize) || $pageSize < 0) $pageSize = 10;
        
        // 实例化广告主模型对象
        $material = new MaterialModel();
        // 广告主总记录数
        $totalCount = $material->getCount();
        
        $rows = array();
        $imageTypes = array('image' => '图片', 'video' => '视频');
        if($totalCount > 0) {
            // 求出记录总页数
            $totalPage  = ceil($totalCount / $pageSize);
            // 获取广告主列表
            $matList    = $material->getList(array(
                'order' => '`id` DESC'
            ), $page, $pageSize);
            
            while($row = $matList->nextRow()) {
                $type       = $row->get('type');
                $rowAry     = $row->toArray();
                $rowAry['type'] = $imageTypes[$type];
                $rows[] = $rowAry;
            }
        }
        
        $this->outputJson(array(
            'page'        => $page
            , 'pageSize'    => $pageSize
            , 'totalPage'   => $totalPage
            , 'data'        => $rows
        ));
    }
    
    public function getData() {
        $output     = array();
        $id         = $this->request->getParameter('id');
        if($id > 0) {
            $material   = new MaterialModel();
            $tplRow     = $material->getOne(array('id' => $id));
            if($tplRow) {
                $output = $tplRow->toArray();
            }
        }
        $this->outputJson($output);
    }
    
    protected function saveData() {
        $id     = $this->request->getParameter('id');
        $name   = $this->request->getParameter('name');
        $input  = $this->request->getInput();
        
        $material = new MaterialModel();
        
        $existRecords = false;
        if($id > 0) {
            $existRecords = $material->getCount(array('id' => "not {$id}", 'name' => $name));
        }
        else {
            $existRecords = $material->getCount(array('name' => $name));
        }
        if($existRecords > 0) {
            $this->outputJson(array(
                'status' => false, 
                'errors' => array(
                    'mat_name' => '存在相同名称的记录'
                )
            ));
        }
        else {
            if($id > 0) {
                $material->update($input, array('id' => $id));
            }
            else {
                $id = $material->insert($input);
            }
            $this->outputJson(array('id' => $id));
        }
    }
    
    public function delete() {
        $output = array('success' => false);
        $ids = $this->request->getParameter('ids');
        if(!empty($ids)) {
            $material = new MaterialModel();
            $material->delete(array('id' => "IN (" . $ids . ")"));
            $output['success'] = true;
        }
        $this->outputJson($output);
    }
    
    public function upload() {
        $output = array('success' => false, 'url' => '', 'error' => '');
        
        $id = $this->request->getParameter('id');
        //如果收到表单传来的参数，则进行上传处理，否则显示表单
        if(isset($_FILES['file'])) {
            if($_FILES['file']['name'] <> ""){
                //包含上传文件类
                //设置文件上传目录
                $savePath = ROOT_PATH . DS . c('SAVEPATH');
                //创建目录
                File::makeDir($savePath);
                //允许的文件类型
                $fileFormat = array('gif','jpg','jpeg','png', 'swf', 'flv', 'application/x-shockwave-flash', 'video/x-flv');
                //文件大小限制，单位: Byte，1KB = 1000 Byte
                //0 表示无限制，但受php.ini中upload_max_filesize设置影响
                $maxSize = 0;
                //覆盖原有文件吗？ 0 不允许  1 允许 
                $overwrite = 0;
                //初始化上传类
                $upload = new FileUpload( $savePath, $fileFormat, $maxSize, $overwrite);
                //如果想生成缩略图，则调用成员函数 $f->setThumb();
                //参数列表: setThumb($thumb, $thumbWidth = 0,$thumbHeight = 0)
                //$thumb=1 表示要生成缩略图，不调用时，其值为 0
                //$thumbWidth  缩略图宽，单位是像素(px)，留空则使用默认值 130
                //$thumbHeight 缩略图高，单位是像素(px)，留空则使用默认值 130
                $upload->setThumb(0);

                //参数中的uploadinput是表单中上传文件输入框input的名字
                //后面的0表示不更改文件名，若为1，则由系统生成随机文件名
                $uploadSuccess = false;
                if (!$upload->run('file', 1)){
                    //通过$f->errmsg()只能得到最后一个出错的信息，
                    //详细的信息在$f->getInfo()中可以得到。
                    if('' == $upload->errmsg()) {
                        $uploadSuccess = true;
                    }
                }
                else {
                    $uploadSuccess = true;
                }
                if($uploadSuccess == true) {
                    //上传结果保存在数组returnArray中。
                    $fileInfos = $upload->getInfo();
                    
                    if(!isset($fileInfos[0]['error']) || empty($fileInfos[0]['error'])) {
                        $output['success'] = $uploadSuccess;
                        $output['type'] = ($fileInfos[0]['type'] == 'application/x-shockwave-flash' || $fileInfos[0]['type'] == 'video/x-flv') ? 'flash' : 'picture';
                        $output['url'] = sprintf("%s/%s", c('IMAGEURL'), $fileInfos[0]['saveName']);
                        $output['size'] = $fileInfos[0]['size'] . 'KB';
                    }
                    else {
                        $output['error'] = $fileInfos[0]['error'];
                    }
                }
                else {
                    $output['error'] = $upload->errmsg();
                }
            }
        }
        $this->outputJson($output);
    }
    
    public function ajaxGetMaterials() {
        $meterials = array();
        $meterial = new MaterialModel();
        $list = $meterial->getList(array(
            'field' => 'id, name'
        ));
        while($row = $list->nextRow()) {
            $id = $row->get('id');
            $name = $row->get('name');
            $meterials[] = array('id' => $id, 'name' => $name);
        }
        $this->outputJson($meterials);
    }
}

?>