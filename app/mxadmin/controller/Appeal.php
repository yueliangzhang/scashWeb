<?php
/**
 * 退款申诉
 */
namespace app\mxadmin\controller;
use app\mxadmin\AdminBase;
use app\common\model\Refund;
use app\common\model\User;
use app\common\model\UserAccount;
use app\common\model\Order;
use app\common\controller\Alipay;
use app\common\controller\Bit2go;

class Appeal extends AdminBase
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
		$list = Refund::order('id', 'desc')->paginate($limit);
		foreach ($list as $key => $value) {
			$list[$key]['addtime'] = date('Y-m-d H:i:s', $value['addtime']);
			$list[$key]['endtime'] = $value['endtime']?date('Y-m-d H:i:s', $value['endtime']):'未确认';
			$user = User::find($value['userid']);
			$list[$key]['email'] = $user['email'];
		}

		return $this->result($list);
	}

	//搜索
	public function search($limit = 15)
	{
		if (request()->isGet()) {
			$data = input('param.');
			$search = new Refund();
			if ($data['email'] != '') {
				$user = User::where('email', $data['email'])->find();
				$search = $search->where('userid', $user['id']);
			}

			if ($data['trade_no'] != '') {
				$search = $search->where('trade_no', $data['trade_no']);
			}

			if ($data['status'] != '') {
				$search = $search->where('status', $data['status'] - 1);
			}

			$list = $search->order('id', 'desc')->paginate($limit);
			foreach ($list as $key => $value) {
				$list[$key]['addtime'] = date('Y-m-d H:i:s', $value['addtime']);
				$list[$key]['endtime'] = $value['endtime']?date('Y-m-d H:i:s', $value['endtime']):'未确认';
				$user = User::find($value['userid']);
				$list[$key]['email'] = $user['email'];
			}

			return $this->result($list);
		}
	}

	// 删除订单
	public function del($id)
    {
        if (request()->isPost()) {
            $data = input('param.');
            if (empty($id)) {
                $ids = explode(',', $data['ids']);
            } else {
                $ids = $id;
            }
            $result = Refund::destroy($ids);
            if ($result == true) {
                return $this->success('删除成功');
            } else {
                return $this->error('删除失败');
            }
        }
    }

    // 审核退款
    public function edit()
    {
    	if (request()->isPost()) {
    		$data = [
    			'id' => $this->request->param('id', '', 'trim'),
    			'reson' => $this->request->param('reson', '', 'trim'),
    			'status' => $this->request->param('status', '', 'trim')
    		];

    		$appeal = Refund::find($data['id']);
    		if ($data['status'] == 2 && empty($data['reason'])) {
    			return $this->error('失败请输入失败原因');
    		}

    		$data['endtime'] = time();
    		if ($data['status'] == 1) { // 同意退款
				//退款方式
				switch ($appeal['type']) {
					case 0://支付宝退款
						$this->refundAlipay($appeal);
						break;
					case 1://USDT
						$this->refundUsd($appeal);
						break;
				}
				
				return $this->success('提交成功！');
			}else{
				$result = Refund::update($data);
				$order = Order::where('trade_no', $appeal['trade_no'])->find();
				Order::update(['id' => $order['id'], 'status' => 1]);
				return $this->success('失败处理成功');
			}
    	}
    }

    /**
     * 支付宝退款
     * @param data 退款单子
     */
    private function refundAlipay($data = [])
    {
    	$orderdata = [
    		'out_trade_no' => $data['trade_no'],
    		'refund_amount' => $data['num']
    	];

    	$alipay = new Alipay();
    	$result = $alipay->refund($orderdata);
    	//数据状态

    	Refund::update(['id' => $data['id'], 'status' => 1, 'endtime' => time()]);
    	//修改订单状态
    	$order = Order::where('trade_no', $data['trade_no'])->find();
    	$order = [
    		'id' => $order['id'],
    		'status' => 4
    	];
    	Order::update($order);

    	return true;
    }

    /**
     * USDT退款
     * @param data 退款单子
     */
    public function refundUsd($data = [])
    {
    	$orderdata = [
    		'amount' => $data['usd'],
    		'order_id' => $data['trade_no'].'_'.rand(100,999),
    		'address' => $data['address']
    	];

    	$bit = new Bit2go();
    	$result = $bit->refund($orderdata);

    	return true;
    }
}