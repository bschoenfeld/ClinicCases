<?php
session_start();

include 'db.php';
function makeList($array)
{
	foreach ($array as $v)
		{

			$list .=  "'$v',";


		}
	//Strips the last comma
	$strip = substr($list,0,-1);
	return $strip;

}

function get_allowed_posters($username)
{

	//first, find out which class you're in
	$a = mysql_query("SELECT username,class FROM `cm_users` WHERE `username` = '$username' limit 1");

	$row = mysql_fetch_object($a);

	$group = $row->class;

	//now a big switch to find out all of the usernames you're allowed to view.

	switch ($group)

		{

			case "prof":
				//get your students
				$b = mysql_query("SELECT `username`, `assigned_prof`,`status` FROM `cm_users` where `assigned_prof` = '$username' AND `status` = 'active'");
					while ($c = mysql_fetch_array($b))
						{$allowed[] = $c[username];}

				//get the admins
				//$d = mysql_query("SELECT `username`,`class`,`status` FROM `cm_users` WHERE `class` = 'admin' AND `status` = 'active'");
					//while ($e = mysql_fetch_array($d))
						//{$allowed[] = $e[username];}

				//then, of course add yourself
				$allowed[] = $username;

				$flist = makeList($allowed);
				return $flist;

				break;
			case "student":
				//get your professor
				$f = mysql_query("SELECT `username`,`assigned_prof` FROM `cm_users` WHERE `username` = '$username' LIMIT 1");
					$g = mysql_fetch_array($f);
					$allowed[] = $g[assigned_prof];

				//get you and your classmates
				$h = mysql_query("SELECT `username`,`assigned_prof` FROM `cm_users` WHERE `assigned_prof` = '$g[assigned_prof]'");
					while ($i = mysql_fetch_array($h))
					{$allowed[] = $i[username];}

				//now get the admins
				//$j = mysql_query("SELECT `username`,`class` FROM `cm_users` WHERE `class` = 'admin'");
					//while ($k = mysql_fetch_array($j))
						//{$allowed[] = $k[username];}

				$flist = makeList($allowed);
				return $flist;
				break;

			case "admin":
			//for the moment, the policy is that admins only see posts by admins
				$l = mysql_query("SELECT `username`,`class` FROM `cm_users` WHERE `class` = 'admin'");
					while ($m = mysql_fetch_array($l))
						{$allowed[] = $m[username];}

				$flist = makeList($allowed);
				return $flist;
				break;




		}






}

?>