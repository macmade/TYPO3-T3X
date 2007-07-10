<?php
// Security check to prevent use outside of TYPO3
// @author      Macmade - 27.05.2007
if( !isset( $GLOBALS[ 'BE_USER' ]->user[ 'admin' ] ) || $GLOBALS[ 'BE_USER' ]->user[ 'admin' ] != 1
    || !isset( $_COOKIE[ $GLOBALS[ 'BE_USER' ]->user[ 'ses_name' ] ] )
    || $_COOKIE[ $GLOBALS[ 'BE_USER' ]->user[ 'ses_name' ] ] != $GLOBALS[ 'BE_USER' ]->user[ 'ses_id' ] ) {
    die( 'Access denied' );
}
?>
			<?php
				$owner_infos = posix_getpwuid($DATA['OWNER'][$_GET['id']]);
				$group_infos = posix_getgrgid($DATA['GROUP'][$_GET['id']]);
				$type_id = ($DATA['TYPE'][$_GET['id']] == 'dir') ? count($GLOBALS['FILETYPES']) - 2 : checkType($dir . $DATA['NAME'][$_GET['id']]);
				$type =getLang('files' . $GLOBALS['FILETYPES'][$type_id]['ext']);
				$size = ($DATA['TYPE'][$_GET['id']] == 'dir') ? '-' : number_format($DATA['SIZE'][$_GET['id']] / 1024,2) . 'kb';
				$mod = sprintf('%o', $DATA['PERMS'][$_GET['id']]);
				$read = ($DATA['READ'][$_GET['id']] == '1') ? getLang('global.yes') : getLang('global.no');
				$write = ($DATA['WRITE'][$_GET['id']] == '1') ? getLang('global.yes') : getLang('global.no');
			?>
			<fieldset>
				<legend class="legend"><?php echo(getLang('options.properties')); ?></legend>
					<table border="0" width="100%" cellspacing="0" cellpadding="0" align="center">
						<tr>
							<td width="35" align="left" valign="top"><?php echo(getLang('options.name')); ?></td>
							<td width="5" align="left" valign="top"></td>
							<td align="left" valign="top" class="properties"><?php echo($DATA['NAME'][$_GET['id']]); ?></td>
						</tr>
						<tr>
							<td width="35" align="left" valign="top"><?php echo(getLang('options.type')); ?></td>
							<td width="5" align="left" valign="top"></td>
							<td align="left" valign="top" class="properties"><?php echo($type); ?></td>
						</tr>
						<tr>
							<td width="35" align="left" valign="top"><?php echo(getLang('options.size')); ?></td>
							<td width="5" align="left" valign="top"></td>
							<td align="left" valign="top" class="properties"><?php echo($size); ?></td>
						</tr>
						<tr>
							<td width="35" align="left" valign="top"><?php echo(getLang('options.perms')); ?></td>
							<td width="5" align="left" valign="top"></td>
							<td align="left" valign="top" class="properties"><?php echo(getUnixPerms($DATA['OWNER'][$_GET['id']])); ?></td>
						</td>
						<tr>
							<td width="35" align="left" valign="top"><?php echo(getLang('options.mod')); ?></td>
							<td width="5" align="left" valign="top"></td>
							<td align="left" valign="top" class="properties"><?php echo(substr($mod, strlen($mod) - 3, 3)); ?></td>
						</tr>
						<tr>
							<td width="35" align="left" valign="top"><?php echo(getLang('options.read')); ?></td>
							<td width="5" align="left" valign="top"></td>
							<td align="left" valign="top" class="properties"><?php echo($read); ?></td>
						</tr>
						<tr>
							<td width="35" align="left" valign="top"><?php echo(getLang('options.write')); ?></td>
							<td width="5" align="left" valign="top"></td>
							<td align="left" valign="top" class="properties"><?php echo($write); ?></td>
						</tr>
						<tr>
							<td width="35" align="left" valign="top"><?php echo(getLang('options.uid')); ?></td>
							<td width="5" align="left" valign="top"></td>
							<td align="left" valign="top" class="properties"><?php echo($DATA['OWNER'][$_GET['id']] . " (" . $owner_infos["name"] . ")"); ?></td>
						</tr>
						<tr>
							<td width="35" align="left" valign="top"><?php echo(getLang('options.gid')); ?></td>
							<td width="5" align="left" valign="top"></td>
							<td align="left" valign="top" class="properties"><?php echo($DATA['GROUP'][$_GET['id']] . " (" . $group_infos["name"] . ")"); ?></td>
						</tr>
						<tr>
							<td width="35" align="left" valign="top"><?php echo(getLang('options.ctime')); ?></td>
							<td width="5" align="left" valign="top"></td>
							<td align="left" valign="top" class="properties"><?php echo(date($CONFIG['DATE_FORMAT'], $DATA['CTIME'][$_GET['id']])); ?></td>
						</tr>
						<tr>
							<td width="35" align="left" valign="top"><?php echo(getLang('options.atime')); ?></td>
							<td width="5" align="left" valign="top"></td>
							<td align="left" valign="top" class="properties"><?php echo(date($CONFIG['DATE_FORMAT'], $DATA['ATIME'][$_GET['id']])); ?></td>
						</tr>
					</table>
			</fieldset>
