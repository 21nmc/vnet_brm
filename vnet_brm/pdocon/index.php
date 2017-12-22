<?php
	function testsql(){
		header("Content-type:text/html;charset=gbk");

        $servname="211.151.2.84";
        $conninfo=array( "Database"=>"fmdb", "UID"=>"21vianet", "PWD"=>"123456");
        $conn=sqlsrv_connect($servname, $conninfo);

        if($conn) echo "conect success<br>";
        else echo "connect failed<br>";

        $sql="select * from tbl_cur_alm where AlmLevel=4";
        $db=sqlsrv_query($conn, $sql);
        while($row=sqlsrv_fetch_array($db))
        {
            echo $row["AlmLevel"].'<br>';
            echo $row["ExtendInfo"].'<br>';//扩展信息 槽位
            echo date("Y-m-d H:i",$row["OccurTime"]) .'<br>';
            echo date("Y-m-d H:i",$row["OccurUtc"]) .'<br>';
            echo date("Y-m-d H:i",$row["OccurDst"]) .'<br>';
            echo $row["TrailName"].'<br>';//网源
            echo $row["IsCleared"].'<br>';

        }



    }

    testsql();
?>
