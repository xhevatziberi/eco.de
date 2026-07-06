<?php
namespace ElementorEco\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;

if (!defined('ABSPATH')) exit;

class PodcastRss extends Widget_Base {

	public function get_name() {
		return 'podcast-rss';
	}

	public function get_title() {
		return __('Podcast RSS Feed', 'elementor-eco');
	}

	public function get_icon() {
		return 'eicon-post-list';
	}

	public function get_categories() {
		return ['eco'];
	}

    public function get_script_depends() {
        return ['eco-podcast-rss-script'];
    }

    public function get_style_depends() {
        return ['eco-podcast-rss-style'];
    }

	protected function register_controls() {
		$this->start_controls_section('content_section', [
			'label' => __('Feed Settings', 'elementor-eco')
		]);

		$this->add_control('rss_url', [
			'label' => __('RSS Feed URL', 'elementor-eco'),
			'type' => Controls_Manager::TEXT,
			'placeholder' => 'https://example.com/feed.xml',
			'default' => 'https://4ew3sj.podcaster.de/ecoverband.rss'
		]);

		$this->add_control('title', [
			'label' => __('Section Title', 'elementor-eco'),
			'type' => Controls_Manager::TEXT,
			'default' => __('Latest Episodes', 'elementor-eco')
		]);

		$this->add_control('limit', [
			'label' => __('Number of Episodes', 'elementor-eco'),
			'type' => Controls_Manager::NUMBER,
			'default' => 5,
			'min' => 1,
			'max' => 20
		]);

		$this->end_controls_section();
	}

	protected function render() {
		$settings = $this->get_settings_for_display();
		$rss_url = esc_url($settings['rss_url']);
		$title = esc_html($settings['title']);
		$limit = intval($settings['limit']);

		if (empty($rss_url)) {
			esc_html_e( 'No RSS feed URL provided.', 'elementor-eco' );
			return;
		}

		$rss = fetch_feed($rss_url);

		if (is_wp_error($rss)) {
			esc_html_e( 'Failed to fetch RSS feed.', 'elementor-eco' );
			return;
		}

		$items = $rss->get_items(0, $limit);

		if (empty($items)) {
			esc_html_e( 'No episodes found.', 'elementor-eco' );
			return;
		}

		echo '<div class="eco-podcast-rss">';
        echo "<h4>{$title}</h4>";
        echo '<ul class="eco-podcast-list">';

        foreach ($items as $item) {
            $link       = esc_url($item->get_permalink());
            $item_title = esc_html($item->get_title());
            $date       = $item->get_date('d.m.Y');

            // Get image
            $itunes_image = $item->get_item_tags('http://www.itunes.com/dtds/podcast-1.0.dtd', 'image');
            $image_url = (!empty($itunes_image) && isset($itunes_image[0]['attribs']['']['href']))
                ? esc_url($itunes_image[0]['attribs']['']['href'])
                : 'https://via.placeholder.com/60';

            // Get audio file
            $enclosure = $item->get_enclosure();
            $audio_url = $enclosure ? esc_url($enclosure->get_link()) : '';

            echo "<li>
                <div class=\"eco-podcast-item\">
                    <img src=\"{$image_url}\" alt=\"\" class=\"eco-podcast-thumb\">
                    <div class=\"eco-podcast-meta\">
                        <a href=\"{$link}\" target=\"_blank\" rel=\"noopener\"><strong>{$item_title}</strong></a>
                        <small>{$date}</small>
                    </div>";

            if ($audio_url) {
                echo "<button class=\"eco-podcast-play\" data-audio=\"{$audio_url}\" aria-label=\"" . esc_attr__('Play episode', 'elementor-eco') . "\"><i class=\"fas fa-play\"></i></button>";
            }

            echo "</div>
                </li>";
        }


        echo '</ul>';
        echo '</div>';

	}
}
