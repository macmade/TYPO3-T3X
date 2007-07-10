<?php
	# Mantis - a php based bugtracking system
	# Copyright (C) 2000 - 2002  Kenzaburo Ito - kenito@300baud.org
	# Copyright (C) 2002 - 2004  Mantis Team   - mantisbt-dev@lists.sourceforge.net
	# This program is distributed under the terms and conditions of the GPL
	# See the README and LICENSE files for details

	# --------------------------------------------------------
	# $Id: manage_proj_user_copy.php,v 1.4 2004/01/11 07:16:07 vboctor Exp $
	# --------------------------------------------------------

	# @@@ We don't call this page from anywhere yet
?>
<?php require_once( 'core.php' ) ?>
<?php
	$f_project_id = gpc_get_int( 'project_id' );
	$f_src_project_id = gpc_get_int( 'src_project_id' );

	# We should check both since we are in the project section and an
	#  admin might raise the first threshold and not realize they need
	#  to raise the second
	access_ensure_project_level( config_get( 'manage_project_threshold' ), $f_project_id );
	access_ensure_project_level( config_get( 'project_user_threshold' ), $f_project_id );

	project_copy_users( $f_src_project_id, $f_project_id );

	print_header_redirect( 'manage_proj_edit_page.php?project_id=' . $f_project_id );
?>
