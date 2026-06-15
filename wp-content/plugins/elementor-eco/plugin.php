<?php
namespace ElementorEco;

/**
 * Class Plugin
 *
 * Main Plugin class
 * @since 1.2.2
 */
class Plugin {

	/**
	 * Instance
	 *
	 * @since 1.2.0
	 * @access private
	 * @static
	 *
	 * @var Plugin The single instance of the class.
	 */
	private static $_instance = null;

	/**
	 * Instance
	 *
	 * Ensures only one instance of the class is loaded or can be loaded.
	 *
	 * @since 1.2.0
	 * @access public
	 *
	 * @return Plugin An instance of the class.
	 */
	public static function instance() {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}

	/**
	 * widget_scripts
	 *
	 * Load required plugin core files.
	 *
	 * @since 1.2.0
	 * @access public
	 */
	public function widget_scripts() {
		$version = '1.2.12';
		wp_register_script( 'eco-widget-page', plugins_url( basename( __DIR__ ) . '/assets/js/page.js' ), [ 'elementor-frontend' ], $version, true );

		wp_register_style( 'eco-events-style', plugins_url( '/assets/css/events.css', __FILE__  ), [], $version );
		wp_register_script( 'eco-events-script', plugins_url( '/assets/js/events.js', __FILE__ ), [ 'jquery' ], $version, true );
		wp_localize_script( 'eco-events-script', 'ecoEvents', [
			'ajaxurl' => admin_url( 'admin-ajax.php' ),
			'nonce'   => wp_create_nonce( 'eco_events_nonce' ),
		] );
		wp_register_script( 'eco-members-script', plugins_url( '/assets/js/members.js', __FILE__ ), [ 'jquery' ], $version, true );
		wp_register_style( 'eco-members-style', plugins_url( '/assets/css/members.css', __FILE__  ), [], $version );

		wp_register_script( 'eco-people-script', plugins_url( '/assets/js/people.js', __FILE__ ), [ 'jquery' ], $version, true );
		wp_register_style( 'eco-people-style', plugins_url( '/assets/css/people.css', __FILE__  ), [], $version );

		wp_register_script( 'eco-logo-cloud-script', plugins_url( '/assets/js/logo-cloud.js', __FILE__ ), [ 'jquery' ], $version, true );
		wp_register_style( 'eco-logo-cloud-style', plugins_url( '/assets/css/logo-cloud.css', __FILE__  ), [], $version );

		wp_register_script( 'eco-podcast-script', plugins_url( '/assets/js/podcast-player.js', __FILE__ ), [ 'jquery' ], $version, true );
		wp_register_style( 'eco-podcast-style', plugins_url( '/assets/css/podcast-player.css', __FILE__  ), [], $version );

		wp_register_script( 'eco-podcast-rss-script', plugins_url( '/assets/js/podcast-rss.js', __FILE__ ), [ 'jquery' ], $version, true );
		wp_register_style( 'eco-podcast-rss-style', plugins_url( '/assets/css/podcast-rss.css', __FILE__  ), [], $version );

		wp_register_script( 'elementor-eco-script', plugins_url( '/assets/js/script.js', __FILE__ ), [ 'jquery' ], $version, true );
		wp_register_style( 'elementor-eco-style', plugins_url( '/assets/css/style.css', __FILE__  ), [], $version );

		wp_enqueue_script( 'elementor-eco-script' );
		wp_enqueue_style( 'elementor-eco-style' );

		// Localization
		wp_localize_script('eco-events-script', 'ecoEventsL10n', [
			'loading_events'      => __('Lade Veranstaltungen...', 'elementor-eco'),
			'loading_month'       => __('Lade Monatsübersicht...', 'elementor-eco'),
			'no_events_month' => __('Keine Veranstaltungen in diesem Monat.', 'elementor-eco'),
			'no_events_today' => __('Heute keine Veranstaltungen.', 'elementor-eco'),
			'no_events'     => __('Keine Veranstaltungen gefunden.', 'elementor-eco'),
			'error_loading'        => __('Fehler beim Laden der Veranstaltungen. Bitte später erneut versuchen.', 'elementor-eco'),
			'monthOverview'=> __('Monatsübersicht', 'elementor-eco'),
			'today'        => __('Heute', 'elementor-eco'),
			'thisMonth'    => __('Diesen Monat', 'elementor-eco'),
			'all'          => __('Alle', 'elementor-eco'),
			'more_info'    => __('Mehr Infos', 'elementor-eco'),
			'ticket_shop'  => __('Zum Ticketshop', 'elementor-eco'),
			'past_event'   => __('Vergangene Veranstaltung', 'elementor-eco'),
		]);

		wp_register_script( 'eco-events-carousel-script', plugins_url( '/assets/js/events-carousel.js', __FILE__ ), [ 'jquery' ], $version, true );
		wp_register_style( 'eco-events-carousel-style', plugins_url( '/assets/css/events-carousel.css', __FILE__  ), [], $version );
		wp_localize_script('eco-events-carousel-script', 'ecoEventsCarousel', [
			'ajaxurl' => admin_url('admin-ajax.php'),
			'loading' => __('Lade Veranstaltungen...', 'elementor-eco'),
			'more_info' => __('Mehr Infos', 'elementor-eco'),
			'ticket_shop' => __('Zum Ticketshop', 'elementor-eco'),
			'empty' => __('Keine Veranstaltungen gefunden.', 'elementor-eco'),
			'error' => __('Fehler beim Laden.', 'elementor-eco'),
		]);

		wp_register_script( 'eco-people-vertical-script', plugins_url( '/assets/js/people-vertical.js', __FILE__ ), [ 'jquery' ], $version, true );
		wp_register_style( 'eco-people-vertical-style', plugins_url( '/assets/css/people-vertical.css', __FILE__  ), [], $version );

		wp_register_style( 'eco-downloads-style', plugins_url( '/assets/css/downloads.css', __FILE__  ), [], $version );
		wp_register_style( 'eco-tile-icon-grid-style', plugins_url( '/assets/css/tile-icon-grid.css', __FILE__ ), [], $version );
		wp_register_style( 'eco-number-box-style', plugins_url( '/assets/css/number-box.css', __FILE__ ), [], $version );

		wp_register_style( 'eco-logo-grid-style', plugins_url( '/assets/css/logo-grid.css', __FILE__ ), [], $version );
		wp_register_script( 'eco-logo-grid-script', plugins_url( '/assets/js/logo-grid.js', __FILE__ ), [], $version, true );

		wp_register_style( 'eco-content-cards-style', plugins_url( '/assets/css/content-cards.css', __FILE__ ), [], $version );
		wp_register_script( 'eco-content-cards-script', plugins_url( '/assets/js/content-cards.js', __FILE__ ), [ 'jquery' ], $version, true );
		wp_localize_script(
			'eco-content-cards-script',
			'ecoContentCards',
			[
				'ajaxurl' => admin_url( 'admin-ajax.php' ),
				'nonce'   => wp_create_nonce( 'eco_content_cards_nonce' ),
			]
		);

		wp_register_style( 'eco-featured-slider-style', plugins_url( '/assets/css/featured-slider.css', __FILE__ ), [], $version );
		wp_register_script( 'eco-featured-slider-script', plugins_url( '/assets/js/featured-slider.js', __FILE__ ), [], $version, true );

		wp_register_style( 'eco-tile-feature-list-style', plugins_url( '/assets/css/tile-feature-list.css', __FILE__ ), [], $version );

		wp_register_style( 'eco-eyebrow-heading-style', plugins_url( '/assets/css/eyebrow-heading.css', __FILE__ ), [], $version );

		wp_register_style( 'eco-event-calendar-style', plugins_url( '/assets/css/event-calendar.css', __FILE__ ), [], $version );
		wp_register_script( 'eco-event-calendar-script', plugins_url( '/assets/js/event-calendar.js', __FILE__ ), [ 'jquery' ], $version, true );
		wp_localize_script('eco-event-calendar-script', 'ecoEventCalendar', [
			'ajaxUrl' => admin_url( 'admin-ajax.php' ),
		]);

		wp_register_style( 'eco-partner-logos-style', plugins_url( '/assets/css/partner-logos.css', __FILE__ ), [], $version );
		wp_register_script( 'eco-partner-logos-script', plugins_url( '/assets/js/partner-logos.js', __FILE__ ), [ 'jquery' ], $version, true );

		wp_register_style( 'eco-member-carousel-style', plugins_url( '/assets/css/member-carousel.css', __FILE__ ), [], $version );
		wp_register_script( 'eco-member-carousel-script', plugins_url( '/assets/js/member-carousel.js', __FILE__ ), [ 'jquery' ], $version, true );


		wp_register_style( 'eco-testimonial-showcase', plugins_url( '/assets/css/testimonial-showcase.css', __FILE__ ), [], $version );
		wp_register_script( 'eco-testimonial-showcase', plugins_url( '/assets/js/testimonial-showcase.js', __FILE__ ), [ 'jquery' ], $version, true );

		wp_register_style( 'eco-benefit-card-style', plugins_url( '/assets/css/benefit-card.css', __FILE__ ), [], $version );
	}

