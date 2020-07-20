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
                        "message" => "Please contact your administrator. Content Unknown!",
                    )
                );
			}

			$n = $_POST['name'];
			$sn = $_POST['surname'];

			$data = array(
				"name" => $n,
				"surname" => $sn
			);
			
			$table_name = "sample";
			
			$result = TP_Globals::create($table_name, $data);

			if ($result == false) {
                return rest_ensure_response( 
					array(
						"status" => "unknown",
						"message" => "Failed to Create Content",
					)
				);
            } else {
				return rest_ensure_response( 
					array(
						"status" => "success",
						"message" => "Product Created Successfully",
					)
				);
			} 

				  

		}

		
		public static function retrieve_product(){
			
			$fields = array( 'name' , 'surname');
			// ASC
			$sort = array('DESC');

			$sort_field = array('ORDER BY', 'surname');

			$table_name = "sample";
			
			$result = TP_Globals::retrieve($table_name, $fields, $sort_field, $sort);

			if ($result == false) {

                return rest_ensure_response( 
					array(
						"status" => "unknown",
						"message" => "Failed to Send Content",
					)
				);

            }else{

				return rest_ensure_response( 
					array(
						"status" => "Sucess",
						"data" => array(
							"name" => $result->name,
							"surname" => $result->surname,
						)),
				);
				
			}


		}

		public static function update_product(){
		
			$id = 1;
			
			$table_name = 'sample';
			
			$fields = array(
				'name' => 'miguel',
				'surname' => 'radaza' 
			);

			$result = TP_Globals::update($table_name, $id, $fields);

			if ($result == false) {
                return rest_ensure_response( 
                    array(
                        "status" => "unknown",
                        "message" => "Please contact your administrator!",
                    )
                );
			}else {
				return rest_ensure_response( 
                    array(
                        "status" => "success",
                        "message" => "Product has been updated successfully!",
                    )
                );
			}

		}

		public static function delete_product(){

			if (!isset($_POST['id'])) {
				return rest_ensure_response( 
                    array(
                        "status" => "unknown_product",
                        "message" => "Please contact your administrator. Authentication Unknown!",
                    )
                );
			}

			$table_name = 'sample' ;

			$id = $_POST['id'];
			
			$result = TP_Globals::delete($table_name, $id);
			
			if ($result == false) {
                return rest_ensure_response( 
                    array(
                        "status" => "invalid_id",
                        "message" => "Something went wrong. Please contact your administrator!. product: not found.",
                    )
                );
			}else {
				return rest_ensure_response( 
                    array(
                        "status" => "success",
                        "message" => "Product has been deleted successfully!",
                    )
                );
			}
		}

		// not working

		// public static function retrieveById_product(){
		// 	if (!isset($_POST['id']) || !isset($_POST['fields'])) 
		// 	{
		// 		return rest_ensure_response( 
        //             array(
        //                 "status" => "unknown",
        //                 "message" => "Please contact your administrator. Authentication Unknown!",
        //             )
        //         );
		// 	}
			


		// 	$table_name = 'sample';

		// 	$fields = $_POST['fields'];
			
		// 	$id = $_POST['id'];

		// 	$result = TP_Globals::retrieveById($table_name, $fields, $id);

		// 	return $result;

		// 	// if ($result == false) {
        //     //     return rest_ensure_response( 
		// 	// 		array(
		// 	// 			"status" => "unknown",
		// 	// 			"message" => "Failed to Send Content",
		// 	// 		)
		// 	// 	);
        //     // } else {
		// 	// 	return rest_ensure_response( 
		// 	// 		array(
		// 	// 			"status" => "Sucess",
		// 	// 			"data" => $result ),
		// 	// 	);
		// 	// } 
		// }

	}
?>