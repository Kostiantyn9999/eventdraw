<?php
session_start();
require_once('config.php');      
require_once('EditableGrid.php');            

function fetch_pairs($mysqli,$query){
	if (!($res = $mysqli->query($query)))return FALSE;
	$rows = array();
	while ($row = $res->fetch_assoc()) {
		$first = true;
		$key = $value = null;
		foreach ($row as $val) {
			if ($first) { $key = $val; $first = false; }
			else { $value = $val; break; } 
		}
		$rows[$key] = $value;
	}
	return $rows;
}
// Database connection
$mysqli = mysqli_init();
$mysqli->options(MYSQLI_OPT_CONNECT_TIMEOUT, 5);
$mysqli->real_connect($config['db_host'],$config['db_user'],$config['db_password'],$config['db_name']); 
                    
// create a new EditableGrid object
$grid = new EditableGrid();

/* 
*  Add columns. The first argument of addColumn is the name of the field in the databse. 
*  The second argument is the label that will be displayed in the header
*/
$grid->addColumn('UserID', 'ID', 'integer', NULL, false);
$grid->addColumn('CliendID', 'Client ID', 'integer');
$grid->addColumn('UserName', 'User Name', 'string');
$grid->addColumn('UserEmail', 'Email', 'email');
$grid->addColumn('UserLogin', 'Login', 'string');
$grid->addColumn('UserPsw', 'Password', 'string');
$grid->addColumn('UserLastLogin', 'Last Login', 'datetime', NULL, false);

if(isset( $_SESSION['isAdmin']) && $_SESSION['isAdmin']== 1)
{
$grid->addColumn('UserActive', 'Active', 'boolean');
$grid->addColumn('UserIsSupport', 'Support', 'boolean');
$grid->addColumn('UserIsAdmin', 'Admin', 'boolean');
$grid->addColumn('UserCreated', 'Created', 'datetime', NULL, false);
$grid->addColumn('UserMaxSessions', 'Max Number of Sessions', 'integer');
$grid->addColumn('action', 'Action', 'html', NULL, false, 'UserID');
}

$mydb_tablename = (isset($_GET['db_tablename'])) ? stripslashes($_GET['db_tablename']) : 'tblClients';


error_log(print_r($_GET,true));
$query = 'SELECT *, "*****" as UserPsw FROM  tblUsers';
$queryCount = 'SELECT count(UserID) as nb FROM tblUsers';

$totalUnfiltered =$mysqli->query($queryCount)->fetch_row()[0];
$total = $totalUnfiltered;

$page=0;
if ( isset($_GET['page']) && is_numeric($_GET['page'])  )
  $page =  (int) $_GET['page'];


$rowByPage=20;

$from= ($page-1) * $rowByPage;

if ( isset($_GET['filter']) && $_GET['filter'] != "" ) {
  $filter =  $_GET['filter'];
  $query .= '  WHERE UserName like "%'.$filter.'%" OR UserLogin like "%'.$filter.'%"';
  $queryCount .= '  WHERE UserName like "%'.$filter.'%" OR UserLogin like "%'.$filter.'%"';
  $total =$mysqli->query($queryCount)->fetch_row()[0];
}

if ( isset($_GET['sort']) && $_GET['sort'] != "" )
  $query .= " ORDER BY " . $_GET['sort'] . (  $_GET['asc'] == "0" ? " DESC " : "" );


$query .= " LIMIT ". $from. ", ". $rowByPage;

error_log("pageCount = " . ceil($total/$rowByPage));
error_log("total = " .$total);
error_log("totalUnfiltered = " .$totalUnfiltered);

$grid->setPaginator(ceil($total/$rowByPage), (int) $total, (int) $totalUnfiltered, null);

/* END SERVER SIDE */ 

error_log($query);

                                                                       
$result = $mysqli->query($query );





$mysqli->close();





// send data to the browser

$grid->renderJSON($result,false, false, !isset($_GET['data_only']));

