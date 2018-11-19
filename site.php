<?php
defined('IN_IA') or exit('Access Denied');

class jiajuModuleSite extends WeModuleSite
{


    //入口，配置信息
    public function doWebSite()
    {
        global $_W, $_GPC;
        $table = "jiaju_config";
        $code = "site";
        $sql = "SELECT * FROM " . tablename($table) . " WHERE uniacid = :uniacid AND code = :code";
        $params = array(':uniacid' => $_W['uniacid'], ':code' => $code);
        $setting = pdo_fetch($sql, $params);
        $item = iunserializer($setting['value']);
        if ($_W['ispost']) {

            $data = array();
            $data['uniacid'] = $_W['uniacid'];
            $data['code'] = $code;
            $data['value'] = iserializer($_POST);
            if (empty($setting)) {
                pdo_insert($table, $data);
            } else {
                pdo_update($table, $data, array('id' => $setting['id']));
            }

            message('提交成功', referer(), success);
        }

        include $this->template('site');
    }
    //关于
    public function doWebAboutme()
    {
        global $_W, $_GPC;
        $table = "jiaju_about";
        $sql = "SELECT * FROM " . tablename($table) . " WHERE uniacid = :uniacid";
        $params = array(':uniacid' => $_W['uniacid']);
        $setting = pdo_fetch($sql, $params);
        $item = iunserializer($setting['value']);
        if ($_W['ispost']) {
            $data = array();
            $data['uniacid'] = $_W['uniacid'];
            $data['value'] = iserializer($_POST);
            if (empty($setting)) {
                pdo_insert($table, $data);
            } else {
                pdo_update($table, $data, array('id' => $setting['id']));
            }
            message('提交成功', referer(), success);
        }

        include $this->template('about');
    }
    //协议
    public function doWebXieyi()
    {
        global $_W, $_GPC;
        $table = "jiaju_xieyi";
        $sql = "SELECT * FROM " . tablename($table) . " WHERE uniacid = :uniacid";
        $params = array(':uniacid' => $_W['uniacid']);
        $setting = pdo_fetch($sql, $params);
        $item = iunserializer($setting['value']);
        if ($_W['ispost']) {
            $data = array();
            $data['uniacid'] = $_W['uniacid'];
            $data['value'] = iserializer($_POST);
            if (empty($setting)) {
                pdo_insert($table, $data);
            } else {
                pdo_update($table, $data, array('id' => $setting['id']));
            }
            message('提交成功', referer(), success);
        }

        include $this->template('xieyi');
    }
    //轮播图图片列表
    public function doWebAdimglist()
    {
        global $_W, $_GPC;
        $table = "jiaju_banner";
        $pindex = max(1, intval($_GPC['page']));
        $psize = 10;
        $sql = "select * from " . tablename($table) . " where `uniacid`=:uniacid order by addtime desc limit " . ($pindex - 1) * $psize . ',' . $psize;
        $params = array(
            ':uniacid' => $_W['uniacid'],
        );
        $result_list = pdo_fetchall($sql, $params);
        $sql2 = "select count(*) from " . tablename($table) . " where `uniacid`=:uniacid";
        $total = pdo_fetchcolumn($sql2, $params);
        $pager = pagination($total, $pindex, $psize);
        include $this->template("adimglist");

    }
    //添加轮播图图片
    public function doWebAddadimg()
    {
        global $_W, $_GPC;
        $tablename = "jiaju_banner";
        if (!$_W['ispost']) {
            include $this->template('addadimg');
        } else {
            $arr = array();
            $arr['uniacid'] = $_W['uniacid'];
            if (empty($_GPC['adimg'])) {
                message('图片不能为空', referer(), 'error');
            } else {
                $arr['adimg'] = $_GPC['adimg'];
            }
            $arr['addtime'] = time();
            $arr['adurl'] = $_GPC['adurl'];
            $add_result = pdo_insert($tablename, $arr);
            if (!empty($add_result)) {
                message('添加成功', $this->createWebUrl('Adimglist', array('type' => $arr['type'])), 'success');
            } else {
                message('添加失败', referer(), 'error');
            }
        }
    }
    //编辑轮播图片
    public function doWebEditad()
    {
        global $_W, $_GPC;
        $table = "jiaju_banner";
        $id = intval($_GPC['id']);
        if ($id == "") {
            message("参数错误", referer(), 'error');
        } else {
            $sql = "select * from " . tablename($table) . " where `uniacid`=:uniacid and `aid`=:id";
            $params = array(
                ':uniacid' => $_W['uniacid'],
                ':id' => $id
            );
            $result = pdo_fetch($sql, $params);
            if (empty($result)) {
                message("信息不存在", referer(), 'error');
            } else {
                if ($_W['ispost']) {
                    $arr = array();
                    $arr['uniacid'] = $_W['uniacid'];
                    if (empty($_GPC['adimg'])) {
                        message('请选择图片', referer(), 'error');
                    } else {
                        $arr['adimg'] = $_GPC['adimg'];
                    }
                    $arr['addtime']=time();
                    $arr['adurl'] = $_GPC['adurl'];
                    $edit_result = pdo_update($table, $arr, array('uniacid' => $_W['uniacid'], 'aid' => $id));
                    if (!empty($edit_result)) {
                        message('编辑成功', $this->createWebUrl('Adimglist'), 'success');
                    } else {
                        message('编辑失败', referer(), 'error');
                    }
                } else {
                    include $this->template('editad');
                }

            }

        }
    }
    //删除轮播图片
    public function doWebDelad()
    {
        global $_W, $_GPC;
        $table = "jiaju_banner";
        $id = intval($_GPC['id']);
        if ($id < 0) {
            message('参数错误', referer(), 'error');
        } else {
            $sql = "select * from " . tablename($table) . " where `uniacid`=:uniacid and `aid`=:id";
            $params = array(
                ':uniacid' => $_W['uniacid'],
                ':id' => $id
            );
            $result = pdo_fetch($sql, $params);
            if (empty($result)) {
                message("数据不存在", referer(), 'error');
            } else {
                $del_result = pdo_delete($table, array('uniacid' => $_W['uniacid'], 'aid' => $id));
                if ($del_result) {
                    message('删除成功', $this->createWebUrl('Adimglist', array('type' => $result['type'])), 'success');
                } else {
                    message("删除失败", referer(), 'error');
                }
            }

        }
    }
    //服务列表
    public function doWebSerlist()
    {
        global $_W, $_GPC;
        $table = "jiaju_sertype";
        $table1 = "jiaju_type";
        $pindex = max(1, intval($_GPC['page']));
        $psize = 10;
        $sql = "select a.*,b.name as tname from " . tablename($table).' a,'. tablename($table1).' b' . " where a.uniacid=:uniacid and a.pid=b.tid and b.tid>0 order by a.sid asc limit " . ($pindex - 1) * $psize . ',' . $psize;
        $params = array(
            ':uniacid' => $_W['uniacid'],
        );
        $result_list = pdo_fetchall($sql, $params);
        $sql2 = "select count(*) from " . tablename($table) . " where `uniacid`=:uniacid";
        $total = pdo_fetchcolumn($sql2, $params);
        $pager = pagination($total, $pindex, $psize);
        if($_GPC['op']=='delete'){
            $res=pdo_delete('jiaju_sertype',array('sid'=>$_GPC['id']));
            if($res){
                message('删除成功！', $this->createWebUrl('serlist'), 'success');
            }else{
                message('删除失败！','','error');
            }
        }
        include $this->template("serlist");

    }
    //添加/编辑服务
    public function doWebAddser()
    {
        global $_W, $_GPC;
        $tablename = "jiaju_type";
        $sertable = "jiaju_sertype";
        $result=pdo_get($sertable,array('sid'=>$_GPC['id']));
        $typelist=pdo_getall($tablename,array('uniacid'=>$_W['uniacid'],'tid >'=>0));
        if (!$_W['ispost']) {
            include $this->template('addser');
        } else {
            $arr = array();
            $arr['uniacid'] = $_W['uniacid'];
            if (empty($_GPC['img'])) {
                message('图片不能为空', referer(), 'error');
            } else {
                $arr['img'] = $_GPC['img'];
            }
            if (empty($_GPC['name'])) {
                message('服务名称不能为空', referer(), 'error');
            } else {
                $arr['name'] = $_GPC['name'];
            }
            if (empty($_GPC['onegrage'])||empty($_GPC['oneprice'])) {
                message('等级类型不能为空，请填：默认', referer(), 'error');
            } else {
                $arr['onegrage'] = $_GPC['onegrage'];
            }
            $arr['addtime'] = time();
            $arr['desc'] = $_GPC['desc'];
            $arr['pid'] = $_GPC['pid'];


            $arr['twograge'] = $_GPC['twograge'];
            $arr['threegrage'] = $_GPC['threegrage'];

            $arr['oneprice'] = $_GPC['oneprice'];
            $arr['twoprice'] = $_GPC['twoprice'];
            $arr['threeprice'] = $_GPC['threeprice'];

            $arr['onescale'] = $_GPC['onescale'];
            $arr['twoscale'] = $_GPC['twoscale'];
            $arr['threescale'] = $_GPC['threescale'];
            if($_GPC['id']==''){
                $res=pdo_insert($sertable,$arr);
                if($res){
                    message('添加成功！', $this->createWebUrl('serlist'), 'success');
                }else{
                    message('添加失败！','','error');
                }
            }else{
                $res=pdo_update($sertable,$arr,array('sid'=>$_GPC['id']));
                if($res){
                    message('编辑成功！', $this->createWebUrl('serlist'), 'success');
                }else{
                    message('编辑失败！','','error');
                }
            }
        }
    }
    //分类列表
    public function doWebTypelist()
    {
        global $_W, $_GPC;
        $table = "jiaju_type";
        $pindex = max(1, intval($_GPC['page']));
        $psize = 10;
        $sql = "select * from " . tablename($table) . " where `uniacid`=:uniacid order by orders asc limit " . ($pindex - 1) * $psize . ',' . $psize;
        $params = array(
            ':uniacid' => $_W['uniacid'],
        );
        $result_list = pdo_fetchall($sql, $params);
        $sql2 = "select count(*) from " . tablename($table) . " where `uniacid`=:uniacid";
        $total = pdo_fetchcolumn($sql2, $params);
        $pager = pagination($total, $pindex, $psize);
        if($_GPC['op']=='delete'){
            $res=pdo_delete('jiaju_type',array('tid'=>$_GPC['id']));
            if($res){
                message('删除成功！', $this->createWebUrl('typelist'), 'success');
            }else{
                message('删除失败！','','error');
            }
        }
        include $this->template("typelist");

    }
    //添加/编辑分类
    public function doWebAddtype()
    {
        global $_W, $_GPC;
        $tablename = "jiaju_type";
        $result=pdo_get('jiaju_type',array('tid'=>$_GPC['id']));
        if (!$_W['ispost']) {
            include $this->template('addtype');
        } else {
            $arr = array();
            $arr['uniacid'] = $_W['uniacid'];
            if (empty($_GPC['img'])) {
                message('图片不能为空', referer(), 'error');
            } else {
                $arr['img'] = $_GPC['img'];
            }
            if (empty($_GPC['name'])) {
                message('分类名称不能为空', referer(), 'error');
            } else {
                $arr['name'] = $_GPC['name'];
            }
            $arr['addtime'] = time();
            $arr['orders'] = $_GPC['orders'];
            if($_GPC['id']==''){
                $res=pdo_insert($tablename,$arr);
                if($res){
                    message('添加成功！', $this->createWebUrl('typelist'), 'success');
                }else{
                    message('添加失败！','','error');
                }
            }else{
                $res=pdo_update($tablename,$arr,array('tid'=>$_GPC['id']));
                if($res){
                    message('编辑成功！', $this->createWebUrl('typelist'), 'success');
                }else{
                    message('编辑失败！','','error');
                }
            }
        }
    }
    //热点文章列表
    public function doWebHotlist()
    {
        global $_W, $_GPC;
        $table = "jiaju_hot";
        $pindex = max(1, intval($_GPC['page']));
        $psize = 10;
        $sql = "select * from " . tablename($table) . " where `uniacid`=:uniacid order by addtime desc limit " . ($pindex - 1) * $psize . ',' . $psize;
        $params = array(
            ':uniacid' => $_W['uniacid'],
        );
        $result_list = pdo_fetchall($sql, $params);
        $sql2 = "select count(*) from " . tablename($table) . " where `uniacid`=:uniacid";
        $total = pdo_fetchcolumn($sql2, $params);
        $pager = pagination($total, $pindex, $psize);
        if($_GPC['op']=='delete'){
            $res=pdo_delete('jiaju_hot',array('id'=>$_GPC['id']));
            if($res){
                message('删除成功！', $this->createWebUrl('hotlist'), 'success');
            }else{
                message('删除失败！','','error');
            }
        }
        include $this->template("hotlist");

    }
    //添加/编辑热点趣文
    public function doWebAddhot()
    {
        global $_W, $_GPC;
        $tablename = "jiaju_hot";
        $result=pdo_get('jiaju_hot',array('id'=>$_GPC['id']));
        if (!$_W['ispost']) {
            include $this->template('addhot');
        } else {
            $arr = array();
            $arr['uniacid'] = $_W['uniacid'];
            if (empty($_GPC['onetitle'])) {
                message('标题不能为空', referer(), 'error');
            } else {
                $arr['onetitle'] = $_GPC['onetitle'];
            }
            if (empty($_GPC['contents'])) {
                message('内容不能为空', referer(), 'error');
            } else {
                $arr['contents'] = $_GPC['contents'];
            }
            $arr['addtime'] = time();
            $arr['twotitle'] = $_GPC['twotitle'];

            if($_GPC['id']==''){
                $res=pdo_insert($tablename,$arr);
                if($res){
                    message('添加成功！', $this->createWebUrl('hotlist'), 'success');
                }else{
                    message('添加失败！','','error');
                }
            }else{
                $res=pdo_update($tablename,$arr,array('id'=>$_GPC['id']));
                if($res){
                    message('编辑成功！', $this->createWebUrl('hotlist'), 'success');
                }else{
                    message('编辑失败！','','error');
                }
            }
        }
    }
    //问题列表
    public function doWebProblist()
    {
        global $_W, $_GPC;
        $table = "jiaju_problem";
        $pindex = max(1, intval($_GPC['page']));
        $psize = 10;
        $sql = "select * from " . tablename($table) . " where `uniacid`=:uniacid order by addtime desc limit " . ($pindex - 1) * $psize . ',' . $psize;
        $params = array(
            ':uniacid' => $_W['uniacid'],
        );
        $result_list = pdo_fetchall($sql, $params);
        $sql2 = "select count(*) from " . tablename($table) . " where `uniacid`=:uniacid";
        $total = pdo_fetchcolumn($sql2, $params);
        $pager = pagination($total, $pindex, $psize);
        include $this->template("problist");
    }
    //添加问题
    public function doWebAddprob()
    {
        global $_W, $_GPC;
        $tablename = "jiaju_problem";
        if (!$_W['ispost']) {
            include $this->template('addprob');
        } else {
            $arr = array();
            $arr['uniacid'] = $_W['uniacid'];
            if (empty($_GPC['title'])) {
                message('问题标题不可为空', referer(), 'error');
            } else {
                $arr['title'] = $_GPC['title'];
            }
            $arr['addtime'] = time();
            if (empty($_GPC['contents'])) {
                message('问题内容不可为空', referer(), 'error');
            } else {
                $arr['contents'] = $_GPC['contents'];
            }
            $add_result = pdo_insert($tablename, $arr);
            if (!empty($add_result)) {
                message('添加成功', $this->createWebUrl('Problist', array('type' => $arr['type'])), 'success');
            } else {
                message('添加失败', referer(), 'error');
            }
        }
    }
    //编辑问题
    public function doWebEditprob()
    {
        global $_W, $_GPC;
        $table = "jiaju_problem";
        $id = intval($_GPC['id']);
        if ($id == "") {
            message("参数错误", referer(), 'error');
        } else {
            $sql = "select * from " . tablename($table) . " where `uniacid`=:uniacid and `id`=:id";
            $params = array(
                ':uniacid' => $_W['uniacid'],
                ':id' => $id
            );
            $result = pdo_fetch($sql, $params);
            if (empty($result)) {
                message("信息不存在", referer(), 'error');
            } else {
                if ($_W['ispost']) {
                    $arr = array();
                    $arr['uniacid'] = $_W['uniacid'];
                    if (empty($_GPC['title'])) {
                        message('标题不可为空', referer(), 'error');
                    } else {
                        $arr['title'] = trim($_GPC['title']);
                    }
                    if (empty($_GPC['contents'])) {
                        message('内容不能为空', referer(), 'error');
                    } else {
                        $arr['contents'] = trim($_GPC['contents']);
                    }
                    $arr['addtime']=time();
                    $edit_result = pdo_update($table, $arr, array('uniacid' => $_W['uniacid'], 'id' => $id));
                    if (!empty($edit_result)) {
                        message('编辑成功', $this->createWebUrl('Problist'), 'success');
                    } else {
                        message('编辑失败,数据没变动', referer(), 'error');
                    }
                } else {
                    include $this->template('editprob');
                }

            }

        }
    }
    //删除问题
    public function doWebDelprob()
    {
        global $_W, $_GPC;
        $table = "jiaju_problem";
        $id = intval($_GPC['id']);
        if ($id < 0) {
            message('参数错误', referer(), 'error');
        } else {
            $sql = "select * from " . tablename($table) . " where `uniacid`=:uniacid and `id`=:id";
            $params = array(
                ':uniacid' => $_W['uniacid'],
                ':id' => $id
            );
            $result = pdo_fetch($sql, $params);
            if (empty($result)) {
                message("数据不存在", referer(), 'error');
            } else {
                $del_result = pdo_delete($table, array('uniacid' => $_W['uniacid'], 'id' => $id));
                if ($del_result) {
                    message('删除成功', $this->createWebUrl('Problist', array('type' => $result['type'])), 'success');
                } else {
                    message("删除失败", referer(), 'error');
                }
            }

        }
    }
    //商家列表
    public function doWebList()
    {
        global $_W, $_GPC;
        $table = "jiaju_stores";
        $pindex = max(1, intval($_GPC['page']));
        $psize = 10;
        $sql = "select * from " . tablename($table) . " where `uniacid`=:uniacid order by addtime desc limit " . ($pindex - 1) * $psize . ',' . $psize;
        $params = array(
            ':uniacid' => $_W['uniacid'],
        );
        $result_list = pdo_fetchall($sql, $params);
        $sql2 = "select count(*) from " . tablename($table) . " where `uniacid`=:uniacid";
        $total = pdo_fetchcolumn($sql2, $params);
        $pager = pagination($total, $pindex, $psize);
        include $this->template("stores");

    }
    public function doWebAddseller()
    {
        global $_W, $_GPC;
        $tablename = "jiaju_stores";
        $params = array(
            ':uniacid' => $_W['uniacid']
        );
        if (!$_W['ispost']) {
            include $this->template('addstores');
        } else {
            $arr = array();
            $arr['uniacid'] = $_W['uniacid'];
            if (empty($_GPC['s_name'])) {
                message('请填商家名称', referer(), 'error');
            } else {
                $arr['s_name'] = $_GPC['s_name'];
            }
            if (empty($_GPC['s_desc'])) {
                message('请填商家简称', referer(), 'error');
            } else {
                $arr['s_desc'] = $_GPC['s_desc'];
            }
            if (empty($_GPC['s_address'])) {
                message('请填写地址', referer(), 'error');
            } else {
                $arr['s_address'] = $_GPC['s_address'];
            }
            if (empty($_GPC['s_headphone'])) {
                message('负责人电话不能为空', referer(), 'error');
            } else {
                $arr['s_headphone'] = $_GPC['s_headphone'];
            }
            if (empty($_GPC['s_img'])) {
                message('请添加上logo', referer(), 'error');
            } else {
                $arr['s_img'] = $_GPC['s_img'];
            }

            if (empty($_GPC['mengimg'])) {
                message('请添加商家封面', referer(), 'error');
            } else {
                $arr['mengimg'] = $_GPC['mengimg'];
            }
            $arr['otherimg'] =implode(',',$_GPC['otherimg']);
            $arr['addtime'] = time();
            $arr['detaildesc'] = $_GPC['detaildesc'];
            $add_result = pdo_insert($tablename, $arr);
            if (!empty($add_result)) {
                message('添加成功', $this->createWebUrl('List', array('type' => $arr['type'])), 'success');
            } else {
                message('添加失败', referer(), 'error');
            }
        }
    }
    public function doWebEditseller()
    {
        global $_W, $_GPC;
        $table = "jiaju_stores";
        $id = intval($_GPC['id']);
        if ($id == "") {
            message("参数错误", referer(), 'error');
        } else {
            $sql = "select * from " . tablename($table) . " where `uniacid`=:uniacid and `id`=:id";
            $params = array(
                ':uniacid' => $_W['uniacid'],
                ':id' => $id
            );
            $result = pdo_fetch($sql, $params);
            $result['otherimg']=explode(',',$result['otherimg']);
            if (empty($result)) {
                message("信息不存在", referer(), 'error');
            } else {
                if ($_W['ispost']) {
                    $arr = array();
                    $arr['uniacid'] = $_W['uniacid'];
                    $arr['detaildesc'] = $_GPC['detaildesc'];
                    if (empty($_GPC['s_name'])) {
                        message('请填商家名称', referer(), 'error');
                    } else {
                        $arr['s_name'] = $_GPC['s_name'];
                    }
                    if (empty($_GPC['s_desc'])) {
                        message('请填商家简称', referer(), 'error');
                    } else {
                        $arr['s_desc'] = $_GPC['s_desc'];
                    }
                    if (empty($_GPC['s_address'])) {
                        message('请填写地址', referer(), 'error');
                    } else {
                        $arr['s_address'] = $_GPC['s_address'];
                    }
                    if (empty($_GPC['s_headphone'])) {
                        message('负责人电话不能为空', referer(), 'error');
                    } else {
                        $arr['s_headphone'] = $_GPC['s_headphone'];
                    }
                    if (empty($_GPC['s_img'])) {
                        message('请添加上logo', referer(), 'error');
                    } else {
                        $arr['s_img'] = $_GPC['s_img'];
                    }

                    if (empty($_GPC['mengimg'])) {
                        message('请添加商家封面', referer(), 'error');
                    } else {
                        $arr['mengimg'] = $_GPC['mengimg'];
                    }
                    //$arr['addtime']=time();
                    $arr['otherimg'] =implode(',',$_GPC['otherimg']);
                    $edit_result = pdo_update('jiaju_stores', $arr, array('uniacid' => $_W['uniacid'], 'id' => $id));
                    if (!empty($edit_result)) {
                        message('编辑成功', $this->createWebUrl('List'), 'success');
                    } else {
                        message('编辑失败', referer(), 'error');
                    }
                } else {
                    include $this->template('editstores');
                }

            }

        }
    }
    public function doWebDelseller()
    {
        global $_W, $_GPC;
        $table = "jiaju_stores";
        $id = intval($_GPC['id']);
        if ($id < 0) {
            message('参数错误', referer(), 'error');
        } else {
            $sql = "select * from " . tablename($table) . " where `uniacid`=:uniacid and `id`=:id";
            $params = array(
                ':uniacid' => $_W['uniacid'],
                ':id' => $id
            );
            $result = pdo_fetch($sql, $params);
            if (empty($result)) {
                message("数据不存在", referer(), 'error');
            } else {
                $del_result = pdo_delete($table, array('uniacid' => $_W['uniacid'], 'id' => $id));
                if ($del_result) {
                    message('删除成功', $this->createWebUrl('List', array('type' => $result['type'])), 'success');
                } else {
                    message("删除失败", referer(), 'error');
                }
            }

        }
    }
    //用户列表
    public function doWebUserlist()
    {
        global $_W, $_GPC;
        $table = "jiaju_user";
        $pindex = max(1, intval($_GPC['page']));
        $psize = 10;
        $getkeyword=$_GPC['keywords'];
        $where=" nickname LIKE '%".$getkeyword."%'";
        $sql = "select * from " . tablename($table) ." where uniacid=:uniacid and ".$where." order by addtime desc limit " . ($pindex - 1) * $psize . ',' . $psize;
        $params = array(
            ':uniacid' => $_W['uniacid'],
        );
        $result_list = pdo_fetchall($sql, $params);
        $sql2 = "select count(*) from " . tablename($table) . " where `uniacid`=:uniacid";
        $total = pdo_fetchcolumn($sql2, $params);
        $pager = pagination($total, $pindex, $psize);
        include $this->template("userlist");

    }
    //全部订单
    public function doWebAllorder(){
        global $_W, $_GPC;
        $table = "jiaju_order";
        $pindex = max(1, intval($_GPC['page']));
        $psize = 10;
        $getkeyword=$_GPC['keywords'];
        $type=isset($_GPC['type'])?$_GPC['type']:'all';
        $where='';
        if(isset($getkeyword)){
            $where=" and  ordernum LIKE '%".$getkeyword."%'";
            $type='all';
        }
        if($type=='wait'){//待派单
            $where.=" and state=1";
        }else if($type=='fins'){//已派单
            $where.=" and state=2";
        }else if($type=='complete'){//已完成
            $where.=" and state=3";
        }else if($type=='cancel'){//取消
            $where.=" and state=4";
        }
        $sql = "select * from " . tablename($table) ." where uniacid=:uniacid ".$where." order by addtime desc limit " . ($pindex - 1) * $psize . ',' . $psize;
        $params = array(
            ':uniacid' => $_W['uniacid'],
        );
        $result_list = pdo_fetchall($sql, $params);
        $sql2 = "select count(*) from " . tablename($table) . " where `uniacid`=:uniacid";
        $total = pdo_fetchcolumn($sql2, $params);
        $pager = pagination($total, $pindex, $psize);
        $type=$_GPC['type'];
        include $this->template("allorderlist");
    }
    //订单详细
    public function doWebOrderdetail()
    {
        global $_W, $_GPC;
        $table = "jiaju_order";
        $id = intval($_GPC['id']);
        if ($id == "") {
            message("参数错误", referer(), 'error');
        } else {
            $sql = "select * from " . tablename($table) . " where `uniacid`=:uniacid and `id`=:id";
            $params = array(
                ':uniacid' => $_W['uniacid'],
                ':id' => $id
            );
            $result = pdo_fetch($sql, $params);
            $result['imgs']=explode(',',$result['imgs']);
            $result['pprice']=$result['price']*($result['bili']/100);
            if (empty($result)) {
                message("信息不存在", referer(), 'error');
            } else {
                if ($_W['ispost']) {
                    //接单
                    $arr = array();
                    $arr['uniacid'] = $_W['uniacid'];
                    $arr['detaildesc'] = $_GPC['detaildesc'];
                    if (empty($_GPC['sfid'])&&empty($_GPC['did'])) {
                        message('请选择服务商家或者师傅', referer(), 'error');
                    } else {
                        $arr['sfid'] = $_GPC['sfid'];
                        $arr['did'] = $_GPC['did'];
                    }
                    $arr['jdtime'] = date('Y-m-d H:i:s',time());
                    $arr['state'] = 2;
                    $edit_result = pdo_update('jiaju_order', $arr, array('uniacid' => $_W['uniacid'], 'id' => $id));
                    if (!empty($edit_result)) {
                        //跳转到已派单
                        message('派单成功', $this->createWebUrl('Sering'), 'success');
                    } else {
                        message('派单失败', referer(), 'error');
                    }
                } else {
                    include $this->template('orderdetail');
                }

            }

        }
    }
    //师傅列表
    public function doWebShifulist(){
        global $_W, $_GPC;
        $table = "jiaju_shifu";
        $pindex = max(1, intval($_GPC['page']));
        $psize = 10;
        $getkeyword=$_GPC['keywords'];
        $where=" a.name LIKE '%".$getkeyword."%'";
        $sql = "select a.*,b.name as sname from " . tablename($table)." a"." left join ". tablename("jiaju_sertype")." b" ." on a.type=b.sid  where a.uniacid=:uniacid and ".$where." order by a.addtime desc limit " . ($pindex - 1) * $psize . ',' . $psize;
        $params = array(
            ':uniacid' => $_W['uniacid'],
        );

        $result_list = pdo_fetchall($sql, $params);
        $sql2 = "select count(*) from " . tablename($table) . " where `uniacid`=:uniacid";
        $total = pdo_fetchcolumn($sql2, $params);
        $pager = pagination($total, $pindex, $psize);
        include $this->template("shifulist");
    }
    //师傅信息详细
    public function doWebShifudetail()
    {
        global $_W, $_GPC;
        $table = "jiaju_shifu";
        $id = intval($_GPC['id']);
        if ($id == "") {
            message("参数错误", referer(), 'error');
        } else {
            $sql = "select * from " . tablename($table) . " where `uniacid`=:uniacid and `sfid`=:id";
            $params = array(
                ':uniacid' => $_W['uniacid'],
                ':id' => $id
            );
            $result = pdo_fetch($sql, $params);
            $typelist=pdo_getall('jiaju_sertype',array('uniacid'=>$_W['uniacid']));
            if (empty($result)) {
                message("信息不存在", referer(), 'error');
            } else {
                if ($_W['ispost']) {
                    //通过
                    $arr = array();
                    $arr['uniacid'] = $_W['uniacid'];
                    $arr['state'] = $_GPC['state'];
                    $arr['type'] = $_GPC['pid'];
                    $arr['grade'] = $_GPC['grade'];
                    if ($_GPC['state']==3){
                        if (empty($_GPC['resfu'])) {
                            message('请填写拒绝理由', referer(), 'error');
                        } else {
                            $arr['resfu'] = $_GPC['resfu'];
                            $arr['state'] = $_GPC['state'];
                        }
                    }
                    pdo_update('jiaju_user',array('state'=>$arr['state']),array('openId'=>$_GPC['openid']));
                    $edit_result = pdo_update('jiaju_shifu', $arr, array('uniacid' => $_W['uniacid'], 'sfid' => $id));
                    if (!empty($edit_result)) {
                        message('操作成功', $this->createWebUrl('Shifulist'), 'success');
                    } else {
                        message('操作失败', referer(), 'error');
                    }
                } else {
                    include $this->template('shifudetail');
                }

            }

        }
    }
    //禁用师傅
    public function doWebStop(){
        global $_W,$_GPC;
        $table="jiaju_shifu";
        $s=intval($_GPC['s']);
        $id=intval($_GPC['id']);
        $openid=$_GPC['openid'];
        pdo_update('jiaju_user',array('state'=>$s),array('openId'=>$openid));
        $result=pdo_update($table,array('state'=>$s),array('uniacid'=>$_W['uniacid'],'sfid'=>$id));
        if($result){
            message('操作成功',referer(),'success');
        }else{
            message('操作失败',referer(),'error');
        }

    }
    /*经营范围列表*/
    public function doWebRange()
    {
        global $_W, $_GPC;
        $table = "jiaju_allowrange";
        $pindex = max(1, intval($_GPC['page']));
        $psize = 10;
        $sql = "select * from " . tablename($table) . " where `uniacid`=:uniacid order by fid desc limit " . ($pindex - 1) * $psize . ',' . $psize;
        $params = array(
            ':uniacid' => $_W['uniacid']
        );

        $fenlei_list = pdo_fetchall($sql, $params);
        //var_dump($fenlei_list);exit;
        $sql2 = "select count(*) from " . tablename($table) . " where `uniacid`=:uniacid";
        $total = pdo_fetchcolumn($sql2, $params);
        $pager = pagination($total, $pindex, $psize);
        include $this->template("range");
    }
    /*添加经营范围*/
    public function doWebAddrange()
    {
        global $_W, $_GPC;
        $table = "jiaju_allowrange";
        if (!$_W['ispost']) {
            include $this->template("addrange");
        } else {
            $arr = array();
            if (empty($_GPC['fname'])) {
                message("范围名称不能为空", referer(), 'error');
            } else {
                $arr['fname'] = $_GPC['fname'];
            }

            $arr['uniacid'] = $_W['uniacid'];
            $arr['addtime'] = time();
            $add_result = pdo_insert($table, $arr);
            if (!empty($add_result)) {
                message("添加成功", $this->createWebUrl('range'), 'success');
            } else {
                message("添加失败", referer(), 'error');
            }
        }
    }

