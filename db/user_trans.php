<?php
/**
 * user transaction like input and output to database
 */
class Usertrans
{
	private $id;
	private $name;
	private $email;
	private $status;
	private $lastlogin;
	private $mconn;

    public function getId(){ return $this->id; }
    public function setId($id){ $this->id = $id; }
    public function getName(){ return $this->name; }
    public function setName($name){ $this->name = $name; }
    public function getEmail(){ return $this->email; }
    public function setEmail($email){ $this->email = $email; }
    public function getStatus(){ return $this->status; }
    public function setStatus($status){ $this->status = $status; }
    public function getLastlogin(){ return $this->lastlogin; }
    public function setLastlogin($lastlogin){ $this->lastlogin = $lastlogin; }
    public function getconn(){ return $this->mconn; }


	function __construct()
	{
		require_once('db_connect.php');
		$conn = new Dbconnect;
		$this->mconn = $conn->establish_conn();
			
	}


    public function savedata(){

    	$sql = "INSERT INTO `users`(`id`, `name`, `email`, `status`, `last_login`) VALUES (null, :name, :email, :status, :lastlogin)";
        $bind = $this->mconn->prepare($sql);
        $bind->bindParam(":name", $this->name);
        $bind->bindParam(":email", $this->email);
        $bind->bindParam(":status", $this->status);
        $bind->bindParam(":lastlogin", $this->lastlogin);
        try {
        	
        	if ($bind->execute()) {
        		return true;
        	} else{
                return false;
        	}

        } catch (Exception $e) {
        	echo $e->getMessage();
        }
    }

    public function getuserbyemail(){

    	$sql = "SELECT * FROM `users` WHERE `email` = :email";
    	$bind = $this->mconn->prepare($sql);
    	$bind->bindParam(":email", $this->email);
    	try {
    		if ($bind->execute()) {
    			$user = $bind->fetch(PDO::FETCH_ASSOC);
    		}
    	} catch (Exception $e) {
    		echo $e->getMessage();
    	}
    	return $user;
    }

    public function updateloginstatus(){

    	$sql = "UPDATE `users` SET `status` = :status, `last_login` = :lastlogin WHERE `id` = :id";
    	$bind = $this->mconn->prepare($sql);
    	$bind->bindParam(":status", $this->status);
    	$bind->bindParam(":lastlogin", $this->lastlogin);
    	$bind->bindParam(":id", $this->id);
    	try {
    		if ($bind->execute()) {
    			return true;
    		}else{
    			return false;
    		}
    	} catch (Exception $e) {
    		echo $e->getMessage();
    	}
    }

    public function getuserbyid($send_id){

    	$sql = "SELECT * FROM `users` WHERE `id` = :id";
    	$bind = $this->mconn->prepare($sql);
    	$bind->bindParam(":id", $send_id);
    	try {
    		if ($bind->execute()) {
    			$user = $bind->fetch(PDO::FETCH_ASSOC);
    		}
    	} catch (Exception $e) {
    		echo $e->getMessage();
    	}
    	return $user;
    }

    public function getalluser(){

    	$sql = "SELECT * FROM `users`";
        $bind = $this->mconn->prepare($sql);
        $bind->execute();
        $user = $bind->fetchAll(PDO::FETCH_ASSOC);
        return $user;
    }
}

?>