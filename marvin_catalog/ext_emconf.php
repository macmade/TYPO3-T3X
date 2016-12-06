<?php

########################################################################
# Extension Manager/Repository config file for ext: "marvin_catalog"
#
# Auto generated 24-12-2009 12:11
#
# Manual updates:
# Only the data in the array - anything else is removed by next write.
# "version" and "dependencies" must not be touched!
########################################################################

$EM_CONF[$_EXTKEY] = array(
	'title' => 'Marvin / Catalog',
	'description' => 'The Marvin catalog of watches from the Magento store',
	'category' => 'plugin',
	'author' => 'Jean-David Gadina',
	'author_email' => 'macmade@netinfluence.ch',
	'shy' => 0,
	'dependencies' => 'oop,netfw',
	'conflicts' => '',
	'priority' => '',
	'module' => '',
	'state' => 'stable',
	'internal' => '',
	'uploadfolder' => 1,
	'createDirs' => '',
	'modify_tables' => '',
	'clearCacheOnLoad' => 1,
	'lockType' => '',
	'author_company' => 'netinfluence',
	'version' => '0.0.0',
	'constraints' => array(
		'depends' => array(
			'oop' => '0.0.0-0.0.0',
			'netfw' => '0.0.0-0.0.0',
		),
		'conflicts' => array(
		),
		'suggests' => array(
		),
	),
	'_md5_values_when_last_written' => 'a:96:{s:21:"ext_conf_template.txt";s:4:"8f5f";s:12:"ext_icon.gif";s:4:"8cd0";s:17:"ext_localconf.php";s:4:"777a";s:14:"ext_tables.php";s:4:"1563";s:23:"scripts/catalog.inc.php";s:4:"8f2a";s:26:"scripts/categories.inc.php";s:4:"08f3";s:21:"scripts/moods.inc.php";s:4:"de38";s:20:"static/pi1/setup.txt";s:4:"96f1";s:17:"lang/flex-pi1.xml";s:4:"9440";s:12:"lang/pi1.xml";s:4:"c170";s:12:"lang/pi2.xml";s:4:"d41e";s:19:"lang/tt_content.xml";s:4:"9d59";s:39:"lang/tx_marvincatalog_View_Category.xml";s:4:"bba9";s:41:"lang/tx_marvincatalog_View_Collection.xml";s:4:"761e";s:40:"lang/tx_marvincatalog_View_SendEmail.xml";s:4:"e776";s:36:"lang/tx_marvincatalog_View_Watch.xml";s:4:"5000";s:16:"lang/wiz-pi1.xml";s:4:"826b";s:16:"lang/wiz-pi2.xml";s:4:"c4dc";s:34:"pi2/class.tx_marvincatalog_pi2.php";s:4:"08a1";s:20:"res/img/bg-noise.png";s:4:"1ff0";s:26:"res/img/btn-background.gif";s:4:"fddd";s:18:"res/img/btn-bg.gif";s:4:"0a6a";s:24:"res/img/btn-facebook.gif";s:4:"b73c";s:20:"res/img/btn-mail.gif";s:4:"c7cb";s:24:"res/img/btn-next-off.gif";s:4:"c7f0";s:20:"res/img/btn-next.gif";s:4:"eb9f";s:28:"res/img/btn-previous-off.gif";s:4:"5673";s:24:"res/img/btn-previous.gif";s:4:"d091";s:20:"res/img/btn-send.gif";s:4:"6a64";s:20:"res/img/btn-shop.gif";s:4:"5391";s:23:"res/img/btn-twitter.gif";s:4:"86eb";s:17:"res/img/crown.gif";s:4:"0a51";s:16:"res/img/dash.gif";s:4:"e7bb";s:19:"res/img/wiz-pi1.gif";s:4:"1fd9";s:19:"res/img/wiz-pi2.gif";s:4:"1fd9";s:18:"res/swf/marvin.swf";s:4:"ab53";s:32:"res/js/jquery-lightbox/index.htm";s:4:"af39";s:48:"res/js/jquery-lightbox/images/lightbox-blank.gif";s:4:"fc94";s:52:"res/js/jquery-lightbox/images/lightbox-btn-close.gif";s:4:"2c38";s:51:"res/js/jquery-lightbox/images/lightbox-btn-next.gif";s:4:"2341";s:51:"res/js/jquery-lightbox/images/lightbox-btn-prev.gif";s:4:"5a91";s:54:"res/js/jquery-lightbox/images/lightbox-ico-loading.gif";s:4:"b5fe";s:50:"res/js/jquery-lightbox/css/jquery.lightbox-0.5.css";s:4:"9b59";s:40:"res/js/jquery-lightbox/photos/image1.jpg";s:4:"2b53";s:40:"res/js/jquery-lightbox/photos/image2.jpg";s:4:"0ccf";s:40:"res/js/jquery-lightbox/photos/image3.jpg";s:4:"34db";s:40:"res/js/jquery-lightbox/photos/image4.jpg";s:4:"e1eb";s:40:"res/js/jquery-lightbox/photos/image5.jpg";s:4:"5399";s:46:"res/js/jquery-lightbox/photos/thumb_image1.jpg";s:4:"deb7";s:46:"res/js/jquery-lightbox/photos/thumb_image2.jpg";s:4:"caa9";s:46:"res/js/jquery-lightbox/photos/thumb_image3.jpg";s:4:"80b2";s:46:"res/js/jquery-lightbox/photos/thumb_image4.jpg";s:4:"27ca";s:46:"res/js/jquery-lightbox/photos/thumb_image5.jpg";s:4:"ea73";s:35:"res/js/jquery-lightbox/js/jquery.js";s:4:"08c4";s:48:"res/js/jquery-lightbox/js/jquery.lightbox-0.5.js";s:4:"5cc0";s:52:"res/js/jquery-lightbox/js/jquery.lightbox-0.5.min.js";s:4:"6d74";s:53:"res/js/jquery-lightbox/js/jquery.lightbox-0.5.pack.js";s:4:"0df7";s:25:"res/js/easyslider/01.html";s:4:"52b9";s:25:"res/js/easyslider/02.html";s:4:"0519";s:25:"res/js/easyslider/03.html";s:4:"28d7";s:31:"res/js/easyslider/images/01.jpg";s:4:"1901";s:31:"res/js/easyslider/images/02.jpg";s:4:"52f7";s:31:"res/js/easyslider/images/03.jpg";s:4:"0f98";s:31:"res/js/easyslider/images/04.jpg";s:4:"5792";s:31:"res/js/easyslider/images/05.jpg";s:4:"12be";s:36:"res/js/easyslider/images/bg_body.gif";s:4:"a407";s:38:"res/js/easyslider/images/bg_header.gif";s:4:"3d62";s:37:"res/js/easyslider/images/btn_next.gif";s:4:"a1d5";s:37:"res/js/easyslider/images/btn_prev.gif";s:4:"7301";s:32:"res/js/easyslider/css/screen.css";s:4:"eaed";s:37:"res/js/easyslider/js/easySlider1.7.js";s:4:"e7fc";s:30:"res/js/easyslider/js/jquery.js";s:4:"8838";s:28:"res/fonts/Helvetica-Bold.ttf";s:4:"8672";s:33:"classes/Flexform/Fields.class.php";s:4:"2c5a";s:28:"classes/Hook/Cache.class.php";s:4:"73e1";s:37:"classes/Bitly/Url/Shortener.class.php";s:4:"1eca";s:36:"classes/Controller/Catalog.class.php";s:4:"717a";s:40:"classes/Magento/Database/Layer.class.php";s:4:"3235";s:50:"classes/Magento/Database/Layer/Exception.class.php";s:4:"40df";s:40:"classes/Magento/Catalog/Helper.class.php";s:4:"e9a3";s:41:"classes/Magento/Category/Helper.class.php";s:4:"95d0";s:38:"classes/Plugin/Wizard/Icon/1.class.php";s:4:"a498";s:38:"classes/Plugin/Wizard/Icon/2.class.php";s:4:"a21b";s:26:"classes/Eid/Base.class.php";s:4:"5217";s:27:"classes/Eid/Cache.class.php";s:4:"00f7";s:29:"classes/Eid/Catalog.class.php";s:4:"333b";s:32:"classes/Eid/Categories.class.php";s:4:"f3eb";s:31:"classes/Eid/Exception.class.php";s:4:"4db5";s:27:"classes/Eid/Moods.class.php";s:4:"05a7";s:27:"classes/View/Base.class.php";s:4:"608e";s:31:"classes/View/Category.class.php";s:4:"e691";s:33:"classes/View/Collection.class.php";s:4:"d699";s:32:"classes/View/SendEmail.class.php";s:4:"d456";s:28:"classes/View/Watch.class.php";s:4:"35d9";s:34:"pi1/class.tx_marvincatalog_pi1.php";s:4:"3d88";s:12:"flex/pi1.xml";s:4:"4392";}',
	'suggests' => array(
	),
);

?>