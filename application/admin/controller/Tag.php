<?php

namespace app\admin\controller;
use app\common\controller\Admin;

/**
 * @title 关键字管理
 * @description 关键字管理
 */
class Tag extends Admin {
    protected $db;

    public function _initialize() {
        parent::_initialize();
        $this->db      = db('tag');

    }

    /**
     * @title 关键字例表
     */
    public function index($page = 1, $r = 20) {
        //读取规则列表


        $list = $this->db->order('counter desc')->paginate($r, false, array(
            'query'  => $this->request->param()
        ));

        $data = array(
            'list' => $list,
            'page' => $list->render(),
        );
        $this->assign($data);
        $this->setMeta("关键字列表");
        return $this->fetch();
    }
    /**
     * @title 添加关键字
     */
    public function add() {
        $link = db('tag');
        if ($this->request->isPost()) {
            $data = input('post.');
            if ($data) {
                unset($data['id']);
                $data['create_at']=strtotime('now');
                $data['counter']=0;



                try{
                    $result = $link->insert($data);
                }catch (\Exception $e){
                    return $this->error("发生错误 关键字不能重复");
                }


                if ($result) {
                    return $this->success("添加成功！", url('Tag/index'));
                } else {
                    return $this->error($link->getError());
                }



            } else {
                return $this->error($link->getError());
            }
        } else {
            $data = array(
                'info' =>null
            );
            $this->assign($data);
            $this->setMeta("添加关键字");
            return $this->fetch('edit');
        }
    }


    /**
     * @title 修改关键字
     */
    public function edit() {
        $link = db('tag');
        $id   = input('id', '', 'trim,intval');
        if ($this->request->isPost()) {
            $data = input('post.');
            if ($data) {
                $result = $link->where( array('id' => $data['id']))->update($data);
                if ($result) {
                    return $this->success("修改成功！", url('Tag/index'));
                } else {
                    return $this->error("修改失败！");
                }
            } else {
                return $this->error($link->getError());
            }
        } else {
            $map  = array('id' => $id);
            $info = $link->where($map)->find();

            $data = array(
                'keyList' => $link->select(),
                'info'    => $info,
            );
            $this->assign($data);
            $this->setMeta("编辑关键字");
            return $this->fetch();
        }
    }

    /**
     * @title 删除链接
     */
    public function delete() {
        $id = $this->getArrayParam('id');
        if (empty($id)) {
            return $this->error('非法操作！');
        }
        $link = db('tag');

        $map    = array('id' => array('IN', $id));
        $result = $link->where($map)->delete();
        if ($result) {
            return $this->success("删除成功！");
        } else {
            return $this->error("删除失败！");
        }
    }

}