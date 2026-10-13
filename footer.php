</main>

<?php if ( ! is_singular( 'ms_part' ) ) : ?>
<div class="ms-site-footer" id="site-footer">
	<?php my_store_render_part( 'footer' ); ?>
</div>
<?php endif; ?>

<?php wp_footer(); ?>
</body>
</html>