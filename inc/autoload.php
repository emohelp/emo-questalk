<?php  
function emqa_autoload_function($className) {
	$classPath = explode('_', $className);

	if ( $classPath[0] != 'EMQA' ) {
		return;
	}
	if ( $classPath[1] == 'Widgets' || $classPath[1] == 'widgets' ) {
		return;
	}
	// Drop 'Google', and maximum class file path depth in this project is 3.
	$classPathSlice = array_slice($classPath, 1, 2);
	if ( count( $classPath ) > 3 ) {
		for ($i=3; $i < count( $classPath ); $i++) { 
			$classPathSlice[1] .= '_' . $classPath[$i];
		}
	}
	$filePath = EMQA_DIR . 'inc/' . implode('_', $classPathSlice) . '.php';
	if ( ! file_exists( $filePath ) ) {
		$filePath = EMQA_DIR . 'inc/' . implode('/', $classPathSlice) . '.php';
	}
	if (file_exists($filePath)) {
		require_once($filePath);
	}
}
spl_autoload_register('emqa_autoload_function'); 

?>