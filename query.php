<?php

/***********************************************
   Originally written for  the
   Zurich Water Game: internet version
   Author:  Nigel Gilbert and the FIRMA project
   --------------------------------------------
   query.php
   
   This file contains functions to interface to the 
   server database
   
   Version 1.0  6 August 2001
   Version 1.1  1 September 2001
   Version 1.2  20 December 2003
   Version 1.3  19 March 2004
   Version 1.4  12 July 2004
   Version 1.5  20 August 2004

**********************************************/

function db_open($database, $user="", $pw="") {
/* open the database and trap errors.  Returns link to database */

    global $db;

    if ($user and $pw) {
    	$db = @pg_connect("dbname=$database user=$user password=$pw");
    }
    elseif ($user and !$pw) {
    	$db = @pg_connect("dbname=$database user=$user");
    }
    else {
    	$db = @pg_connect("dbname=$database");
    } 
		if (!$db) {
				echo "<h2><font color=red>Cannot connect to database: $database</font></h2>
				<p>This may be because the database server has not been started, or the username 
				($user) or password ($pw) are incorrect.</p>";
				exit;
		}
    return $db;
}

function db_write($sql) {
/* carry out an UPDATE or INSERT operation.  Assumes db is already connected. */

    global $db;
    
    $result = @pg_exec($db, $sql);
    if (!$result) db_error("", $sql);
}

function db_write_no_error($sql) {
/* carry out an UPDATE or INSERT operation.  Assumes db is already connected. */

    global $db;
    
    $result = @pg_exec($db, $sql);
}
    
function db_error($errormsg="", $sql="") {
/* display an error message and then die */

    global $database, $db;
    
    if (empty($errormsg)) $errormsg = pg_last_error($db);
	if (PHP_SAPI === 'cli') print_r(array('msg' => $errormsg, 'database' => $database, 'sql' => $sql));
	else display_error(array('msg' => $errormsg, 'database' => $database, 'sql' => $sql));
}

class Query {

/* class to send and store result of a query to the database.  Assumes the db is already
   connected */

    private
    	  $cursor,
        $result,
        $row,
        $nrows,
        $lastSQL;
        
    function error($msg) {
        db_error($msg, $this->lastSQL);
    }
    
    function query($query) {
    /* never returns if there is an error (prints error message and dies) */

        global $db;

        $this->lastSQL = $query;
        $this->result = @pg_query($db, $query);
        if ($this->result === false) $this->error("");
        $this->cursor = 0;
        if (($this->nrows = pg_numrows($this->result)) > 0) 
        		$this->row = pg_fetch_assoc($this->result, 0);
    }
    
    function seek($loc) {
    /* positions the cursor at $loc, so that the next record to be read is the one at $loc */
    
        $this->cursor = $loc;
    }

    function next_rec() {
    /* extracts an associative array holding the next record retrieved from the DB.
    Returns the array or 0 if no more */
    
        if ($this->cursor >= $this->nrows) return 0;
        $this->row = pg_fetch_array($this->result, $this->cursor++);
        return $this->row;
    }

    function num_recs() {
    /* returns the number of records retrieved from the DB */
    
        return $this->nrows;
    }

    function last_rec() {
    /* set the cursor to the last row in the retrieved array */
    
        $this->cursor = $this->nrows;
    }
    
    function first_rec() {
    /* set the cursor to the first row in the retrieved array */
    
        $this->cursor = 0;
    }
    
    function prev_rec() {
    /* returns the row before the one previously retrieved, or 0 if there isn't one */
    
        if (!$this->cursor) return 0;
        $this->row = pg_fetch_array($this->result, --$this->cursor);
        return $this->row;
    }
    
    function field($fieldname) {
    /* return the value in the given field in the current record */
    
        return $this->row[$fieldname];
    }
    
    function fields() {
    /* return the hash of fields and their values for the current record */
    
    		return $this->row;
    	}
}

function db_modify($arr, $key, $table) {
	/* $arr is a hash of field names from the $table table. 
	$key is a single field name, included in $arr
	The table is seached for a single record that has the value in $arr for the $key.  
	If there is none, a new record is inserted with the values in $arr;
	otherwise the found record is updated with the values in $arr
	if they are diferent form the existing.	
	Returns -1 for failure, 0 if the update is the same as the existing,
	1 if a new record has been added, or 2 if the record has been updated */
	
	if (!isset($arr[$key])) return -1;
	$query = new Query("select * from $table where $key=" . $arr[$key]);
	if ($query->num_recs() > 1) return -1;  // more than 1 record matches
	if ($query->num_recs() == 0) {	// insert
		$sql = "INSERT into $table (";
		$vals = "";
		$comma = "";
		foreach ($arr as $k => $v) {
			$sql .= $comma . $k;
			$vals .= $comma . $v;
			$comma = ", ";
			}
		$sql .= ") VALUES ($vals)";
		db_write($sql);
		return 1;
		}
	// update?  check if update data is different from existing
	$changed = false;
	foreach ($arr as $k => $v) {
		if ($v != prepare($query->field($k))) $changed = true;
		}
	if (!$changed) return 0;
	$sql = "UPDATE $table SET ";
	$comma ="";
	foreach ($arr as $k => $v) {
		$sql .= "$comma$k=$v";
		$comma = ", ";
		}
	$sql .= " where $key=" . $arr[$key];
	db_write($sql);
	return 2;
}

function db_retrieve($object_name, $table_name) {
/* retrieve an object from the database.  Assumes that
the object and all its values have been serialised before being
stored in the database */

        $query = new Query("SELECT serializedobject FROM $table_name WHERE
                                                        name='$object_name'");
        if (!$query->next_rec()) $query->error("No record found");
        return unserialize(stripslashes($query->field('serializedobject')));
}

function db_save($object, $object_name, $table_name) {
/* saves an object into the database.  Serializes
the object and all its values before storing them
in the database */

        global $zurich;
        
        $s = addslashes(serialize($object));
        db_write("UPDATE $table_name SET time = '$zurich->time', 
                                           name = '$object_name',
                                           serializedobject = '$s'
                                       WHERE name='$object_name'"); 
}
