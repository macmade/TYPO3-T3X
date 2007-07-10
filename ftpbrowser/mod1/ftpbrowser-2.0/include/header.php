<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">

<html lang="en">

	<head>
		<meta http-equiv="content-type" content="text/html; charset=iso-8859-1">
		<title><?php echo(getLang('header.title')); ?></title>
		<meta name="generator" content="BBEdit 7.0">
		<script src="css-js/scripts.js" type="text/javascript" language="Javascript" charset="iso-8859-1">
		</script>
		<style type="text/css" media="screen">
			<!--
			body,td,p,div {
				color: <?php echo($CONFIG['COLOR_TEXT']) ?>;
				font-style: normal;
				font-weight: normal;
				font-size: 10px;
				line-height: normal;
				font-family: Arial, Helvetica, Geneva, Swiss, SunSans-Regular;
				text-decoration: none;
			}
			input {
				color: <?php echo($CONFIG['COLOR_INPUT']) ?>;
				font-size: 10px;
				background-color: #CCCCCC;
				border: solid 1px #4D4D4D;
			}
			select {
				color: <?php echo($CONFIG['COLOR_INPUT']) ?>;
				font-size: 10px;
				background-color: #CCCCCC;
				border: solid 1px #4D4D4D;
			}
			textarea {
				color: <?php echo($CONFIG['COLOR_INPUT']) ?>;
				font-size: 10px;
				background-color: #CCCCCC;
				border: solid 1px #4D4D4D;
			}
			a {
				color: <?php echo($CONFIG['COLOR_LINK']) ?>;
				font-style: normal;
				font-weight: bold;
				font-size: 10px;
				line-height: normal;
				font-family: Arial, Helvetica, Geneva, Swiss, SunSans-Regular;
				text-decoration: none;
			}
			a:visited {
				color: <?php echo($CONFIG['COLOR_LINKVISITED']) ?>;
				text-decoration: none;
			}
			a:hover {
				color: <?php echo($CONFIG['COLOR_LINKHOVER']) ?>;
				text-decoration: underline;
			}
			a:active {
				color: <?php echo($CONFIG['COLOR_LINKACTIVE']) ?>;
				font-weight: bold;
				text-decoration: underline;
			}
			.legend {
				color: <?php echo($CONFIG['COLOR_LEGEND']) ?>;
				font-size: 12px;
			}
			.result {
				color: <?php echo($CONFIG['COLOR_RESULT']) ?>;
				font-size: 12px;
				font-weight: bold;
			}
			.properties {
				color: <?php echo($CONFIG['COLOR_PROPERTIES']) ?>;
			}
			.infos {
				color: <?php echo($CONFIG['COLOR_INFOS']) ?>;
				font-weight: bold;
			}
			.filename {
				color: <?php echo($CONFIG['COLOR_FILENAME']) ?>;
				padding: 2px;
			}
			.type {
				color: <?php echo($CONFIG['COLOR_TYPE']) ?>;
				padding: 2px;
			}
			.size {
				color: <?php echo($CONFIG['COLOR_SIZE']) ?>;
				padding: 2px;
			}
			.perms {
				color: <?php echo($CONFIG['COLOR_PERMS']) ?>;
				padding: 2px;
			}
			.mod {
				color: <?php echo($CONFIG['COLOR_MOD']) ?>;
				padding: 2px;
			}
			.read {
				color: <?php echo($CONFIG['COLOR_READ']) ?>;
				padding: 2px;
			}
			.write {
				color: <?php echo($CONFIG['COLOR_WRITE']) ?>;
				padding: 2px;
			}
			.uid {
				color: <?php echo($CONFIG['COLOR_UID']) ?>;
				padding: 2px;
			}
			.gid {
				color: <?php echo($CONFIG['COLOR_GID']) ?>;
				padding: 2px;
			}
			.ctime {
				color: <?php echo($CONFIG['COLOR_CTIME']) ?>;
				padding: 2px;
			}
			.atime {
				color: <?php echo($CONFIG['COLOR_ATIME']) ?>;
				padding: 2px;
			}
			-->
		</style>
	</head>

	<body bgcolor="#EFEFEF" leftmargin="0" topmargin="0" marginwidth="0" marginheight="0" onload="javascript:preloadPictures();">
		<?php
			if ($CONFIG['HEADER']) {
		?>
		<table border="0" width="100%" cellspacing="0" cellpadding="0" align="center">
			<tr>
				<td width="390" height="125" align="left" valign="top"><img src="gfx/topbar_logo.gif" alt="" width="390" height="125" hspace="0" vspace="0" border="0" align="middle"></td>
				<td width="125" height="125" align="left" valign="top"><img src="gfx/beta_flag.gif" alt="" width="125" height="125" hspace="0" vspace="0" border="0" align="middle"></td>
				<td height="125" align="left" valign="top" background="gfx/topbar_background.gif"><img src="gfx/spacer.gif" alt="" width="10" height="125" hspace="0" vspace="0" border="0" align="middle"></td>
			</tr>
		</table>
		<?php
			}
		?>
		<table border="0" width="100%" cellspacing="10" cellpadding="0" align="center">
			<tr>
				<td align="left" valign="top">
