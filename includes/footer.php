</main>

    <!-- External CSS for Footer -->
    <link rel="stylesheet" href="<?php echo $BASE_URL; ?>assets/css/footer.css?v=<?php echo time(); ?>">

    <footer class="site-footer">
        <div class="footer-inner">
            <div class="footer-widget brand-widget">
                <span class="footer-pre-title">AUTHENTIC CUISINE</span>
                <h2 class="footer-title"><?php echo htmlspecialchars($settings['restaurant_name'] ?? 'Lebanese Kitchen'); ?></h2>
                <p class="footer-desc"><?php echo htmlspecialchars($settings['restaurant_description'] ?? 'A taste of Beirut, served with hospitality.'); ?></p>
            </div>

            <div class="footer-widget">
                <h4 class="widget-title">HOURS</h4>
                <div class="hours-list">
                    <span class="day-time"><?php echo htmlspecialchars($settings['opening_title'] ?? 'Opens Daily'); ?></span>
                    <span class="day-time"><?php echo htmlspecialchars($settings['opening_hours'] ?? '12:00a.m. - 12:00p.m.'); ?></span>
                </div>
            </div>

            <div class="footer-widget">
                <h4 class="widget-title">VISIT</h4>
                <div class="visit-list">
                    <span class="contact-item">Reservations: <a href="tel:<?php echo htmlspecialchars($settings['restaurant_phone'] ?? ''); ?>"><?php echo htmlspecialchars($settings['restaurant_phone'] ?? ''); ?></a></span>
                    <?php if (!empty($settings['restaurant_email'])): ?>
                    <span class="contact-item"><a href="mailto:<?php echo htmlspecialchars($settings['restaurant_email']); ?>"><?php echo htmlspecialchars($settings['restaurant_email']); ?></a></span>
                    <?php endif; ?>
                    <span class="contact-item"><?php echo htmlspecialchars($settings['restaurant_address'] ?? 'Lebanon'); ?></span>
                </div>
            </div>

            <div class="footer-bottom">
                <p class="copyright">&copy; <?php echo date('Y'); ?><a href="https://www.instagram.com/mostafa.36510/" target="_blank" rel="noopener noreferrer" style="text-decoration: none; color: inherit;"> Mustafa Abou El-Hajj</a>. All rights reserved.</p>
            </div>
        </div>
        

    </footer>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>

