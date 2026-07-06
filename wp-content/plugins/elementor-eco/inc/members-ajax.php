<?php
/**
 * AJAX functionality and caching for the Members widget.
 *
 * Path:
 * plugins/elementor-eco/inc/members-ajax.php
 */

namespace ElementorEco;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Members_Ajax {

	const NONCE_ACTION        = 'eco_members_nonce';
	const INITIAL_LIMIT       = 8;
	const CACHE_EXPIRATION    = DAY_IN_SECONDS;
	const CACHE_VERSION_OPTION = 'eco_members_cache_version';

	/**
	 * Register hooks.
	 */
	public static function init() {
		add_action( 'wp_ajax_eco_load_members', [ __CLASS__, 'ajax_load_members' ] );
		add_action( 'wp_ajax_nopriv_eco_load_members', [ __CLASS__, 'ajax_load_members' ] );

		add_action( 'wp_ajax_eco_load_member_details', [ __CLASS__, 'ajax_load_member_details' ] );
		add_action( 'wp_ajax_nopriv_eco_load_member_details', [ __CLASS__, 'ajax_load_member_details' ] );

		/*
		 * Invalidate member caches whenever a member changes.
		 */
		add_action( 'save_post_member', [ __CLASS__, 'invalidate_cache_on_save' ], 10, 3 );
		add_action( 'before_delete_post', [ __CLASS__, 'invalidate_cache_for_post' ] );
		add_action( 'trashed_post', [ __CLASS__, 'invalidate_cache_for_post' ] );
		add_action( 'untrashed_post', [ __CLASS__, 'invalidate_cache_for_post' ] );
	}

	/**
	 * Get current cache version.
	 */
	public static function get_cache_version() {
		$version = get_option( self::CACHE_VERSION_OPTION );

		if ( ! $version ) {
			$version = (string) time();

			add_option(
				self::CACHE_VERSION_OPTION,
				$version,
				'',
				false
			);
		}

		return sanitize_key( (string) $version );
	}

	/**
	 * Increment the cache version.
	 *
	 * Existing transient keys become obsolete immediately.
	 */
	public static function invalidate_cache() {
		$version = sprintf(
			'%s_%s',
			time(),
			wp_rand( 1000, 9999 )
		);

		update_option(
			self::CACHE_VERSION_OPTION,
			$version,
			false
		);
	}

	/**
	 * Invalidate when a member is saved.
	 */
	public static function invalidate_cache_on_save( $post_id, $post, $update ) {
		unset( $update );

		if ( wp_is_post_revision( $post_id ) ) {
			return;
		}

		if ( wp_is_post_autosave( $post_id ) ) {
			return;
		}

		if ( ! $post || 'member' !== $post->post_type ) {
			return;
		}

		self::invalidate_cache();
	}

	/**
	 * Invalidate when a member is deleted, trashed, or restored.
	 */
	public static function invalidate_cache_for_post( $post_id ) {
		if ( 'member' !== get_post_type( $post_id ) ) {
			return;
		}

		self::invalidate_cache();
	}

	/**
	 * Return available member initials.
	 *
	 * The result is cached because it rarely changes.
	 */
	public static function get_available_letters() {
		global $wpdb;

		$version   = self::get_cache_version();
		$cache_key = 'eco_member_letters_' . $version;
		$letters   = get_transient( $cache_key );

		if ( false !== $letters && is_array( $letters ) ) {
			return $letters;
		}

		/*
		 * Query only the first character of published member titles.
		 * This is far lighter than loading all posts, logos and ACF fields.
		 */
		$characters = $wpdb->get_col(
			$wpdb->prepare(
				"
				SELECT DISTINCT UPPER(LEFT(post_title, 1))
				FROM {$wpdb->posts}
				WHERE post_type = %s
				AND post_status = %s
				AND post_title <> ''
				ORDER BY post_title ASC
				",
				'member',
				'publish'
			)
		);

		$letters     = [];
		$has_other   = false;

		foreach ( $characters as $character ) {
			$character = strtoupper( (string) $character );

			if ( preg_match( '/^[A-Z]$/', $character ) ) {
				$letters[] = $character;
			} else {
				$has_other = true;
			}
		}

		$letters = array_values( array_unique( $letters ) );
		sort( $letters );

		if ( $has_other ) {
			array_unshift( $letters, '#' );
		}

		set_transient(
			$cache_key,
			$letters,
			self::CACHE_EXPIRATION
		);

		return $letters;
	}