	/**
	 * Include Widgets files
	 *
	 * Load widgets files
	 *
	 * @since 1.2.0
	 * @access private
	 */
	private function include_widgets_files() {
		// common
		// require_once( __DIR__ . '/widgets/common/gallery.php' );
		// require_once( __DIR__ . '/widgets/common/breadcrumbs.php' );
		require_once( __DIR__ . '/widgets/common/address.php' );
		require_once( __DIR__ . '/widgets/events.php' );
		require_once( __DIR__ . '/widgets/members.php' );
		require_once( __DIR__ . '/widgets/people.php' );
		require_once( __DIR__ . '/widgets/logo-cloud.php' );
		require_once( __DIR__ . '/widgets/podcast-player.php' );
		require_once( __DIR__ . '/widgets/podcast-rss.php' );
		require_once( __DIR__ . '/widgets/eco-events-carousel.php' );
		require_once( __DIR__ . '/widgets/people-vertical.php' );
		require_once( __DIR__ . '/widgets/downloads.php' );
		require_once( __DIR__ . '/widgets/tile-icon-grid.php' );
		require_once( __DIR__ . '/widgets/number-box.php' );
		require_once( __DIR__ . '/widgets/logo-grid.php' );
		require_once( __DIR__ . '/widgets/content-cards.php' );
		require_once( __DIR__ . '/widgets/featured-slider.php' );
		require_once( __DIR__ . '/widgets/tile-feature-list.php' );
		require_once( __DIR__ . '/widgets/eyebrow-heading.php' );
		require_once( __DIR__ . '/widgets/event-calendar.php' );
		require_once( __DIR__ . '/widgets/partner-logos.php' );
		require_once( __DIR__ . '/widgets/member-carousel.php' );
		require_once( __DIR__ . '/widgets/testimonial-showcase.php' );
		require_once( __DIR__ . '/widgets/benefit-card.php' );
	}

