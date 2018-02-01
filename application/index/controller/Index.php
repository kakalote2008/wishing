<?php
namespace app\index\controller;

class Index
{
    public function index()
    {
        return ['success' => 1];
    }

    public function createWishingPool()
    {
        header('Access-Control-Allow-Origin:*');
        $id = input('id');
        $name = input('name');
        $description = input('description');
        $wishing_card_ids_array = input('wishing_card_ids/a');
        $wishing_card_ids = implode(",", $wishing_card_ids_array);
        $img_bg = input('img_bg');
        $img_cover = input('img_cover');
        $data = [
            'name' => $name,
            'description' => $description,
            'wishing_card_ids' => $wishing_card_ids,
            'img_bg' => $img_bg,
            'img_cover' => $img_cover,
        ];
        if ($id) {
            $update = db('wishing_pool')->where('id', $id)->update($data);
            $success = $update == 1 ? 1 : 0;
        } else {
            $insert = db('wishing_pool')->insert($data);
            $success = $insert == 1 ? 1 : 0;
        }

        return ['success' => $success];
    }

    public function createWishingCard()
    {
        header('Access-Control-Allow-Origin:*');
        $name = input('name');
        $description = input('description');
        $price = input('price/f');
        $valid_time = input('valid_time/d');
        $img = input('img');
        $data = [
            'name' => $name,
            'description' => $description,
            'price' => $price,
            'img' => $img,
            'valid_time' => $valid_time,
        ];
        $insert = db('wishing_card')->insert($data);
        $success = $insert == 1 ? 1 : 0;
        return ['success' => $success];
    }

    public function createGuardingCard()
    {

        header('Access-Control-Allow-Origin:*');
        $name = input('name');
        $description = input('description');
        $price = input('price/f');
        $valid_time = input('valid_time/d');
        $img = input('img');
        $function = input('function');
        $data = [
            'name' => $name,
            'description' => $description,
            'price' => $price,
            'img' => $img,
            'valid_time' => $valid_time,
            'function' => $function,
        ];
        $insert = db('guarding_card')->insert($data);
        $success = $insert == 1 ? 1 : 0;
        return ['success' => $success];

    }

    public function showUsers()
    {
        header('Access-Control-Allow-Origin:*');
        $nick_name = input('nick_name');
        $users = db('user')->where('nick_name', ['like', $nick_name . '%'], ['like', '%' . $nick_name], 'or')->select();
        return ['success' => 1, 'data' => $users];
    }

    public function showWishings()
    {
        header('Access-Control-Allow-Origin:*');
        $user_id = input('user_id/d');
        $wishing_pool_id = input('wishing_pool_id/d');
        if ($user_id && $wishing_pool_id) {
            $wishings = db('wishing')->where('wishing_pool_id', $wishing_pool_id)->where('user_id', $user_id)->where('status', '<', 4)->limit(100)->order('create_time desc')->select();
        } elseif ($user_id) {
            $wishings = db('wishing')->where('user_id', $user_id)->where('status', '<', 4)->limit(100)->order('create_time desc')->select();
        } elseif ($wishing_pool_id) {
            $wishings = db('wishing')->where('wishing_pool_id', $wishing_pool_id)->where('status', '<', 4)->limit(100)->order('create_time desc')->select();
        } else {
            $wishings = db('wishing')->where('status', '<', 4)->order('create_time desc')->limit(100)->select();
        }

        return ['success' => 1, 'data' => $wishings];
    }

    public function showGuardings()
    {
        header('Access-Control-Allow-Origin:*');
        $buy_user_id = input('buy_user_id/d');
        if ($buy_user_id) {
            $guardings = db('guarding')->where('buy_user_id', $buy_user_id)->limit(100)->order('create_time desc')->select();
        } else {
            $guardings = db('guarding')->order('create_time desc')->limit(100)->select();
        }

        return ['success' => 1, 'data' => $guardings];
    }

    public function showWishingCards()
    {
        header('Access-Control-Allow-Origin:*');
        $wishing_cards = db('wishing_card')->select();
        return ['success' => 1, 'data' => $wishing_cards];
    }

    public function showGuardingCards()
    {
        header('Access-Control-Allow-Origin:*');
        $guarding_cards = db('guarding_card')->select();
        return ['success' => 1, 'data' => $guarding_cards];
    }

