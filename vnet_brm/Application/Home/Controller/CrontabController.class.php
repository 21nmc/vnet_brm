<?php
namespace Home\Controller;
use Think\Controller;
use User\Api\UserApi;

class CrontabController extends Controller {
    //http://211.151.5.12/zichan/index.php/Home/Crontab/day_crontab.html

    public function day_crontab(){

        $Resource_zabbix_type = M('resource_zabbix_type');
        $Week_day = M('week_day');
        $Crontab_week = M('crontab_week');
        $Week_history = M('week_history');
        //1,处理时间 2，按自愿类别获取端口集合3，扫描端口数据并聚合

        $crontablist = $Crontab_week->where('flag="day"')->select();
        $lasttime=$crontablist[0]['lasttime'];
 echo $lasttime."------------";
        $stime=strtotime($lasttime." 00:00:00");
        $etime=$stime+86400;
        $nowtime=date("Y-m-d H:i:s");
        $nowtime=strtotime($nowtime);
        $sdate=date("Y-m-d",$stime);
        $edate=date("Y-m-d",$etime);


echo $sdate.$edate."------------";
        if ($nowtime>$etime){//超过截止时间24小时才可运行下方程序,否则每次刷新日期递增

            
            $list = $Week_day->select();
            $id=$list[0]['id'];
            for($i=0;$i<count($list);$i++){

                $type_id=$list[$i]['id'];
                $type_name=$list[$i]['type_name'];
                $depart_id=$list[$i]['depart_id'];

                $data_history['stime'] = $sdate;
                $data_history['etime'] = $edate;
                $data_history['type_id'] = $list[$i]['type_id'];
                $data_history['depart_id'] = $list[$i]['depart_id'];
                $data_history['type_name'] = $list[$i]['type_name'];
                $data_history['bandwidth'] = $list[$i]['bandwidth'];
                $data_history['pay_bandwidth'] = $list[$i]['pay_bandwidth'];
                $data_history['report_type'] = 'day';
                if ($type_name=="华北-BGP" || $type_name=="华东-联通" || $type_name=="华南-电信"){
                    $data['index_key'] = 1;
                }                
                $Week_history->data($data_history)->add();
            }
            if(!empty($id)){
                echo "将数据插入历史表成功！<br />";
            }else{
                echo "日报表数据为空！<br />";
            }
            
            $Week_day->where("1=1")->delete();
            echo "旧数据删除成功！<br />";

            $list = $Resource_zabbix_type->select();
            for($i=0;$i<count($list);$i++){

                $type_id=$list[$i]['id'];
                $type_name=$list[$i]['type_name'];
                $depart_id=$list[$i]['depart_id'];
               
                $data['stime'] = $sdate;
                $data['etime'] = $edate;
                $data['type_id'] = $list[$i]['id'];
                $data['depart_id'] = $list[$i]['depart_id'];
                $data['type_name'] = $list[$i]['type_name'];
                $data['pay_bandwidth'] = $list[$i]['pay_bandwidth'];
                if ($type_name=="华北-BGP" || $type_name=="华东-联通" || $type_name=="华南-电信"){
                    $data['index_key'] = 1;
                }
                


                $week_list = $Week_day->where(" stime = '".$sdate."' and etime = '".$edate."' and type_id='".$type_id."' and depart_id='".$depart_id."' ")->select();
                $checkid=$week_list[0]['id'];
                if (empty($checkid)){
                    $Week_day->data($data)->add();
                }

            }
            echo "新数据插入成功！<br />";
            $data_crontab['lasttime'] = $edate;
            if(!empty($id)){ //第一次获取资源类别不更新定时开关表
                $list=$Crontab_week->where('flag="day"')->save($data_crontab);
                if (!empty($list)){
                    echo "下一单日运行周期标记更新成功！".$edate."<br />";
                }
            }

        }else{
            
           echo "运行周期未到，下一个周期是".$edate."<br />";

        }




        
        
    }
    












    public function week_crontab(){

        $Resource_zabbix_type = M('resource_zabbix_type');
        $Week = M('week');
        $Crontab_week = M('crontab_week');
        $Week_history = M('week_history');
        //1,处理时间 2，按自愿类别获取端口集合3，扫描端口数据并聚合

        $crontablist = $Crontab_week->where('flag="week"')->select();
        $lasttime=$crontablist[0]['lasttime'];
        $stime=strtotime($lasttime." 00:00:00");
        $etime=$stime+604800; //7天
        $nowtime=date("Y-m-d H:i:s");
        $nowtime=strtotime($nowtime);
        $sdate=date("Y-m-d",$stime);
        $edate=date("Y-m-d",$etime);
        if ($nowtime>$etime){
            
            $list = $Week->select();
            $id=$list[0]['id'];
            for($i=0;$i<count($list);$i++){

                $type_id=$list[$i]['id'];
                $type_name=$list[$i]['type_name'];
                $depart_id=$list[$i]['depart_id'];

                $data_history['stime'] = $sdate;
                $data_history['etime'] = $edate;
                $data_history['type_id'] = $list[$i]['type_id'];
                $data_history['depart_id'] = $list[$i]['depart_id'];
                $data_history['type_name'] = $list[$i]['type_name'];
                $data_history['bandwidth'] = $list[$i]['bandwidth'];
                $data_history['report_type'] = 'week';
                $data_history['pay_bandwidth'] = $list[$i]['pay_bandwidth'];
                if ($type_name=="华北-BGP" || $type_name=="华东-联通" || $type_name=="华南-电信"){
                    $data['index_key'] = 1;
                }  
                $Week_history->data($data_history)->add();
            }
            if(!empty($id)){
                echo "将数据插入历史表成功！<br />";
            }else{
                echo "周报表数据为空！<br />";
            }
            
            $Week->where("1=1")->delete();
            echo "旧数据删除成功！<br />";

            $list = $Resource_zabbix_type->select();
            for($i=0;$i<count($list);$i++){

                $type_id=$list[$i]['id'];
                $type_name=$list[$i]['type_name'];
                $depart_id=$list[$i]['depart_id'];
               
                $data['stime'] = $sdate;
                $data['etime'] = $edate;
                $data['type_id'] = $list[$i]['id'];
                $data['depart_id'] = $list[$i]['depart_id'];
                $data['type_name'] = $list[$i]['type_name'];
                $data['pay_bandwidth'] = $list[$i]['pay_bandwidth'];
                if ($type_name=="华北-BGP" || $type_name=="华东-联通" || $type_name=="华南-电信"){
                    $data['index_key'] = 1;
                }  
                $week_list = $Week->where(" stime = '".$sdate."' and etime = '".$edate."' and type_id='".$type_id."' and depart_id='".$depart_id."' ")->select();
                $checkid=$week_list[0]['id'];
                if (empty($checkid)){
                    $Week->data($data)->add();
                }

            }
            echo "新数据插入成功！<br />";
            $data_crontab['lasttime'] = $edate;
            if(!empty($id)){ //第一次获取资源类别不更新定时开关表
                $list=$Crontab_week->where('flag="week"')->save($data_crontab);
                if (!empty($list)){
                    echo "下一单周运行周期标记更新成功！".$edate."<br />";
                }
            }

        }else{
            
            echo "运行周期未到，下一个周期是".$edate."<br />";

        }




        
        
    }
    




