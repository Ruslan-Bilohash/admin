<?php
// hello_admin.php — Мультимовний попап-привітання для адміністратора з перемикачем мов
// Оновлено: 2 січня 2026 року
// Автор: Руслан Білогаш
// Підключається через: include 'hello_admin.php';
// Показується тільки один раз за сесію

// Перевірка, чи вже показували привітання в цій сесії
if (!isset($_SESSION['hello_admin_shown'])) {
    $_SESSION['hello_admin_shown'] = true;

    // =============================================
    // ПОВНИЙ МАСИВ ПЕРЕКЛАДІВ (UA / EN / NO)
    // =============================================
    $greetings = [
        'ua' => [
            'title'       => 'Ласкаво просимо до адмін-панелі!',
            'message'     => 'Привіт! Це твій особистий простір для керування MapsMe Norway 🇺🇦🤝🇳🇴<br><br>Тут ти можеш додавати новини, модерувати оголошення, змінювати статус і робити сайт ще кращим для наших українців у Норвегії ❤️<br><br>Дякуємо, що ти з нами! Разом ми — сила, разом ми будуємо нове життя далеко від дому.',
            'button'      => 'Почати роботу',
            'extra'       => 'Слава Україні! Героям слава! 🇺🇦'
        ],
        'en' => [
            'title'       => 'Welcome to the Admin Panel!',
            'message'     => 'Hi! This is your personal space to manage MapsMe Norway 🇺🇦🤝🇳🇴<br><br>Here you can add news, moderate ads, change status, and make the site even better for Ukrainians in Norway ❤️<br><br>Thank you for being with us! Together we are stronger, together we are building a new life far from home.',
            'button'      => 'Start Working',
            'extra'       => 'Glory to Ukraine! Glory to the Heroes! 🇺🇦'
        ],
        'no' => [
            'title'       => 'Velkommen til Admin-panelet!',
            'message'     => 'Hei! Dette er ditt personlige rom for å administrere MapsMe Norway 🇺🇦🤝🇳🇴<br><br>Her kan du legge til nyheter, moderere annonser, endre status og gjøre nettstedet enda bedre for ukrainere i Norge ❤️<br><br>Takk for at du er med oss! Sammen er vi sterkere, sammen bygger vi et nytt liv langt hjemmefra.',
            'button'      => 'Begynn å jobbe',
            'extra'       => 'Ære til Ukraina! Ære til heltene! 🇺🇦'
        ]
    ];

    // Початкова мова — з config.php ($current_lang)
    $current_popup_lang = $current_lang ?? 'ua';
    $t = $greetings[$current_popup_lang] ?? $greetings['ua'];
?>

<!-- Попап-привітання з перемикачем мов -->
<div id="helloAdminModal" class="modal" style="display:flex;">
    <div class="modal-content" style="
        background: white;
        padding: 3.5rem 2.5rem;
        border-radius: 24px;
        max-width: 640px;
        width: 90%;
        text-align: center;
        box-shadow: 0 20px 60px rgba(0,0,0,0.4);
        position: relative;
        animation: fadeInUp 0.6s ease;
    ">
        <!-- Перемикач мов (прапори) -->
        <div class="lang-switcher" style="
            position: absolute;
            top: 1.5rem;
            left: 50%;
            transform: translateX(-50%);
            display: flex;
            gap: 0.8rem;
        ">
            <div class="lang-flag <?= $current_popup_lang === 'ua' ? 'active' : '' ?>" 
                 onclick="switchPopupLang('ua')" title="Українська">🇺🇦</div>
            <div class="lang-flag <?= $current_popup_lang === 'en' ? 'active' : '' ?>" 
                 onclick="switchPopupLang('en')" title="English">🇺🇸</div>
            <div class="lang-flag <?= $current_popup_lang === 'no' ? 'active' : '' ?>" 
                 onclick="switchPopupLang('no')" title="Norsk">🇳🇴</div>
        </div>

        <span class="modal-close" onclick="closeHelloModal()" style="
            position: absolute;
            top: 1.5rem;
            right: 1.5rem;
            font-size: 2.8rem;
            cursor: pointer;
            color: #6b7280;
            font-weight: bold;
            transition: color 0.2s;
        ">×</span>

        <h2 id="popupTitle" style="
            font-family: 'Playfair Display', serif;
            font-size: clamp(2.2rem, 5vw, 3.2rem);
            color: #4361ee;
            margin: 3rem 0 1.5rem;
        ">
            <?= htmlspecialchars($t['title']) ?>
        </h2>

        <p id="popupMessage" style="font-size: 1.25rem; line-height: 1.8; color: #333; margin-bottom: 2rem;">
            <?= $t['message'] ?>
        </p>

        <?php if (!empty($t['extra'])): ?>
            <p id="popupExtra" style="font-size: 1.4rem; font-weight: 700; color: #ef4444; margin-bottom: 2.5rem;">
                <?= htmlspecialchars($t['extra']) ?>
            </p>
        <?php endif; ?>

        <button onclick="closeHelloModal()" style="
            padding: 1.2rem 3rem;
            background: linear-gradient(90deg, #4361ee, #3b82f6);
            color: white;
            border: none;
            border-radius: 12px;
            font-size: 1.3rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            box-shadow: 0 8px 25px rgba(67,97,238,0.3);
        ">
            <?= htmlspecialchars($t['button']) ?>
        </button>
    </div>
</div>

<style>
    .modal {
        position: fixed;
        inset: 0;
        background: rgba(0,0,0,0.75);
        z-index: 9999;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .modal-content {
        animation: fadeInUp 0.6s ease;
        position: relative;
    }
    @keyframes fadeInUp {
        from { opacity: 0; transform: translateY(50px); }
        to { opacity: 1; transform: translateY(0); }
    }
    .modal-close:hover {
        color: #ef4444;
        transform: scale(1.2);
    }
    button:hover {
        background: #3b82f6;
        transform: translateY(-4px);
        box-shadow: 0 12px 35px rgba(67,97,238,0.4);
    }
    .lang-switcher {
        display: flex;
        gap: 0.8rem;
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
        .modal-content { padding: 2.5rem 1.8rem; }
        h2 { font-size: 2.4rem; }
    }
</style>

<script>
// Перемикання мови в попапі
function switchPopupLang(lang) {
    const greetings = <?= json_encode($greetings) ?>;
    const t = greetings[lang] || greetings['ua'];

    document.getElementById('popupTitle').textContent = t.title;
    document.getElementById('popupMessage').innerHTML = t.message;
    if (document.getElementById('popupExtra')) {
        document.getElementById('popupExtra').textContent = t.extra || '';
    }
    document.querySelector('button[onclick="closeHelloModal()"]').textContent = t.button;

    // Оновлюємо активний прапорець
    document.querySelectorAll('.lang-flag').forEach(flag => {
        flag.classList.remove('active');
        if (flag.onclick.toString().includes(`'${lang}'`)) {
            flag.classList.add('active');
        }
    });
}

function closeHelloModal() {
    document.getElementById('helloAdminModal').style.display = 'none';
}
</script>

<?php } // кінець умови показу попапу ?>