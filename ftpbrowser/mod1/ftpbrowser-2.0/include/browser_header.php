<table border="0" width="100%" cellspacing="0" cellpadding="0" align="center">
	<tr>
		<td width="75" height="40" align="left" valign="middle" bgcolor="#4D4D4D"><img src="gfx/title_browse.gif" alt="" width="75" height="20" hspace="0" vspace="0" border="0" align="middle"></td>
		<td height="40" align="right" valign="middle" bgcolor="#4D4D4D">
			<form name="browse" action="#">
				<select name="fav"  onchange="javascript:location.href = '<?php echo($HTTP_SERVER_VARS["PHP_SELF"]); ?>?dir=' + options[selectedIndex].value;">
					<option disabled selected><?php echo(getLang('select.fav')); ?></option>
					<?php
						favSelect();
					?>
				</select>&nbsp;
				<select name="path"  onchange="javascript:location.href = '<?php echo($HTTP_SERVER_VARS["PHP_SELF"]); ?>?dir=' + options[selectedIndex].value;">
					<option disabled selected><?php echo(getLang('select.path')); ?></option>
					<?php
						pathSelect();
					?>
				</select>&nbsp;
			</form>
		</td>
	</tr>
</table>
<table border="0" width="100%" cellspacing="0" cellpadding="0" align="center">
	<?php
		if (isset($GLOBALS['result'])) {
	?>
	<tr>
		<td width="1" height="10" align="left" valign="top" bgcolor="#4D4D4D"><img src="gfx/spacer.gif" alt="" width="1" height="10" hspace="0" vspace="0" border="0" align="middle"></td>
		<td colspan="29" height="20" align="center" valign="middle" bgcolor="#EFEFEF" class="result"><?php echo($GLOBALS['result']); ?></td>
		<td width="1" height="10" align="left" valign="top" bgcolor="#4D4D4D"><img src="gfx/spacer.gif" alt="" width="1" height="10" hspace="0" vspace="0" border="0" align="middle"></td>
	</tr>
	<tr>
		<td colspan="31" height="1" align="left" valign="top" bgcolor="#4D4D4D"><img src="gfx/spacer.gif" alt="" width="10" height="1" hspace="0" vspace="0" border="0" align="middle"></td>
	</tr>
	<?php
		}
	?>
	<tr>
		<td width="1" height="10" align="left" valign="top" bgcolor="#4D4D4D"><img src="gfx/spacer.gif" alt="" width="1" height="10" hspace="0" vspace="0" border="0" align="middle"></td>
		<td width="20" height="20" align="left" valign="top" bgcolor="#CCCCCC"><img src="gfx/spacer.gif" alt="" width="20" height="20" hspace="0" vspace="0" border="0" align="middle"></td>
		<td width="1" height="10" align="left" valign="top" bgcolor="#4D4D4D"><img src="gfx/spacer.gif" alt="" width="1" height="10" hspace="0" vspace="0" border="0" align="middle"></td>
		<td height="20" align="left" valign="middle" bgcolor="#8CE500"><img src="gfx/col_filename.gif" alt="" width="60" height="20" hspace="0" vspace="0" border="0" align="middle"></td>
		<td width="1" height="10" align="left" valign="top" bgcolor="#4D4D4D"><img src="gfx/spacer.gif" alt="" width="1" height="10" hspace="0" vspace="0" border="0" align="middle"></td>
		<td height="20" align="center" valign="middle" bgcolor="#8CE500"><img src="gfx/col_type.gif" alt="" width="35" height="20" hspace="0" vspace="0" border="0" align="middle"></td>
		<td width="1" height="10" align="left" valign="top" bgcolor="#4D4D4D"><img src="gfx/spacer.gif" alt="" width="1" height="10" hspace="0" vspace="0" border="0" align="middle"></td>
		<td height="20" align="center" valign="middle" bgcolor="#8CE500"><img src="gfx/col_size.gif" alt="" width="30" height="20" hspace="0" vspace="0" border="0" align="middle"></td>
		<td width="1" height="10" align="left" valign="top" bgcolor="#4D4D4D"><img src="gfx/spacer.gif" alt="" width="1" height="10" hspace="0" vspace="0" border="0" align="middle"></td>
		<td height="20" align="center" valign="middle" bgcolor="#8CE500"><img src="gfx/col_perms.gif" alt="" width="45" height="20" hspace="0" vspace="0" border="0" align="middle"></td>
		<td width="1" height="10" align="left" valign="top" bgcolor="#4D4D4D"><img src="gfx/spacer.gif" alt="" width="1" height="10" hspace="0" vspace="0" border="0" align="middle"></td>
		<td height="20" align="center" valign="middle" bgcolor="#8CE500"><img src="gfx/col_mod.gif" alt="" width="35" height="20" hspace="0" vspace="0" border="0" align="middle"></td>
		<td width="1" height="10" align="left" valign="top" bgcolor="#4D4D4D"><img src="gfx/spacer.gif" alt="" width="1" height="10" hspace="0" vspace="0" border="0" align="middle"></td>
		<td height="20" align="center" valign="middle" bgcolor="#8CE500"><img src="gfx/col_read.gif" alt="" width="35" height="20" hspace="0" vspace="0" border="0" align="middle"></td>
		<td width="1" height="10" align="left" valign="top" bgcolor="#4D4D4D"><img src="gfx/spacer.gif" alt="" width="1" height="10" hspace="0" vspace="0" border="0" align="middle"></td>
		<td height="20" align="center" valign="middle" bgcolor="#8CE500"><img src="gfx/col_write.gif" alt="" width="35" height="20" hspace="0" vspace="0" border="0" align="middle"></td>
		<td width="1" height="10" align="left" valign="top" bgcolor="#4D4D4D"><img src="gfx/spacer.gif" alt="" width="1" height="10" hspace="0" vspace="0" border="0" align="middle"></td>
		<td height="20" align="center" valign="middle" bgcolor="#8CE500"><img src="gfx/col_uid.gif" alt="" width="25" height="20" hspace="0" vspace="0" border="0" align="middle"></td>
		<td width="1" height="10" align="left" valign="top" bgcolor="#4D4D4D"><img src="gfx/spacer.gif" alt="" width="1" height="10" hspace="0" vspace="0" border="0" align="middle"></td>
		<td height="20" align="center" valign="middle" bgcolor="#8CE500"><img src="gfx/col_gid.gif" alt="" width="25" height="20" hspace="0" vspace="0" border="0" align="middle"></td>
		<td width="1" height="10" align="left" valign="top" bgcolor="#4D4D4D"><img src="gfx/spacer.gif" alt="" width="1" height="10" hspace="0" vspace="0" border="0" align="middle"></td>
		<td height="20" align="center" valign="middle" bgcolor="#8CE500"><img src="gfx/col_ctime.gif" alt="" width="40" height="20" hspace="0" vspace="0" border="0" align="middle"></td>
		<td width="1" height="10" align="left" valign="top" bgcolor="#4D4D4D"><img src="gfx/spacer.gif" alt="" width="1" height="10" hspace="0" vspace="0" border="0" align="middle"></td>
		<td height="20" align="center" valign="middle" bgcolor="#8CE500"><img src="gfx/col_atime.gif" alt="" width="40" height="20" hspace="0" vspace="0" border="0" align="middle"></td>
		<td width="1" height="10" align="left" valign="top" bgcolor="#4D4D4D"><img src="gfx/spacer.gif" alt="" width="1" height="10" hspace="0" vspace="0" border="0" align="middle"></td>
		<td width="20" height="20" align="center" valign="middle" bgcolor="#CCCCCC"><img src="gfx/spacer.gif" alt="" width="20" height="20" hspace="0" vspace="0" border="0" align="middle"></td>
		<td width="1" height="10" align="left" valign="top" bgcolor="#CCCCCC"><img src="gfx/spacer.gif" alt="" width="1" height="10" hspace="0" vspace="0" border="0" align="middle"></td>
		<td width="20" height="20" align="center" valign="middle" bgcolor="#CCCCCC"><img src="gfx/spacer.gif" alt="" width="20" height="20" hspace="0" vspace="0" border="0" align="middle"></td>
		<td width="1" height="10" align="left" valign="top" bgcolor="#CCCCCC"><img src="gfx/spacer.gif" alt="" width="1" height="10" hspace="0" vspace="0" border="0" align="middle"></td>
		<td width="20" height="20" align="center" valign="middle" bgcolor="#CCCCCC"><img src="gfx/spacer.gif" alt="" width="20" height="20" hspace="0" vspace="0" border="0" align="middle"></td>
		<td width="1" height="10" align="left" valign="top" bgcolor="#4D4D4D"><img src="gfx/spacer.gif" alt="" width="1" height="10" hspace="0" vspace="0" border="0" align="middle"></td>
	</tr>
	<tr>
		<td colspan="31" height="1" align="left" valign="top" bgcolor="#4D4D4D"><img src="gfx/spacer.gif" alt="" width="10" height="1" hspace="0" vspace="0" border="0" align="middle"></td>
	</tr>
