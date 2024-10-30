<?php

namespace MDT\Google_News;

/**
 * Class Rewrite
 *
 * Categories:
 *
 * /category/{slug|slug/slug}/feed/google-news/
 *
 * Post_tag:
 *
 * /tag/{slug}/feed/google-news/
 *
 * @package MDT\Google_News
 */
class Rewrite {

	/**
	 * Google News feed slug.
	 *
	 * @var string
	 */
	const FEED_SLUG = 'google-news';

	/**
	 * Rewrite constructor.
	 */
	public function __construct() {
		add_action( 'init', [ $this, 'add_feed' ]);
	}

	/**
	 * Add the feed.
	 */
	public function add_feed() {
		add_feed( self::FEED_SLUG, [ $this, 'render' ] );
	}

	/**
	 * Loads the correct template for our feed
	 */
	public static function render() {
		if ( self::FEED_SLUG !== get_query_var( 'feed' ) ) {
			return;
		}

		include( ABSPATH . WPINC . '/feed-rss2.php' );
	}
}

new Rewrite();
