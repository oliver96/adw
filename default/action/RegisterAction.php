<?php
import("action.CommonAction");
import("model.MemberModel");
import("model.LocationModel");
import("model.StoreModel");
import("model.SellerModel");
import("model.LocationModel");
import("model.MessageLogModel");
import("com.zcx.auth.Session");
import("com.zcx.net.HttpClient");
class RegisterAction extends CommonAction {
    public function index() {
        $hardAddr = $this->request->getParameter('hard_addr');
        $simNo = $this->request->getParameter('sim_no');
        $member = new MemberModel();
        $memInfo = $member->getOne(array('hard_addr' => $hardAddr, 'sim_no' => $simNo));
        if($memInfo) {
            $this->output();
        }
        else {
            $this->redirect(url(array('m' => 'register', 'a' => 'form')));
        }
    }
    
    public function form() {
        $this->output();
    }
    
    public function verify() {
        $this->output();
    }
    
    public function confirm() {
        // 映射地域
        $location = new LocationModel();
        $locMap = $location->getID2NameMap();
        $provMap = $locMap['provMap'];
        $cityMap = $locMap['cityMap'];
        
        // 映射门店
        $store = new StoreModel();
        $storeMap = $store->getID2NameMap();
        
        // 映射店员
        $seller = new SellerModel();
        $sellerMap = $seller->getID2NameMap();
        
        $form = Session::get('CacheForm');
        $form['province'] = $provMap[$form['province']];
        $form['city'] = $cityMap[$form['city']];
        $form['store_name'] = $storeMap[$form['store_id']];
        $form['seller_name'] = $sellerMap[$form['seller_id']];
        
        $this->assign('form', $form);
        $this->output();
    }
    
    public function saveData() {
        $id     = $this->request->getParameter('id');
        $name   = $this->request->getParameter('name');
        $input  = $this->request->getInput();
        
        $member = new MemberModel();
        
        $existRecords = $member->getCount(array('name' => $name));
        if($existRecords > 0) {
            $this->outputJson(array(
                'status' => false, 
                'errors' => array(
                    'name' => '存在相同名称的记录'
                )
            ));
        }
        else {
            Session::set('CacheForm', $input);
            $this->outputJson(array('status' => true));
        }
    }
    
    public function sendMessage() {
        $form = Session::get('CacheForm');
        $mobile = $form['mobile'];
        $curTime = date('Y-m-d H:i:s');
        $verifyCode     = $this->genRandomNum(6);
        $post           = array(
            'username'      => '',
            'pwd'           => '',
            'mobs'          => '',
            'smscp'         => '',
            'taskname'      => '',
            'taskbz'        => '',
            'taskqm'        => '',
            'sfcdx'         => '',
            'fssj'          => $curTime,
            'nr'            => "欢迎您注册电子保卡，您的短信验证码为：" . $verifyCode . "。"
        );
        $smsApi         = c('SMSAPI');
        $content        = trim(HttpClient::quickPost($smsApi, $post));
        if(!empty($content)) {
            $pos = strpos($content, ',');
            $retNo = substr($content, 0, $pos);
            $retErr = substr($content, $pos + 1);
            
            if(1 == $retNo) {
                Session::set('VerifyCode', $verifyCode);
            }
            
            $message = new MessageLogModel();
            $message->insert(array(
                'mobile' => $mobile,
                'code' => $verifyCode,
                'ret_no' => $retNo,
                'ret_err' => $retErr,
                'dateline' => $curTime
            ));
        }
    }
    
    public function doVerify() {
        $output = array('success' => true);
        $code = $this->request->getParameter('code');
        $sendCode = Session::get('VerifyCode');
        
        if($code == $sendCode) {
            $output['success'] = true;
        }
        
        $this->outputJson($output);
    }
    
    public function activate() {
        $output = array('id' => 0);
        $form = Session::get('CacheForm');
        
        $member = new MemberModel();

        $output['id'] = $member->insert($form);
        
        $this->outputJson($output);
    }


    private function genRandomNum($len) { 
        $chars = array("0", "1", "2", "3", "4", "5", "6", "7", "8", "9"); 
        $charsLen = count($chars) - 1; 

        shuffle($chars);    // 将数组打乱 
        
        $output = ""; 
        for ($i=0; $i<$len; $i++) 
        { 
            $output .= $chars[mt_rand(0, $charsLen)]; 
        } 

        return $output; 
    } 
}
?>
