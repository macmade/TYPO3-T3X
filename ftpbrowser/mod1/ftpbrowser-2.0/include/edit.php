<table border="0" width="100%" cellspacing="0" cellpadding="0" align="center">
	<tr>
		<td width="79%" align="left" valign="top">
			<form action="<?php echo($_SERVER['PHP_SELF'] . '?dir=' . $dir); ?>" method="post" name="edit">
				<input type="hidden" name="action" value="editfile">
				<input type="hidden" name="edit" value="<?php echo($_GET['edit']); ?>">
				<fieldset>
					<legend class="legend"><?php echo(getLang('options.edit')); ?></legend>
					<?php
						if ($GLOBALS['DATA']['READ'][$_GET['id']] == '1' && $GLOBALS['DATA']['WRITE'][$_GET['id']] == '1') {
							
							/**
							 * Get permissions
							 */
							$filemod = sprintf('%o', fileperms($dir . $_GET['edit']));
							$filemod = substr($filemod, strlen($filemod) - 3, 3);
							$user = substr($filemod,0,1);
							$group = substr($filemod,1,1);
							$world = substr($filemod,2,1);
							
							/**
							 * Checkboxes utility
							 */
							if ($user >= 4) {
								$ur = 'checked';
							}
							if ($user == 2 || $user == 3 || $user == 6 || $user == 7) {
								$uw = 'checked';
							}
							if ($user == 1 || $user == 3 || $user == 5 || $user == 7) {
								$ux = 'checked';
							}
							if ($group >= 4) {
								$gr = 'checked';
							}
							if ($group == 2 || $group == 3 || $group == 6 || $group == 7) {
								$gw = 'checked';
							}
							if ($group == 1 || $group == 3 || $group == 5 || $group == 7) {
								$gx = 'checked';
							}
							if ($world >= 4) {
								$wr = 'checked';
							}
							if ($world == 2 || $world == 3 || $world == 6 || $world == 7) {
								$ww = 'checked';
							}
							if ($world == 1 || $world == 3 || $world == 5 || $world == 7) {
								$wx = 'checked';
							}
					?>
					<table border="0" width="100%" cellspacing="5" cellpadding="0" align="center">
						<tr>
							<td width="50" align="left" valign="middle"><?php echo(getLang('options.name')); ?></td>
							<td align="left" valign="middle">
								<input type="text" name="name" size="35" value="<?php echo($_GET['edit']); ?>">
							</td>
						</tr>
						<tr>
							<td width="50" align="left" valign="middle"><?php echo(getLang('options.perms')); ?></td>
							<td align="left" valign="middle">
								<input type="checkbox" name="ur" value="4" <?php echo($ur); ?> onclick="javascript:setPerms('edit');">
								<input type="checkbox" name="uw" value="2" <?php echo($uw); ?> onclick="javascript:setPerms('edit');">
								<input type="checkbox" name="ux" value="1" <?php echo($ux); ?> onclick="javascript:setPerms('edit');">
								 - <input type="checkbox" name="gr" value="4" <?php echo($gr); ?> onclick="javascript:setPerms('edit');">
								<input type="checkbox" name="gw" value="2" <?php echo($gw); ?> onclick="javascript:setPerms('edit');">
								<input type="checkbox" name="gx" value="1" <?php echo($gx); ?> onclick="javascript:setPerms('edit');">
								 - <input type="checkbox" name="wr" value="4" <?php echo($wr); ?> onclick="javascript:setPerms('edit');">
								<input type="checkbox" name="ww" value="2" <?php echo($ww); ?> onclick="javascript:setPerms('edit');">
								<input type="checkbox" name="wx" value="1" <?php echo($wx); ?> onclick="javascript:setPerms('edit');">
								<input type="text" name="mod" value="<?php echo($filemod); ?>" size="3" disabled>
							</td>
						</tr>
						<?php
							for ($i = 0; $i < count($GLOBALS['FILETYPES']); $i++) {
								
								/**
								 * Check if file is editable
								 */
								if (ereg('.+' . $GLOBALS['FILETYPES'][$i]['ext'] . '$',$_GET['edit'])) {
									if ($GLOBALS['FILETYPES'][$i]['edit']) {
										$can_edit = true;
									}
									break;
								}
							}
							
							/**
							 * Display file content
							 */
							if (is_file($dir . $_GET['edit']) && $can_edit == 'true') {
								$fileopen = fopen($dir . $_GET['edit'], 'r');
						?>
						<tr>
							<td width="50" align="left" valign="top"><?php echo(getLang('options.content')); ?></td>
							<td align="left" valign="middle">
								<textarea name="content" rows="10" cols="75"><?php fpassthru($fileopen); ?></textarea>
							</td>
						</tr>
						<?php
								fclose($fileopen);
							}
						?>
						<tr>
							<td width="50" align="left" valign="middle"></td>
							<td align="left" valign="middle">
								<a href="javascript:document.edit.submit();" title="<?php echo(getLang('href_title.edit')); ?>"><img src="gfx/btn_go.gif" alt="" width="30" height="20" hspace="0" vspace="0" border="0" align="middle"></a>
							</td>
						</tr>
					</table>
					<?php
						}
						else {
					?>
					<center>
						<p class="result">sorry, can't read / write</p>
					</center>
					<?php
						}
					?>
				</fieldset>
			</form>
		</td>
		<td width="2%" align="left" valign="top"></td>
		<td width="19%" align="left" valign="top">
			<?php
				require($CONFIG['FTP_PATH'] . 'include/properties.php');
			?>
		</td>
	</tr>
</table>
<br>
