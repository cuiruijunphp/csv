<?php
namespace Home\Controller;
use Think\Controller;
class CsvController extends BaseController {

    /*
     * 列表
     */
    public function index(){
        $data['list'] = $this->loopDir2('./Public');

        $this->assign($data);
        $this->display();
    }


    /*
     * 合并/下载文件
     */
    public function import_do(){
        $file_name_list = I('post.file_name');

        if(!$file_name_list){
            echo '没选择文件';
        }

        $data = [];

        foreach($file_name_list as $k => $v){
            $order_list = $this->importExcel('./Public/' . $v);

            $data_return = $this->excel_data_to_array($order_list);

            $data = array_merge($data, $data_return);
        }

        $xlsCell = array(
            array('sample_id', 'Sample Id'),
            array('call', 'Call'),
            array('assay_id', 'Assay Id'),
            array('well_position', 'Well Position'),
            array('chip_name', 'Chip Name'),
            array('customer_name', 'Customer Name'),
            array('dna_id', 'DNA Id'),
            array('date', 'Date'),
        );

        $this->exportExcel('订单',$xlsCell,$data);

    }


    public function import(){

//        $file = './Public/2.csv';
        $file = './Public/4.csv';
        $order_list = $this->importExcel($file);

        $common = explode('/', $order_list[1][0]);

        $chip_name = $common[2];
        $customer_name_tmp = $common[4];

        $customer_name = explode(' (',$customer_name_tmp)[0];

        //取日期
        $date = $common[1] . $common[7];

        $company_export = [];
        foreach($order_list as $k => $v){
            if($k > 3){
                $company_export[] = [
                    'sample_id' => $v[0],
                    'call' => $v[1],
                    'assay_id' => $v[2],
                    'well_position' => $v[3],
                    'chip_name' => $chip_name,
                    'customer_name' => $customer_name,
                    'dna_id' => explode('-', $v[0])[0],
                    'date' => $date,
                ];
            }
        }

        $xlsCell = array(
            array('sample_id', 'Sample Id'),
            array('call', 'Call'),
            array('assay_id', 'Assay Id'),
            array('well_position', 'Well Position'),
            array('chip_name', 'Chip Name'),
            array('customer_name', 'Customer Name'),
            array('dna_id', 'DNA Id'),
            array('date', 'Date'),
        );

        $this->exportExcel('订单',$xlsCell,$company_export);
    }

    /*
     * 上传页面
     */
    public function upload(){
        $this->display();
    }

    /*
     * 处理上传文件
     */
    public function upload_do(){

        if($_FILES['file']['tmp_name']){
            $upload = new \Think\Upload();// 实例化上传类
            $upload->maxSize   =     2048000 ;// 设置附件上传大小
//            $upload->exts      =     array('jpg', 'gif', 'png', 'jpeg');// 设置附件上传类型
            $upload->rootPath  =      './Public/'; // 设置附件上传根目录
//            $upload->savePath  =      '' ; // 设置附件上传（子）目录
            $upload->saveName  =      explode('.', $_FILES['file']['name'])[0]; // 设置附件上传（子）目录
            // 上传文件
            $info  =  $upload->uploadOne($_FILES['file']);
            if(!$info) {// 上传错误提示错误信息
                $this->result_return(null, 500, $upload->getError());
            }else{// 上传成功 获取上传文件信息
                $file_path = $info['savepath'].$info['savename'];

                echo '上传成功';
            }
        }
    }

    /*
     * 删除文件
     */
    public function del(){
        $params = I('get.');
        $ids = $params['ids'];
        $id = $params['id'];

        if($ids){
            // 删除文件夹以及相应的文件
            $this->deldir('./Public/' . $ids . '/');
        }elseif($id){
            // 删除相应文件,跳转回页面
            $file_path = str_replace('_', '/' , $id);
            unlink('./Public/' . $file_path);
        }

        $this->redirect('index');
    }

    private function excel_data_to_array($order_list){
        $common = explode('/', $order_list[1][0]);

        $chip_name = $common[2];
        $customer_name_tmp = $common[4];

        $customer_name = explode(' (',$customer_name_tmp)[0];

        //取日期
        $date = $common[1] . $common[7];

        $company_export = [];
        foreach($order_list as $k => $v){
            if($k > 3){
                $company_export[] = [
                    'sample_id' => $v[0],
                    'call' => $v[1],
                    'assay_id' => $v[2],
                    'well_position' => $v[3],
                    'customer_name' => $customer_name,
                    'chip_name' => $chip_name,
                    'dna_id' => explode('-', $v[0])[0],
                    'date' => $date,
                ];
            }
        }

        return $company_export;
    }

    function loopDir2($dir)
    {
        $fileArray = [];
        $files = scandir($dir);
        if (false !== $files) {
            foreach ($files as $file) {
                if ($file != '.' && $file != '..') {
                    if (is_dir($dir . DIRECTORY_SEPARATOR . $file)) {
                        $fileArray[$file] = $this->loopDir2($dir . DIRECTORY_SEPARATOR . $file);
                    } else {
                        $fileArray[] = $file;
                    }
                }
            }
        }
        return $fileArray;
    }

    //清空文件夹函数和清空文件夹后删除空文件夹函数的处理
    function deldir($path){
        //如果是目录则继续
        if(is_dir($path)){
            //扫描一个文件夹内的所有文件夹和文件并返回数组
            $p = scandir($path);
            if(count($p) > 2){
                foreach($p as $val){
                    //排除目录中的.和..
                    if($val !="." && $val !=".."){
                        //如果是目录则递归子目录，继续操作
                        if(is_dir($path.$val)){
                            //子目录中操作删除文件夹和文件
                            $this->deldir($path.$val.'/');
                            //目录清空后删除空文件夹
                            @rmdir($path.$val.'/');
                        }else{
                            //如果是文件直接删除
                            unlink($path.$val);
                        }
                    }
                }
            }
        }
        // 清空dir
        @rmdir($path);
    }
}