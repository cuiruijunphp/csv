<?php
namespace Home\Controller;
use Think\Controller;
class BaseController extends Controller {

    /**
     * 接口通用返回
     * @author cuirj
     * @date   2019/9/27 下午12:48
     *
     * @param  int param
     * @return  array
     */
    public function result_return($data, $code = 200, $message = ''){

        $return_data = [
            'code' => $code,
            'message' => $message,
            'data' => $data
        ];

        echo json_encode($return_data);
        exit;
    }
	/**
	 * Thinkphp默认分页样式转Bootstrap分页样式
	 * @author H.W.H
	 * @param string $page_html tp默认输出的分页html代码
	 * @return string 新的分页html代码
	 */
	public function bootstrap_page_style($page_html){
		if ($page_html) {
			$page_show = str_replace('<div>','<nav><ul class="pagination">',$page_html);
			$page_show = str_replace('</div>','</ul></nav>',$page_show);
			$page_show = str_replace('<span class="current">','<li class="active"><a>',$page_show);
			$page_show = str_replace('</span>','</a></li>',$page_show);
			$page_show = str_replace(array('<a class="num"','<a class="prev"','<a class="next"','<a class="end"','<a class="first"'),'<li><a',$page_show);
			$page_show = str_replace('</a>','</a></li>',$page_show);
		}
		return $page_show;
	}

	/**
	 * 分页类的改写
	 * @author cuirj
	 * @date   2019/10/27 上午11:01
	 * @method get
	 *
	 * @param  int $count 总条数
	 * @param  int $page_size 每页多少条
	 * @return  array
	 */
	public function page_new($count, $page_size = 10){

		$Page = new \Think\Page($count,$page_size);
		$Page->lastSuffix = false;//最后一页不显示为总页数
		$Page->setConfig('header','<li class="disabled hwh-page-info"><a>共<em>%TOTAL_ROW%</em>条  <em>%NOW_PAGE%</em>/%TOTAL_PAGE%页</a></li>');
		$Page->setConfig('prev','上一页');
		$Page->setConfig('next','下一页');
		$Page->setConfig('last','末页');
		$Page->setConfig('first','首页');
		$Page->setConfig('theme','%HEADER% %FIRST% %UP_PAGE% %LINK_PAGE% %DOWN_PAGE% %END%');
		$page_show = $this->bootstrap_page_style($Page->show());//重点在这里
		return $page_show;
	}

	/**
	 * 数据导出为.xls格式
	 * @param string $fileName 导出的文件名
	 * @param $expCellName     array -> 数据库字段以及字段的注释
	 * @param $expTableData    Model -> 要传入的数据
	 */
	public function exportExcel($fileName='table',$expCellName,$expTableData){
		$xlsTitle = iconv('utf-8', 'gb2312', $fileName);//文件名称
		$xlsName = $fileName.date("_Y.m.d_H.i.s"); //or $xlsTitle 文件名称可根据自己情况设定
		$cellNum = count($expCellName);
		$dataNum = count($expTableData);

		Vendor('PHPExcel.Classes.PHPExcel');
		Vendor('PHPExcel.Classes.PHPExcel.IOFactory');
		Vendor('PHPExcel.Classes.PHPExcel.Reader.Excel5');
		Vendor('PHPExcel.Classes.PHPExcel.Writer.Excel5');

		$objPHPExcel = new \PHPExcel();
		$cellName = array('A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z','AA','AB','AC','AD','AE','AF','AG','AH','AI','AJ','AK','AL','AM','AN','AO','AP','AQ','AR','AS','AT','AU','AV','AW','AX','AY','AZ');

		for($i=0;$i<$cellNum;$i++){
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue($cellName[$i].'1', $expCellName[$i][1]);
		}
		// Miscellaneous glyphs, UTF-8
		for($i=0;$i<$dataNum;$i++){
			for($j=0;$j<$cellNum;$j++){
				$objPHPExcel->getActiveSheet(0)->setCellValue($cellName[$j].($i+2), $expTableData[$i][$expCellName[$j][0]]);
			}
		}

		header('pragma:public');
		header('Content-type:application/vnd.ms-excel;charset=utf-8;name="'.$xlsTitle.'.xls"');
		header("Content-Disposition:attachment;filename=$xlsName.xls");//attachment新窗口打印inline本窗口打印
		$objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
		$objWriter->save('php://output');
		exit;
	}

	public function importExcel($file){
        // 判断文件是什么格式
        $type = pathinfo( $file);
        $type = strtolower($type["extension"]);

//    echo $type;die;
//    $type=$type==='xlsx' ? 'Excel2007' : 'Excel5';

        if($type=='xlsx'){
            $type=  'Excel2007';
        }elseif($type=='csv'){
            $type='CSV';
        }else{
            $type='Excel5';
        }

//    echo $type;die;
        ini_set('max_execution_time', '0');
        Vendor('PHPExcel.Classes.PHPExcel');
        Vendor('PHPExcel.Classes.PHPExcel.IOFactory');
        Vendor('PHPExcel.Classes.PHPExcel.Reader.Excel5');
        Vendor('PHPExcel.Classes.PHPExcel.Writer.Excel5');
        // 判断使用哪种格式
        $objReader = \PHPExcel_IOFactory::createReader($type);
        $objPHPExcel = $objReader->load($file);
        $sheet = $objPHPExcel->getSheet(0);
//    // 取得总行数
//    $highestRow = $sheet->getHighestRow();
//    // 取得总列数
//    $highestColumn = $sheet->getHighestColumn();
//    //循环读取excel文件,读取一条,插入一条
//    $data=array();
//    //从第一行开始读取数据
//    for($j=1;$j<=$highestRow;$j++){
//        //从A列读取数据
//        for($k='A';$k<=$highestColumn;$k++){
//            // 读取单元格
//            //数据坐标
//            $address=$k.$j;
//            //读取到的数据，保存到数组$arr中
//            $data[$j][]=$objPHPExcel->getActiveSheet()->getCell($address)->getValue();
//
//           // $data[$j][]=$objPHPExcel->getActiveSheet()->getCell("$k$j")->getValue();
//        }
//    }
        foreach ($objPHPExcel->getWorksheetIterator() as $worksheet) {     //遍历工作表
            //echo 'Worksheet - ' , $worksheet->getTitle() , PHP_EOL;
            foreach ($worksheet->getRowIterator() as $key=>$row) {       //遍历行
                //  echo $row->getRowIndex()."<br/>";
                $cellIterator = $row->getCellIterator();   //得到所有列
                $cellIterator->setIterateOnlyExistingCells( false); // Loop all cells, even if it is not set
                foreach ($cellIterator as $cell) {  //遍历列
//                    if (!is_null($cell)) {  //如果列不给空就得到它的坐标和计算的值
//                        $rows[$key][]=   $cell->getCalculatedValue();
//                    }
                    $rows[$key][]=   $cell->getCalculatedValue();
                }
            }
        }
        return $rows;
    }
}