	/**
	 * AJAX: Load first 8 or all members for one letter.
	 */
	public static function ajax_load_members() {
		check_ajax_referer( self::NONCE_ACTION, 'nonce' );

		$letter = isset( $_POST['letter'] )
			? self::sanitize_letter( wp_unslash( $_POST['letter'] ) )
			: '';

		$load_all = isset( $_POST['load_all'] )
			? rest_sanitize_boolean( wp_unslash( $_POST['load_all'] ) )
			: false;

		if ( ! $letter ) {
			wp_send_json_error(
				[
					'message' => __( 'Invalid filter.', 'elementor-eco' ),
				],
				400
			);
		}

		$members = self::get_members_html( $letter, $load_all );

		wp_send_json_success( $members );
	}

	/**
	 * AJAX: Load complete details for one member.
	 */
	public static function ajax_load_member_details() {
		check_ajax_referer( self::NONCE_ACTION, 'nonce' );

		$member_id = isset( $_POST['member_id'] )
			? absint( $_POST['member_id'] )
			: 0;

		if (
			! $member_id ||
			'member' !== get_post_type( $member_id ) ||
			'publish' !== get_post_status( $member_id )
		) {
			wp_send_json_error(
				[
					'message' => __( 'Member not found.', 'elementor-eco' ),
				],
				404
			);
		}

		$version   = self::get_cache_version();
		$cache_key = sprintf(
			'eco_member_details_%d_%s',
			$member_id,
			$version
		);

		$details = get_transient( $cache_key );

		if ( false === $details || ! is_array( $details ) ) {
			$details = self::build_member_details( $member_id );

			set_transient(
				$cache_key,
				$details,
				self::CACHE_EXPIRATION
			);
		}

		wp_send_json_success( $details );
	}

	/**
	 * Get member-card HTML.
	 */
	private static function get_members_html( $letter, $load_all ) {
		$version   = self::get_cache_version();
		$mode      = $load_all ? 'all' : 'initial';
		$cache_key = sprintf(
			'eco_members_%s_%s_%s',
			strtolower( $letter ),
			$mode,
			$version
		);

		$cached = get_transient( $cache_key );

		if ( false !== $cached && is_array( $cached ) ) {
			return $cached;
		}

		$posts_per_page = $load_all ? -1 : self::INITIAL_LIMIT;

		$where_filter = static function ( $where, $query ) use ( $letter ) {
			global $wpdb;

			if ( ! $query->get( 'eco_member_letter_query' ) ) {
				return $where;
			}

			if ( '#' === $letter ) {
				$where .= " AND {$wpdb->posts}.post_title NOT REGEXP '^[A-Za-z]'";
			} else {
				$like = $wpdb->esc_like( $letter ) . '%';

				$where .= $wpdb->prepare(
					" AND {$wpdb->posts}.post_title LIKE %s",
					$like
				);
			}

			return $where;
		};

		add_filter( 'posts_where', $where_filter, 10, 2 );

		$query = new \WP_Query(
			[
				'post_type'              => 'member',
				'post_status'            => 'publish',
				'posts_per_page'         => $posts_per_page,
				'orderby'                => 'title',
				'order'                  => 'ASC',
				'eco_member_letter_query' => true,
				'update_post_meta_cache' => true,
				'update_post_term_cache' => false,
				'ignore_sticky_posts'    => true,
				'no_found_rows'          => $load_all,
			]
		);

		remove_filter( 'posts_where', $where_filter, 10 );

		$total = $load_all
			? $query->post_count
			: (int) $query->found_posts;

		ob_start();

		if ( $query->have_posts() ) {
			while ( $query->have_posts() ) {
				$query->the_post();

				self::render_member_card( get_the_ID() );
			}
		} else {
			?>
			<p class="eco-members-empty">
				<?php esc_html_e( 'No members found.', 'elementor-eco' ); ?>
			</p>
			<?php
		}

		wp_reset_postdata();

		$result = [
			'html'         => ob_get_clean(),
			'total'        => $total,
			'shown'        => $query->post_count,
			'has_more'     => ! $load_all && $total > self::INITIAL_LIMIT,
			'loaded_all'   => $load_all,
			'letter'       => $letter,
		];

		set_transient(
			$cache_key,
			$result,
			self::CACHE_EXPIRATION
		);

		return $result;
	}

