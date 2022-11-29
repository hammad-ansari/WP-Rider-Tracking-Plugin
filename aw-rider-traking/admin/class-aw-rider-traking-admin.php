<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://https://dev.rapidev.tech/
 * @since      1.0.0
 *
 * @package    Aw_Rider_Traking
 * @subpackage Aw_Rider_Traking/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Aw_Rider_Traking
 * @subpackage Aw_Rider_Traking/admin
 * @author     Abdul Wahab <admin@dev.rapidev.tech>
 */
class Aw_Rider_Traking_Admin {

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
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	/**
	 * Register the stylesheets for the admin area.
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

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/aw-rider-traking-admin.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the admin area.
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

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/aw-rider-traking-admin.js', array( 'jquery' ), $this->version, false );

	}

	public function init_functions(){

		add_rewrite_endpoint( 'assigned-orders', EP_PAGES );
		add_rewrite_endpoint( 'aw-order-traking', EP_PAGES );

		// Add new role
		$roles_set = get_option('driver_role_is_set');
    if(!$roles_set){
        add_role('Driver', 'Driver', array(
            'read' => true, // True allows that capability, False specifically removes it.
            'edit_posts' => true,
            'delete_posts' => true,
            'upload_files' => true
        ));
        update_option('driver_role_is_set',true);
    }



	}

	public function add_custom_field_to_orders_page($order){

		$args = array(
		    'role'    => 'Driver',
		    'order'   => 'ASC'
		);
		$users = get_users( $args );
		if(!empty($users)){

				$get_rider = $order->get_meta( '_select_aw_driver' );
				$options[''] = __( 'Select a rider', 'woocommerce'); // default value

				foreach ($users as $key => $user) {

						$user_id = $user->ID;
						$user_name = $user->user_firstname;

						$options[$user_id] = $user_name;


				}


				echo '<div class="options_group">';
				woocommerce_wp_select( array(
						 'id'      => '_select_aw_driver',
						 'label'   => __( 'Assign this order to a driver', 'woocommerce' ),
						 'options' => $options, //this is where I am having trouble
						 'value'   => $get_rider,
				 ) );

				 echo '</div>';
		}



	}

	public function save_custom_field( $order_id  ){
	    update_post_meta( $order_id, '_select_aw_driver', wc_clean( $_POST[ '_select_aw_driver' ] ) );
	}

	function user_has_role($user_id, $role_name)
	{
	    $user_meta = get_userdata($user_id);
	    $user_roles = $user_meta->roles;
	    return in_array($role_name, $user_roles);
	}

	public function add_rider_tab_myaccount_page( $menu_links ){

		$current_user_id = get_current_user_id();

		$user_is_driver = $this->user_has_role($current_user_id, 'Driver');
		$user_is_customer = $this->user_has_role($current_user_id, 'customer');

		if(!empty($user_is_driver)){

			$menu_links = array_slice( $menu_links, 0, 5, true )
			+ array( 'assigned-orders' => 'Assigned Orders' )
			+ array_slice( $menu_links, 5, NULL, true );
		}

// 		if(!empty($user_is_customer)){

// 			$menu_links = array_slice( $menu_links, 0, 5, true )
// 			+ array( 'aw-order-traking' => 'Order Traking' )
// 			+ array_slice( $menu_links, 6, NULL, true );
// 		}

		return $menu_links;

	}
	public function order_traking_page_content() {

		if(!empty(@$_GET['order_id'])){

			$driver_id = get_post_meta($_GET['order_id'], '_select_aw_driver', true);
			$driver_lat_lng = json_decode(get_user_meta($driver_id, 'current_lat_lng', true));
			$driver_lat = $driver_lat_lng->latitude;
			$driver_lng = $driver_lat_lng->longitude;
			
			$order = wc_get_order( $_GET['order_id'] );
			
			$customer_shipping_address_1  = $order->get_shipping_address_1();
			$customer_shipping_address_2  = $order->get_shipping_address_2();
			
			?>
			
			<div>
				<h3>
					Total Distance: <b><span id="estimated-distance"></span></b>
				</h3>
				<h3>
					Estimated Time: <b><span id="estimated-time"></span></b>
				</h3>
			</div>
			<div id="map" style="height:300px">

			</div>

			<?php
		}else{

		}

		global $wp;
			$request = explode( '/', $wp->request );

		?>
		
		<script type="text/javascript">

			var customerCurrentLat = '';
			var customerCurrentLng = '';
			var originLatLng = '';
			var destinationLatLng = '';
			<?php
			if($request[1] == 'aw-order-traking'){
				?>
			
			<?php
			if(!empty($customer_shipping_address_1)){
					?>
						
					function initMap() {
						directionsService = new google.maps.DirectionsService();
						directionsRenderer = new google.maps.DirectionsRenderer();
						const geocoder = new google.maps.Geocoder();
						const service = new google.maps.DistanceMatrixService();


					  const map = new google.maps.Map(document.getElementById("map"), {
						zoom: 7,
						center: { lat: 32.0549407, lng: 72.6268209 },
					  });

					  directionsRenderer.setMap(map);

						// build request
							const origin1 = "<?= $customer_shipping_address_1 ?>";
							const destinationA = { lat: <?= $driver_lat ?>, lng: <?= $driver_lng ?> };
							const request = {
								origins: [origin1],
								destinations: [destinationA],
								travelMode: google.maps.TravelMode.DRIVING,
								unitSystem: google.maps.UnitSystem.METRIC,
								avoidHighways: false,
								avoidTolls: false,
							};

							// get distance matrix response
							  service.getDistanceMatrix(request).then((response) => {
								// put response
								var estimated_time = response.rows[0]['elements'][0]['duration']['text'];
								var estimate_distance = response.rows[0]['elements'][0]['distance']['text'];

								document.getElementById("estimated-distance").innerText = estimate_distance;
								document.getElementById("estimated-time").innerText = estimated_time;
							});

						calcRoute();

					}


				function calcRoute() {

// 					var originLatLng = new google.maps.LatLng(customerCurrentLat, customerCurrentLng);
					var destinationLatLng = new google.maps.LatLng(<?= $driver_lat ?>, <?= $driver_lng ?>);
// 					console.log(originLatLng);
					  var request = {
						  origin: '<?= $customer_shipping_address_1 ?>',
						  destination: destinationLatLng,
						  travelMode: google.maps.TravelMode.DRIVING
					  };
					  directionsService.route(request, function(response, status) {
						if (status == 'OK') {
						  directionsRenderer.setDirections(response);
						}else{
							console.log(status);
						}
					  });
					}
			
					window.initMap = initMap;
			
			
						
			<?php	}else{ ?>
			
					
				function getLocation(){
					if (navigator.geolocation){
						navigator.geolocation.getCurrentPosition(showPosition,showError);
					}
					else{
						console.log("Geolocation is not supported by this browser.");
					}
				}

				function showPosition(position){
					customerCurrentLat = position.coords.latitude;
					customerCurrentLng = position.coords.longitude;
					console.log('lat is', customerCurrentLat);
					console.log('lon is', customerCurrentLng);
				}

				function showError(error){
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
			
				function initMap() {
						directionsService = new google.maps.DirectionsService();
						directionsRenderer = new google.maps.DirectionsRenderer();
						const geocoder = new google.maps.Geocoder();
						const service = new google.maps.DistanceMatrixService();


					  const map = new google.maps.Map(document.getElementById("map"), {
						zoom: 7,
						center: { lat: 32.0549407, lng: 72.6268209 },
					  });

					  directionsRenderer.setMap(map);

						// build request
							const origin1 = {lat:customerCurrentLat, lng:customerCurrentLng};
							const destinationA = { lat: <?= $driver_lat ?>, lng: <?= $driver_lng ?> };
							const request = {
								origins: [origin1],
								destinations: [destinationA],
								travelMode: google.maps.TravelMode.DRIVING,
								unitSystem: google.maps.UnitSystem.METRIC,
								avoidHighways: false,
								avoidTolls: false,
							};

							// get distance matrix response
							  service.getDistanceMatrix(request).then((response) => {
								// put response
								var estimated_time = response.rows[0]['elements'][0]['duration']['text'];
								var estimate_distance = response.rows[0]['elements'][0]['distance']['text'];

								document.getElementById("estimated-distance").innerText = estimate_distance;
								document.getElementById("estimated-time").innerText = estimated_time;
							});

						calcRoute();

					}


				function calcRoute() {

					var originLatLng = new google.maps.LatLng(customerCurrentLat, customerCurrentLng);
					var destinationLatLng = new google.maps.LatLng(<?= $driver_lat ?>, <?= $driver_lng ?>);
// 					console.log(originLatLng);
					  var request = {
						  origin: originLatLng,
						  destination: destinationLatLng,
						  travelMode: google.maps.TravelMode.DRIVING
					  };
					  directionsService.route(request, function(response, status) {
						if (status == 'OK') {
						  directionsRenderer.setDirections(response);
						}else{
							console.log(status);
						}
					  });
					}
			
					window.initMap = initMap;
					
					getLocation();
			
				<?php }
				
			}
			?>

			


		</script>
		<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBdoQyTxBXPm7Tuoc8-bkNdKMVquxyrFi0&callback=initMap&libraries=places&v=weekly&map_ids=8d193001f940fde3" async></script>
		<?php
	}


	public function my_account_rider_page_content() {

		// of course you can print dynamic content here, one of the most useful functions here is get_current_user_id()

		$current_user_id = get_current_user_id();

		$get_order_id = @$_GET['order_id'];
		if(!empty($get_order_id)){


			$order = wc_get_order( $get_order_id ); // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited

				if ( ! $order ) {
					return;
				}

				$order_items           = $order->get_items( apply_filters( 'woocommerce_purchase_order_item_types', 'line_item' ) );
				$show_purchase_note    = $order->has_status( apply_filters( 'woocommerce_purchase_note_order_statuses', array( 'completed', 'processing' ) ) );
				$show_customer_details = is_user_logged_in() && $order->get_user_id() === get_current_user_id();
				$downloads             = $order->get_downloadable_items();
				$show_downloads        = $order->has_downloadable_item() && $order->is_download_permitted();

				if ( $show_downloads ) {
					wc_get_template(
						'order/order-downloads.php',
						array(
							'downloads'  => $downloads,
							'show_title' => true,
						)
					);
				}
				?>
				<section class="woocommerce-order-details">
					<?php do_action( 'woocommerce_order_details_before_order_table', $order ); ?>

					<h2 class="woocommerce-order-details__title"><?php esc_html_e( 'Order details', 'woocommerce' ); ?></h2>

					<table class="woocommerce-table woocommerce-table--order-details shop_table order_details">

						<thead>
							<tr>
								<th class="woocommerce-table__product-name product-name"><?php esc_html_e( 'Product', 'woocommerce' ); ?></th>
								<th class="woocommerce-table__product-table product-total"><?php esc_html_e( 'Total', 'woocommerce' ); ?></th>
							</tr>
						</thead>

						<tbody>
							<?php
							do_action( 'woocommerce_order_details_before_order_table_items', $order );

							foreach ( $order_items as $item_id => $item ) {
								$product = $item->get_product();

								wc_get_template(
									'order/order-details-item.php',
									array(
										'order'              => $order,
										'item_id'            => $item_id,
										'item'               => $item,
										'show_purchase_note' => $show_purchase_note,
										'purchase_note'      => $product ? $product->get_purchase_note() : '',
										'product'            => $product,
									)
								);
							}

							do_action( 'woocommerce_order_details_after_order_table_items', $order );
							?>
						</tbody>

						<tfoot>
							<?php
							foreach ( $order->get_order_item_totals() as $key => $total ) {
								?>
									<tr>
										<th scope="row"><?php echo esc_html( $total['label'] ); ?></th>
										<td><?php echo ( 'payment_method' === $key ) ? esc_html( $total['value'] ) : wp_kses_post( $total['value'] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></td>
									</tr>
									<?php
							}
							?>
							<?php if ( $order->get_customer_note() ) : ?>
								<tr>
									<th><?php esc_html_e( 'Note:', 'woocommerce' ); ?></th>
									<td><?php echo wp_kses_post( nl2br( wptexturize( $order->get_customer_note() ) ) ); ?></td>
								</tr>
							<?php endif; ?>
						</tfoot>
					</table>

					<?php do_action( 'woocommerce_order_details_after_order_table', $order ); ?>
				</section>

				<?php
				/**
				 * Action hook fired after the order details.
				 *
				 * @since 4.4.0
				 * @param WC_Order $order Order data.
				 */
				do_action( 'woocommerce_after_order_details', $order );

