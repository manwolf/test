<?php
$worker = new GearmanWorker ();
$worker->addServer ();
$worker->addFunction ( 'say', function (GearmanJob $job) {
	$workload = $job->workload (); // 接收client传递的数据
	echo 'receive data:' . $workload . PHP_EOL;
	return strrev ( $workload ); // 仅作反转处理
} );

// 无际循环运行，gearman内部已有处理，不会出现占用过高死掉的情况
while ( $worker->work () ) {
	if ($worker->returnCode () !== GEARMAN_SUCCESS) {
		echo 'error' . PHP_EOL;
	}
}