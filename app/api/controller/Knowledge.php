<?php
/**
 * @copyright Copyright (c) 2022 勾股工作室
 * @license https://opensource.org/licenses/GPL-3.0
 * @link https://www.gougucms.com
 */
declare (strict_types = 1);
namespace app\api\controller;

use app\api\BaseController;
use Mpdf\Mpdf;
use think\facade\Db;
use think\facade\View;

class Knowledge extends BaseController
{	
    public function pdf()
    {
        $param = get_params();
		$detail = Db::name('KnowledgeDoc')->where('id',$param['id'])->find();
		if(empty($detail)){
			return to_assign(1,"数据错误，无法生成");
		}
		$detail['user'] = Db::name('Admin')->where('id',$detail['admin_id'])->value('name');
		View::assign('detail', $detail);
		//注意：不支持网络图，不支持emoji表情，不支持代码高亮。
        $html = View::fetch('pdf');
        //tempDir指定临时文件目录，需要有可写入的权限，否则会报错
        $mpdf = new Mpdf([
			'mode'=>'utf-8',//或者zh
            'format' => 'A4',
			'margin_top'=>19,
			'margin_bottom'=>19,
			//重新定义字体路径
			'fontDir' => [
				CMS_ROOT . "public/static/mpdf/"
			],
			//重新定义默认字体
			'fontdata' => [
				"sun-exta" => [
					'R'  => 'MiSans-Regular.ttf', // regular font
					'B'  => 'MiSans-Bold.ttf', // optional: bold font
					'I'  => 'MiSans-Regular.ttf', // optional: italic font
					'BI' => 'MiSans-Bold.ttf', // optional: bold-italic font 
					'useKashida' => 75, 
					'sip-ext' => 'MiSans-Regular.ttf', /* SIP=Plane2 Unicode (extension B) */
				]
			],
            'tempDir' => CMS_ROOT . "public/storage/"
        ]);
		
		// 设置水印		
		$w = $detail['user'];
		$mpdf->SetWatermarkText($w,0.12);
		$mpdf->showWatermarkText = true;
		//自定义标签样式设置也可以直接修改DefaultCss.php中的默认设置
		$mpdf->defaultCSS["BODY"]['FONT-SIZE'] = '10.5pt';
		$mpdf->defaultCSS["BODY"]['LINE-HEIGHT'] = '1.6em';
		$mpdf->defaultCSS["P"]['MARGIN'] = '0.8em 0';
		$mpdf->defaultCSS["P"]['PADDING'] = '0';
		$mpdf->defaultCSS["H1"]['FONT-SIZE'] = '16pt';
		$mpdf->defaultCSS["H1"]['MARGIN'] = '0.8em 0';
		$mpdf->defaultCSS["H2"]['FONT-SIZE'] = '14.5pt';
		$mpdf->defaultCSS["H2"]['MARGIN'] = '0.8em 0';
		$mpdf->defaultCSS["H3"]['FONT-SIZE'] = '13pt';
		$mpdf->defaultCSS["H3"]['MARGIN'] = '0.8em 0';
		$mpdf->defaultCSS["H4"]['FONT-SIZE'] = '12pt';
		$mpdf->defaultCSS["H4"]['MARGIN'] = '0.8em 0';
		$mpdf->defaultCSS["H5"]['FONT-SIZE'] = '10.5pt';
		$mpdf->defaultCSS["H5"]['FONT-WEIGHT'] = 'normal';
		$mpdf->defaultCSS["H5"]['MARGIN'] = '0.8em 0';
		$mpdf->defaultCSS["IMG"]['MARGIN'] = '.8em 0';
		$mpdf->defaultCSS['SPAN'] = [
			'PADDINT-LEFT' => '14px',
			'PADDINT-RIGHT' => '14px',
			'PADDINT-TOP' => '0px',
			'PADDINT-BOTTOM' => '0px'
		];
        $mpdf->SetDisplayMode('fullpage');
        //自动分析录入内容字体
        $mpdf->autoScriptToLang = true;
        $mpdf->autoLangToFont = true;
        //文章pdf文件存储路径
		$filename = CMS_ROOT . "public" . "/storage/".$detail['title']."_".$detail['id'].".pdf";
		$pageNumber = count($mpdf->pages);
		$header= '<table width="100%"><tr> 
<td width="50%" style="text-align:left;font-size:12px; color:#999999">勾股OA办公系统</td> 
<td width="50%" style="text-align:right;font-size:12px; color:#999999">企业数字化、信息化办公的优秀解决方案</td> 
</tr></table>'; 
		$footer= '<p style="text-align:center;font-size:12px; color:#999999">第 {PAGENO}/{nbpg} 页</p>'; 
		$mpdf->SetHTMLHeader($header);
		$mpdf->SetHTMLFooter($footer);
        //以html为标准分析写入内容
        $mpdf->WriteHTML($html);
		//直接下载文件
		//$mpdf->Output('tmp.pdf',true);
		$mpdf->Output($detail['title']."_".$detail['id'].".pdf","d");
		exit;
		/*
        //生成磁盘文件
        $mpdf->Output($filename);
        //判断是否生成文件成功
        if (is_file($filename)){
            return to_assign(0,"文件生成成功");
        } else {
            return to_assign(1,"文件生成失败");
        }
		*/
    }

}
