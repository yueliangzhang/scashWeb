<?php
/**
 * 退款申诉
 */
namespace app\member\controller;
use app\member\MemberBase;
use app\common\model\Appeal as AppealModel;
use app\common\model\UserAccount;

class Appeal extends MemberBase
{
	//展示
	public function index()
	{
		return view();
	}

	//数据
	public function datalist($limit = 15)
	{
		$list = AppealModel::order('id', 'desc')->paginate($limit);
		foreach ($list as $key => $value) {
			$list[$key]['addtime'] = date('Y-m-d H:i:s', $value['addtime']);
			$list[$key]['endtime'] = $value['endtime']?date('Y-m-d H:i:s', $value['endtime']):'未确认';
			$account = UserAccount::find($value['cid']);
			$list[$key]['email'] = $account['email'];
		}

		return $this->result($list);
	}
}