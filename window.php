<?php
$path  = ''; // It should be end with a trailing slash  
if ( !defined('WP_LOAD_PATH') ) {

	/** classic root path if wp-content and plugins is below wp-config.php */
	$classic_root = dirname(dirname(dirname(dirname(__FILE__)))) . '/' ;
	
	if (file_exists( $classic_root . 'wp-load.php') )
		define( 'WP_LOAD_PATH', $classic_root);
	else
		if (file_exists( $path . 'wp-load.php') )
			define( 'WP_LOAD_PATH', $path);
		else
			exit("Could not find wp-load.php");
}

// let's load WordPress
require_once( WP_LOAD_PATH . 'wp-load.php');
global $wpdb;
$themes=$wpdb->get_results("SELECT id,title FROM ".$wpdb->prefix."spidercalendar_theme");
$calendars=$wpdb->get_results("SELECT id,title FROM ".$wpdb->prefix."spidercalendar_calendar WHERE published=1");
?>
<html xmlns="http://www.w3.org/1999/xhtml"><head>
	<title>Spider Calendar</title>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
	<script language="javascript" type="text/javascript" src="<?php echo get_option("siteurl"); ?>/wp-includes/js/jquery/jquery.js"></script>
	<script language="javascript" type="text/javascript" src="<?php echo get_option("siteurl"); ?>/wp-includes/js/tinymce/tiny_mce_popup.js"></script><link rel="stylesheet" href="<?php echo get_option("siteurl"); ?>/wp-includes/js/tinymce/themes/advanced/skins/wp_theme/dialog.css?ver=342-20110630100">
	<script language="javascript" type="text/javascript" src="<?php echo get_option("siteurl"); ?>/wp-includes/js/tinymce/utils/mctabs.js"></script>
	<script language="javascript" type="text/javascript" src="<?php echo get_option("siteurl"); ?>/wp-includes/js/tinymce/utils/form_utils.js"></script>
	<base target="_self">
</head>
<body id="link" onLoad="tinyMCEPopup.executeOnLoad('init();');document.body.style.display='';"  style="" dir="ltr" class="forceColors">
<form name="spider_cat" action="#">
	<div class="tabs" role="tablist" tabindex="-1">
		<ul>
			<li id="Single_product_tab" class="current" role="tab" tabindex="0"><span><a href="javascript:mcTabs.displayTab('Single_product_tab','Single_product_panel');" onMouseDown="return false;" tabindex="-1">Spider Calendar</a></span></li>
			
		</ul>
	</div>
	
	<div class="panel_wrapper">
		<div id="Single_product_panel" class="panel current">
		<br>
		<table border="0" cellpadding="4" cellspacing="0">
         <tbody><tr>
            <td nowrap="nowrap"><label for="spider_Calendar">Select Calendar</label></td>
            <td><select name="spider_Calendar" id="spider_Calendar" >
<option value="- Select a Calendar -" selected="selected">- Select a Calendar -</option>
<?php 
	   foreach($calendars as $calendar)
	   {
		   ?>
           <option value="<?php echo $calendar->id; ?>"><?php echo $calendar->title; ?></option>
           <?php }?>
</select>
            </td>
          </tr>
         
        </tbody></table>
		</div>
        </div>
        <div class="mceActionPanel">
		<div style="float: left">
			<input type="button" id="cancel" name="cancel" value="Cancel" onClick="tinyMCEPopup.close();" />
		</div>

		<div style="float: right">
			<input type="submit" id="insert" name="insert" value="Insert" onClick="insert_spider_calendar();" />
		</div>
	</div>

</form>

<script type="text/javascript">
function insert_spider_calendar() {
	
	
		
		
			if(document.getElementById('spider_Calendar').value=='- Select a Calendar -')
			{
				tinyMCEPopup.close();
			}
			else
			{


			   var tagtext;
			   tagtext='[Spider_Calendar id="'+document.getElementById('spider_Calendar').value+'" theme="1"]';
			   window.tinyMCE.execInstanceCommand('content', 'mceInsertContent', false, tagtext);
			   tinyMCEPopup.editor.execCommand('mceRepaint');
			   tinyMCEPopup.close();

			}
		
}

</script>
</body></html>
<?php
?>