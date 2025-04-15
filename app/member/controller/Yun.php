<?php
/**
 * 云管理
 */
namespace app\member\controller;
use app\member\MemberBase;
use app\common\model\UserAccount;
use app\common\model\Order;
use think\exception\ValidateException;
// 加载支付宝支付
use app\common\controller\Alipay;

class Yun extends MemberBase
{
	/**
	 * 展示
	 */
	public function index()
	{
		return view();
	}

	/**
	 * 数据
	 */
	public function datalist($limit = 15)
	{
		$id = session('user_info.id');
		$list = UserAccount::where('userid', $id)->order('id', 'desc')->paginate($limit);
		foreach ($list as $key => $value) {
			$list[$key]['info'] = nl2br($value['info']);
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
			$id = session('user_info.id');
			$search = new UserAccount();
			if ($data['uid'] != '') {
				$search = $search->where('uid', $data['uid']);
			}

			if ($data['email'] != '') {
				$search = $search->where('email', $data['email']);
			}

			if ($data['remarks'] != '') {
				$search = $search->where('remarks', 'like', '%'.$data['remarks'].'%');
			}

			if ($data['type'] > 0) {
				$search = $search->where('type', $data['type'] - 1);
			}

			$list = $search->where('userid', $id)->order('id', 'desc')->paginate($limit);
			foreach ($list as $key => $value) {
				$list[$key]['info'] = nl2br($value['info']);
			}
			return $this->result($list);
		}
	}

	/**
	 * 数据导出
	 */
	public function excel()
	{
		if (request()->isGet()) {
			$data = input('param.');
			$id = session('user_info.id');
			$search = new UserAccount();
			if ($data['uid'] != '') {
				$search = $search->where('uid', $data['uid']);
			}

			if ($data['email'] != '') {
				$search = $search->where('email', $data['email']);
			}

			if ($data['remarks'] != '') {
				$search = $search->where('remarks', 'like', '%'.$data['remarks'].'%');
			}

			if ($data['type'] > 0) {
				$search = $search->where('type', $data['type'] - 1);
			}

			$datalist = [];
			$list = $search->where('userid', $id)->order('id', 'desc')->select();
			foreach ($list as $key => $value) {
				$type = '';
				switch ($value['typ']) {
					case 0:
						$type = '阿里云国际';
						break;
					case 1:
						$type = '腾讯云国际';
						break;
					case 2:
						$type = '华为云国际';
						break;
					case 3:
						$type = 'AWS亚马逊';
						break;
					case 4:
						$type = 'GCP谷歌云';
						break;
					case 4:
						$type = 'Azure微软云';
						break;
				}
				$datalist[$key] = [
					'uid' => $value['uid'],
					'type' => $type,
					'email' => $value['email'],
					'usd' => $value['usd'],
					'info' => $value['info'],
					'status' => $value['status']?'启用':'禁用',
					'addtime' => date('Y-m-d H:i:s', $value['addtime'])
				];
			}

			return $this->result($datalist);
		}
	}

	/**
	 * 账户充值
	 */
	public function recharge()
	{
		if (request()->isPost()) {
			$data = [
				'id' => $this->request->param('id', '', 'trim'),
				'rate' => $this->request->param('rate', '', 'trim'),
				'type' => $this->request->param('type', '', 'trim'),
				'num' => $this->request->param('num', '', 'trim'),
				'tax' => $this->request->param('tax', '', 'trim'),
				'istax' => $this->request->param('istax', '', 'trim')
			];

			$id = session('user_info.id');
			$cloud = UserAccount::find($data['id']);
			$title = "";
			//根据类型判断最低充值金额
			switch ($data['type']) {
				case 0://阿里云
					if ($data['num'] < 50) {
						return $this->error('阿里云国际最低50美金起充值！');
					}
					$title = "充值阿里云国际账户UID：".$cloud['uid'];
					break;
				case 1://腾讯云
					if ($data['num'] < 50) {
						return $this->error('腾讯云国际最低50美金起充值！');
					}
					$title = "充值腾讯云国际账户UID：".$cloud['uid'];
					break;
				case 2://华为云
					if ($data['num'] < 200) {
						return $this->error('华为云国际最低200美金起充值！');
					}
					$title = "充值华为云国际账户UID：".$cloud['uid'];
					break;
				case 3://aws
					if ($data['num'] < 500) {
						return $this->error('aws最低500美金起充值！');
					}
					$title = "充值AWS账户UID：".$cloud['uid'];
					break;
				case 4://gcp
					if ($data['num'] < 500) {
						return $this->error('gcp最低500美金起充值！');
					}
					$title = "充值GCP账户UID：".$cloud['uid'];
					break;
				case 5://azure
					if ($data['num'] < 500) {
						return $this->error('azure最低500美金起充值！');
					}
					$title = "充值Azure账户UID：".$cloud['uid'];
					break;
			}

			$yuan = $data['num'] * $data['rate'];
			$money = 0;
			$invoice = '';
			//是否开票
			if ($data['istax'] == 1) {
				//判断是否填写开票信息
				$user = User::find($id);
				if (empty($user['company']) || empty($user['idcode']) || is_null($user['company']) || is_null($user['idcode'])) {
					return $this->error('请到个人中心->个人信息完善开票信息！');
				}

				$money = $yuan * (1 + $data['tax']);
				$money = intval($money);
				$invoice = '公司名：'.$user['company'].'<br>税号：'.$user['idcode'];
			}else{
				$money = intval($yuan);
			}

			//入订单表
			$order = [
				'userid' => $id,
				'title' => $title,
				'type' => 3,
				'goodsId' => $data['id'],
				'trade_no' => 'RE'.date('YmdHis').rand(1000,9999),
				'num' => $money,
				'invoice' => $yuan,
				'invoiceTitle' => $invoice,
				'addtime' => time()
			];

			$result = Order::create($order);
			if ($result == true) {
				return $this->success('下单成功，跳转支付!', '/member/yun/pay?id='.$result->id);
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