    /*删除经营范围*/
    public function doWebDelrange()
    {
        global $_W, $_GPC;
        $table = "jiaju_allowrange";
        $id = intval($_GPC['id']);
        if ($id < 0) {
            message('参数错误', referer(), 'error');
        } else {
            $sql = "select * from " . tablename($table) . " where `uniacid`=:uniacid and `fid`=:c_id";
            $params = array(
                ':uniacid' => $_W['uniacid'],
                ':c_id' => $id
            );
            $result = pdo_fetch($sql, $params);
            if (empty($result)) {
                message("数据不存在", referer(), 'error');
            } else {
                $del_result = pdo_delete($table, array('uniacid' => $_W['uniacid'], 'fid' => $id));
                if ($del_result) {
                    message("删除成功", $this->createWebUrl('range'), 'success');
                } else {
                    message("删除失败", referer(), 'error');
                }
            }
        }
    }
    /*编辑经营范围*/
    public function doWebEditrange()
    {
        global $_W, $_GPC;
        $table = "jiaju_allowrange";
        $id = intval($_GPC['id']);
        if ($id < 0) {
            message("参数错误", referer(), 'error');
        } else {
            $sql = "select * from " . tablename($table) . " where `uniacid`=:uniacid and `fid`=:c_id";
            $params = array(
                ':uniacid' => $_W['uniacid'],
                ':c_id' => $id
            );
            $result = pdo_fetch($sql, $params);
            if (empty($result)) {
                message('数据不存在', referer(), 'error');
            } else {
                if ($_W['ispost']) {
                    $arr = array();
                    if (empty($_GPC['fname'])) {
                        message("经营范围名称不能为空", referer(), 'error');
                    } else {
                        $arr['fname'] = $_GPC['fname'];
                    }

                    $edit_result = pdo_update($table, $arr, array('uniacid' => $_W['uniacid'], 'fid' => $id));
                    if (!empty($edit_result)) {
                        message("编辑成功", $this->createWebUrl('range'), 'success');
                    } else {
                        message("编辑失败", referer(), 'error');
                    }
                }
                include $this->template('editrange');
            }
        }

    }
    //审核管理
    public function doWebSh(){
        global $_W,$_GPC;
        $table="jiaju_stores";
        $s=intval($_GPC['s']);
        $id=intval($_GPC['id']);
        $result=pdo_update($table,array('status'=>$s),array('uniacid'=>$_W['uniacid'],'id'=>$id));
        if($result){
            message('操作成功',referer(),'success');
        }else{
            message('操作失败',referer(),'error');
        }

    }
    public function __construct()
    {
        global $_W, $_GPC;
        if ($_W['os'] == 'mobile') {

        } else {
            $do = $_GPC['do'];
            $doo = $_GPC['doo'];
            $act = $_GPC['act'];
            global $frames;
            if ($_W['user']['type'] < 3) {
                $frames = $this->getModuleFrames();
                $this->_calc_current_frames2($frames);
            } else {
                $frames = $this->getModuleFrames2();
                $this->_calc_current_frames2($frames);
            }

        }
    }