	/**
	 * Render one member card.
	 */
	private static function render_member_card( $member_id ) {
		$title      = get_the_title( $member_id );
		$website    = self::get_field_value( 'website', $member_id );
		$logo_id    = get_post_thumbnail_id( $member_id );
		$has_detail = self::member_has_details( $member_id );

		$website = is_string( $website ) ? trim( $website ) : '';
		?>
		<article class="eco-member-card">
			<div class="eco-member-card__visual">
				<?php if ( $logo_id ) : ?>
					<?php if ( $website ) : ?>
						<a
							href="<?php echo esc_url( $website ); ?>"
							target="_blank"
							rel="noopener noreferrer"
							aria-label="<?php echo esc_attr( $title ); ?>"
						>
					<?php endif; ?>

					<?php
					echo wp_get_attachment_image(
						$logo_id,
						'medium',
						false,
						[
							'class'    => 'eco-member-card__logo',
							'alt'      => $title,
							'loading'  => 'lazy',
							'decoding' => 'async',
						]
					);
					?>

					<?php if ( $website ) : ?>
						</a>
					<?php endif; ?>
				<?php else : ?>
					<div class="eco-member-card__placeholder" aria-hidden="true">
						<span><?php echo esc_html( self::get_placeholder_text( $title ) ); ?></span>
					</div>
				<?php endif; ?>
			</div>

			<div class="eco-member-card__content">
				<h3 class="eco-member-card__title">
					<?php echo esc_html( $title ); ?>
				</h3>

				<div class="eco-member-card__actions">
					<?php if ( $website ) : ?>
						<a
							class="eco-member-card__link"
							href="<?php echo esc_url( $website ); ?>"
							target="_blank"
							rel="noopener noreferrer"
						>
							<?php esc_html_e( 'Visit Website', 'elementor-eco' ); ?>
						</a>
					<?php endif; ?>

					<?php if ( $has_detail ) : ?>
						<button
							type="button"
							class="eco-member-card__details member-description-link"
							data-member-id="<?php echo esc_attr( $member_id ); ?>"
						>
							<span aria-hidden="true">ⓘ</span>
							<?php esc_html_e( 'More Information', 'elementor-eco' ); ?>
						</button>
					<?php endif; ?>
				</div>
			</div>
		</article>
		<?php
	}

	/**
	 * Determine whether the information button should be shown.
	 *
	 * Post meta has already been primed by WP_Query, so this does not create
	 * a database query for every individual field.
	 */
	private static function member_has_details( $member_id ) {
		$detail_fields = [
			'email',
			'phone',
			'fax_number',
			'line_1',
			'line_2',
			'line_3',
			'zip_code',
			'city',
			'country',
			'description',
		];

		foreach ( $detail_fields as $field_name ) {
			$value = self::get_field_value( $field_name, $member_id );

			if ( is_array( $value ) && ! empty( $value ) ) {
				return true;
			}

			if ( is_string( $value ) && '' !== trim( $value ) ) {
				return true;
			}

			if ( ! is_array( $value ) && ! is_string( $value ) && ! empty( $value ) ) {
				return true;
			}
		}

		return false;
	}

