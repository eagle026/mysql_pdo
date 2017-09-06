<?php
include ('config.mysql.php');
include ('Pdos.class.php');
$pdo=new Pdos($mysql_array);
$insert=array(
    'table'=>'usrs',
    'key'=>"companyid,wid,clientid,realname,phone,status",
    'val'=>"400400|1001|27346|'测试客服'|15602311588|3",
);
$table=$insert['table'];
$key=$insert['key'];
$value=$insert['val'];
$id=$pdo->insert($table,$key,$value);

$delete=array(
    'table'=>'usrs',
    'where'=>'id>18',
);
$table=$delete['table'];
$var=$delete['where'];
//$pdo->delete($table,$var);

$update=array(
    'table'=>'usrs',
    'key'=>"companyid,wid,clientid,realname,phone,status",
    'val'=>"400400|1001|27346|'测试客服'|15602311588|9",
    'where'=>'id=8'
);
$table=$update['table'];
$key=$update['key'];
$value=$update['val'];
$var=$update['where'];
//$pdo->update($table,$key,$value,$var);

$select=array(
    'table'=>'usrs',
    'key'=>'id,companyid,wid,clientid,realname,phone,status',
    'where'=>'id>9',
    'order'=>'id desc',
    'limit'=>'3',

);
$table=$select['table'];
$key=$select['key'];
$vars=$select['where'];
$order=$select['order'];
$limit=$select['limit'];
 $rows=$pdo->select($table,$key,$vars,$order,$limit);
 var_dump($rows);


