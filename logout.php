<?php
session_start();
if (isset($_POST['status']) && $_POST['status'] == "leave") {
	require("db/user_trans.php");
	$user_trans = new Usertrans;
	$user_trans->setId($_POST['userid']);
	$user_trans->setStatus(0);
	$user_trans->setLastlogin(date('Y-m-d h:i:s'));

	if ($user_trans->updateloginstatus()) {
		unset($_SESSION['user_id']);
		session_destroy();
		echo json_encode(['status'=>'1']);
	} else {
		echo json_encode(['status'=>'0']);
	}
}
?>