	/**
	 * Build modal data.
	 */
	private static function build_member_details( $member_id ) {
		$website    = self::get_field_value( 'website', $member_id );
		$email      = self::get_field_value( 'email', $member_id );
		$phone      = self::get_field_value( 'phone', $member_id );
		$fax        = self::get_field_value( 'fax_number', $member_id );
		$line_1     = self::get_field_value( 'line_1', $member_id );
		$line_2     = self::get_field_value( 'line_2', $member_id );
		$line_3     = self::get_field_value( 'line_3', $member_id );
		$zip_code   = self::get_field_value( 'zip_code', $member_id );
		$city       = self::get_field_value( 'city', $member_id );
		$country    = self::get_field_value( 'country', $member_id );
		$description = self::get_field_value( 'description', $member_id );

		$website = is_string( $website ) ? trim( $website ) : '';
		$email   = is_string( $email ) ? sanitize_email( $email ) : '';
		$phone   = is_string( $phone ) ? trim( $phone ) : '';
		$fax     = is_string( $fax ) ? trim( $fax ) : '';

		ob_start();
		?>
		<div class="eco-member-details">
			<?php if ( $website ) : ?>
				<p>
					<a
						href="<?php echo esc_url( $website ); ?>"
						target="_blank"
						rel="noopener noreferrer"
					>
						<?php esc_html_e( 'Visit Website', 'elementor-eco' ); ?>
					</a>
				</p>
			<?php endif; ?>

			<?php if ( $line_1 || $line_2 || $line_3 || $zip_code || $city || $country ) : ?>
				<div class="eco-member-details__address">
					<?php if ( $line_1 ) : ?>
						<p><?php echo esc_html( $line_1 ); ?></p>
					<?php endif; ?>

					<?php if ( $line_2 ) : ?>
						<p><?php echo esc_html( $line_2 ); ?></p>
					<?php endif; ?>

					<?php if ( $line_3 ) : ?>
						<p><?php echo esc_html( $line_3 ); ?></p>
					<?php endif; ?>

					<?php if ( $zip_code || $city ) : ?>
						<p>
							<?php echo esc_html( trim( $zip_code . ' ' . $city ) ); ?>
						</p>
					<?php endif; ?>

					<?php if ( $country ) : ?>
						<p><?php echo esc_html( $country ); ?></p>
					<?php endif; ?>
				</div>
			<?php endif; ?>

			<?php if ( $phone ) : ?>
				<p>
					<strong><?php esc_html_e( 'Phone:', 'elementor-eco' ); ?></strong>
					<a href="<?php echo esc_url( 'tel:' . self::clean_phone_link( $phone ) ); ?>">
						<?php echo esc_html( $phone ); ?>
					</a>
				</p>
			<?php endif; ?>

			<?php if ( $fax ) : ?>
				<p>
					<strong><?php esc_html_e( 'Fax:', 'elementor-eco' ); ?></strong>
					<?php echo esc_html( $fax ); ?>
				</p>
			<?php endif; ?>

			<?php if ( $email ) : ?>
				<p>
					<strong><?php esc_html_e( 'Email:', 'elementor-eco' ); ?></strong>
					<a href="<?php echo esc_url( 'mailto:' . $email ); ?>">
						<?php echo esc_html( antispambot( $email ) ); ?>
					</a>
				</p>
			<?php endif; ?>

			<?php if ( $description ) : ?>
				<div class="eco-member-details__description">
					<?php echo wp_kses_post( wpautop( $description ) ); ?>
				</div>
			<?php endif; ?>
		</div>
		<?php

		return [
			'title' => get_the_title( $member_id ),
			'html'  => ob_get_clean(),
		];
	}

	/**
	 * ACF-compatible field getter.
	 *
	 * Falls back to native post meta if ACF is unavailable.
	 */
	private static function get_field_value( $field_name, $post_id ) {
		if ( function_exists( 'get_field' ) ) {
			return get_field( $field_name, $post_id );
		}

		return get_post_meta( $post_id, $field_name, true );
	}

	/**
	 * Sanitize A-Z or #.
	 */
	private static function sanitize_letter( $letter ) {
		$letter = strtoupper( trim( sanitize_text_field( $letter ) ) );

		if ( '#' === $letter ) {
			return '#';
		}

		if ( preg_match( '/^[A-Z]$/', $letter ) ) {
			return $letter;
		}

		return '';
	}

	/**
	 * Build a simple placeholder from the first two words.
	 */
	private static function get_placeholder_text( $title ) {
		$words = preg_split( '/\s+/', trim( wp_strip_all_tags( $title ) ) );
		$text  = '';

		foreach ( array_slice( $words, 0, 2 ) as $word ) {
			if ( '' !== $word ) {
				$text .= strtoupper( substr( $word, 0, 1 ) );
			}
		}

		return $text ?: '–';
	}

	/**
	 * Prepare telephone value for a tel: URL.
	 */
	private static function clean_phone_link( $phone ) {
		return preg_replace( '/[^0-9+]/', '', $phone );
	}
}

Members_Ajax::init();