    public function doubleweek_crontab(){

        $Resource_zabbix_type = M('resource_zabbix_type');
        $Doubleweek = M('doubleweek');
        $Crontab_week = M('crontab_week');
        $Week_history = M('week_history');
        //1,处理时间 2，按自愿类别获取端口集合3，扫描端口数据并聚合

        $crontablist = $Crontab_week->where('flag="doubleweek"')->select();
        $lasttime=$crontablist[0]['lasttime'];
        $stime=strtotime($lasttime." 00:00:00");
        $etime=$stime+1209600; //14天
        $nowtime=date("Y-m-d H:i:s");
        $nowtime=strtotime($nowtime);

        $sdate=date("Y-m-d",$stime);
        $edate=date("Y-m-d",$etime);
        //echo $nowtime.'==='.$etime;
        if ($nowtime>$etime){

            
            $list = $Doubleweek->select();
            $id=$list[0]['id'];
            for($i=0;$i<count($list);$i++){

                $type_id=$list[$i]['id'];
                $type_name=$list[$i]['type_name'];
                $depart_id=$list[$i]['depart_id'];

                $data_history['stime'] = $sdate;
                $data_history['etime'] = $edate;
                $data_history['type_id'] = $list[$i]['type_id'];
                $data_history['depart_id'] = $list[$i]['depart_id'];
                $data_history['type_name'] = $list[$i]['type_name'];
                $data_history['bandwidth'] = $list[$i]['bandwidth'];
                $data_history['pay_bandwidth'] = $list[$i]['pay_bandwidth'];
                $data_history['report_type'] = 'doubleweek';
                if ($type_name=="华北-BGP" || $type_name=="华东-联通" || $type_name=="华南-电信"){
                    $data['index_key'] = 1;
                }  
                $Week_history->data($data_history)->add();
            }
            if(!empty($id)){
                echo "将数据插入历史表成功！<br />";
            }else{
                echo "周报表数据为空！<br />";
            }
            
            $Doubleweek->where("1=1")->delete();
            echo "旧数据删除成功！<br />";

            $list = $Resource_zabbix_type->select();
            for($i=0;$i<count($list);$i++){

                $type_id=$list[$i]['id'];
                $type_name=$list[$i]['type_name'];
                $depart_id=$list[$i]['depart_id'];
               
                $data['stime'] = $sdate;
                $data['etime'] = $edate;
                $data['type_id'] = $list[$i]['id'];
                $data['depart_id'] = $list[$i]['depart_id'];
                $data['type_name'] = $list[$i]['type_name'];
                $data['pay_bandwidth'] = $list[$i]['pay_bandwidth'];
                if ($type_name=="华北-BGP" || $type_name=="华东-联通" || $type_name=="华南-电信"){
                    $data['index_key'] = 1;
                }  
                $week_list = $Doubleweek->where(" stime = '".$sdate."' and etime = '".$edate."' and type_id='".$type_id."' and depart_id='".$depart_id."' ")->select();
                $checkid=$week_list[0]['id'];
                if (empty($checkid)){
                    $Doubleweek->data($data)->add();
                }

            }
            echo "新数据插入成功！<br />";
            $data_crontab['lasttime'] = $edate;
            if(!empty($id)){ //第一次获取资源类别不更新定时开关表
                $list=$Crontab_week->where('flag="doubleweek"')->save($data_crontab);
                if (!empty($list)){
                    echo "下一双周运行周期标记更新成功！".$edate."<br />";
                }
            }
        }else{
            
            echo "运行周期未到，下一个双周周期是".$edate."<br />";

        }


        
    }






