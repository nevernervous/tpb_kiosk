<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       http://waaark.com
 * @since      1.0.0
 *
 * @package    Tpb_Wp_Pos
 * @subpackage Tpb_Wp_Pos/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Tpb_Wp_Pos
 * @subpackage Tpb_Wp_Pos/public
 * @author     Antoine Wodniack <antoine@wodniack.fr>
 */
class Tpb_Wp_Pos_Public {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Tpb_Wp_Pos_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Tpb_Wp_Pos_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/tpb-wp-pos-public.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Tpb_Wp_Pos_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Tpb_Wp_Pos_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		
		 
		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/tpb-wp-pos-public.js', array( 'jquery' ), $this->version, false );

		//print scripts
		wp_enqueue_script( 'rsvp', plugin_dir_url( __FILE__ ) . 'js/dependencies/rsvp-3.1.0.min.js', array( 'jquery' ), 1, false );
		wp_enqueue_script( 'sha', plugin_dir_url( __FILE__ ) . 'js/dependencies/sha-256.min.js', array( 'jquery' ),1, false ); 
		wp_enqueue_script( 'qztray', plugin_dir_url( __FILE__ ) . 'js/qz-tray.js', array( 'jquery' ), 1, false );

	}


    /**
     * Send order
     *
	 * @since    1.0.0
     */
   public static function send_order() {
    	if ( !isset( $_SESSION['cart'] ) || !$_SESSION['cart'] )
    		return false;

    	$cart = $_SESSION['cart'];
		$user =  $_SESSION['user_name'];
		$phone = $_SESSION['phone'];
    	$order = array();

    	foreach ( $cart as $product_id => $amounts ) {
    		$product = get_post( $product_id );
    		$prices = tbp_get_product_prices( $product_id );

    		$record_id = get_post_meta( $product_id, 'product_id', true );
    		//$sku = get_post_meta( $product_id, 'sku', true );
    		$title = $product->post_title;
			$sku = 133;
    		foreach( $amounts as $amount => $qty ) {
				$unit = $prices[$amount]->unit;
				$price = $prices[$amount]->price;

	    		$line = array(
	    			'record_id' => $record_id,
	    			'sku' => $sku,
	    			'title' => $title,
	    			'unit' => $unit,
	    			'unit_price' => $price,
	    			'qty' => $qty,
	    			'total_price' => $qty*$price
	    		);
				$sku++;
	    		$order[] = $line;
    		}

    	}
		//$results = print_r($order, true);
		
		
		//  MJ Freeway
		
		$nid = Tpb_Wp_Pos_Public::check_user($user, $phone);
		if($nid !='multiple' && $nid !='none') {
			$checkout = Tpb_Wp_Pos_Public::mjFreeway($nid, $order);
			$file = WP_PLUGIN_DIR."/tpb-wp-pos/log.txt";  
			file_put_contents($file, $checkout);
			$result = true;
		}else {
			$checkout = false;
			$result = false;
		}
		
		return $result;
	
	

	 }	
	/*
		 ////  Greenbits Receipt  
		
		$receipt = Tpb_Wp_Pos_Public::printReceipt($order, $user);
		$file = WP_PLUGIN_DIR."/tpb-wp-pos/log.txt";  
		file_put_contents($file, $receipt);
	
    	// Do things here to create order in POS
    	// ...
    	$result = true;

    	return $result;
    }
	 */
	public function printReceipt($order, $customer) {
		$prints = '###Customer Order###\n\n';
		$text = '###Customer Order### <br><br>';
		$prints.='Patient Name: '.$customer.'\n';
		$text.='Patient Name: '.$customer.'<br>';
		$total = 0;
		foreach($order as $o) {
			$prints.='Product: '.$o['title'].' ('.$o['sku'].')'.'\n';
			$text.='Product: '.$o['title'].' ('.$o['sku'].')'.'<br>';
			$text.='Size: '.$o['unit'].'<br>';
			$prints.='Quantity: '.$o['qty'].'\n';
			$text.='Quantity: '.$o['qty'].'<br>' ;
			
			$prints.='Cost: '.$o['total_price'].'\n\n';
			$text.='Cost: '.$o['total_price'].'<br>' .'<br>' ;
			$total+=intval($o['total_price']);
		}
		$text.='Total: $'.$total;
		$prints.='Total: $'.$total;

		$to = 'thepeakbeyondreceipts@gmail.com';

			$subject = 'Customer order from '.$customer;

			$headers = "From: thepeakbeyondorders@gmail.com \r\n";
			$headers .= "Reply-To: thepeakbeyondorders@gmail.com \r\n";
			$headers .= "MIME-Version: 1.0\r\n";
			$headers .= "Content-Type: text/html; charset=UTF-8\r\n";

			$message = $text;


			mail($to, $subject, $message, $headers);
		
		return $prints;
		
		
		
	}
	
	
	
	public function check_user($name, $phone) {
		$parts = explode(" ", $name);

		if(count($parts) == 1) {
			
			$fname = Tpb_Wp_Pos_Public::theUser('first_name',$name,$phone);
			if($fname) {
				$success = $fname;
			}else {
				$lname = Tpb_Wp_Pos_Public::theUser('last_name',$name,$phone);
				if($lname) {
					$success = $lname;
				}else {
					$success = "none";
				}
			}
			echo $success;
		}else {
			
			$lastname = array_pop($parts);
			$firstname = implode(" ", $parts);
			
			$fname = Tpb_Wp_Pos_Public::theUser('first_name',$firstname,$phone);
			if($fname) {
				$success = $fname;
			}else {
				$lname = Tpb_Wp_Pos_Public::theUser('last_name',$lastname,$phone);
				if($lname) {
					$success = $lname;
				}else {
					$success = "none";
				}
			}
			return $success;
		
		}
	}
		
		
		
	public function theUser($which, $who, $phone) {
		
	
	$url = "https://i.gomjfreeway.com/elementalwellness/api/order/patient_list";
		$data = array($which=>$who);
		$data_string = json_encode($data);
		$access_token_parameters = array(
				'version' =>'8',
        'api_key'  	=>'977237471589bc2768d4be7.38775850',
        'api_id'  => '349708323584adb34ed9179.68028223',
		  'format' =>'JSON',
		  'location_nid' =>'45',
			  'data'=>$data_string
		 );
	 
	 	$curl = curl_init($url);    // we init curl by passing the url
		curl_setopt($curl, CURLOPT_HEADER, false);
	    curl_setopt($curl,CURLOPT_POST,true);   // to send a POST request
	    curl_setopt($curl,CURLOPT_POSTFIELDS,$access_token_parameters);   // indicate the data to send
	    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);   // to return the transfer as a string of the return value of curl_exec() instead of outputting it out directly.
	    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);   // to stop cURL from verifying the peer's certificate.
	    $result = curl_exec($curl);   // to perform the curl session
	    curl_close($curl);   // to close the curl session
		//$xml = new SimpleXMLElement($result);
		//echo $result;
		$pot = json_decode($result,true);
		$users = 0;
		$success =  $pot['response_details']['success'];	
		//$patients = count($pot['response_details']['patients']);

		
		
		if($success =='1') {
			
			foreach($pot['response_details']['patients'] as $user) {
			//echo $pot['response_details']['patients'][0]['phone_mobile'];
				if($phone = preg_replace('/\D+/', '', $user['phone_mobile']) || $phone = preg_replace('/\D+/', '', $user['phone_home'])) {
					$users= $user['nid'];
				}else {
					$users = false;
				}
			}
			if($users) {
				return $users;
			}else {
				return false;
			}
		}else {
			return false;
		}
		
	}	
	public function mjFreeway($id, $ordr) {
		$url = "https://i.gomjfreeway.com/elementalwellness/api/order/update_order";
		$pot='';
		$oID='';
		foreach($ordr as $o) {
			
			$order = array('patient_nid'=>$id,
			  'product_sku'=>$o['sku'],
			  'qty'=>$o['qty'],
			  'order_id'=>$oID,
			  'pricing_weight_id'=>'5',
			  'order_source'=>'Online'
			);
		
		$ord = json_encode($order);
		$access_token_parameters = array(
			  'version' =>'4',
			  'api_key'  	=>'977237471589bc2768d4be7.38775850',
				'api_id'  => '349708323584adb34ed9179.68028223',
			  'format' =>'JSON',
			  'location_nid' =>'45',
			  'data'=>$ord
			  
		 );
		
		$curl = curl_init($url);    // we init curl by passing the url
		curl_setopt($curl, CURLOPT_HEADER, false);
	    curl_setopt($curl,CURLOPT_POST,true);   // to send a POST request
	    curl_setopt($curl,CURLOPT_POSTFIELDS,$access_token_parameters);   // indicate the data to send
	    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);   // to return the transfer as a string of the return value of curl_exec() instead of outputting it out directly.
	    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);   // to stop cURL from verifying the peer's certificate.
	    $result = curl_exec($curl);   // to perform the curl session
	    curl_close($curl);   // to close the curl session
		//$xml = new SimpleXMLElement($result);
		//echo $result;
		$pot = json_decode($result,true);
		$oID= $pot['response_details']['order_id'];
		
		}
		//extract data from the post
		//$sms = Tpb_Wp_Pos_Public::sendSMS();
		
		return print_r($pot, true);
	}
	
	public function sendSMS() {
		//set POST variables
		$url = 'http://thepeakbeyond.com/development/sms.php';
		$fields = array(
			'num' => ''
		);

		//url-ify the data for the POST
		foreach($fields as $key=>$value) { $fields_string .= $key.'='.$value.'&'; }
		rtrim($fields_string, '&');

		//open connection
		$ch = curl_init();

		//set the url, number of POST vars, POST data
		curl_setopt($ch,CURLOPT_URL, $url);
		curl_setopt($ch,CURLOPT_POST, count($fields));
		curl_setopt($ch,CURLOPT_POSTFIELDS, $fields_string);
		
		//execute post
		$result = curl_exec($ch);

		//close connection
		curl_close($ch);
	}
	
}