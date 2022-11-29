<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://https://dev.rapidev.tech/
 * @since      1.0.0
 *
 * @package    Aw_Rider_Traking
 * @subpackage Aw_Rider_Traking/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    Aw_Rider_Traking
 * @subpackage Aw_Rider_Traking/public
 * @author     Abdul Wahab <admin@dev.rapidev.tech>
 */
class Aw_Rider_Traking_Public {

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
		 * defined in Aw_Rider_Traking_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Aw_Rider_Traking_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/aw-rider-traking-public.css', array(), $this->version, 'all' );

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
		 * defined in Aw_Rider_Traking_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Aw_Rider_Traking_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/aw-rider-traking-public.js', array( 'jquery' ), $this->version, false );

	}

	public function wp_footer_functions(){

		global $wp;
   		$request = explode( '/', $wp->request );

		if(@$request[1] == 'assigned-orders'){
		?>
		<script>

			var x=document.getElementById("show-current-location");
			$ = jQuery;
			$( document ).ready(function() {

				
				$(document).on("click","#aw-start-on-route-btn",async function(e){
					e.preventDefault();
					await getLocation();
				});
				

				async function getLocation(){
					var loca = await navigator.geolocation;
					if (loca){
						navigator.geolocation.getCurrentPosition(showPosition,showError);
					}
					else{
						//var x=document.getElementById("show-country");
						//x.innerHTML="Geolocation is not supported by this browser.";
						console.log("Geolocation is not supported by this browser.");
					}

					console.log(navigator.geolocation.code);
				}

				function showPosition(position){
					lat= position.coords.latitude;
					lon= position.coords.longitude;
					console.log('lat is', lat);
					console.log('lon is', lon);

					$.ajax({
						type : "get",
						url :  "<?php echo admin_url('admin-ajax.php'); ?>",
						data : {lat:lat, lon:lon, action:'save_lat_lng_to_user'},
						success:function(response){
							
							alert('Now customer can see your current location');
// 							console.log(response)

						},
						error:function(err){
							console.log(err)
						}

					})

				}

				function showError(error){
					console.log(error.code);
					switch(error.code){
						case error.PERMISSION_DENIED:
							alert("User denied the request for Geolocation.");
							break;
						case error.POSITION_UNAVAILABLE:
							alert("Location information is unavailable.");
							break;
						case error.TIMEOUT:
							alert("The request to get user location timed out.");
							break;
						case error.UNKNOWN_ERROR:
							alert("An unknown error occurred.");
							break;
					}
				}



				// 	Update current location
			//
				var order_id = 0;
				$(document).on("click","#aw-order-deliver-btn",function(e) {
					e.preventDefault();

					var order_id = $(this).attr('order-id');
					
					$(this).html('Processing...');
					$(this).css({"pointer-events": "none", "cursor": "default", "color": "#b6b6b6"});
					console.log(order_id);

					getCurrentLocation();

					function getCurrentLocation(){
						if (navigator.geolocation){
							navigator.geolocation.getCurrentPosition(showCurrentPosition,showCurrentLocationError);
						}
						else{
							console.log("Geolocation is not supported by this browser.");
						}

						console.log(navigator.geolocation.code);
					}

					function showCurrentPosition(position){
						lat=position.coords.latitude;
						lon=position.coords.longitude;
						console.log('lat is', lat);
						console.log('lon is', lon);

						$.ajax({
							type : "get",
							url :  "<?php echo admin_url('admin-ajax.php'); ?>",
							data : {lat:lat, lon:lon, order_id:order_id, action:'update_lat_lng_to_user'},
							success:function(response){
								window.location.reload();
								// $('#aw-order-deliver-btn').html('Delivering');
							},
							error:function(err){
								console.log(err)
							}

						})

					}

					function showCurrentLocationError(error){
						console.log(error.code);
						switch(error.code){
							case error.PERMISSION_DENIED:
								x.innerHTML="User denied the request for Geolocation."
								break;
							case error.POSITION_UNAVAILABLE:
								x.innerHTML="Location information is unavailable."
								break;
							case error.TIMEOUT:
								x.innerHTML="The request to get user location timed out."
								break;
							case error.UNKNOWN_ERROR:
								x.innerHTML="An unknown error occurred."
								break;
						}
					}


				});




			});

		</script>

		<script async defer src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBdoQyTxBXPm7Tuoc8-bkNdKMVquxyrFi0"></script>
		<?php
		}
	}


	function save_lat_lng_to_user(){

		$current_lat_long = array();

		$current_lat_long['latitude'] = $_GET['lat'];
		$current_lat_long['longitude'] = $_GET['lon'];

		$current_lat_long = json_encode($current_lat_long);

		$current_user_id = get_current_user_id();

		update_user_meta( $current_user_id, 'current_lat_lng', $current_lat_long );

		print_r($current_lat_long);

		exit();

	}


	function update_lat_lng_to_user(){

		$current_lat_long = array();

		$current_lat_long['latitude'] = $_GET['lat'];
		$current_lat_long['longitude'] = $_GET['lon'];
		$order_id = $_GET['order_id'];
		$_order = new WC_Order( $order_id );
		$_order->update_status('completed');

		$current_lat_long = json_encode($current_lat_long);

		$current_user_id = get_current_user_id();

		update_user_meta( $current_user_id, 'current_lat_lng', $current_lat_long );

		print_r($current_lat_long);

		exit();

	}



}