    public function month_crontab(){

        $Resource_zabbix_type = M('resource_zabbix_type');
        $Month = M('month');
        $Crontab_week = M('crontab_week');
        $Week_history = M('week_history');
        //1,处理时间 2，按自愿类别获取端口集合3，扫描端口数据并聚合

        $smonth=date('Y-m-01', strtotime('-1 month'));
        $emonth= date('Y-m-t', strtotime('-1 month'));

        $crontablist = $Crontab_week->where('flag="month"')->select();
        $lasttime=$crontablist[0]['lasttime'];//201710

        $stime=date('Y-m-01', strtotime('-1 month')); //上月第一天
        $etime=date('Y-m-t', strtotime('-1 month')); //上月最后一天

        $list = $Month->select();
        $id=$list[0]['id'];
        for($i=0;$i<count($list);$i++){

            $type_id=$list[$i]['id'];
            $type_name=$list[$i]['type_name'];
            $depart_id=$list[$i]['depart_id'];

            $data_history['stime'] = $stime;
            $data_history['etime'] = $etime;
            $data_history['type_id'] = $list[$i]['type_id'];
            $data_history['depart_id'] = $list[$i]['depart_id'];
            $data_history['type_name'] = $list[$i]['type_name'];
            $data_history['bandwidth'] = $list[$i]['bandwidth'];
            $data_history['pay_bandwidth'] = $list[$i]['pay_bandwidth'];
            $data_history['report_type'] = 'month';
            if ($type_name=="华北-BGP" || $type_name=="华东-联通" || $type_name=="华南-电信"){
                $data['index_key'] = 1;
            }  
            $Week_history->data($data_history)->add();
        }
        if(!empty($id)){
            echo "将数据插入历史表成功！<br />";
        }else{
            echo "月报表数据为空！<br />";
        }
        
        $Month->where("1=1")->delete();
        echo "旧数据删除成功！<br />";

        $list = $Resource_zabbix_type->select();
        for($i=0;$i<count($list);$i++){

            $type_id=$list[$i]['id'];
            $type_name=$list[$i]['type_name'];
            $depart_id=$list[$i]['depart_id'];
           
            $data['stime'] = $stime;
            $data['etime'] = $etime;
            $data['type_id'] = $list[$i]['id'];
            $data['depart_id'] = $list[$i]['depart_id'];
            $data['type_name'] = $list[$i]['type_name'];
            $data['pay_bandwidth'] = $list[$i]['pay_bandwidth'];
            if ($type_name=="华北-BGP" || $type_name=="华东-联通" || $type_name=="华南-电信"){
                $data['index_key'] = 1;
            }  
            $week_list = $Month->where(" stime = '".$stime."' and etime = '".$etime."' and type_id='".$type_id."' and depart_id='".$depart_id."' ")->select();
            $checkid=$week_list[0]['id'];
            if (empty($checkid)){
                $Month->data($data)->add();
            }

        }
        echo "新数据插入成功！<br />";
        $nextdate=date('Y-m-t', strtotime('month')); //上月最后一天

        $data_crontab['lasttime'] = date("Ym");
        if(!empty($id)){ //第一次获取资源类别不更新定时开关表
            $list=$Crontab_week->where('flag="month"')->save($data_crontab);
            if (!empty($list)){
                echo "下一月报运行周期标记更新成功！".$edate."<br />";
            }
        }

    }







    public function day_aggregated(){

        $Week_day = M('week_day');//将结果汇总到这个表 日，周，双周，月
        $Week_data = M('week_data');//按照resource_zabbix.tiemid获取每个出口的 日，周，双周，月数据表
        $Resource_zabbix = M('resource_zabbix2');//所有出口参数表

        $type_arr = array();
        $list = $Week_day->select();

        foreach ($list as $one){

            $type_id = $one['type_id'];
            $type_name = $one['type_name'];

            $port_list = $Resource_zabbix->where("type_id = $type_id")->select();
            $all_sum = 0;
            $weekmax = 0;
            foreach ($port_list as $o){

                $id = intval($o['id']);
                $initemid = intval($o['initemid']);
                $outitemid = intval($o['outitemid']);
                
                if ($type_id!=7 && $type_id!=8 && $type_id!=9){

                    $week_data_list = $Week_data->where("itemid = $initemid")->select();
                    $weekmax_in=intval($week_data_list[0]['daymax']); 

                    $week_data_list = $Week_data->where("itemid = $outitemid")->select();
                    $weekmax_out=intval($week_data_list[0]['daymax']);                      

                   
                    if ($weekmax_in>$weekmax_out){
                        $weekmax=$weekmax_in;
                    }else{
                        $weekmax=$weekmax_out;
                    }

                    if ($id==513){//兆维in和out都统计并减去一个合图的量
                        $weekmax=$weekmax_in+$weekmax_out;
                    }elseif ($id==515){//兆维in和out都统计并减去一个合图的量
                        $weekmax=$weekmax_in+$weekmax_out;
                    }elseif ($id==272){//兆维in和out都统计并减去一个合图的量
                        $weekmax_jian_in=$weekmax_in;
                        $weekmax_jian_out=$weekmax_out;
                    }elseif ($id==268){//兆维in和out都统计并减去一个合图的量
                        $weekmax_jian_in=$weekmax_in;
                        $weekmax_jian_out=$weekmax_out;                      
                    }else{
                        $weekmax_jian_in=0;
                        $weekmax_jian_out=0;                        
                    }                    

                }else{
                    $checkitemid = intval($o['outitemid']);
                    $week_data_list = $Week_data->where("itemid = $checkitemid")->select();
                    $weekmax=$week_data_list[0]['daymax'];                      
                }

                //bgp兆维in和out都统计并减去一个合图的量
                $weekmax=$weekmax-($weekmax_jian_in+$weekmax_jian_out); 
                echo $weekmax."-----<br />";
                $weekmax = round(($weekmax/(1024*1024)));
                $all_sum = $all_sum+$weekmax;
                
            }


            unset($data);
            $data['bandwidth']=$all_sum;
            $list=$Week_day->where("type_id = $type_id")->save($data);

            echo $type_name."执行成功！<br />";

        }

          
        
    }
















