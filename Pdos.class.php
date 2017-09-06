<?php
//创建类使用PDO实现
class Pdos {
    private $type;
    private $host;
    private $port;
    private $charset;
    private $dbname;
    private $user;
    private $pass;
    private $pdo;
    public function __construct($arr=array()){
        $this->type=isset($arr['type'])?$arr['type']:'mysql';
        $this->host=isset($arr['host'])?$arr['host']:'127.0.0.1';
        $this->port=isset($arr['port'])?$arr['port']:'3306';
        $this->charset=isset($arr['charset'])?$arr['charset']:'utf8';
        $this->dbname=isset($arr['dbname'])?$arr['dbname']:'test';
        $this->user=isset($arr['user'])?$arr['user']:'root';
        $this->pass=isset($arr['pass'])?$arr['pass']:'root';
        $this-> db_init();
        //设置异常模式
        $this->pdo->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
    }

    private function db_init(){
        try{
            $this->pdo=new PDO("$this->type:host=$this->host;port=$this->port;charset=$this->charset;dbname=$this->dbname",$this->user,$this->pass);
        }catch(PDOException $e){
            $this->message($e);
        }
    }

    /**
     * PDOException对象 错误提示
     * @param  object $e PDOException对象
     * @return 给出错误提示，并终止
     */
    private  function message($e,$sql=0){
       $info=array(
           "code"=>500,
           "errFile"=>$e->getfile(),
           "errLine"=>$e->getline(),
           "errSql"=>$sql,
           "errInfo"=>$e->getMessage()
         // "errInfo"=>iconv('gbk','utf-8',$e->getMessage())
       );
        exit(json_encode($info));
    }

    /***
     * @param $sql  sql
     * @return mixed  error:return info  success:return affected_rows
     */
    private function exec($sql){
        try{
            return $this->pdo->exec($sql);
        }catch(PDOException $e){
            $this->message($e,$sql);
        }
    }

    /***
     * @param $table  table
     * @param $key  "key1,key2,key3,key4"
     * @param $value  "value1|value2|value3|value4";
     * @return mixed
     */
    public function insert($table,$key,$value) {
        global $tbl;
        $table = $tbl.$table;
        $v=explode("|",$value);
        for($i=0;$i<count($v);$i++){
            $xxx.="\"".(substr($v[$i],0,7)=="content" ? $_POST[$v[$i]] :$v[$i])."\"";
            if($i<count($v)-1)$xxx.=",";
        }
        $sql="insert into $table ($key)values($xxx)";
        return $this->exec($sql);
    }

    /***
     * @return mixed error:info success:lastInsertId
     */
    public function lastInsertId(){
        try{
            return $this->pdo->lastInsertId();
        }catch(PDOException $e){
            $this->message($e);
        }
    }

    /***
     * @param $table table
     * @param $vars  'id=2'
     */
    public function delete($table,$vars) {
        global $tbl;
        $table = $tbl.$table;
        if($vars)$vars = "where $vars";
        $sql="delete from $table $vars";
        return $this->exec($sql);
    }

    /***
     * @param $table table
     * @param $key  "key1,key2,key3,key4"
     * @param $value  "value1|value2|value3|value4";
     * @param $vars   "id=2"
     */
    public function update($table,$key,$value,$vars) {
        global $tbl;
        $table = $tbl.$table;
        if($vars){
            $vars = "where $vars";
        }
        $k=explode(",",$key);
        $v=explode("|",$value);
        for($i=0;$i<count($k);$i++){
            $xxx.=$k[$i]."=\"".(substr($v[$i],0,7)=="content" ? $_POST[$v[$i]] :$v[$i])."\"";
            if($i<count($k)-1)$xxx.=",";
        }
        $sql="update $table set $xxx $vars";
        return $this->exec($sql);
    }

    /***
     * @param $table table
     * @param $key    "key1,key2,key3"
     * @param string $vars  "id=2"
     * @return bool
     */
    public function select($table,$key,$vars="",$order='',$limit=""){
        global $tbl;
        $table = $tbl.$table;
        if($vars)$vars = "where $vars";
        if($order)$order = "order by  $order";
        if($limit) $limit = "limit $limit";

        $sql="select $key from $table $vars $order $limit";
        try{
            $stmt=$this->pdo->query($sql);
            return $stmt->fetchall(pdo::FETCH_ASSOC);
        }catch(PDOException $e){
            $this->message($e,$sql);
        }
    }


    /***
     * @param $table
     * @param $arr
     */
    public function replace($table,$arr) {
        global $tbl;
        $table = $tbl.$table;
        if(!$arr)return;
        $A=array();
        foreach($arr as $k=>$v){
            $v=var_export($v,true);
            if(substr($v,-2,2)=='\\\'')$v=substr($v,0,-2).'\'';
            $A[]=$k.'='.$v;
        }
        $sql="replace into $table set ".implode(',',$A);
        return $this->exec($sql);
    }


}