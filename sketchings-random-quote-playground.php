<?php
/*
Plugin Name:  WP Random Quote Playground
Plugin URI:   https://github.com/sketchings/wp-random-quote-playground
Description:  WordPress Random Quote Plugin For Play and Experimentation
Version:      1.0
Author:       Alena Holligan
Author URI:   https://sketchings.com/
License:      MIT
License URI:  https://opensource.org/licenses/MIT
*/

/*
 * Quote Data
 */
function sketchings_quotes ( $category = null )
{
    // array of morning motivational quotes
    $motivation = array(
        "You don’t have to be great to start, but you have to start to be great. — Zig Ziglar",
        "Set a goal that makes you want to jump out of bed in the morning.",
        "You’ll never change your life until you change something you do daily. The secret of your success is found in your daily routine. – John C. Maxwell",
        "This is not just another day, this is yet another chance to make your dreams come true.",
        "Your future is created by what you do today, not tomorrow. — Robert Kiyosaki",
        "Leave your ego at the door every morning, and just do some truly great work. Few things will make you feel better than a job brilliantly done. – Robin S. Sharma",
    );

    // array of evening encouragement quotes
    $encouragement = [
        "Night time is really the best time to work. All the ideas are there to be yours because everyone else is asleep. - Catherine O'Hara",
        "May you dream of lovely things and to find them real.",
        "Always end the day with a positive thought. No matter how hard things were, tomorrow’s a fresh opportunity to make it better.",
        "Wake up with determination. Go to bed with satisfaction.",
        "Always remember to fall asleep with a dream and wake up with a purpose.",
        "Sleep is the best meditation. - Dalai Lama",
    ];

    if ( in_array(strtolower(trim($category)), array('motivation', 'motivated', 'motivate')) ) {
        return $motivation;
    }

    if ( in_array(strtolower(trim($category)), array('encouragement', 'encouraged', 'encourage', 'encouraging')) ) {
        return $encouragement;
    }

    return array_merge($motivation, $encouragement);
}

/*
 * Return a random type based on category or time of day
 */
function sketchings_random_quote ( $category = null )
{
    $hour = date('H');
    if (!empty($category)) {
        $quotes = sketchings_quotes($category);
    } elseif ($hour > 4 && $hour < 17) {
        $quotes = sketchings_quotes('motivation');
    } else {
        $quotes = sketchings_quotes('encouragement');
    }
    $index = array_rand($quotes);
    return $quotes[$index];
}

/*
 * Add random quote to the bottom of each page
 */
function sketchings_quote () {
    echo '<h3>Random Quote</h3>';
    echo '<blockquote>' . sketchings_random_quote() . '</blockquote>';
}
add_action( 'loop_end', 'sketchings_quote' );


/*
 * Tool tip styles
 * Proper way to enqueue scripts and styles
 */
function sketchings_random_quote_styles() {
    wp_register_style( 'quote-styles',  plugin_dir_url( __FILE__ ) . 'css/styles.css' );
    wp_enqueue_style( 'quote-styles' );
}
add_action( 'wp_head', 'sketchings_random_quote_styles' );

/*
 * Show tool tip for certain words
 */
function sketchings_tooltip($content) {
    $search = array(
            ' motivation',
            ' motivate',
            ' encouragement',
            ' encourage',
            ' encouraging',
        );
    foreach ($search as $word) {
        $content = str_ireplace(
            $word,
            ' <span class="tooltip">' . trim($word) . '<span class="tooltiptext">'
            . sketchings_random_quote($word)
            . '</span></span>',
            $content
        );
    }
    return $content;
}
add_filter ('the_content', 'sketchings_tooltip');

/*
 * Add shortcode to add quote to widget or post
 */
function sketchings_random_quote_shortcode( $atts ) {
    return sketchings_random_quote( $atts['category'] );
}
add_shortcode( 'random-quote', 'sketchings_random_quote_shortcode' );