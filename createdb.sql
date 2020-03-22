\connect test
DROP DATABASE zwg;
CREATE DATABASE zwg;
\connect zwg
DROP SEQUENCE objects_id_seq, log_id_seq, requests_id_seq,msgs_id_seq;
CREATE table objects (
	id serial,
	name varchar(30),
	time datetime,
	serializedobject varchar(10000),
	primary key (id)
	);
CREATE table log (
	id serial,
	name varchar(30),
	action varchar(500),
	detail int2,
	time timestamp,
	primary key (id)
	);
CREATE table requests (
	id serial,
	time timestamp,
	requestor varchar(30),
	requestee varchar(30),
	request varchar(30),
	param1 varchar(30),
	param2 varchar(30),
	primary key (id)
	);
CREATE table msgs (
	id serial,
	sender varchar(30),
	recipient varchar(30),
	timesent datetime,
	timeread datetime,
	msg text,
	primary key (id)
	);

GRANT all on objects to public;
GRANT all on log to public;
GRANT all on requests to public;
GRANT all on msgs to public;
GRANT all on objects_id_seq to public;
GRANT all on log_id_seq to public;
GRANT all on requests_id_seq to public;
GRANT all on msgs_id_seq to public;

INSERT INTO objects (name, time, serializedobject) VALUES ('Zurich', now(), '');
