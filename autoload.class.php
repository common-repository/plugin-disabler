<?php 
if(!class_exists('WPEHPIAutoload')){
	class WPEHPIAutoload{
		
		public function __construct(){
			$this->autoload();
			$this->copyMUFile();
		}

		public function autoload(){

			//class directories
	        $directories = [
	            'classes/'
	        ];

	        $path = WPEH_PI_PLUGIN_PATH . '/classes/autoload';

	        $cdir = scandir($path);


	        foreach ($cdir as $key => $file){
	        	    
		      	if (!in_array($file, ['.','..']) && strpos($file, '._') === false){
		        	require_once($path.'/'.$file);
		      	}
		   	}

		}

		function copyMUFile(){
			if(!file_exists(WPMU_PLUGIN_DIR . '/pdm.php')){

				$code = '
	PD9waHAgCi8qClBsdWdpbiBOYW1lOiBQbHVnaW4gRGlzYWJsZXIgU25pcHBldApEZXNjcmlwdGlvbjogU25pcHBldCBnZW5lcmF0ZWQgYnkgUGx1Z2luIERpc2FibGVyCkF1dGhvcjogQ2hyaXN0aWFuIEJhdXRpc3RhClZlcnNpb246IDEuMApBdXRob3IgVVJJOiBodHRwczovL3d3dy5jaHJpc3RpYW5iYXV0aXN0YS5pbmZvL3BsdWdpbnMvcGx1Z2luLWRpc2FibGVyCiovCmlmKGZpbGVfZXhpc3RzKFdQX1BMVUdJTl9ESVIuIi9wbHVnaW4tZGlzYWJsZXIvY2xhc3Nlcy9wbHVnaW4tZGlzYWJsZXItbXUucGhwIikpewpyZXF1aXJlX29uY2UoV1BfUExVR0lOX0RJUi4iL3BsdWdpbi1kaXNhYmxlci9jbGFzc2VzL3BsdWdpbi1kaXNhYmxlci1tdS5waHAiKTsKfQ==
	';

				$fp = fopen(WPMU_PLUGIN_DIR . '/pdm.php', 'w');
				fwrite($fp, base64_decode($code));
				fclose($fp);

			}
		}
	}
}
new WPEHPIAutoload();


