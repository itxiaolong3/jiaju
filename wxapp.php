<?php
defined('IN_IA') or exit('Access Denied');
header("Access-Control-Allow-Origin: *");
require_once 'AipSpeech.php';
class jiajuModuleWxapp extends WeModuleWxapp
{

    public function doPageTest()
    {
        global $_GPC, $_W;
        $data = array();
        $table = "jiaju_config";
        $configs = array();
        $configsql = "select * from " . tablename($table) . " where `uniacid`=:uniacid and `code`=:code ";
        $params = array(
            ':uniacid' => $_W['uniacid'],
            ':code' => "site"
        );
        $result = pdo_fetch($configsql, $params);
        if (!empty($result)) {
            $configs = iunserializer($result['value']);
        }
        //$configs['appid'], $configs['appsecret']
        $getaccesstoken = $this->getAccessToken($configs['appid'], $configs['appsecret']);
        $data['msg'] = "得到的acctoken" . $getaccesstoken;
        echo $getaccesstoken;

    }
    //获取手机号
    public function doPageGetphone(){
        global $_GPC, $_W;
        $code=$_GPC['code'];
        $encryptedData=$_GPC['encryptedData'];
        $iv=$_GPC['iv'];
        $configtable = "jiaju_config";
        $configs = array();
        $configsql = "select * from " . tablename($configtable) . " where `uniacid`=:uniacid and `code`=:code ";
        $params = array(
            ':uniacid' => $_W['uniacid'],
            ':code' => "site"
        );
        $result = pdo_fetch($configsql, $params);
        if (!empty($result)) {
            $configs = iunserializer($result['value']);
        }
        $session_key=$this->getSession_key($configs['appid'],$configs['appsecret'],$code);
        //开始解密手机号
        $getdata=$this->decodephone($configs['appid'],$session_key,$encryptedData,$iv);
        echo htmlspecialchars_decode($getdata);
        //echo json_encode(htmlspecialchars_decode($getdata));

    }

