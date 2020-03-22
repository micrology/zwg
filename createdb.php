<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">

<html lang="en">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	<title>Create ZWG database</title>
	<meta name="generator" content="Pepper">
	<meta name="author" content="Nigel Gilbert">
	<META HTTP-EQUIV="expires" CONTENT="now">
	<META HTTP-EQUIV="pragma" CONTENT="no-cache">
	<!-- Date:		 Friday April 19 2002 -->
</head>
<body>

<?php
include("common.php");

	$submit = get_param('submit');
	$dbname = get_param('dbname');
	
	if (!($submit and $dbname)) {
?>

<form method=post action="createdb.php">
	Database to create or reset:
	<input type=text name=dbname value=zwg size=5><P>
	User for DB: 
	<input type=text name=user value='' size=10>
	Password for DB: 
	<input type=text name=password value='' size=10>
	<p>
	<input type=submit name=submit value=Go>
	<input type=reset name=reset value=reset>
</form>

<?php
	}
else {
include("query.php");

	$user=get_param('user');
	$password=get_param('password');
	
echo "Creating (or resetting) database $dbname with user \"$user\" and password \"$password\"<P>";
	db_open("test", $user, $password);
	db_comm("DROP DATABASE $dbname");
	db_comm("CREATE DATABASE $dbname");
	db_open("$dbname", $user, $password);
//	db_comm("DROP SEQUENCE objects_id_seq, log_id_seq, requests_id_seq,msgs_id_seq");
//	db_comm("DROP TABLE objects, log, requests, msgs");
	db_comm("CREATE table objects (
		id serial,
		name varchar(30),
		time timestamp,
		serializedobject varchar(10000),
		primary key (id)
		)");
	db_comm("CREATE table log (
		id serial,
		name varchar(30),
		action varchar(500),
		detail int2,
		time timestamp,
		primary key (id)
		)");
	db_comm("CREATE table requests (
		id serial,
		time timestamp,
		requestor varchar(30),
		requestee varchar(30),
		request varchar(200),
		param1 varchar(500),
		param2 varchar(80),
		primary key (id)
		)");
	db_comm("CREATE table msgs (
		id serial,
		sender varchar(30),
		recipient varchar(30),
		timesent timestamp,
		timeread timestamp,
		msg text,
		primary key (id)
		)");
	
	db_comm("GRANT all on objects to public");
	db_comm("GRANT all on log to public");
	db_comm("GRANT all on requests to public");
	db_comm("GRANT all on msgs to public");
	db_comm("GRANT all on objects_id_seq to public");
	db_comm("GRANT all on log_id_seq to public");
	db_comm("GRANT all on requests_id_seq to public");
	db_comm("GRANT all on msgs_id_seq to public");
	
	db_comm("INSERT INTO objects (name, time, serializedobject) 
		VALUES ('Zurich', now(), '')");
}

function db_comm($comm) {
	echo "$comm ";
	db_write($comm);
	echo " ...Done.<P>";
}
?>
	
</body>
</html>
