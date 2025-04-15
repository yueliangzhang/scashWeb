<?php
/**
 * 退款
 */
namespace app\member\controller;
use app\member\MemberBase;
use app\common\model\Refund as RefundModel;
use app\common\model\Order;

class Refund extends MemberBase
{
	//展示
	public function index()
	{
		return view();
	}

	//数据
	public function datalist($limit = 15)
	{
		$list = RefundModel::where('userid', session('user_info.id'))->order('id', 'desc')->paginate($limit);
		foreach ($list as $key => $value) {
			$list[$key]['addtime'] = date('Y-m-d H:i:s', $value['addtime']);
			$list[$key]['endtime'] = $value['endtime']?date('Y-m-d H:i:s', $value['endtime']):'未确认';
		}

		return $this->result($list);
	}

	//搜索
	public function search($limit = 15)
	{
		if (request()->isGet()) {
			$data = input('param.');
			$search = new RefundModel();
			$search = $search->where('userid', session('user_info.id'));
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
			}

			return $this->result($list);
		}
	}

	//取消退款
	public function cancel($id)
	{
		if (request()->isPost()) {
			$refund = RefundModel::find($id);
			if (empty($refund)) {
				return $this->error('数据不存在');
			}

			if ($refund['status'] != 0) {
				return $this->error('只有在待审核才能取消');
			}

			$data = [
				'id' => $id,
				'status' => 2,
				'endtime' => time()
			];
			$result = RefundModel::update($data);
			if ($result == true) {
				Order::where('trade_no', $refund['trade_no'])->update(['status' => 1]);
				return $this->success('取消成功');
			}else{
				return $this->error('取消失败');
			}
		}
	}


}