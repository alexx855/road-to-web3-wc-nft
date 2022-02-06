<?php

/**
 * Provide a public-facing view for the plugin
 *
 * This file is used to markup the public-facing aspects of the plugin.
 *
 * @link       https://hackathon-wc-nft.alexpedersen.dev
 * @since      1.0.0
 *
 * @package    Hackathon_Wc_Nft
 * @subpackage Hackathon_Wc_Nft/public/partials
 */
?>

<div id="transferModal" class="modal" style="display: none;">
    <div class="modal-background"></div>
    <div class="modal-content">
        <div class="modal-heading">
            <span slot="heading">Transfer <span class="modal-close">&times;</span></span>
        </div>
        <div class="content">
            <!-- <p>Enter the adress you want to transfer ${token_id} to:</p> -->
            <div style="display: none;" id="response"></div>
            <form id="transferForm" method="get" class="search-form" action="">
                <p>Enter the adress you want to transfer to:</p>
                <label for="transferToAdress">
                    <span class="screen-reader-text"><?php _e('Transfer to adress:', 'hackathon-wc-nft'); ?></span>
                    <input type="search" id="transferToAdress" class="" placeholder="<?php echo esc_attr_x('0x...', 'placeholder', 'hackathon-wc-nft'); ?>" value="" name="transferToAdress" />
                </label>
                <input id="submitTransfer" type="submit" class="btn-submit-transfer" value="<?php echo esc_attr_x('Sign', 'submit button', 'hackathon-wc-nft'); ?>" />
                <input type="hidden" name="tokenId" id="tokenId" value="" />
                <input type="hidden" name="contractAddress" id="contractAddress" value="" />
            </form>
        </div>
    </div>
</div>