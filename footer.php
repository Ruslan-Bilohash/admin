<?php
// Очищаем мета-теги после использования
unset($_SESSION['meta']);
?>
        </div> <!-- Закрываем .content из header.php -->
    </div> <!-- Закрываем .d-flex из header.php -->

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Проверяем, что текущая страница не news_add
            if (window.location.search.includes('module=news_add')) {
                console.log('Пропуск toggle/sidebar для news_add');
                return;
            }

            const sidebar = document.querySelector('#sidebar');
            const toggleBtn = document.querySelector('.toggle-btn');

            if (toggleBtn && sidebar) {
                // Обработка события сворачивания/разворачивания
                toggleBtn.addEventListener('click', function () {
                    sidebar.classList.toggle('show');
                    toggleBtn.classList.toggle('collapsed');
                });
            } else {
                console.warn('Toggle button or sidebar not found. Skipping toggle functionality.');
            }
        });
    </script>
</body>
</html>