				wc_get_template( 'order/order-details-customer.php', array( 'order' => $order ) );


		}else{

		$args = array( // Accepts a string: one of 'pending', 'processing', 'on-hold', 'completed', 'refunded, 'failed', 'cancelled', or a custom order status.
		    'meta_key'      => '_select_aw_driver',
		    'meta_value'    => $current_user_id,
		    'meta_compare'  => '=',
		    'return'        => 'objects'
		);
		?>

		<a href="javascript:void(0)" class="button" id="aw-start-on-route-btn"><span>On Route</span></a>
		<span id="show-current-location"></span>
		<?php
		$orders = wc_get_orders( $args );
		if(!empty($orders)){
		?>
		<table class="shop_table shop_table_responsive my_account_orders">

				<thead>
					<tr>

							<th class="">
								<span class="nobr">Order</span>
							</th>
							<th class="">
								<span class="nobr">Date</span>
							</th>
							<th class="">
								<span class="nobr">Status</span>
							</th>
							<th class="">
								<span class="nobr">Total</span>
							</th>
							<th class="">
								<span class="nobr">Action</span>
							</th>

					</tr>
				</thead>

				<tbody>
					<?php foreach ($orders as $key => $order) {
						$item_count = $order->get_item_count();
						?>
						<tr class="order">
							<td><a href="<?php echo esc_url( $order->get_view_order_url() ); ?>">
								<?php echo _x( '#', 'hash before order number', 'woocommerce' ) . $order->get_order_number(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
							</a></td>
							<td><?php echo esc_html( wc_format_datetime( $order->get_date_created() ) ); ?></td>
							<td><?php echo esc_html( wc_get_order_status_name( $order->get_status() ) ); ?></td>
							<td><?php
							/* translators: 1: formatted order total 2: total order items */
							printf( _n( '%1$s for %2$s item', '%1$s for %2$s items', $item_count, 'woocommerce' ), $order->get_formatted_order_total(), $item_count ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
							?></td>
							<td>
<!-- 								<a href="#" class="button">Start Delivering</a><br><br> -->
					<?php  if ( $order->has_status('completed') ) {

							}else{
								?>
								<a href="javascript:void(0)" class="" id="aw-order-deliver-btn" order-id="<?= $order->get_id() ?>">
									Delivered
								</a>

					<?php } ?>

								<a href="<?= get_site_url() ?>/my-account/assigned-orders/?order_id=<?= $order->get_id() ?>" class="button" id="aw-order-delivered-btn">
									<span>View</span>
								</a>
							</td>
						</tr>
				<?php	} ?>

				</tbody>
			</table>
		<?php
		}


// 		$current_user = wp_get_current_user();

// 		echo '<pre>';
// 		print_r($user->roles);
// 		echo '</pre>';
		}

	}

	public function add_my_account_my_orders_order_traking( $actions, $order ) {

		$action_slug = 'aw-order-traking';

		$actions[$action_slug] = array(
			'url'  => get_site_url().'/my-account/aw-order-traking/?track_order=true&order_id='.$order->get_id(),
			'name' => 'Track Order',
		);
		return $actions;
	}


	public function add_filter(){
				global $wpdb, $table_prefix;


         $post_type = (isset($_GET['post_type'])) ? $_GET['post_type'] : 'post';

         //only add filter to post type you want
         if ($post_type == 'shop_order'){
             //query database to get a list of years for the specific post type:

							$args = array(
									'role'    => 'Driver',
									'order'   => 'ASC'
								);
							 $users = get_users( $args );
// 			 					echo '<pre>';
// 			 					print_r($users);
// 			 					echo '</pre>';
							 if(!empty($users)){
								 ?>
								 <select id="aw-users" name="users">
		  							<option value="">Select Driver</option>

									  <?php
									  foreach ($users as $user) {  ?>
											<option value="<?php echo $user->ID; ?>"><?php echo $user->user_firstname; ?></option>

									<?php } ?>
								  </select>
								<input type="hidden" name="driver_name" value="" id="driver_name"/>
								<script>
									jQuery(document).ready(function($){

										$('#aw-users').change(function(){
											$('#driver_name').val($(this).find('option:selected').val());
										});

									});
								</script>
		 						 <input type="submit" class="button" style="float:left;" name="assign_driver" value="Assign Driver">
								<style>
									.user_id.column-user_id,#user_id{
										text-align: center;
									}
								 </style>
						<?php
								 
							 }


							
         }

		}

		public function admin_init(){
			
			if(isset($_GET['assign_driver'])){
				
				$chef_name = $_GET['driver_name'];

				$orders_for_chef = [];

				foreach($_GET['post'] as $post){

					$order = wc_get_order($post);

					if($order->get_status() != 'processing'){
						continue;
					}
					update_post_meta($post, '_select_aw_driver', $chef_name);

				}
				

				wp_redirect(admin_url('edit.php?post_type=shop_order'));
			}

		}

		public function adding_columns_to_shop_order(	$columns){
			$columns['user_id'] = __( 'Driver Name' );
			return $columns;
		}

		public function smashing_shop_order_column( $column, $post_id  ){
			if ( $column == 'user_id' ) {
    		echo	'<b>'.get_post_meta($post_id, '_select_aw_driver',true) . '</b>';
  		}
		}



}
