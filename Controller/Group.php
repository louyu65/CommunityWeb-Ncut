<?php

/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2016/12/12
 * Time: 下午 01:38
 */
class Group
{
    public $sid;
    public $mysqli;
    public $group_id;
    public function __construct($group_id = null){
        if(Auth::check()==true){
            $this->sid = $_SESSION['sid'];
            $this->group_id = $group_id;
        }else{
            return redirect('login.php');
        }
        $this->mysqli=Connect::conn();
    }
    public function add($request){  //新增群組
        $ret = array();
        if($this->checkName($request['gname'])){
            $photopath = $this->uploadPhoto($_FILES['gphoto'],$this->sid);
            $sql = "INSERT INTO `group_data` (`group_id`,`gname`,`manager`,`gphoto`) VALUE (NULL ,'".$request['gname']."','".$this->sid."','".$photopath."')";
            if (!$this->mysqli->query($sql)) {  //讀取錯誤訊息
                printf("Errormessage: %s\n", $this->mysqli->error);
            }else{
                $ret = [
                    'status' => true,
                    'msg' => "",
                ];
            }
        }else{
            $ret = [
               'status' => false,
               'msg' => "名稱重複使用",
            ];
        }
        return $ret;
        unset($ret);
        unset($sql);
        unset($photopath);
    }

    public function delelte(){
        if(isset($this->group_id)){
            $sql = "DELETE FROM `group_data` WHERE `group_id` = ".$this->group_id;
            if (!$this->mysqli->query($sql)) {  //讀取錯誤訊息
                printf("Errormessage: %s\n", $this->mysqli->error);
            }else{
                $ret = [
                    status => true,
                    msg => "",
                ];
            }
        }else{
            $ret = [
                status => false,
                msg => "錯誤！",
            ];
        }
        return $ret;
        unset($ret);
        unset($sql);
    }

    public function edit($request){
        if(isset($this->group_id)){
            //UPDATE  `group1`.`group_data` SET  `gname` =  'test1' WHERE  `group_data`.`group_id` =1 LIMIT 1 ;
            $sql = "UPDATE  `group1`.`group_data` SET  `gname` =  '".$request['gname'];
            if(isset($request['gphoto'])){
                $photopath = $this->uploadPhoto($_FILES['photo'],$this->sid);
                $sql .= " `gphoto` = '" .$photopath."'";
            }
            $sql.= " WHERE `group_id` = ".$this->group_id;
            if (!$this->mysqli->query($sql)) {  //讀取錯誤訊息
                printf("Errormessage: %s\n", $this->mysqli->error);
            }else{
                $ret = [
                    status => true,
                    msg => "",
                ];
            }
        }else{
            $ret = [
                status => false,
                msg => "錯誤！",
            ];
        }
        return $ret;
        unset($ret);
        unset($sql);
    }

    public function checkName($name){   //察看是否有重複的群足名稱
        $sql = "SELECT * FROM group_data WHERE `gname` = '".$name."'";
        $result = $this->mysqli->query($sql);
        if($result->num_rows>0){ //察看是否大於0
            return false;
        }else{
            return true;
        }
        unset($sql);
        unset($result);
    }

    public function uploadPhoto($file,$name){
        $uploaddir = 'images/group/';
        $uploadfile = $uploaddir . basename(rand(1111, 9999).$file['name']);
        if (move_uploaded_file($file['tmp_name'], $uploadfile)) {
            return $uploadfile;
        } else {
            return false;
        }
    }
    
    public function getMy(){
        $sql = "SELECT * FROM `group_data` WHERE `manager` = '".$this->sid."'";
        $result = $this->mysqli->query($sql);
        $a = array();
        while ($row = $result->fetch_array()){
            $b = [
                'group_id' => $row['group_id'],
                'gname' => $row['gname'],
                'gphoto' => $row['gphoto'],
            ];
            array_push($a,$b);
        }
        return $a;
    }
}