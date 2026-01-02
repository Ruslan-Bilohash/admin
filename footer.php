<!-- Футер з перемикачем мов (прапорами) -->
<footer class="footer">
    <div class="container">
        <p class="copyright">© <?= date('Y') ?> Board CMS — безкоштовний скрипт дошки оголошень</p>
        <p class="speed-info">
            <span class="speed-label">Швидкість сторінки:</span>
            <span class="speed-value" id="pageSpeed">...</span>
        </p>
        <p class="dev">Зроблено українським ентузіастом · Розповсюджується безкоштовно v1.3</p>

        <!-- Перемикач мов з прапорами -->
        <div class="lang-flags" title="Змінити мову інтерфейсу">
            <div class="lang-flag <?= $current_lang === 'ua' ? 'active' : '' ?>" 
                 onclick="window.location='?lang=ua'">🇺🇦</div>
            <div class="lang-flag <?= $current_lang === 'en' ? 'active' : '' ?>" 
                 onclick="window.location='?lang=en'">🇺🇸</div>
            <div class="lang-flag <?= $current_lang === 'no' ? 'active' : '' ?>" 
                 onclick="window.location='?lang=no'">🇳🇴</div>
        </div>
    </div>
</footer>

<style>
    .footer {
        background: linear-gradient(to top, #1e293b, #111827);
        color: #cbd5e1;
        text-align: center;
        padding: 3rem 0 2rem;
        margin-top: 6rem;
        border-top: 1px solid rgba(96,165,250,0.15);
    }
    .container {
        max-width: 1200px;
        margin: 0 auto;
        padding: 0 5%;
    }
    .copyright {
        font-size: 1.1rem;
        margin-bottom: 0.8rem;
        opacity: 0.9;
    }
    .speed-info {
        font-size: 0.95rem;
        margin: 0.6rem 0;
        opacity: 0.8;
    }
    .speed-label {
        color: #94a3b8;
    }
    .speed-value {
        color: #60a5fa;
        font-weight: 600;
    }
    .dev {
        font-size: 0.9rem;
        margin-top: 1.5rem;
        opacity: 0.7;
    }
    .lang-flags {
        display: flex;
        justify-content: center;
        gap: 0.8rem;
        margin-top: 1.5rem;
    }
    .lang-flag {
        width: 42px;
        height: 42px;
        border-radius: 50%;
        border: 3px solid transparent;
        font-size: 1.8rem;
        line-height: 42px;
        text-align: center;
        cursor: pointer;
        transition: all 0.3s ease;
        background: rgba(255,255,255,0.12);
        box-shadow: inset 0 0 0 1px rgba(255,255,255,0.08);
    }
    .lang-flag.active {
        border-color: #60a5fa;
        background: rgba(96,165,250,0.35);
        box-shadow: 0 0 0 4px rgba(96,165,250,0.25);
        transform: scale(1.12);
    }
    .lang-flag:hover:not(.active) {
        background: rgba(255,255,255,0.25);
        transform: scale(1.12);
    }
    @media (max-width: 768px) {
        .footer { padding: 2.5rem 0 1.5rem; }
        .copyright { font-size: 1rem; }
        .lang-flags { gap: 0.6rem; }
        .lang-flag { width: 36px; height: 36px; font-size: 1.6rem; line-height: 36px; }
    }
</style>

<script>
// Проста оцінка швидкості завантаження сторінки
window.addEventListener('load', () => {
    const loadTime = Math.round(performance.now() / 1000);
    const speedEl = document.getElementById('pageSpeed');
    if (speedEl) {
        speedEl.textContent = loadTime + ' сек';
       
        // Кольорова оцінка
        if (loadTime <= 2) {
            speedEl.style.color = '#10b981'; // зелений — відмінно
        } else if (loadTime <= 4) {
            speedEl.style.color = '#f59e0b'; // жовтий — нормально
        } else {
            speedEl.style.color = '#ef4444'; // червоний — повільно
        }
    }
});
</script>