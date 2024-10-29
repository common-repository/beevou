<?php
	/*
	Plugin Name: Beevou
	Description: Display beevou.net vouchers
	Version: 0.1
	Author: Database Team
	Author URI: http://www.db-team.com/
	Plugin URI: http://www.beevou.com/
	*/

	define('BEEVOU_URL', 'https://beevou.net/');
	define('CLIENT_ID', 'NTE3ZTRiN2I2Zjk5NzBj');
	define('CLIENT_SECRET', '4487f2a87d5bd72e205993234f1a47d72bc61588');
	
	define('BEEVOU_DIR', plugin_dir_path(__FILE__));
	define('BEEVOU_URL', plugin_dir_url(__FILE__));

	function beevou_load(){	
		//if(is_admin()) //load admin files only in admin
			//require_once(BEEVOU_DIR.'includes/admin.php');
			
		//require_once(BEEVOU_DIR.'includes/core.php');
	}
	beevou_load();
	
	/*Admin Functions*/
	
	function beevou_admin() {  
		require_once(BEEVOU_DIR.'includes/admin.php');
	} 
	
	function beevou_admin_actions() {  
		add_options_page("Beevou", "Beevou", 1, "Beevou", "beevou_admin");  
	}  
	  
	add_action('admin_menu', 'beevou_admin_actions'); 

	/*Content Functions*/
	
	function beevou_shortcode($atts) {
		extract( shortcode_atts( array(
			'vouchers_count' => 5,
		), $atts ));
		
		if (isset($_GET['beevou_template'])) {
			return beevou_show_template($_GET['beevou_template']);
		}
		else {
			return beevou_get_vouchers();
		}
	}
	
	function beevou_get_vouchers() {
		//Connect to the Beevou API 
		$username = get_option('beevou_username');
		$password = get_option('beevou_password');
		
		$show_icon = (get_option('beevou_show_icon') == 'y');
		$show_description = (get_option('beevou_show_description') == 'y');
		
		$url = BEEVOU_URL.'oauth/token?grant_type=password&username='.$username.'&password='.$password.'&client_id='.CLIENT_ID.'&client_secret='.CLIENT_SECRET;

		//open connection
		$ch = curl_init();

		//set the url, number of POST vars, POST data
		curl_setopt($ch, CURLOPT_URL,$url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_BINARYTRANSFER, true);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		
		//execute post
		$result = curl_exec($ch);

		$result = json_decode((string) $result);

		$access_token = $result->access_token;

		$url = BEEVOU_URL.'api/voucherstemplates/get_public_templates?access_token='.$access_token;

		//open connection
		$ch = curl_init();
		
		$fields = array(
            'username' => urlencode($username)
        );
		
		//url-ify the data for the POST
		$fields_string = '';
		foreach($fields as $key=>$value) { $fields_string .= $key.'='.$value.'&'; }
		rtrim($fields_string,'&');

		//set the url, number of POST vars, POST data
		curl_setopt($ch, CURLOPT_URL,$url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_BINARYTRANSFER, true);
		curl_setopt($ch, CURLOPT_POST,count($fields));
		curl_setopt($ch, CURLOPT_POSTFIELDS,$fields_string);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

		//execute post
		$result = curl_exec($ch);

		$result = json_decode((string) $result, true);
		
		$page_url = 'http';
		if (isset($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] == "on") {$page_url .= "s";}
			$page_url .= "://";
		if ($_SERVER["SERVER_PORT"] != "80") {
			$page_url .= $_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"].$_SERVER["REQUEST_URI"];
		} else {
			$page_url .= $_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];
		}

		$output = '<ul style="list-style-type: none;">';
		
		foreach ($result['items'] as $template) {
			$template_icon = ($template['image3'] != '') ? BEEVOU_URL.'users/'.$template['user_id'].'/'.$template['image3'] : BEEVOU_URL.'img/noimagevoucher.png';
			$template_url = $page_url.((strpos($page_url, '?') === false) ? '?' : '&').'beevou_template='.$template['id'];
			
			$output .= '<li style="'.(($show_icon) ? 'height: 110px;' : '').'">';
				if ($show_icon) $output .= '<img src="'.$template_icon.'" style="float: left; margin-right: 10px; width: 100px; height: 100px;" />';
				$output .= '<a href="'.$template_url.'">'.$template['name'].'</a>';
				if ($show_description) $output .= '<p>'.$template['description'].'</p>';
			$output .= '</li>';
		}
		
		$output .= '</ul>';
		
		return $output;
	} 
	
	function beevou_show_template($template_id) {
		return '<iframe allowtransparency="true" src="'.BEEVOU_URL.'voucherstemplates/public_view_frame/'.$template_id.'" width="450px" height="400px" frameborder="0"></iframe>';
	}

	add_shortcode('beevou', 'beevou_shortcode');

	/*Plugin Functions*/

	register_activation_hook(__FILE__, 'beevou_activation');
	register_deactivation_hook(__FILE__, 'beevou_deactivation');

	function beevou_activation() {
	 
		//actions to perform once on plugin activation go here   
		
		//register uninstaller
		register_uninstall_hook(__FILE__, 'beevou_uninstall');
	}

	function beevou_deactivation() {    
		// actions to perform once on plugin deactivation go here	    
	}

	function beevou_uninstall(){
		
		//actions to perform once on plugin uninstall go here	    
	}
?>