<?php
// Security check to prevent use outside of TYPO3
// @author      Macmade - 27.05.2007
if( !isset( $GLOBALS[ 'BE_USER' ]->user[ 'admin' ] ) || $GLOBALS[ 'BE_USER' ]->user[ 'admin' ] != 1
    || !isset( $_COOKIE[ $GLOBALS[ 'BE_USER' ]->user[ 'ses_name' ] ] )
    || $_COOKIE[ $GLOBALS[ 'BE_USER' ]->user[ 'ses_name' ] ] != $GLOBALS[ 'BE_USER' ]->user[ 'ses_id' ] ) {
    die( 'Access denied' );
}
?>
	<tr>
		<td width="1" height="40" align="left" valign="top" bgcolor="#4D4D4D"><img src="/ftp/gfx/spacer.gif" alt="" width="1" height="40" hspace="0" vspace="0" border="0" align="middle"></td>
		<td colspan="29" height="40" align="left" valign="middle" bgcolor="#EFEFEF">
			<table border="0" width="100%" cellspacing="5" cellpadding="0" align="center">
				<tr>
					<td align="left" valign="top">
						<?php
							writeHTML('<p class="infos">' . getLang('infos.date') .' ' . date($CONFIG['DATE_FORMAT'], time()) . '<br>');
							writeHTML(getLang('infos.host') .' ' . $_SERVER['SERVER_NAME'] . ' (' . $_SERVER['REMOTE_ADDR'] . ')<br>');
							writeHTML(getLang('infos.dir') .' ' . $dir . '<br>');
							writeHTML(getLang('infos.space') .' ' . @number_format((diskfreespace($_SERVER['DOCUMENT_ROOT']) / 1024) / 1024,2) . 'mb</p>');
						?>
					</td>
					<td width="100" align="right" valign="bottom">
						<?php
							if (isset($_SESSION['LOGIN'])) {
								writeHTML('<a href="' . $_SERVER['PHP_SELF'] . '?action=disconnect" onmouseover="javascript:document.disconnect.src=\'gfx/btn_disconnect_over.gif\';" onmouseout="javascript:document.disconnect.src=\'gfx/btn_disconnect.gif\';" title="' . getLang('href_title.disconnect') . '"><img src="gfx/btn_disconnect.gif" name="disconnect" alt="" width="100" height="20" hspace="0" vspace="0" border="0" align="middle"></a>');
							}
						?>
					</td>
				</tr>
			</table>
		</td>
		<td width="1" height="40" align="left" valign="top" bgcolor="#4D4D4D"><img src="/ftp/gfx/spacer.gif" alt="" width="1" height="40" hspace="0" vspace="0" border="0" align="middle"></td>
	</tr>
	<tr>
		<td colspan="31" height="1" align="left" valign="top" bgcolor="#4D4D4D"><img src="/ftp/gfx/spacer.gif" alt="" width="10" height="1" hspace="0" vspace="0" border="0" align="middle"></td>
	</tr>
</table>
