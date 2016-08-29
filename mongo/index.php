<?php
// 链接服务器
$m = new MongoClient ();

// 选择一个数据库
$db = $m->comedy;

// 选择一个集合（ Mongo 的“集合”相当于关系型数据库的“表”）
$collection = $db->cartoons;

// 插入一个文档（译注：“文档”相当于关系型数据库的“行”）
$document = array (
		"title" => "Calvin and Hobbes",
		"author" => "Bill Watterson" 
);
$collection->insert ( $document );

// 添加另一个文档，它的结构与之前的不同
$document = array (
		"title" => "XKCD",
		"online" => true 
);
$collection->insert ( $document );

// 查询集合中的所有文档
$cursor = $collection->find ();

// 遍历查询结果
foreach ( $cursor as $document ) {
	echo $document ["title"] . "\n";
}