<?php
/**
 * 支付回调
 */
namespace app\common\controller;
use app\common\controller\Alipay;
use app\common\model\Order;
use app\common\model\Refund;
use app\common\controller\Bit2go;
use app\BaseController;

class Pay extends BaseController
{
	/**
	 * 支付宝同步回调
	 */
	public function return()
	{
      	$data = input('param.');
      	$pay = new Alipay();
		//$orderno = input('param.orderno');
		//print_r($data);exit;
		//判断订单是否支付
		$order = Order::where('trade_no', $data['out_trade_no'])->find();
		//判断订单是否存在
		if (empty($order)) {
			return $this->error('订单号不存在！', url('/member'));
		}
		//判断状态
		if ($order['status'] == 1) {
			return $this->error('已支付，不用处理！', url('/member'));
		}

		// 判断回调数据
		$result = $pay->returnUrl();
		//判断订单号是否一致
		if ($result == true) {
			//更新数据库
			$order->id = $order['id'];
			$order->status = 1;
			$order->endtime = time();
			$status = $order->save();
			if ($status) {
				return $this->success("付款成功！等待开通账户", url('/member'));
			}else{
				return $this->error('付款失败！');
			}
		}else{
			return $this->error('订单匹配失败！');
		}
	}

	/**
	 * 支付宝异步回调
	 */
	public function notify()
	{
		$data = input('param.');
		file_put_contents("./notify/log_".date('Ymd').".log", '时间：'.date('Y-m-d H:i:s').'  回调参数：'.json_encode($data).PHP_EOL,FILE_APPEND);
		//$orderno = input('param.orderno');
		//判断订单是否支付
		$order = Order::where('trade_no', $data['out_trade_no'])->find();
		//判断订单是否存在
		if (empty($order)) {
			echo 'error';exit;
		}
		//判断状态
		if ($order['status'] == 1) {
			echo 'success';exit;
		}

		// 判断回调数据
		//$result = $this->pay->notifyUrl();
		//判断订单号是否一致
		if ($data['trade_status'] == 'TRADE_SUCCESS') {
			//更新数据库
			$order->id = $order['id'];
			$order->status = 1;
			$order->endtime = time();
			$status = $order->save();
			if ($status == true) {
				echo "success";exit;
			}else{
				echo 'error';exit;
			}
		}else{
			echo 'error';exit;
		}
	}

	/**
	 * bit2go付款回调
	 */
	public function bit2goPaymentNotify()
	{
		$postdata = file_get_contents('php://input');
		file_put_contents("./notify/logusdt_".date('Ymd').".log", '时间：'.date('Y-m-d H:i:s').'  回调参数：'.$postdata.PHP_EOL,FILE_APPEND);
		$data = json_decode($postdata, true);
		$setconfig = \app\mxadmin\model\Config::getConfigData('setconfig');
		$order = Order::where('trade_no', $data['order_id'])->find();
		//判断状态
		if ($order['status'] != 0) {
			$this->error('回调状态不对');
		}
		//订单支付状态
		if ($data['status'] == 'paid' || $data['status'] == 'paid_over' || $data['status'] == 'partial_payment') {
			//更新订单状态
			$orderdata = [
				'id' => $order['id'],
				'usd' => $data['pay_amount'],
				'num' => number_format($data['pay_amount'] * $setconfig['rate'], 2),
				'status' => 1,
				'paytime' => time()
			];

			$result = Order::update($orderdata);
			if ($result == true) {
				$this->success('回调成功');
			}else{
				$this->error('回调失败');
			}
		}
	}

	/**
	 * bit2go退款回调
	 */
	public function bit2goPayoutNotify()
	{
		$postdata = file_get_contents('php://input');
		file_put_contents("./notify/logusdtrefund_".date('Ymd').".log", '时间：'.date('Y-m-d H:i:s').'  回调参数：'.$postdata.PHP_EOL,FILE_APPEND);
		$data = json_decode($postdata, true);
		$trade_no = explode('_', $data['order_id']);
		$refund = Refund::where(['trade_no', $trade_no[1], 'status' => 0])->find();
		if ($refund['status'] != 0) {
			$this->error('回调状态不对');
		}

		if ($data['status'] == 'paid') {
			//更新退款订单
			$refund_data = [
				'id' => $refund['id'],
				'status' => 1,
				'endtime' => time()
			];

			$result = Refund::update($refund_data);
			if ($result == true) {
				$order = Order::where('trade_no', $trade_no[1])->find();
				Order::update(['id' => $order['id'], 'status' => 4]);
				$this->success('回调成功');
			}else{
				$this->error('回调失败');
			}
		}
	}
}