    public function week_aggregated(){

        $Week = M('week');
        $Week_data = M('week_data');
        $Resource_zabbix = M('resource_zabbix2');

        $type_arr = array();
        $list = $Week->select();

        foreach ($list as $one){

            $type_id = $one['type_id'];
            $type_name = $one['type_name'];

            $port_list = $Resource_zabbix->where("type_id = $type_id")->select();
            $all_sum = 0;

            foreach ($port_list as $o){

                $id = intval($o['id']);
                $initemid = intval($o['initemid']);
                $outitemid = intval($o['outitemid']);
                
                if ($type_id!=7 && $type_id!=8 && $type_id!=9){

                    $week_data_list = $Week_data->where("itemid = $initemid")->select();
                    $weekmax_in=intval($week_data_list[0]['weekmax']); 

                    $week_data_list = $Week_data->where("itemid = $outitemid")->select();
                    $weekmax_out=intval($week_data_list[0]['weekmax']);                      

                   
                    if ($weekmax_in>$weekmax_out){
                        $weekmax=$weekmax_in;
                    }else{
                        $weekmax=$weekmax_out;
                    }

                    if ($id==513){//兆维in和out都统计并减去一个合图的量
                        $weekmax=$weekmax_in+$weekmax_out;
                    }elseif ($id==515){//兆维in和out都统计并减去一个合图的量
                        $weekmax=$weekmax_in+$weekmax_out;
                    }elseif ($id==272){//兆维in和out都统计并减去一个合图的量
                        $weekmax_jian_in=$weekmax_in;
                        $weekmax_jian_out=$weekmax_out;
                    }elseif ($id==268){//兆维in和out都统计并减去一个合图的量
                        $weekmax_jian_in=$weekmax_in;
                        $weekmax_jian_out=$weekmax_out;                      
                    }else{
                        $weekmax_jian_in=0;
                        $weekmax_jian_out=0;                        
                    }                    

                }else{
                    $checkitemid = intval($o['outitemid']);
                    $week_data_list = $Week_data->where("itemid = $checkitemid")->select();
                    $weekmax=$week_data_list[0]['weekmax'];                      
                }

                //bgp兆维in和out都统计并减去一个合图的量
                $weekmax=$weekmax-($weekmax_jian_in+$weekmax_jian_out); 
                
                $weekmax = round(($weekmax/(1024*1024)));
                $all_sum = $all_sum+$weekmax;
                
                
            }


            unset($data);
            $data['bandwidth']=$all_sum;
            $list=$Week->where("type_id = $type_id")->save($data);

            echo $type_name."执行成功！<br />";

        }

          
        
    }





    public function doubleweek_aggregated(){

        $Doubleweek = M('doubleweek');
        $Week_data = M('week_data');
        $Resource_zabbix = M('resource_zabbix2');

        $type_arr = array();
        $list = $Doubleweek->select();

       foreach ($list as $one){

            $type_id = $one['type_id'];
            $type_name = $one['type_name'];

            $port_list = $Resource_zabbix->where("type_id = $type_id")->select();
            $all_sum = 0;
            $weekmax = 0;
            $weekmax_jian_in = 0;
            $weekmax_jian_out = 0;

            foreach ($port_list as $o){

                $id = intval($o['id']);
                $initemid = intval($o['initemid']);
                $outitemid = intval($o['outitemid']);
                
                if ($type_id!=7 && $type_id!=8 && $type_id!=9){

                    $week_data_list = $Week_data->where("itemid = $initemid")->select();
                    $weekmax_in=intval($week_data_list[0]['doubleweekmax']); 

                    $week_data_list = $Week_data->where("itemid = $outitemid")->select();
                    $weekmax_out=intval($week_data_list[0]['doubleweekmax']);  
echo $id."===".$initemid."@@@".$outitemid."-=-=-".$weekmax_in."-----".$weekmax_out."<br />";

                   
                    if ($weekmax_in>$weekmax_out){
                        $weekmax=$weekmax_in;
                    }else{
                        $weekmax=$weekmax_out;
                    }
/*                    if ($id==123){//兆维in和out都统计并减去一个合图的量
                        $weekmax=$weekmax_in+$weekmax_out;
                    }elseif ($id==124){//兆维in和out都统计并减去一个合图的量
                        $weekmax=$weekmax_in+$weekmax_out;
                    }else*/



                    if ($id==272){//兆维in和out都统计并减去一个合图的量
                        $weekmax_jian_in=$weekmax_in;
                        $weekmax_jian_out=$weekmax_out;
                    }elseif ($id==268){//兆维in和out都统计并减去一个合图的量
                        $weekmax_jian_in=$weekmax_in;
                        $weekmax_jian_out=$weekmax_out;                      
                    }else{
                        $weekmax_jian_in=0;
                        $weekmax_jian_out=0;                        
                    }          



                }else{
                    $checkitemid = intval($o['outitemid']);
                    $week_data_list = $Week_data->where("itemid = $checkitemid")->select();
                    $weekmax=$week_data_list[0]['doubleweekmax'];                      
                }

                //bgp兆维in和out都统计并减去一个合图的量
                //echo  $weekmax.'----------'.$weekmax_jian_in.'============'.$weekmax_jian_out."<br />";
                $weekmax=$weekmax-($weekmax_jian_in+$weekmax_jian_out); 
                
                $weekmax = round(($weekmax/(1024*1024)));
                $all_sum = $all_sum+$weekmax;
                
                
            }
            
            unset($data);
            $data['bandwidth']=$all_sum;
            $list=$Doubleweek->where("type_id = $type_id")->save($data);

            echo $type_name."执行成功！<br />";

        }

        
    }









