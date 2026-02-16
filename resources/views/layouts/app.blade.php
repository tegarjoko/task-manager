<!DOCTYPE html>
<html lang="en" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ARC: Task Protocol</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Rajdhani:wght@500;600;700&family=Space+Mono:wght@400;700&display=swap" rel="stylesheet">
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Rajdhani', 'sans-serif'],
                        mono: ['Space Mono', 'monospace'],
                    },
                    colors: {
                        arc: {
                            orange: '#ff5500', // International Orange
                            slate: '#1a1d21', // Dark Gunmetal
                            dark: '#0f1113', // Almost Black
                            gray: '#8b9bb4', // Muted Blue-Gray
                            white: '#e0e0e0', // Off-white
                            paper: '#e6e4d5', // Vintage Beige
                            card: '#fdfcf5', // Bone / Off-White
                            ink: '#1a1d21', // Carbon Black
                            steel: '#a0aec0', // Muted Steel Border
                        }
                    }
                }
            }
        }
    </script>
    <style>
        @keyframes drift {
            0% { background-position: 0 0; }
            100% { background-position: 40px 40px; }
        }
        @keyframes scanline {
            0% { transform: translateY(-100%); opacity: 0; }
            50% { opacity: 1; }
            100% { transform: translateY(100vh); opacity: 0; }
        }
        
        /* Base Body Colors */
        body {
            background-color: #0f1113;
            color: #bdc3c7;
            transition: background-color 0.3s ease, color 0.3s ease;
            overflow-x: hidden; /* Prevent scrollbar twitching */
        }

        /* Dedicated Background Element */
        #app-background {
            position: fixed;
            top: 0;
            left: 0;
            width: 100vw;
            height: 100vh;
            z-index: 0;
            pointer-events: none;
            background-size: 40px 40px;
            /* Dark Mode Grid */
            background-image: 
                linear-gradient(rgba(26, 29, 33, 0.8) 1px, transparent 1px),
                linear-gradient(90deg, rgba(26, 29, 33, 0.8) 1px, transparent 1px);
            animation: drift 20s linear infinite;
        }

        /* Scanline Overlay */
        body::before {
            content: "";
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 5px;
            background: linear-gradient(to right, transparent, rgba(255, 85, 0, 0.5), transparent);
            animation: scanline 8s linear infinite;
            pointer-events: none;
            z-index: 10; /* Above background, below content */
        }
        
        .arc-border {
             border: 1px solid #333;
             box-shadow: 0 0 0 1px #000;
        }

        /* Glitch Animation Keyframes */
        @keyframes glitch-entry {
            0% { opacity: 0; transform: scale(0.8) skew(50deg); filter: hue-rotate(90deg); }
            20% { opacity: 1; transform: skew(-20deg); }
            40% { transform: translateX(-5px) skew(10deg); filter: hue-rotate(-90deg); }
            60% { transform: translateX(5px) skew(-5deg); }
            80% { transform: skew(2deg); }
            100% { opacity: 1; transform: scale(1) skew(0); filter: none; }
        }
        
        @keyframes glitch-text {
            0% { transform: translate(0); }
            20% { transform: translate(-2px, 2px); }
            40% { transform: translate(-2px, -2px); }
            60% { transform: translate(2px, 2px); }
            80% { transform: translate(2px, -2px); }
            100% { transform: translate(0); }
        }

        .animate-glitch-entry {
            animation: glitch-entry 0.4s cubic-bezier(0.25, 0.46, 0.45, 0.94) both;
        }
        
        .animate-glitch-text {
            animation: glitch-text 0.3s infinite;
        }
    </style>
