<?php

class Room extends Model
{
    function __construct()
    {
        parent::__construct();
    }


//Controller
    public
    function room_create()
    {
        //data={
        //    "frind_list":[メンバーid,,,,,]
        //}

        $this->data["is_friend"] = 0;
        $this->create();
        $room_id = $this->dbh->lastInsertId('id');
        $entry = new Enter;
        $entry->data['room_id'] = $room_id;
        $entry->data['is_friend'] = 0;
        //自分を
        $entry->create();
        //みんな
        foreach ($this->data["friend_list"] as $id) {
            $entry->data['user_id'] = $id;
            $entry->create();
        }
    }

    public
    function friend_create()
    {
//        data={

//        "friend_id": 友達のID
//        }
        $this->data["is_friend"] = 1;
        $this->data['name'] = '';
        $this->create();
        $room_id = $this->dbh->lastInsertId('id');
        //自分をエントリー
        $entry = new Enter;
        $entry->data['room_id'] = $room_id;
        $entry->data['is_friend'] = 1;
        $entry->create();
        //友達をエントリー
        $entry->data['user_id'] = $this->data['friend_id'];
        $entry->create();
        $this->res['data'] = true;
    }

    public
    function index()
    {
        // data=data
        $sql = "select room.id as room_id , room.name as room_name ,enter.is_friend,room.last_message_content
               ,room.last_message_time,enter.user_id
            from  room  inner  join  enter on   room.id =enter.room_id
            and enter.user_id =:user_id
             order by room.last_message_time desc ";

        $this->stmt = $this->dbh->prepare($sql);
        $this->res["db"] = $this->stmt->execute([
            ':user_id' => $this->data["user_id"]

        ]);

        $this->res["data"] = $this->stmt->fetchAll(PDO::FETCH_ASSOC);


        $array = $this->res["data"];

        foreach ($array as $key => $ar) {

            if ($array[$key]['is_friend'] == 1) {
                $sql = "select user.name from enter  inner join user on enter.user_id=user.id where user_id!=:user_id  and room_id=" . $array[$key]['room_id'];
                $this->stmt = $this->dbh->prepare($sql);
                $this->res["db"] = $this->stmt->execute([
                    ':user_id' => $this->data["user_id"]
                ]);
                $array[$key]['room_name'] = $this->stmt->fetchAll(PDO::FETCH_ASSOC)[0]['name'];
            } else {
                $sql = "select count(*) as member_count from enter where room_id=" . $array[$key]['room_id'];
                $this->stmt = $this->dbh->prepare($sql);
                $this->res["db"] = $this->stmt->execute([]);

                $array[$key]['member_count'] = $this->stmt->fetchAll(PDO::FETCH_ASSOC)[0]['member_count'];

            }


        }
        $this->res["data"] = $array;


    }

    function friend_index()
    {
        $sql = "select room.id as room_id  from room  left join enter on room.id=enter.room_id
       where enter.user_id=:user_id and room.is_friend=1";
        $this->stmt = $this->dbh->prepare($sql);
        $this->res["db"] = $this->stmt->execute([
            ':user_id' => $this->data["user_id"]
        ]);

        $array = $this->stmt->fetchAll(PDO::FETCH_ASSOC);
        foreach ($array as $key => $ar) {
            $sql = "select user.name ,user.id from enter  inner join user on enter.user_id=user.id
      where enter.user_id!=:user_id  and room_id=" . $array[$key]['room_id'];
            $this->stmt = $this->dbh->prepare($sql);
            $this->res["db"] = $this->stmt->execute([
                ':user_id' => $this->data["user_id"]

            ]);



               $array1= $this->stmt->fetchAll(PDO::FETCH_ASSOC);
                $array[$key]['friend_name'] = $array1[0]["name"];
            $array[$key]['friend_id'] = $array1[0]["id"];
        }
        $this->res["data"] = $array;
    }


//Model

    public
    function create()
    {
        $sql = "INSERT INTO $this->table (name, is_friend,last_message_time) VALUES (:name, :is_friend,now())";
        $this->stmt = $this->dbh->prepare($sql);
        $this->res["db"] = $this->stmt->execute([
            ':name' => $this->data["name"],
            ':is_friend' => $this->data["is_friend"]
        ]);
    }

    public function room_update(){
        $sql = "update room set last_message_time=now() where id=:room_id";
        $this->stmt = $this->dbh->prepare($sql);
        $this->res["db"] = $this->stmt->execute([
            ':room_id' => $this->data["room_id"]
        ]);

        $sql = "update room set last_message_content=:content where id=:room_id ";
        $this->stmt = $this->dbh->prepare($sql);
        $this->res["db"] = $this->stmt->execute([
            ':room_id' => $this->data["room_id"],
            ':content' => $this->data["content"]

        ]);
    }







}










