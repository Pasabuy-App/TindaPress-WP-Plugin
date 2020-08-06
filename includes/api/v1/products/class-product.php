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

    class TP_Products {

        public static function add_product(){
            global $wpdb;
            
            // Step1 : check if datavice plugin is activated
            if (TP_Globals::verifiy_datavice_plugin() == false) {
                return rest_ensure_response( 
                    array(
                        "status" => "unknown",
                        "message" => "Please contact your administrator. Plugin Missing!",
                    )
                );
            }

            // Step2 : Check if wpid and snky is valid
            if (TP_Globals::validate_user() == false) {
                return rest_ensure_response( 
                    array(
                        "status" => "unknown",
                        "message" => "Please contact your administrator. Request Unknown!",
                    )
                );
            }

            // Step3 : Sanitize all Request
			if (!isset($_POST["wpid"]) || !isset($_POST["snky"]) || !isset($_POST["ctid"]) || !isset($_POST["stid"]) || !isset($_POST["title"]) || !isset($_POST["short_info"]) || !isset($_POST["long_info"]) ||  !isset($_POST["sku"]) ||   !isset($_POST["price"]) || !isset($_POST["weight"]) || !isset($_POST["dimension"]) || !isset($_POST["preview"])) {
				return rest_ensure_response( 
					array(
						"status" => "unknown",
						"message" => "Please contact your administrator. Request unknown!",
					)
                );
                
            }

            // Step 4: Check if ID is in valid format (integer)
			if (!is_numeric($_POST["wpid"]) || !is_numeric($_POST["ctid"]) || !is_numeric($_POST["stid"]) ) {
				return rest_ensure_response( 
					array(
						"status" => "failed",
						"message" => "Please contact your administrator. ID not in valid format!",
					)
                );
                
			}

			// Step 5: Check if ID exists
			if (!get_user_by("ID", $_POST['wpid'])) {
				return rest_ensure_response( 
					array(
						"status" => "failed",
						"message" => "User not found!",
					)
                );
                
            }

            // Step6 : Check if all variable is not empty
            if (empty($_POST["wpid"]) 
                || empty($_POST["snky"]) 
                || empty($_POST["ctid"]) 
                || empty($_POST["stid"]) 
                || empty($_POST["title"]) 
                || empty($_POST["short_info"]) 
                || empty($_POST["long_info"]) 
                || empty($_POST["sku"]) 
                || empty($_POST["price"]) 
                || empty($_POST["weight"]) 
                || empty($_POST["dimension"]) 
                || empty($_POST["preview"])) {
				return rest_ensure_response( 
					array(
						"status" => "unknown",
						"message" => "Required fields cannot be empty.",
					)
                );
                
            }

            // variable for time stamp
            $later = TP_Globals::date_stamp();

            // variables for query
            $created_by = $_POST['wpid'];
            $ctid = $_POST['ctid'];
            $stid = $_POST['stid'];
            $table_product = TP_PRODUCT_TABLE;
            $table_revs = TP_REVISION;

            // array for child value
            $child_vals = array(
                $_POST['title'],
                $_POST['short_info'],
                $_POST['long_info'],
                $_POST['sku'],
                $_POST['price'],
                $_POST['weight'],
                $_POST['dimension'],
                $_POST['preview'],
            );

            $revs_type = "products";

            $child_keys = array('title', 'short_info', 'long_info', 'sku', 'price',  'weight',  'dimension', 'preview' );
            // array for last id
            $last_id_product = array();

            // Step7 : insert query for table revision
            for ($count=0; $count < count($child_keys) ; $count++) {
                $sql = "INSERT INTO $table_revs (revs_type, child_key , child_val, created_by, date_created  ) VALUES ('$revs_type', '$child_keys[$count]', '$child_vals[$count]', $created_by, '$later')";
                $result = $wpdb->query($sql );
                $last_id=$wpdb->insert_id;
                $last_ids[] = $last_id;

            }

            // Step8 : Insert tbl_product
            $vals  = implode(", ", $last_ids);
            $colss  = implode(", ", $child_keys); 
            $sql2 = "INSERT INTO $table_product (stid, ctid, $colss, created_by, date_created) VALUES ($stid, $ctid, $vals, $created_by, '$later' )";
            $result_update = $wpdb->query($sql2);
            // last insert id of tbl_product
            $last_product_id = $wpdb->insert_id;

            // Step9 : update tbl_revisons for parent id
            for ($count=0; $count < count($child_keys) ; $count++) { 
                $resultss = $wpdb->update($table_revs, array('parent_id' => $last_product_id), array( 'ID' => $last_ids[$count]));
            }

            // Step10 : return Success
            return rest_ensure_response( 
                array(
                    "status" => "success",
                    "message" => "Product added successfully!",
                )
            );

        }

        public static function get_product(){
            global $wpdb;

            // Step1 : validate if datavice plugin is activated
            if (TP_Globals::verifiy_datavice_plugin() == false) {
                return rest_ensure_response( 
                    array(
                        "status" => "unknown",
                        "message" => "Please contact your administrator. Plugin Missing!",
                    )
                );
            }

            // Step2 : Check if wpid and snky is valid
            if (TP_Globals::validate_user() == false) {
                return rest_ensure_response( 
                    array(
                        "status" => "unknown",
                        "message" => "Please contact your administrator. Request Unknown!",
                    )
                );
            }

            // Step3 : Sanitize all Request
			if (!isset($_POST["wpid"]) || !isset($_POST["snky"]) ) {
				return rest_ensure_response( 
					array(
						"status" => "unknown",
						"message" => "Please contact your administrator. Request unknown!",
					)
                );
                
            }

            // Step 4: Check if ID is in valid format (integer)
			if (!is_numeric($_POST["wpid"]) || !is_numeric($_POST["ctid"])  ) {
				return rest_ensure_response( 
					array(
						"status" => "failed",
						"message" => "Please contact your administrator. ID not in valid format!",
					)
                );
                
			}

			// Step 5: Check if ID exists
			if (!get_user_by("ID", $_POST['wpid'])) {
				return rest_ensure_response( 
					array(
						"status" => "failed",
						"message" => "User not found!",
					)
                );
                
            }

            // Step6 : Sanitize all Request if empty
			if (empty($_POST["wpid"]) || empty($_POST["snky"]) ) {
				return rest_ensure_response( 
					array(
						"status" => "unknown",
						"message" => "Required fields cannot be empty.",
					)
                );
                
            }
            // table names variable for query
            $table_product = TP_PRODUCT_TABLE;
            $table_product_revs = TP_PRODUCT_REVS_TABLE;
            $table_stores = TP_STORES_TABLE;
            $table_stores_revs = TP_STORES_REVS_TABLE;
            $table_categories = TP_CATEGORIES_TABLE;
            $table_categories_revs = TP_CATEGORIES_REVS_TABLE;
            $table_revs = TP_REVISION;

            // Step7 : if last insert id is not in Request
            if(!isset($_POST['lid'])){

                // product list query
                $result =  $wpdb->get_results("SELECT
                        prod.id AS id,
                        revs_2.child_val AS store_name,
                        max( IF ( revs_2.child_key = 'title', revs_2.child_val, '' ) ) AS cat_title,
                        max( IF ( revs_2.child_key = 'info', revs_2.child_val, '' ) ) AS cat_info,
                        max( IF ( revs.child_key = 'title', revs.child_val, '' ) ) AS title,
                        max( IF ( revs.child_key = 'preview', revs.child_val, '' ) ) AS preview,
                        max( IF ( revs.child_key = 'short_info', revs.child_val, '' ) ) AS short_info,
                        max( IF ( revs.child_key = 'long_info', revs.child_val, '' ) ) AS long_info,
                        max( IF ( revs.child_key = 'status', revs.child_val, '' ) ) AS STATUS,
                        max( IF ( revs.child_key = 'sku', revs.child_val, '' ) ) AS sku,
                        max( IF ( revs.child_key = 'price', revs.child_val, '' ) ) AS price,
                        max( IF ( revs.child_key = 'weight', revs.child_val, '' ) ) AS weight,
                        max( IF ( revs.child_key = 'dimension', revs.child_val, '' ) ) AS dimension,
                        revs.created_by,
                        prod.date_created 
                    FROM
                        $table_product prod
                        INNER JOIN $table_revs revs ON prod.title = revs.ID 
                        OR prod.preview = revs.ID 
                        OR prod.short_info = revs.ID 
                        OR prod.long_info = revs.ID 
                        OR prod.`status` = revs.ID 
                        OR prod.sku = revs.ID 
                        OR prod.price = revs.ID 
                        OR prod.weight = revs.ID 
                        OR prod.dimension = revs.ID
                        INNER JOIN $table_stores str ON prod.stid = str.ID
                        INNER JOIN $table_revs revs_1 ON str.title = revs_1.ID
                        INNER JOIN $table_categories cat ON prod.ctid = cat.ID
                        INNER JOIN $table_revs revs_2 ON cat.title = revs_2.ID 
                        OR cat.info = revs_2.ID 
                    GROUP BY
                        revs.parent_id DESC LIMIT 12
                ");
                $last_id = min($result);

                // Return result
                return rest_ensure_response( 
                    array(
                        "status" => "success",
                        "data" => array(
                            'list' => $result, 
                            'last_id' => $last_id
                        )
                    )
                );

            }else{
                // Sanitize requirest if numeric
                if(!is_numeric($_POST["lid"])){
					return rest_ensure_response( 
						array(
							"status" => "failed",
							"message" => "Parameters not in valid format!",
						)
					);

                }
                // Sanitize requirest if not empty
                if(empty($_POST["lid"])){
					return rest_ensure_response( 
						array(
							"status" => "unknown",
							"message" => "Required fields cannot be empty.",
						)
					);

                }
                
                // variable of query
                $get_last_id = $_POST['lid'];
                $add_feeds = $get_last_id - 5;


                // query
                $result =  $wpdb->get_results("SELECT
                    prod.id AS id,
                    revs_2.child_val AS store_name,
                    max( IF ( revs_2.child_key = 'title', revs_2.child_val, '' ) ) AS cat_title,
                    max( IF ( revs_2.child_key = 'info', revs_2.child_val, '' ) ) AS cat_info,
                    max( IF ( revs.child_key = 'title', revs.child_val, '' ) ) AS title,
                    max( IF ( revs.child_key = 'preview', revs.child_val, '' ) ) AS preview,
                    max( IF ( revs.child_key = 'short_info', revs.child_val, '' ) ) AS short_info,
                    max( IF ( revs.child_key = 'long_info', revs.child_val, '' ) ) AS long_info,
                    max( IF ( revs.child_key = 'status', revs.child_val, '' ) ) AS STATUS,
                    max( IF ( revs.child_key = 'sku', revs.child_val, '' ) ) AS sku,
                    max( IF ( revs.child_key = 'price', revs.child_val, '' ) ) AS price,
                    max( IF ( revs.child_key = 'weight', revs.child_val, '' ) ) AS weight,
                    max( IF ( revs.child_key = 'dimension', revs.child_val, '' ) ) AS dimension,
                    revs.created_by,
                    prod.date_created 
                FROM
                    $table_product prod
                    INNER JOIN $table_revs revs ON prod.title = revs.ID 
                    OR prod.preview = revs.ID 
                    OR prod.short_info = revs.ID 
                    OR prod.long_info = revs.ID 
                    OR prod.`status` = revs.ID 
                    OR prod.sku = revs.ID 
                    OR prod.price = revs.ID 
                    OR prod.weight = revs.ID 
                    OR prod.dimension = revs.ID
                    INNER JOIN $table_stores str ON prod.stid = str.ID
                    INNER JOIN $table_revs revs_1 ON str.title = revs_1.ID
                    INNER JOIN $table_categories cat ON prod.ctid = cat.ID
                    INNER JOIN $table_revs revs_2 ON cat.title = revs_2.ID 
                    OR cat.info = revs_2.ID 
                    WHERE prod.id BETWEEN $add_feeds AND ($get_last_id - 1) 
                GROUP BY
                    revs.parent_id DESC LIMIT 12
                
              
                ");

                //Step 8: Check if array count is 0 , return error message if true
				if (count($result) < 1) {

					return rest_ensure_response( 
						array(
							"status" => "failed",
							"message" => "No more posts to see",
						)
                    );
                    
				} else {

					//Pass the last id
                    $last_id = min($result);
                    //Step 9: Return a success message and a complete object
                    return rest_ensure_response( 
                        array(
                            "status" => "success",
                            "data" => array(
                                'list' => $result, 
                                'last_id' => $last_id
                            )
                        )
                    );
                    
                }
                
            }
        }

        public static function update_product(){
            global $wpdb;

            // Step1 : check if datavice plugin is activated
            if (TP_Globals::verifiy_datavice_plugin() == false) {
                return rest_ensure_response( 
                    array(
                        "status" => "unknown",
                        "message" => "Please contact your administrator. Plugin Missing!",
                    )
                );
            }

            // Step2 : Check if wpid and snky is valid
            if (TP_Globals::validate_user() == false) {
                return rest_ensure_response( 
                    array(
                        "status" => "unknown",
                        "message" => "Please contact your administrator. Request Unknown!",
                    )
                );
            }

            // Step3 : Sanitize all Request
            if (!isset($_POST["wpid"]) 
                || !isset($_POST["snky"]) 
                || !isset($_POST["ctid"]) 
                || !isset($_POST['pdid']) 
                || !isset($_POST["stid"]) 
                || !isset($_POST["title"]) 
                || !isset($_POST["short_info"]) 
                || !isset($_POST["long_info"]) 
                || !isset($_POST["sku"]) 
                || !isset($_POST["price"]) 
                || !isset($_POST["weight"]) 
                || !isset($_POST["dimension"]) 
                || !isset($_POST["preview"])) {
                return rest_ensure_response( 
                    array(
                        "status" => "unknown",
                        "message" => "Please contact your administrator. Request unknown!",
                    )
                );
                
            }

            // Step 4: Check if ID is in valid format (integer)
            if (!is_numeric($_POST["wpid"]) || !is_numeric($_POST["ctid"]) || !is_numeric($_POST["stid"]) ) {
                return rest_ensure_response( 
                    array(
                        "status" => "failed",
                        "message" => "Please contact your administrator. ID not in valid format!",
                    )
                );
                
            }

            // Step 5: Check if ID exists
            if (!get_user_by("ID", $_POST['wpid'])) {
                return rest_ensure_response( 
                    array(
                        "status" => "failed",
                        "message" => "User not found!",
                    )
                );
                
            }


               // Step6: Sanitize all Request if empty
               if (empty($_POST["wpid"]) 
                || empty($_POST["snky"]) 
                || empty($_POST["ctid"]) 
                || empty($_POST['pdid']) 
                || empty($_POST["stid"]) 
                || empty($_POST["title"]) 
                || empty($_POST["short_info"]) 
                || empty($_POST["long_info"]) 
                || empty($_POST["sku"]) 
                || empty($_POST["price"]) 
                || empty($_POST["weight"]) 
                || empty($_POST["dimension"]) 
                || empty($_POST["preview"])) {
               return rest_ensure_response( 
                   array(
                       "status" => "unknown",
                       "message" => "Required fields cannot be empty",
                   )
               );
               
           }
            // variables for query    
            $later = TP_Globals::date_stamp();
            $created_by = $_POST['wpid'];
            $parent_id = $_POST['pdid'];
            $ctid = $_POST['ctid'];
            $stid = $_POST['stid'];
            $revs_type = "products";

            $child_vals = array(
                $_POST['title'],
                $_POST['short_info'],
                $_POST['long_info'],
                $_POST['sku'],
                $_POST['price'],
                $_POST['weight'],
                $_POST['dimension'],
                $_POST['preview'],
            );

            
            $child_keys = array('title', 'short_info', 'long_info', 'sku', 'price',  'weight',  'dimension', 'preview' );
            $last_id_product = array();

            $table_revs = TP_REVISION;
            $table_product = TP_PRODUCT_TABLE;

            // query
            for ($count=0; $count < count($child_keys) ; $count++) {
                $sql = "INSERT INTO $table_revs (revs_type, parent_id, child_key , child_val, created_by, date_created  ) VALUES ('$revs_type', $parent_id, '$child_keys[$count]', '$child_vals[$count]', '$created_by', '$later')";
                // $result = $wpdb->insert('$table_product_revs', array( 'parent_id'=> $parent_id, 'child_key'=> $child_keys[$count], 'child_val' => $child_vals[$count] , 'created_by' => $created_by, 'date_created' => $later ) );
                $result_insert = $wpdb->query($sql);
                $last_id=$wpdb->insert_id;
                $last_ids[] = $last_id;

                $sql2 = "UPDATE $table_product SET  $child_keys[$count] = $last_ids[$count] WHERE ID= $parent_id ";
                $update_result = $wpdb->query($sql2);

            }
            // return if result is empty or null
            if($result_insert < 0 || $update_result <0 ){
                return rest_ensure_response( 
                    array(
                        "status" => "unknown",
                        "message" => "Please contact your administrator!",
                    )
                );

            }else {
                // return Success
				return rest_ensure_response( 
                    array(
                        "status" => "success",
                        "message" => "Product has been updated successfully!",
                    )
                );
                
			}

        }

        public static function retrieveById_product(){
            global $wpdb;
                
            if (TP_Globals::verifiy_datavice_plugin() == false) {
                return rest_ensure_response( 
                    array(
                        "status" => "unknown",
                        "message" => "Please contact your administrator. Plugin Missing!",
                    )
                );
            }

            // Step1 : Check if wpid and snky is valid
            if (TP_Globals::validate_user() == false) {
                return rest_ensure_response( 
                    array(
                        "status" => "unknown",
                        "message" => "Please contact your administrator. Request Unknown!",
                    )
                );
            }

            // Step2 : Sanitize all Request
			if (!isset($_POST["wpid"]) || !isset($_POST["snky"]) || !isset($_POST['pdid'])  ) {
				return rest_ensure_response( 
					array(
						"status" => "unknown",
						"message" => "Please contact your administrator. Request unknown!",
					)
                );
                
            }

            // Step 3: Check if ID is in valid format (integer)
			if (!is_numeric($_POST["wpid"]) || !is_numeric($_POST["pdid"])  ) {
				return rest_ensure_response( 
					array(
						"status" => "failed",
						"message" => "Please contact your administrator. ID not in valid format!",
					)
                );
                
			}

			// Step 4: Check if ID exists
			if (!get_user_by("ID", $_POST['wpid'])) {
				return rest_ensure_response( 
					array(
						"status" => "failed",
						"message" => "User not found!",
					)
                );
                
            }


               // Step5 : Sanitize all Request
			if (empty($_POST["wpid"]) || empty($_POST["snky"]) || empty($_POST['pdid']) ) {
				return rest_ensure_response( 
					array(
						"status" => "unknown",
						"message" => "Required field cannot be empty",
					)
                );
                
            }


            $table_product = TP_PRODUCT_TABLE;
            $table_stores = TP_STORES_TABLE;
            $table_categories = TP_CATEGORIES_TABLE;

            $table_revs = TP_REVISION_TABLE;
            $pdid = $_POST['pdid'];

            $result =  $wpdb->get_results("SELECT
                tp_prod.ID,
                ( SELECT tp_rev.child_val FROM $table_revs tp_rev WHERE ID = tp_str.title ) AS `store_name`,
                ( SELECT tp_cat.types FROM $table_categories tp_cat WHERE ID = tp_prod.ctid ) AS `category`,
                ( SELECT tp_rev.child_val FROM $table_revs tp_rev WHERE ID = tp_prod.title ) AS `title`,
                ( SELECT tp_rev.child_val FROM $table_revs tp_rev WHERE ID = tp_prod.preview ) AS `preview`,
                ( SELECT tp_rev.child_val FROM $table_revs tp_rev WHERE ID = tp_prod.short_info ) AS `short_info`,
                ( SELECT tp_rev.child_val FROM $table_revs tp_rev WHERE ID = tp_prod.long_info ) AS `long_info`,
                ( SELECT tp_rev.child_val FROM $table_revs tp_rev WHERE ID = tp_prod.`status` ) AS `status`,
                ( SELECT tp_rev.child_val FROM $table_revs tp_rev WHERE ID = tp_prod.sku ) AS `sku`,
                ( SELECT tp_rev.child_val FROM $table_revs tp_rev WHERE ID = tp_prod.price ) AS `price`,
                ( SELECT tp_rev.child_val FROM $table_revs tp_rev WHERE ID = tp_prod.weight ) AS `weight`,
                ( SELECT tp_rev.child_val FROM $table_revs tp_rev WHERE ID = tp_prod.dimension ) AS `dimension`,
                ( SELECT tp_rev.child_val FROM $table_revs tp_rev WHERE ID = tp_prod.short_info ) AS `short_info` 
            FROM
                $table_product tp_prod
                INNER JOIN $table_revs tp_rev ON tp_rev.ID = tp_prod.title
                INNER JOIN $table_stores tp_str ON tp_str.ID = tp_prod.stid 
            WHERE
                tp_prod.ID = $pdid
            GROUP BY
                tp_prod.ID
            ");

            if(empty($result)){
                //Step 6: Return result
                return rest_ensure_response( 
                    array(
                        "status" => "success",
                        "message" => "Please Contact your Administrator. Product fetch failed"
                    )
                );   
            }else {
                //Step 6: Return result
                return rest_ensure_response( 
                    array(
                        "status" => "success",
                        "data" => array(
                            'list' => $result, 
                        
                        )
                    )
                );
            }
        }

        public static function delete_product(){
            global $wpdb;
            // Step1 : check if datavice plugin is activated
            if (TP_Globals::verifiy_datavice_plugin() == false) {
                return rest_ensure_response( 
                    array(
                        "status" => "unknown",
                        "message" => "Please contact your administrator. Plugin Missing!",
                    )
                );
            }
            // Step2 : Check if wpid and snky is valid
            if (TP_Globals::validate_user() == false) {
                return rest_ensure_response( 
                    array(
                        "status" => "unknown",
                        "message" => "Please contact your administrator. Request Unknown!",
                    )
                );
            }

             // Step3 : Sanitize all Request
			if (!isset($_POST["wpid"]) || !isset($_POST['pid']) ) {
				return rest_ensure_response( 
					array(
						"status" => "unknown",
						"message" => "Please contact your administrator. Request unknown!",
					)
                );
                
            }

            // Step 1: Check if ID is in valid format (integer)
            if (!is_numeric($_POST["wpid"]) || !is_numeric($_POST["pid"]) ) {
                return rest_ensure_response( 
                    array(
                        "status" => "failed",
                        "message" => "Please contact your administrator. ID not in valid format!",
                    )
                );
                
            }

            // Step 5: Check if ID exists
			if (!get_user_by("ID", $_POST['wpid'])) {
				return rest_ensure_response( 
					array(
						"status" => "failed",
						"message" => "User not found!",
					)
                );
                
            }

            // Step6 : Sanitize all Request
			if (empty($_POST["wpid"]) || empty($_POST['pid']) ) {
				return rest_ensure_response( 
					array(
						"status" => "unknown",
						"message" => "Please contact your administrator. Request unknown!",
					)
                );
                
            }
            // variables
            $parentid = $_POST['pid'];

            $wpid = $_POST['wpid'];

            $product_type = "products";

            $date_stamp = TP_Globals::date_stamp();

            // Query
            $wpdb->query("START TRANSACTION ");

                $result1 = $wpdb->query("INSERT INTO tp_revisions (revs_type, parent_id, child_key, child_val, created_by, date_created) 
                                            VALUES ('$product_type', $parentid , 'status', 'active', $wpid, '$date_stamp'  )");
               
                $last_id = $wpdb->insert_id;

                $result2 = $wpdb->query("UPDATE tp_products SET tp_products.`status` =  $last_id  WHERE tp_products.ID = $parentid ");

            $wpdb->query("COMMIT");

            // check of retsult is true or not
            if ($result1 == false || $result2 == false) {

                return rest_ensure_response( 
					array(
						"status" => "failed",
						"message" => "Please contact your administrator. Deletion Failed",
					)
                );

            }else{
            // return Success
                return rest_ensure_response( 
					array(
						"status" => "success",
						"message" => "Product has been updated successfully",
					)
                );

            }

        }

        public static function get_product_by_storeid(){
            global $wpdb;
            
            // Step 1 : Verfy if Datavice Plugin is Activated
			if (TP_Globals::verifiy_datavice_plugin() == false) {
                return rest_ensure_response( 
                    array(
                        "status" => "unknown",
                        "message" => "Please contact your administrator. Plugin Missing!",
                    )
                );
			}
			//step 2: validate User
			if (TP_Globals::validate_user() == false) {
                return rest_ensure_response( 
                    array(
                        "status" => "unknown",
                        "message" => "Please contact your administrator. Request Unknown!",
                    )
                );
            }

            // Step3 : Sanitize all Request
			if (!isset($_POST["wpid"]) || !isset($_POST["snky"]) || !isset($_POST['stid']) ) {
				return rest_ensure_response( 
					array(
						"status" => "unknown",
						"message" => "Please contact your administrator. Request unknown!",
					)
                );
                
            }


            // Step 4: Check if ID is in valid format (integer)
			if (!is_numeric($_POST["wpid"]) || !is_numeric($_POST['stid']) ) {
				return rest_ensure_response( 
					array(
						"status" => "failed",
						"message" => "Please contact your administrator. ID not in valid format!",
					)
                );
                
            }
            

			// Step 5: Check if ID exists
			if (!get_user_by("ID", $_POST['wpid'])) {
				return rest_ensure_response( 
					array(
						"status" => "failed",
						"message" => "User not found!",
					)
                );
                
            }

            // Step6 : Sanitize all Request if emply
			if (empty($_POST["wpid"]) || empty($_POST["snky"]) || empty($_POST['stid']) ) {
				return rest_ensure_response( 
					array(
						"status" => "unknown",
						"message" => "Required fields cannot be empyty.",
					)
                );
                
            }

            $stid = $_POST['stid'];

            $tp_revs = TP_REVISION_TABLE;
            $table_product = TP_PRODUCT_TABLE;
            $table_categories = TP_CATEGORIES_TABLE;


            $result = $wpdb->get_results("SELECT
                tp_prod.ID,
                ( SELECT tp_cat.types FROM $table_categories tp_cat WHERE ID = tp_prod.ctid ) AS `category`,
                ( SELECT tp_rev.child_val FROM $tp_revs tp_rev WHERE ID = tp_prod.title ) AS `title`,
                ( SELECT tp_rev.child_val FROM $tp_revs tp_rev WHERE ID = tp_prod.preview ) AS `preview`,
                ( SELECT tp_rev.child_val FROM $tp_revs tp_rev WHERE ID = tp_prod.short_info ) AS `short_info`,
                ( SELECT tp_rev.child_val FROM $tp_revs tp_rev WHERE ID = tp_prod.long_info ) AS `long_info`,
                ( SELECT tp_rev.child_val FROM $tp_revs tp_rev WHERE ID = tp_prod.`status` ) AS `status`,
                ( SELECT tp_rev.child_val FROM $tp_revs tp_rev WHERE ID = tp_prod.sku ) AS `sku`,
                ( SELECT tp_rev.child_val FROM $tp_revs tp_rev WHERE ID = tp_prod.price ) AS `price`,
                ( SELECT tp_rev.child_val FROM $tp_revs tp_rev WHERE ID = tp_prod.weight ) AS `weight`,
                ( SELECT tp_rev.child_val FROM $tp_revs tp_rev WHERE ID = tp_prod.dimension ) AS `dimension`,
                ( SELECT tp_rev.child_val FROM $tp_revs tp_rev WHERE ID = tp_prod.short_info ) AS `short_info` 
            FROM
                $table_product tp_prod
                INNER JOIN $tp_revs tp_rev ON tp_rev.ID = tp_prod.title 
            WHERE
                tp_prod.stid = $stid
            GROUP BY
                tp_prod.ID ");


            if(empty($result)){
                return rest_ensure_response( 
                    array(
                        "status" => "unknown",
                        "message" => "Please contact your administrator.",
                    )
                );
            }else{
                return rest_ensure_response( 
                    array(
                        "status" => "success",
                        "data" => array(
                            'list' => $result, 
                        
                        )
                    )
                );
            }


        }

        public static function get_product_search(){
            global $wpdb;


              // Step 1 : Verfy if Datavice Plugin is Activated
			if (TP_Globals::verifiy_datavice_plugin() == false) {
                return rest_ensure_response( 
                    array(
                        "status" => "unknown",
                        "message" => "Please contact your administrator. Plugin Missing!",
                    )
                );
			}
			//step 2: validate User
			if (TP_Globals::validate_user() == false) {
                return rest_ensure_response( 
                    array(
                        "status" => "unknown",
                        "message" => "Please contact your administrator. Request Unknown!",
                    )
                );
            }

            // Step3 : Sanitize all Request
			if (!isset($_POST["wpid"]) || !isset($_POST["snky"]) || !isset($_POST['search']) ) {
				return rest_ensure_response( 
					array(
						"status" => "unknown",
						"message" => "Please contact your administrator. Request unknown!",
					)
                );
                
            }

             // Step6 : Sanitize all Request if emply
			if (empty($_POST["wpid"]) || empty($_POST["snky"]) || empty($_POST['search']) ) {
				return rest_ensure_response( 
					array(
						"status" => "unknown",
						"message" => "Required fields cannot be empyty.",
					)
                );
                
            }


            // Step 4: Check if ID is in valid format (integer)
			if (!is_numeric($_POST["wpid"]) ) {
				return rest_ensure_response( 
					array(
						"status" => "failed",
						"message" => "Please contact your administrator. ID not in valid format!",
					)
                );
                
            }
            

			// Step 5: Check if ID exists
			if (!get_user_by("ID", $_POST['wpid'])) {
				return rest_ensure_response( 
					array(
						"status" => "failed",
						"message" => "User not found!",
					)
                );
                
            }

            $value = $_POST['search'];

           
            $tp_revs = TP_REVISION_TABLE;
            $table_product = TP_PRODUCT_TABLE;
            $table_categories = TP_CATEGORIES_TABLE;


            $result = $wpdb->get_results("SELECT
                tp_prod.ID,
                ( SELECT tp_rev.child_val FROM tp_stores INNER JOIN $tp_revs tp_rev ON tp_rev.ID = tp_stores.title WHERE tp_prod.stid = tp_stores.id ) AS `store_name`,
                ( SELECT tp_cat.types FROM tp_categories tp_cat WHERE ID = tp_prod.ctid ) AS `category`,
                ( SELECT tp_rev.child_val FROM $tp_revs tp_rev WHERE ID = tp_prod.title ) AS `title`,
                ( SELECT tp_rev.child_val FROM $tp_revs tp_rev WHERE ID = tp_prod.preview ) AS `preview`,
                ( SELECT tp_rev.child_val FROM $tp_revs tp_rev WHERE ID = tp_prod.short_info ) AS `short_info`,
                ( SELECT tp_rev.child_val FROM $tp_revs tp_rev WHERE ID = tp_prod.long_info ) AS `long_info`,
                ( SELECT tp_rev.child_val FROM $tp_revs tp_rev WHERE ID = tp_prod.`status` ) AS `status`,
                ( SELECT tp_rev.child_val FROM $tp_revs tp_rev WHERE ID = tp_prod.sku ) AS `sku`,
                ( SELECT tp_rev.child_val FROM $tp_revs tp_rev WHERE ID = tp_prod.price ) AS `price`,
                ( SELECT tp_rev.child_val FROM $tp_revs tp_rev WHERE ID = tp_prod.weight ) AS `weight`,
                ( SELECT tp_rev.child_val FROM $tp_revs tp_rev WHERE ID = tp_prod.dimension ) AS `dimension`,
                ( SELECT tp_rev.child_val FROM $tp_revs tp_rev WHERE ID = tp_prod.short_info ) AS `short_info` 
            FROM
                $table_product tp_prod
                INNER JOIN $tp_revs tp_rev ON tp_rev.ID = tp_prod.title 
            WHERE
                tp_rev.child_val REGEXP '^$value';", OBJECT);

            if(empty($result)){
                return rest_ensure_response( 
                    array(
                        "status" => "unknown",
                        "message" => "Please contact your administrator. No Product with this value",
                    )
                );
                
            }else{
                return rest_ensure_response( 
                    array(
                        "status" => "success",
                        "data" => array(
                            'list' => $result, 
                        
                        )
                    )
                );
            }


        }

        // GET product by store  product category ID
        public static function initialize(){
            global $wpdb;


			//  Step1 : Verify if Datavice Plugin is Active
			if (TP_Globals::verifiy_datavice_plugin() == false) {
                return rest_ensure_response( 
                    array(
                        "status" => "unknown",
                        "message" => "Please contact your administrator. Plugin Missing!",
                    )
                );
			}
			
			//  Step2 : Validate if user is exist
			if (TP_Globals::validate_user() == false) {
                return rest_ensure_response( 
                    array(
                        "status" => "unknown",
                        "message" => "Please contact your administrator. Request Unknown!",
                    )
                );
            }

            // Step3 : Sanitize all Request
			if (!isset($_POST["wpid"]) || !isset($_POST["snky"]) || !isset($_POST["ctid"]) || !isset($_POST["types"]) || !isset($_POST["stid"])) {
				return rest_ensure_response( 
					array(
						"status" => "unknown",
						"message" => "Please contact your administrator. Request unknown!",
					)
                );
                
            }


            // Step 4: Check if ID is in valid format (integer)
			if (!is_numeric($_POST["wpid"]) || !is_numeric($_POST['ctid'])) {
				return rest_ensure_response( 
					array(
						"status" => "failed",
						"message" => "Please contact your administrator. ID not in valid format!",
					)
                );
                
			}

			// Step 5: Check if ID exists
			if (!get_user_by("ID", $_POST['wpid'])) {
				return rest_ensure_response( 
					array(
						"status" => "failed",
						"message" => "User not found!",
					)
                );
                
			}
			
            // Step6 : Sanitize all Request
			if (empty($_POST["wpid"]) || empty($_POST["snky"]) || empty($_POST["ctid"]) || empty($_POST["types"])  || empty($_POST["stid"])) {
				return rest_ensure_response( 
					array(
						"status" => "unknown",
						"message" => "Required fields cannot be empty.",
					)
                );
                
            }

            //Step 7: Create table name for posts (tp_categories, tp_categories_revs)
			// table names and POST Variables
            $store_table           = TP_STORES_TABLE;
            $tp_categories_table   = TP_CATEGORIES_TABLE;
            $product_table         = TP_PRODUCT_TABLE;
            $product_revs_table    = TP_PRODUCT_REVS_TABLE;
            $tp_revs               = TP_REVISION_TABLE;
            // datavice table variables declarations
            $dv_geo_brgy    = DV_BRGY_TABLE;
            $dv_revs        =  DV_REVS_TABLE;
            $dv_address     = DV_ADDRESS_TABLE;
            $dv_geo_city    = DV_CITY_TABLE;
            $dv_geo_prov    = DV_PROVINCE_TABLE;
            $dv_geo_count   = DV_COUNTRY_TABLE;    

            $types = $_POST['types'];
            $stid = $_POST['stid'];
            $ctid = $_POST['ctid'];

            $result = $wpdb->get_results("SELECT
                    tp_prod.ID,
                    tp_cat.types,
                    tp_rev.child_val AS title,
                    tp_reevs.child_val AS category_name,
                    ( SELECT tp_rev.child_val FROM $tp_revs tp_rev WHERE ID = tp_cat.title ) AS `type`, 
                    ( SELECT tp_rev.child_val FROM $tp_revs tp_rev WHERE ID = tp_prod.preview ) AS `preview`,
                    ( SELECT tp_rev.child_val FROM $tp_revs tp_rev WHERE ID = tp_prod.short_info ) AS `short_info`,
                    ( SELECT tp_rev.child_val FROM $tp_revs tp_rev WHERE ID = tp_prod.long_info ) AS `long_info`,
                    ( SELECT tp_rev.child_val FROM $tp_revs tp_rev WHERE ID = tp_prod.`status` ) AS `status`,
                    ( SELECT tp_rev.child_val FROM $tp_revs tp_rev WHERE ID = tp_prod.sku ) AS `sku`,
                    ( SELECT tp_rev.child_val FROM $tp_revs tp_rev WHERE ID = tp_prod.price ) AS `price`,
                    ( SELECT tp_rev.child_val FROM $tp_revs tp_rev WHERE ID = tp_prod.weight ) AS `weight`,
                    ( SELECT tp_rev.child_val FROM $tp_revs tp_rev WHERE ID = tp_prod.dimension ) AS `dimension` 
                FROM
                    tp_products tp_prod
                    INNER JOIN $tp_revs tp_rev ON tp_rev.ID = tp_prod.title
                    INNER JOIN $store_table  tp_strs ON tp_prod.stid = tp_strs.ID
                    INNER JOIN $tp_categories_table tp_cat ON tp_cat.ID = tp_strs.ctid
                    INNER JOIN $tp_revs tp_reevs ON tp_reevs.ID = tp_cat.title 
                WHERE
                    tp_prod.stid = $stid AND tp_cat.types = '$types' AND tp_strs.ctid = $ctid");

       
            if (empty($result)) {

                return rest_ensure_response( 
                    array(
                        "status" => "success",
                        "message" => "Please contact your Administrator. Empty result"
                        
                    )
                );

            }else {
                
                // reutrn success result
                return rest_ensure_response( 
                    array(
                        "status" => "success",
                        "data" => array(
                            'list' => $result, 
                        
                        )
                    )
                );
            }


        }


    }
