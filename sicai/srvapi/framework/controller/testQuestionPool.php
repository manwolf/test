<?php
include_once 'base/crudCtr.php';
/**
 * 功能：E题库
 * 作者： 黄东
 * 日期：2015年09月14日
 */
class testQuestionPool extends crudCtr {
	public function __construct() {
		$this->tablename = 'test_question_details';
	}
	// 查询客户是否已注册，未注册提示客户注册，已注册返回姓名，年级，返回上次测验成绩（第一次测验完成之后）。
	function demandregister() {
		$msg = new responseMsg ();
		$capturs = $this->captureParams ();
		$prefixJS = $capturs ['callback'];
		// $token = $capturs ['token'];
		$newrow = $capturs ['newrow'];
		$user_tid = $newrow ['user_tid'];
		// $this->aaa($user_tid);
		// exit;
		// 当user_tid为空时，即客户没有登录时
		if ($user_tid == '') {
			$msg->ResponseMsg ( 1, '您还没有登录，请您先登录！', false, 1, $prefixJS );
			exit ();
		}
		
		// test_state 0为计数测试 1为无限测试 test_number 学生测试次数 没有课时且<2时可以继续测试
		$querySql = 'select s.*,u.test_state,u.test_number,u.test_state from test_user_scores s,user_info u 
				where s.user_tid=u.tid and user_tid=' . $user_tid . ' order by s.create_time desc limit 1';
		
		$model = spClass ( $this->tablename );
		if ($result = @$model->findSql ( $querySql )) {
			$msg->ResponseMsg ( 0, '查询成功', $result, $total_page, $prefixJS );
		} else {
			// 0 防止弹窗
			$msg->ResponseMsg ( 0, '该学生没有测试成绩返回！', $result, $total_page, $prefixJS );
		}
		
		return true;
	}
	// 根据年级，教材，学期等从题库随机取10道题 题库题目数》=30
	function getForTest() {
		$msg = new responseMsg ();
		$capturs = $this->captureParams ();
		$prefixJS = $capturs ['callback'];
		// $token = $capturs ['token'];
		$newrow = $capturs ['newrow'];
		$test_grade = $newrow ['test_grade']; // 年级类型 小学 初中高中
		$teaching_material_type = $newrow ['teaching_material_type']; // 教材类型
		$test_semester = $newrow ['test_semester']; // 学期 上
		$city = $newrow ['city']; // 城市
		$user_tid = $newrow ['user_tid'];
		$test_class = $newrow ['test_class']; // 年级 初中7年级
		
		if ($user_tid == '') {
			$msg->ResponseMsg ( 1, '缺少学生ID！', $result, 0, $prefixJS );
			exit ();
		}
		// 查询学生测试状态
		$querySql = 'select test_number,test_state from user_info where tid=' . $user_tid;
		$model = spClass ( $this->tablename );
		$result = $model->findSql ( $querySql );
		// test_state测试状态 0 计数测试 1无限测试
		if (1 == $result [0] ['test_state']) {
			// 生成10道题
			$this->generateTest ( $test_grade, $teaching_material_type, $test_semester, $city, $test_class, $user_tid );
			exit ();
		}
		if (0 == $result [0] ['test_state']) {
			// 学生测试次数 <2
			if ($result [0] ['test_number'] < 2) {
				// 生成10道题
				$this->generateTest ( $test_grade, $teaching_material_type, $test_semester, $city, $test_class, $user_tid );
				// 学生测试次数+1
				$updateSql = 'update user_info set test_number=test_number+1 where tid=' . $user_tid;
				$model = spClass ( $this->tablename );
				$resultup = $model->runSql ( $updateSql );
				exit ();
			} else {
				$msg->ResponseMsg ( 1, '您已参与过两次测试，若要继续请购买套餐课程！', $result, 0, $prefixJS );
				exit ();
			}
		}
		
		return true;
	}
	// 将用户测试信息储存 同时判断用户每题答案正确与否
	function saveTestInformation() {
		$msg = new responseMsg ();
		$capturs = $this->captureParams ();
		$prefixJS = $capturs ['callback'];
		// $token = $capturs ['token'];
		$newrow = $capturs ['newrow'];
		// user_test_num新增时加入测试编号
		// 接收给用户生成的10道题目 及用户选择的答案
		// 插入10条记录
		// 待定
		// { "0": { "tid": 9, "userAnswer": "B" },
		// "1": { "tid": 10, "userAnswer": "A" },
		// "2": { "tid": 57, "userAnswer": "B" },
		// "3": { "tid": 58, "userAnswer": "D" },
		// "4": { "tid": 59, "userAnswer": "" },
		// "5": { "tid": 60, "userAnswer": "" },
		// "6": { "tid": 61, "userAnswer": "B" },
		// "7": { "tid": 63, "userAnswer": "B" },
		// "8": { "tid": 64, "userAnswer": "D" },
		// "9": { "tid": 65, "userAnswer": "B" },
		// "total_tid": 13, "userTid": "2", "userToken": "UCVWJAJjVG0JJwc8An4Ffg==" }
		
		$json_string = $newrow ['test_information']; // 接收JSON数组
		                                             // echo $json_string;
		                                             // // echo aaa;
		$json = stripslashes ( $json_string ); // 去斜杠
		                                       // $json_string='{"id":1,"name":"foo","email":"foo@foobar.com","interest":["wordpress","php"]} ';
		                                       // $obj=json_decode($json);
		                                       // print_r($json);
		$result = (json_decode ( $json, true )); // 转为关联数组
		                                         // print_r ($result);
		                                         // print_r ($result[0]['tid']);
		                                         // exit;
		                                         
		// 根据用户选择的答案计算用户成绩 和 击败多少人
		$score = 0;
		for($i = 0; $i < 10; $i ++) {
			$querySql = 'select tid,test_right_answers from test_question_details where tid=' . $result [$i] ['tid'];
			$model = spClass ( $this->tablename );
			$resultqu = $model->findSql ( $querySql );
			// print_r($resultqu) ;
			if ($resultqu [0] ['test_right_answers'] == $result [$i] ['userAnswer']) {
				// 每一个正确答案加1
				$score = $score + 1;
				// 将学生选择答案存入
				$updateSql = 'update test_user_information set judge_type=0 , user_correct_answer= "' . $result [$i] ['userAnswer'] . '"
        		 		where test_user_scores_tid=' . $result ['total_tid'] . ' and test_question_details_tid=' . $result [$i] ['tid'];
				$model = spClass ( $this->tablename );
				$resultu = $model->runSql ( $updateSql );
			} else {
				// 将学生选择答案存入
				$updateSql = 'update test_user_information set judge_type=1 , user_correct_answer= "' . $result [$i] ['userAnswer'] . '"
        		 		where test_user_scores_tid=' . $result ['total_tid'] . ' and test_question_details_tid=' . $result [$i] ['tid'];
				$model = spClass ( $this->tablename );
				$resultu = $model->runSql ( $updateSql );
			}
			// echo $resultqu[0]['tid'].'----'.$resultqu[0]['test_right_answers'].'<br>';
		}
		// 用户分数 每题10分
		$score = $score * 10;
		// echo $score;
		
		// 根据该成绩查询 所对应的击败人数范围
		$querySql = 'select beat_number_min,beat_number_max 
				from test_score_beat_number where score_min <=' . $score . ' and score_max >=' . $score;
		$model = spClass ( $this->tablename );
		$results = $model->findSql ( $querySql );
		// 根据范围随机生成一个 击败比例
		$f = $results [0] ['beat_number_min'];
		$m = $results [0] ['beat_number_max'];
		$random = $this->createRandID ( $f, $m );
		// 合并到一个数组
		$resultend = array (
				'score' => $score,
				'random' => $random 
		);
		// 储存用户此次测试成绩 及击败人数
		$updateSql = 'update test_user_scores set test_scores=' . $score . ',beat_number=' . $random . ' ,test_user_right_num=' . ($score / 10) . '
       		where tid=' . $result ['total_tid'];
		$model = spClass ( $this->tablename );
		$resultup = $model->runSql ( $updateSql );
		$msg->ResponseMsg ( 0, 'success', $resultend, 0, $prefixJS );
		
		// //根据用户分数计算用户击败学生百分比
		// switch ($score) {
		
		// case $score >= $results[0]['score_min'] && $score <= $results[0]['score_max'] :
		
		// break;
		// case $score >= 10 && $score <=20 :
		
		// break;
		// case $score >= 21 && $score <= 40 :
		
		// break;
		// case $score >= 41 && $score <= 60 :
		
		// break;
		// case $score >= 61 && $score <= 80 :
		
		// break;
		// case $score >= 81 && $score <= 99 :
		
		// break;
		// case $score >= 81 && $score <= 100 :
		
		// break;
		// default :
		// echo "成绩输入错误<br>";
		// }
		
		return true;
	}
	