    function getModuleFrames()
    {

        $frames = array();
        $name = "jiaju";
        $frames['set']['title'] = '管理中心';
        $frames['set']['active'] = '';
        $frames['set']['items'] = array();

        $frames['set']['items']['site']['url'] = url('site/entry/site', array('m' => $name));
        $frames['set']['items']['site']['title'] = '站点设置';
        $frames['set']['items']['site']['actions'] = array();
        $frames['set']['items']['site']['active'] = '';

        // $frames['set']['items']['aboutme']['url'] = url('site/entry/aboutme', array('m' => $name));
        //$frames['set']['items']['aboutme']['title'] = '关于设置';
        // $frames['set']['items']['aboutme']['actions'] = array();
        //  $frames['set']['items']['aboutme']['active'] = '';

        $frames['set']['items']['xieyi']['url'] = url('site/entry/xieyi', array('m' => $name));
        $frames['set']['items']['xieyi']['title'] = '协议设置';
        $frames['set']['items']['xieyi']['actions'] = array();
        $frames['set']['items']['xieyi']['active'] = '';

        ////////////////////////////////////////////
        $frames['jz']['title'] = '家政管理';
        $frames['jz']['active'] = '';
        $frames['jz']['items'] = array();

        $frames['jz']['items']['servicelist']['url'] = url('site/entry/serlist', array('m' => $name));
        $frames['jz']['items']['servicelist']['title'] = '服务列表';
        $frames['jz']['items']['servicelist']['actions'] = array();
        $frames['jz']['items']['servicelist']['active'] = '';

        $frames['jz']['items']['service']['url'] = url('site/entry/addser', array('m' => $name));
        $frames['jz']['items']['service']['title'] = '添加服务';
        $frames['jz']['items']['service']['actions'] = array();
        $frames['jz']['items']['service']['active'] = '';

        $frames['jz']['items']['shifu']['url'] = url('site/entry/shifulist', array('m' => $name));
        $frames['jz']['items']['shifu']['title'] = '师傅列表';
        $frames['jz']['items']['shifu']['actions'] = array();
        $frames['jz']['items']['shifu']['active'] = '';
        ////////////////////////////////////////////////////////////
        $frames['seller']['title'] = '商家管理';
        $frames['seller']['active'] = '';
        $frames['seller']['items'] = array();

        $frames['seller']['items']['list1']['url'] = url('site/entry/list', array('m' => $name));
        $frames['seller']['items']['list1']['title'] = '商家列表';
        $frames['seller']['items']['list1']['actions'] = array();
        $frames['seller']['items']['list1']['active'] = '';

        $frames['seller']['items']['addseller']['url'] = url('site/entry/addseller', array('m' => $name));
        $frames['seller']['items']['addseller']['title'] = '添加商家';
        $frames['seller']['items']['addseller']['actions'] = array();
        $frames['seller']['items']['addseller']['active'] = '';


        ////////////////////////
        $frames['order']['title'] = '订单管理';
        $frames['order']['active'] = '';
        $frames['order']['items'] = array();

        $frames['order']['items']['allorder']['url'] = url('site/entry/allorder', array('m' => $name,'type'=>'all'));
        $frames['order']['items']['allorder']['title'] = '全部订单';
        $frames['order']['items']['allorder']['actions'] = array();
        $frames['order']['items']['allorder']['active'] = '';

        //        $frames['order']['items']['wait']['url'] = url('site/entry/wait', array('m' => $name));
//        $frames['order']['items']['wait']['title'] = '待派单';
//        $frames['order']['items']['wait']['actions'] = array();
//        $frames['order']['items']['wait']['active'] = '';
//
//        $frames['order']['items']['sering']['url'] = url('site/entry/sering', array('m' => $name));
//        $frames['order']['items']['sering']['title'] = '已派单';
//        $frames['order']['items']['sering']['actions'] = array();
//        $frames['order']['items']['sering']['active'] = '';
//
//        $frames['order']['items']['final']['url'] = url('site/entry/final', array('m' => $name));
//        $frames['order']['items']['final']['title'] = '已完成';
//        $frames['order']['items']['final']['actions'] = array();
//        $frames['order']['items']['final']['active'] = '';
//
//        $frames['order']['items']['cancel']['url'] = url('site/entry/cancel', array('m' => $name));
//        $frames['order']['items']['cancel']['title'] = '取消订单';
//        $frames['order']['items']['cancel']['actions'] = array();
//        $frames['order']['items']['cancel']['active'] = '';
        ////////////////////////
//        $frames['fenlei']['title'] = '经营范围管理';
//        $frames['fenlei']['active'] = '';
//        $frames['fenlei']['items'] = array();
//
//        $frames['fenlei']['items']['range']['url'] = url('site/entry/range', array('m' => $name));
//        $frames['fenlei']['items']['range']['title'] = '经营范围列表';
//        $frames['fenlei']['items']['range']['actions'] = array();
//        $frames['fenlei']['items']['range']['active'] = '';
//
//        $frames['fenlei']['items']['addrange']['url'] = url('site/entry/addrange', array('m' => $name));
//        $frames['fenlei']['items']['addrange']['title'] = '添加经营范围';
//        $frames['fenlei']['items']['addrange']['actions'] = array();
//        $frames['fenlei']['items']['addrange']['active'] = '';

        /////////////////////////////
//        $frames['pro']['title'] = '问题管理';
//        $frames['pro']['active'] = '';
//        $frames['pro']['items'] = array();
//
//        $frames['pro']['items']['pro']['url'] = url('site/entry/problist', array('m' => $name));
//        $frames['pro']['items']['pro']['title'] = '问题列表';
//        $frames['pro']['items']['pro']['actions'] = array();
//        $frames['pro']['items']['pro']['active'] = '';
//
//        $frames['pro']['items']['addp']['url'] = url('site/entry/addprob', array('m' => $name));
//        $frames['pro']['items']['addp']['title'] = '新增问题';
//        $frames['pro']['items']['addp']['actions'] = array();
//        $frames['pro']['items']['addp']['active'] = '';
//////////////////////////////
        $frames['ad']['title'] = '轮播图管理';
        $frames['ad']['active'] = '';
        $frames['ad']['items'] = array();

        $frames['ad']['items']['site']['url'] = url('site/entry/adimglist', array('m' => $name));
        $frames['ad']['items']['site']['title'] = '轮播图';
        $frames['ad']['items']['site']['actions'] = array();
        $frames['ad']['items']['site']['active'] = '';

        $frames['ad']['items']['addad']['url'] = url('site/entry/addadimg', array('m' => $name));
        $frames['ad']['items']['addad']['title'] = '新增轮播图';
        $frames['ad']['items']['addad']['actions'] = array();
        $frames['ad']['items']['addad']['active'] = '';
        ///////////////
        $frames['type']['title'] = '分类管理';
        $frames['type']['active'] = '';
        $frames['type']['items'] = array();

        $frames['type']['items']['typelsit']['url'] = url('site/entry/typelist', array('m' => $name));
        $frames['type']['items']['typelsit']['title'] = '分类列表';
        $frames['type']['items']['typelsit']['actions'] = array();
        $frames['type']['items']['typelsit']['active'] = '';

        $frames['type']['items']['addtype']['url'] = url('site/entry/addtype', array('m' => $name));
        $frames['type']['items']['addtype']['title'] = '新增分类';
        $frames['type']['items']['addtype']['actions'] = array();
        $frames['type']['items']['addtype']['active'] = '';
        ///////////////
        $frames['hot']['title'] = '趣文热点';
        $frames['hot']['active'] = '';
        $frames['hot']['items'] = array();

        $frames['hot']['items']['hotlist']['url'] = url('site/entry/hotlist', array('m' => $name));
        $frames['hot']['items']['hotlist']['title'] = '文章列表';
        $frames['hot']['items']['hotlist']['actions'] = array();
        $frames['hot']['items']['hotlist']['active'] = '';

        $frames['hot']['items']['addhot']['url'] = url('site/entry/addhot', array('m' => $name));
        $frames['hot']['items']['addhot']['title'] = '新增文章';
        $frames['hot']['items']['addhot']['actions'] = array();
        $frames['hot']['items']['addhot']['active'] = '';

        /////////////////////////////
        $frames['user']['title'] = '用户管理';
        $frames['user']['active'] = '';
        $frames['user']['items'] = array();

        $frames['user']['items']['userlist']['url'] = url('site/entry/userlist', array('m' => $name));
        $frames['user']['items']['userlist']['title'] = '用户列表';
        $frames['user']['items']['userlist']['actions'] = array();
        $frames['user']['items']['userlist']['active'] = '';
        /////////////////////////////
        /////////////////////////////

        return $frames;
    }

