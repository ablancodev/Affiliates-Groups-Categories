<?php
/* 
* Plugin Name: Affiliates Groups Categories
* Plugin URI: http://www.eggemplo.com
* Description: Commissions by groups and categories.
* Version: 1.0
* Author: eggemplo
* Author URI: http://www.eggemplo.com
* License: GPLv3
*/

class Affiliates_Groups_Categories {
	
	public static function init() {
		
		add_filter( 'affiliates_wc_method_product_rate', array( __CLASS__, 'affiliates_wc_method_product_rate' ), 10, 4 );
		
	}
	
	public static function affiliates_wc_method_product_rate ( $rate, $product_id, $affiliate_id, $params ) {
		
		$rates = array();
		$rates['Silver']['hardware'] = 0.10;
		$rates['Silver']['subscriptions'] = 0.25;
		$rates['Gold']['hardware'] = 0.10;
		$rates['Gold']['subscriptions'] = 0.50;
		
		$user_id = affiliates_get_affiliate_user($affiliate_id);
		
		$new_rate = $rate;
		
		if ( $user_id ) {
			$user_groups = self::get_user_groups ( $user_id );
			if ( sizeof( $user_groups ) > 0 ) {
				$first_group = $user_groups[0];
				
				// the category
				$cat_list = wp_get_post_terms($product_id,'product_cat');
				if ( sizeof( $cat_list )>0 ) {
					$first_cat = $cat_list[0];
					
					// change the rate
					if ( isset( $rates[$first_group->name][$first_cat->name] ) ) {
						$new_rate = $rates[$first_group->name][$first_cat->name];
					}
				}
			}
		}
		return $new_rate;
		
	}
	
	
	public static function get_user_groups ( $user_id ) {
		global $wpdb;
	
		$groups_table = _groups_get_tablename( 'group' );
		$result = array();
		if ( $groups = $wpdb->get_results( "SELECT * FROM $groups_table ORDER BY group_id DESC" ) ) {
			foreach( $groups as $group ) {
				$is_member = Groups_User_Group::read( $user_id, $group->group_id ) ? true : false;
				if ( $is_member ) {
					$result[] = $group;
				}
			}
		}
		return  $result;
	}
	
}

Affiliates_Groups_Categories::init();

?>
