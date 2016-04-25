 <?php

			include_once 'tools/paging.php';
			include_once 'base/crudCtr.php';
			class displayPagingCtr extends crudCtr {
					public function __construct() {
							$this->tablename = 'class_list';
					}
	 			function displayPaging() {
	 				$msg = new responseMsg ();
	 				$capturs = $this->captureParams ();
	 				$prefixJS = $capturs ['callback'];
	 				$token = $capturs ['token'];
	 				$newrow = $capturs ['newrow'];
	 				$page=$newrow['page'];//前端传来的页数
	 				$type=$newrow['type'];
	 				if($type=='web'){
	 				$PageSize=20;//定义电脑端20个数据为一页
	 				}
	 				if($type=='app'){
	 					$PageSize=10;//手机端10个数据为一页
	 				}
	 			
	 				$querySql="select count(*) as count from class_list ";
	 				$model = spClass ( 'class_list' );
	 				$result = $model->findSql ( $querySql );
	 				$count=$result[0]['count'];
	 				
	 				$offset= paging::showPaging ($result,$page,$PageSize,$prefixJS);
	 			
	 				$querySql="select * from class_list limit $offset,$PageSize ";
	 				$model = spClass ( 'class_list' );
	 				$result = $model->findSql ( $querySql );
	 				$msg->ResponseMsg ( 0, '查询成功', $result, 0, $prefixJS );
	 				
	 				
	 			}
	 			}
	 				
	 				
	 				
				