<?php
# 如果服务器有TestVersion.key，则运行测试环境(define测试环境)
$file_path="{$_SERVER['DOCUMENT_ROOT']}/test_key/TestVersion.key";
if(file_exists($file_path)){
	define('TestVersion', 'This is a test version.');
}else{

}
if (defined ( 'TestVersion' )) {
	Header ( "Location: http://wxh.e-teacher.cn/eTeacher_student_test/index.html" );
} else {
	Header ( "Location: http://wxh.e-teacher.cn/eTeacher/index.html" );
}