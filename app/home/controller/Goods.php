<?php
/**
 * 独服详情
 */
namespace app\home\controller;
use app\home\HomeBase;
//加载数据模型
use app\common\model\Goods as GoodsModel;
use app\common\model\Category;
use app\common\model\Order;
use app\common\model\User;
// 加载支付宝支付
use app\common\controller\Alipay;
use app\common\controller\Bit2go;
class Goods extends HomeBase
{
	/**
	 * 独服详细
	 */
	public function stand()
	{
		$name = $this->request->param('name', '', 'trim');
		$name = strtolower($name);
		$stand = GoodsModel::where('alias', $name)->find();
		$cate = Category::where('id', $stand['cid'])->find();
		$parentCate = Category::find($cate['pid']);
		$cate['parentname'] = $parentCate['name'];

		//商品推荐
		$recommend = GoodsModel::where('cid', $stand['cid'])->where('id', 'not in', $stand['id'])->order('id', 'desc')->limit(6)->select();
		if (!empty($stand)) {
			return view('', [
				'info' => $stand,
				'cate' => $cate,
				'recommend' => $recommend
			]);
		}else{
			$this->error('页面不存在！');
		}
	}

	/**
	 * 购买下订单
	 * @param id 商品ID
	 * @param email 邮箱地址
	 */
	public function buying()
	{
		if (request()->isPost()) {
			$data = [
				'id' => $this->request->param('id', '', 'trim'),
				'num' => $this->request->param('num', '', 'trim'),
				'invoice' => $this->request->param('invoice', '', 'trim'),
				'paytype' => $this->request->param('paytype', '', 'trim')
			];

			//选择USDT就不能开发票
			if ($data['paytype'] == 1 && $data['invoice'] == 1) {
				$this->error('选择USDT支付不能开票！');
			}
			//判断是否登录
			if (!$this->isLogin()) {
				$this->error('登录超时，请重新登录', config('app.hostname').'/member');
			}
			$stand = GoodsModel::find($data['id']);
			//判断是否有量
			if ($stand['num'] == 0) {
				$this->error('产品已经售完！请过段时间再购买');
			}
			//获取用户信息
			$user = session('user_info');
			$userinfo = User::find($user['id']);
			//发票费率 tax
			$setconfig = \app\mxadmin\model\Config::getConfigData('setconfig');
			$usd = $data['num']/$setconfig['rate'];
			//生成订单号
			$trade_no = 'ST'.date('YmdHis').rand(1000,9999);
			$order = [
				'userid' => $userinfo['id'],
				'title' => $stand['title'],
				'type' => 0,
				'goodsId' => $data['id'],
				'trade_no' => $trade_no,
				'usd' => intval($usd),
				'num' => intval($data['num']),
				'addtime' => time()
			];
			//发票金额
			if ($data['invoice'] == 1) {
				$amount = $data['num'] * (1 + $setconfig['tax']);
				$amount = intval($amount);
				$order['invoice'] = $amount;
				//判断发票抬头
				if (empty($userinfo['company']) && empty($userinfo['idcode'])) {
					$this->error('发票信息未填写，请完善用户信息！', config('app.hostname').'/member');
				}

				$order['invoiceTitle'] = '企业名：'.$userinfo['company'].'<br>企业编码：'.$userinfo['idcode'];
			}else{
				$amount = intval($data['num']);
			}

			$order['remarks'] = '手机号码：'.$userinfo['mobile'].'<br>微信账户：'.$userinfo['wechat'];
			//创建订单
			$result = Order::create($order);
			if ($result == true) {
				//判断支付方式
				switch ($data['paytype']) {
					case 0://支付宝
						//提交支付订单
						$alipaydata = [
							'trade_no' => $order['trade_no'],
							'amount' => $amount,
							'title' => $order['title']
						];
						$alipay = new Alipay();
						$alipay->pay($alipaydata);
						break;
					case 1://提交USDT
						$this->payBit($order);
						break;
				}
			}else{
				$this->error('下单失败！');
			}
		}
	}

	/**
	 * 提交USDT支付
	 * @param order 订单详情
	 */
	private function payBit($order = [])
	{
		$order = [
			'amount' => $order['usd'],
			'order_id' => $order['trade_no']
		];

		$bit = new Bit2go();
		$result = $bit->rechargeUsd($order);
		if ($result['code'] == 200) {
			$data = $result['result'];
			//打开收银台
			$this->success('提交成功，等待打开付款页面', $data['payurl']);
		}
	}
}