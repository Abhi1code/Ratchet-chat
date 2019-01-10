<?php
session_start();
if (isset($_SESSION['user_id'])) {
	header("location:chat_page.php","_SELF");
	die();
}
?>

<!DOCTYPE html>
<html>
<head>
	<title>Ratchet</title>
</head>
<body>
	<center><h3>Ratchet chat</h3></center>
	<center>
		<form action="index.php" method="POST">
			
					<input type="text" name="name" id="name" placeholder="Enter name" style="width: 30%;padding: 10px;margin: 10px;border-width: 2px;border-radius: 5px" /></br>

					<input type="text" name="email" id="email" placeholder="Enter email" style="width: 30%;padding: 10px;margin: 10px;border-width: 2px;border-radius: 5px" /></br>

					<input type="submit" name="join" id="join" value="join chat !!" style="width: 32%;padding: 10px;margin: 10px;background-color: #262;color: #ffffff;font-size: 15px;border-width: 2px;border-radius: 5px"/></br>
					
		</form>
	</center>
	<div id="php script">
		<?php

		if (isset($_POST['join'])) {
			if (!empty($_POST['name']) && !empty($_POST['email'])) {
				require("db/user_trans.php");
				$user_trans = new Usertrans;
				$user_trans->setName($_POST['name']);
				$user_trans->setEmail($_POST['email']);
				$user_trans->setStatus(1);
				$user_trans->setLastlogin(date('Y-m-d h:i:s'));
				$userdata = $user_trans->getuserbyemail();
				if (is_array($userdata) && count($userdata)>0) {
					$user_trans->setId($userdata['id']);
					if ($user_trans->updateloginstatus()) {
						$_SESSION['user_id'] = $user_trans->getId();
						echo "login..";
						header("location:chat_page.php","_SELF");
					}else{
                        echo "Failed to login..";
					}
				} else {
					if ($user_trans->savedata()) {
						$login_id = $user_trans->getconn()->lastInsertId();
						$user_trans->setId($login_id);
                        $_SESSION['user_id'] = $user_trans->getId();
					    echo "user registered..";
					    header("location:chat_page.php","_SELF");
				} else {
					    echo "error in connection";
				}
			} 
		}
    }
		?>
	</div>
</body>
</html>