    public function deleteWishingById()
    {
        header('Access-Control-Allow-Origin:*');
        $id = input('id');
        $delete = db('wishing')->where('id', $id)->setField('status', 4);
        $success = $delete == 1 ? 1 : 0;
        return ['success' => $success];
    }

    public function deleteGuardingById()
    {
        header('Access-Control-Allow-Origin:*');
        $id = input('id');
        $delete = db('guarding')->where('id', $id)->delete();
        $success = $delete == 1 ? 1 : 0;
        return ['success' => $success];
    }

    public function deleteWishingPoolById()
    {
        header('Access-Control-Allow-Origin:*');
        $id = input('id');
        $delete = db('wishing_pool')->where('id', $id)->delete();
        $success = $delete == 1 ? 1 : 0;
        return ['success' => $success];
    }

    public function deleteWishingCardById()
    {
        header('Access-Control-Allow-Origin:*');
        $id = input('id');
        $delete = db('wishing_card')->where('id', $id)->delete();
        $success = $delete == 1 ? 1 : 0;
        return ['success' => $success];
    }

    public function deleteGuardingCardById()
    {
        header('Access-Control-Allow-Origin:*');
        $id = input('id');
        $delete = db('guarding_card')->where('id', $id)->delete();
        $success = $delete == 1 ? 1 : 0;
        return ['success' => $success];
    }

    public function getWishingPools()
    {
        header('Access-Control-Allow-Origin:*');
        $wishing_pools = db('wishing_pool')->order('create_time desc')->select();
        return ['success' => 1, 'data' => $wishing_pools];
    }

    public function getGuardingById()
    {
        header('Access-Control-Allow-Origin:*');
        $id = input('id');
        $guarding = db('guarding')->where('id', $id)->find();
        $status = $this->calcGuardingStatus($guarding);
        $guarding['status'] = $status['status'];
        $guarding['countdown'] = $status['countdown'];
        return ['success' => 1, 'data' => $guarding];
    }

    public function getWishingById()
    {
        header('Access-Control-Allow-Origin:*');
        $id = input('id');
        $wishing = db('wishing')->where('id', $id)->find();
        if (!$wishing) {
            return ['success' => 0, 'msg' => '愿望不存在'];
        }
        if ($wishing['user_id'] && $wishing['public']) {
            $wishing['nick_name'] = db('user', [], false)->where('id', $wishing['user_id'])->value('nick_name');
            $wishing['avatar_url'] = db('user', [], false)->where('id', $wishing['user_id'])->value('avatar_url');
        }
        $status = $this->calcWishingStatus($wishing);
        $wishing['status'] = $status['status'];
        $wishing['countdown'] = $status['countdown'];
        $wishing['wishing_card_name'] = db('wishing_card', [], false)->where('id', $wishing['wishing_card_id'])->value('name');
        $wishing['wishing_card_img'] = db('wishing_card', [], false)->where('id', $wishing['wishing_card_id'])->value('img');if ($wishing['blessing_user_ids']) {
            $blessingInfos = [];
            $blessing_user_ids_array = explode(',', $wishing['blessing_user_ids']);
            //防止重复祝福
            foreach ($blessing_user_ids_array as $key => $value) {
                $user['nick_name'] = db('user', [], false)->where('id', $value)->value('nick_name');
                $user['avatar_url'] = db('user', [], false)->where('id', $value)->value('avatar_url');
                $blessingInfos[] = $user;
            }
            $wishing['blessingInfos'] = $blessingInfos;
        }
        return ['success' => 1, 'data' => $wishing];
    }

    public function getWishingPoolById()
    {
        header('Access-Control-Allow-Origin:*');
        $id = input('id');
        $wishing_pool = db('wishing_pool')->where('id', $id)->find();
        if ($wishing_pool) {
            $wishing_cards = db('wishing_card')->where('id', 'in', explode(',', $wishing_pool['wishing_card_ids']))->select();
            $wishing_pool['wishing_cards'] = $wishing_cards;
        }

        return ['success' => 1, 'data' => $wishing_pool];
    }