	// // 查询测试的历史记录
	// 查询最近时间 学生的测试题 判断 题目总数 学生答对总数 击败学生百分比
	function queryhistoricalrecord() {
		$msg = new responseMsg ();
		$capturs = $this->captureParams ();
		$prefixJS = $capturs ['callback'];
		// $token = $capturs ['token'];
		$newrow = $capturs ['newrow'];
		$user_tid = $newrow ['user_tid'];
		if ($user_tid == '') {
			$msg->ResponseMsg ( 1, '缺少user_tid!', $result, 0, $prefixJS );
			exit ();
		}
// 		//查询学生测试权限
// 		$querySql = 'select test_number,test_state from user_info where tid=' . $user_tid;
// 		$model = spClass ( $this->tablename );
// 		$result = $model->findSql ( $querySql );
// 		// test_state测试状态 0 计数测试 1无限测试
// 		if(0==$result[0]['test_state']){
// 			$msg->ResponseMsg ( 1, '您好！若要查看测试结果请先购买套餐课程！', $result, 0, $prefixJS );
// 			exit ();
// 		}
		// select max(time) as abc,id FROM table group by id
		// 查询学生每次测试基本信息 按时间降序
		$querySql = 'select s.*,t.teaching_material_type,t.test_semester,t.test_grade,t.test_class
				 from test_question_types t  left join  test_user_scores s on 
				 t.tid=s.test_question_types_tid 
				left join  test_user_information i on s.tid=i.test_user_scores_tid 
				where  s.user_tid=' . $user_tid . ' group by s.tid order by create_time desc';
		$model = spClass ( $this->tablename );
		if ($result = $model->findSql ( $querySql )) {
			$msg->ResponseMsg ( 0, 'secsess', $result, 0, $prefixJS );
		} else {
			$msg->ResponseMsg ( 1, '您还没有参与过测试！', $result, 0, $prefixJS );
		}
		
		return true;
	}
	// 查询测试的历史记录详情
	function queryhistoricalrecordDetails() {
		$msg = new responseMsg ();
		$capturs = $this->captureParams ();
		$prefixJS = $capturs ['callback'];
		// $token = $capturs ['token'];
		$newrow = $capturs ['newrow'];
		$user_tid = $newrow ['user_tid'];
		$test_user_scores_tid = $newrow ['test_user_scores_tid']; // 测试总ID 成绩ID
		if ($user_tid == '' || $test_user_scores_tid == '') {
			$msg->ResponseMsg ( 0, '缺少学生id 或  考试总ID', $result, 0, $prefixJS );
			exit ();
		}
// 		//查询学生测试权限
// 		$querySql = 'select test_number,test_state from user_info where tid=' . $user_tid;
// 		$model = spClass ( $this->tablename );
// 		$result = $model->findSql ( $querySql );
// 		// test_state测试状态 0 计数测试 1无限测试
// 		if(0==$result[0]['test_state']){
// 			$msg->ResponseMsg ( 1, '您好！若要查看测试结果请先购买套餐课程！', $result, 0, $prefixJS );
// 			exit ();
// 		}
		// 查询对应的试题
		$querySql = 'select d.* ,i.user_correct_answer,i.judge_type,t.teaching_material_type,t.test_semester,t.test_grade,t.test_class
				from test_user_information i,test_question_details d,test_question_types t
				where d.tid=i.test_question_details_tid and d.test_question_types_tid=t.tid and i.test_user_scores_tid=' . $test_user_scores_tid . ' 
						and i.user_tid=' . $user_tid . ' order by d.tid asc';
		$model = spClass ( $this->tablename );
		$result = $model->findSql ( $querySql );
		// print_r($result);
		// echo $result[0]['tid'];
		for($i = 0; $i < 10; $i ++) {
			// 查询每道题的答案
			// echo $result[$i]['tid'];
			// exit;
			// $querySql='select a.* from test_user_information i,test_alternative_answers a
			// where a.test_question_details_tid=i.test_question_details_tid
			// and i.user_tid='.$user_tid.' and i.test_user_scores_tid='.$test_user_scores_tid;
			$querySql = 'select test_question_details_tid,answers_options,answers_options_content 
						from test_alternative_answers where test_question_details_tid =' . $result [$i] ['tid'];
			$model = spClass ( $this->tablename );
			$resultquery = $model->findSql ( $querySql );
			// 将每道题的答案插入到每道题里
			$result [$i] ['answers_options'] = $resultquery;
		}
		// $result[]=$resultquery;
		$msg->ResponseMsg ( 0, 'secsess', $result, 0, $prefixJS );
		return true;
	}
	
