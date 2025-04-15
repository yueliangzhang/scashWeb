<?php
/**
 * 伪原创
 */
namespace app\common\controller;
use app\BaseController;

class Original extends BaseController
{
	// 文章内容
    protected $source = "";
	//关键词
	protected $keyword = "";

	/**
	 * 初始化
	 * @param source 文章内容
	 * @param keyword 关键词
	 */
	public function __construct($source = '', $keyword = '')
	{
		$this->source = strip_tags($source, '<p>');
		$this->keyword = explode(',', $keyword);//分割关键词
	}

	/**
	 * 伪原创生成
	 */
	public function Content()
    {
        if (!$this->source) return false;
		
		$text = str_replace(array("\r\n", "\n", "\r"), '[h]', $this->source);
		$text = $this->keyword_lock($text);//关键词锁定
		$text = $this->keyword_unlock($this->wyc($text));//关键词解锁
		
		return str_replace('[h]', PHP_EOL, $text);
	}

	//关键词锁定
	public function keyword_lock($content = '')
	{
		foreach ($this->keyword as $id => $age) {
			$content =& $content;
			$content = str_replace($age, "[k$id]", $content);
		}

		return $content;
	}
	
	//关键词解锁
	public function keyword_unlock($content = '')
	{
		foreach ($this->keyword as $id => $age) {
			$content =& $content;
			$content = str_ireplace("[k$id]", $age, $content);
		}
		
		return $content;
		
	}

	//内容切分
	public function mbStrSplit($string = '', $len = 1) 
	{
		$start = 0;
		$strlen = mb_strlen($string);
		while ($strlen) {
			$array[] = mb_substr($string, $start, $len, "utf8");
			$string = mb_substr($string, $len, $strlen, "utf8");
			$strlen = mb_strlen($string);
		}

		return $array;
	}

	/**
	 * 伪原创
	 * @param info 锁定关键词的内容
	 */
	public function wyc($info = '')
	{
		//内容字符转utf-8
		$infocount = mb_strlen($info, 'UTF-8');
		$wyc = '';
		//1000以内可用
		if($infocount <= 990){
			$zh_en = $this->translate($info, 'zh-CN', 'EN');//中文转英文
			$wyc = $this->translate($zh_en, 'EN', 'zh-CN');//英文转中文
		}else{
			//如果大于于1000，每1000字符进行分割循环翻译
			$info = $this->mbStrSplit($info, 800);
			$arr = count($info);

			for($i = 0; $i < $arr; $i++){
				$zh_en = $this->translate($info[$i], 'zh-CN', 'EN');
				$wyc .= $this->translate($zh_en, 'EN', 'zh-CN');
			}
		}

		return $wyc;
	}

	/**
	 * 翻译请求
	 * @param text 内容
	 * @param from 语言
	 * @param to 语言
	 */
	public function translate($text = '', $from = '', $to = '')
	{
		$url = "https://translate.google.com/translate_a/single?client=gtx&dt=t&ie=UTF-8&oe=UTF-8&sl=".$from."&tl=".$to."&q=".urlencode($text);
		set_time_limit(0);
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows NT 6.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/72.0.3626.81 Safari/537.36 SE 2.X MetaSr 1.0");
		curl_setopt($ch, CURLOPT_HEADER, false);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
		curl_setopt($ch, CURLOPT_MAXREDIRS,20);
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 40);
		curl_setopt($ch, CURLOPT_URL, $url);
		$result = curl_exec($ch);
		curl_close($ch);
		$result = json_decode($result);
		if(!empty($result)){
			foreach($result[0] as $k){
				$v[] = $k[0];
			}
			return implode(" ", $v);
		}
	}
}