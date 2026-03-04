<?php
/**
 * Common HTML footer template
 * @param bool $show_footer Whether to show footer content
 */
function renderFooter($show_footer = true) {
?>
<?php if ($show_footer): ?>
    <footer>
        <div class="footer-content">
            <p>&copy; <?php echo date('Y'); ?> The Thinking Mind. All rights reserved.</p>
            <p>Empowering learners through knowledge assessment.</p>
        </div>
    </footer>
<?php endif; ?>
</body>
</html>
<?php
}
?>
