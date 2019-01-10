<?php
session_start();
if (!isset($_SESSION['user_id'])) {
	header("location:index.php","_SELF");
	die();
}
require("db/user_trans.php");
$getuserinfo = new Usertrans;
$userdata = $getuserinfo->getuserbyid($_SESSION['user_id']); 
$user_name = $userdata['name'];
$user_email = $userdata['email']; 
$all_users = $getuserinfo->getalluser();
?>

<!DOCTYPE html>
<html>
<head>
	<title>chatroom</title>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>

</head>
<body>
	<center><h2>Ratchet chat</h2></center>   
	<div id="user_list">	  		
   <table style="border-collapse: collapse;border-spacing: 5px;margin-left: 5%;max-height: 400px;display: block;overflow: scroll;position: fixed;" border='3' >
   	  <thead>
   	  	<tr>
   	  		<th style="padding: 10px;" text-align="center">Id</th>
   	  		<th style="padding: 10px;" colspan='2'>Email</th>
   	  	</tr>
   	  	</thead>
   	  	<tr>
   	  		<td style="padding: 10px;text-align: center;"><?php echo "".$user_name; ?></td>
   	  		<td style="padding: 10px;text-align: center;" colspan='2'><?php echo "".$user_email; ?></td>
   	  	</tr>
   	  <tr>
   	  	<th style="padding: 10px;">user</th>
   	  	<th style="padding: 10px;">last_login</th>
   	  	<th style="padding: 10px;">status</th>
   	  </tr>
   	  <tbody>
   	  	<?php 
           foreach ($all_users as $key => $user) {
           	if (isset($_SESSION['user_id']) && $_SESSION['user_id'] == $user['id']) {
           		continue;
           	}
           	echo "<tr><td style='padding: 5px;text-align: center;'>".$user['name']."</td>";
           	echo "<td style='padding: 5px;text-align: center;' id='".$user['id'].'dt'."'>".$user['last_login']."</td>";
           	if ($user['status'] == 1) {
           		echo "<td style='padding: 5px;text-align: center;background-color: green;' id='".$user['id']."'>"."online"."</td>";
           	}else {
           		echo "<td style='padding: 5px;text-align: center;background-color: #ff0000;' id='".$user['id']."'>"."offline"."</td></tr>";
           	}
           	
           }
   	  	?>
   	  </tbody>
   	  <tfoot>
   	  	   <tr>
   	  	   	<td colspan="3" align="center">
   	  	   		<input type="button" name="close" id="close" value="close" style="width: 100%;background-color: #A9A9A9;">
   	  	   	</td>
   	  	   </tr>
   	  </tfoot>
   </table>
   </div>  

   <div id="user_message" align="right">
   	<table style="border-collapse: collapse;border-spacing: 5px;width: 50%;margin-right: 5%;position: relative;" id="chats">
   		<thead>
   			<tr><th colspan='4' style="padding: 5px;background-color: #A9A9A9;"><strong>ChatRoom</strong></th></tr>
   		</thead>
   		<tbody style="display: block;overflow-y: auto;overflow-x: hidden;height: 350px;" id="tbody">
   			<div id="tdiv">
   			<!--<tr>
   				<td valign="top" colspan="3" style="padding: 5px;background-color: #ffe0bd;">
   					<div><strong>From</strong></div>
   					<div>Message</div>
   				</td>
   				<td valign="top" align="right" style="padding: 5px;background-color: #ffe0bd;width: 20%;">DateTime</td>
   			</tr>-->
   			</div>
   		</tbody>
   	</table>
   </div>
   <div id="input" style="position: absolute;bottom: 0px;right: 0px;margin-right: 5%;width: 100%;margin-bottom: 3%;margin-top: 2%;" align="right" >
   	 <form>
   	 	<textarea value="Enter Message" id="msg" name="msg" placeholder="Enter Message" style="width: 50%;"></textarea></br>
   	 	<input type="button" name="send" value="send" id="send" style="width: 50.5%;background-color: #0000ff;height: 30px;"/>
   	 </form>
   </div>
   <script type="text/javascript">
   	
   	$(document).ready(function(){

      var conn = new WebSocket('ws://13.127.75.31:8282');
      
      conn.onopen = function(e) {
      console.log("Connection established!");
      var id = <?php echo $_SESSION['user_id']; ?>;
      var data = {
               meta : 'online',
               id : id
          	};
      conn.send(JSON.stringify(data)); 
      };

      conn.onmessage = function(e) {
      console.log(e.data);
      var data = JSON.parse(e.data);
      if (data.meta == 'msg') {
      	if (data.from == 'Me') {

      		var row = '<tr style="margin-bottom:1px;display:block" align="right"><td valign="top" style="padding: 5px;background-color: #ffe0bd;display:block;" align="right"><div><strong>' + data.from + '   (' + data.dt + ')' + '</strong></div><div>' + data.msg + '</div></td>';

      	} else {

      		var row = '<tr style="margin-bottom:1px;display:block;"><td valign="top" colspan="3" style="padding: 5px;background-color: #7ec0ee;display:block;" align="left"><div><strong>' + data.from + '   (' + data.dt + ')' + '</strong></div><div>' + data.msg + '</div></td>';

      	}
      	
   				$("#chats > tbody").append(row);	
   				$("#tbody").scrollTop(1000);
 	
   			} else {
   				if (data.meta == 'offline') {
                        
                        $("#"+data.id).text("offline");
                        $("#"+data.id+"dt").text(data.dt);
                        $("#"+data.id).css("background-color", "red");
   				} else {
   					if (data.meta == 'online') {

   						$("#"+data.id).text("online");
   						$("#"+data.id+"dt").text(data.dt);
   						$("#"+data.id).css("background-color", "green");
   					}
   				}
   			}
      
      };

      conn.onclose = function(e) {
         console.log("Connection closed..");
      };
      
      

      $("#close").click(function(){
      	var id = <?php echo $_SESSION['user_id']; ?>;
          $.ajax({
          	url: "logout.php",
          	method: "post",
          	data: "userid="+id+"&status=leave",
            success: function(result){
            	var stat = JSON.parse(result);
                if (stat.status == 1) {
                   var data = {
                   meta : 'offline',
                   id : id
          	        };
          	        conn.send(JSON.stringify(data));
                	conn.close();
                	location = "index.php";
                    
          	}
                }
            
          });
          });

      $('#msg').keydown(function(event) {
         // enter has keyCode = 13, change it if you want to use another button
         if (event.keyCode == 13) {
           send();
         return true;
          }
       });

      $('#send').click(function(){
      	send();
      });


      function send(){
      	var id = <?php echo $_SESSION['user_id']; ?>;
          var msg = $("#msg").val();
          if (msg !== "") {
          	var data = {
                   userid : id,
                   usermsg : msg,
                   meta : 'msg'
          	};
          	conn.send(JSON.stringify(data));
          	$("#msg").val("");
            
            
          }
      }
     
   	});
   
   </script>
</body>
</html>