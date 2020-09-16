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
    class TP_Store_Listing {

        public static function listen(){
            return rest_ensure_response(
                TP_Store_Listing:: list_open()
            );
        }

        public static function list_open(){

            // 2nd Initial QA 2020-08-24 10:49 PM - Miguel
            global $wpdb;

            // declaring table names to variable
            $table_store = TP_STORES_TABLE;
            $table_revs = TP_REVISIONS_TABLE;
            $table_address = DV_ADDRESS_TABLE;
            $table_dv_revs = DV_REVS_TABLE;
            $table_brgy = DV_BRGY_TABLE;
            $table_city = DV_CITY_TABLE;
            $table_province = DV_PROVINCE_TABLE;
            $table_country = DV_COUNTRY_TABLE;
            $table_category = TP_CATEGORIES_TABLE;
            $table_contacts = DV_CONTACTS_TABLE;
            $table_dv_revisions = DV_REVS_TABLE;

            // Step1 : Check if prerequisites plugin are missing
            $plugin = TP_Globals::verify_prerequisites();
            if ($plugin !== true) {
                return array(
                        "status" => "unknown",
                        "message" => "Please contact your administrator. ".$plugin." plugin missing!",
                );
            }

            // Step2 : Check if wpid and snky is valid
            if (DV_Verification::is_verified() == false) {
                return array(
                        "status" => "unknown",
                        "message" => "Please contact your administrator. Verification Issues!",
                );
            }

            // Step3 : Query
            $sql ="SELECT
            str.ID,
            str.ctid AS `catid`,
            str.address AS `add_id`,
            CONCAT(( SELECT rev.child_val FROM tp_revisions rev WHERE rev.parent_id = str.ID AND  rev.date_created = ( SELECT MAX(date_created) FROM tp_revisions tp_rev WHERE tp_rev.ID = rev.ID AND revs_type = 'stores'  ) AND child_key ='commission' ), '%') AS comm,
            ( SELECT rev.child_val FROM tp_revisions rev WHERE rev.ID = cat.title  AND rev.date_created = (SELECT MAX(tp_rev.date_created) FROM tp_revisions tp_rev WHERE ID = rev.ID  AND revs_type ='categories'   )  ) as cat_name,
            ( SELECT rev.child_val FROM tp_revisions rev WHERE rev.id = str.title AND  rev.date_created = ( SELECT MAX(date_created) FROM tp_revisions tp_rev WHERE tp_rev.ID = rev.ID AND revs_type = 'stores' )  ) AS title,
            ( SELECT rev.child_val FROM tp_revisions rev WHERE rev.id = str.short_info AND  rev.date_created = ( SELECT MAX(date_created) FROM tp_revisions tp_rev WHERE tp_rev.ID = rev.ID AND revs_type = 'stores' ) ) AS short_info,
            ( SELECT rev.child_val FROM tp_revisions rev WHERE rev.id = str.long_info AND  rev.date_created = ( SELECT MAX(date_created) FROM tp_revisions tp_rev WHERE tp_rev.ID = rev.ID AND revs_type = 'stores' ) ) AS long_info,
            ( SELECT rev.child_val FROM tp_revisions rev WHERE rev.id = str.logo AND  rev.date_created = ( SELECT MAX(date_created) FROM tp_revisions tp_rev WHERE tp_rev.ID = rev.ID AND revs_type = 'stores' ) ) AS avatar,
            ( SELECT rev.child_val FROM tp_revisions rev WHERE rev.id = str.banner AND  rev.date_created = ( SELECT MAX(date_created) FROM tp_revisions tp_rev WHERE tp_rev.ID = rev.ID AND revs_type = 'stores' ) ) AS banner,
            IF ( ( SELECT rev.child_val FROM tp_revisions rev WHERE rev.id = str.`status` AND date_created = (SELECT MAX(tp_rev.date_created) FROM tp_revisions tp_rev WHERE tp_rev.ID = rev.ID AND tp_rev.child_key = 'status')   ) = 1, 'Active', 'Inactive' ) AS `status`,
            ( SELECT dv_rev.child_val FROM dv_revisions  dv_rev WHERE dv_rev.ID = `add`.street AND dv_rev.date_created = (SELECT MAX(date_created)  FROM dv_revisions WHERE ID = dv_rev.ID AND revs_type ='address')   ) AS street,
            ( SELECT brgy_name FROM dv_geo_brgys WHERE ID = ( SELECT dv_rev.child_val FROM dv_revisions dv_rev WHERE dv_rev.id = `add`.brgy  AND dv_rev.date_created = (SELECT MAX(date_created)  FROM dv_revisions WHERE ID = dv_rev.ID AND revs_type ='address') ) ) AS brgy,
            ( SELECT city_name FROM dv_geo_cities WHERE city_code = ( SELECT dv_rev.child_val FROM dv_revisions dv_rev WHERE dv_rev.id = `add`.city  AND dv_rev.date_created = (SELECT MAX(date_created)  FROM dv_revisions WHERE ID = dv_rev.ID AND revs_type ='address')  ) ) AS city,
            ( SELECT prov_name FROM dv_geo_provinces WHERE prov_code = ( SELECT dv_rev.child_val FROM dv_revisions dv_rev WHERE dv_rev.id = `add`.province AND dv_rev.date_created = (SELECT MAX(date_created)  FROM dv_revisions WHERE ID = dv_rev.ID AND revs_type ='address')  ) ) AS province,
            ( SELECT country_name FROM dv_geo_countries WHERE id = ( SELECT dv_rev.child_val FROM dv_revisions dv_rev WHERE dv_rev.id = `add`.country  AND dv_rev.date_created = (SELECT MAX(date_created)  FROM dv_revisions WHERE ID = dv_rev.ID AND revs_type ='address')  ) ) AS country,
            ( SELECT dv_rev.child_val FROM dv_revisions  dv_rev WHERE dv_rev.ID = `add`.latitude AND dv_rev.date_created = (SELECT MAX(date_created)  FROM dv_revisions WHERE ID = dv_rev.ID AND revs_type ='address') AND child_key ='latitude' AND revs_type ='address'  ) AS `lat`,
            ( SELECT dv_rev.child_val FROM dv_revisions  dv_rev WHERE dv_rev.ID = `add`.longitude AND dv_rev.date_created = (SELECT MAX(date_created)  FROM dv_revisions WHERE ID = dv_rev.ID AND revs_type ='address') AND child_key ='longitude' AND revs_type ='address'  ) AS `long`,
            ( SELECT child_val FROM dv_revisions WHERE ID = ( SELECT revs FROM dv_contacts WHERE types = 'phone' AND stid = str.ID LIMIT 1 ) LIMIT 1 ) AS phone,
            ( SELECT child_val FROM dv_revisions WHERE ID = ( SELECT revs FROM dv_contacts  WHERE types = 'email' AND stid = str.ID LIMIT 1 ) LIMIT 1 ) AS email
        FROM
            tp_stores str
            INNER JOIN dv_address `add` ON str.address = `add`.ID
            INNER JOIN tp_categories cat ON cat.ID = str.ctid
            ";
            isset($_POST['status']) ? $sts = $_POST['status'] : $sts = NULL  ;
            isset($_POST['catid']) ? $ctd = $_POST['catid'] : $ctd = NULL  ;
            isset($_POST['stid']) ? $std = $_POST['stid'] : $std = NULL  ;

            // Ternary condition for isset value
            $status = $sts == '0' || $sts == NULL ? NULL : ($sts == '2'&& $sts !== '0'? '0':'1');
            $catid = $ctd == '0'? NULL: $catid = $ctd;
            $stid = $std == "0" ? NULL: $stid = $std;

            // Status condition
            if(isset($_POST['status'])){
                if($status != NULL){

                    $sql .= " WHERE ( SELECT rev.child_val FROM tp_revisions rev WHERE rev.id = str.`status` AND date_created = (SELECT MAX(tp_rev.date_created) FROM tp_revisions tp_rev WHERE tp_rev.ID = rev.ID AND tp_rev.child_key = 'status')   ) = $status";
                }
            }

            // Category condition
            if (isset($_POST['catid'])) {
                if ($catid != NULL && $catid != '0') {

                    if ($status !== NULL ) {

                        $sql .= " AND `str`.ctid = $catid ";

                    }else{

                        $sql .= " WHERE `str`.ctid = $catid ";

                    }
                }
            }

            // Store ID Condition
            if (isset($_POST['stid'])) {
                if ($stid != 0 ) {

                    if ( $status == NULL && empty($status) && $catid == NULL && empty($catid) ) {
                        $sql .= " WHERE `str`.ID = '$stid' ";

                    } else {
                        $sql .= " AND `str`.ID = '$stid' ";

                    }

                }
            }
            // Uncomment for debugging

            $limit ='12';

            if( isset($_POST['lid']) ){
				// Step 4: Validate parameter
                if (empty($_POST['lid']) ) {
                    return array(
                        "status" => "failed",
                        "message" => "Required fields cannot be empty.",
                    );
                }
				if ( !is_numeric($_POST["lid"])) {
					return array(
						"status" => "failed",
						"message" => "Parameters not in valid format.",
					);
				}

				$lastid = $_POST['lid'];
				$sql .= " AND str.ID < $lastid ";
				$limit = 7;

            }

            // return $sql;
            // Execute query
			$sql .= " ORDER BY str.ID DESC LIMIT $limit ";
            $result = $wpdb->get_results($sql);

            // Step4 : Check if no result
            if (!$result ) {
                return array(
                    "status" => "success",
                    "data" => [],
                );
            }else{

                foreach ($result as $key => $value) {

                    if($value->avatar == null || $value->avatar == 'None' ){
                        $value->avatar =  TP_PLUGIN_URL . "assets/images/default-store.png" ;
                    }

                    if($value->banner == null || $value->banner == 'None' ){
                        $value->banner =  TP_PLUGIN_URL . "assets/images/default-banner.png" ;
                    }

                    if($value->lat == null || $value->lat == 'None' ){
                        $value->lat = "" ;
                    }

                    if($value->long == null || $value->long == 'None' ){
                        $value->long = "" ;
                    }

                }
                // Step5 : Return Result
                return array(
                    "status" => "success",
                    "data" => $result,
                );
            }
        }
    }