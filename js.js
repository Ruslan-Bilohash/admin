    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const sidebar = document.querySelector('#sidebar');
            const toggleBtn = document.querySelector('.toggle-btn');

            // Инициализация состояния боковой панели
            sidebar.classList.add('collapse');

            // Обработка события сворачивания/разворачивания
            toggleBtn.addEventListener('click', function () {
                sidebar.classList.toggle('show');
                toggleBtn.classList.toggle('collapsed');
            });
        });
    </script>