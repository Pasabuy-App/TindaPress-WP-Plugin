<?php
	// Exit if accessed directly
	if ( ! defined( 'ABSPATH' ) ) 
	{
		exit;
	}

	/** 
        * @package tindapress-wp-plugin
        * @version 0.1.0
	*/
?>

<?php
	class TP_Initialization {

		
		public static function initialize() {
            return "test";
            
		}

		
		public static function add_products(){

			if (!isset($_POST['name']) || !isset($_POST['surname'])) {
                return rest_ensure_response( 
                    array(
                        "status" => "unknown",
                        "message" => "Please contact your administrator. Authentication Unknown!",
                    )
                );
			}

			$name = $_POST['name'];
			$surname = $_POST['surname'];

			$data = array(
				"name" => $name,
				"surname" => $surname
			);
			
			$table_name="sample";
			
			
			$result = TP_Globals::create($table_name, $data);

			if ($result == false) {
                return rest_ensure_response( 
					array(
						"status" => "unknown",
						"message" => "Failed to Send Content",
					)
				);
            } else {
				return rest_ensure_response( 
					array(
						"status" => "success",
						"message" => "sent",
					)
				);
			} 

				  

		}

	}

?>