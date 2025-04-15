<?php
/**
 * 标签
 */
namespace app\mxadmin\controller;
use app\mxadmin\AdminBase;
use app\common\model\Tags as TagsModel;
use app\common\model\PageMap;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use think\exception\ValidateException;

class Tags extends AdminBase
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
		$list = TagsModel::order('id', 'desc')->paginate($limit);
		foreach ($list as $key => $value) {
			$list[$key]['addtime'] = date('Y-m-d H:i:s', $value['addtime']);
			//有多少数据关联标签ID
			$num = PageMap::where('tag_id', $value['id'])->count();
			$list[$key]['num'] = $num;
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
			$search = new TagsModel();
			if ($data['keywords'] != '') {
				$search = $search->where('name', 'like', '%'.$data['keywords'].'%');
			}

			if ($data['status'] > 0) {
				$search = $search->where('status', $data['status'] - 1);
			}

			$list = $search->order('id', 'desc')->paginate($limit);
			foreach ($list as $key => $value) {
				$list[$key]['addtime'] = date('Y-m-d H:i:s', $value['addtime']);
				//有多少数据关联标签ID
				$num = PageMap::where('tag_id', $value['id'])->count();
				$list[$key]['num'] = $num;
			}

			return $this->result($list);
		}
	}

	//天际
	public function add()
	{
		if (request()->isPost()) {
			$data = [
				'name' => $this->request->param('name', '', 'trim'),
				'description' => $this->request->param('description', '', 'trim'),
				'goods_id' => $this->request->param('goods_id', '', 'trim'),
				'is_goods' => $this->request->param('is_goods', '', 'trim')
			];

			//判断标签是否存在
			$isone = TagsModel::where('name', $data['name'])->find();
			if (!empty($isone)) {
				return $this->error('标签已存在');
			}

			$result = TagsModel::create($data);
			if ($result == true) {
				//添加数据入pagemap
				$pagedata = [
					'tag_id' => $result->id,
					'page_id' => $data['goods_id'],
					'is_article' => $data['is_goods'],
					'addtime' => time()
				];
				PageMap::create($pagedata);
				return $this->success('添加成功！');
			}else{
				return $this->error('添加失败');
			}
		}
	}

	//修改标签
	public function edit($id)
	{
		if (request()->isPost()) {
			$data = [
				'id' => $this->request->param('id', '', 'trim'),
				'name' => $this->request->param('name', '', 'trim'),
				'description' => $this->request->param('description', '', 'trim'),
				'is_goods' => $this->request->param('is_goods', '', 'trim')
			];

			$result = TagsModel::update($data);
			if ($result == true) {
				return $this->success('修改成功！');
			}else{
				return $this->error('修改失败');
			}
		}
	}

	//excel批量导入代付
	public function addexcel()
	{
		$file = $_FILES['file']['tmp_name'];
		try {
			$file = iconv('utf-8', 'gb2312', $file);
			if (empty($file) OR !file_exists($file)) {
				throw new \Exception('文件不存在!');
			}

			$objReader = IOFactory::createReader('Xlsx');
            if (!$objReader->canRead($file)) {
                $objReader = IOFactory::createReader('Xls');
                if (!$objReader->canRead($file)) {
                    throw new \Exception('只支持导入Excel文件！');
                }
            }
            $objReader->setReadDataOnly(true);//忽略任何格式的信息
            $objPHPExcel = $objReader->load($file);
            $worksheet = $objPHPExcel->getActiveSheet();
            $sheet = $objPHPExcel->getSheet(0);//excel中的第一张sheet
            $highestRow = $sheet->getHighestRow();//取得总行数
            //判断数据是否大于500
            if ($highestRow > 20000) {
                return $this->error('导入数据不能超过20000');
            }

            //标签数据处理
            for ($j = 2; $j <= $highestRow; $j++) { 
            	$name = $worksheet->getCellByColumnAndRow(1, $j)->getValue();//标签名
            	$description = $worksheet->getCellByColumnAndRow(2, $j)->getValue();//标签介绍
            	$goods_id = $worksheet->getCellByColumnAndRow(3, $j)->getValue();//产品ID
            	$is_goods = $worksheet->getCellByColumnAndRow(3, $j)->getValue();//产品类型 0、文章；1、服务器；2、公有云
            	//判断数据是否存在
            	if (!empty($name)) {
            		$isdata = TagsModel::where('name', $name)->find();
	            	if (empty($isdata)) {
	            		$insert = [
	            			'name' => $name,
	            			'description' => $description,
	            			'goods_id' => $goods_id,
	            			'is_goods' => $is_goods
	            		];

	            		$dataId = (new TagsModel())->insertGetId($insert);
	            		if ($dataId == true) {
	            			//添加数据入pagemap
							$pagedata = [
								'tag_id' => $dataId,
								'page_id' => $goods_id,
								'is_article' => $is_goods,
								'addtime' => time()
							];
							PageMap::create($pagedata);
	            		}
	            		//postBaiduPage('/tech/tags.html?id='.$data);
	            	}
            	}
            }

            return $this->success('提交成功！');
		} catch (Exception $e) {
			return $this->error($e->getMessage());
		}
	}

	/**
     * 删除代理
     * @param $id
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
            $result = TagsModel::destroy($ids);
            if ($result == true) {
            	//删除所有的关联标签
            	$allid = PageMap::where('tag_id', 'in', $ids)->column('id');
            	$nowids = implode($allid, ',');
            	PageMap::destroy($nowids);
                return $this->success('删除成功');
            } else {
                return $this->error('删除失败');
            }
        }
    }

    /**
     * 是否专题
     */
    public function edit_state_same($id)
    {
    	if (request()->isPost()) {
            $data = input('param.');
            
        	$result = TagsModel::update(['status' => $data['status']], ['id' => $id]);
            if ($result == true) {
                return $this->success($data['status'] ? '已推荐' : '不推荐');
            } else {
                return $this->error('状态修改失败');
            }
        }
    }
}