    //文字转语音
    public function doPageWordtovodio(){
        global $_W, $_GPC;
        // 你的 APPID AK SK
        $APP_ID = '14498317';
        $API_KEY = 'eEmeTNeTn9ek7sT2CTjcoGVn';
        $SECRET_KEY = 'XTsS39jHZC2t9RoxuSofoXYBvNmGvVkr';
        $word=$_GPC['words'];
        if (empty($word)){
            $word='请输入转换文字';
        }
        //设置信息
        $vol=5;//音量
        $per=0;//发音人选择, 0为女声，1为男声，3为情感合成-度逍遥，4为情感合成-度丫丫，默认为普通女
        $spd=5;//语速，取值0-9，默认为5中语速
        $pit=6;//音调，取值0-9，默认为5中语调

        $client =new AipSpeech($APP_ID, $API_KEY, $SECRET_KEY);
        $result = $client->synthesis($word, 'zh', 1, array(
            'vol' => $vol,
            'spd'=>$spd,
            'per'=>$per,
            'pit'=>$pit,
        ));
        $data=array();
        // 识别正确返回语音二进制 错误则返回json 参照下面错误码
        if(!is_array($result)){
            $filename=date('Y-m-d H:i:s',time()).'.mp3';
            file_put_contents('../attachment/aaa/'.$filename, $result);
            $data['msg']='deal success';
            $data['code']=1;
            $data['result']=$_W['attachurl'].'aaa/'.$filename;
            echo json_encode($data);
        }else{
            $data['msg']='deal faill';
            $data['code']=-1;
            echo json_encode($data);
        }

    }
    // 验证码
    public function doPageSendSms(){
        require_once MODULE_ROOT.'/aliyun-dysms-php-sdk/api_demo/SmsDemo.php';
        $code = cache_load('code');
        $res['status']=1;
        $res['code']=$code;
        return $this->result(0,'success',$res);
    }
    public function doPageSiteinfo()
    {
        global $_W, $_GPC;
        $table = "zunyang_188che_config";
        $errno = 0;
        $message = '返回消息';
        $data = array();
        $sql = "select * from " . tablename($table) . " where `uniacid`=:uniacid and `code`=:code ";
        $params = array(
            ':uniacid' => $_W['uniacid'],
            ':code' => "site"
        );

        $result = pdo_fetchall($sql, $params);
        if (!empty($result)) {

            $data = iunserializer($result['value']);
        } else {
            $errno = 1;
            $message = '参数错误';
        }
        return $this->result($errno, $message, $data);

    }
    //////////////////////////////////////////////////
    //我的接口开始
//获取轮播图
    public function doPageGetad()
    {
        global $_W, $_GPC;
        $table = "jiaju_banner";
        $data = array();
        $sql = "select * from " . tablename($table) . " where `uniacid`=:uniacid";
        $params = array(
            ':uniacid' => $_W['uniacid'],
        );
        $result = pdo_fetchall($sql, $params);
        if (empty($result)) {
            $data['status'] = 0;
            $data['msg'] = "没有数据";
        } else {
            foreach ($result as $k=>$v){
                $result[$k]['adimg']=tomedia($v['adimg']);
            }
            $data['status'] = 1;
            $data['msg'] = "返回数据成功";
            $data['result'] = $result;
        }
        echo json_encode($data);
    }
    //获取服务分类
    public function doPageGettype()
    {
        global $_W, $_GPC;
        $table = "jiaju_type";
        $data = array();
        $sql = "select * from " . tablename($table) . " where `uniacid`=:uniacid order by orders desc";
        $params = array(
            ':uniacid' => $_W['uniacid'],
        );
        $result = pdo_fetchall($sql, $params);
        if (empty($result)) {
            $data['status'] = 0;
            $data['msg'] = "没有数据";
        } else {
            foreach ($result as $k=>$v){
                $result[$k]['img']=tomedia($v['img']);
            }
            $data['status'] = 1;
            $data['msg'] = "返回数据成功";
            $data['result'] = $result;
        }
        echo json_encode($data);
    }
    //获取趣文热点
    public function doPageGethot()
    {
        global $_W, $_GPC;
        $table = "jiaju_hot";
        $data = array();
        $sql = "select * from " . tablename($table) . " where `uniacid`=:uniacid order by addtime desc";
        $params = array(
            ':uniacid' => $_W['uniacid'],
        );
        $result = pdo_fetchall($sql, $params);
        if (empty($result)) {
            $data['status'] = 0;
            $data['msg'] = "没有数据";
        } else {
            $data['status'] = 1;
            $data['msg'] = "返回数据成功";
            $data['result'] = $result;
        }
        echo json_encode($data);
    }
    //获取指定趣文热点
    public function doPageGetonehot()
    {
        global $_W, $_GPC;
        $table = "jiaju_hot";
        $data = array();
        $sql = "select * from " . tablename($table) . " where `uniacid`=:uniacid and `id`=:id order by addtime desc";
        $params = array(
            ':uniacid' => $_W['uniacid'],
            ':id'=>$_GPC['id']
        );
        $result = pdo_fetch($sql, $params);
        if (empty($result)) {
            $data['status'] = 0;
            $data['msg'] = "没有数据";
        } else {
            $data['status'] = 1;
            $data['msg'] = "返回数据成功";
            $data['result'] = $result;
        }
        echo json_encode($data);
    }
    //获取分类列表
    public function doPageAllType(){
        global $_W, $_GPC;
        $alldata=array();
        $type=pdo_getall('jiaju_type',array('uniacid'=>$_W['uniacid']),array(),'','orders asc');
        $alldata['data']=$type;
        foreach ($type as $k=>$v){
            if ($v['tid']==0){
                $list=pdo_getall('jiaju_sertype',array('uniacid'=>$_W['uniacid']),array(),'','sid ASC');
            }else{
                $list=pdo_getall('jiaju_sertype',array('uniacid'=>$_W['uniacid'],'pid'=>$v['tid']),array(),'','sid ASC');
            }
            foreach ($list as $kk=>$vv){
                $list[$kk]['img']=tomedia($vv['img']);
            }
            $alldata['data'][$k]['Datas']=$list;
        }
        echo json_encode($alldata);
    }
    //获取详细服务信息
    public function doPageGetservice(){
        global $_W, $_GPC;
        $sid=$_GPC['sid'];
        $info=pdo_get('jiaju_sertype',array('uniacid'=>$_W['uniacid'],'sid'=>$sid));
        $info['img']=tomedia($info['img']);

        $grageandprice=array(
            array("grage"=>$info['onegrage'],"price"=>$info['oneprice'],"bili"=>$info['onescale']),
            array("grage"=>$info['twograge'],"price"=>$info['twoprice'],"bili"=>$info['twoscale']),
            array("grage"=>$info['threegrage'],"price"=>$info['threeprice'],"bili"=>$info['threescale']),
        );
        $dealarr=array();
        foreach ($grageandprice as $k=>$v){
            if ($v['grage']&&$v['price']){
                array_push($dealarr,$v);
            }
        }
        $data['allinfo']=$info;
        $data['grage']=$dealarr;
        echo json_encode($data);
    }
    //微信登录
    public function doPagedoLogin()
    {
        global $_W, $_GPC;
        $usertable = "jiaju_user";
        $js_code = $_GPC['code'];
        $insertdata=array();
        $insertdata['nickname']=$_GPC['nickname'];
        $insertdata['headerimg']=$_GPC['headerimg'];
        $insertdata['uniacid']=$_W['uniacid'];
        $insertdata['addtime']=date('Y-m-d H:i:s',time());
        $table = "jiaju_config";
        $configs = array();
        $configsql = "select * from " . tablename($table) . " where `uniacid`=:uniacid and `code`=:code ";
        $params = array(
            ':uniacid' => $_W['uniacid'],
            ':code' => "site"
        );
        $result = pdo_fetch($configsql, $params);
        if (!empty($result)) {
            $configs = iunserializer($result['value']);
        }
        $openid = $this->getOpendId($configs['appid'], $configs['appsecret'], $js_code);
        $data = array();
        if (!empty($openid)) {
            $insertdata['openId']=$openid;
            $issave=pdo_get($usertable,array('openId'=>$openid));
            if (empty($issave)){
                $re=pdo_insert($usertable,$insertdata);
                if ($re){
                    $data['status'] = 1;
                    $data['userinfo'] = $issave;
                    $data['msg'] = '保存用户数据成功';
                }else{
                    $data['status'] = 0;
                    $data['msg'] = '保存用户数据失败';
                }
            }else{
                $data['status'] = 1;
                $data['msg'] = '已登录过';
                $data['userinfo'] = $issave;
            }
        } else {
            $data['code'] = $js_code;
            $data['openid'] = $openid;
            $data['status'] = -1;
            $data['msg'] = 'doPageCheckinfo中无法获取openid';
        }
        echo json_encode($data);

    }
    //获取用户信息
    public function doPageGetuserinfo(){
        global $_W, $_GPC;
        $openid=$_GPC['openid'];
        $info=pdo_get('jiaju_user',array('openId'=>$openid));
        if ($info){
            $data['code']=1;
            $data['result']=$info;
        }else{
            $data['code']=0;
            $data['result']=array();
        }
        echo json_encode($data);
    }
    //保存地址
    public function doPageSaveadd(){
        global $_W, $_GPC;
        $getid=$_GPC['id'];//如果有id就是添加
        $savadata['name']=$_GPC['name'];
        $savadata['phone']=$_GPC['phone'];
        $savadata['gender']=$_GPC['gender'];
        $savadata['label']=$_GPC['label'];
        $savadata['address']=$_GPC['address'];
        $savadata['detailadd']=$_GPC['detailadd'];
        $savadata['openid']=$_GPC['openid'];
        $savadata['is_default']=$_GPC['is_default'];
        $savadata['uniacid']= $_W['uniacid'];
        $savadata['addtime']=time();
        $ishavadefault=pdo_get("jiaju_address",array('openid'=>$_GPC['openid'],'uniacid'=>$_W['uniacid'],"is_default"=>1));
        if ($getid){
            if (!empty($_GPC['latitude'])&&!empty($_GPC['longitude'])){
                $savadata['latitude']=$_GPC['latitude'];
                $savadata['longitude']=$_GPC['longitude'];
            }
            if($ishavadefault){
                //已经有默认了
                if($_GPC['is_default']){//此条地址是否设置默认
                    pdo_update('jiaju_address',array('is_default'=>0),array('id'=>$ishavadefault['id']));
                    $savadata['is_default']=1;
                }else{
                    $savadata['is_default']=0;
                }
            }else{
                //还没有默认
                $savadata['is_default']=1;
            }
            //编辑
            $res=pdo_update('jiaju_address',$savadata,array('id'=>$getid));
            if($res){
                $resdata['state']=1;
                $resdata['msg']='修改成功';
            }else{
                pdo_update('jiaju_address',array('is_default'=>1),array('id'=>$ishavadefault['id']));
                $resdata['state']=0;
                $resdata['msg']='修改失败';
            }
            echo json_encode($resdata);

        }else{
            //添加
            $savadata['latitude']=$_GPC['latitude'];
            $savadata['longitude']=$_GPC['longitude'];
            if($ishavadefault){
                //已经有默认了
                if($_GPC['is_default']){//此条地址是否设置默认
                    pdo_update('jiaju_address',array('is_default'=>0),array('id'=>$ishavadefault['id']));
                    $savadata['is_default']=1;
                }else{
                    $savadata['is_default']=0;
                }
            }else{
                //还没有默认
                $savadata['is_default']=1;
            }
            $res=pdo_insert("jiaju_address",$savadata);
            if($res){
                $data['state']=1;
                $data['msg']='添加成功';
            }else{
                pdo_update('jiaju_address',array('is_default'=>1),array('id'=>$ishavadefault['id']));
                $data['state']=0;
                $data['msg']='添加失败';
            }
            echo json_encode($data);
        }

    }
    //我的地址
    public function doPageMyAddress(){
        global $_W, $_GPC;
        $openid=$_GPC['openid'];
        $res=pdo_getall('jiaju_address',array('openid'=>$openid), array() , '' , 'is_default DESC');
        foreach ($res as $k=>$v){
            $res[$k]['all_address']=$v['address'].$v['detailadd'];
        }
        $data['Data']=$res;
        echo json_encode($data);
    }
    //获取单一地址
    public function doPagegetoneAdd(){
        global $_W, $_GPC;
        $ID=$_GPC['id'];
        $openid=$_GPC['openid'];
        if ($ID){
            $res=pdo_get('jiaju_address',array('id'=>$ID));
        }else{
            $res=pdo_get('jiaju_address',array('openid'=>$openid,'is_default'=>1));
            if (empty($res)){
                $res=pdo_get('jiaju_address',array('openid'=>$openid), array() , '' , 'is_default DESC');
            }
        }
        $res['mobile']=$res['phone'];
        $res['detail_info']=$res['detailadd'];
        $data['Data']=$res;
        echo json_encode($data);
    }
    //删除地址
    public function doPageDelAdd(){
        global $_W, $_GPC;
        $resdata=array();
        $id=$_GPC['id'];
        $res=pdo_delete('jiaju_address',array('id'=>$id));
        if($res){
            $resdata['state']=1;
            $resdata['msg']='删除成功';
        }else{
            $resdata['state']=0;
            $resdata['msg']='删除失败';
        }
        echo json_encode($resdata);
    }
    //添加formid
    public function doPageAddFormId(){
        global $_W, $_GPC;
        if($_GPC['form_id']!="the formId is a mock one" and $_GPC['form_id']){
            $data['openid']=$_GPC['openid'];
            $data['form_id']=$_GPC['form_id'];
            $data['uniacid']=$_W['uniacid'];
            $data['time']=time();
            $res=pdo_insert('jiaju_formid',$data);
        }
    }
    //提交预约订单
    public function doPageSubmitOrder(){
        global $_W, $_GPC;
        $data['doc_id']=$_GPC['doc_id'];//选中服务id
        $getsertype=pdo_get('jiaju_sertype',array('sid'=>$_GPC['doc_id']));
        $openid=$_GPC['openid'];
        $data['sertype']=$getsertype['name'];
        $data['true_name']=$_GPC['true_name'];
        $data['mobile']=$_GPC['mobile'];
        $data['address']=$_GPC['address'];
        $data['remark']=$_GPC['remark'];
        $data['price']=$_GPC['price'];
        $data['grage']=$_GPC['grage'];
        $data['bili']=$_GPC['bili'];
        $data['imgs']=$_GPC['imgs'];
        $data['openid']=$openid;
        $data['uniacid']=$_W['uniacid'];
        $data['ordernum']=$this->randNum(8);
        $data['addtime']=date('Y-m-d H:i:s',time());
        $addre=pdo_insert("jiaju_order",$data);
        $newid=pdo_insertid();
        if ($addre){
            ///////////////模板消息///////////////////
            $this->sendweimsg($newid,$openid);
            ///////////////模板消息///////////////////
            $resdata['code']=1;
            $resdata['msg']='提交成功';

        }else{
            $resdata['code']=0;
            $resdata['msg']='提交失败';
        }
        echo json_encode($resdata);
    }
    function getconfig(){
        global $_W, $_GPC;
        $configs = array();
        $configsql = "select * from " . tablename("jiaju_config") . " where `uniacid`=:uniacid and `code`=:code ";
        $params = array(
            ':uniacid' => $_W['uniacid'],
            ':code' => "site"
        );
        $result = pdo_fetch($configsql, $params);
        if (!empty($result)) {
            $configs = iunserializer($result['value']);
        }
        return $configs;
    }
    function randNum($length)
    {
        $pattern = '12345678901234567890ABCDEFGHI';
        $key='';
        for($i=0;$i<$length;$i++)
        {
            $key .= $pattern{mt_rand(0,28)};    //生成php随机数
        }
        return $key;
    }
    //发送模板消息
    function sendweimsg($oid,$openid){
        global $_W, $_GPC;
        $res=$this->getconfig();
        $appid=$res['appid'];
        $secret=$res['appsecret'];
        $access_token = $this->getAccessToken($appid,$secret);
        $res2=pdo_get('jiaju_order',array('id'=>$oid));
        $form=pdo_get('jiaju_formid',array('openid'=>$openid,'time >='=>time()-60*60*24*7),array(),'','time desc');

        $formwork ='{
           "touser": "'.$_GPC['openid'].'",
           "template_id": "'.$res["xd_tid"].'",
           "page": "pages/service/index/index",
           "form_id":"'.$form['form_id'].'",
           "data": {
             "keyword1": {
               "value": "'.$res2['sertype'].'",
               "color": "#173177"
             },
             "keyword2": {
               "value":"'.$res2['ordernum'].'",
               "color": "#173177"
             },
             "keyword3": {

               "value": "'.$res2['true_name'].'",
               "color": "#173177"
             },
             "keyword4": {
               "value":  "'.$res2['mobile'].'",
               "color": "#173177"
             },
             "keyword5": {
               "value": "'.$res2['addtime'].'",
               "color": "#173177"
             },
             "keyword6": {
               "value": "'.$res2['price']."元".'",
               "color": "#173177"
             }
             ,
             "keyword7": {
               "value": "'.$res2['address'].'",
               "color": "#173177"
             },
             "keyword8": {
               "value": "'.$res2['remark'].'",
               "color": "#173177"
             }
           },
            "emphasis_keyword": ""
         }';
        // $formwork=$data;
        $url = "https://api.weixin.qq.com/cgi-bin/message/wxopen/template/send?access_token=".$access_token."";
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL,$url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER,0);
        curl_setopt($ch, CURLOPT_POST,1);
        curl_setopt($ch, CURLOPT_POSTFIELDS,$formwork);
        $data = curl_exec($ch);
        curl_close($ch);
        pdo_delete('jiaju_formid',array('id'=>$form['id']));
    }
    //获取个人订单
    public function doPageUserOrder(){
        global $_W, $_GPC;
        $page=max(1, intval($_GPC['page']));
        $pagesize=8;
        $openid=$_GPC['openid'];
        $type=$_GPC['active'];//0全部 1待受理 2服务中 3待评价 4已完成
        if(empty($type)){
            $result=pdo_getall('jiaju_order',array('uniacid'=>$_W['uniacid'],'openid'=>$openid), array() , '' , 'addtime DESC' );
        }else{
            $sql="select * from " . tablename("jiaju_order") . " where `uniacid`=:uniacid and `openid`=:openid and `state`=:state order by addtime desc";
            $params = array(
                ':uniacid' => $_W['uniacid'],
                ':openid' => $openid,
                ':state'=>$type
            );
            $select_sql =$sql." LIMIT " .($page - 1) * $pagesize.",".$pagesize;
            $result = pdo_fetchall($select_sql, $params);
        }
        foreach ($result as $k=>$v){
            $result[$k]['imgs']=explode(',',$v['imgs']);
        }
        $resdata['Data']=$result;
        echo json_encode($resdata);

    }
    //获取三种订单数量
    public function doPageGetordercount(){
        global $_W, $_GPC;
        $data['Data']['wait']=pdo_fetchcolumn("SELECT COUNT(*) FROM ".tablename('jiaju_order')
            .' where openid=:openid and uniacid=:uniacid and state=1',array('openid'=>$_GPC['openid'],'uniacid'=>$_W['uniacid']));
        $data['Data']['sering']=pdo_fetchcolumn("SELECT COUNT(*) FROM ".tablename('jiaju_order').
            ' where openid=:openid and uniacid=:uniacid and state=2',array('openid'=>$_GPC['openid'],'uniacid'=>$_W['uniacid']));
        $data['Data']['pingjia']=pdo_fetchcolumn("SELECT COUNT(*) FROM ".tablename('jiaju_order')
            .' where openid=:openid and uniacid=:uniacid and state=3',array('openid'=>$_GPC['openid'],'uniacid'=>$_W['uniacid']));
        echo json_encode($data);
    }
    //获取订单详细
    public function doPagegetOrderDetail(){
        global $_W, $_GPC;
        $detailinfo=pdo_get('jiaju_order',array('id'=>$_GPC['id']));
        $detailinfo['imgs']=explode(',',$detailinfo['imgs']);
        $data['Data']=$detailinfo;
        echo json_encode($data);
    }
    /////////////////////////////////////////////////////////////
    //我的接口结束
    //获取问题
    public function doPageGetprob()
    {
        global $_W, $_GPC;
        $table = "jiaju_problem";
        $data = array();
        $sql = "select * from " . tablename($table) . " where `uniacid`=:uniacid";
        $params = array(
            ':uniacid' => $_W['uniacid'],
        );
        $result = pdo_fetchall($sql, $params);
        if (empty($result)) {
            $data['status'] = 0;
            $data['msg'] = "没有数据";
        } else {
            $data['status'] = 1;
            $data['msg'] = "返回数据成功";
            $data['result'] = $result;
        }
        echo json_encode($data);
    }
    //获取关于
    public function doPageGetabout()
    {
        global $_W, $_GPC;
        $table = "jiaju_about";
        $sql = "SELECT * FROM " . tablename($table) . " WHERE uniacid = :uniacid";
        $params = array(':uniacid' => $_W['uniacid']);
        $setting = pdo_fetch($sql, $params);
        $result = iunserializer($setting['value']);
        if (empty($result)) {
            $data['status'] = 0;
            $data['msg'] = "没有数据";
        } else {
            $result['logoimg']=tomedia($result['logoimg']);
            $data['status'] = 1;
            $data['msg'] = "返回数据成功";
            $data['result'] = $result;
        }
        echo json_encode($data);
    }
    //获取协议
    public function doPageGetxieyi()
    {
        global $_W, $_GPC;
        $table = "jiaju_xieyi";
        $sql = "SELECT * FROM " . tablename($table) . " WHERE uniacid = :uniacid";
        $params = array(':uniacid' => $_W['uniacid']);
        $setting = pdo_fetch($sql, $params);
        $result = iunserializer($setting['value']);
        if (empty($result)) {
            $data['status'] = 0;
            $data['msg'] = "没有数据";
        } else {
            $data['status'] = 1;
            $data['msg'] = "返回数据成功";
            $data['result'] = $result;
        }
        echo json_encode($data);
    }

    //检查用户信息是否完整和是否登录
    public function doPageCheckinfo()
    {
        global $_W, $_GPC;
        $table = "jiaju_user";
        $js_code = $_GPC['code'];
        $table = "jiaju_config";
        $configs = array();
        $configsql = "select * from " . tablename($table) . " where `uniacid`=:uniacid and `code`=:code ";
        $params = array(
            ':uniacid' => $_W['uniacid'],
            ':code' => "site"
        );
        $result = pdo_fetch($configsql, $params);
        if (!empty($result)) {
            $configs = iunserializer($result['value']);
        }
        //$configs['appid'], $configs['appsecret']
        $openid = $this->getOpendId($configs['appid'], $configs['appsecret'], $js_code);
        $data = array();
        if (!empty($openid)) {

            $sql = "select * from " . tablename($table) . " where `uniacid`=:uniacid and `openId`=:openId order by addtime desc";
            $params = array(
                ':uniacid' => $_W['uniacid'],
                ':openId' => $openid
            );
            $result = pdo_fetch($sql, $params);

            if (empty($result)) {
                $data['result'] = array();
                $data["status"] = -1;
            } else {
                $errno = 1;
                $data["status"] = 1;
                $data['result'] = $result;
            }

        } else {
            $data['status'] = -1;
            $data['msg'] = 'doPageCheckinfo中无法获取openid';
        }
        echo json_encode($data);

    }

    /*处理发帖上传图片*/
    public function doPagePostuploadimg()
    {

        $message = "请求到服务器";
        global $_GPC, $_W;
        $uptypes = array('image/jpg', 'image/jpeg', 'image/png', 'image/pjpeg', 'image/gif', 'image/bmp', 'image/x-png');
        $max_file_size = 2000000; //上传文件大小限制, 单位BYTE
        $destination_folder = "../attachment/" . $_GPC['m'] . "/" . date('Ymd') . "/"; //上传文件路径
        $arr = array();
        $errno = 0;
        if (!is_uploaded_file($_FILES["file"]['tmp_name'])) //是否存在文件
        {
            $arr['status'] = 0;
            $arr['message'] = '图片不存在!';
            $message = "图片不存在!";
            print_r($message);
            $errno = 1;
            return $this->result($errno, $message, $arr);
            exit;
        }
        $file = $_FILES["file"];
        $arr['file'] = $file;
        if ($max_file_size < $file["size"]) //检查文件大小
        {
            $arr['status'] = 0;
            $arr['message'] = '文件太大';
            $message = "文件太大";
            print_r($message);
            $errno = 1;
            return $this->result($errno, $message, $arr);

            exit;
        }
        if (!in_array($file["type"], $uptypes)) //检查文件类型
        {
            $arr['status'] = 0;
            $message = "文件类型不符!" . $file["type"];
            print_r($message);
            $errno = 1;
            return $this->result($errno, $message, $arr);
            exit;
        }

        if (!file_exists($destination_folder)) {
            mkdir($destination_folder);
        }
        $filename = $file["tmp_name"];
        $pinfo = pathinfo($file["name"]);
        $ftype = $pinfo['extension'];
        $destination = $destination_folder . str_shuffle(time() . rand(111111, 999999)) . "." . $ftype;
        if (file_exists($destination)) {
            $arr['status'] = 0;

            $message = "同名文件已经存在了!" . $file["type"];
            print_r($message);
            $errno = 1;
            return $this->result($errno, $message, $arr);
            exit;
        }
        if (!move_uploaded_file($filename, $destination)) {
            $arr['status'] = 0;
            $message = "移动文件出错";
            print_r($message);
            $errno = 1;
            return $this->result($errno, $message, $arr);
            echo $arr;
            exit;
        }
        $pinfo = pathinfo($destination);
        $fname = $pinfo['basename'];
        $arr['imgname'] = "文件名：" + $fname;
        //echo $fname;
        @require_once(IA_ROOT . '/framework/function/file.func.php');
        @$filename = $fname;
        @file_remote_upload($filename);
        //https://pj.dede1.com/attachment/../attachment/zunyang_chefu188/20180515/9158665524662366.jpg
        $getdealurl = substr($destination, 14);
        $message = "生成的文件路径：" . tomedia($getdealurl);
        $arr['imgpath'] = tomedia($getdealurl);
        //print_r($message);
        // json_encode($arr);
        return $this->result($errno, $message, $arr);

    }

    /*保存门店入驻内容和图片*/
    public function doPagePostshenqing()
    {
        global $_W, $_GPC;
        $table = "jiaju_stores";
        $data = array();
        //$imgarray = array();
        $data['uniacid'] = $_W['uniacid'];
        $data['s_name'] = $_GPC['s_name'];
        $data['s_desc'] = $_GPC['s_desc'];
        $data['s_address'] = $_GPC['s_address'];
        $data['s_headname'] = $_GPC['s_headname'];
        $data['s_headphone'] = $_GPC['s_headphone'];
        $data['s_compername'] = $_GPC['s_compername'];
        $data['allrange'] = $_GPC['allrange'];
        $data['longitude'] = $_GPC['longitude'];
        $data['latitude'] = $_GPC['latitude'];
        $data['starttime'] = $_GPC['starttime'];
        $data['grade'] = $_GPC['grade'];
        $data['c_code'] = $_GPC['c_code'];
        $data['endtime'] = $_GPC['endtime'];

//        //把转义符恢复htmlspecialchars_decode
//        $s_img = htmlspecialchars_decode($_GPC['s_img']);
//        //把引号替换
//        $s_img = str_replace('"', '', $s_img);
//        //剔除[ ]
//        $s_img = ltrim($s_img, '[');
//        $s_img = substr($s_img, 0, -1);
//
//        //$data['img'] =$deal;//这里也有可能是数组
//        $data['s_img'] = $s_img;

        //$data['s_img'] = $_GPC['s_img'];
//        $data['s_img'] = ltrim($_GPC['s_img'], 'https://pj.dede1.com/attachment/');
//        $data['yingyeimg'] = ltrim($_GPC['yingyeimg'], 'https://pj.dede1.com/attachment/');
//        $data['travelallowimg'] = ltrim($_GPC['travelallowimg'], 'https://pj.dede1.com/attachment/');
        $data['s_img']=substr($_GPC['s_img'],32);
        $data['yingyeimg']=substr($_GPC['yingyeimg'],32);
        $data['travelallowimg']=substr($_GPC['travelallowimg'],32);
        $data['mengimg']=substr($_GPC['mengimg'],32);
        $data['workimg']=substr($_GPC['workimg'],32);
        $arr=array();
        if (empty($_GPC['s_name'])){
            $arr['status'] = 0;
            $arr['message'] = "申请失败，门店名称不可为空";
            echo json_encode($arr);
            die();
        }
        //else if(empty($_GPC['c_code'])){
        //    $arr['status'] = 0;
        //  $arr['message'] = "申请失败，请填社会信用代码";
        //echo json_encode($arr);
        //die();
        //}
        else if(empty($_GPC['yingyeimg'])){
            $arr['status'] = 0;
            $arr['message'] = "申请失败，营业执照必须有";
            echo json_encode($arr);
            die();
        }else if(empty($_GPC['mengimg'])){
            $arr['status'] = 0;
            $arr['message'] = "申请失败，门店照片必须有";
            echo json_encode($arr);
            die();
        }else if(empty($_GPC['s_desc'])){
            $arr['status'] = 0;
            $arr['message'] = "申请失败，单位简称";
            echo json_encode($arr);
            die();
        }else if(empty($_GPC['travelallowimg'])){
            $arr['status'] = 0;
            $arr['message'] = "申请失败，旅游许可证必须有";
            echo $arr;
            die();
        }else if(empty($_GPC['s_img'])){
            $arr['status'] = 0;
            $arr['message'] = "申请失败，公司logo不可为空";
            echo json_encode($arr);
            die();
        }else if(empty($_GPC['allrange'])){
            $arr['status'] = 0;
            $arr['message'] = "申请失败，经营范围不可为空";
            echo json_encode($arr);
            die();
        }else if(empty($_GPC['s_compername'])){
            $arr['status'] = 0;
            $arr['message'] = "申请失败，公司名不可为空";

            echo json_encode($arr);
            die();
        }else if(empty($_GPC['s_headname'])){
            $arr['status'] = 0;
            $arr['message'] = "申请失败，必须有负责人";

            echo json_encode($arr);
            die();
        }else if(empty($_GPC['s_headphone'])){
            $arr['status'] = 0;
            $arr['message'] = "申请失败，负责人电话不可为空";

            echo json_encode($arr);
            die();
        }else if(empty($_GPC['s_address'])){
            $arr['status'] = 0;
            $arr['message'] = "申请失败，门店地址为空";

            echo json_encode($arr);
            die();
        }else if (empty($_GPC['longitude'])||empty($_GPC['latitude'])){
            $arr['status'] = 0;
            $arr['message'] = "申请失败，经纬度不可为空";
            echo json_encode($arr);
            die();
        }else if (empty($_GPC['starttime'])||empty($_GPC['endtime'])){
            $arr['status'] = 0;
            $arr['message'] = "申请失败，经营时间不可为空";
            echo json_encode($arr);
            die();
        }else{
            $data['addtime'] = time();
            $res = pdo_insert($table, $data);
            if ($res) {
                $arr['status'] = 1;
                $arr['message'] = "申请成功";

            } else {
                $arr['status'] = -1;
                $arr['message'] = "申请失败";
            }
        }

        echo json_encode($arr);
    }


    //数组转xml
    public function arrayToXml($arr)
    {
        $xml = "<xml>";
        foreach ($arr as $key => $val) {
            if (is_numeric($val)) {
                $xml .= "<" . $key . ">" . $val . "</" . $key . ">";
            } else {
                $xml .= "<" . $key . "><![CDATA[" . $val . "]]></" . $key . ">";
            }
        }
        $xml .= "</xml>";
        return $xml;
    }

    //获取经营范围
    public function doPageGetrange()
    {
        global $_W;
        $table = "jiaju_allowrange";
        $arr = array();
        $sql = "select * from " . tablename($table);
        $result = pdo_fetchall($sql);
        if ($result) {
            $arr['status'] = 1;
            $arr['msg'] = '获取数据成功';
            $arr['data']=$result;
            return json_encode($arr);
            exit();
        } else {
            $arr['status'] = 0;
            $arr['msg'] = '数据为空';
            $arr['data'] = array();
            return json_encode($arr);
            exit();
        }
    }
