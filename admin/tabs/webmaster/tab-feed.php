<?php

/**
 * Outputs Feed tab under Webmaster page.
 */
function cd_core_webmaster_feed_tab() {
  $feed_url = get_option('cd_webmaster_feed_url', null);

  if (!$feed_url) {
    echo '<div class="settings-error error"><p>ISSUE: Feed URL must be supplied to use this tab.</p></div>';

    return;
  }

  $feed       = fetch_feed($feed_url);
  $feed_items = $feed->get_items(0, 5);
  ?>

  <ul class="cd-feed-list">
    <?php foreach ($feed_items as $item): ?>
      <li>
        <h3 class="cd-feed-title">
          <a href="<?php echo $item->get_permalink(); ?>"><?php echo $item->get_title(); ?></a>
        </h3>

        <p class="cd-feed-content"><?php echo $item->get_description(); ?></p>

        <p class="cd-feed-meta">
          <span class="cd-feed-author">
            Posted by
            <?php
            $authors = $item->get_authors();
            echo $authors[0]->name;
            ?>
          </span>
          <span class="cd-feed-date">
            On <?php echo $item->get_date(); ?>
          </span>
        </p>
      </li>
    <?php endforeach; ?>
  </ul>
<?php
}

add_action('cd_webmaster_feed_tab', 'cd_core_webmaster_feed_tab');