<?php
/**
 * 我的订单
 */
namespace app\member\controller;
use app\member\MemberBase;
use app\common\model\Order as OrderModel;
use app\common\model\UserAccount;
use app\common\model\Refund;

class Order extends MemberBase
{
	//展示
	public function index()
	{
		return view();
	}

	//数据
	public function datalist($limit = 15)
	{
		$list = OrderModel::where('userid', session('user_info.id'))->order('id', 'desc')->paginate($limit);
		foreach ($list as $key => $value) {
			if ($value['invoice'] == 0) {
				$list[$key]['invoice'] = '不开票';
			}
			$list[$key]['addtime'] = date('Y-m-d H:i:s', $value['addtime']);
			$list[$key]['paytime'] = $value['paytime']?date('Y-m-d H:i:s', $value['paytime']):'未付款';
		}

		return $this->result($list);
	}

	//搜索
	public function search($limit = 15)
	{
		if (request()->isGet()) {
			$data = input('param.');
			$search = new OrderModel();
			if ($data['trade_no'] != '') {
				$search = $search->where('trade_no', $data['trade_no']);
			}

			if ($data['status'] > 0) {
				$search = $search->where('status', $data['status'] - 1);
			}

			$list = $search->where('userid', session('user_info.id'))->order('id','desc')->paginate($limit);
            foreach ($list as $key => $value) {
				$list[$key]['addtime'] = date('Y-m-d H:i:s', $value['addtime']);
				$list[$key]['endtime'] = $value['endtime']?date('Y-m-d H:i:s', $value['endtime']):'未确认';
				$account = UserAccount::find($value['cid']);
				$list[$key]['email'] = $account['email'];
			}

			return $this->result($list);
		}
	}

	//退款
	public function refund($id)
	{
		if (request()->isPost()) {
			$order = OrderModel::find($id);
			if (empty($order)) {
				return $this->error('数据不存在！');
			}
			$time = time();
			if ($time > ($order['paytime'] + 12*3600)) {
				return $this->error('付款后12小时内不能退款！');
			}

			if ($order['paytype'] == 0) {//支付宝退款
				if ($order['invoice'] > 0) {
					$money = $order['invoice'];
				}else{
					$money = $order['num'];
				}
				
				$refund = [
					'userid' => $order['userid'],
					'trade_no' => $order['trade_no'],
					'type' => $order['paytype'],
					'num' => $money,
					'usd' => $order['usd'],
					'addtime' => time()
				];
			} elseif ($order['paytype'] == 1) { //USDT退款
				$addr = $this->request->param('address', '', 'trim');
				$refund = [
					'userid' => $order['userid'],
					'trade_no' => $order['trade_no'],
					'type' => $order['paytype'],
					'address' => $addr,
					'num' => $order['num'],
					'usd' => $order['usd'],
					'addtime' => time()
				];
			}

			//判断订单号是否存在
			$isone = Refund::where(['trade_no' => $order['trade_no'], 'status' => 1])->find();
			if (!empty($isone)) {
				return $this->error('退款已存在！');
			}
			$result = Refund::create($refund);
			if ($result == true) {
				//更新退款数据
				$data = [
					'id' => $id,
					'status' => 3
				];

				OrderModel::update($data);
				return $this->success('退款申请成功！');
			}else{
				return $this->error('退款申请失败');
			}
		}
	}
}