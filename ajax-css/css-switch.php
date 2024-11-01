<?php
/*
Plugin Name: AJAX CSS Switcher
Plugin URI: http://dancameron.org/wordpress
Description: This Wordpress Plugin allows you to add CSS switching to your theme without any reload. It also stores the users selection for future visits. This plugin uses UDASS The Unobtrusive Degradable Ajax Style Sheet Switcher.
"The Unobtrusive Degradable Ajax Style Sheet Switcher combines the power of Server Side processing and DOM scripting to swap style sheets on the fly with the power of Ajax. If JavaScript? is disabled - UDASSS degrades gracefully to improve your website accessibility. Needless to say, it is very sexy indeed."
Author: http://dancameron.org
Version: Depracated --DO NOT USE--
Author URI: http://dancameron.org

Installation:
	1. Download the plugin and unzip it (didn't you already do this?).
	2. Place all of your style sheets in the 'css-container' folder.
	3. Upload the 'ajax-css' folder into your wp-content/plugins/ directory.
	4. Go to the Plugins page in your WordPress Administration area and click 'Activate' next to CSS AJAX Switch.
	5. Go to Presentation > CSS AJAX Switch to configure your settings and click 'save'.
	6. Place the search box where you want by placing <?php gajaxsearch(); ?> into your theme (most likely your sidebar) or simplify your life and use widget sidebars. 
	
	--How to Use--
	
		Option 1 - Use sidebar Widgets and add CSS Switch to your sidebar

		Option 2 - Add <?php csss(); ?> wherever you'd like CSS AJAX Switch to be.

		Advanced Setup - Create your own link. 
		Example,<a class="altCss" href="index.php?css=CSS NAME">TEXT LINK</a></strong>
		Make sure to change the "CSS NAME" to the name of the CSS provided above.
	
	7. Have fun and if you can contribute (see notes).
		

Version history:
-1.5
Resolved prototype dependancy

- 1
First Release

- .25
My First Save
*/
/*
Widget Icon Provided by <a href="http://www.famfamfam.com/lab/icons/silk/">http://www.famfamfam.com/lab/icons/silk/</a>
*/
/*
This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, version 2.

This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
*/



