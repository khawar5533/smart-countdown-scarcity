<?php
if (!class_exists('WBGS_SmartCountdownFront')) {
    class WBGS_SmartCountdownFront {
        public function __construct() {
          add_action('woocommerce_before_main_content', [$this,'wbgs_woocommerce_shop_sale_alert'], 5,0);
        }
        //show sale alert on shop page
        public function wbgs_woocommerce_shop_sale_alert(){ ?>
         <div class="wbgs_main_banner">
            <?php 
            $combine_data_front = [];
            $args = array(
                'post_type'      => 'product',
                'post_status'    => 'publish',
                'posts_per_page' => -1,
                'fields'         => 'ids',
            );
            $product_ids = get_posts($args);

            foreach ($product_ids as $product_id) {

             $product_data_front = get_option("wbgs_product_{$product_id}_data");
             if(is_array($product_data_front)){
               $combine_data_front[$product_id] = $product_data_front;
             }

            }
            // Sort by product ID
            ksort($combine_data_front);
            $heading = get_option('wbgs_custom_option_text');
            $heading = !empty($heading) ? $heading : '';
            if(isset($combine_data_front) && !empty($combine_data_front)){
              foreach($combine_data_front as $key => $front_data){
                $status_front = isset($front_data['status']) ? $front_data['status'] : '';
                if ( is_shop() && isset($status_front) && $status_front === 'enable' ) {
                    $product_id_front = isset($front_data['id']) ? $front_data['id'] : '';
                    $product_title =  esc_attr(get_the_title( $product_id_front  ));
                    $stock_alert = isset($front_data['stock_alert']) ? $front_data['stock_alert'] : '';
                    $end_time = isset($front_data['end_time']) ? $front_data['end_time'] : '';
                    $sale_price = get_post_meta($product_id_front, '_sale_price', true);
                    if ( ! empty( $end_time ) ) {
                        $date_format = get_option( 'date_format' );
                        $formatted_date = date_i18n( $date_format . ' H:i:s', $end_time );
                    }

                    $banner_image = isset($front_data['banner_image']) && !empty($front_data['banner_image']) ? $front_data['banner_image'] : '';
                      ?>
                      <a href="<?php echo get_the_permalink($product_id_front);?>" target="_blank">
                        <div class="wbgs_banner_container" style="background-image: url(''); background-size: cover; background-position: center; height: 300px;">
                         <!-- Left: Product Info -->    
                        <div class="wbgs_banner_inner">
                            <h3 class="countdown-title"><?php echo esc_attr($heading); ?> </h3>
                            <h4 class="product-title"><?php echo $product_title;?></h4>
                            <p class="stock-info"><?php echo esc_html_e('Remaing Stock Items :', 'smart-countdown-scarcity'); ?> <?php echo esc_attr($stock_alert);?></p>
                            <p class="sale-price"><?php echo esc_html_e('Sale Price :', 'smart-countdown-scarcity'); ?> <?php echo esc_attr($sale_price);?></p>
                          </div>
                             <!-- Center: Countdown -->
                        <div class="wbgs_banner_center">
                          <div class="wbgs_promo_banner"><h3><span class="wbgs_highlight"><?php echo esc_html_e('UP TO', 'smart-countdown-scarcity'); ?></span> <strong><?php echo esc_html_e('60% OFF', 'smart-countdown-scarcity'); ?></strong></h3></div>
                            <div id="wbgacountdown" class="wbgacountdown"></div>
                        </div>
                        <!-- Right: Product Image -->
                         <div class="wbgs_banner_right">
                            <img src="<?php echo esc_url($banner_image); ?>" alt="<?php echo $product_title;?>" />
                         </div>
                    </div>
                </a>
                  <script>
                    function startCountdown(id, targetDate) {
                        const countdownEl = document.getElementById(id);
                        const interval = setInterval(() => {
                            const now = new Date().getTime();
                            const distance = targetDate - now;

                            if (distance < 0) {
                                clearInterval(interval);
                                countdownEl.innerHTML = "EXPIRED";
                                return;
                            }

                            const days = Math.floor(distance / (1000 * 60 * 60 * 24));
                            const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                            const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
                            const seconds = Math.floor((distance % (1000 * 60)) / 1000);

                            countdownEl.innerHTML = `<div class="time-block"><div class="value">${days}</div><div class="label">Days</div></div> 
                           <div class="time-block"><div class="value">${hours}</div><div class="label">Hour</div></div>
                            <div class="time-block"><div class="value">${minutes}</div><div class="label">Minutes</div></div>
                            <div class="time-block"><div class="value">${seconds}</div><div class="label">Seconds</div></div>`;
                        }, 1000);
                    }

                    document.addEventListener("DOMContentLoaded", function() {
                        startCountdown("wbgacountdown", new Date("<?php echo esc_js($formatted_date)?>").getTime());
                    });
                </script>

            <?php
            }
                
             }

            }
            ?>

         </div>
        <?php    
        }
    }
    new WBGS_SmartCountdownFront();
}

?>