    public function getWishingsByPoolId()
    {
        header('Access-Control-Allow-Origin:*');
        $wishing_pool_id = input('wishing_pool_id');
        $start_id = input('start_id', 0);
        $start = input('start');
        $limit = input('limit');
        $wishings = db('wishing', [], false)->where('wishing_pool_id', $wishing_pool_id)->where('id', '>', $start_id)->where('status', '<', 4)->limit($start, $limit)->order('create_time desc')->select();
        $count = db('wishing', [], false)->where('wishing_pool_id', $wishing_pool_id)->count();
        $hasmore = $count > $start + $limit;
        foreach ($wishings as $key => $value) {
            if ($wishings[$key]['user_id'] && $wishings[$key]['public']) {
                $wishings[$key]['nick_name'] = db('user', [], false)->where('id', $wishings[$key]['user_id'])->value('nick_name');
                $wishings[$key]['avatar_url'] = db('user', [], false)->where('id', $wishings[$key]['user_id'])->value('avatar_url');
            }
            $status = $this->calcWishingStatus($value);
            $wishings[$key]['status'] = $status['status'];
            $wishings[$key]['countdown'] = $status['countdown'];
            $wishings[$key]['wishing_card_name'] = db('wishing_card', [], false)->where('id', $wishings[$key]['wishing_card_id'])->value('name');
            $wishings[$key]['wishing_card_img'] = db('wishing_card', [], false)->where('id', $wishings[$key]['wishing_card_id'])->value('img');

        }
        return ['success' => 1, 'data' => $wishings, 'hasmore' => $hasmore, 'total_count' => $count];
    }
    public function getGuardingsByUserId()
    {
        header('Access-Control-Allow-Origin:*');
        $buy_user_id = input('buy_user_id');
        $start = input('start');
        $limit = input('limit');
        $guardings = db('guarding', [], false)->where('buy_user_id', $buy_user_id)->limit($start, $limit)->order('create_time desc')->select();
        $count = db('guarding', [], false)->where('buy_user_id', $buy_user_id)->count();
        $hasmore = $count > $start + $limit;
        foreach ($guardings as $key => $value) {
            if ($guardings[$key]['buy_user_id']) {
                $guardings[$key]['nick_name'] = db('user', [], false)->where('id', $guardings[$key]['buy_user_id'])->value('nick_name');
                $guardings[$key]['avatar_url'] = db('user', [], false)->where('id', $guardings[$key]['buy_user_id'])->value('avatar_url');
            }
            $status = $this->calcGuardingStatus($value);
            $guardings[$key]['status'] = $status['status'];
            $guardings[$key]['countdown'] = $status['countdown'];
            $guardings[$key]['guarding_card_name'] = db('guarding_card')->where('id', $guardings[$key]['guarding_card_id'])->value('name');
            $guardings[$key]['guarding_card_img'] = db('guarding_card', [], false)->where('id', $guardings[$key]['guarding_card_id'])->value('img');
        }
        return ['success' => 1, 'data' => $guardings, 'hasmore' => $hasmore];
    }

    public function getWishingsByUserId()
    {
        header('Access-Control-Allow-Origin:*');
        $user_id = input('user_id');
        $start = input('start');
        $limit = input('limit');
        $wishings = db('wishing', [], false)->where('user_id', $user_id)->where('status', '<', 4)->limit($start, $limit)->order('create_time desc')->select();
        $count = db('wishing', [], false)->where('user_id', $user_id)->count();
        $hasmore = $count > $start + $limit;
        foreach ($wishings as $key => $value) {
            if ($wishings[$key]['user_id'] && $wishings[$key]['public']) {
                $wishings[$key]['nick_name'] = db('user', [], false)->where('id', $wishings[$key]['user_id'])->value('nick_name');
                $wishings[$key]['avatar_url'] = db('user', [], false)->where('id', $wishings[$key]['user_id'])->value('avatar_url');
            }
            $status = $this->calcWishingStatus($value);
            $wishings[$key]['status'] = $status['status'];
            $wishings[$key]['countdown'] = $status['countdown'];
            $wishings[$key]['wishing_card_name'] = db('wishing_card')->where('id', $wishings[$key]['wishing_card_id'])->value('name');
            $wishings[$key]['wishing_card_img'] = db('wishing_card', [], false)->where('id', $wishings[$key]['wishing_card_id'])->value('img');
        }
        return ['success' => 1, 'data' => $wishings, 'hasmore' => $hasmore, 'total_count' => $count];
    }

