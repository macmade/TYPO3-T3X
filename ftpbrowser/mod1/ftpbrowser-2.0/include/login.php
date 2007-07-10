<?php
	if (isset($result)) {
?>
<p align="center" class="result"><?php echo($result); ?></p>
<?php
	}
?>
<table border="0" width="390" cellspacing="0" cellpadding="0" align="center">
	<tr>
		<td width="515" align="left" valign="top">
			<form action="<?php echo($_SERVER['PHP_SELF']); ?>" method="post" name="login">
				<input type="hidden" name="action" value="login">
				<fieldset>
					<legend class="legend"><?php echo(getLang('login.legend')); ?></legend>
					<table border="0" width="100%" cellspacing="5" cellpadding="0" align="center">
						<tr>
							<td width="50" align="left" valign="middle"><?php echo(getLang('login.username')); ?>:</td>
							<td align="left" valign="middle">
								<input type="text" name="log" size="35">
							</td>
						</tr>
						<tr>
							<td width="50" align="left" valign="middle"><?php echo(getLang('login.password')); ?>:</td>
							<td align="left" valign="middle">
								<input type="password" name="pwd" size="35">
							</td>
						</tr>
						<tr>
							<td width="50" align="left" valign="middle"></td>
							<td align="left" valign="middle">
								<a href="javascript:document.login.submit();" title="<?php echo(getLang('href_title.connect')); ?>"><img src="gfx/btn_go.gif" alt="" width="30" height="20" hspace="0" vspace="0" border="0" align="middle"></a>
							</td>
						</tr>
					</table>
				</fieldset>
			</form>
		</td>
	</tr>
</table>
