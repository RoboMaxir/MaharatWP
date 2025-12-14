<?php
/**
 * Custom template tags for this theme.
 *
 * Eventually, some of the functionality here could be replaced by core features.
 *
 * @package Professional Project Theme
 */

if (!function_exists('professional_project_posted_on')) :
	/**
	 * Prints HTML with meta information for the current post-date/time.
	 */
	function professional_project_posted_on() {
		$time_string = '<time class="entry-date published updated" datetime="%1$s">%2$s</time>';
		
		if (get_the_time('U') !== get_the_modified_time('U')) {
			$time_string = '<time class="entry-date published" datetime="%1$s">%2$s</time><time class="updated" datetime="%3$s">%4$s</time>';
		}

		$time_string = sprintf(
			$time_string,
			esc_attr(get_the_date(DATE_W3C)),
			esc_html(get_the_date()),
			esc_attr(get_the_modified_date(DATE_W3C)),
			esc_html(get_the_modified_date())
		);

		printf(
			'<span class="posted-on">%1$s <a href="%2$s" rel="bookmark">%3$s</a></span>',
			__('Posted on', 'professional-project-theme'),
			esc_url(get_permalink()),
			$time_string
		);
	}
endif;

if (!function_exists('professional_project_posted_by')) :
	/**
	 * Prints HTML with meta information for the current author.
	 */
	function professional_project_posted_by() {
		printf(
			'<span class="byline"> %1$s <span class="author vcard"><a class="url fn n" href="%2$s">%3$s</a></span></span>',
			__('by', 'professional-project-theme'),
			esc_url(get_author_posts_url(get_the_author_meta('ID'))),
			esc_html(get_the_author())
		);
	}
endif;

if (!function_exists('professional_project_entry_footer')) :
	/**
	 * Prints HTML with meta information for the categories, tags and comments.
	 */
	function professional_project_entry_footer() {
		// Hide category and tag text for pages.
		if ('post' === get_post_type()) {
			/* translators: used between list items, there is a space after the comma */
			$categories_list = get_the_category_list(__(', ', 'professional-project-theme'));
			if ($categories_list) {
				/* translators: 1: list of categories. */
				printf('<span class="cat-links">%1$s %2$s</span>', __('Posted in', 'professional-project-theme'), $categories_list); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			}

			/* translators: used between list items, there is a space after the comma */
			$tags_list = get_the_tag_list('', __(', ', 'professional-project-theme'));
			if ($tags_list) {
				/* translators: 1: list of tags. */
				printf('<span class="tags-links">%1$s %2$s</span>', __('Tagged', 'professional-project-theme'), $tags_list); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			}
		}

		if (!is_single() && !post_password_required() && (comments_open() || get_comments_number())) {
			echo '<span class="comments-link">';
			comments_popup_link(
				sprintf(
					wp_kses(
						/* translators: %s: post title */
						__('Leave a Comment<span class="screen-reader-text"> on %s</span>', 'professional-project-theme'),
						array(
							'span' => array(
								'class' => array(),
							),
						)
					),
					wp_kses_post(get_the_title())
				)
			);
			echo '</span>';
		}

		edit_post_link(
			sprintf(
				wp_kses(
					/* translators: %s: Name of current post. Only visible to screen readers */
					__('Edit <span class="screen-reader-text">%s</span>', 'professional-project-theme'),
					array(
						'span' => array(
							'class' => array(),
						),
					)
				),
				wp_kses_post(get_the_title())
			),
			'<span class="edit-link">',
			'</span>'
		);
	}
endif;

if (!function_exists('professional_project_post_thumbnail')) :
	/**
	 * Displays an optional post thumbnail.
	 *
	 * Wraps the post thumbnail in an anchor element on index views, or a div
	 * element when on single views.
	 */
	function professional_project_post_thumbnail() {
		if (post_password_required() || is_attachment() || !has_post_thumbnail()) {
			return;
		}

		if (is_singular()) :
			?>

			<div class="post-thumbnail">
				<?php the_post_thumbnail(); ?>
			</div><!-- .post-thumbnail -->

		<?php else : ?>

			<div class="post-thumbnail">
				<a href="<?php the_permalink(); ?>" aria-hidden="true" tabindex="-1">
					<?php
					the_post_thumbnail(
						'post-thumbnail',
						array(
							'alt' => the_title_attribute(
								array(
									'echo' => false,
								)
							),
						)
					);
					?>
				</a>
			</div><!-- .post-thumbnail -->

			<?php
		endif;
	}
