<section id="block-<?php print $block->module .'-'. $block->delta; ?>" class="block block-<?php print $block->module ?> clearfix">

  <?php print render($title_prefix); ?>
  <?php if (!empty($block->subject)): ?>
    <h2><?php print $block->subject ?></h2>
  <?php endif;?>
  <?php print render($title_suffix); ?>

  <div class="content">
    <?php print $edit_links; ?>
    <?php print $content; ?>
  </div>

</section> <!-- /.block -->
