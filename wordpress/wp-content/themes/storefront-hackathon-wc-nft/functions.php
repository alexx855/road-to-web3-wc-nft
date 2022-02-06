<?php

/**
 * Storefront automatically loads the core CSS even if using a child theme as it is more efficient
 * than @importing it in the child theme style.css file.
 *
 * Uncomment the line below if you'd like to disable the Storefront Core CSS.
 *
 * If you don't plan to dequeue the Storefront Core CSS you can remove the subsequent line and as well
 * as the sf_child_theme_dequeue_style() function declaration.
 */
//add_action( 'wp_enqueue_scripts', 'sf_child_theme_dequeue_style', 999 );

/**
 * Dequeue the Storefront Parent theme core CSS
 */
function sf_child_theme_dequeue_style()
{
    wp_dequeue_style('storefront-style');
    wp_dequeue_style('storefront-woocommerce-style');
}

/**
 * Note: DO NOT! alter or remove the code above this text and only add your custom PHP functions below this text.
 */

add_filter('woocommerce_checkout_fields', '__return_null');

// force redirect to checkout when cart is not empty
add_filter('login_redirect', function ($redirect_to, $request, $user) {

    // Checkout 
    if (!WC()->cart->is_empty()) {
        $redirect = get_permalink(wc_get_page_id('checkout'));
    } else {
        // Get the "My account" url
        $redirect = get_permalink(wc_get_page_id('my-account'));
    }

    return $redirect;
}, 10, 3);

