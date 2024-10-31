<?php
if(!class_exists('WPEHPIBackendUI')){
	class WPEHPIBackendUI{

		function __construct(){
			add_action('admin_menu', [$this, 'submenuPage']);
			add_action('add_meta_boxes', [$this, 'pluginMetaBox']);
			add_action('save_post', [$this, 'savePostMeta']);
			add_action('admin_enqueue_scripts', [$this, 'enqueue']);

			add_action( 'wp_ajax_hp_settings', [$this, 'ajaxHPSettings']);

		}

		function submenuPage(){

			add_submenu_page(
		        'tools.php',
		        __( 'Plugin Disabler Settings', 'textdomain' ),
		        __( 'Plugin Disabler', 'textdomain' ),
		        'manage_options',
		        'pi-settings-page',
		        [$this, 'view']
		    );

		    $pt = [];
			foreach(get_post_types() as $gpt){
				$pt[] = $gpt;
			}

			update_option('pi_post_types', $pt);

		}

		function enqueue(){
	    	wp_enqueue_style('pi_admin_style', WPEH_PI_PLUGIN_URL . '/assets/css/style.css');
		}

		function view($cmb){

			if (isset( $_POST['name_of_nonce_field'] ) || 
				wp_verify_nonce( $_POST['pi_hp_nonce_field'], 'pi_hp_nonce_action')){
			 	
				$sd = WPEHPIBackendUI::sanitizePostArray($_POST['hp_deactivate_plugins']);

				if($sd != 'stop'){
						$sanitizedData = sanitize_option('pi_hp_deactivated_plugins', $sd);
					   	update_option('pi_hp_deactivated_plugins', $sanitizedData);

					    echo '	<div class="notice notice-success is-dismissible">
							        <p>Changes has been saved. </p>
							    		<button type="button" class="notice-dismiss">
							    		<span class="screen-reader-text">Dismiss this notice.</span>
							    	</button>
							    </div>';
				}else{
						echo '	<div class="notice notice-warning is-dismissible">
							        <p>Error in saving changes. </p>
							    		<button type="button" class="notice-dismiss">
							    		<span class="screen-reader-text">Dismiss this notice.</span>
							    	</button>
							    </div>';
				}		 

			   
			
			 }

			 if (isset( $_POST['name_of_nonce_field'] ) || 
				wp_verify_nonce( $_POST['pi_op_nonce_field'], 'pi_op_nonce_action')){
			 
			   	$sanitizedData = sanitize_option('pi_op_deactivated_plugins', $_POST['op_deactivate_plugins']);

				$sd = WPEHPIBackendUI::sanitizePostArray($_POST['op_deactivate_plugins']);

				if($sd != 'stop'){
						$sanitizedData = sanitize_option('pi_op_deactivated_plugins', $sd);
					   	update_option('pi_op_deactivated_plugins', $sanitizedData);

					    echo '	<div class="notice notice-success is-dismissible">
							        <p>Changes has been saved. </p>
							    		<button type="button" class="notice-dismiss">
							    		<span class="screen-reader-text">Dismiss this notice.</span>
							    	</button>
							    </div>';
				}else{
						echo '	<div class="notice notice-warning is-dismissible">
							        <p>Error in saving changes.</p>
							    		<button type="button" class="notice-dismiss">
							    		<span class="screen-reader-text">Dismiss this notice.</span>
							    	</button>
							    </div>';
				}	
			
			 }

			require_once(WPEH_PI_PLUGIN_PATH.'/view/settings.view.php');
		}

		function pluginMetaBox(){

			$pt = [];
			foreach(get_post_types() as $gpt){
				$pt[] = $gpt;
			}

			 add_meta_box(
		            'product_meta', 
		            'Manage Plugins',
		            [$this, 'metaBoxHtml'],
		            $pt,
		            'side',
		            'high');
		}

		function metaBoxHtml($post){
			
			$plugins = get_option('active_plugins', []);

			$deactivatedPlugins = get_post_meta($post->ID, 'pi_deactivated_plugins_meta', true);
			$deactivatedPlugins = (!empty($deactivatedPlugins)?$deactivatedPlugins:[]);

			echo '<input type="hidden" name="pi_meta" value="1">';

			if(!empty($plugins)){
				echo '<ul>';	
				$k = array_search( 'wpeh-plugin-disabler/wpeh-plugin-disabler.php', $plugins );
				unset($plugins[$k]);

				foreach($plugins as $plugin){

					$pluginData = get_plugin_data( WP_PLUGIN_DIR .'/'. $plugin ,  ['Version' => 'Version'], false);
						
					echo '<li>
							<div class="onoffswitch">
							    <input type="checkbox" name="deactivate_plugins[]" class="onoffswitch-checkbox" 
							    id="'.strtolower(str_replace(['/','.'],'_', $plugin)).'" 
							    '.(in_array($plugin, $deactivatedPlugins)?'checked':'').' 
							    value="'.$plugin.'">
							    <label class="onoffswitch-label" for="'.strtolower(str_replace(['/','.'],'_', $plugin)).'">
							        <span class="onoffswitch-inner"></span>
							        <span class="onoffswitch-switch"></span>
							    </label>
							    <span class="plugin-name"> '.$pluginData['Name'].'</span> 
							</div>
							<div style="clear:both"></div>
						</li>';
				}
				echo '</ul>';
			}


		}

		function savePostMeta($postID){
			if(isset($_POST['pi_meta'])){
				$sd = WPEHPIBackendUI::sanitizePostArray($_POST['deactivate_plugins']);
				if($sd != 'stop'){
			   		$sanitizedData = sanitize_meta('pi_deactivated_plugins_meta', $sd, get_post_type($postID));	
	           		update_post_meta($postID, 'pi_deactivated_plugins_meta',$sanitizedData);
	       		}
	  		}
		}


		public static function sanitizePostArray($dataArr){
			if(empty($dataArr)) return [];

			$sanitizedArr = [];

			foreach($dataArr as $data){
				if(is_array($data)){
					return 'stop';
				}else{
					$sanitizedArr[] = sanitize_text_field($data);
				}
			}

			return $sanitizedArr;
		}

		
	}

}

new WPEHPIBackendUI();








