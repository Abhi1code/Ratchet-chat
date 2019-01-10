<?php

		if (isset($_POST['name']) && isset($_POST['email'])) {
			if (!empty($_POST['name']) && !empty($_POST['email'])) {
				$code = 0;
				require("../db/user_trans.php");
				$user_trans = new Usertrans;
				$user_trans->setName($_POST['name']);
				$user_trans->setEmail($_POST['email']);
				$user_trans->setStatus(1);
				$user_trans->setLastlogin(date('Y-m-d h:i:s'));
				$userdata = $user_trans->getuserbyemail();
				if (is_array($userdata) && count($userdata)>0) {
					$user_trans->setId($userdata['id']);
					if ($user_trans->updateloginstatus()) {
						
						echo "login,".$userdata['id'];
						
					}else{
                        echo "Failed to login,".$code;
					}
				} else {
					if ($user_trans->savedata()) {
						$login_id = $user_trans->getconn()->lastInsertId();
						$user_trans->setId($login_id);
                        
					    echo "login,".$login_id;
					    
				} else {
					    echo "Error in connection,".$code;
				}
			} 
		}
    }
		?>