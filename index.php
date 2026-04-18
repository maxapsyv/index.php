<!DOCTYPE html>
<html lang="az">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Restricted Area - System Terminal</title>
    <style>
        body {
            background-color: #050505;
            color: #0f0;
            font-family: 'Courier New', Courier, monospace;
            margin: 0;
            padding: 20px;
            overflow-x: hidden;
            font-size: 14px;
        }
        #terminal {
            white-space: pre-wrap;
            word-wrap: break-word;
        }
        .red { color: #f00; }
        .cyan { color: #0ff; }
        .yellow { color: #ff0; }
        .white { color: #fff; }
        .glitch {
            animation: glitch-anim 0.2s linear infinite;
            display: inline-block;
        }
        @keyframes glitch-anim {
            0% { transform: translate(0) }
            20% { transform: translate(-2px, 2px) }
            40% { transform: translate(-2px, -2px) }
            60% { transform: translate(2px, 2px) }
            80% { transform: translate(2px, -2px) }
            100% { transform: translate(0) }
        }
        .input-line {
            display: flex;
            align-items: center;
            margin-top: 5px;
        }
        .input-line span { margin-right: 8px; }
        input {
            background: transparent;
            border: none;
            color: #0f0;
            font-family: 'Courier New', Courier, monospace;
            font-size: 14px;
            outline: none;
            flex-grow: 1;
        }
    </style>
</head>
<body>

<div id="terminal"></div>

<script>
    const term = document.getElementById('terminal');
    
    // Audio Context üçün (Səs effektləri)
    const audioCtx = new (window.AudioContext || window.webkitAudioContext)();

    function beep(duration = 200, frequency = 600, type = 'square') {
        const oscillator = audioCtx.createOscillator();
        const gainNode = audioCtx.createGain();
        oscillator.type = type;
        oscillator.frequency.value = frequency;
        oscillator.connect(gainNode);
        gainNode.connect(audioCtx.destination);
        oscillator.start();
        setTimeout(() => oscillator.stop(), duration);
    }

    const sleep = ms => new Promise(r => setTimeout(r, ms));

    function printOut(text, colorClass = '') {
        const span = document.createElement('span');
        if (colorClass) span.className = colorClass;
        span.innerText = text + '\n';
        term.appendChild(span);
        window.scrollTo(0, document.body.scrollHeight);
        return span;
    }

    async function typeText(text, colorClass = '', speed = 30) {
        const span = document.createElement('span');
        if (colorClass) span.className = colorClass;
        term.appendChild(span);
        for (let i = 0; i < text.length; i++) {
            span.innerHTML += text.charAt(i);
            window.scrollTo(0, document.body.scrollHeight);
            await sleep(speed);
        }
        span.innerHTML += '\n';
    }

    function askInput(promptText, isPassword = false, colorClass = 'yellow') {
        return new Promise(resolve => {
            const div = document.createElement('div');
            div.className = 'input-line';
            
            const promptSpan = document.createElement('span');
            promptSpan.className = colorClass;
            promptSpan.innerText = promptText;
            
            const inputField = document.createElement('input');
            inputField.type = isPassword ? 'password' : 'text';
            inputField.autocomplete = 'off';
            
            div.appendChild(promptSpan);
            div.appendChild(inputField);
            term.appendChild(div);
            inputField.focus();

            inputField.addEventListener('keydown', function(e) {
                if (e.key === 'Enter') {
                    const val = inputField.value;
                    inputField.disabled = true;
                    resolve(val);
                }
            });
            
            // Ekrana klikləyəndə inputa fokuslanmaq üçün
            document.addEventListener('click', () => inputField.focus());
        });
    }

    async function spinner(duration, text) {
        const chars = ['|', '/', '-', '\\'];
        const span = document.createElement('span');
        span.className = 'yellow';
        term.appendChild(span);
        
        let i = 0;
        const endTime = Date.now() + (duration * 1000);
        while (Date.now() < endTime) {
            span.innerText = `[${chars[i % 4]}] ${text}...`;
            i++;
            await sleep(100);
        }
        span.className = 'green';
        span.innerText = `[+] ${text} tamamlandı!\n`;
    }

    const mask = `
       _,.-------.,_
    ,;~'             '~;,
  ,;                     ;,
 ;                         ;
,'                         ',
,;                         ;,
; ;      .           .      ; ;
| ;   ______       ______   ; |
|  \`/~"     ~" . "~     "~\\'  |
|  ~  ,-~~~^~, | ,~^~~~-,  ~  |
 |   |        }:{        |   |
 |   l       / | \\       !   |
 .~  (__,.--" .^. "--.,__)  ~.
 |     ---;' / | \\ \`;---     |
  \\__.       \\/^\\/       .__/
   V| \\                 / |V
    | |T~\\___!___!___/~T| |
    | |\`IIII_I_I_I_IIII'| |
    |  \\,III I I I III,/  |
     \\   \`~~~~~~~~~~'    /
       \\   .       .   /
         \\.    ^    ./
           ^~~~^~~~^
`;

    const mapArt = `
    +-------------------------------------------------+
    |  [PEYK XƏRİTƏSİ - QAFQAZ REGİONU]               |
    |                                                 |
    |      .        _,-~^~-._           .             |
    |           ,-'           \`-.                     |
    |         /      Tbilisi      \\             .     |
    |        |          * |                  |        |
    |  .     |           \\         |    .             |
    |         \\           V  Bolnisi/                 |
    |          \`-.               ,-'                  |
    |              \`~-.___,-~'                        |
    +-------------------------------------------------+
    `;

    function makeid(length) {
        let result = '';
        const characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789!@#$%^&*';
        for (let i = 0; i < length; i++) {
            result += characters.charAt(Math.floor(Math.random() * characters.length));
        }
        return result;
    }

    async function runTerminal() {
        // AUDIO CONTEXT icazəsi üçün ilk klik
        await askInput("[SİSTEMİ BAŞLATMAQ ÜÇÜN ENTER-Ə BASIN]", false, 'cyan');
        term.innerHTML = '';
        
        // GİRİŞ
        printOut("==============================================", 'red');
        printOut("  GİZLİ SİSTEMƏ GİRİŞ (RESTRICTED AREA)", 'red');
        printOut("==============================================\n", 'red');

        let loggedIn = false;
        while (!loggedIn) {
            let user = await askInput("Login: ");
            let pwd = await askInput("Password: ", true);
            
            if (user === "Admin" && pwd === "Admin333") {
                await spinner(2, "Doğrulama aparılır");
                await typeText("\n[+] Giriş icazəsi verildi. Əsas mərkəzə bağlanılır...", 'green');
                await sleep(1000);
                loggedIn = true;
            } else {
                beep(300, 300);
                printOut("\n[!] XƏTA: KİMLİK DOĞRULANMADI!", 'glitch red');
                printOut("[-] Giriş rədd edildi.\n", 'red');
            }
        }

        // HÜCUM MƏRHƏLƏSİ
        term.innerHTML = '';
        printOut(mask, 'green');
        await typeText("We are Anonymous. We do not forgive.", 'cyan', 50);
        printOut("==============================================================", 'white');
        printOut("  ANONYMOUS CYBER-ATTACK SYSTEM (v10.0 TACTICAL Ed.)", 'white');
        printOut("==============================================================\n", 'white');

        let target = await askInput("[?] Hədəf istifadəçi (Target): ");
        
        await typeText(`\n[*] ${target} üçün qlobal peyk izləməsi aktivləşdirilir...`, 'cyan');
        await spinner(3, "Cihaz izləri toplanır");
        
        let phones = ["Apple iPhone 14 Pro Max", "Samsung Galaxy S24 Ultra", "Xiaomi 13 Pro", "Google Pixel 8 Pro", "Apple iPhone 15"];
        let randomPhone = phones[Math.floor(Math.random()*phones.length)];
        let fakeIp = `${Math.floor(Math.random()*150+50)}.${Math.floor(Math.random()*200+10)}.${Math.floor(Math.random()*200)}.${Math.floor(Math.random()*254)}`;
        let fakeImei = Math.floor(Math.random()*900000000000000 + 100000000000000).toString();

        printOut("\n>>> CİHAZ MƏLUMATLARI AŞKARLANDI <<<", 'green');
        await sleep(500);
        await typeText(`[*] İP Ünvanı : ${fakeIp}`, 'white');
        await typeText(`[*] Cihaz Növü: ${randomPhone}`, 'white');
        await typeText(`[*] IMEI Kodu : ${fakeImei}`, 'white');
        await sleep(1000);
        
        await typeText("\n[*] Koordinatlar hesablanır...", 'cyan');
        await sleep(1000);
        printOut(mapArt, 'cyan');
        await sleep(500);
        printOut("[!] HƏDƏFİN LOKASİYASI KİLİDLƏNDİ: GÜRCÜSTAN, BOLNİSİ", 'red');
        await sleep(2000);

        printOut(`\n[!] Kriptoqrafik analiz və şifrə sındırma başladı...\n`, 'yellow');
        
        // Matrix effekti
        const matrixLine = document.createElement('span');
        term.appendChild(matrixLine);
        const endTime = Date.now() + 6000;
        while (Date.now() < endTime) {
            matrixLine.className = 'green';
            matrixLine.innerText = `Analiz: ${makeid(16)} | Kombinasiya: ${makeid(8)}\n`;
            window.scrollTo(0, document.body.scrollHeight);
            await sleep(40);
        }
        
        printOut("\n[-] Avtomatik axtarış uğursuz oldu.", 'red');
        await typeText("[!] Sistem 'anti-brute' müdafiəsini aktivləşdirdi.", 'yellow');
        await typeText("[!] Əllə müdaxilə (Manual Override) rejiminə keçilir.\n", 'yellow');

        // MANUAL SHELL
        let commands = 0;
        while (commands < 3) {
            let cmd = await askInput("root@anonymous:~# ", false, 'red');
            if (cmd.trim() !== '') {
                commands++;
                if (commands < 3) {
                    await typeText(`[*] '${cmd}' əmri analiz edilir...`, 'cyan');
                    await spinner(1, "Paketlər yönləndirilir");
                    for(let k=0; k<3; k++){
                        printOut(`0x${Math.floor(Math.random()*9000+1000).toString(16).toUpperCase()} -> Məlumat Bloku [OK]`, 'green');
                        await sleep(100);
                    }
                    printOut("");
                }
            }
        }

        // FINAL XAOS
        term.innerHTML = '';
        printOut(mask, 'red');
        await typeText("\n[!] Kritik Payload göndərilir... Sistem çökdürülür...", 'yellow');
        await spinner(4, "Zərərli paketlər inyeksiya edilir");
        await sleep(1000);
        
        await typeText(`\n[+] ONAYLANDI (Server Girişi Təsdiqləndi)`, 'green', 50);
        await sleep(1000);
        
        printOut("[!] TƏHLÜKƏ SƏVİYYƏSİ: KRİTİK!", 'red');
        for(let a=0; a<6; a++){
            beep(250, 800, 'sawtooth');
            await sleep(400);
        }
        
        printOut("\n[-] GÜVƏNLİK DUVARI KEÇİLƏ BİLMƏDİ", 'glitch red');
        await sleep(1500);
        
        printOut("\n[!!!] BAĞLANTI KƏSİLDİ VƏ İP ÜNVANINIZ QEYDƏ ALINDI!", 'glitch red');
        await typeText("\nSistemdən məcburi çıxış...", 'white', 100);
    }

    runTerminal();
</script>
</body>
</html>
