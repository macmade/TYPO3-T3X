<?php
	# Mantis - a php based bugtracking system
	# Copyright (C) 2000 - 2002  Kenzaburo Ito - kenito@300baud.org
	# Copyright (C) 2002 - 2004  Mantis Team   - mantisbt-dev@lists.sourceforge.net
	# This program is distributed under the terms and conditions of the GPL
	# See the README and LICENSE files for details

	# --------------------------------------------------------
	# $Id: bug_update.php,v 1.80 2004/12/01 17:40:57 thraxisp Exp $
	# --------------------------------------------------------
?>
<?php
	# Update bug data then redirect to the appropriate viewing page
?>
<?php
	require_once( 'core.php' );

	$t_core_path = config_get( 'core_path' );

	require_once( $t_core_path.'bug_api.php' );
	require_once( $t_core_path.'bugnote_api.php' );
	require_once( $t_core_path.'custom_field_api.php' );
?>
<?php
	$f_bug_id = gpc_get_int( 'bug_id' );
	$f_update_mode = gpc_get_bool( 'update_mode', FALSE ); # set if called from generic update page
	$f_new_status	= gpc_get_int( 'status', bug_get_field( $f_bug_id, 'status' ) );

	if ( ! ( 
				( access_has_bug_level( access_get_status_threshold( $f_new_status, bug_get_field( $f_bug_id, 'project_id' ) ), $f_bug_id ) ) ||
				( access_has_bug_level( config_get( 'update_bug_threshold' ) , $f_bug_id ) ) ||
				( ( bug_get_field( $f_bug_id, 'reporter_id' ) == auth_get_current_user_id() ) && 
						( ( ON == config_get( 'allow_reporter_reopen' ) ) ||
								( ON == config_get( 'allow_reporter_close' ) ) ) ) 
			) ) {
		access_denied();
	}

	# extract current extended information
	$t_bug_data = bug_get( $f_bug_id, true );
	$t_old_bug_status = $t_bug_data->status;

	$t_bug_data->reporter_id		= gpc_get_int( 'reporter_id', $t_bug_data->reporter_id );
	$t_bug_data->handler_id			= gpc_get_int( 'handler_id', $t_bug_data->handler_id );
	$t_bug_data->duplicate_id		= gpc_get_int( 'duplicate_id', $t_bug_data->duplicate_id );
	$t_bug_data->priority			= gpc_get_int( 'priority', $t_bug_data->priority );
	$t_bug_data->severity			= gpc_get_int( 'severity', $t_bug_data->severity );
	$t_bug_data->reproducibility	= gpc_get_int( 'reproducibility', $t_bug_data->reproducibility );
	$t_bug_data->status				= gpc_get_int( 'status', $t_bug_data->status );
	$t_bug_data->resolution			= gpc_get_int( 'resolution', $t_bug_data->resolution );
	$t_bug_data->projection			= gpc_get_int( 'projection', $t_bug_data->projection );
	$t_bug_data->category			= gpc_get_string( 'category', $t_bug_data->category );
	$t_bug_data->eta				= gpc_get_int( 'eta', $t_bug_data->eta );
	$t_bug_data->os					= gpc_get_string( 'os', $t_bug_data->os );
	$t_bug_data->os_build			= gpc_get_string( 'os_build', $t_bug_data->os_build );
	$t_bug_data->platform			= gpc_get_string( 'platform', $t_bug_data->platform );
	$t_bug_data->version			= gpc_get_string( 'version', $t_bug_data->version );
	$t_bug_data->build				= gpc_get_string( 'build', $t_bug_data->build );
	$t_bug_data->fixed_in_version		= gpc_get_string( 'fixed_in_version', $t_bug_data->fixed_in_version );
	$t_bug_data->view_state			= gpc_get_int( 'view_state', $t_bug_data->view_state );
	$t_bug_data->summary			= gpc_get_string( 'summary', $t_bug_data->summary );

	$t_bug_data->description		= gpc_get_string( 'description', $t_bug_data->description );
	$t_bug_data->steps_to_reproduce	= gpc_get_string( 'steps_to_reproduce', $t_bug_data->steps_to_reproduce );
	$t_bug_data->additional_information	= gpc_get_string( 'additional_information', $t_bug_data->additional_information );

	$f_private						= gpc_get_bool( 'private' );
	$f_bugnote_text					= gpc_get_string( 'bugnote_text', '' );
	$f_close_now					= gpc_get_string( 'close_now', false );

	# Handle auto-assigning
	if ( ( NEW_ == $t_bug_data->status )
	  && ( 0 != $t_bug_data->handler_id )
	  && ( ON == config_get( 'auto_set_status_to_assigned' ) ) ) {
		$t_bug_data->status = config_get( 'bug_assigned_status' );
	}

	helper_call_custom_function( 'issue_update_validate', array( $f_bug_id, $t_bug_data, $f_bugnote_text ) );

	$t_resolved = config_get( 'bug_resolved_status_threshold' );

	$t_custom_status_label = "update"; # default info to check
	if ( $t_bug_data->status == $t_resolved ) {
		$t_custom_status_label = "resolved";
	}
	if ( $t_bug_data->status == CLOSED ) {
		$t_custom_status_label = "closed";
	}
	
	$t_related_custom_field_ids = custom_field_get_linked_ids( $t_bug_data->project_id );
	foreach( $t_related_custom_field_ids as $t_id ) {
		$t_def = custom_field_get_definition( $t_id );
		$t_custom_field_value = gpc_get_custom_field( "custom_field_$t_id", $t_def['type'], null );

		# Only update the field if it would have been display for editing
		if( !( $t_def['display_' . $t_custom_status_label] || $t_def['require_' . $t_custom_status_label] ) ) {
			continue;
		}
		
		# Only update the field if it is posted
		if ( $t_custom_field_value === null ) {
			continue;
		}

		# Do not set custom field value if user has no write access.
		if( !custom_field_has_write_access( $t_id, $f_bug_id ) ) {
			continue;
		}

		if ( $t_def['require_' . $t_custom_status_label] && ( gpc_get_custom_field( "custom_field_$t_id", $t_def['type'], '' ) == '' ) ) {
			error_parameters( lang_get_defaulted( custom_field_get_field( $t_id, 'name' ) ) );
			trigger_error( ERROR_EMPTY_FIELD, ERROR );
		}
		if ( !custom_field_set_value( $t_id, $f_bug_id, $t_custom_field_value ) ) {
			error_parameters( lang_get_defaulted( custom_field_get_field( $t_id, 'name' ) ) );
			trigger_error( ERROR_CUSTOM_FIELD_INVALID_VALUE, ERROR );
		}
	}

	$t_notify = true;
	$t_bug_note_set = false;
	if ( ( $t_old_bug_status != $t_bug_data->status ) && ( FALSE == $f_update_mode ) ) {
		# handle status transitions that come from pages other than bug_*update_page.php
		# this does the minimum to act on the bug and sends a specific message
		switch ( $t_bug_data->status ) {
			case $t_resolved:
				# bug_resolve updates the status and bugnote and sends message
				bug_resolve( $f_bug_id, $t_bug_data->resolution, $t_bug_data->fixed_in_version, 
						$f_bugnote_text, $t_bug_data->duplicate_id, $t_bug_data->handler_id);
				$t_notify = false;
				$t_bug_note_set = true;

				if ( $f_close_now ) {
					bug_set_field( $f_bug_id, 'status', CLOSED );
				}

				// update bug data with fields that may be updated inside bug_resolve(), otherwise changes will be overwritten
				// in bug_update() call below.
				$t_bug_data->handler_id = bug_get_field( $f_bug_id, 'handler_id' );
				$t_bug_data->status = bug_get_field( $f_bug_id, 'status' );
				break;

			case CLOSED:
				# bug_close updates the status and bugnote and sends message
				bug_close( $f_bug_id, $f_bugnote_text );
				$t_notify = false;
				$t_bug_note_set = true;

				// update bug data with fields that may be updated inside bug_resolve(), otherwise changes will be overwritten
				// in bug_update() call below.
				$t_bug_data->status = bug_get_field( $f_bug_id, 'status' );
				$t_bug_data->resolution = bug_get_field( $f_bug_id, 'resolution' );
				break;

			case config_get( 'bug_reopen_status' ):
				if ( $t_old_bug_status >= $t_resolved ) {
					bug_set_field( $f_bug_id, 'handler_id', $t_bug_data->handler_id ); # fix: update handler_id before calling bug_reopen
					# bug_reopen updates the status and bugnote and sends message
					bug_reopen( $f_bug_id, $f_bugnote_text );
					$t_notify = false;
					$t_bug_note_set = true;

					// update bug data with fields that may be updated inside bug_resolve(), otherwise changes will be overwritten
					// in bug_update() call below.
					$t_bug_data->status = bug_get_field( $f_bug_id, 'status' );
					$t_bug_data->resolution = bug_get_field( $f_bug_id, 'resolution' );
					break;
				} # else fall through to default
		}
	}
		
	# Add a bugnote if there is one
	if ( ( !is_blank( $f_bugnote_text ) ) && ( false == $t_bug_note_set ) ) {
		bugnote_add( $f_bug_id, $f_bugnote_text, $f_private );
	}

	# Update the bug entry, notify if we haven't done so already
	bug_update( $f_bug_id, $t_bug_data, true, ( false == $t_notify ) );

	helper_call_custom_function( 'issue_update_notify', array( $f_bug_id ) );
  
	print_successful_redirect_to_bug( $f_bug_id );
?>