    public function month_aggregated(){

        $Month = M('month');
        $Week_data = M('week_data');
        $Resource_zabbix = M('resource_zabbix2');

        $type_arr = array();
        $list = $Month->select();

        foreach ($list as $one){

            $type_id = $one['type_id'];
            $type_name = $one['type_name'];

            $port_list = $Resource_zabbix->where("type_id = $type_id")->select();
            $all_sum = 0;

            foreach ($port_list as $o){

                $id = intval($o['id']);
                $initemid = intval($o['initemid']);
                $outitemid = intval($o['outitemid']);
                
                if ($type_id!=7 && $type_id!=8 && $type_id!=9){

                    $week_data_list = $Week_data->where("itemid = $initemid")->select();
                    $weekmax_in=intval($week_data_list[0]['monthmax']); 

                    $week_data_list = $Week_data->where("itemid = $outitemid")->select();
                    $weekmax_out=intval($week_data_list[0]['monthmax']);                      

                   
                    if ($weekmax_in>$weekmax_out){
                        $weekmax=$weekmax_in;
                    }else{
                        $weekmax=$weekmax_out;
                    }
                    if ($id==513){//兆维in和out都统计并减去一个合图的量
                        $weekmax=$weekmax_in+$weekmax_out;
                    }elseif ($id==515){//兆维in和out都统计并减去一个合图的量
                        $weekmax=$weekmax_in+$weekmax_out;
                    }elseif ($id==272){//兆维in和out都统计并减去一个合图的量
                        $weekmax_jian_in=$weekmax_in;
                        $weekmax_jian_out=$weekmax_out;
                    }elseif ($id==268){//兆维in和out都统计并减去一个合图的量
                        $weekmax_jian_in=$weekmax_in;
                        $weekmax_jian_out=$weekmax_out;                      
                    }else{
                        $weekmax_jian_in=0;
                        $weekmax_jian_out=0;                        
                    }                 

                }else{
                    $checkitemid = intval($o['outitemid']);
                    $week_data_list = $Week_data->where("itemid = $checkitemid")->select();
                    $weekmax=$week_data_list[0]['monthmax'];                      
                }

                //bgp兆维in和out都统计并减去一个合图的量
                $weekmax=$weekmax-($weekmax_jian_in+$weekmax_jian_out); 
                
                $weekmax = round(($weekmax/(1024*1024)));
                $all_sum = $all_sum+$weekmax;
                
                
            }
            unset($data);
            $data['bandwidth']=$all_sum;
            $list=$Month->where("type_id = $type_id")->save($data);

            echo $type_name."执行成功！<br />";

        }

          
        
    }










public function mis_month_aggregated(){

    $Room = M('room');
    $Mis_month = M('mis_month');
    $Mis_data = M('mis_data');
    $Resource_zabbix2 = M('resource_zabbix2');

    $type_arr = array();
    $list = $Mis_month->select();

    $report_type="mis_month";
    $this->mis_history_aggregated($report_type);

    foreach ($list as $one){

        $citycode = $one['citycode'];
        $roomcode = $one['roomcode'];
        $productcode = $one['productcode'];

        $port_list = $Resource_zabbix2->where("misstatus=1 and roomcode = '".$roomcode."' and productcode = '".$productcode."' ")->select();
        $all_sum = 0;
        $all_item = 0;

        foreach ($port_list as $o){

            $id = intval($o['id']);
            $productcode = intval($o['productcode']);
            $initemid = intval($o['initemid']);
            $outitemid = intval($o['outitemid']);
            
            if ($productcode==222 ){

                $week_data_list = $Mis_data->where("itemid = $initemid")->select();
                $weekmax_in=intval($week_data_list[0]['mis_monthmax']); 

                $week_data_list = $Mis_data->where("itemid = $outitemid")->select();
                $weekmax_out=intval($week_data_list[0]['mis_monthmax']); 
               
                if ($weekmax_in>$weekmax_out){
                    $weekmax=$weekmax_in;
                }else{
                    $weekmax=$weekmax_out;
                }

// X_ZW_10506_CT_431-Ten-GigabitEthernet2/0/5
// X_ZW_10506_CT_431-Ten-GigabitEthernet1/0/48
// X-M6-10504-187-B-VRF-GigabitEthernet3/0/1
// X-B28-10504-371-A-VRF-GigabitEthernet3/0/1

                if ($id==513){//兆维in和out都统计并减去一个合图的量
                    $weekmax=$weekmax_in+$weekmax_out;
                }elseif ($id==515){//兆维in和out都统计并减去一个合图的量
                    $weekmax=$weekmax_in+$weekmax_out;
                }elseif ($id==272){//兆维in和out都统计并减去一个合图的量
                    $weekmax_jian_in=$weekmax_in;
                    $weekmax_jian_out=$weekmax_out;
                }elseif ($id==268){//兆维in和out都统计并减去一个合图的量
                    $weekmax_jian_in=$weekmax_in;
                    $weekmax_jian_out=$weekmax_out;                      
                }else{
                    $weekmax_jian_in=0;
                    $weekmax_jian_out=0;                        
                }                 

            }else{
                $checkitemid = intval($o['outitemid']);
                $week_data_list = $Mis_data->where("itemid = $checkitemid")->select();
                $weekmax=$week_data_list[0]['mis_monthmax'];                      
            }

            //bgp兆维in和out都统计并减去一个合图的量
            $weekmax=$weekmax-($weekmax_jian_in+$weekmax_jian_out); 
            
            $weekmax = round(($weekmax/(1024*1024)));
            $all_sum = $all_sum+$weekmax;
            $all_item = $all_item.",".$initemid.",".$outitemid;
            
        }
        unset($data);
        $room_list = $Room->where("roomcode = '".$roomcode."'")->find();
        
        $data['name']=$room_list['name'];
        $data['bandwidth_month']=$all_sum;
        $year=date("Y");
        $month=date('m', strtotime('-1 month'));

        $data['year']=$year;
        $data['month']=$month;
        echo "roomcode = '".$roomcode."' and productcode = '".$productcode."'===".$all_sum."(".$all_item.")"." <br>";
        $list=$Mis_month->where("roomcode = '".$roomcode."' and productcode = '".$productcode."' ")->save($data);


    }
    // $etime=date('Y-m-t', strtotime('-1 month')); //上月最后一天
    // $edate=date("Y-m-d",$etime);
    // $data_crontab['lasttime'] = $edate;
    // echo $edate."-------------------";
    // $list=$Crontab_week->where('flag="mis_month"')->save($data_crontab);
    // if (!empty($list)){
    //     echo "下一月报运行周期标记更新成功！".$edate."<br />";
    // }
      
    
}





public function mis_day_aggregated(){

    $Room = M('room');
    $Crontab_week = M('crontab_week');
    $Mis_month = M('mis_month');
    $Mis_data = M('mis_data');
    $Resource_zabbix2 = M('resource_zabbix2');

    $type_arr = array();
    $list = $Mis_month->select();

    $crontablist = $Crontab_week->where('flag="mis_day"')->find();
    $lasttime=$crontablist['lasttime'];
    $nowtime=date("Y-m-d H:i:s");
    $nowtime=strtotime($nowtime);
    $stime=strtotime($lasttime." 00:00:00");
    $etime=$stime+86400;
    $sdate=date("Y-m-d",$stime);
    $day=date("Y-m-d",strtotime("-1 day"));

    $edate=date("Y-m-d",$etime);
    $data_crontab['lasttime'] = $edate;
if ($nowtime>$etime){//超过截止时间24小时才可运行下方程序,否则每次刷新日期递增

    $report_type="mis_day";
    $this->mis_history_aggregated($report_type);

    foreach ($list as $one){

        $citycode = $one['citycode'];
        $roomcode = $one['roomcode'];
        $productcode = $one['productcode'];

        $port_list = $Resource_zabbix2->where("misstatus=1 and roomcode = '".$roomcode."' and productcode = '".$productcode."' ")->select();
        $all_sum = 0;
        $all_item = 0;

        foreach ($port_list as $o){

            $id = intval($o['id']);
            $productcode = intval($o['productcode']);
            $initemid = intval($o['initemid']);
            $outitemid = intval($o['outitemid']);
            
            if ($productcode==222 ){

                $week_data_list = $Mis_data->where("itemid = $initemid")->select();
                $weekmax_in=intval($week_data_list[0]['daymax']); 

                $week_data_list = $Mis_data->where("itemid = $outitemid")->select();
                $weekmax_out=intval($week_data_list[0]['daymax']); 
               
                if ($weekmax_in>$weekmax_out){
                    $weekmax=$weekmax_in;
                }else{
                    $weekmax=$weekmax_out;
                }

// X_ZW_10506_CT_431-Ten-GigabitEthernet2/0/5
// X_ZW_10506_CT_431-Ten-GigabitEthernet1/0/48
// X-M6-10504-187-B-VRF-GigabitEthernet3/0/1
// X-B28-10504-371-A-VRF-GigabitEthernet3/0/1

                if ($id==513){//兆维in和out都统计并减去一个合图的量
                    $weekmax=$weekmax_in+$weekmax_out;
                }elseif ($id==515){//兆维in和out都统计并减去一个合图的量
                    $weekmax=$weekmax_in+$weekmax_out;
                }elseif ($id==272){//兆维in和out都统计并减去一个合图的量
                    $weekmax_jian_in=$weekmax_in;
                    $weekmax_jian_out=$weekmax_out;
                }elseif ($id==268){//兆维in和out都统计并减去一个合图的量
                    $weekmax_jian_in=$weekmax_in;
                    $weekmax_jian_out=$weekmax_out;                      
                }else{
                    $weekmax_jian_in=0;
                    $weekmax_jian_out=0;                        
                }                 

            }else{
                $checkitemid = intval($o['outitemid']);
                $week_data_list = $Mis_data->where("itemid = $checkitemid")->select();
                $weekmax=$week_data_list[0]['daymax'];                      
            }

            //bgp兆维in和out都统计并减去一个合图的量
            $weekmax=$weekmax-($weekmax_jian_in+$weekmax_jian_out); 
            
            $weekmax = round(($weekmax/(1024*1024)));
            $all_sum = $all_sum+$weekmax;
            $all_item = $all_item.",".$initemid.",".$outitemid;
            
        }
        unset($data);
        $room_list = $Room->where("roomcode = '".$roomcode."'")->find();
        
        $data['name']=$room_list['name'];
        $data['bandwidth_day']=$all_sum;


        $data['day']=$day;
        $data['stime']=$stime;
        $data['etime']=$etime;
        echo "roomcode = '".$roomcode."' and productcode = '".$productcode."'===".$all_sum."(".$all_item.")"." <br>";
        $list=$Mis_month->where("roomcode = '".$roomcode."' and productcode = '".$productcode."' ")->save($data);


    }


    $list=$Crontab_week->where('flag="mis_day"')->save($data_crontab);
    if (!empty($list)){
        echo "下一日报运行周期标记更新成功！".$edate."<br />";
    }    

}else{
    echo "下一日报运行周期未到！".$edate."<br />";
} 


}



public function mis_week_aggregated(){

    $Room = M('room');
    $Crontab_week = M('crontab_week');
    $Mis_month = M('mis_month');
    $Mis_data = M('mis_data');
    $Resource_zabbix2 = M('resource_zabbix2');

    $type_arr = array();
    $list = $Mis_month->select();

    $crontablist = $Crontab_week->where('flag="mis_week"')->select();
    $lasttime=$crontablist[0]['lasttime'];
    $stime=strtotime($lasttime." 00:00:00");
    $etime=$stime+604800; //7天
    $sdate=date("Y-m-d",$stime);
    $edate=date("Y-m-d",$etime);

    $nowtime=date("Y-m-d H:i:s");
    $nowtime=strtotime($nowtime);

if ($nowtime>$etime){//超过截止时间24小时才可运行下方程序,否则每次刷新日期递增

    $report_type="mis_week";
    $this->mis_history_aggregated($report_type);

    foreach ($list as $one){

        $citycode = $one['citycode'];
        $roomcode = $one['roomcode'];
        $productcode = $one['productcode'];

        $port_list = $Resource_zabbix2->where("misstatus=1 and roomcode = '".$roomcode."' and productcode = '".$productcode."' ")->select();
        $all_sum = 0;
        $all_item = 0;

        foreach ($port_list as $o){

            $id = intval($o['id']);
            $productcode = intval($o['productcode']);
            $initemid = intval($o['initemid']);
            $outitemid = intval($o['outitemid']);
            
            if ($productcode==222 ){

                $week_data_list = $Mis_data->where("itemid = $initemid")->select();
                $weekmax_in=intval($week_data_list[0]['weekmax']); 

                $week_data_list = $Mis_data->where("itemid = $outitemid")->select();
                $weekmax_out=intval($week_data_list[0]['weekmax']); 
               
                if ($weekmax_in>$weekmax_out){
                    $weekmax=$weekmax_in;
                }else{
                    $weekmax=$weekmax_out;
                }

// X_ZW_10506_CT_431-Ten-GigabitEthernet2/0/5
// X_ZW_10506_CT_431-Ten-GigabitEthernet1/0/48
// X-M6-10504-187-B-VRF-GigabitEthernet3/0/1
// X-B28-10504-371-A-VRF-GigabitEthernet3/0/1

                if ($id==513){//兆维in和out都统计并减去一个合图的量
                    $weekmax=$weekmax_in+$weekmax_out;
                }elseif ($id==515){//兆维in和out都统计并减去一个合图的量
                    $weekmax=$weekmax_in+$weekmax_out;
                }elseif ($id==272){//兆维in和out都统计并减去一个合图的量
                    $weekmax_jian_in=$weekmax_in;
                    $weekmax_jian_out=$weekmax_out;
                }elseif ($id==268){//兆维in和out都统计并减去一个合图的量
                    $weekmax_jian_in=$weekmax_in;
                    $weekmax_jian_out=$weekmax_out;                      
                }else{
                    $weekmax_jian_in=0;
                    $weekmax_jian_out=0;                        
                }                 

            }else{
                $checkitemid = intval($o['outitemid']);
                $week_data_list = $Mis_data->where("itemid = $checkitemid")->select();
                $weekmax=$week_data_list[0]['weekmax'];                      
            }

            //bgp兆维in和out都统计并减去一个合图的量
            $weekmax=$weekmax-($weekmax_jian_in+$weekmax_jian_out); 
            
            $weekmax = round(($weekmax/(1024*1024)));
            $all_sum = $all_sum+$weekmax;
            $all_item = $all_item.",".$initemid.",".$outitemid;
            
        }
        unset($data);
        $room_list = $Room->where("roomcode = '".$roomcode."'")->find();
        
        $data['name']=$room_list['name'];
        $data['bandwidth_week']=$all_sum;


        $data['week_stime']=$stime;
        $data['week_etime']=$etime;
        echo "roomcode = '".$roomcode."' and productcode = '".$productcode."'===".$all_sum."(".$all_item.")"." <br>";
        $list=$Mis_month->where("roomcode = '".$roomcode."' and productcode = '".$productcode."' ")->save($data);


    }


    $data_crontab['lasttime'] = $edate;
    $list=$Crontab_week->where('flag="mis_week"')->save($data_crontab);
    if (!empty($list)){
        echo "下一周报运行周期标记更新成功！".$edate."<br />";
    }  
}else{
    echo "下一周报运行周期未到！".$edate."<br />";
} 
  
}









public function mis_doubleweek_aggregated(){

    $Room = M('room');
    $Crontab_week = M('crontab_week');
    $Mis_month = M('mis_month');
    $Mis_data = M('mis_data');
    $Resource_zabbix2 = M('resource_zabbix2');

    $type_arr = array();
    $list = $Mis_month->select();


    $crontablist = $Crontab_week->where('flag="mis_doubleweek"')->select();
    $lasttime=$crontablist[0]['lasttime'];
    $stime=strtotime($lasttime." 00:00:00");
    $etime=$stime+1209600; //14天
    $sdate=date("Y-m-d",$stime);
    $edate=date("Y-m-d",$etime);

    $nowtime=date("Y-m-d H:i:s");
    $nowtime=strtotime($nowtime);

if ($nowtime>$etime){//超过截止时间24小时才可运行下方程序,否则每次刷新日期递增

    $report_type="mis_doubleweek";
    $this->mis_history_aggregated($report_type);

    foreach ($list as $one){

        $citycode = $one['citycode'];
        $roomcode = $one['roomcode'];
        $productcode = $one['productcode'];

        $port_list = $Resource_zabbix2->where("misstatus=1 and roomcode = '".$roomcode."' and productcode = '".$productcode."' ")->select();
        $all_sum = 0;
        $all_item = 0;

        foreach ($port_list as $o){

            $id = intval($o['id']);
            $productcode = intval($o['productcode']);
            $initemid = intval($o['initemid']);
            $outitemid = intval($o['outitemid']);
            
            if ($productcode==222 ){

                $week_data_list = $Mis_data->where("itemid = $initemid")->select();
                $weekmax_in=intval($week_data_list[0]['doubleweekmax']); 

                $week_data_list = $Mis_data->where("itemid = $outitemid")->select();
                $weekmax_out=intval($week_data_list[0]['doubleweekmax']); 
               
                if ($weekmax_in>$weekmax_out){
                    $weekmax=$weekmax_in;
                }else{
                    $weekmax=$weekmax_out;
                }

// X_ZW_10506_CT_431-Ten-GigabitEthernet2/0/5
// X_ZW_10506_CT_431-Ten-GigabitEthernet1/0/48
// X-M6-10504-187-B-VRF-GigabitEthernet3/0/1
// X-B28-10504-371-A-VRF-GigabitEthernet3/0/1

                if ($id==513){//兆维in和out都统计并减去一个合图的量
                    $weekmax=$weekmax_in+$weekmax_out;
                }elseif ($id==515){//兆维in和out都统计并减去一个合图的量
                    $weekmax=$weekmax_in+$weekmax_out;
                }elseif ($id==272){//兆维in和out都统计并减去一个合图的量
                    $weekmax_jian_in=$weekmax_in;
                    $weekmax_jian_out=$weekmax_out;
                }elseif ($id==268){//兆维in和out都统计并减去一个合图的量
                    $weekmax_jian_in=$weekmax_in;
                    $weekmax_jian_out=$weekmax_out;                      
                }else{
                    $weekmax_jian_in=0;
                    $weekmax_jian_out=0;                        
                }                 

            }else{
                $checkitemid = intval($o['outitemid']);
                $week_data_list = $Mis_data->where("itemid = $checkitemid")->select();
                $weekmax=$week_data_list[0]['doubleweekmax'];                      
            }

            //bgp兆维in和out都统计并减去一个合图的量
            $weekmax=$weekmax-($weekmax_jian_in+$weekmax_jian_out); 
            
            $weekmax = round(($weekmax/(1024*1024)));
            $all_sum = $all_sum+$weekmax;
            $all_item = $all_item.",".$initemid.",".$outitemid;
            
        }
        unset($data);
        $room_list = $Room->where("roomcode = '".$roomcode."'")->find();
        
        $data['name']=$room_list['name'];
        $data['bandwidth_doubleweek']=$all_sum;


        $data['dweek_stime']=$stime;
        $data['dweek_etime']=$etime;
        echo "roomcode = '".$roomcode."' and productcode = '".$productcode."'===".$all_sum."(".$all_item.")"." <br>";
        $list=$Mis_month->where("roomcode = '".$roomcode."' and productcode = '".$productcode."' ")->save($data);


    }

    $data_crontab['lasttime'] = $edate;
    $list=$Crontab_week->where('flag="mis_doubleweek"')->save($data_crontab);
    if (!empty($list)){
        echo "下一双周报运行周期标记更新成功！".$edate."<br />";
    } 

}else{
    echo "下一双周报运行周期未到！".$edate."<br />";
}     
    
}




public function mis_history_aggregated($report_type){

    $Room = M('room');
    $Crontab_week = M('crontab_week');
    $Mis_month = M('mis_month');
    $Mis_history = M('mis_history');

    $c_list = $Crontab_week->where(" flag='mis_day' ")->find();
    $lasttime=$c_list['lasttime'];


    $month_list = $Mis_month->order(" roomcode desc")->select();
    //vnet_crontab_week
    for($i=0;$i<count($month_list);$i++){
        //$dataList[]=array('report_type'=>$report_type[$i],'mis_month_id'=>$tempmis_month_id);
        $month_list[$i]['report_type']=$report_type;
        $month_list[$i]['id']='';
        $month_list[$i]['day']=$lasttime;
    }
    $Mis_history->addAll($month_list);


}
    
    



public function user_all_changed(){
// http://211.151.5.12/zichan/index.php/Home/Crontab/user_all_changed/username/zhuwj/password/333333/formtype/hrm.html
        $User = M('user');
        $Vnetuser = M('vnetuser','no',DB_CONFIG2);
        $CustomerUser = M('vnet_user','no',DB_CONFIG3);
        $Yh_user = M('yh_user','no',DB_CONFIG4);
        $Users_users = M('users_users','no',DB_CONFIG5);


        $formtype = $_REQUEST['formtype'];
        $username = $_REQUEST['username'];
        $newpassword = $_REQUEST['password'];
        $data['password'] = $_REQUEST['password'];

        if(empty($username)){$this->error('用户名称不能为空！');}
        if(empty($newpassword)){$this->error('密码不能为空！');}


//--------------hrm密码变更
        $salt = substr(md5(time()),0,4);
        $md5newpassword = md5(md5(trim($newpassword)) . $salt);   
        $hrm_data['password'] = $md5newpassword;
        $hrm_data['salt'] = $salt;
        $hrmlist=$Vnetuser->where(' name ="'.$username.'"')->select();
        $hrm_userid=$hrmlist[0]['user_id'];
        $hrm_password=$hrmlist[0]['password'];
        

//--------------customer密码变更
        $customer_data['password'] = trim($newpassword);
//--------------newcenter密码变更
        $nms_data['password'] = trim($newpassword);
//--------------tiki密码变更
        $letters = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789./';
        $salt = '$1$';
        for ($i=0; $i<8; $i++)
            $salt .= $letters[rand(0, strlen($letters) - 1)];
        $salt .= '$';
        $hash = crypt(trim($newpassword), $salt);

        $tiki_data['hash'] = $hash;
        $tiki_data['password'] = trim($newpassword);
        $tiki_data['pass_confirm'] = time();

        if (!empty($data['password'])){

            if ($formtype=='zichan'){
                $Api = new UserApi();
                $hrm_res = $Api->updateInfo_hrm($hrm_userid, $hrm_password, $hrm_data);//hrm
                $customer_res = $CustomerUser->where(array('username'=>$username))->save($customer_data);//customer
                $nms_res = $Yh_user->where(array('username'=>$username))->save($nms_data);//nms
                $tiki_res = $Users_users->where(array('login'=>$username))->save($tiki_data);//tiki  
            }elseif ($formtype=='tiki'){
                $res = $User->where(array('username'=>$username))->save($data);

                $Api = new UserApi();
                $hrm_res = $Api->updateInfo_hrm($hrm_userid, $hrm_password, $hrm_data);//hrm
                $customer_res = $CustomerUser->where(array('username'=>$username))->save($customer_data);//customer
                $nms_res = $Yh_user->where(array('username'=>$username))->save($nms_data);//nms

            }elseif ($formtype=='nms'){
                $res = $User->where(array('username'=>$username))->save($data);

                $Api = new UserApi();
                $hrm_res = $Api->updateInfo_hrm($hrm_userid, $hrm_password, $hrm_data);//hrm
                $customer_res = $CustomerUser->where(array('username'=>$username))->save($customer_data);//customer
                $tiki_res = $Users_users->where(array('login'=>$username))->save($tiki_data);//tiki  

            }elseif ($formtype=='customer'){
                $res = $User->where(array('username'=>$username))->save($data);

                $Api = new UserApi();
                $hrm_res = $Api->updateInfo_hrm($hrm_userid, $hrm_password, $hrm_data);//hrm
                $nms_res = $Yh_user->where(array('username'=>$username))->save($nms_data);//nms
                $tiki_res = $Users_users->where(array('login'=>$username))->save($tiki_data);//tiki  

            }elseif ($formtype=='hrm'){
                $res = $User->where(array('username'=>$username))->save($data);

                $customer_res = $CustomerUser->where(array('username'=>$username))->save($customer_data);//customer
                $nms_res = $Yh_user->where(array('username'=>$username))->save($nms_data);//nms
                $tiki_res = $Users_users->where(array('login'=>$username))->save($tiki_data);//tiki   

            }else{
                $res = $User->where(array('username'=>$username))->save($data);

                $Api = new UserApi();
                $hrm_res = $Api->updateInfo_hrm($hrm_userid, $hrm_password, $hrm_data);//hrm
                $customer_res = $CustomerUser->where(array('username'=>$username))->save($customer_data);//customer
                $nms_res = $Yh_user->where(array('username'=>$username))->save($nms_data);//nms
                $tiki_res = $Users_users->where(array('login'=>$username))->save($tiki_data);//tiki            
            }

            echo "密码同步修改成功！";
        }

       

}






    
    
}