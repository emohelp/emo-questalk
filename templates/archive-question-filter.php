<?php
/**
 * The template for displaying answers
 *
 * @package EMO Questalk
 * @since EMO Questalk 1.0.0
 */

global $emqa_general_settings;
// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Nonce verification is handled elsewhere.
$sort = isset( $_GET['sort'] ) ? esc_html( $_GET['sort'] ) : '';
// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Nonce verification is handled elsewhere.
$filter = isset( $_GET['filter'] ) ? esc_html( $_GET['filter'] ) : 'all';
?>
<div class="emqa-question-filter">
	<span><?php esc_html_e( 'Filter:', 'emqa' ); ?></span>
	<?php
	// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Nonce verification is handled elsewhere.
	if ( !isset( $_GET['user'] ) ) : ?>
		<a href="<?php echo esc_url( add_query_arg( array( 'filter' => 'all' ) ) ) ?>" class="<?php echo 'all' == $filter ? 'active' : '' ?>"><?php esc_html_e( 'All', 'emqa' ); ?></a>
		<?php if ( emqa_is_enable_status() ) : ?>
			<a href="<?php echo esc_url( add_query_arg( array( 'filter' => 'open' ) ) ) ?>" class="<?php echo 'open' == $filter ? 'active' : '' ?>"><?php esc_html_e( 'Open', 'emqa' ); ?></a>
			<a href="<?php echo esc_url( add_query_arg( array( 'filter' => 'resolved' ) ) ) ?>" class="<?php echo 'resolved' == $filter ? 'active' : '' ?>"><?php esc_html_e( 'Resolved', 'emqa' ); ?></a>
			<a href="<?php echo esc_url( add_query_arg( array( 'filter' => 'closed' ) ) ) ?>" class="<?php echo 'closed' == $filter ? 'active' : '' ?>"><?php esc_html_e( 'Closed', 'emqa' ); ?></a>
		<?php endif; ?>
		<a href="<?php echo esc_url( add_query_arg( array( 'filter' => 'unanswered' ) ) ) ?>" class="<?php echo 'unanswered' == $filter ? 'active' : '' ?>"><?php esc_html_e( 'Unanswered', 'emqa' ); ?></a>
		<?php if ( is_user_logged_in() ) : ?>
			<a href="<?php echo esc_url( add_query_arg( array( 'filter' => 'my-questions' ) ) ) ?>" class="<?php echo 'my-questions' == $filter ? 'active' : '' ?>"><?php esc_html_e( 'My questions', 'emqa' ); ?></a>
			<a href="<?php echo esc_url( add_query_arg( array( 'filter' => 'my-subscribes' ) ) ) ?>" class="<?php echo 'my-subscribes' == $filter ? 'active' : '' ?>"><?php esc_html_e( 'My subscribes', 'emqa' ); ?></a>
		<?php endif; ?>
	<?php else : ?>
		<a href="<?php echo esc_url( add_query_arg( array( 'filter' => 'all' ) ) ) ?>" class="<?php echo 'all' == $filter ? 'active' : '' ?>"><?php esc_html_e( 'Questions', 'emqa' ); ?></a>
		<a href="<?php echo esc_url( add_query_arg( array( 'filter' => 'subscribes' ) ) ) ?>" class="<?php echo 'subscribes' == $filter ? 'active' : '' ?>"><?php esc_html_e( 'Subscribes', 'emqa' ); ?></a>
	<?php endif; ?>
	<select id="emqa-sort-by" class="emqa-sort-by" onchange="this.options[this.selectedIndex].value && (window.location = this.options[this.selectedIndex].value);">
		<option selected disabled><?php esc_html_e( 'Sort by', 'emqa' ); ?></option>
		<option <?php selected( $sort, 'views' ) ?> value="<?php echo esc_url( add_query_arg( array( 'sort' => 'views' ) ) ) ?>"><?php esc_html_e( 'Views', 'emqa' ) ?></option>
		<option <?php selected( $sort, 'answers' ) ?> value="<?php echo esc_url( add_query_arg( array( 'sort' => 'answers' ) ) ) ?>"><?php esc_html_e( 'Answers', 'emqa' ); ?></option>
		<option <?php selected( $sort, 'votes' ) ?> value="<?php echo esc_url( add_query_arg( array( 'sort' => 'votes' ) ) ) ?>"><?php esc_html_e( 'Votes', 'emqa' ) ?></option>
	</select>
</div>