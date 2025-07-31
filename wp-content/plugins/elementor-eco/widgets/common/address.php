<?php
namespace ElementorEco\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Repeater;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Address extends Widget_Base {

	/**
	 * Retrieve the widgset name.
	 *
	 * @since 1.0.0
	 *
	 * @access public
	 *
	 * @return string Widget name.
	 */
	public function get_name() {
		return 'address';
	}

	public function get_title() {
		return __( 'Address', 'elementor-eco' );
	}

	public function get_icon() {
		return 'eicon-ellipsis-h';
	}

	public function get_categories() {
		return [ 'eco' ];
	}

	public function __construct($data = [], $args = null) {
      parent::__construct($data, $args);

      //
   }

	public function get_script_depends() {
		// return [ 'eco-heading-script' ];
		return [ 'eco' ];
	}

	protected function register_controls() {
		$this->start_controls_section(
			'section_content',
			[
				'label' => __( 'Section Content', 'elementor-eco' ),
			]
		);

		$this->add_control(
			'title',
			[
				'label' => __( 'Title', 'elementor-eco' ),
				'type' => Controls_Manager::TEXT,
				'default' => __( 'Title' , 'elementor-eco' ),
			]
		);

		// $this->add_control(
		// 	'content',
		// 	[
		// 		'label' => __( 'Content', 'elementor-eco' ),
		// 		'type' => Controls_Manager::TEXTAREA,
		// 		'default' => __( 'Content' , 'elementor-eco' ),
		// 	]
		// );

		// $this->add_control(
		// 	'image',
		// 	[
		// 		'label' => __( 'Image', 'elementor-eco' ),
		// 		'type' => Controls_Manager::MEDIA,
		// 		'default' => [
		// 			'url' => '',
		// 		],
		// 	]
		// );

		$this->add_control(
			'list',
			[
				'label' => esc_html__( 'Repeater List', 'elementor-eco' ),
				'type' => Controls_Manager::REPEATER,
				'fields' => [
					[
						'name' => 'list_title',
						'label' => esc_html__( 'Title', 'elementor-eco' ),
						'type' => Controls_Manager::TEXT,
						'default' => esc_html__( 'List Title' , 'elementor-eco' ),
						'label_block' => true,
					],
					[
						'name' => 'list_label',
						'label' => esc_html__( 'Label', 'elementor-eco' ),
						'type' => Controls_Manager::TEXT,
						'default' => esc_html__( 'List Label' , 'elementor-eco' ),
						'label_block' => true,
					],
				],
				'default' => [
					[
						'list_title' => esc_html__( 'Title #1', 'elementor-eco' ),
					],
				],
				'title_field' => '{{{ list_title }}}',
			]
		);

		$this->end_controls_section();
	}

	protected function render2() {
		$settings = $this->get_settings_for_display();

		echo '<div class="qodef-shortcode qodef-m  qodef-custom-list">';
			echo '<div class="qodef-custom-list-title">';
				echo '<h4 class="qodef-m-title">' . $settings['title'] . '</h4>';
				
			echo '</div>';

			echo '<div class="qodef-custom-list-items">';
			foreach ( $settings['list'] as $item ) {
				echo '<div class="qodef-custom-list-item">';
					echo '<span class="qodef-m-text">' . $item['list_title'] . '</span>';
					echo '<span class="qodef-m-label">' . $item['list_label'] . '</span>';
				echo '</div>';
			}
			echo '</div>';
		echo '</div>';
		
		?>

		<?php
	}

	protected function render() {
		$settings = $this->get_settings_for_display();
		?>

		<div class="qodef-shortcode qodef-m  qodef-custom-list">
			<div class="qodef-custom-list-title">
				<h5 class="qodef-m-title"><?php echo $settings['title']; ?></h5>
				
			</div>

			<div class="qodef-custom-list-items">
			<?php
			foreach ( $settings['list'] as $item ) { ?>
				<div class="qodef-custom-list-item copy-text">
					<div>
						<input type="text" class="qodef-m-text" value="<?php echo $item['list_title']; ?>" />
						<span class="qodef-m-label"><?php echo $item['list_label']; ?></span>
					</div>
					<button><i class="fa fa-clone"></i></button>
				</div>
			<?php
			}
			?>
			</div>
		</div>

		<?php
	}

	protected function content_template() {
		?>
		<div class="qodef-shortcode qodef-m  qodef-custom-list">
			<div class="qodef-custom-list-title">
				<h5 class="qodef-m-title">{{{ settings.title }}}</h5>
				
			</div>

			<div class="qodef-custom-list-items">
			<# _.each( settings.list, function( item ) { #>
				<div class="qodef-custom-list-item copy-text">
					<div>
						<input type="text" class="qodef-m-text" value="{{ item.list_title }}" />
						<span class="qodef-m-label">{{ item.list_label }}</span>
					</div>
					<button><i class="fa fa-clone"></i></button>
				</div>
			<# }); #>
			</div>
		</div>
		
		<?php
	}
}