	// PHP获取一组随机数字不重复
	function createRandID($f, $m) {
		// 注意，要先声明一个空数组，否则while里的in_array会报错
		$querySql = '';
		$arr = array ();
		// 使用while循环，只要不够1个就永远循环 不会死循环
		while ( count ( $arr ) < 1 ) {
			// 产生一个随机数
			$a = rand ( $f, $m );
			// 判断：如果产生的随机数不再数组里就赋值到数组里
			// 主要避免产生重复的数字
			if (! in_array ( $a, $arr )) {
				// 把随机数赋值到数组里
				$arr [] = $a;
			}
		}
		// 返回产生的随机数字
		return implode ( $arr, ',' );
	}
	// 自动生成10道测试题 内部调用 返回题目及对应的答案
	function generateTest($test_grade, $teaching_material_type, $test_semester, $city, $test_class, $user_tid) {
		$msg = new responseMsg ();
		$capturs = $this->captureParams ();
		$prefixJS = $capturs ['callback'];
		// $token = $capturs ['token'];
		$newrow = $capturs ['newrow'];
		// 查询同一类型题库最大ID和最小ID confirm_submit=1客服已提交试题
		$querySql = 'select count(d.tid) as sum,max(d.tid) as max,min(d.tid) as min,d.test_question_types_tid
				from   test_question_types t left join test_question_details d  on  t.tid=d.test_question_types_tid
				where confirm_submit=1  ';
		if ($test_grade != '') {
			$querySql = $querySql . ' and t.test_grade="' . $test_grade . '"';
		}
		if ($teaching_material_type != '') {
			$querySql = $querySql . ' and t.teaching_material_type="' . $teaching_material_type . '"';
		}
		if ($test_semester != '') {
			$querySql = $querySql . ' and t.test_semester="' . $test_semester . '"';
		}
		if ($city != '') {
			$querySql = $querySql . ' and t.test_city="' . $city . '"';
		}
		if ($test_class != '') {
			$querySql = $querySql . ' and t.test_class="' . $test_class . '"';
		}
		
		// where t.test_grade="'.$test_grade.'" and t.teaching_material_type="'.
		// $teaching_material_type.'" and t.test_semester="'.$test_semester.'" and t.test_city="'.$city.'" and t.test_class='.
		// $test_class;
// 		echo $querySql;
		$model = spClass ( $this->tablename );
		$resulqu = $model->findSql ( $querySql );
		// echo $resulqu[0]['test_question_types_tid'];
		// echo $resulqu[0]['max'];
		// echo $resulqu[0]['min'];
		// echo $resulqu[0]['sum'];
		// $msg->ResponseMsg ( 0, '1', $resulqu, 0, $prefixJS );
		// 少于三十道题的题库不予测试
		if ($resulqu [0] ['sum'] < 30) {
			$msg->ResponseMsg ( 1, '该题库题目数少于三十！', $result, 0, $prefixJS );
			return;
		}
		
		// 根据题库类型 随机生成10道题 --当题库超过三十道题 会随机十道题 confirm_submit=1 已提交的题目
		$querySql = 'SELECT * FROM
		test_question_details WHERE confirm_submit=1 and test_question_types_tid=' . $resulqu [0] ['test_question_types_tid'] . ' and tid >=
		((SELECT MAX(tid) FROM test_question_details )-(SELECT MIN(tid) FROM test_question_details)) * RAND() + (SELECT MIN(tid) FROM test_question_details)
		LIMIT 10';
		$model = spClass ( $this->tablename );
		$result = $model->findSql ( $querySql );
		// echo count($result);
		// $msg->ResponseMsg ( 0, 'success', $result, 0, $prefixJS );
		// echo $result[0]['tid'];
		// exit;
		// 若生成了随机10道题
		if (10 == count ( $result )) {
			
			// 添加用户测试记录
			$addSql = 'insert test_user_scores set user_tid=' . $user_tid . ',test_questions_num=' . count ( $result ) . ' ,test_question_types_tid=' . $resulqu [0] ['test_question_types_tid'];
			$model = spClass ( $this->tablename );
			$resultadd = $model->runSql ( $addSql );
			// 新增后立刻取出最后一条记录 id 仅对当前进程
			$sql = 'select last_insert_id() as test_user_scores_tid';
			$model = spClass ( $this->tablename );
			$resultsql = $model->findSql ( $sql );
			$test_user_scores_tid = $resultsql [0] ['test_user_scores_tid'];
			// $result [] ['total_tid'] = $resultsql;
			// $result []=$resultsql;
			// array_splice($result,0,0,$resultsql);
			// 储存生成的10道题ID
			for($i = 0; $i < 10; $i ++) {
				// echo $i;
				// $a= $result[$i]['tid'];
				// 将总ID插入数组
				$result [$i] ['scores_tid'] = $resultsql;
				
				$addSql = 'insert  test_user_information set user_tid=' . $user_tid . ' 
						,test_question_details_tid=' . $result [$i] ['tid'] . ',test_user_scores_tid=' . $test_user_scores_tid;
				// echo $addSql;
				$model = spClass ( $this->tablename );
				$resulta = $model->runSql ( $addSql );
				// 查询10道题每题对应的答案
				$querySql = 'select test_question_details_tid,answers_options,answers_options_content 
						from test_alternative_answers where test_question_details_tid = ' . $result [$i] ['tid'] . ' order by answers_options asc';
				$model = spClass ( $this->tablename );
				$resultqu = $model->findSql ( $querySql );
				//
				//
				// 将每题的答案合并到各个题目组
				$result [$i] ['answers'] = $resultqu;
				// foreach ($a as $k=>$v) {
				// $b[$k]=$a[$v];
				// }
				// $result[0]['class_disc']=$resultsd;
				// $result [$i]['scores_tid'] = $resultsql;
				// $result [$i]=$resultsql;
			}
			
			$msg->ResponseMsg ( 0, 'success', $result, 0, $prefixJS );
			return true;
		} else {
			$msg->ResponseMsg ( 1, '获取试题失败！请重新获取！', $result, 0, $prefixJS );
			return false;
		}
		
		return true;
	}
	// 判断用户测试权限 内部接口 用户登录时调用
	function testingAuthority($user_tid) {
		$msg = new responseMsg ();
		$capturs = $this->captureParams ();
		$prefixJS = $capturs ['callback'];
		// $token = $capturs ['token'];
		$newrow = $capturs ['newrow'];
		// 判断用户是否正在进行付费课程 order_state !=0 不计算已完成订单 order_type!=0不计算免费
		$querySql = 'select count(c.user_confirm)*2  as userClassSurplusNum
					   from user_info u,order_list o,class_list c
					   where  u.tid=o.user_tid and o.tid=c.order_tid and o.order_state !=0 and o.order_type!=0 and  c.user_confirm=0 and u.tid=' . $user_tid;
		$model = spClass ( $this->tablename );
		$result = $model->findSql ( $querySql );
		
		// 如果有付费剩余课时
		if ($result [0] ['userClassSurplusNum'] > 0) {
			// 修改学生测试状态为1 无限测试
			$updateSql = 'update user_info set test_state=1 where tid=' . $user_tid;
			$model = spClass ( $this->tablename );
			$result = $model->runSql ( $updateSql );
			
			return;
		} 		// 如果没有付费剩余课时
		else {
			
			date_default_timezone_set ( 'PRC' ); // 设置时区
			$time = time (); // 获取当前时间
			                 // 获取距离当前最近完成订单的时间
			                 // 获取用户最后一个付费订单
			                 // 刚购买未排课 则为时间最近的 （这样就可以测试）
			$querySql = 'select * from order_list 
					where order_type!=0 and order_type!=2 and user_tid=' . $user_tid . ' 
							order by create_time desc limit 1';
			$model = spClass ( $this->tablename );
			$result = $model->findSql ( $querySql );
			// 如果没有订单
			if (! $result) {
				// 计次测试
				$updateSql = 'update user_info set test_state=0 where tid=' . $user_tid;
				$model = spClass ( $this->tablename );
				$result = $model->runSql ( $updateSql );
				return;
			}
			// 判断最后完成订单时间是否与当前时间在 7天内
			
			if ($time - $result [0] ['order_end_time'] <= 7) {
				// 修改学生测试状态为1 无限测试
				$updateSql = 'update user_info set test_state=1 where tid=' . $user_tid;
				$model = spClass ( $this->tablename );
				$result = $model->runSql ( $updateSql );
				
				return;
			}
			// 如果不在上完课七天内
			if ($time - $result [0] ['order_end_time'] > 7) {
				// 修改学生测试状态为0 限次测试
				$updateSql = 'update user_info set test_state=0 where tid=' . $user_tid;
				$model = spClass ( $this->tablename );
				$result = $model->runSql ( $updateSql );
				return;
			}
		}
		return true;
	}
}