    public function wish()
    {
        header('Access-Control-Allow-Origin:*');
        $content = input('content');
        $public = input('public', 1);
        $out_trade_no = input('out_trade_no');
        $user_id = input('user_id/d', 0);
        $wishing_pool_id = input('wishing_pool_id/d');
        $wishing_card_id = input('wishing_card_id/d');
        $valid_time = db('wishing_card')->where('id', $wishing_card_id)->value('valid_time');
        $data = [
            'content' => $content,
            'user_id' => $user_id,
            'wishing_pool_id' => $wishing_pool_id,
            'wishing_card_id' => $wishing_card_id,
            'valid_time' => $valid_time,
            'out_trade_no' => $out_trade_no,
            'public' => $public,
        ];
        $insert = db('wishing')->insertGetId($data);
        if ($insert > 0) {
            $success = 1;
            db('wishing_pool')->where('id', $wishing_pool_id)->setInc('wishing_count');
        } else {
            $success = 0;
        }

        return ['success' => $success, 'wishing_id' => $insert];
    }

    public function bless()
    {
        header('Access-Control-Allow-Origin:*');
        $bless_user_id = input('bless_user_id/d', 0);
        $wishing_id = input('wishing_id/d');
        $wishing = db('wishing', [], false)->where('id', $wishing_id)->find();
        if ($wishing) {
            if ($wishing['blessing_user_ids']) {
                $blessing_user_ids_array = explode(',', $wishing['blessing_user_ids']);
                //防止重复祝福
                foreach ($blessing_user_ids_array as $key => $value) {
                    if ($bless_user_id == $value) {
                        return ['success' => 0, 'msg' => '您已经祝福过了'];
                    }
                }
                $blessing_user_ids = $wishing['blessing_user_ids'] . ',' . $bless_user_id;
                $update = db('wishing', [], false)->where('id', $wishing_id)->setField('blessing_user_ids', $blessing_user_ids);
                $update = db('wishing', [], false)->where('id', $wishing_id)->setInc('blessing_count');
                $success = $update == 1 ? 1 : 0;
                return ['success' => $success];
            } else {
                $update = db('wishing', [], false)->where('id', $wishing_id)->setField('blessing_user_ids', $bless_user_id);
                $update = db('wishing', [], false)->where('id', $wishing_id)->setInc('blessing_count');
                $success = $update == 1 ? 1 : 0;
                return ['success' => $success];
            }
        } else {
            return ['success' => 0, 'msg' => 'wishing not exist'];
        }
    }

    public function guard()
    {
        header('Access-Control-Allow-Origin:*');
        $guarding_card_id = input('guarding_card_id/d');
        $buy_user_id = input('buy_user_id/d');
        $buy_time = date('Y-m-d H:i:s');
        $data = [
            'guarding_card_id' => $guarding_card_id,
            'buy_user_id' => $buy_user_id,
            'buy_time' => $buy_time,
        ];
        $insert = db('guarding')->insertGetId($data);
        $success = $insert > 0 ? 1 : 0;

        return ['success' => $success, 'guarding_id' => $insert];

    }
    /**
     * 1 守护中
     * 2 守护失效
     * 3 未领取
     */
    private function calcGuardingStatus($guarding)
    {
        $valid_time = db('guarding_card')->where('id', $guarding['guarding_card_id'])->value('valid_time');
        if ($guarding['receive_user_id'] > 0) {
            $receive_time = strtotime($guarding['receive_time']);
            $now = time();
            $calc = $valid_time - ($now - $receive_time);
            if ($calc > 0) {
                $status['status'] = 1;
                $status['countdown'] = $calc;
                db('guarding')->where('id', $guarding['id'])->setField('status', 1);
            } else {
                $status['status'] = 2;
                $status['countdown'] = 0;
                db('guarding')->where('id', $guarding['id'])->setField('status', 2);
            }
        } else {
            $status['status'] = 3;
            $status['countdown'] = 0;
        }
        return $status;
    }