function csss_header() {
	$csss_global   = get_option("csss_global");
	$csss_one   = get_option("csss_one");
	$csss_one_name   = get_option("csss_one_name");
	$csss_two   = get_option("csss_two");
	$csss_two_name   = get_option("csss_two_name");
	$csss_three   = get_option("csss_three");
	$csss_three_name   = get_option("csss_three_name");
	$csss_four   = get_option("csss_four");
	$csss_four_name   = get_option("csss_four_name");
	$csss_five   = get_option("csss_five");
	$csss_five_name   = get_option("csss_five_name");
	?>
	<?php
	// style-switcher.php
	class AlternateStyles {
		var $styleSheet = array();	// @Array: collection of All Style Sheets
		var $altStyles = array();	// @Array: collection of Alternate Style Sheets
		var $prefStyleSheet = '';	// @String: The name (title) of the Preferred Style Sheet
		var $styleSheets = '';		// @String: All the style sheets output in their respective html formats
		// @constructor
		function AlternateStyles() {
			$this->prefStyleSheet = $this->cleanTitle($_GET['css']);
			if ( isset($_GET['cssJaxy']) && $_GET['cssJaxy'] == true ) {
				$this->setStyleCookie($this->prefStyleSheet);
				die();
			}
		}
		// @public
		function add($path,$media='',$title='',$alternate=false) {
			// first grab all global styles
			if ( !$title ) {
				$mediaRef = ($media != '' ? 'media="'.$media.'" ' : '');
				$styleLink = '<link type="text/css" href="'.$path.'" rel="stylesheet" '.$mediaRef.' />';
				// add it to our style sheet array
				array_push($this->styleSheet,$styleLink);
			}
			// otherwise we're adding the 'preferred' & 'alternates'
			else {
				$this->determinePreferred($path,$title,$media,$alternate);
			}
			// now grab our preferred
			$this->getPreferredStyles();
		}
		// @private
		function getPreferredStyles() {
			$this->styleSheets = '';
			$totalStyleSheets = count($this->styleSheet);
			for ( $i = 0; $i < $totalStyleSheets;$i++ ) {
				$this->styleSheets .= $this->styleSheet[$i]."\n";
			}
		}
		// @private
		function determinePreferred($path,$title,$media='',$alternate=false) {
			// still need that media thing no matter what
			$mediaRef = ($media != '' ? 'media="'.$media.'" ' : '');
			// if $_GET['css'] was set
			if ( $this->prefStyleSheet ) {
				$this->setStylecookie($this->prefStyleSheet);
				if ( $this->prefStyleSheet == $title ) {
					$styleLink = '<link type="text/css" href="'.$path.'" rel="stylesheet" '.$mediaRef.' title="'.$title.'" />';
				}
				else {
					$styleLink = '<link type="text/css" href="'.$path.'" rel=" alternate stylesheet" '.$mediaRef.' title="'.$title.'" />';
				}
			}
			// or we could have set a style sheet from before
			elseif ( $_COOKIE['PrefStyles'] ) {
				// odd bug with prototype, php, and cookies....don't ask
				$cookieCheck = $this->fixOurCookie($_COOKIE['PrefStyles']);
				if ( $cookieCheck == $title ) {
					$styleLink = '<link type="text/css" href="'.$path.'" rel="stylesheet" '.$mediaRef.' title="'.$title.'" />';
				}
				else {
					$styleLink = '<link type="text/css" href="'.$path.'" rel=" alternate stylesheet" '.$mediaRef.' title="'.$title.'" />';
				}
			}
			// probably just our first time here
			else  {
				$styleLink = '<link type="text/css" href="'.$path.'" rel="'.($alternate ? 'alternate ' : '' ).'stylesheet" '.$mediaRef.' title="'.$title.'" />';
			}
			array_push($this->styleSheet,$styleLink);
		}
		// @private
		function setStyleCookie($value) {
			setcookie("PrefStyles", $value, time()+(3600*24*365));  /* expires in 1 year */
		}
		// @private
		function cleanTitle($str) {
			return str_replace('_',' ',$str);
		}
		// @private
		function fixOurCookie($str) {
			$c = explode('?',$str);
			return $c[0];
		}
		// @public
		function drop() {
			// watchout! magic may occur
			echo $this->styleSheets;
		}
	}
	?>
	<?php
	
	// style sheet path[, media, title, bool(set as alternate)]
	$styleSheet = new AlternateStyles();
	$styleSheet->add('/wp-content/themes/'. get_option('csss_global').'','screen,projection'); // [Global Styles]
	$styleSheet->add('/wp-content/plugins/ajax-css/css-container/'. get_option('csss_one').'','screen,projection',''. get_option('csss_one_name').''); // [Preferred Styles]
	$styleSheet->add('/wp-content/plugins/ajax-css/css-container/'. get_option('csss_two').'','screen,projection',''. get_option('csss_two_name').'',true); // [Alternate Styles]
	$styleSheet->add('/wp-content/plugins/ajax-css/css-container/'. get_option('csss_three').'','screen,projection',''. get_option('csss_three_name').'',true); // // [Alternate Styles]
	$styleSheet->add('/wp-content/plugins/ajax-css/css-container/'. get_option('csss_four').'','screen,projection',''. get_option('csss_four_name').'',true); // // [Alternate Styles]
	$styleSheet->add('/wp-content/plugins/ajax-css/css-container/'. get_option('csss_five').'','screen,projection',''. get_option('csss_five_name').'',true); // // [Alternate Styles]

	$styleSheet->getPreferredStyles();
	?>
	<script type='text/javascript' src='/wp-content/plugins/ajax-css/js/prototype.js'></script>
	<script type='text/javascript' src='/wp-content/plugins/ajax-css/js/common.js'></script>
	<script type='text/javascript' src='/wp-content/plugins/ajax-css/js/alternateStyles.js'></script>
	 <?php
	 $styleSheet->drop();
	 ?>

<?php } // end css_header()

//admin panel
function csss_adminPanel() {
		add_submenu_page('themes.php','CSS AJAX Switch ', 'CSS AJAX Switch', 5,
			basename(__FILE__), 'csss_optionsSubpanel');
}


