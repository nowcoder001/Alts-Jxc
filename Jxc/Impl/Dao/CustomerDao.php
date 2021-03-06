<?php

namespace Jxc\Impl\Dao;

use Exception;
use Jxc\Impl\Core\MySQLDao;
use Jxc\Impl\Vo\VoCustomer;

/**
 * 顾客信息Dao
 * Class ProductDao
 * @package Jxc\Impl\Dao
 */
class CustomerDao extends MySQLDao {

    public function __construct($config) {
        parent::__construct($config);
    }


    public function w2uiSelect($where = array()) {
        $sets = $this->mysqlDB()->select('tb_customer', '*', $where);
        $data = array();
        foreach ($sets as $k => $v) {
            $voCustomer = new VoCustomer();
            $voCustomer->convert($v);
            $data[] = array('id' => $k, 'text' => "[{$voCustomer->ct_id}]:{$voCustomer->ct_name}", 'ct_id' => $voCustomer->ct_id, 'ct_address' => $voCustomer->ct_address);
        }
        return $data;
    }

    public function selectById($ids, $status = 0) {
        $inId = implode(",", $ids);
        $query = "SELECT * FROM tb_customer WHERE ct_id IN ({$inId}) AND status={$status};";
        $sets = $this->mysqlDB()->ExecuteSQL($query);
        $map = array();
        foreach ($sets as $v) {
            $voCustomer = new VoCustomer();
            $voCustomer->convert($v);
            $map[$voCustomer->ct_id] = $voCustomer;
        }
        return $map;
    }

    public function selectCustomNameList() {
        $query = "SELECT ct_name FROM tb_customer;";
        $resultSet = $this->mysqlDB()->ExecuteSQL($query);
        return $resultSet;
    }

    public function selectAll($status = 0) {
        $sets = $this->mysqlDB()->select('tb_customer', '*', array('status' => $status));
        $array = array();
        foreach ($sets as $data) {
            $voCustomer = new VoCustomer();
            $voCustomer->convert($data);
            $array[$voCustomer->ct_id] = $voCustomer;
        }
        return $array;
    }

    public function w2Search($searches, $logic) {
        $where = '';
        foreach ($searches as $k => $v) {
            if ($where) {
                $where .= $logic;
            }
            switch ($v['type']) {
                case 'text': {
                    switch ($v['operator']) {
                        case 'is':
                            $where .= "{$v['field']} = '{$v['value']}'";
                            break;
                        case 'begins':
                            $where .= "{$v['field']} LIKE '{$v['value']}%'";
                            break;
                        case 'ends':
                            $where .= "{$v['field']} LIKE '%{$v['value']}'";
                            break;
                        case 'contains':
                            $where .= "{$v['field']} LIKE '%{$v['value']}%'";
                            break;
                    }
                    break;
                }
            }
        }
        $query = 'select * from tb_customer where ' . $where;
        $sets = $this->mysqlDB()->ExecuteSQL($query);
        $array = array();
        foreach ($sets as $data) {
            $vo = new VoCustomer();
            $vo->convert($data);
            $vo->recid = $vo->ct_id;
            $array[] = $vo;
        }
        return $array;
    }

    /**
     * @param $ct_id
     * @param int $status
     * @return VoCustomer|null
     */
    public function selectByCtId($ct_id, $status = 0) {
        $sets = $this->mysqlDB()->select('tb_customer', '*', array('ct_id' => $ct_id, 'status' => $status));
        if ($sets) {
            $voCustomer = new VoCustomer();
            $voCustomer->convert($sets[0]);
            return $voCustomer;
        }
        return null;
    }

    /**
     * @param $voCustomer VoCustomer
     * @return VoCustomer
     * @throws Exception
     */
    public function insert($voCustomer) {
        $query = $this->mysqlDB()->sqlInsert('tb_customer', $voCustomer->toArray());
        $this->mysqlDB()->ExecuteSQL($query);
        $voCustomer->ct_id = $this->mysqlDB()->getInsertId();
        return $voCustomer;
    }

    /**
     * @param $voCustomer VoCustomer
     * @param array $fields
     */
    public function updateByFields($voCustomer, $fields = array()) {
        $query = $this->mysqlDB()->sqlUpdateWhere('tb_customer', $voCustomer->toArray($fields), array('ct_id' => $voCustomer->ct_id));
        $this->mysqlDB()->ExecuteSQL($query);
    }

    /**
     * @param $ct_id  int  顾客唯一ID
     * @throws Exception
     */
    public function delete($ct_id) {
        $query = $this->mysqlDB()->sqlDeleteWhere('tb_customer', array('ct_id' => $ct_id));
        $this->mysqlDB()->ExecuteSQL($query);
    }


    //

}