	/**
	 * Register Widgets
	 *
	 * Register new Elementor widgets.
	 *
	 * @since 1.2.0
	 * @access public
	 */
	public function register_widgets() {
		// Its is now safe to include Widgets files
		$this->include_widgets_files();

		// Register Widgets
		// \Elementor\Plugin::instance()->widgets_manager->register_widget_type( new Widgets\Gallery() );
		// \Elementor\Plugin::instance()->widgets_manager->register_widget_type( new Widgets\Breadcrumbs() );
		\Elementor\Plugin::instance()->widgets_manager->register_widget_type( new Widgets\Address() );
		\Elementor\Plugin::instance()->widgets_manager->register_widget_type( new Widgets\EventList() );
		\Elementor\Plugin::instance()->widgets_manager->register_widget_type( new Widgets\Members() );
		\Elementor\Plugin::instance()->widgets_manager->register_widget_type( new Widgets\People() );
		\Elementor\Plugin::instance()->widgets_manager->register_widget_type( new Widgets\LogoCloud() );
		\Elementor\Plugin::instance()->widgets_manager->register_widget_type( new Widgets\PodcastPlayer() );
		\Elementor\Plugin::instance()->widgets_manager->register_widget_type( new Widgets\PodcastRSS() );
		\Elementor\Plugin::instance()->widgets_manager->register_widget_type( new \ECO_Events_Carousel_Widget() );
		\Elementor\Plugin::instance()->widgets_manager->register_widget_type( new Widgets\PeopleVertical() );
		\Elementor\Plugin::instance()->widgets_manager->register_widget_type( new Widgets\Downloads() );
		\Elementor\Plugin::instance()->widgets_manager->register_widget_type( new Widgets\TileIconGrid() );
		\Elementor\Plugin::instance()->widgets_manager->register_widget_type( new Widgets\NumberBox() );
		\Elementor\Plugin::instance()->widgets_manager->register_widget_type( new Widgets\LogoGrid() );
		\Elementor\Plugin::instance()->widgets_manager->register_widget_type( new Widgets\ContentCards() );
		\Elementor\Plugin::instance()->widgets_manager->register_widget_type( new Widgets\FeaturedSlider() );
		\Elementor\Plugin::instance()->widgets_manager->register_widget_type( new Widgets\TileFeatureList() );
		\Elementor\Plugin::instance()->widgets_manager->register_widget_type( new Widgets\EyebrowHeading() );
		\Elementor\Plugin::instance()->widgets_manager->register_widget_type( new Widgets\EventCalendar() );
		\Elementor\Plugin::instance()->widgets_manager->register_widget_type( new Widgets\PartnerLogos() );
		\Elementor\Plugin::instance()->widgets_manager->register_widget_type( new Widgets\MemberCarousel() );
		\Elementor\Plugin::instance()->widgets_manager->register_widget_type( new Widgets\TestimonialShowcase() );
		\Elementor\Plugin::instance()->widgets_manager->register_widget_type( new Widgets\BenefitCard() );
	}

