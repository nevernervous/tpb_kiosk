<?php
// use this to check last modified post

include ("wp-config.php");

global $wpdb;

$sql = "SELECT post_modified_gmt FROM $wpdb->posts ORDER BY post_modified_gmt DESC LIMIT 1";

$result = $wpdb->get_row($sql);

#print_r ($result);
#print $result->post_modified_gmt;
print md5($result->post_modified_gmt);

?>
