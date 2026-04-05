    <!-- Footer -->
    <footer class="main-footer mt-5">
        <div class="container">
            <div class="row">
                <!-- About Widget -->
                <div class="col-lg-4 col-md-6 mb-4">
                    <div class="footer-widget">
                        <a class="logo" href="index.php">OVA<span>RALL</span></a>
                        <p class="mt-3 text-white-50">
                            Your destination for premium fashion, electronics, and lifestyle products. 
                            We bring you the best quality products at affordable prices.
                        </p>
                        <div class="social-links">
                            <a href="#" title="Facebook"><i class="fab fa-facebook-f"></i></a>
                            <a href="#" title="Instagram"><i class="fab fa-instagram"></i></a>
                            <a href="#" title="Twitter"><i class="fab fa-twitter"></i></a>
                            <a href="https://wa.me/<?php echo CONTACT_WHATSAPP; ?>" title="WhatsApp" target="_blank"><i class="fab fa-whatsapp"></i></a>
                        </div>
                    </div>
                </div>
                
                <!-- Quick Links -->
                <div class="col-lg-2 col-md-6 mb-4">
                    <div class="footer-widget">
                        <h4>Quick Links</h4>
                        <ul>
                            <li><a href="index.php">Home</a></li>
                            <li><a href="shop.php">Shop</a></li>
                            <li><a href="shop.php?category=fashion">Fashion</a></li>
                            <li><a href="shop.php?category=electronics">Electronics</a></li>
                            <li><a href="shop.php?category=health">Health</a></li>
                        </ul>
                    </div>
                </div>
                
                <!-- Customer Service -->
                <div class="col-lg-2 col-md-6 mb-4">
                    <div class="footer-widget">
                        <h4>Customer Service</h4>
                        <ul>
                            <li><a href="about.php">About Us</a></li>
                            <li><a href="contact.php">Contact Us</a></li>
                            <li><a href="faq.php">FAQ</a></li>
                            <li><a href="shipping.php">Shipping Info</a></li>
                            <li><a href="returns.php">Returns Policy</a></li>
                        </ul>
                    </div>
                </div>
                
                <!-- Contact Info -->
                <div class="col-lg-4 col-md-6 mb-4">
                    <div class="footer-widget">
                        <h4>Contact Us</h4>
                        <div class="footer-contact">
                            <p>
                                <i class="fas fa-phone"></i>
                                <a href="tel:<?php echo CONTACT_PHONE; ?>" class="text-white-50">
                                    <?php echo CONTACT_PHONE; ?> (Call)
                                </a>
                            </p>
                            <p>
                                <i class="fab fa-whatsapp"></i>
                                <a href="https://wa.me/<?php echo CONTACT_WHATSAPP; ?>" class="text-white-50" target="_blank">
                                    <?php echo CONTACT_WHATSAPP; ?> (WhatsApp)
                                </a>
                            </p>
                            <p>
                                <i class="fas fa-envelope"></i>
                                <a href="mailto:<?php echo CONTACT_EMAIL; ?>" class="text-white-50">
                                    <?php echo CONTACT_EMAIL; ?>
                                </a>
                            </p>
                            <p>
                                <i class="fas fa-map-marker-alt"></i>
                                <span class="text-white-50">Dhaka, Bangladesh</span>
                            </p>
                        </div>
                        
                        <!-- Newsletter -->
                        <div class="mt-4">
                            <h5 class="text-white mb-3">Newsletter</h5>
                            <form action="newsletter.php" method="POST" class="d-flex">
                                <input type="email" name="email" class="form-control" placeholder="Your email" required>
                                <button type="submit" class="btn btn-primary-custom ms-2">
                                    <i class="fas fa-paper-plane"></i>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Footer Bottom -->
            <div class="footer-bottom">
                <p>&copy; <?php echo date('Y'); ?> <?php echo SITE_NAME; ?>. All Rights Reserved.</p>
            </div>
        </div>
    </footer>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Custom JS -->
    <script src="assets/js/main.js"></script>
</body>
</html>
