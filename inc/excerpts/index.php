<?php
/**
 * Roundhouse custom excerpts feature.
 *
 * @package Roundhouse
 */

/**
 * HTML tags to display in an excerpt.
 *
 * @return string List of tags.
 */
function pvd_custom_excerpt_allowed_tags() {
	// Add tags to allow in custom excerpts to this array.
	$allowed_html = [
		'p'      => [],
		'a'      => [
			'href' => [],
		],
		'b'      => [],
		'i'      => [],
		'strong' => [],
		'em'     => [],
	];
	return $allowed_html;
}

if ( ! function_exists( 'pvd_custom_excerpt' ) ) :
	/**
	 * Customize WP excerpts.
	 *
	 * @param  string $custom_excerpt The raw excerpt.
	 * @return string                 The custom excerpt.
	 */
	function pvd_custom_excerpt( $custom_excerpt ) {
		// Get the raw excerpt from WordPress.
		$raw_excerpt = $custom_excerpt;

		if ( '' === $custom_excerpt ) {

			// Prepare Custom Excerpt.
			$custom_excerpt = get_the_content( '' );
			$custom_excerpt = strip_shortcodes( $custom_excerpt );
			$custom_excerpt = apply_filters( 'the_content', $custom_excerpt );
			$custom_excerpt = str_replace( ']]>', ']]&gt;', $custom_excerpt );
			$custom_excerpt = wp_kses(
				$custom_excerpt,
				pvd_custom_excerpt_allowed_tags()
			);

			// Read more link values.
			$read_more_title = sprintf(
				/* translators: %s Post Title */
				__(
					'Read more about: %s',
					'roundhouse'
				),
				get_the_title()
			);
			$read_more_class = 'post-excerpt__read-more read-more read-more-link';

			$excerpt_end  = '<a href="' . esc_url( get_permalink() ) . '" title="' . esc_attr( $read_more_title ) . '" class="' . esc_attr( $read_more_class ) . '">' . __( 'Read more', 'roundhouse' ) . '</a>';
			$excerpt_more = apply_filters( 'excerpt_more', ' ' . $excerpt_end );

			// Set the excerpt word count and only break after sentence is complete.
			$excerpt_word_count = 30;
			if ( wp_is_mobile() ) {
				$excerpt_word_count = 15;
			}
			$excerpt_length = apply_filters( 'excerpt_length', $excerpt_word_count );
			$tokens         = [];
			$excerpt_output = '';
			$count          = 0;

			// Divide the string into tokens; HTML tags, or words, followed by any whitespace.
			preg_match_all( '/(<[^>]+>|[^<>\s]+)\s*/u', $custom_excerpt, $tokens );

			foreach ( $tokens[0] as $token ) {

				if ( $count >= $excerpt_length && preg_match( '/[\?\.\!]\s*$/uS', $token ) ) {
					// Limit reached, continue until , ; ? . or ! occur at the end.
					$excerpt_output .= trim( $token );
					$excerpt_output .= $excerpt_more;
					break;
				}

				// Add words to complete sentence.
				$count++;

				// Append what's left of the token.
				$excerpt_output .= $token;
			}

			$custom_excerpt = trim( force_balance_tags( $excerpt_output ) );

			return $custom_excerpt;

		}

		return apply_filters( 'pvd_custom_excerpt', $custom_excerpt, $raw_excerpt );
	}

endif;

remove_filter( 'get_the_excerpt', 'wp_trim_excerpt' );
add_filter( 'get_the_excerpt', 'pvd_custom_excerpt' );