    function _calc_current_frames2(&$frames)
    {
        global $_W, $_GPC, $frames;
        if (!empty($frames) && is_array($frames)) {
            foreach ($frames as &$frame) {
                foreach ($frame['items'] as &$fr) {
                    $query = parse_url($fr['url'], PHP_URL_QUERY);
                    parse_str($query, $urls);
                    if (defined('ACTIVE_FRAME_URL')) {
                        $query = parse_url(ACTIVE_FRAME_URL, PHP_URL_QUERY);
                        parse_str($query, $get);
                    } else {
                        $get = $_GET;
                    }
                    if (!empty($_GPC['a'])) {
                        $get['a'] = $_GPC['a'];
                    }
                    if (!empty($_GPC['c'])) {
                        $get['c'] = $_GPC['c'];
                    }
                    if (!empty($_GPC['do'])) {
                        $get['do'] = $_GPC['do'];
                    }
                    if (!empty($_GPC['doo'])) {
                        $get['doo'] = $_GPC['doo'];
                    }
                    if (!empty($_GPC['op'])) {
                        $get['op'] = $_GPC['op'];
                    }
                    if (!empty($_GPC['m'])) {
                        $get['m'] = $_GPC['m'];
                    }
                    $diff = array_diff_assoc($urls, $get);

                    if (empty($diff)) {
                        $fr['active'] = ' active';
                        $frame['active'] = ' active';
                    }
                }
            }
        }
    }
}