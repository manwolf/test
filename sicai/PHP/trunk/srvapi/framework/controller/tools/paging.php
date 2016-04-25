<?php
include_once 'responseMsg.php';
class paging{
	static function showPaging($result,$page,$PageSize,$prefixJS){
					$count=$result[0]['count'];
					if($count==0)
					{//如果总是为0则页数显示为零
					$page_count=0;
					
					}
					if($count % $PageSize)
						{//是否能整除，如果不能整除则+1
					$page_count=(int)($count/$PageSize)+1;
					
					}
					else
						{
						$page_count=$count/$PageSize;
			
					}
					#前端传来的页数
					if($page<=1 || $page==''){
						$page=1;
					}
				if($page>=$page_count){
						$page=$page_count;
					}
			
				 $offset=($page-1)*$PageSize;
				
				 return $offset ;
	}
}