endif;

if (!function_exists('professional_project_project_details')) :
	/**
	 * Displays project details for the custom post type
	 */
	function professional_project_project_details($post_id = null) {
		if (!$post_id) {
			$post_id = get_the_ID();
		}

		if (get_post_type($post_id) !== 'projects') {
			return;
		}

		$budget = get_post_meta($post_id, '_project_budget', true);
		$status = get_post_meta($post_id, '_project_status', true);
		$client = get_post_meta($post_id, '_project_client', true);
		$start_date = get_post_meta($post_id, '_project_start_date', true);
		$end_date = get_post_meta($post_id, '_project_end_date', true);
		$priority = get_post_meta($post_id, '_project_priority', true);

		// Status labels
		$status_labels = array(
			'open' => __('Open', 'professional-project-theme'),
			'in_progress' => __('In Progress', 'professional-project-theme'),
			'closed' => __('Closed', 'professional-project-theme')
		);

		// Priority labels
		$priority_labels = array(
			'low' => __('Low', 'professional-project-theme'),
			'medium' => __('Medium', 'professional-project-theme'),
			'high' => __('High', 'professional-project-theme'),
			'urgent' => __('Urgent', 'professional-project-theme')
		);

		// Start building the output
		$output = '<div class="project-details">';

		if ($budget) {
			$output .= '<div class="project-budget"><strong>' . __('Budget:', 'professional-project-theme') . '</strong> $' . esc_html($budget) . '</div>';
		}

		if ($status && isset($status_labels[$status])) {
			$output .= '<div class="project-status"><strong>' . __('Status:', 'professional-project-theme') . '</strong> <span class="status-' . esc_attr($status) . '">' . esc_html($status_labels[$status]) . '</span></div>';
		}

		if ($client) {
			$output .= '<div class="project-client"><strong>' . __('Client:', 'professional-project-theme') . '</strong> ' . esc_html($client) . '</div>';
		}

		if ($start_date) {
			$output .= '<div class="project-start-date"><strong>' . __('Start Date:', 'professional-project-theme') . '</strong> ' . esc_html(date_i18n(get_option('date_format'), strtotime($start_date))) . '</div>';
		}

		if ($end_date) {
			$output .= '<div class="project-end-date"><strong>' . __('End Date:', 'professional-project-theme') . '</strong> ' . esc_html(date_i18n(get_option('date_format'), strtotime($end_date))) . '</div>';
		}

		if ($priority && isset($priority_labels[$priority])) {
			$output .= '<div class="project-priority"><strong>' . __('Priority:', 'professional-project-theme') . '</strong> <span class="priority-' . esc_attr($priority) . '">' . esc_html($priority_labels[$priority]) . '</span></div>';
		}

		$output .= '</div>';

		echo $output; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	}
endif;

if (!function_exists('professional_project_breadcrumbs')) :
	/**
	 * Display breadcrumbs for the site
	 */
	function professional_project_breadcrumbs() {
		if (!is_home()) {
			echo '<nav class="breadcrumbs">';
			echo '<a href="' . esc_url(home_url('/')) . '">' . esc_html__('Home', 'professional-project-theme') . '</a>';
			
			if (is_category() || is_single()) {
				echo ' > ';
				
				if (is_single()) {
					$category = get_the_category();
					if ($category) {
						$category_links = array();
						foreach ($category as $cat) {
							$category_links[] = '<a href="' . esc_url(get_category_link($cat->term_id)) . '">' . esc_html($cat->name) . '</a>';
						}
						echo implode(', ', $category_links) . ' > ';
						echo '<span>' . get_the_title() . '</span>';
					}
				} elseif (is_category()) {
					echo '<span>' . single_cat_title('', false) . '</span>';
				}
			} elseif (is_page()) {
				echo ' > <span>' . get_the_title() . '</span>';
			}
			
			echo '</nav>';
		}
	}
endif;