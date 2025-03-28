<?php 
/*
	Clear out all PMPro member and report data. (Back up your database first. Use at your own risk!!!)
	This will delete all orders, members/level data, and reporting data.
	Your levels, discount codes, and settings will remain in place. 
	All users will remain users (without memberships).
	All subscriptions at the gateway will remain active.

	To Use:
	* Copy this code into your active theme's functions.php or a custom WP plugin.
	* Navigate to /wp-admin/?pmprocleardata=1 when logged in as an admin.
	
	To truly uninstall PMPro, uninstall the plugin through the WP plugins screen, which will remove all levels and settings data as well.
*/
function pmpro_clear_all_data_init()
{	
	global $wpdb;
	
	if(!empty($_REQUEST['pmprocleardata']) && current_user_can("manage_options"))
	{
		//truncate some tables
		echo "Clearing out tables (pmpro_discount_codes_uses, pmpro_memberships_users, pmpro_membership_orders) ...";
		
		$tables = array(
			'pmpro_discount_codes_uses',		
			'pmpro_memberships_users',		
			'pmpro_membership_orders'
		);

		foreach($tables as $table){
			$truncate_table = $wpdb->prefix . $table;
			// setup sql query
			$sql = "TRUNCATE TABLE `$truncate_table`";
			// run the query
			$wpdb->query($sql);
		}

		require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
		dbDelta($sql);
		
		echo " done.<hr />";
	
		//clear caches
		echo "Clearing report caches ...";
		
		delete_transient("pmpro_report_memberships_signups");
		delete_transient("pmpro_report_memberships_cancellations");
		delete_transient("pmpro_report_mrr");
		delete_transient("pmpro_report_cancellation_rate");
		delete_transient("pmpro_report_sales");
		delete_transient("pmpro_report_revenue");
		
		echo " done.<hr />";		
	
		//clear login/visit info
		echo "Deleting visit/view/login data ...";
		delete_option("pmpro_visits");
		delete_option("pmpro_views");
		delete_option("pmpro_logins");
		echo " ... done.<hr />";
	
		exit;
	}	
}
add_action("init", "pmpro_clear_all_data_init");