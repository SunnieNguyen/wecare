<?php
if (get_query_var('header_search')) { ?>
<div id="magic-search" class="magic-search">
    <form role="search" method="get" class="search-form" action="<?php echo esc_url(home_url('/')); ?>">
        <div class="inner-form">
            <div class="row justify-content-end">
                <div class="input-field first justify-content-end" id="first">
                    <svg class="search-icon" xmlns="http://www.w3.org/2000/svg"
                         xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" width="612.01px" height="612.01px"
                         viewBox="0 0 612.01 612.01" xml:space="preserve"><g><path d="M606.209,578.714L448.198,423.228C489.576,378.272,515,318.817,515,253.393C514.98,113.439,399.704,0,257.493,0
				C115.282,0,0.006,113.439,0.006,253.393s115.276,253.393,257.487,253.393c61.445,0,117.801-21.253,162.068-56.586
				l158.624,156.099c7.729,7.614,20.277,7.614,28.006,0C613.938,598.686,613.938,586.328,606.209,578.714z M257.493,467.8
				c-120.326,0-217.869-95.993-217.869-214.407S137.167,38.986,257.493,38.986c120.327,0,217.869,95.993,217.869,214.407
				S377.82,467.8,257.493,467.8z"></path></g>
                    </svg>
                    <input autocomplete="off" type="search" class="input" id="inputFocus" placeholder="Search"
                           value="<?php echo get_search_query(); ?>" name="s" required/>
                    <input type="submit" value="<?php echo esc_attr_x('Search', 'submit button', 'healfio'); ?>"
                           class="search-submit"/>
                    <?php
                    if (!get_theme_mod('search_switcher', false)) {
                        if (get_query_var('header_search')) {
                            echo '<input type="hidden" name="post_type" value="product">';
                        }
                    } ?>
                    <div class="clear" id="clear">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="#6e6e73" width="24" height="24"
                             viewBox="0 0 24 24">
                            <path d="M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12z"></path>
                        </svg>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
<?php } else { ?>
    <form role="search" method="get" class="search-form" action="<?php echo esc_url(home_url('/')); ?>">
        <label>
            <span class="screen-reader-text"><?php echo esc_html_x( 'Search for:', 'label', 'healfio' ) ?></span>
            <input autocomplete="off" type="search" class="search-field"
                   placeholder="<?php echo esc_attr_x( 'Search …', 'placeholder' , 'healfio') ?>"
                   value="<?php echo get_search_query() ?>" name="s"
                   title="<?php echo esc_attr_x( 'Search for:', 'label', 'healfio' ) ?>" required/>
        </label>
        <input type="submit" class="search-submit"
               value="<?php echo esc_attr_x( 'Search', 'submit button', 'healfio' ) ?>" />
    </form>
<?php }