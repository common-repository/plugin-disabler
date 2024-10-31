<?php 
if(!class_exists('WPEHPIPluginRemover')){
	class WPEHPIPluginRemover{
		function __construct(){
			add_action('mu_plugin_loaded', [$this, 'removePlugins']);
		}

		function removePlugins(){

			if(is_admin()) return;

			global $pluginIsolator;

			$pluginIsolator['pluginsToDisable'] = '';

			$url2Arr = explode('?', $_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']);

			$url1 = rtrim(str_replace(['https://', 'http://', 'www.'], '', get_site_url()), '/');;
			$url2 = rtrim(str_replace(['https://', 'http://', 'www.'], '', $url2Arr[0] ), '/');

			if($url1 == $url2){
				$pluginIsolator['pluginsToDisable'] = get_option('pi_hp_deactivated_plugins', []);
			}else{
				$currentUri = rtrim(str_replace(str_replace(['https://', 'http://'], '', get_site_url()), '', $_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']),'/');
				$currentUri = explode('/', $currentUri);
				$pageData = get_page_by_path($currentUri[count($currentUri)-1], OBJECT, get_option('pi_post_types', []));

				if(isset($pageData->ID) && !empty($pageData->ID)){
					$pluginIsolator['pluginsToDisable'] = get_post_meta($pageData->ID, 'pi_deactivated_plugins_meta', true);
				}else{
					$pluginIsolator['pluginsToDisable'] = get_option('pi_op_deactivated_plugins', []);
				}
			}

			add_filter( 'option_active_plugins', function( $plugins ){

				global $pluginIsolator;  

				if(!isset($pluginIsolator['pluginsToDisable'])) return $plugins;

				$pluginsToDisable = $pluginIsolator['pluginsToDisable'];
				$newPluginList = [];
				if(!empty($pluginsToDisable)){
					foreach($pluginsToDisable as $ptd){
						$k = array_search( $ptd, $plugins );
						unset($plugins[$k]);
					}
				}

				return $plugins;

			});
			
		}

	}
}
new WPEHPIPluginRemover();