function csss_optionsSubpanel() {
	if($_POST['action'] == "save") {
		echo "<div class=\"updated fade\" id=\"limitcatsupdatenotice\"><p>" . __("Configuration <strong>updated</strong>.") . "</p></div>";
		//updating stuff..
		update_option("csss_global", $_POST["global"]);
		update_option("csss_one", $_POST["one"]);
		update_option("csss_one_name", $_POST["one_name"]);
		update_option("csss_two", $_POST["two"]);
		update_option("csss_two_name", $_POST["two_name"]);
		update_option("csss_three", $_POST["three"]);
		update_option("csss_three_name", $_POST["three_name"]);
		update_option("csss_four", $_POST["four"]);
		update_option("csss_four_name", $_POST["four_name"]);
    	update_option("csss_five", $_POST["five"]);
		update_option("csss_five_name", $_POST["five_name"]);
		update_option("csss_two_option", $_POST["2Option"]);
		update_option("csss_three_option", $_POST["3Option"]);
		update_option("csss_four_option", $_POST["4Option"]);
		update_option("csss_five_option", $_POST["5Option"]);
		
		$csss_two_option   = get_option("csss_two_option");
		$csss_three_option   = get_option("csss_three_option");
		$csss_four_option   = get_option("csss_four_option");
		$csss_five_option   = get_option("csss_five_option");
		$csss_global   = get_option("csss_global");
		$csss_one   = get_option("csss_one");
		$csss_one_name   = get_option("csss_one_name");
		$csss_two   = get_option("csss_two");
		$csss_two_name   = get_option("csss_two_name");
		$csss_three   = get_option("csss_three");
		$csss_three_name   = get_option("csss_three_name");
		$csss_four   = get_option("csss_four");
		$csss_four_name   = get_option("csss_four_name");
		$csss_five   = get_option("csss_five");
		$csss_five_name   = get_option("csss_five_name");

	     }

		?>
		
		<div class="wrap">
		<h2>CSS AJAX Switch</h2>	
		<form method="post">
			
	    <fieldset class="options">
		<legend>Global CSS</legend>
		
		This is your url path to your main CSS file. Forexample, kubrick/style.css
			<p>
		/wp-content/themes/<input type="text" name="global" size="35" value='<?php echo get_option('csss_global'); ?>'>
		
		</p>
		
		
		<legend>CSS ONE</legend>
		<p>
			<strong>CSS Filename:</strong>
			<br/>	
				&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;/wp-content/plugins/ajax-css/css-container/<input type="textarea" name="one" value='<?php echo get_option('csss_one'); ?>'>
			<br/>
			<strong>CSS Name:</strong>&nbsp;&nbsp;<small>(Spaces Allowed)</small>
			<br/>
				&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&lt;a class="altCss" href="index.php?css=<input type="textarea" name="one_name" value='<?php echo get_option('csss_one_name'); ?>'>"&gt;
		</p>
		<br/>
		<legend>CSS OPTION TWO</legend>
		<p>
			<strong>CSS Filename:</strong>
			<br/>	
				&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;/wp-content/plugins/ajax-css/css-container/<input type="textarea" name="two" value='<?php echo get_option('csss_two'); ?>'>
			<br/>
			<strong>CSS Name:</strong>&nbsp;&nbsp;<small>(Spaces Allowed)</small>
			<br/>
				&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&lt;a class="altCss" href="index.php?css=<input type="textarea" name="two_name" value='<?php echo get_option('csss_two_name'); ?>'>"&gt;
		</p>
		<br/>
		<legend>CSS OPTION THREE</legend>
		<p>
			<strong>CSS Filename:</strong>
			<br/>	
				&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;/wp-content/plugins/ajax-css/css-container/<input type="textarea" name="three" value='<?php echo get_option('csss_three'); ?>'>
			<br/>
			<strong>CSS Name:</strong>&nbsp;&nbsp;<small>(Spaces Allowed)</small>
			<br/>
				&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&lt;a class="altCss" href="index.php?css=<input type="textarea" name="three_name" value='<?php echo get_option('csss_three_name'); ?>'>"&gt;
		</p>
		<br/>
		<legend>CSS OPTION FOUR</legend>
		<p>
			<strong>CSS Filename:</strong>
			<br/>	
				&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;/wp-content/plugins/ajax-css/css-container/<input type="textarea" name="four" value='<?php echo get_option('csss_four'); ?>'>
			<br/>
			<strong>CSS Name:</strong>&nbsp;&nbsp;<small>(Spaces Allowed)</small>
			<br/>
				&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&lt;a class="altCss" href="index.php?css=<input type="textarea" name="four_name" value='<?php echo get_option('csss_four_name'); ?>'>"&gt;
		</p>
		<br/>
		<legend>CSS OPTION FIVE</legend>
		<p>
			<strong>CSS Filename:</strong>
			<br/>	
				&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;/wp-content/plugins/ajax-css/css-container/<input type="textarea" name="five" value='<?php echo get_option('csss_five'); ?>'>
			<br/>
			<strong>CSS Name:</strong>&nbsp;&nbsp;<small>(Spaces Allowed)</small>
			<br/>
				&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&lt;a class="altCss" href="index.php?css=<input type="textarea" name="five_name" value='<?php echo get_option('csss_five_name'); ?>'>"&gt;
		</p>
		
		<br/>
		<legend>Widget and Function Users</legend>
		<p>
			<strong>Select CSS in Use</strong>
			<br/>
		&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Two: <input name="2Option" type="checkbox" id="2Option" value="true"  <?php if(get_option('csss_two_option') == 'true') { echo 'checked="true"'; } ?> />&nbsp;&nbsp;&nbsp;
		Three: <input name="3Option" type="checkbox" id="3Option" value="true"  <?php if(get_option('csss_three_option') == 'true') { echo 'checked="true"'; } ?> />&nbsp;&nbsp;&nbsp;
		Four: <input name="4Option" type="checkbox" id="4Option" value="true"  <?php if(get_option('csss_four_option') == 'true') { echo 'checked="true"'; } ?> />&nbsp;&nbsp;&nbsp;
		Five: <input name="5Option" type="checkbox" id="5Option" value="true"  <?php if(get_option('csss_five_option') == 'true') { echo 'checked="true"'; } ?> />
		</p>
		<fieldset class="options">
		<div class="submit">
		<input type="hidden" name="action" value="save">
		<input type="submit" value="Save">
		</div>
		</fieldset>
		</form></table>
	    </fieldset>
		
			<H2>How to use</H2>
			<p>
				<ul>
					<li>
					Option 1 - Use sidebar Widgets and add CSS Switch to your sidebar
					</li><li>
					Option 2 - Add &lt;?php csss(); ?&gt; wherever you'd like CSS AJAX Switch to be.
					</li><li><p/>
					Advanced - Create your own link. </li>Example,<br/> <strong>&lt;a class="altCss" href="index.php?css=CSS NAME"&gt;TEXT LINK&lt;/a&gt;</strong> <br/>Make sure to change the "CSS NAME" to the name of the CSS provided above.
					
				</ul>
			</p>
			
			
		
	

		
	
	</div>
		<?php } // end csss_optionsSubpanel()

	function widget_csss_init() {

		// Check for the required plugin functions. This will prevent fatal
		// errors occurring when you deactivate the dynamic-sidebar plugin.
		if ( !function_exists('register_sidebar_widget') )
			return;

		// This is the function that outputs our little Google search form.
		function widget_csss($args) {

			// $args is an array of strings that help widgets to conform to
			// the active theme: before_widget, before_title, after_widget,
			// and after_title are the array keys. Default tags: li and h2.
			extract($args);

			// Each widget can store its own options. We keep strings here.
			$options = get_option('widget_csss');
			$title = $options['title'];


			// These lines generate our output. Widgets can be very complex
			// but as you can see here, they can also be very, very simple.
			echo $before_widget . $before_title . $title . $after_title;

			?>
					
					
					
					<a class="altCss" href="index.php?css=<?php echo get_option('csss_one_name'); ?>"><img src="/wp-content/plugins/ajax-css/js/button.png" /></a>
					<?php if( get_option('csss_two_option') == 'true') { ?>
						<a class="altCss" href="index.php?css=<?php echo get_option('csss_two_name'); ?>"><img src="/wp-content/plugins/ajax-css/js/button.png" /></a><?php } ?>
					<?php if( get_option('csss_three_option') == 'true') { ?>
						<a class="altCss" href="index.php?css=<?php echo get_option('csss_three_name'); ?>"><img src="/wp-content/plugins/ajax-css/js/button.png" /></a><?php } ?>
					<?php if( get_option('csss_four_option') == 'true') { ?>
						<a class="altCss" href="index.php?css=<?php echo get_option('csss_four_name'); ?>"><img src="/wp-content/plugins/ajax-css/js/button.png" /></a><?php } ?>
					<?php if( get_option('csss_five_option') == 'true') { ?>
						<a class="altCss" href="index.php?css=<?php echo get_option('csss_five_name'); ?>"><img src="/wp-content/plugins/ajax-css/js/button.png" /></a><?php } ?>
					
					
					
			<?php }  // end widget_csss($args)

			echo $after_widget;


		// This is the function that outputs the form to let the users edit
		// the widget's title. It's an optional feature that users cry for.
		function widget_csss_control() {

			// Get our options and see if we're handling a form submission.
			$options = get_option('widget_csss');
			if ( !is_array($options) )
				$options = array('title'=>'', 'buttontext'=>__('CSS Switch', 'widgets'));
			if ( $_POST['csss-submit'] ) {

				// Remember to sanitize and format use input appropriately.
				$options['title'] = strip_tags(stripslashes($_POST['csss-title']));
				update_option('widget_csss', $options);
				$buttontext = htmlspecialchars($options['buttontext'], ENT_QUOTES);
			}

			// Be sure you format your options to be valid HTML attributes.


				// Here is our little form segment. Notice that we don't need a
				// complete form. This will be embedded into the existing form.
				echo '<p style="text-align:right;"><label for="csss-title">' . __('Title:') . ' <input style="width: 200px;" id="csss-title" name="csss-title" type="text" value="'.$title.'" /></label></p>';

				echo '<input type="hidden" id="csss-submit" name="csss-submit" value="1" />';
		}

		// This registers our widget so it appears with the other available
		// widgets and can be dragged and dropped into any active sidebars.
		register_sidebar_widget(array('CSS Switch', 'widgets'), 'widget_csss');

		// This registers our optional widget control form. Because of this
		// our widget will have a button that reveals a 300x100 pixel form.
		register_widget_control(array('CSS Switch', 'widgets'), 'widget_csss_control', 300, 100);
	}

	// For the few without widgets
	function csss() {
		?>
				
				
						<a class="altCss" href="index.php?css=<?php echo get_option('csss_one_name'); ?>"><img src="/wp-content/plugins/ajax-css/js/button.png" /></a>
						<?php if( get_option('csss_two_option') == 'true') { ?>
							<a class="altCss" href="index.php?css=<?php echo get_option('csss_two_name'); ?>"><img src="/wp-content/plugins/ajax-css/js/button.png" /></a><?php } ?>
						<?php if( get_option('csss_three_option') == 'true') { ?>
							<a class="altCss" href="index.php?css=<?php echo get_option('csss_three_name'); ?>"><img src="/wp-content/plugins/ajax-css/js/button.png" /></a><?php } ?>
						<?php if( get_option('csss_four_option') == 'true') { ?>
							<a class="altCss" href="index.php?css=<?php echo get_option('csss_four_name'); ?>"><img src="/wp-content/plugins/ajax-css/js/button.png" /></a><?php } ?>
						<?php if( get_option('csss_five_option') == 'true') { ?>
							<a class="altCss" href="index.php?css=<?php echo get_option('csss_five_name'); ?>"><img src="/wp-content/plugins/ajax-css/js/button.png" /></a><?php } ?>
				
				
				
		<?php }  // end gajaxsearch()

	// Run our code later in case this loads prior to any required plugins.
	add_action('plugins_loaded', 'widget_csss_init');

//user hooks
add_action('wp_head', 'csss_header');


//admin hooks
add_action('admin_menu', 'csss_adminPanel');

?>
