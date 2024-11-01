<?php
/**
 * Admin View: Page - Admin options.
 *
 * @package WooCommerce\Integrations
 * @since 1.0.0
 * @version 2.6.4
**/

defined('ABSPATH') or die('No script kiddies please!');

?>
<table class="form-table">
	<tr valign="top">
		<th scope="row" class="titledesc">
			<label>
				<?php esc_html_e('StoreFront Token', 'swipecart'); ?>
			</label>
		</th>
		<td class="forminp">
			<fieldset>
				<legend class="screen-reader-text"><span><?php esc_html_e('Storefront Token', 'swipecart'); ?></span></legend>
				<input class="input-text regular-input" type="text" value="<?php echo esc_attr($authCombo['StoreFrontToken']); ?>" id="StoreFrontToken" readonly>
				<?php echo sprintf('<button data-nonce="%s" class="button swipecart-btn-spinner" id="swipecart-reveal-token-btn">%s</button>', wp_create_nonce('swipecart_reveal_tokens'), esc_html__('Reset Tokens', 'swipecart')); ?>
				<p class="description"><?php esc_html_e('This StoreFront Token required for Mobile API.', 'swipecart'); ?></p>
			</fieldset>
		</td>
	</tr>
</table>