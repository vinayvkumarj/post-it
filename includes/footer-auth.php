    </main>

    <footer class="footer">
        <p>&copy; <?php echo date('Y'); ?> <?php echo SITE_NAME; ?>. All rights reserved.</p>
    </footer>

    <script>
        // Load dark mode 
        (function() {
            const savedDarkMode = localStorage.getItem("darkMode") === "true";
            if (savedDarkMode) {
                document.body.classList.add("dark-mode");
            }
        })();

        document.addEventListener("DOMContentLoaded", function() {
            const darkModeToggle = document.getElementById("darkModeToggleAuth");

            if (darkModeToggle) {
                // Set initial text based on saved preference
                const isDark = document.body.classList.contains("dark-mode");
                const textSpan = darkModeToggle.querySelector("span");
                if (textSpan) {
                    textSpan.textContent = isDark ? "Light Mode" : "Dark Mode";
                }

                darkModeToggle.addEventListener("click", function(e) {
                    e.preventDefault();
                    e.stopPropagation();

                    document.body.classList.toggle("dark-mode");

                    const isDark = document.body.classList.contains("dark-mode");
                    localStorage.setItem("darkMode", isDark);

                    const textSpan = this.querySelector("span");
                    if (textSpan) {
                        textSpan.textContent = isDark ? "Light Mode" : "Dark Mode";
                    }
                });
            }
        });
    </script>
    </body>

    </html>