<br>
<table border="0" width="100%" cellspacing="0" cellpadding="0" align="center">
	<tr>
		<td width="49%" align="left" valign="top">
			<form action="<?php echo($_SERVER['PHP_SELF'] . '?dir=' . $dir); ?>" method="post" name="mkdir">
				<input type="hidden" name="action" value="mkdir">
				<fieldset>
					<legend class="legend"><?php echo(getLang('options.newdir')); ?></legend>
					<table border="0" width="100%" cellspacing="5" cellpadding="0" align="center">
						<tr>
							<td width="50" align="left" valign="middle"><?php echo(getLang('options.name')); ?></td>
							<td align="left" valign="middle">
								<input type="text" name="name" size="35">
							</td>
						</tr>
						<tr>
							<td width="50" align="left" valign="middle"><?php echo(getLang('options.perms')); ?></td>
							<td align="left" valign="middle">
								<input type="checkbox" name="ur" value="4" checked onclick="javascript:setPerms('mkdir');">
								<input type="checkbox" name="uw" value="2" checked onclick="javascript:setPerms('mkdir');">
								<input type="checkbox" name="ux" value="1" checked onclick="javascript:setPerms('mkdir');">
								 - <input type="checkbox" name="gr" value="4" checked onclick="javascript:setPerms('mkdir');">
								<input type="checkbox" name="gw" value="2" onclick="javascript:setPerms('mkdir');">
								<input type="checkbox" name="gx" value="1" checked onclick="javascript:setPerms('mkdir');">
								 - <input type="checkbox" name="wr" value="4" checked onclick="javascript:setPerms('mkdir');">
								<input type="checkbox" name="ww" value="2" onclick="javascript:setPerms('mkdir');">
								<input type="checkbox" name="wx" value="1" checked onclick="javascript:setPerms('mkdir');">
								<input type="text" name="mod" value="755" size="3" disabled>
							</td>
						</tr>
						<tr>
							<td width="50" align="left" valign="middle"></td>
							<td align="left" valign="middle">
								<a href="javascript:document.mkdir.submit();" title="<?php echo(getLang('href_title.newdir')); ?>"><img src="gfx/btn_go.gif" alt="" width="30" height="20" hspace="0" vspace="0" border="0" align="middle"></a>
							</td>
						</tr>
					</table>
				</fieldset>
			</form>
			<form action="<?php echo($_SERVER['PHP_SELF'] . '?dir=' . $dir); ?>" method="post" name="upload" enctype="multipart/form-data">
				<input type="hidden" name="action" value="upload">
				<fieldset>
					<legend class="legend"><?php echo(getLang('options.upload')); ?></legend>
					<table border="0" width="100%" cellspacing="5" cellpadding="0" align="center">
						<tr>
							<td width="50" align="left" valign="middle"><?php echo(getLang('options.file')); ?></td>
							<td align="left" valign="middle">
								<input type="file" name="upload">
							</td>
						</tr>
						<tr>
							<td width="50" align="left" valign="middle"><?php echo(getLang('options.perms')); ?></td>
							<td align="left" valign="middle">
								<input type="checkbox" name="ur" value="4" checked onclick="javascript:setPerms('upload');">
								<input type="checkbox" name="uw" value="2" checked onclick="javascript:setPerms('upload');">
								<input type="checkbox" name="ux" value="1" checked onclick="javascript:setPerms('upload');">
								 - <input type="checkbox" name="gr" value="4" checked onclick="javascript:setPerms('upload');">
								<input type="checkbox" name="gw" value="2" onclick="javascript:setPerms('upload');">
								<input type="checkbox" name="gx" value="1" checked onclick="javascript:setPerms('upload');">
								 - <input type="checkbox" name="wr" value="4" checked onclick="javascript:setPerms('upload');">
								<input type="checkbox" name="ww" value="2" onclick="javascript:setPerms('upload');">
								<input type="checkbox" name="wx" value="1" checked onclick="javascript:setPerms('upload');">
								<input type="text" name="mod" value="755" size="3" disabled>
							</td>
						</tr>
						<tr>
							<td width="50" align="left" valign="middle"></td>
							<td align="left" valign="middle">
								<a href="javascript:document.upload.submit();" title="<?php echo(getLang('href_title.upload')); ?>"><img src="gfx/btn_go.gif" alt="" width="30" height="20" hspace="0" vspace="0" border="0" align="middle"></a>
							</td>
						</tr>
					</table>
				</fieldset>
			</form>
		</td>
		<td width="2%" align="left" valign="top"></td>
		<td width="49%" align="left" valign="top">
			<form action="<?php echo($_SERVER['PHP_SELF'] . '?dir=' . $dir); ?>" method="post" name="newfile">
				<input type="hidden" name="action" value="newfile">
				<fieldset>
					<legend class="legend"><?php echo(getLang('options.newfile')); ?></legend>
					<table border="0" width="100%" cellspacing="5" cellpadding="0" align="center">
						<tr>
							<td width="50" align="left" valign="middle"><?php echo(getLang('options.name')); ?></td>
							<td align="left" valign="middle">
								<input type="text" name="name" size="35">
							</td>
						</tr>
						<tr>
							<td width="50" align="left" valign="middle"><?php echo(getLang('options.extension')); ?></td>
							<td align="left" valign="middle">
								<select name="extension">
									<option value="none"><?php echo(getLang('select.newfile')); ?></option>
									<?php
										fileTypeSelect();
									?>
								</select>
							</td>
						</tr>
						<tr>
							<td width="50" align="left" valign="middle"><?php echo(getLang('options.perms')); ?></td>
							<td align="left" valign="middle">
								<input type="checkbox" name="ur" value="4" checked onclick="javascript:setPerms('newfile');">
								<input type="checkbox" name="uw" value="2" checked onclick="javascript:setPerms('newfile');">
								<input type="checkbox" name="ux" value="1" checked onclick="javascript:setPerms('newfile');">
								 - <input type="checkbox" name="gr" value="4" checked onclick="javascript:setPerms('newfile');">
								<input type="checkbox" name="gw" value="2" onclick="javascript:setPerms('newfile');">
								<input type="checkbox" name="gx" value="1" checked onclick="javascript:setPerms('newfile');">
								 - <input type="checkbox" name="wr" value="4" checked onclick="javascript:setPerms('newfile');">
								<input type="checkbox" name="ww" value="2" onclick="javascript:setPerms('newfile');">
								<input type="checkbox" name="wx" value="1" checked onclick="javascript:setPerms('newfile');">
								<input type="text" name="mod" value="755" size="3" disabled>
							</td>
						</tr>
						<tr>
							<td width="50" align="left" valign="top"><?php echo(getLang('options.content')); ?></td>
							<td align="left" valign="middle">
								<textarea name="content" rows="10" cols="40"></textarea>
							</td>
						</tr>
						<tr>
							<td width="50" align="left" valign="middle"></td>
							<td align="left" valign="middle">
								<a href="javascript:document.newfile.submit();" title="<?php echo(getLang('href_title.newfile')); ?>"><img src="gfx/btn_go.gif" alt="" width="30" height="20" hspace="0" vspace="0" border="0" align="middle"></a>
							</td>
						</tr>
					</table>
				</fieldset>
			</form>
		</td>
	</tr>
</table>
