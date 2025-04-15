<?php
/**
 * 订单管理
 */
namespace app\mxadmin\controller;
use app\mxadmin\AdminBase;
use app\common\model\Order as OrderModel;
use app\common\model\User;
use app\common\model\UserAccount;
use app\common\model\Goods;
use app\common\model\Cloud;
use app\common\model\Stand;

class Order extends AdminBase
{
	/**
	 * 展示
	 */
	public function index()
	{
		$nowtime = strtotime("-1 month");
		$type = config('status.accType');
		return view('', [
			'nowtime' => $nowtime,
			'type' => $type
		]);
	}

	/**
	 * 数据
	 */
	public function datalist($limit = 15)
	{
		$list = OrderModel::order('id', 'desc')->paginate($limit);
		foreach ($list as $key => $value) {
			$user = User::find($value['userid']);
			$list[$key]['users'] = '登录邮箱：'.$user['email'].'<br>UID：'.$value['userid'];
			if ($value['type'] == 1) {
				$list[$key]['kaihu'] = $value['email'];
			}else{
				$list[$key]['kaihu'] = '无';
			}

			if ($value['invoice'] == 0) {
				$list[$key]['invoiceTitle'] = '不开票';
			}else{
				$list[$key]['invoiceTitle'] = $value['invoiceTitle'];
			}
			
			$list[$key]['addtime'] = date('Y-m-d H:i:s', $value['addtime']);
			$list[$key]['paytime'] = $value['paytime']?date('Y-m-d H:i:s', $value['paytime']):'未付款';
		}

		return $this->result($list);
	}

	/**
	 * 搜索
	 */
	public function search($limit = 15)
	{
		if (request()->isGet()) {
			$data = input('param.');
			$search = new OrderModel();

			if ($data['email'] != '') {
				$user = User::where('email', $data['email'])->find();
				$search = $search->where('userid', $user['id']);
			}

			if ($data['trade_no'] != '') {
				$search = $search->where('trade_no', $data['trade_no']);
			}

			if ($data['status'] > 0) {
				$search = $search->where('status', $data['status'] - 1);
			}

			if ($data['start_time'] > 0 && $data['end_time'] > 0){
            	$endtime = $data['end_time']." 23:59:59";
            	$search = $search->where('addtime', 'between time',[$data['start_time'],$endtime]);
            }

            $list = $search->order('id','desc')->paginate($limit);
            foreach ($list as $key => $value) {
				$user = User::find($value['userid']);
				$list[$key]['users'] = '登录邮箱：'.$user['email'].'<br>UID：'.$value['userid'];
				if ($value['type'] == 1) {
					$list[$key]['kaihu'] = $value['email'];
				}else{
					$list[$key]['kaihu'] = '无';
				}

				if ($value['invoice'] == 0) {
					$list[$key]['invoiceTitle'] = '不开票';
				}else{
					$list[$key]['invoiceTitle'] = $value['invoiceTitle'];
				}
				
				$list[$key]['addtime'] = date('Y-m-d H:i:s', $value['addtime']);
				$list[$key]['paytime'] = $value['paytime']?date('Y-m-d H:i:s', $value['paytime']):'未付款';
			}

			return $this->result($list);
		}
	}


	/**
	 * 服务器添加
	 */
	public function addStand()
	{
		if (request()->isPost()) {
			$data = [
				'id' => $this->request->param('id', '', 'trim'),
				'userid' => $this->request->param('userid', '', 'trim'),
				'content' => $this->request->param('content', '', 'trim'),
				'seller' => $this->request->param('seller', '', 'trim'),
				'agsell' => $this->request->param('agsell', '', 'trim'),
				'price' => $this->request->param('price', '', 'trim'),
				'endtime' => $this->request->param('endtime', '', 'trim'),
				'remarks' => $this->request->param('remarks', '', 'trim')
			];

			//服务器添加
			$stand = [
				'uid' => $data['userid'],
				'content' => $data['content'],
				'seller' => $data['seller'],
				'agsell' => $data['agsell'],
				'price' => $data['price'],
				'addtime' => time(),
				'endtime' => strtotime($data['endtime']),
				'remarks' => $data['remarks']
			];

			$result = Stand::create($stand);
			if ($result == true) {
				//更新订单状态
				$order = [
					'id' => $data['id'],
					'status' => 2
				];

				OrderModel::update($order);
				return $this->success('添加成功！');
			}else{
				return $this->error('添加失败');
			}
		}
	}

	/**
	 * 服务器续期
	 */
	public function editStand()
	{
		if (request()->isPost()) {
			$data = [
				'id' => $this->request->param('id', '', 'trim'),
				'goodsId' => $this->request->param('goodsId', '', 'trim'),
				'endtime' => $this->request->param('endtime', '', 'trim')
			];

			//更新时间
			$stand = Stand::find($data['goodsId']);
			$endtime = date('Y-m-d H:i:s', $stand['endtime']);
			$update = [
				'id' => $data['goodsId'],
				'endtime' => strtotime($endtime . '+'.$data['endtime'].' month')
			];
			$result = Stand::update($update);
			if ($result == true) {
				//更新状态
				$order = [
					'id' => $data['id'],
					'status' => 2
				];
				OrderModel::update($order);
				return $this->success('续期成功！');
			}else{
				return $this->error('续期失败');
			}
		}
	}

	/**
	 * 公有云添加
	 */
	public function addCloud()
	{
		if (request()->isPost()) {
			$data = [
				'id' => $this->request->param('id', '', 'trim'),
				'userid' => $this->request->param('userid', '', 'trim'),
				'uid' => $this->request->param('uid', '', 'trim'),
				'email' => $this->request->param('email', '', 'trim'),
				'type' => $this->request->param('type', '', 'trim'),
				'usd' => $this->request->param('usd', '', 'trim'),
				'info' => $this->request->param('info', '', 'trim'),
				'remarks' => $this->request->param('remarks', '', 'trim')
			];

			//公有云添加
			$cloud = [
				'userid' => $data['userid'],
				'type' => $data['type'],
				'uid' => $data['uid'],
				'email' => $data['email'],
				'usd' => $data['usd'],
				'info' => $data['info'],
				'remarks' => $data['remarks'],
				'addtime' => time()
			];

			$result = UserAccount::create($cloud);
			if ($result ==  true) {
				//更新订单
				$order = [
					'id' => $data['id'],
					'status' => 2
				];

				OrderModel::update($order);
				return $this->success('添加成功！');
			}else{
				return $this->error('添加失败');
			}
		}
	}

	/**
	 * 公有云充值
	 */
	public function editCloud()
	{
		if (request()->isPost()) {
			$data = [
				'id' => $this->request->param('id', '', 'trim'),
				'goodsId' => $this->request->param('goodsId', '', 'trim'),
				'usd' => $this->request->param('usd', '', 'trim')
			];

			//更新状态
			$update = [
				'id' => $data['goodsId'],
				'usd' => $data['usd']
			];
			$result = UserAccount::update($update);
			if ($result == true) {
				$order = [
					'id' => $data['id'],
					'status' => 2
				];
				OrderModel::update($order);
				return $this->success('充值成功！');
			}else{
				return $this->error('充值失败！');
			}
		}
	}

	/**
	 * 删除
	 */
	public function del($id)
    {
        if (request()->isPost()) {
            $data = input('param.');
            if (empty($id)) {
                $ids = explode(',', $data['ids']);
            } else {
                $ids = $id;
            }
            $result = OrderModel::destroy($ids);
            if ($result == true) {
                return $this->success('删除成功');
            } else {
                return $this->error('删除失败');
            }
        }
    }
}