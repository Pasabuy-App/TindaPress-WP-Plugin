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
    
    class TP_Product_Discount_Create {
        
        //REST API Call
        public static function listen(){
            
            return rest_ensure_response( 
                self:: insert_product()
            );
        }
        
        public static function insert_product(){
            global $wpdb;
            $tbl_revs_fields = TP_REVISION_FIELDS;
            $tbl_revssion = TP_REVISIONS_TABLE;
            $date = TP_Globals::date_stamp();

            // Step 1 : Check if prerequisites plugin are missing
            $plugin = TP_Globals::verify_prerequisites();
            if ($plugin !== true) {
                return array(
                    "status" => "unknown",
                    "message" => "Please contact your administrator. ".$plugin." plugin missing!",
                );
            }

            //  Step 2 : Validate if user is exist
			if (DV_Verification::is_verified() == false) {
                return array(
                    "status" => "unknown",
                    "message" => "Please contact your administrator. Verification Issues!",
                );
            }

            if (!isset($_POST['pdid']) || !isset($_POST['value']) || !isset($_POST['type'])  ) {
                return array(
                    "status" => "unknown",
                    "message" => "Please contact your administrator. Request unknown!"
                );
            }

            if (empty($_POST['pdid']) || empty($_POST['value']) || empty($_POST['type']) ) {
                return array(
                    "status" => "failed",
                    "message" => "Required fields cannot be empty."
                );
            }

            if ( !isset($_POST['exp']) || empty($_POST['exp']) || $_POST['exp'] == "") {
          
                $expiration_date = NULL;
                
            } else {

                $dt = self::validateDate($_POST['exp']);   

                if ( !$dt ) {
                    return array(
                        "status" => "failed",
                        "message" => "Expiratation date is not in valid format.",
                    );
                }

                $expiration_date = $_POST['exp'];
            }

            if ($_POST['type'] !== 'percentage_discount' && $_POST['type'] !== 'less_discount') {
                return array(
                    "status" => "failed",
                    "message" => "Request type is not in valid format.",
                );
            }


            // Validate Product
            $product_id = $_POST['pdid'];
                
                $validate_product = $wpdb->get_row(
                    $wpdb->prepare("SELECT child_val FROM tp_revisions WHERE ID = (SELECT `status` FROM  tp_products WHERE ID = %d ) AND revs_type = 'products' AND child_key = 'status' AND parent_id = %d ", $product_id, $product_id )
                );

                if (!$validate_product) {
                    return array(
                        "status" => "failed",
                        "message" => "This product does not exists.",
                    );
                }

                
                if ($validate_product->child_val === '0' ) {
                    return array(
                        "status" => "failed",
                        "message" => "This product is currently inactive.",
                    );
                }
            // End of validation product

            $user = self::catch_post();

            $type = $_POST['type'];

            $wpdb->query("START TRANSACTION");


            // Validate product discount
            
                $check_produc_discount = $wpdb->get_row("SELECT
                 (SELECT child_val  FROM tp_revisions rev  WHERE child_key = 'discount_name'  AND revs_type = 'products'  AND parent_id = '{$user["product_id"]}' 	AND ID = ( SELECT max(ID) FROM tp_revisions WHERE child_key = 'discount_name' AND parent_id = '{$user["product_id"]}' AND revs_type = 'products' ) ) as  `name`,
                 (SELECT child_val  FROM tp_revisions rev  WHERE child_key = 'discount_value'  AND revs_type = 'products'  AND parent_id = '{$user["product_id"]}' 	AND ID = ( SELECT max(ID) FROM tp_revisions WHERE child_key = 'discount_value' AND parent_id = '{$user["product_id"]}' AND revs_type = 'products' )) as  `value`, 
                 (SELECT child_val  FROM tp_revisions rev  WHERE child_key = 'discount_expiry'  AND revs_type = 'products'  AND parent_id = '{$user["product_id"]}' 	AND ID = ( SELECT max(ID) FROM tp_revisions WHERE child_key = 'discount_expiry' AND parent_id = '{$user["product_id"]}' AND revs_type = 'products' )) as  `expiry`,
                 IF ( (SELECT child_val  FROM tp_revisions rev  WHERE child_key = 'discount_status'  AND revs_type = 'products'  AND parent_id = '{$user["product_id"]}' 	AND ID = ( SELECT max(ID) FROM tp_revisions WHERE child_key = 'discount_status' AND parent_id = '{$user["product_id"]}' AND revs_type = 'products' )) = 1 , 'Active', 'Inactive') as  `status` 
                 ");

                if ($check_produc_discount->status === 'Active') {
                    if($check_produc_discount->name !== NULL && $check_produc_discount->value !== NULL && $check_produc_discount->expiry !== NULL ){
                        return array(
                            "status" => "failed",
                            "message" => "This product is already have an discount.",
                        );
                    }
                }

            // End validate product discount


        
            // insert discount name
            $result_discount = $wpdb->query($wpdb->prepare("INSERT INTO $tbl_revssion $tbl_revs_fields VALUES ( '%s', %d, '%s', '%s', %d, '%s' )", 'products', $user['product_id'], 'discount_name', $type, $user['wpid'], $date  ));
            $result_discount_id = $wpdb->insert_id;
            
            $update_hash_discount = $wpdb->query("UPDATE $tbl_revssion SET `hash_id` = SHA2( $result_discount_id ,256) WHERE ID = $result_discount_id AND revs_type = 'products' ");
            
            // insert discount value
            $result_discount_value = $wpdb->query($wpdb->prepare("INSERT INTO $tbl_revssion $tbl_revs_fields VALUES ( '%s', %d, '%s', '%s', %d, '%s' )", 'products', $user['product_id'], 'discount_value', $user['value'], $user['wpid'], $date  ));
            $result_discount_id_value = $wpdb->insert_id;
            
            $update_hash_discount_value = $wpdb->query("UPDATE $tbl_revssion SET `hash_id` = SHA2( $result_discount_id_value ,256) WHERE ID = $result_discount_id_value AND revs_type = 'products' ");

            $result_expiry = $wpdb->query($wpdb->prepare("INSERT INTO $tbl_revssion $tbl_revs_fields VALUES ( '%s', %d, '%s', '%s', %d, '%s' )", 'products', $user['product_id'], 'discount_expiry', $user['expiry'], $user['wpid'], $date  ));
            $result_expiry_id = $wpdb->insert_id;

            $update_hash_expiry = $wpdb->query("UPDATE $tbl_revssion SET `hash_id` = SHA2( $result_expiry_id ,256) WHERE ID = $result_expiry_id AND revs_type = 'products' ");

            $result_discount_stauts = $wpdb->query($wpdb->prepare("INSERT INTO $tbl_revssion $tbl_revs_fields VALUES ( '%s', %d, '%s', '%s', %d, '%s' )", 'products', $user['product_id'], 'discount_status', '1', $user['wpid'], $date  ));
            $result_discount_stauts_id = $wpdb->insert_id;

            $update_hash_discount= $wpdb->query("UPDATE $tbl_revssion SET `hash_id` = SHA2( $result_discount_stauts_id ,256) WHERE ID = $result_discount_stauts_id AND revs_type = 'products' ");

            
            if ($result_discount < 1  || $result_expiry < 1 || $update_hash_discount < 1 || $update_hash_expiry < 1 || $result_discount_stauts < 1 || $update_hash_discount < 1) {
                $wpdb->query("ROLL BACK");
                return array(
                    "status" => "failed",
                    "message" => "An error occured while submitting data to server."
                );
            }else{
                $wpdb->query("COMMIT");
                return array(
                    "status" => "success",
                    "message" => "Data has been added successfully."
                );
            }
        }

        public static function validateDate($date, $format = 'Y-m-d h:i:s')
        {
            $d = DateTime::createFromFormat($format, $date);
            return $d && $d->format($format) == $date;
        }

        public static function catch_post(){
            $curl_user = array();

            $curl_user['wpid'] = $_POST['wpid'];
            $curl_user['type'] = $_POST['type'];
            $curl_user['product_id'] = $_POST['pdid'];
            $curl_user['value'] = $_POST['value'];
            $curl_user['expiry'] = $_POST['exp'];
            
            return $curl_user;

        }

    }