    /**
     *
     * 1.守护中：（当前时间-许愿时间-守护有效时长）-(御守守护时长+祝福人数/5)  < 0  为守护中状态的愿望
     * 2.守护失效：（当前时间-许愿时间-守护有效时长）-(御守守护时长+祝福人数/5)  > 0 为守护失效的愿望
     * 3.许愿失败：许愿发送失败的状态
     * 4.删除：被管理员逻辑删除的愿望
     */
    private function calcWishingStatus($wishing)
    {
       
        $now = time();
        //先计算祝福增益逻辑
        $add = round($wishing['blessing_count'] / 5);
        if ($add > $wishing['add']) {
            if ($wishing['status'] == 2) { //已经失效的要重置
                db('wishing', [], false)->where('id', $wishing['id'])->setField('create_time', $now);
                db('wishing', [], false)->where('id', $wishing['id'])->setField('valid_time', 3600 * 24 * ($add - $wishing['add']));
                db('wishing', [], false)->where('id', $wishing['id'])->setField('add', $add);
            } else if ($wishing['status'] == 1) { //还没失效的累加时间
                $valid_time = db('wishing', [], false)->where('id', $wishing['id'])->value('valid_time');
                db('wishing', [], false)->where('id', $wishing['id'])->setField('valid_time', $valid_time + 3600 * 24 * ($add - $wishing['add']));
                db('wishing', [], false)->where('id', $wishing['id'])->setField('add', $add);
            }
        }
        //计算展现状态
        $create_time = strtotime($wishing['create_time']);
        $valid_time = db('wishing', [], false)->where('id', $wishing['id'])->value('valid_time');
        $calc = $valid_time - ($now - $create_time);
        if ($calc > 0) {
            $status['status'] = 1;
            $status['countdown'] = $calc;
        } else {
            $status['status'] = 2;
            $status['countdown'] = 0;
        }

        //状态更新
        if ($wishing['status'] != $status['status']) {
            db('wishing')->where('id', $wishing['id'])->setField('status', $status['status']);
        }
        return $status;
    }

    public function getUserByid()
    {
        header('Access-Control-Allow-Origin:*');
        $id = input('id');
        $user = db('user')->where('id', $id)->find();
        return ['success' => 1, 'data' => $user];
    }

    public function upload()
    {
        $file = request()->file('file');
        $domain = 'http://wish.dtxn.net/';
        $path = 'upload/';
        if ($file) {
            $info = $file->move($path);
            if ($info) {
                return ['success' => 1, 'file_path' => $domain . $path . $info->getSaveName()];
            } else {
                return ['success' => 0, 'msg' => $file->getError()];
            }
        }
    }

    public function login()
    {
        $appid = 'wx8a263f9a4b85e002';
        $secret = '98640682c3de985d618e400829f1ba7e';
        $jscode = input('jscode');
        $nick_name = input('nick_name');
        $avatar_url = input('avatar_url');
        $url = 'https://api.weixin.qq.com/sns/jscode2session?appid=' . $appid . '&secret=' . $secret . '&js_code=' . $jscode . '&grant_type=authorization_code';
        $con = curl_init($url);
        curl_setopt($con, CURLOPT_RETURNTRANSFER, true);
        $response = json_decode(curl_exec($con), true);
        curl_close($con);
        // var_dump($response);
        if (isset($response['openid'])) {
            $user = db('user', [], false)->where('openid', $response['openid'])->find();
            if ($user) {
                $update = db('user', [], false)->where('openid', $response['openid'])->update(['session_key' => $response['session_key'], 'nick_name' => $nick_name, 'avatar_url' => $avatar_url]);
                $user_id = db('user', [], false)->where('openid', $response['openid'])->value('id');
                return ['success' => 1, 'user_id' => $user_id];
            } else {
                $data = [
                    'openid' => $response['openid'],
                    'avatar_url' => $avatar_url,
                    'nick_name' => $nick_name,
                    'session_key' => $response['session_key'],
                ];
                $insert = db('user', [], false)->insertGetId($data);
                $success = $insert > 0 ? 1 : 0;
                return ['success' => $success, 'user_id' => $insert];
            }
        } else {
            $response['success'] = 0;
            return $response;
        }
    }
    public function payUnifiedOrder()
    {
        $url = 'https://api.mch.weixin.qq.com/pay/unifiedorder';
        $request = request();
        $con = curl_init($url);
        $user_id = input('user_id');
        $data['attach'] = input('attach');
        $data['openid'] = db('user')->where('id', $user_id)->value('openid');
        $data['appid'] = 'wx8a263f9a4b85e002';
        $data['mch_id'] = '1497582232';
        $data['nonce_str'] = md5(rand());
        $data['body'] = input('description');
        $data['out_trade_no'] = md5(uniqid());
        $out_trade_no = $data['out_trade_no'];
        $data['total_fee'] = input('price');
        $data['spbill_create_ip'] = $request->ip();
        // $data['spbill_create_ip'] = input('ip');
        $data['notify_url'] = 'https://wish.dtxn.net/PayNotify';
        $data['trade_type'] = 'JSAPI';
        ksort($data);
        $stringSignTemp = '';
        foreach ($data as $key => $value) {
            $stringSignTemp = $stringSignTemp . $key . '=' . $value . '&';
        }
        $stringSignTemp = $stringSignTemp . 'key=174b06a3b65f7aa6547f9cc9ab575a6d';
        $data['sign'] = strtoupper(md5($stringSignTemp));
        $doc = $this->arrayToXml($data);
        curl_setopt($con, CURLOPT_HEADER, false);
        curl_setopt($con, CURLOPT_POSTFIELDS, $doc);
        curl_setopt($con, CURLOPT_POST, true);
        curl_setopt($con, CURLOPT_RETURNTRANSFER, true);
        $response = $this->xmlToArray(curl_exec($con));
        curl_close($con);
        if ($response['return_code'] == 'SUCCESS' && isset($response['result_code']) && $response['result_code'] == 'SUCCESS') {
            return ['success' => 1, 'prepay_id' => $response['prepay_id'], 'out_trade_no' => $data['out_trade_no'], 'return_code' => $response['return_code'], 'result_code' => $response['result_code']];
        }
        return ['success' => 1, 'return_code' => $response['return_code'], 'return_msg' => $response['return_msg']];

    }

