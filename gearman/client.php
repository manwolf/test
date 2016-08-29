<?php
$client = new GearmanClient ();
$client->addServer ( '127.0.0.1', 4730 ); // 本机可以直接addServer(),默认服务器端使用4730端口
$client->setCompleteCallback ( 'completeCallBack' ); // 先绑定才有效

$result1 = $client->do ( 'say', 'do' ); // do是同步进行，进行处理并返回处理结果。
$result2 = $client->doBackground ( 'say', 'doBackground' ); // 异步进行，只返回处理句柄。
$result3 = $client->addTask ( 'say', 'addTask' ); // 添加任务到队列,同步进行？通过添加task可以设置回调函数。
$result4 = $client->addTaskBackground ( 'say', 'addTaskBackground' ); // 添加后台任务到队列，异步进行？
$client->runTasks (); // 运行队列中的任务，只是do系列不需要runTask()。

echo 'result1:';
var_dump ( $result1 );
echo '<br/>';

echo 'result2:';
var_dump ( $result2 );
echo '<br/>';

echo 'result3:';
var_dump ( $result3 );
echo '<br/>';

echo 'result4:';
var_dump ( $result4 );
echo '<br/>';

// 绑定回调函数，只对addTask有效
function completeCallBack($task) {
	echo 'CompleteCallback！handle result:' . $task->data () . '<br/>';
}