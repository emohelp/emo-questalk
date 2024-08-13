<?php
/**
 * The template for displaying captcha form
 *
 * @package EMO Questalk
 * @since EMO Questalk 1.0.0
 */
?>

<?php if ( ( 'emqa-question' == get_post_type() && emqa_is_captcha_enable_in_single_question() ) || ( emqa_is_ask_form() && emqa_is_captcha_enable_in_submit_question() ) ) : ?>
<p class="emqa-captcha">
	<?php 
	$number_1 = wp_rand( 0, 20 );
	$number_2 = wp_rand( 0, 20 );
	?>
	<span class="emqa-number-one"><?php echo esc_attr( $number_1 ) ?></span>
	<span class="emqa-plus">&#43;</span>
	<span class="emqa-number-one"><?php echo esc_attr( $number_2 ) ?></span>
	<span class="emqa-plus">&#61;</span>
	<input type="text" name="emqa-captcha-result" id="emqa-captcha-result" value="" placeholder="<?php esc_html_e( 'Enter the result', 'emqa' ) ?>">
	<input type="hidden" name="emqa-captcha-number-1" id="emqa-captcha-number-1" value="<?php echo esc_attr( $number_1 ) ?>">
	<input type="hidden" name="emqa-captcha-number-2" id="emqa-captcha-number-2" value="<?php echo esc_attr( $number_2 ) ?>">
</p>
<?php endif; ?>