<?php
/**
 * 一个用于Mysql数据库的分页类
 *
 * @author      Avenger <avenger@php.net>
 * @version     1.0
 * @lastupdate  2003-04-08 11:11:33
 *
 *
 * 使用实例:
 * $p = new show_page;		//建立新对像
 * $p->file="ttt.php";		//设置文件名，默认为当前页
 * $p->pvar="pagecount";	//设置页面传递的参数，默认为p
 * $p->setvar(array("a" => '1', "b" => '2'));	//设置要传递的参数,要注意的是此函数必须要在 set 前使用，否则变量传不过去
 * $p->set(20,2000,1);		//设置相关参数，共三个，分别为'页面大小'、'总记录数'、'当前页(如果为空则自动读取GET变量)'
 * $p->output(0);			//输出,为0时直接输出,否则返回一个字符串
 * echo $p->limit();		//输出Limit子句。在sql语句中用法为 "SELECT * FROM TABLE LIMIT {$p->limit()}";
 *
 */


class page {

    /**
     * 页面输出结果
     *
     * @var string
     */
	var $output;

    /**
     * 使用该类的文件,默认为 PHP_SELF
     *
     * @var string
     */
	var $file;

    /**
     * 页数传递变量，默认为 'p'
     *
     * @var string
     */
	var $pvar = "page";

    /**
     * 页面大小
     *
     * @var integer
     */
	var $psize;

    /**
     * 当前页面
     *
     * @var ingeger
     */
	var $curr;

    /**
     * 要传递的变量数组
     *
     * @var array
     */
	var $varstr;

    /**
     * 总页数
     *
     * @var integer
     */
    var $tpage;

    /**
     * 分页设置
     *
     * @access public
     * @param int $pagesize 页面大小
     * @param int $total    总记录数
     * @param int $current  当前页数，默认会自动读取
     * @return void
     */
    function set($pagesize=10,$total,$current=false,$showSelect=false) {
		global $HTTP_SERVER_VARS,$_GET;
		
		$this->tpage = ceil($total/$pagesize);
		if (!$current) {$current = $_GET[$this->pvar];}
		if ($current>$this->tpage) {$current = $this->tpage;}
		if ($current<1) {$current = 1;}

		$this->curr  = $current;
		$this->psize = $pagesize;

		if (!$this->file) {$this->file = $HTTP_SERVER_VARS['PHP_SELF'];}



			if($showSelect)
			{  if ($this->tpage>1) {
					$this->output .= "共:<font color=>".$total."</font>&nbsp;".$this->curr."/".$this->tpage." &nbsp;";
				}else{
					$this->output .= "共:<font color=>".$total."</font>条 ";
				}
			}
			if ($current>1) {
				if ($this->curr==1){
							$this->output.='&nbsp;|<&nbsp;';
				}else{
							$this->output.='<a href='.$this->file.'?'.$this->pvar.'='.(1).($this->varstr).' title="首页">&nbsp;|<</a>&nbsp;';
				}
			}
		if ($this->tpage > 1) {
            $start	= floor(($current-1)/5)*5;
			if ($start<1)			{$start=1;}
            $end	= $start+4;
			
            if ($start>1){
            	$start=$start+1;
            	$end=$end+1;
            	}

			if ($current>5) {
				//$this->output.='<a href='.$this->file.'?'.$this->pvar.'='.($start-5).($this->varstr).' title="前五页"><<</a>&nbsp;';//&lt;&lt;&lt;
			}
            if ($current>1) {
				$this->output.='<a href='.$this->file.'?'.$this->pvar.'='.($current-1).($this->varstr).' title="上一页"><</a>&nbsp;';//&lt;&lt;
			}



			//if (-5)
            
            

            

           if ($end>$this->tpage)	{$end=$this->tpage;} 


            for ($i=$start; $i<=$end; $i++) {
                if ($current==$i) {
                    $this->output.='<font color="">'.$i.'</font>&nbsp;';    //输出当前页数
                } else {
                    $this->output.='<a href="'.$this->file.'?'.$this->pvar.'='.$i.$this->varstr.'">['.$i.']</a>&nbsp;';    //输出页数
                }
            }


      if ($current<$this->tpage) {
				$this->output.='<a href='.$this->file.'?'.$this->pvar.'='.($current+1).($this->varstr).' title="下一页">></a>&nbsp;';//&gt;&gt;
			}
            if ($this->tpage>5 && ($this->tpage-$current)>=5 ) {
				//$this->output.='<a href='.$this->file.'?'.$this->pvar.'='.($end+1).($this->varstr).' title="下五页">>></a>';//&gt;&gt;&gt;
			}

		    if ($this->curr==$this->tpage){
					$this->output.='&nbsp;末页';
	  		}else{
	  		  $this->output.='<a href='.$this->file.'?'.$this->pvar.'='.($this->tpage).($this->varstr).' title="末页">>|</a>&nbsp;';

		    }


			if($showSelect)
			{
				$this->output .= "&nbsp;&nbsp;快速选择&nbsp;<select name=pp onChange=\"window.location.href='".$this->file."?".$this->pvar."='+this.value+'".$this->varstr."';\">";//<li class=no>
				
				//$this->output .= "<option value=\"".$this->curr."\">".$this->curr."</option>";
				
				for($i=1;$i<=$this->tpage;$i++)
				{
					if ($i==$this->curr){
							$this->output .= "<option value=$i selected>$i</option>";
						}else{
							$this->output .= "<option value=$i>$i</option>";
						}
				}
				
				$this->output .= "</select>";//</li>
				
			}


			if($showSelect)
			{
				//$this->output .= "&nbsp;&nbsp;<select style='color:red' name='pageRecord' onchange=\"window.location.href='".$this->file."?"."pageRecord="."'+this.value+'".$this->varstr."';\"><option value=".$pagesize.">".$pagesize."</option><option value=8>8</option><option value=10>10</option><option value=15>15</option><option value=20>20</option><option value=25>25</option><option value=30>30</option><option value=50>50</option><option value=80>80</option><option value=100>100</option></select>记录/页";
			}
		}
	}

    /**
     * 要传递的变量设置
     *
     * @access public
     * @param array $data   要传递的变量，用数组来表示，参见上面的例子
     * @return void
     */	
	function setvar($data) {
		
		foreach ($data as $k=>$v) {
		//	$this->varstr.='&amp;'.$k.'='.urlencode($v);
			$this->varstr.='&amp;'.$k.'='.$v;
			
		}
	}

    /**
     * 分页结果输出
     *
     * @access public
     * @param bool $return 为真时返回一个字符串，否则直接输出，默认直接输出
     * @return string
     */
	function output($return = false) {
		if ($return) {
			return $this->output;
		} else {
			echo $this->output;
		}
	}

    /**
     * 生成Limit语句
     *
     * @access public
     * @return string
     */
    function limit() {
		return (($this->curr-1)*$this->psize).','.$this->psize;
	}

} //End Class
?>