//获取指定商家
    public function doPageGetoneshangjia()
    {
        global $_W, $_GPC;
        $table = "jiaju_stores";
        $data = array();
        $s_id = $_GPC['s_id'];
        //指定商店模糊查询
        $sql = "select * from " . tablename($table) . " where `uniacid`=:uniacid and `status`=:status  AND `id` =:s_id";
        $params = array(
            ':uniacid' => $_W['uniacid'],
            ':status' => 1,
            ':s_id' => $s_id,
        );

        $result = pdo_fetch($sql, $params);

        if (empty($result)) {
            $data['status'] = 0;
            $data['msg'] = "没有您要找的门店";
        } else {
            $result['s_img'] = tomedia($result['s_img']);
            $result['yingyeimg'] = tomedia($result['yingyeimg']);
            $result['travelallowimg'] = tomedia($result['travelallowimg']);
            $result['mengimg'] = tomedia($result['mengimg']);
            $result['workimg'] = tomedia($result['workimg']);
            $data['result'] = $result;
        }

        echo json_encode($data);
    }
    //获取商家
    public function doPageGetshangjia()
    {
        global $_W, $_GPC;
        //$tablename="zunyang_188che_seller";
        $table = "jiaju_stores";
        $data = array();
        $s_name = $_GPC['s_name'];
        $getnum=$_GPC['num'];
        if (empty($s_name)) {
            //没传参数时，全局搜索
            $sql = "select * from " . tablename($table) . " where `uniacid`=:uniacid and `status`=:status order by addtime";
            ///$sql .= " limit " . 5 * $getnum . ',' . 5;
            $params = array(
                ':uniacid' => $_W['uniacid'],
                ':status' => 1
            );
            $result = pdo_fetchall($sql, $params);

            if (empty($result)) {
                $data["msg"] = "没有数据";
                $data["result"] = $result;
            } else {
                if (is_array($result)) {
                    foreach ($result as $k => $v) {
                        $result[$k]['s_img'] = tomedia($v['s_img']);
                        $result[$k]['yingyeimg'] = tomedia($v['yingyeimg']);
                        $result[$k]['travelallowimg'] = tomedia($v['travelallowimg']);
                        $result[$k]['workimg'] = tomedia($v['workimg']);
                    }
                    $data["result"] = $result;
                } else {
                    $result['s_img'] = tomedia($result['s_img']);
                    $result['yingyeimg'] = tomedia($result['yingyeimg']);
                    $result['travelallowimg'] = tomedia($result['travelallowimg']);
                    $result['workimg'] = tomedia($result['workimg']);
                    $data["result"] = $result;
                }
                //$data["result"]=$result;
            }

        } else {
            //指定商店模糊查询
            $sql = "select * from " . tablename($table) . " where `uniacid`=:uniacid and `status`=:status  AND s_name LIKE '%{$s_name}%'";
            $params = array(
                ':uniacid' => $_W['uniacid'],
                ':status' => 1
            );
            $data['sql'] = $sql;
            $result = pdo_fetchall($sql, $params);

            if (empty($result)) {
                $data['status'] = 0;
                $data['msg'] = "没有您要找的门店";
            } else {
                if (is_array($result)) {
                    foreach ($result as $k => $v) {
                        $result[$k]['s_img'] = tomedia($v['s_img']);
                        $result[$k]['yingyeimg'] = tomedia($v['yingyeimg']);
                        $result[$k]['travelallowimg'] = tomedia($v['travelallowimg']);
                        $result[$k]['workimg'] = tomedia($v['workimg']);
                    }
                    $data['result'] = $result;
                } else {
                    $result['s_img'] = tomedia($result['s_img']);
                    $result['yingyeimg'] = tomedia($result['yingyeimg']);
                    $result['travelallowimg'] = tomedia($result['travelallowimg']);
                    $result['workimg'] = tomedia($result['workimg']);
                }
                $data['result'] = $result;
            }
        }
        echo json_encode($data);
    }

    //地图定位获取城市
    public function doPageMap()
    {
        global $_GPC, $_W;
        $op = $_GPC['op'];
        //$res=pdo_get('zhtc_system',array('uniacid'=>$_W['uniacid']));
        $table = "jiaju_config";

        $data = array();
        $sql = "select * from " . tablename($table) . " where `uniacid`=:uniacid and `code`=:code ";
        $params = array(
            ':uniacid' => $_W['uniacid'],
            ':code' => "site"
        );

        $result = pdo_fetch($sql, $params);
        if (!empty($result)) {

            $data = iunserializer($result['value']);
        }
        $url = "https://apis.map.qq.com/ws/geocoder/v1/?location=" . $op . "&key=" . $data['mapkey'] . "&get_poi=0&coord_type=1";
        $html = file_get_contents($url);
        echo $html;
    }