</head>
<body class="antialiased min-h-screen font-sans selection:bg-arc-orange selection:text-white transition-colors duration-300">
    <div id="app-background"></div>
    <div class="relative z-20 container mx-auto p-6 max-w-5xl pt-16">
        <header class="mb-12 border-b-2 border-arc-orange pb-4 flex justify-between items-end">
            <a href="{{ route('home') }}" class="group">
                <h1 class="text-6xl font-bold uppercase tracking-tighter leading-none text-arc-ink dark:text-white group-hover:text-arc-orange transition-colors">
                    ARC<span class="text-arc-orange">.</span>SYS
                </h1>
                <p class="font-mono text-xs tracking-widest uppercase mt-1 text-gray-500 dark:text-arc-gray">Tactical Command Interface // V.3.0</p>
            </a>
            <div class="flex items-center gap-4">
                <button id="audio-toggle" class="text-arc-orange hover:text-white transition-colors" title="TOGGLE AUDIO">
                    <!-- Speaker Icon (On) -->
                    <svg id="icon-sound-on" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.536 8.464a5 5 0 010 7.072m2.828-9.9a9 9 0 010 12.728M5.586 15H4a1 1 0 01-1-1v-4a1 1 0 011-1h1.586l4.707-4.707C10.923 3.663 12 4.109 12 5v14c0 .891-1.077 1.337-1.707.707L5.586 15z"></path></svg>
                    <!-- Speaker Icon (Off) -->
                    <svg id="icon-sound-off" class="w-5 h-5 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5.586 15H4a1 1 0 01-1-1v-4a1 1 0 011-1h1.586l4.707-4.707C10.923 3.663 12 4.109 12 5v14c0 .891-1.077 1.337-1.707.707L5.586 15zM17 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2"></path></svg>
                </button>
                <div class="font-mono text-xs text-arc-orange animate-pulse">
                    SYS.ONLINE
                </div>
            </div>
        </header>

        <script>
            // AUDIO SYSTEM
            class SoundSystem {
                constructor() {
                    this.ctx = null;
                    this.muted = localStorage.getItem('arc_muted') === 'true';
                    this.updateIcon();
                }

                init() {
                    if (!this.ctx) {
                        this.ctx = new (window.AudioContext || window.webkitAudioContext)();
                    }
                    if (this.ctx.state === 'suspended') {
                        this.ctx.resume();
                    }
                }

                toggleMute() {
                    this.muted = !this.muted;
                    localStorage.setItem('arc_muted', this.muted);
                    this.updateIcon();
                    if (!this.muted) this.playTone(1200, 'sine', 0.1); // Feedback beep
                }

                updateIcon() {
                    const onIcon = document.getElementById('icon-sound-on');
                    const offIcon = document.getElementById('icon-sound-off');
                    if (this.muted) {
                        onIcon.classList.add('hidden');
                        offIcon.classList.remove('hidden');
                    } else {
                        onIcon.classList.remove('hidden');
                        offIcon.classList.add('hidden');
                    }
                }

                playTone(freq, type, duration) {
                    if (this.muted || !this.ctx) return;
                    const osc = this.ctx.createOscillator();
                    const gain = this.ctx.createGain();
                    
                    osc.type = type;
                    osc.frequency.setValueAtTime(freq, this.ctx.currentTime);
                    
                    gain.gain.setValueAtTime(0.1, this.ctx.currentTime);
                    gain.gain.exponentialRampToValueAtTime(0.01, this.ctx.currentTime + duration);
                    
                    osc.connect(gain);
                    gain.connect(this.ctx.destination);
                    
                    osc.start();
                    osc.stop(this.ctx.currentTime + duration);
                }

                // FX PRESETS
                hover() { this.playTone(2000 + Math.random()*500, 'sine', 0.05); }
                click() { this.playTone(800, 'square', 0.1); }
                success() {
                    if (this.muted || !this.ctx) return;
                    this.playTone(800, 'sine', 0.1);
                    setTimeout(() => this.playTone(1200, 'sine', 0.2), 100);
                }
            }

            const sfx = new SoundSystem();

            // Initialize on interaction
            document.addEventListener('click', () => sfx.init(), { once: true });

            document.addEventListener('DOMContentLoaded', () => {
                // Mute Toggle
                document.getElementById('audio-toggle').addEventListener('click', () => sfx.toggleMute());

                // Global Event Delegation for Dynamic Elements
                document.body.addEventListener('mouseenter', (e) => {
                    if (e.target.closest('a, button, .interactive')) {
                        sfx.hover();
                    }
                }, true);

                document.body.addEventListener('click', (e) => {
                    if (e.target.closest('a, button, .interactive')) {
                        sfx.click();
                    }
                }, true);
            });
        </script>
        
        <main class="bg-arc-card dark:bg-arc-slate arc-border p-8 relative overflow-hidden transition-colors duration-300">
            <!-- Decorative Corner Elements -->
            <div class="absolute top-0 left-0 w-4 h-4 border-t-2 border-l-2 border-arc-orange"></div>
            <div class="absolute top-0 right-0 w-4 h-4 border-t-2 border-r-2 border-arc-orange"></div>
            <div class="absolute bottom-0 left-0 w-4 h-4 border-b-2 border-l-2 border-arc-orange"></div>
            <div class="absolute bottom-0 right-0 w-4 h-4 border-b-2 border-r-2 border-arc-orange"></div>

            @yield('content')
        </main>
        
        <footer class="mt-8 text-center font-mono text-xs text-gray-400 dark:text-arc-gray opacity-50">
            SECURE CONNECTION ESTABLISHED // RAIDERS PROTOCOL
        </footer>
    </div>
</body>
</html>
