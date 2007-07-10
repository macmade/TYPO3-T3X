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
	 * File 'config/files.php' / File types configuration file.
	 * 
	 * @author		Jean-David Gadina / macmade.net (info@macmade.net)
	 * @version		2.0
	 */
	
	/**
	 * File types definitions.
	*/
	$FILETYPES = array(
		array(
			'regexp' => 'txt',
			'icon' => 'text',
			'edit' => 1,
			'ext' => '.txt',
		),
		array(
			'regexp' => 'rtf',
			'icon' => 'text',
			'edit' => 0,
			'ext' => '.rtf',
		),
		array(
			'regexp' => 'doc',
			'icon' => 'text',
			'edit' => 0,
			'ext' => '.doc',
		),
		array(
			'regexp' => 'xls',
			'icon' => 'text',
			'edit' => 0,
			'ext' => '.xls',
		),
		array(
			'regexp' => 'pps',
			'icon' => 'text',
			'edit' => 0,
			'ext' => '.pps',
		),
		array(
			'regexp' => 'swx',
			'icon' => 'text',
			'edit' => 0,
			'ext' => '.swx',
		),
		array(
			'regexp' => 'jpe?g',
			'icon' => 'picture',
			'edit' => 0,
			'ext' => '.jpg',
		),
		array(
			'regexp' => 'gif',
			'icon' => 'picture',
			'edit' => 0,
			'ext' => '.gif',
		),
		array(
			'regexp' => 'png',
			'icon' => 'picture',
			'edit' => 0,
			'ext' => '.png',
		),
		array(
			'regexp' => 'tiff?',
			'icon' => 'picture',
			'edit' => 0,
			'ext' => '.tif',
		),
		array(
			'regexp' => 'eps',
			'icon' => 'picture',
			'edit' => 0,
			'ext' => '.eps',
		),
		array(
			'regexp' => 'bmp',
			'icon' => 'picture',
			'edit' => 0,
			'ext' => '.bmp',
		),
		array(
			'regexp' => 'ps',
			'icon' => 'picture',
			'edit' => 0,
			'ext' => '.ps',
		),
		array(
			'regexp' => 'psd',
			'icon' => 'picture',
			'edit' => 0,
			'ext' => '.psd',
		),
		array(
			'regexp' => 'ai',
			'icon' => 'picture',
			'edit' => 0,
			'ext' => '.ai',
		),
		array(
			'regexp' => 'fh[0-9][0-9]?',
			'icon' => 'picture',
			'edit' => 0,
			'ext' => '.freehand',
		),
		array(
			'regexp' => 'pdf',
			'icon' => 'picture',
			'edit' => 0,
			'ext' => '.pdf',
		),
		array(
			'regexp' => 'html',
			'icon' => 'code',
			'edit' => 1,
			'ext' => '.html',
		),
		array(
			'regexp' => 'shtml',
			'icon' => 'code',
			'edit' => 1,
			'ext' => '.shtml',
		),
		array(
			'regexp' => 'xml',
			'icon' => 'code',
			'edit' => 1,
			'ext' => '.xml',
		),
		array(
			'regexp' => 'css',
			'icon' => 'code',
			'edit' => 1,
			'ext' => '.css',
		),
		array(
			'regexp' => 'xss',
			'icon' => 'code',
			'edit' => 1,
			'ext' => '.xss',
		),
		array(
			'regexp' => 'dtd',
			'icon' => 'code',
			'edit' => 1,
			'ext' => '.dtd',
		),
		array(
			'regexp' => 'php',
			'icon' => 'code',
			'edit' => 1,
			'ext' => '.php',
		),
		array(
			'regexp' => 'asp',
			'icon' => 'code',
			'edit' => 1,
			'ext' => '.asp',
		),
		array(
			'regexp' => 'js',
			'icon' => 'code',
			'edit' => 1,
			'ext' => '.js',
		),
		array(
			'regexp' => 'vbs?',
			'icon' => 'code',
			'edit' => 1,
			'ext' => '.vbs',
		),
		array(
			'regexp' => 'sql',
			'icon' => 'code',
			'edit' => 1,
			'ext' => '.sql',
		),
		array(
			'regexp' => 'inc',
			'icon' => 'code',
			'edit' => 1,
			'ext' => '.inc',
		),
		array(
			'regexp' => 'c',
			'icon' => 'code',
			'edit' => 1,
			'ext' => '.c',
		),
		array(
			'regexp' => 'cpp',
			'icon' => 'code',
			'edit' => 1,
			'ext' => '.cpp',
		),
		array(
			'regexp' => 'm',
			'icon' => 'code',
			'edit' => 1,
			'ext' => '.m',
		),
		array(
			'regexp' => 'h',
			'icon' => 'code',
			'edit' => 1,
			'ext' => '.h',
		),
		array(
			'regexp' => 'java',
			'icon' => 'code',
			'edit' => 1,
			'ext' => '.java',
		),
		array(
			'regexp' => 'jar',
			'icon' => 'code',
			'edit' => 0,
			'ext' => '.jar',
		),
		array(
			'regexp' => 'sh',
			'icon' => 'code',
			'edit' => 1,
			'ext' => '.sh',
		),
		array(
			'regexp' => 'zip',
			'icon' => 'compress',
			'edit' => 0,
			'ext' => '.zip',
		),
		array(
			'regexp' => 'sitx?',
			'icon' => 'compress',
			'edit' => 0,
			'ext' => '.sit',
		),
		array(
			'regexp' => 'gz',
			'icon' => 'compress',
			'edit' => 0,
			'ext' => '.gz',
		),
		array(
			'regexp' => 'bz',
			'icon' => 'compress',
			'edit' => 0,
			'ext' => '.bz',
		),
		array(
			'regexp' => 'tar',
			'icon' => 'compress',
			'edit' => 0,
			'ext' => '.tar',
		),
		array(
			'regexp' => 'hqx',
			'icon' => 'compress',
			'edit' => 0,
			'ext' => '.hqx',
		),
		array(
			'regexp' => 'bin',
			'icon' => 'compress',
			'edit' => 0,
			'ext' => '.bin',
		),
		array(
			'regexp' => 'aiff?',
			'icon' => 'audio',
			'edit' => 0,
			'ext' => '.aif',
		),
		array(
			'regexp' => 'mp3',
			'icon' => 'audio',
			'edit' => 0,
			'ext' => '.mp3',
		),
		array(
			'regexp' => 'wav',
			'icon' => 'audio',
			'edit' => 0,
			'ext' => '.mp3',
		),
		array(
			'regexp' => 'mid',
			'icon' => 'audio',
			'edit' => 0,
			'ext' => '.mid',
		),
		array(
			'regexp' => 'swf',
			'icon' => 'video',
			'edit' => 0,
			'ext' => '.swf',
		),
		array(
			'regexp' => 'dcr',
			'icon' => 'video',
			'edit' => 0,
			'ext' => '.dcr',
		),
		array(
			'regexp' => 'mov',
			'icon' => 'video',
			'edit' => 0,
			'ext' => '.mov',
		),
		array(
			'regexp' => 'avi',
			'icon' => 'video',
			'edit' => 0,
			'ext' => '.avi',
		),
		array(
			'regexp' => 'mp4',
			'icon' => 'video',
			'edit' => 0,
			'ext' => '.mp4',
		),
		array(
			'regexp' => 'wmw',
			'icon' => 'video',
			'edit' => 0,
			'ext' => '.wmw',
		),
		array(
			'regexp' => 'directory',
			'icon' => 'directory',
			'edit' => 0,
			'ext' => '.directory',
		),
		array(
			'regexp' => 'unknown',
			'icon' => 'unknown',
			'edit' => 0,
			'ext' => '.unknown',
		),
	);
?>