//计算距离
    function getDistance($lat1, $lng1, $lat2, $lng2)
    {
        $earthRadius = 6367000; //approximate radius of earth in meters
        $lat1 = ($lat1 * pi()) / 180;
        $lng1 = ($lng1 * pi()) / 180;
        $lat2 = ($lat2 * pi()) / 180;
        $lng2 = ($lng2 * pi()) / 180;
        $calcLongitude = $lng2 - $lng1;
        $calcLatitude = $lat2 - $lat1;
        $stepOne = pow(sin($calcLatitude / 2), 2) + cos($lat1) * cos($lat2) * pow(sin($calcLongitude / 2), 2);
        $stepTwo = 2 * asin(min(1, sqrt($stepOne)));
        $calculatedDistance = $earthRadius * $stepTwo;
        return round($calculatedDistance / 1000);
    }

    function cut_str($string, $sublen, $start = 0, $code = 'UTF-8')
    {
        if ($code == 'UTF-8') {
            $pa = "/[\x01-\x7f]|[\xc2-\xdf][\x80-\xbf]|\xe0[\xa0-\xbf][\x80-\xbf]|[\xe1-\xef][\x80-\xbf][\x80-\xbf]|\xf0[\x90-\xbf][\x80-\xbf][\x80-\xbf]|[\xf1-\xf7][\x80-\xbf][\x80-\xbf][\x80-\xbf]/";
            preg_match_all($pa, $string, $t_string);

            if (count($t_string[0]) - $start > $sublen) return join('', array_slice($t_string[0], $start, $sublen)) . "...";
            return join('', array_slice($t_string[0], $start, $sublen));
        } else {
            $start = $start * 2;
            $sublen = $sublen * 2;
            $strlen = strlen($string);
            $tmpstr = '';

            for ($i = 0; $i < $strlen; $i++) {
                if ($i >= $start && $i < ($start + $sublen)) {
                    if (ord(substr($string, $i, 1)) > 129) {
                        $tmpstr .= substr($string, $i, 2);
                    } else {
                        $tmpstr .= substr($string, $i, 1);
                    }
                }
                if (ord(substr($string, $i, 1)) > 129) $i++;
            }
            if (strlen($tmpstr) < $strlen) $tmpstr .= "...";
            return $tmpstr;
        }
    }


    function get_web($url, $data = null, $header = null, $ip = null)
    {
        $https = substr($url, 0, 5);
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        if ('https' == $https) {
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
            curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);
        }
        if (!empty($data)) {
            curl_setopt($curl, CURLOPT_POST, 1);
            curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
        }

        if (!empty($header)) {
            curl_setopt($curl, CURLOPT_HTTPHEADER, $header);
        }

        if (!empty($ip)) {
            $header = array(
                'CLIENT-IP:' . $ip,
                'X-FORWARDED-FOR:' . $ip
            );

            curl_setopt($curl, CURLOPT_HTTPHEADER, $header);
        }

        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        $output = curl_exec($curl);
        $char_a = array('GBK', 'GB2312', 'ASCII', 'UTF-8');
        $encode = mb_detect_encoding($output, $char_a);
        if ('UTF-8' != $encode && in_array($encode, $char_a)) {
            $string = mb_convert_encoding($string, 'UTF-8', $encode);
        }

        curl_close($curl);
        return $output;
    }

    /**
     *
     *  生成app 凭证
     *
     *
     */
    function create_sig($appkey, $mobile, $strRand, $t)
    {
        //return md5($appkey . $mobile);
        $str = "appkey=" . $appkey . "&random=" . $strRand . "&time=" . $t . "&mobile=" . $mobile;
        return bin2hex(hash('sha256', $str, true));
    }

    /**
     *
     *  json to array
     *
     *
     */

    function json2array($json)
    {
        return json_decode($json, true);
    }

    //官方校验微信签名
    public function doPageCheckSignature()
    {
        echo 1;
        die();
        $data = array();
        $table = "zunyang_188che_getinfo";
        $gethtml = file_get_contents('php://input');
        $data['getcon'] = $gethtml;

        pdo_insert($table, $data);

        global $_W, $_GPC;
        $signature = $_GPC["signature"];
        $timestamp = $_GPC["timestamp"];
        $nonce = $_GPC["nonce"];

        $token = 'xiaolong';
        $tmpArr = array($token, $timestamp, $nonce);
        sort($tmpArr, SORT_STRING);
        $tmpStr = implode($tmpArr);
        $tmpStr = sha1($tmpStr);

        if ($tmpStr == $signature) {
            echo $_GPC['echostr'];
        } else {
            echo 'xxx';
        }

    }

    //获取文件
    private function get_php_file($filename)
    {
        return trim(substr(file_get_contents($filename), 15));
    }

    //设置responsemsg到文件
    private function set_php_file($filename, $content)
    {
        $fp = fopen($filename, "w");
        fwrite($fp, "" . $content);
        fclose($fp);
    }


    /**
     * @param $appid 程序appid
     * @param $secret 密钥
     * @param $js_code 获取的code
     * @return mixed openid
     */
    public function getOpendId($appid, $secret, $js_code)
    {

        $urls = "https://api.weixin.qq.com/sns/jscode2session?appid=" . $appid . "&secret=" . $secret . "&js_code=" . $js_code . "&grant_type=authorization_code";

        $html = file_get_contents($urls);

        $getcode = json_decode($html);
        return $getcode->openid;
    }

    //获取accessToken

    /**
     * @return mixed
     */
    public function getAccessToken($appid, $secret)
    {
        $url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=" . $appid . "&secret=" . $secret;
        $con = file_get_contents($url);
        $getcode = json_decode($con);
        return $getcode->access_token;

    }
    //用户获取Session_key，获取手机号的必备属性
    public function getSession_key($appid, $secret,$code)
    {
        $url="https://api.weixin.qq.com/sns/jscode2session?appid=". $appid ."&secret=". $secret ."&js_code=".$code."&grant_type=authorization_code";
        $con = file_get_contents($url);
        $getcode = json_decode($con);
        return $getcode->session_key;

    }
    //解密手机号
    public function  decodephone($appid,$sessionKey,$encryptedData,$iv){
        include_once "getphonelib/wxBizDataCrypt.php";
        $pc = new WXBizDataCrypt($appid, $sessionKey);
        $errCode = $pc->decryptData($encryptedData, $iv, $data );
        if ($errCode == 0) {
            return $data;
        } else {
            return $data;
        }
    }


}