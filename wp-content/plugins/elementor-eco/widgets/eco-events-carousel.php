<?php
if (! defined('ABSPATH')) exit;

use Elementor\Controls_Manager;
use Elementor\Widget_Base;

class ECO_Events_Carousel_Widget extends Widget_Base {

  public function get_name()        { return 'eco-events-carousel'; }
  public function get_title()       { return 'ECO – Events Carousel'; }
  public function get_icon()        { return 'eicon-slider-push'; }
  public function get_categories()  { return ['general']; }
  public function get_style_depends(){ return ['eco-events-carousel-style']; }
  public function get_script_depends(){ return ['eco-events-carousel-script']; }

  protected function register_controls() {

    $this->start_controls_section('content', ['label' => 'Content']);

    // Manual event-category filter (takes priority over ACF term field when set)
    $this->add_control('manual_event_category', [
      'label'       => 'Filter (event-category) – manual',
      'type'        => \Elementor\Controls_Manager::SELECT2,
      'multiple'    => true,
      'label_block' => true,
      'default'     => [],
      'options'     => $this->ecocar_event_category_options(),
      'description' => 'If you pick categories here, they override the ACF term field (event_cats_in_cats).',
    ]);


    $this->add_control('posts_per_page', [
      'label' => 'How many',
      'type' => Controls_Manager::NUMBER,
      'default' => 6,
      'min' => 1, 'max' => 30, 'step' => 1,
    ]);

    $this->add_control('upcoming_only', [
      'label' => 'Only upcoming',
      'type' => Controls_Manager::SWITCHER,
      'label_on' => 'Yes',
      'label_off'=> 'No',
      'return_value' => 'yes',
      'default' => 'yes',
    ]);

    $this->add_control('orderby', [
      'label' => 'Order by',
      'type' => Controls_Manager::SELECT,
      'default' => 'start_date',
      'options' => [
        'start_date' => 'Start date',
        'title'      => 'Title',
        'date'       => 'Published date',
      ],
    ]);

    $this->add_control('order', [
      'label' => 'Order',
      'type' => Controls_Manager::SELECT,
      'default' => 'ASC',
      'options' => [
        'ASC'  => 'ASC',
        'DESC' => 'DESC',
      ],
    ]);

    $this->end_controls_section();

    $this->start_controls_section('carousel', ['label' => 'Carousel']);
    $this->add_control('show_arrows', [
      'label' => 'Show arrows',
      'type' => Controls_Manager::SWITCHER,
      'return_value' => 'yes',
      'default' => 'yes',
    ]);
    $this->add_control('show_dots', [
      'label' => 'Show dots',
      'type' => Controls_Manager::SWITCHER,
      'return_value' => 'yes',
      'default' => 'no',
    ]);
    $this->add_control('slides_per_view', [
      'label' => 'Slides per view (desktop)',
      'type' => Controls_Manager::NUMBER,
      'default' => 2,
      'min' => 1, 'max' => 4,
    ]);
    $this->add_control('space_between', [
      'label' => 'Space between (px)',
      'type' => Controls_Manager::NUMBER,
      'default' => 24,
      'min' => 0, 'max' => 60,
    ]);
    $this->end_controls_section();
  }

  protected function render() {
    $s = $this->get_settings_for_display();
    $attrs = [
      'data-count'   => (int)$s['posts_per_page'],
      'data-upcoming'=> ($s['upcoming_only']==='yes') ? '1' : '0',
      'data-orderby' => esc_attr($s['orderby']),
      'data-order'   => esc_attr($s['order']),
      'data-arrows'  => ($s['show_arrows']==='yes') ? '1' : '0',
      'data-dots'    => ($s['show_dots']==='yes') ? '1' : '0',
      'data-slides'  => max(1,(int)$s['slides_per_view']),
      'data-space'   => max(0,(int)$s['space_between']),
      'data-cats'   => esc_attr( implode(',', array_filter((array) (get_field('event_cats_in_cats') ?: []))) ),
    ];

    // --- Category filtering resolution ---
    // Priority 1: Elementor control "manual_event_category"
    $s = $this->get_settings_for_display();
    $cats_ids = array_filter(array_map('intval', (array)($s['manual_event_category'] ?? [])));

    if (empty($cats_ids)) {
      // Priority 2: ACF field on the current queried *term* (archive pages)
      $qo = get_queried_object();
      if ($qo && isset($qo->taxonomy, $qo->term_id)) {
        // Try passing the term object first
        $acf_val = get_field('event_cats_in_cats', $qo);
        if (empty($acf_val)) {
          // Fallback explicit "taxonomy_termId"
          $acf_val = get_field('event_cats_in_cats', "{$qo->taxonomy}_{$qo->term_id}");
        }
        if (!empty($acf_val)) {
          $cats_ids = array_map(function($t){
            return is_object($t) ? (int)$t->term_id : (int)$t;
          }, (array)$acf_val);
          $cats_ids = array_filter($cats_ids);
        }
      }
    }

    // Attach as data attribute for JS → AJAX
    $attrs['data-cats'] = esc_attr(implode(',', $cats_ids));

    $attr_html = '';
    foreach ($attrs as $k=>$v) { $attr_html .= sprintf(' %s="%s"', $k, esc_attr($v)); }

    echo '<div class="eco-events-carousel"'.$attr_html.'>';
    echo '  <div class="swiper">';
    echo '    <div class="swiper-wrapper">';
    echo '      <div class="swiper-slide"><div class="event-item" style="padding:24px;text-align:center;"><span class="event-loading">'.esc_html__('Lade Veranstaltungen...','eco').'</span></div></div>';
    echo '    </div>';
    echo '    <div class="swiper-pagination"></div>';
    if (($s['show_arrows'] ?? 'yes') === 'yes') {
      echo '    <div class="swiper-button-prev"></div>';
      echo '    <div class="swiper-button-next"></div>';
    }
    echo '  </div>';
    echo '</div>';
  }

  private function ecocar_event_category_options(): array {
    $opts  = [];
    $terms = get_terms([
      'taxonomy'   => 'event-category',
      'hide_empty' => false,
    ]);
    if (!is_wp_error($terms)) {
      foreach ($terms as $t) {
        $opts[$t->term_id] = $t->name;
      }
    }
    return $opts;
  }

}