function random_string($length = 10)
{
    return substr(str_shuffle(str_repeat($x = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ', ceil($length / strlen($x)))), 1, $length);
}

function write_log($log_msg)
{
    $log_filename = WP_CONTENT_DIR;
    if (!file_exists($log_filename)) {
        mkdir($log_filename, 0777, true);
    }
    $log_file_data = $log_filename . '/debug.log';
    file_put_contents($log_file_data, $log_msg . "\n", FILE_APPEND);
}

// change how we get customer downloads, get user nfts usin nfpt port api
add_filter('woocommerce_customer_get_downloadable_products', function ($downloads) {

    return $downloads;
}, 10, 1);

// override default woocommerce order downloads values to add some nfts specific values
add_filter('woocommerce_order_get_downloadable_items', function ($downloads, $order) {
    // this came from wordpress/wp-content/plugins/woocommerce/includes/class-wc-order.php
    $nfts = array();

    foreach ($order->get_items() as $item) {
        if (!is_object($item)) {
            continue;
        }

        // Check item refunds.
        $refunded_qty = abs($order->get_qty_refunded_for_item($item->get_id()));
        if ($refunded_qty && $item->get_quantity() === $refunded_qty) {
            continue;
        }

        if ($item->is_type('line_item')) {
            $item_downloads = $item->get_item_downloads();
            $product        = $item->get_product();
            if ($product && $item_downloads) {
                foreach ($item_downloads as $file) {

                    // custom nft meta data
                    $nft_transaction_external_url = $item->get_meta('_wc_nft_transaction_external_url');
                    $nft_contract_address = $item->get_meta('_wc_nft_contract_address');
                    $nft_transaction_hash = $item->get_meta('_wc_nft_transaction_hash');
                    $nft_token_id = $item->get_meta('_wc_nft_token_id');
                    $nft_chain = $item->get_meta('_wc_nft_chain');

                    $nft_mint_to_address = $item->get_meta('_wc_nft_mint_to_address');

                    $nfts[] = array(
                        'nft_mint_to_address'          => $nft_mint_to_address,
                        'nft_transaction_external_url' => $nft_transaction_external_url,
                        'nft_contract_address'         => $nft_contract_address,
                        'nft_transaction_hash'         => $nft_transaction_hash,
                        'nft_chain'                    => $nft_chain,
                        'nft_token_id'                 => $nft_token_id,
                        'download_url'        => $file['download_url'],
                        'download_id'         => $file['id'],
                        'product_id'          => $product->get_id(),
                        'product_name'        => $product->get_name(),
                        'product_url'         => $product->is_visible() ? $product->get_permalink() : '', // Since 3.3.0.
                        'download_name'       => $file['name'],
                        'order_id'            => $order->get_id(),
                        'order_key'           => $order->get_order_key(),
                        'downloads_remaining' => $file['downloads_remaining'],
                        'access_expires'      => $file['access_expires'],
                        'file'                => array(
                            'name' => $file['name'],
                            'file' => $file['file'],
                        ),
                    );
                }
            }
        }
    }

    return $nfts;
}, 30, 2);

// theme specific changes
add_action('init', 'replace_storefront_primary_navigation');
function replace_storefront_primary_navigation()
{
    remove_action('storefront_sidebar', 'storefront_get_sidebar', 10);

    remove_action('storefront_before_content', 'woocommerce_breadcrumb', 10);

    remove_action('storefront_header', 'storefront_primary_navigation', 50);
    remove_action('storefront_header', 'storefront_header_cart', 60);
    remove_action('storefront_header', 'storefront_product_search', 40);

    // add user avatar to header
    add_action('storefront_header', function () { ?>

        <div class="user-item" style="position: absolute; top: 10px; right: 10px; z-index: 999;">
            <a href="<?php echo get_permalink(get_option('woocommerce_myaccount_page_id')); ?>" style=" border-radius: 50%;
                        margin-right: 10px;
                        overflow: hidden;
                        display: block;
                        ">

                <?php

                if (is_user_logged_in()) :
                    $avatar = get_avatar(get_current_user_id(), 32);
                    echo $avatar;
                else :  ?>

                    <span style="
                        background-color: red;
                        width: 32px;
                        height: 32px;
                        display: block;
                    "></span>

                <?php endif; ?>
            </a>
        </div>

    <?php
    }, 40);

    remove_action('storefront_footer', 'storefront_handheld_footer_bar', 999);
    remove_post_type_support('product', 'comments');
}

function order_has_nft($order)
{
    $items = $order->get_items(apply_filters('woocommerce_purchase_order_item_types', 'line_item'));
    foreach ($items as $item) {
        if ($item->is_type('line_item')) {
            $product = $item->get_product();

            if ($product && $item->get_meta('_wc_nft_mint_to_address')) {
                return true;
            }
        }
    }
    return false;
}

// WooCommerce is going to change the order status either to processing or completed, which also can be filtered with woocommerce_payment_complete_order_status.
// add_action( 'woocommerce_payment_complete_order_status_processing', 'rudr_complete_for_status' );
// add_action( 'woocommerce_payment_complete_order_status_completed', 'rudr_complete_for_status' );

add_action('woocommerce_checkout_order_processed', function ($order_id, $posted_data, $order) {
    // add_action('woocommerce_before_thankyou', function ($order_id) use ($nftport_chain, $nftport_api_key, $nftport_api_url) {
    // $order = wc_get_order($order_id);

    $nftport_api_key = get_option('wc_nft_nftport_api_key');
    $nftport_api_url = get_option('wc_nft_nftport_api_url', 'https://api.nftport.xyz/v0');
    $nftport_chain = get_option('wc_nft_nftport_chain', 'polygon');

    if (empty($nftport_api_key) || empty($nftport_api_url)) {
        return;
    }

    if (order_has_nft($order)) {

        // ? group all in one mint action, remove one by one restrictions
        $items = $order->get_items(apply_filters('woocommerce_purchase_order_item_types', 'line_item'));
        foreach ($items as $item) {
            // ? save order meta as nft metadata

            $product        = $item->get_product();

            $mint_to_address = $item->get_meta('_wc_nft_mint_to_address');

            if (!$mint_to_address) {
                continue;
            }

            write_log('$mint_to_address: ' . $mint_to_address);

            // $product_full_description = $product->get_description();
            $product_short_description = $product->get_short_description();
            $name = $product->get_name();
            $description = $product_short_description;

            $item_downloads = $item->get_item_downloads();
            $product        = $item->get_product();

            // set default image wc placeholder
            $file_url =  wc_placeholder_img_src('woocommerce_single');
            if ($product && $item_downloads) {
                // foreach ($item_downloads as $file) {
                // get first element array php
                $file = array_shift($item_downloads);
                if (!empty($file['file'])) {
                    $file_url =  $file['file'];
                }
                // }
            }

            write_log('$file_url: ' . $file_url);

            $curl = curl_init();

            curl_setopt_array($curl, array(
                CURLOPT_URL => $nftport_api_url . '/mints/easy/urls',
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'POST',
                CURLOPT_POSTFIELDS => '{
                            "chain": "' . $nftport_chain  . '",
                            "name": "' . $name . '",
                            "description": "' . $description . '",
                            "file_url": "' . $file_url . '",
                            "mint_to_address": "' . $mint_to_address . '"
                        }',
                CURLOPT_HTTPHEADER => array(
                    'Content-Type: application/json',
                    'Authorization: ' . $nftport_api_key . ''
                ),
            ));

            $response = curl_exec($curl);

            // Check HTTP status code
            if (!curl_errno($curl)) {
                switch ($http_code = curl_getinfo($curl, CURLINFO_HTTP_CODE)) {
                    case 200:  # OK
                        $response = json_decode($response, true);
                        break;
                    default:
                        write_log('Unexpected HTTP code: ', $http_code, "\n");
                }
            }

            curl_close($curl);

            if (!isset($response['response']) || $response['response'] !== 'OK') {

                // echo '<pre>';
                // print_r($response);
                // echo '</pre>';

                write_log('Error minting NFT');

                // ? re try minting if fails, trigger it with status change
                if ($order) {
                    // $order->set_status('processing');
                    $order->update_status('processing', 'Error minting NFT', false);
                    if (!empty($response['error'])) {
                        $order->add_order_note(
                            'Error minting NFT: ' . $response['error']
                        );
                    }
                    // $order->save();
                }
            } else {

                // echo '<pre>';
                // print_r($response);
                // echo '</pre>';

                // ?  add order note
                $order->add_order_note(
                    'NFT minted: ' . $response['transaction_external_url']
                );

                // save order item meta data
                $item->add_meta_data('_wc_nft_transaction_external_url', $response['transaction_external_url']);
                $item->add_meta_data('_wc_nft_contract_address', $response['contract_address']);
                $item->add_meta_data('_wc_nft_transaction_hash', $response['transaction_hash']);
                $item->add_meta_data('_wc_nft_chain', $response['chain']);
                // token id not present in response for now, not mited yet
                $item->save();
            }
        }
    }
}, 10, 3);

add_filter('woocommerce_account_downloads_columns', 'add_account_downloads_column', 10, 1);
function add_account_downloads_column($columns)
{
    global $wp;

    $newColumns = array();

    unset($columns['download-remaining']);
    unset($columns['download-expires']);
    unset($columns['download-file']);

    $newColumns['nft-order-column'] = __('Order', 'hackathon-wc-nft');
    $newColumns['nft-tx-column'] = __('TX', 'hackathon-wc-nft');
    // $newColumns['nft-chain-column'] = __('Chain', 'hackathon-wc-nft');

    // If in My account NFTs dashboard page, show trasfer action and hide wc related columns
    $request = explode('/', $wp->request);
    if (end($request) == 'downloads' && is_account_page()) {
        unset($columns['download-product']);
        unset($newColumns['nft-tx-column']);
        unset($newColumns['nft-order-column']);
        $newColumns['nft-image-column'] = __('Image', 'hackathon-wc-nft');
        // $newColumns['nft-token-column'] = __('Token', 'hackathon-wc-nft');
        $newColumns['nft-name-column'] = __('Name', 'hackathon-wc-nft');
        $newColumns['nft-description-column'] = __('Description', 'hackathon-wc-nft');
        $newColumns['nft-transfer-column'] = __('Transfer', 'hackathon-wc-nft');
    }
    // $newColumns['nft-transfer-column'] = __('Transfer', 'hackathon-wc-nft');

    // $columns['download-file'] = __( 'Download a copy', 'hackathon-wc-nft' );
    $newColumns = array_merge($newColumns, $columns);

    return $newColumns;
}

add_filter('woocommerce_account_menu_items', function ($items) {
    $items['downloads'] = _('NFTs', 'hackathon-wc-nft');
    return $items;
});

// TODO: load moralis and trigger transfer action with sdk
add_action('woocommerce_account_downloads_column_nft-order-column', function ($item) {
    if ($item && !empty($item['order_id'])) {
        $order = wc_get_order($item['order_id']);

        if ($order) {
            echo '<a href="' . esc_url($order->get_view_order_url()) . '">' .
                esc_html(_x('#', 'hash before order number', 'woocommerce') . $order->get_order_number()) .
                '</a>';
            return true;
        }
    }

    echo 'not found';
});

// TODO: load moralis and trigger transfer action with sdk
add_action('woocommerce_account_downloads_column_nft-transfer-column', function ($item) {

    if (!empty($item['nft_contract_address']) && !empty($item['nft_token_id'])) {
        echo '<a href="javascript:void(0);" data-nft-token-id="' . $item['nft_token_id'] . '" data-nft-contract-adress="' . $item['nft_contract_address'] . '" class="btn-transfer-nft woocommerce-MyAccount-btn-transfer-nft button alt">' . _('Transfer', 'hackathon-wc-nft') . '</a>';
    } else {
        // ? check for order status here?
        echo 'not minted yet';
    }
});

add_action('woocommerce_account_downloads_column_nft-token-column', function ($item) {
    if ($item && !empty($item['nft_contract_address'])) {
        echo $item['nft_token_id'];
    } else {
        echo 'not found';
    }
});

add_action('woocommerce_account_downloads_column_nft-tx-column', function ($item) {
    if ($item && !empty($item['nft_transaction_hash'])) {
        echo '<a href="' . esc_url($item['nft_transaction_external_url']) . '" target="_blank">' . esc_html($item['nft_transaction_hash']) . '</a>';
    } else {
        echo 'no transaction hash';
    }
});

// change how we get downloads, get user nfts usin nfpt port api
add_filter('woocommerce_customer_get_downloadable_products', function ($downloads) {

    $nftport_api_key = get_option('wc_nft_nftport_api_key');
    $nftport_api_url = get_option('wc_nft_nftport_api_url', 'https://api.nftport.xyz/v0');
    $nftport_chain = get_option('wc_nft_nftport_chain', 'polygon');

    if (empty($nftport_api_key) || empty($nftport_api_url)) {
        return;
    }

    // create list array from user $user_address nfts 
    $user = wp_get_current_user();
    $user_address = $user->user_login;

    // TODO: add pagination
    $page_number = 1;
    $page_size = 50;
    $include = 'metadata';
    $url = $nftport_api_url . '/accounts/' . $user_address . '?chain=' . $nftport_chain . '&page_number=' . $page_number . '&page_size=' . $page_size . '&include=' . $include . '';

    $curl = curl_init();
    curl_setopt_array($curl, array(
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'GET',
        CURLOPT_HTTPHEADER => array(
            'Authorization: ' . $nftport_api_key . ''
        ),
    ));

    $response = curl_exec($curl);

    // Check HTTP status code
    if (!curl_errno($curl)) {
        switch ($http_code = curl_getinfo($curl, CURLINFO_HTTP_CODE)) {
            case 200:  # OK
                $response = json_decode($response, true);
                break;
            default:
                write_log('Unexpected HTTP code: ', $http_code, "\n");
        }
    }

    curl_close($curl);

    // echo '<pre>';
    // print_r($response);
    // echo '</pre>';

    $nfts = array();

    if ($response['response'] && $response['response'] === 'OK') {

        if (!empty($response['total'])) {
            foreach ($response['nfts'] as $nft) {
                // TODO: run moralis sync to update order item meta nft_token_id value
                $nfts[] = array(
                    'nft_contract_address' => $nft['contract_address'],
                    'nft_token_id' => $nft['token_id'],
                    'nft_name' => $nft['name'],
                    'nft_description' => $nft['description'],
                    'nft_file_url' => $nft['file_url'],
                    'nft_cached_file_url' => $nft['cached_file_url'],
                    'nft_creator_address' => $nft['creator_address'],
                    'nft_metadata' => $nft['metadata'],
                    'nft_metadata_url' => $nft['metadata_url'],
                );
            }
        }
    }

    return $nfts;
}, 10, 1);

// custom my account nfts table
remove_action('woocommerce_available_downloads', 'woocommerce_order_downloads_table', 10);
add_action('woocommerce_available_downloads', function ($downloads) {
    if (!$downloads) {
        return;
    }
    wc_get_template(
        'order/order-downloads.php',
        array(
            'downloads'  => $downloads,
            'show_title' => true,
        )
    );
}, 10);

add_action('woocommerce_account_downloads_column_nft-name-column', function ($item) {
    if ($item && !empty($item['nft_name'])) {
        echo esc_html($item['nft_name']);
    } else {
        echo 'no name';
    }
});

add_action('woocommerce_account_downloads_column_nft-description-column', function ($item) {
    if ($item && !empty($item['nft_description'])) {
        echo esc_html($item['nft_description']);

        // echo '<pre>';
        // echo print_r($item);
        // echo '</pre>';

    } else {
        echo 'no description';
    }
});

add_action('woocommerce_account_downloads_column_nft-image-column', function ($item) {

    $html  = '<div class="woocommerce-nft-image--placeholder">';
    if (!empty($item['nft_cached_file_url'])) {
        $html .= sprintf('<img src="%s" alt="%s" width="100" class="wp-post-image" />', $item['nft_cached_file_url'], esc_html__('NFT cached image', 'hackathon-wc-nft'));
    } else {
        $html .= sprintf('<img src="%s" alt="%s" width="100" class="wp-post-image" />', esc_url(wc_placeholder_img_src('woocommerce_single')), esc_html__('Awaiting NFT image', 'hackathon-wc-nft'));
    }
    $html .= '</div>';

    echo $html;
});

add_action('woocommerce_account_downloads_column_nft-chain-column', function ($item) {
    if ($item && !empty($item['nft_chain'])) {
        echo esc_html($item['nft_chain']);
    } else {
        echo 'no chain';
    }
});

// add custom meta for nft products to mint later
add_action('woocommerce_checkout_create_order_line_item',  function ($item, $cart_item_key, $values, $order) {
    // TODO: check if NFT item, if not return
    if (!is_user_logged_in()) {
        return;
    }

    $user = wp_get_current_user();

    // ? FEAT: get adress from billing and anonymus login
    $mint_to_address = $user->user_login;
    // $mint_to_address = '0x5deCa6A2295e13803Aeaa2e182DAF72eB748302b';

    // ? validate adress
    if (!empty($mint_to_address)) {
        $item->update_meta_data('_wc_nft_mint_to_address', $mint_to_address);
    }
}, 10, 4);

// TODO: move logic to plugin
add_action('wp_head', function () {

    // if (!is_user_logged_in()) {
    //     return;
    // }

    $moralis_app_id = get_option('wc_nft_moralis_app_id');
    $moralis_server_url = get_option('wc_nft_moralis_server_url');
    $moralis_chain = get_option('wc_nft_moralis_chain', 'polygon');

    $user = wp_get_current_user();

    // TODO: get from html attr data
    // contract adress
    $token_address = '';

    // user adress from user login, seted by ethpress
    $adress = $user->user_login;
    ?>

    <script src="https://cdn.jsdelivr.net/npm/web3@latest/dist/web3.min.js"></script>
    <script src="https://unpkg.com/moralis/dist/moralis.js"></script>
    <script type="text/javascript">
        // moralis server setup
        const serverUrl = "<?php echo $moralis_server_url; ?>";
        const appId = "<?php echo $moralis_app_id; ?>";
        Moralis.start({
            serverUrl,
            appId
        });

        // get polygon NFTs for adress, for contract token_address
        function getPolygonNFTs() {
            const options = {
                chain: '<?php echo $moralis_chain; ?>',
                // token_address: '<?php echo $token_address; ?>',
                adress: '<?php echo $adress; ?>'
            };

            return Moralis.Web3API.account.getNFTs(options);
        }

        async function loginWithMetaMask() {
            console.log('loginWithMetaMask');

            // Enable web3 and get the initialized web3 instance from Web3.js
            const web3Js = new Web3(Moralis.provider)
            await Moralis.enableWeb3();

            // let user = Moralis.User.current();
            // if (!user) {
            //     user = await Moralis.authenticate();
            // }
            // console.log('user', user);
        }

        function logOut() {
            Moralis.User.logOut().then(function(user) {
                console.log("logged out user:", user);
            });
        }
    </script>

<?php
});

// TODO: this is not working as excpected with multiple nfts
// this is a workournd bc lack of time, only allow 1 nft/product per order
// products are set to sold indivually, this will be remove and fixed to support multiple nfts per product and batch mint

// Empty cart each time you click on add cart to avoid multiple element selected
add_action('woocommerce_before_calculate_totals', function ($cart) {
    if (is_admin() && !defined('DOING_AJAX'))
        return;

    if (did_action('woocommerce_before_calculate_totals') >= 2)
        return;

    $cart_items = $cart->get_cart();

    if (count($cart_items) > 1) {
        $cart_item_keys = array_keys($cart_items);
        $cart->remove_cart_item(reset($cart_item_keys));
    }
}, 30, 1);

// Skip the cart and redirect to check out url when clicking on Add to cart
add_filter('add_to_cart_redirect', function () {
    global $woocommerce;

    // Remove the default `Added to cart` message
    wc_clear_notices();

    return $woocommerce->cart->get_checkout_url();
});

// Edit default add_to_cart button text
add_filter('add_to_cart_text', 'woo_custom_cart_button_text');
add_filter('woocommerce_product_single_add_to_cart_text', function () {
    return __('Buy & Mint', 'hackathon-wc-nft');
});

// Unset all options related to the cart
update_option('woocommerce_cart_redirect_after_add', 'no');
update_option('woocommerce_enable_ajax_add_to_cart', 'no');
