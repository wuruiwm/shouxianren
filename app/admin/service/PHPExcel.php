<?php
/**
 * Created by .
 * User: QQ:758246061
 * Date: 2018/8/6
 * Time: 10:00
 */

namespace app\admin\service;


// 引入extend下的类库文件
use think\Db;
use think\Exception;
use think\facade\Request;

require_once EXTEND_PATH . 'PHPExcel/Classes/PHPExcel.php';


class PHPExcel
{

    public static function export($filename, $title, $field = [], $database = [], $type = 'Excel2007', $sum = 0)
    {
        $letterArr = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'S', 'Y', 'Z'];
        $phpExcel = new \PHPExcel();
        $objSheet = $phpExcel->getActiveSheet();
        $objSheet->setTitle($filename);
        $objSheet->getProtection()->setSheet(true);
        $objSheet->protectCells('A:N', 'sxr888888');
        foreach ($title as $key => $value) {
            $objSheet->setCellValue($letterArr[$key] . '1', $value);
            $objSheet->getColumnDimension($letterArr[$key])->setWidth(20);
            $objSheet->getStyle($letterArr[$key] . '1')->getFont()->setBold(true)->setName('Verdana');
            if ($sum == 1) {
                $objSheet->setCellValue('A2', '合计');
                $objSheet->setCellValue('F2', "=SUM(" . 'F3' . ':' . 'F99999)');
                $objSheet->setCellValue('G2', "=SUM(" . 'G3' . ':' . 'G99999)');
                $objSheet->setCellValue('H2', "=SUM(" . 'H3' . ':' . 'H99999)');
                $objSheet->setCellValue('I2', "=SUM(" . 'I3' . ':' . 'I99999)');
                $objSheet->setCellValue('K2', "=SUM(" . 'K3' . ':' . 'K99999)');
                $objSheet->setCellValue('L2', "=SUM(" . 'L3' . ':' . 'L99999)');
                $objSheet->getStyle($letterArr[$key] . '2')->getFont()->setBold(true)->setName('Verdana');
            }

        }

        if ($database) {
            foreach ($database as $k => $v) {
                foreach ($field as $k1 => $v1) {
                    if ($sum == 1) {
                        $objSheet->setCellValue($letterArr[$k1] . ($k + 3), $v[$v1]);
                    } else {
                        $objSheet->setCellValue($letterArr[$k1] . ($k + 2), $v[$v1]);
                    }
                }
            }
        }
        if ($type == 'Excel2007') {
            $filename = $filename . date('Y年m月d日H点i分s秒', time()) . '.xlsx';
        } else if ($type == 'Excel5') {
            $filename = $filename . date('Y年m月d日H点i分s秒', time()) . '.xls';
        }

        $PHPWrite = \PHPExcel_IOFactory::createWriter($phpExcel, $type);
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename=' . $filename);
        header('Cache-Control: max-age=0');
        $PHPWrite->save("php://output");
    }

}