	function add_elementor_widget_categories( $elements_manager ) {

		$elements_manager->add_category(
			'eco',
			[
				'title' => __( 'Eco Widgets', 'elementor-eco' ),
				'icon' => 'eicon-star',
			]
		);
	}

	function add_elementor_page_settings_controls( \Elementor\Core\DocumentTypes\PageBase $page ) {
		$page->start_controls_section(
			'page_section',
			[
				'label' => __( 'Eco', 'elementor' ),
				'tab' => \Elementor\Controls_Manager::TAB_SETTINGS,
			]
		);
		$page->end_controls_section();
	}

	/**
	 *  Plugin class constructor
	 *
	 * Register plugin action hooks and filters
	 *
	 * @since 1.2.0
	 * @access public
	 */
	public function __construct() {
		require_once __DIR__ . '/inc/members-ajax.php';
		
		require_once __DIR__ . '/inc/content-cards-ajax.php';

		require_once __DIR__ . '/inc/event-calendar-ajax.php';
		\ElementorEco\EventCalendarAjax::init();

		// Register widget scripts
		add_action( 'elementor/frontend/after_register_scripts', [ $this, 'widget_scripts' ] );

		// Register widgets
		add_action( 'elementor/widgets/widgets_registered', [ $this, 'register_widgets' ] );

		// Wiget Categories
		add_action( 'elementor/elements/categories_registered', [ $this, 'add_elementor_widget_categories' ] );

		// Add page settings
		add_action( 'elementor/element/post/document_settings/after_section_end', [ $this, 'add_elementor_page_settings_controls' ], 10, 2 );
	}
}

// Instantiate Plugin Class
Plugin::instance();
