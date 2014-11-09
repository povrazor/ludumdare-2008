<?php get_header(); ?>
<div id="body">
	<div id="content" class="fullcolumn botpad">

<?php 	if (have_posts()) : while (have_posts()) : the_post(); ?>
<?php 		if ( get_post_meta(get_the_ID(),'border',true) !== "false" ) { ?>
		<div class="ld-post post" id="post-<?php the_ID(); ?>">
			<div class="head"><h2><?php the_title(); ?></h2></div>
			<div class="entry">
				<?php the_content('<p class="serif">Read the rest of this page &raquo;</p>'); ?>
				<br />
			</div>
		</div>
<?php		} else { ?>
		<div class="entry">
			<?php the_content('<p class="serif">Read the rest of this page &raquo;</p>'); ?>
		</div>
<?php		} ?>
		<?php endwhile; endif; ?>
	<?php edit_post_link('Edit this entry.', '<div>', '</div>'); ?>
	</div>
</div>
<?php get_footer(); ?>