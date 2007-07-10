<?php
	/***************************************************************
	 * Copyright notice
	 * 
	 * (c) 2004 macmade.net
	 * All rights reserved
	 * 
	 * This program is free software; you can redistribute it and/or
	 * modify it under the terms of the GNU General Public License as
	 * published by the Free Software Foundation; either version 2
	 * of the License, or (at your option) any later version.
	 * 
	 * This script is distributed in the hope that it will be useful,
	 * but WITHOUT ANY WARRANTY; without even the implied warranty of
	 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	 * GNU General Public License for more details.
	 * 
	 * The GNU General Public License can be found at
	 * http://www.gnu.org/copyleft/gpl.html
	 * 
	 * This copyright notice MUST APPEAR in all copies of the script!
	 **************************************************************/
	
	/**
	 * File 'lang/EN.php' / English language page.
	 * 
	 * @author		Jean-David Gadina / macmade.net (info@macmade.net)
	 * @version		2.0
	 */
	$LANG = array();
	$LANG['EN'] = array(
		'header.title' => '+ FTP Browser +',
		'login.legend' => 'please login',
		'login.username' => 'username',
		'login.password' => 'password',
		'login.href' => 'connect',
		'login.error' => 'sorry, wrong username or password',
		'files.txt' => 'Text Document',
		'files.rtf' => 'Rich Text Document',
		'files.doc' => 'MS-Word Document',
		'files.xls' => 'Excel Document',
		'files.pps' => 'PowerPoint Document',
		'files.swx' => 'Open Office Document',
		'files.jpg' => 'JPEG File',
		'files.gif' => 'GIF File',
		'files.png' => 'PNG File',
		'files.tif' => 'TIF File',
		'files.eps' => 'EPS File',
		'files.bmp' => 'Bitmap File',
		'files.ps' => 'PostScript File',
		'files.psd' => 'Photoshop Document',
		'files.ai' => 'Illustrator Document',
		'files.freehand' => 'FreeHand Document',
		'files.pdf' => 'Portable Document Format',
		'files.html' => 'HyperText Markup Language',
		'files.shtml' => 'Server Parsed HTML',
		'files.xml' => 'Extended Markup Language',
		'files.css' => 'Cascading Stylesheet',
		'files.xss' => 'Extended Stylesheet',
		'files.dtd' => 'Doctype Definition',
		'files.php' => 'PHP Document',
		'files.asp' => 'Active Server Page',
		'files.js' => 'Javascript Document',
		'files.vbs' => 'Visual Basic Document',
		'files.sql' => 'SQL Document',
		'files.inc' => 'Include File',
		'files.c' => 'C Document',
		'files.cpp' => 'C++ Document',
		'files.m' => 'C Source File',
		'files.h' => 'Header File',
		'files.java' => 'Java Document',
		'files.jar' => 'Java Document',
		'files.sh' => 'Shell Script File',
		'files.zip' => 'Zip File',
		'files.sit' => 'StuffIt File',
		'files.gz' => 'GunZip File',
		'files.bz' => 'BZip File',
		'files.tar' => 'Tar File',
		'files.hqx' => 'Bin-Hex File',
		'files.bin' => 'Mac-Binary File',
		'files.aif' => 'AIFF Sound File',
		'files.mp3' => 'MPEG-3 Sound File',
		'files.wav' => 'WAV Sound File',
		'files.mid' => 'MIDI Sound File',
		'files.swf' => 'Flash Movie',
		'files.dcr' => 'Shockwave Movie',
		'files.mov' => 'QuickTime Movie',
		'files.avi' => 'AVI Movie',
		'files.mp4' => 'MPEG-4 Movie',
		'files.wmw' => 'Windows Media Player Movie',
		'files.directory' => 'Directory',
		'files.unknown' => 'Unknown',
		'result.dirNotFound' => 'directory not found - displaying root directory',
		'result.dirError' => 'error reading directory - check the permissions',
		'global.yes' => 'yes',
		'global.no' => 'no',
		'href_title.connect' => 'connect',
		'href_title.opendir' => 'open directory',
		'href_title.viewfile' => 'view file',
		'href_title.edit' => 'edit',
		'href_title.compress' => 'compress',
		'href_title.erase' => 'erase',
		'href_title.disconnect' => 'disconnect',
		'href_title.newdir' => 'create directory',
		'href_title.upload' => 'upload file',
		'href_title.newfile' => 'create file',
		'href_title.edit' => 'submit changes',
		'select.fav' => 'Favorites:',
		'select.path' => 'Path:',
		'select.newfile' => 'None / Custom',
		'infos.date' => 'date:',
		'infos.host' => 'host:',
		'infos.dir' => 'working directory:',
		'infos.space' => 'free space on disk:',
		'options.newdir' => 'new directory',
		'options.upload' => 'upload file',
		'options.newfile' => 'new file',
		'options.edit' => 'edit',
		'options.properties' => 'properties',
		'options.name' => 'name:',
		'options.perms' => 'perms:',
		'options.file' => 'file:',
		'options.extension' => 'extension:',
		'options.content' => 'content:',
		'options.type' => 'type:',
		'options.size' => 'size:',
		'options.mod' => 'mod:',
		'options.read' => 'read:',
		'options.write' => 'write:',
		'options.uid' => 'uid:',
		'options.gid' => 'gid:',
		'options.ctime' => 'c-time:',
		'options.atime' => 'a-time:',
		'action.compress.exist' => 'error creating archive (archive already exists)',
		'action.compress.success' => 'archive successfully created',
		'action.compress.error' => 'error creating archive (no permissions)',
		'action.mkdir.exist' => 'error creating directory (directory already exists)',
		'action.mkdir.success' => 'directory successfully created',
		'action.mkdir.error' => 'error creating directory',
		'action.upload.exist' => 'error uploading file (file already exists)',
		'action.upload.success' => 'file successfully uploaded',
		'action.upload.error' => 'error uploading file',
		'action.newfile.exist' => 'error creating file (file already exists)',
		'action.newfile.success' => 'file successfully created',
		'action.newfile.error' => 'error creating file',
		'action.erase.dir.success' => 'directory successfully erased',
		'action.erase.dir.error' => 'error erasing directory (make sure directory is empty)',
		'action.erase.file.success' => 'file successfully erased',
		'action.erase.file.error' => 'error erasing file',
		'action.editfile.dir.exist' => 'error editing directory (directory already exists)',
		'action.editfile.dir.success' => 'directory successfully edited',
		'action.editfile.file.exist' => 'error editing file (file already exists)',
		'action.editfile.file.success' => 'file successfully edited',
		'chmod.error' => '(no privileges to execute a chmod)',
	);
?>
