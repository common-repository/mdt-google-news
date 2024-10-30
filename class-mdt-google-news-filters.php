<?php

namespace MDT\Google_News;

/**
 * Class Filters
 *
 * Filters and modifies the content of the Google News feed for compatibility
 *
 * @package MDT\Google_News
 */
class Filters {

	/**
	 * Filters constructor.
	 */
	public function __construct() {
		add_action( 'template_redirect', [ $this, 'add_actions_filters' ] );
		add_filter( 'option_posts_per_rss', [ __CLASS__, 'posts_per_rss' ], 99 );
	}

	/**
	 *  Binds the filters/actions for the feed content. Will only run if it's
	 * the google-news feed query.
	 */
	public function add_actions_filters() {
		if ( ! is_feed( Rewrite::FEED_SLUG ) ) {
			return;
		}

		/**
		 * Run an action before starting to render the feed.
		 */
		do_action( 'mdt_google_news_init' );

		add_action( 'rss2_item', [ $this, 'remove_actions' ], 9, 0 );
		add_action( 'rss2_item', [ $this, 'add_featured_image' ], 10, 0 );
	}

	/**
	 * Pulls the existing methods that mrss to feeds.
	 */
	public function remove_actions() {
		remove_action( 'rss2_item', 'mrss_item', 10, 0 );
	}

	/**
	 * Adds a new media:content tag for the featured images, since we pulled the actions that would have done it.
	 */
	public function add_featured_image() {
		$featured_image = get_post_thumbnail_id( get_the_ID() );
		$image_details  = get_post( $featured_image );
		if ( $featured_image && is_object( $image_details ) ) {
			$img_attr = wp_get_attachment_image_src( $image_details->ID, 'full' );
			if ( is_array( $img_attr ) ) {
				echo sprintf(
					'<media:content url="%s" type="%s" medium="image" width="%s" height="%s"></media:content>',
					esc_url( $img_attr[0] ),
					esc_attr( $image_details->post_mime_type ),
					esc_attr( $img_attr[1] ),
					esc_attr( $img_attr[2] )
				);
			}
		}
	}

	/**
	 * @param $count
	 *
	 * @return int
	 */
	public static function posts_per_rss( $count ) {
		if ( is_feed( Rewrite::FEED_SLUG ) ) {

			/**
			 * Filter the posts per page for the Google News feed
			 *
			 * @param int
			 */
			$count = apply_filters( 'mdt_google_news_posts_per_rss', 100 );
		}

		return $count;
	}
}

new Filters();
