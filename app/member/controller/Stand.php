<?php
/**
 * 服务器管理
 */
namespace app\member\controller;
use app\member\MemberBase;
use app\common\model\Stand as StandModel;
use app\common\model\User;
use app\common\model\Order;
// 加载支付宝支付
use app\common\controller\Alipay;

class Stand extends MemberBase
{
	//展示
	public function index()
	{
		return view();
	}

	//数据
	public function datalist($limit = 15)
	{
		$uid = session('user_info.id');
		$list = StandModel::where('uid', $uid)->order('id', 'desc')->paginate($limit);
		$time = time();
		foreach ($list as $key => $value) {
			// 3天内到期的
			if ($value['endtime'] <= ($time + 3*24*3600)) {
				$endtime = '<font color="red">'.date('Y-m-d', $value['endtime']).'</font>';
			}else if ($value['endtime'] <= ($time + 7*24*3600)) {
				$endtime = '<font color="blue">'.date('Y-m-d', $value['endtime']).'</font>';
			}else{
				$endtime = date('Y-m-d', $value['endtime']);
			}
			$list[$key]['content'] = nl2br($value['content']);
			$list[$key]['time'] = date('Y-m-d', $value['addtime']).'<br>'.$endtime;
			$list[$key]['endtime'] = date('Y-m-d', $value['endtime']);
		}

		return $this->result($list);
	}

	//搜索
	public function search($limit = 15)
	{
		if (request()->isGet()) {
			$data = input('param.');
			$uid = session('user_info.id');
			$search = new StandModel();
			$search = $search->where('uid', $uid);
			if ($data['ip'] != '') {
				$search = $search->where('content', 'like', '%'.$data['ip'].'%');
			}

			if ($data['end_time'] > 0) {
				$search = $search->whereTime('endtime', '<', $data['end_time'].' 23:59:59');
			}

			$time = time();
			$list = $search->order('endtime', 'asc')->paginate($limit);
			foreach ($list as $key => $value) {
				// 3天内到期的
				if ($value['endtime'] <= ($time + 3*24*3600)) {
					$endtime = '<font color="red">'.date('Y-m-d', $value['endtime']).'</font>';
				}else if ($value['endtime'] <= ($time + 7*24*3600)) {
					$endtime = '<font color="blue">'.date('Y-m-d', $value['endtime']).'</font>';
				}else{
					$endtime = date('Y-m-d', $value['endtime']);
				}
				$list[$key]['content'] = nl2br($value['content']);
				$list[$key]['time'] = date('Y-m-d', $value['addtime']).'<br>'.$endtime;
				$list[$key]['endtime'] = date('Y-m-d', $value['endtime']);
			}

			return $this->result($list);
		}
	}

	//服务器续期
	public function xudata()
	{
		if (request()->isPost()) {
			$data = [
				'id' => $this->request->param('id', '', 'trim'),
				'tax' => $this->request->param('tax', '', 'trim'),
				'seller' => $this->request->param('seller', '', 'trim'),
				'num' => $this->request->param('num', '', 'trim'),
				'istax' => $this->request->param('istax', '', 'trim')
			];

			$uid = session('user_info.id');
			$money = 0;
			$invoice = '';
			//是否开票
			if ($data['istax'] == 1) {
				//判断是否填写开票信息
				$user = User::find($uid);
				if (empty($user['company']) || empty($user['idcode']) || is_null($user['company']) || is_null($user['idcode'])) {
					return $this->error('请到个人中心->个人信息完善开票信息！');
				}

				$money = $data['seller'] * $data['num'] * (1 + $data['tax']);
				$invoice = '公司名：'.$user['company'].'<br>税号：'.$user['idcode'];
			}else{
				$money = $data['seller'] * $data['num'];
			}

			//入订单表
			$order = [
				'userid' => $uid,
				'title' => '服务器续期：'.$data['num'].'个月',
				'type' => 2,
				'goodsId' => $data['id'],
				'trade_no' => 'RE'.date('YmdHis').rand(1000,9999),
				'num' => $money,
				'invoice' => $data['seller'] * $data['num'],
				'invoiceTitle' => $invoice,
				'addtime' => time()
			];

			$result = Order::create($order);
			if ($result == true) {
				return $this->success('下单成功，跳转支付!', '/member/stand/pay?id='.$result->id);
			}else{
				return $this->error('下单失败');
			}
		}
	}

	//支付跳转
	public function pay($id)
	{
		$order = Order::find($id);
		$data = [
			'trade_no' => $order['trade_no'],
			'amount' => $order['num'],
			'title' => $order['title']
		];

		$alipay = new Alipay();
		$alipay->pay($data);
	}
}