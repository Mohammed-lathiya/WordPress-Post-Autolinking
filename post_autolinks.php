<?php
if (!class_exists('Ck_autolinking_add')) {
    class Ck_autolinking_add
    {	

        public function __construct()
        {
            $this->hooks();
        }

        public function hooks()
        {

            add_filter('the_content', array($this, 'ck_the_content_handler'));
        }

        public function get_posts()
        {
            $results    = [];

            $query_args = [
                'post_type'              => ['pauple_helpie'],
                'post_status'            => ['publish'],
                'orderby'                => 'menu_order',
                'no_found_rows'          => true,
                'update_post_meta_cache' => false,
                'update_post_term_cache' => false,
                'posts_per_page' => -1,
            ];
            $query = new \WP_Query($query_args);

            if ($query->have_posts()) {
                while ($query->have_posts()) {
                    // Optionally, pick parts of the post and create a custom collection.
                    $query->the_post();
                    $results[] = get_post();
                }
                wp_reset_postdata();
                return $results;
            }
        }

        public function ck_the_content_handler($content)
        {
            if (!$this->is_post_type()) {
                return $content;
            }
            // $posts_array = get_helpie_kb_articles();
            $posts_array = $this->get_posts();
            $content = $this->execute($content, $posts_array);
            
            return $content;
        }

        /* Protected Methods */
        protected function is_post_type()
        {
            $post_type = 'pauple_helpie';
            global $post;
            if ($post->post_type == $post_type) {
                return true;
            }

            return false;
        }

       


        public function execute($content, $posts)
        {

			      $input = $content;
            // $words = $all_post_titles;

            $matches = array();
            // loop through words to find the closest
            foreach ($posts as $post) {
				
                $title = $post->post_title;
                if (!isset($title) || empty($title)) {
                    continue;
                }

                // calculate the distance between the input word,
                // and the current word
                $pos = strpos($input, $title);
                if ($pos == true) {

                    $link = "<a href='" . get_permalink($post->ID) . "'>" . $title . "</a>";
                    $content = str_replace($title, $link, $content);
                    array_push($matches, $title);
                }
            }
            // error_log('$matches : ' . print_r($matches, true));
            return $content;
        }
    } // END
}

$autolinking = new Ck_autolinking_add();
?>
