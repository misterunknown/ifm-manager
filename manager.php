<?php

/**
 * IFM Manager
 *
 * This is a wrapper for the IFM (https://github.com/misterunknown/ifm) which handles different users
 *
 * License: MIT
 */

// include IFM
require_once( "libifm.php" );

// start session
if( session_status() != PHP_SESSION_ACTIVE ) {
	session_start();
}

// enable logout
if( isset( $_GET['logout'] ) ) {
	session_destroy();
}

// start IFM if user is logged in
if( isset( $_SESSION['login'] ) && $_SESSION['login'] != "" ) {
	// start IFM with the custom permission set
	$IFM = new IFM( $_SESSION['permissions'] );
	$IFM->run();
}

// check credentials if they were passed
elseif( isset( $_POST['login'] ) && isset( $_POST['password'] ) ) {
	$db = new SQLite3( 'users.db' );
	$rows = $db->query( "SELECT * FROM users WHERE login = '" . $db->escapeString( $_POST['login'] ) . "'" );
	$user = $rows->fetchArray();
	$_SESSION['login'] = $user['login'];
	$permissions = $db->query( "SELECT
		root_dir,
		tmp_dir,
		ajaxrequest,
		chmod,
		copymove,
		createdir,
		createfile,
		edit,
		`delete`,
		download,
		extract,
		upload,
		remoteupload,
		rename,
		zipnload,
		showlastmodified,
		showfilesize,
		showowner,
		showgroup,
		showpermissions,
		showhtdocs,
		showhiddenfiles,
		showpath
		FROM permissions WHERE userid = " . $db->escapeString( $user['id'] ) );
	$_SESSION['permissions'] = $permissions->fetchArray();
	// start IFM with the custom permission set
	$IFM = new IFM( $_SESSION['permissions'] );
	$IFM->run();
}

// otherwise show the login form
else {
?>

<!DOCTYPE HTML>
<html>
	<head>
		<title>IFMManager Login</title>
	</head>
	<body>
		<h1>IFMManager Login</h1>
		<form action="" method="post">
			<input type="text" name="login"><br>
			<input type="password" name="password"><br>
			<button type="submit">Login</button>
		</form>
	</body>
</html>

<?php
}