    public function PayNotify()
    {
        $xml = file_get_contents("php://input");
        $xml_array = $this->xmlToArray($xml);
        if ($xml_array['return_code'] == 'SUCCESS') {
            $url = 'https://api.mch.weixin.qq.com/pay/orderquery';
            $con = curl_init($url);
            $query['appid'] = 'wx8a263f9a4b85e002';
            $query['mch_id'] = '1497582232';
            $query['out_trade_no'] = $xml_array['out_trade_no'];
            $query['nonce_str'] = md5(rand());
            ksort($query);
            $stringSignTemp = '';
            foreach ($query as $key => $value) {
                $stringSignTemp = $stringSignTemp . $key . '=' . $value . '&';
            }
            $stringSignTemp = $stringSignTemp . 'key=174b06a3b65f7aa6547f9cc9ab575a6d';
            $query['sign'] = strtoupper(md5($stringSignTemp));
            $doc = $this->arrayToXml($query);
            curl_setopt($con, CURLOPT_HEADER, false);
            curl_setopt($con, CURLOPT_POSTFIELDS, $doc);
            curl_setopt($con, CURLOPT_POST, true);
            curl_setopt($con, CURLOPT_RETURNTRANSFER, true);
            $response = $this->xmlToArray(curl_exec($con));
            if ($response['return_code'] == 'SUCCESS' && isset($response['result_code']) && $response['result_code'] == 'SUCCESS') {
                $data['attach'] = $response['attach'];
                $data['openid'] = $response['openid'];
                $data['bank_type'] = $response['bank_type'];
                $data['total_fee'] = $response['total_fee'];
                $data['transaction_id'] = $response['transaction_id'];
                $data['out_trade_no'] = $response['out_trade_no'];
                $data['time_end'] = $response['time_end'];
                $data['trade_state'] = $response['trade_state'];
                $insert = db('order')->insertGetId($data);
                if ($insert) {
                    $return['return_code'] = 'SUCCESS';
                    $return['return_msg'] = 'ok';
                    return xml($return);
                }
            }
        }
    }
    public function checkPay()
    {
        $out_trade_no = input('out_trade_no');
        $order = db('order')->where('out_trade_no', $out_trade_no)->find();
        if ($order) {
            return ['success' => 1, 'trade_state' => $order['trade_state']];
        }
        return ['success' => 0];
    }

    public function arrayToXml($arr)
    {
        $xml = "<?xml version='1.0' encoding='UTF-8'?><xml>";
        foreach ($arr as $key => $val) {
            if (is_array($val)) {
                $xml .= "<" . $key . ">" . arrayToXml($val) . "</" . $key . ">";
            } else {
                $xml .= "<" . $key . ">" . $val . "</" . $key . ">";
            }
        }
        $xml .= "</xml>";
        return $xml;
    }

    public function xmlToArray($xml)
    {
        //禁止引用外部xml实体
        libxml_disable_entity_loader(true);
        $values = json_decode(json_encode(simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA)), true);
        return $values;
    }

}
