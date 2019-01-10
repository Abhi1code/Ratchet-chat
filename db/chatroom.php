<?php
/**
 * save chats and returns chats 
 */
class Chatroom
{
	private $id;
	private $senderid;
	private $msg;
	private $datetime;
	private $mconn;

    public function getId(){ return $this->id; }
    public function setId($id){ $this->id = $id; }
    public function getSenderid(){ return $this->senderid; }
    public function setSenderid($senderid){ $this->senderid = $senderid; }
    public function getMsg(){ return $this->msg; }
    public function setMsg($msg){ $this->msg = $msg; }
    public function getDatetime(){ return $this->datetime; }
    public function setDatetime($datetime){ $this->datetime = $datetime; }
    public function getconn(){ return $this->mconn; }

	function __construct()
	{
		require_once('db_connect.php');
		$conn = new Dbconnect;
		$this->mconn = $conn->establish_conn();
	}

	public function savechats()
	{
		$sql = "INSERT INTO `chats`(`id`, `senderid`, `message`, `datetime`) VALUES (null, :senderid, :message, :date_time)";
        $bind = $this->mconn->prepare($sql);
        $bind->bindParam(":senderid", $this->senderid);
        $bind->bindParam(":message", $this->msg);
        $bind->bindParam(":date_time", $this->datetime);
        
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
}
?>