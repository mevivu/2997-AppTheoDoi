<script>
    /**
     * =========================================================
     * MEMO GAME JS ENGINE - FULL INTERACTIVE CONTROLLER
     * =========================================================
     */
    (function () {
        'use strict';

        // Web Audio Synthesizer (Zero external dependencies, 100% reliable)
        const SoundFX = {
            audioCtx: null,
            enabled: localStorage.getItem('memo_sound_enabled') !== 'false',

            init() {
                if (!this.audioCtx && (window.AudioContext || window.webkitAudioContext)) {
                    this.audioCtx = new (window.AudioContext || window.webkitAudioContext)();
                }
                if (this.audioCtx && this.audioCtx.state === 'suspended') {
                    this.audioCtx.resume();
                }
            },

            playFlip() {
                if (!this.enabled) return;
                this.init();
                if (!this.audioCtx) return;
                const osc = this.audioCtx.createOscillator();
                const gain = this.audioCtx.createGain();
                osc.type = 'sine';
                osc.frequency.setValueAtTime(320, this.audioCtx.currentTime);
                osc.frequency.exponentialRampToValueAtTime(560, this.audioCtx.currentTime + 0.08);
                gain.gain.setValueAtTime(0.2, this.audioCtx.currentTime);
                gain.gain.exponentialRampToValueAtTime(0.01, this.audioCtx.currentTime + 0.08);
                osc.connect(gain);
                gain.connect(this.audioCtx.destination);
                osc.start();
                osc.stop(this.audioCtx.currentTime + 0.08);
            },

            playMatch() {
                if (!this.enabled) return;
                this.init();
                if (!this.audioCtx) return;
                const now = this.audioCtx.currentTime;
                [523.25, 659.25, 783.99, 1046.50].forEach((freq, i) => {
                    const osc = this.audioCtx.createOscillator();
                    const gain = this.audioCtx.createGain();
                    osc.type = 'triangle';
                    osc.frequency.setValueAtTime(freq, now + i * 0.08);
                    gain.gain.setValueAtTime(0.25, now + i * 0.08);
                    gain.gain.exponentialRampToValueAtTime(0.01, now + i * 0.08 + 0.25);
                    osc.connect(gain);
                    gain.connect(this.audioCtx.destination);
                    osc.start(now + i * 0.08);
                    osc.stop(now + i * 0.08 + 0.25);
                });
            },

            playWrong() {
                if (!this.enabled) return;
                this.init();
                if (!this.audioCtx) return;
                const now = this.audioCtx.currentTime;
                const osc = this.audioCtx.createOscillator();
                const gain = this.audioCtx.createGain();
                osc.type = 'sawtooth';
                osc.frequency.setValueAtTime(220, now);
                osc.frequency.exponentialRampToValueAtTime(140, now + 0.22);
                gain.gain.setValueAtTime(0.18, now);
                gain.gain.exponentialRampToValueAtTime(0.01, now + 0.22);
                osc.connect(gain);
                gain.connect(this.audioCtx.destination);
                osc.start(now);
                osc.stop(now + 0.22);
            },

            playPeekTick() {
                if (!this.enabled) return;
                this.init();
                if (!this.audioCtx) return;
                const osc = this.audioCtx.createOscillator();
                const gain = this.audioCtx.createGain();
                osc.type = 'sine';
                osc.frequency.setValueAtTime(880, this.audioCtx.currentTime);
                gain.gain.setValueAtTime(0.2, this.audioCtx.currentTime);
                gain.gain.exponentialRampToValueAtTime(0.01, this.audioCtx.currentTime + 0.1);
                osc.connect(gain);
                gain.connect(this.audioCtx.destination);
                osc.start();
                osc.stop(this.audioCtx.currentTime + 0.1);
            },

            playVictory() {
                if (!this.enabled) return;
                this.init();
                if (!this.audioCtx) return;
                const now = this.audioCtx.currentTime;
                const notes = [
                    { f: 523.25, d: 0.15 },
                    { f: 659.25, d: 0.15 },
                    { f: 783.99, d: 0.15 },
                    { f: 1046.50, d: 0.4 },
                ];
                let time = now;
                notes.forEach(n => {
                    const osc = this.audioCtx.createOscillator();
                    const gain = this.audioCtx.createGain();
                    osc.type = 'triangle';
                    osc.frequency.setValueAtTime(n.f, time);
                    gain.gain.setValueAtTime(0.3, time);
                    gain.gain.exponentialRampToValueAtTime(0.01, time + n.d);
                    osc.connect(gain);
                    gain.connect(this.audioCtx.destination);
                    osc.start(time);
                    osc.stop(time + n.d);
                    time += n.d * 0.8;
                });
            },

            voiceAudio: null,
            playVoice(audioUrl) {
                if (!this.enabled || !audioUrl) return;
                try {
                    if (this.voiceAudio) {
                        this.voiceAudio.pause();
                        this.voiceAudio.currentTime = 0;
                    }
                    this.voiceAudio = new Audio(audioUrl);
                    this.voiceAudio.play().catch(e => console.log('playVoice caught error:', e));
                } catch (e) {
                    console.error('playVoice error:', e);
                }
            }
        };

        // Canvas Confetti
        function launchConfetti() {
            const canvas = document.getElementById('memoConfettiCanvas');
            if (!canvas) return;
            const ctx = canvas.getContext('2d');
            canvas.width = window.innerWidth;
            canvas.height = window.innerHeight;
            canvas.style.display = 'block';

            const pieces = [];
            const colors = ['#0284c7', '#0ea5e9', '#10b981', '#059669', '#f59e0b', '#ef4444'];

            for (let i = 0; i < 90; i++) {
                pieces.push({
                    x: Math.random() * canvas.width,
                    y: Math.random() * canvas.height - canvas.height,
                    size: Math.random() * 8 + 4,
                    color: colors[Math.floor(Math.random() * colors.length)],
                    speed: Math.random() * 4 + 2,
                    rotation: Math.random() * 360,
                    rotationSpeed: Math.random() * 10 - 5
                });
            }

            let animationId;
            let frames = 0;

            function draw() {
                ctx.clearRect(0, 0, canvas.width, canvas.height);
                pieces.forEach(p => {
                    ctx.save();
                    ctx.translate(p.x, p.y);
                    ctx.rotate((p.rotation * Math.PI) / 180);
                    ctx.fillStyle = p.color;
                    ctx.fillRect(-p.size / 2, -p.size / 2, p.size, p.size);
                    ctx.restore();

                    p.y += p.speed;
                    p.rotation += p.rotationSpeed;
                    if (p.y > canvas.height) {
                        p.y = -20;
                        p.x = Math.random() * canvas.width;
                    }
                });

                frames++;
                if (frames < 200) {
                    animationId = requestAnimationFrame(draw);
                } else {
                    cancelAnimationFrame(animationId);
                    ctx.clearRect(0, 0, canvas.width, canvas.height);
                    canvas.style.display = 'none';
                }
            }
            draw();
        }

        // Game Controller State
        window.MemoGameApp = {
            routes: {
                data: '',
                submit: '',
                bulkAdd: '',
                cardIndex: '',
                themeIndex: '',
            },
            themeId: null,
            ageConfigId: null,
            gameMode: 'single', // 'single' or 'multi' (3 rounds)

            gameData: null,
            currentRound: 1,
            totalRounds: 1,
            roundsHistory: [],

            cards: [],
            flippedCards: [],
            matchedPairs: 0,
            mistakes: 0,
            moves: 0,

            timerInterval: null,
            timeRemaining: 0,
            roundDurationSpent: 0,
            totalDurationSpent: 0,

            isBoardLocked: true,
            isGameRunning: false,

            init() {
                // Tự động tính toán đường dẫn API theo window.location.pathname thực tế của trình duyệt
                // Tương thích 100% khi chạy ở root hoặc thư mục con (ví dụ: /Cham-Con/2997-AppTheoDoi/admin/memo-game/play)
                const currentPath = window.location.pathname.replace(/\/+$/, '');
                const playBasePath = currentPath.endsWith('/play') ? currentPath : currentPath + '/play';
                const memoBasePath = playBasePath.replace(/\/play$/, '');

                this.routes = {
                    data: `${playBasePath}/data`,
                    submit: `${playBasePath}/submit`,
                    bulkAdd: `${memoBasePath}/card/bulk-add`,
                    cardIndex: `${memoBasePath}/card`,
                    themeIndex: `${memoBasePath}/theme`,
                };

                // Đồng bộ đường dẫn các nút header theo đúng môi trường hiện tại
                const headerCardBtn = document.getElementById('headerBtnCardIndex');
                if (headerCardBtn) headerCardBtn.href = this.routes.cardIndex;

                const headerBulkBtn = document.getElementById('headerBtnBulkAdd');
                if (headerBulkBtn) headerBulkBtn.href = this.routes.bulkAdd;

                const backBtn = document.querySelector('.btn-header-back');
                if (backBtn) backBtn.href = this.routes.themeIndex;

                this.bindDOMEvents();
                this.updateSoundButtonUI();

                // Select first active chips by default
                const defaultThemeBtn = document.querySelector('.memo-theme-btn.active') || document.querySelector('.memo-theme-btn');
                const defaultAgeBtn = document.querySelector('.memo-age-btn.active') || document.querySelector('.memo-age-btn');

                if (defaultThemeBtn) this.themeId = defaultThemeBtn.dataset.themeId;
                if (defaultAgeBtn) this.ageConfigId = defaultAgeBtn.dataset.ageId;

                this.loadGameData();
            },

            bindDOMEvents() {
                // Theme selection
                document.querySelectorAll('.memo-theme-btn').forEach(btn => {
                    btn.addEventListener('click', (e) => {
                        if (this.isGameRunning && !confirm('Bạn đang chơi dở bài test. Đổi chủ đề sẽ bắt đầu lại từ đầu?')) {
                            return;
                        }
                        document.querySelectorAll('.memo-theme-btn').forEach(b => b.classList.remove('active'));
                        btn.classList.add('active');
                        this.themeId = btn.dataset.themeId;
                        this.loadGameData();
                    });
                });

                // Age Config selection
                document.querySelectorAll('.memo-age-btn').forEach(btn => {
                    btn.addEventListener('click', (e) => {
                        if (this.isGameRunning && !confirm('Bạn đang chơi dở bài test. Đổi độ tuổi/lưới sẽ bắt đầu lại từ đầu?')) {
                            return;
                        }
                        document.querySelectorAll('.memo-age-btn').forEach(b => b.classList.remove('active'));
                        btn.classList.add('active');
                        this.ageConfigId = btn.dataset.ageId;
                        this.loadGameData();
                    });
                });

                // Mode selection
                document.querySelectorAll('.memo-mode-btn').forEach(btn => {
                    btn.addEventListener('click', (e) => {
                        document.querySelectorAll('.memo-mode-btn').forEach(b => b.classList.remove('active'));
                        btn.classList.add('active');
                        this.gameMode = btn.dataset.mode;
                    });
                });

                // Main Start Button
                const startBtn = document.getElementById('btnStartGame');
                if (startBtn) {
                    startBtn.addEventListener('click', () => this.startNewGameSession());
                }

                // Retry Button
                const retryBtn = document.getElementById('btnRetryLoad');
                if (retryBtn) {
                    retryBtn.addEventListener('click', () => this.loadGameData());
                }

                // Sound Button
                const soundBtn = document.getElementById('btnToggleSound');
                if (soundBtn) {
                    soundBtn.addEventListener('click', () => {
                        SoundFX.enabled = !SoundFX.enabled;
                        localStorage.setItem('memo_sound_enabled', SoundFX.enabled);
                        this.updateSoundButtonUI();
                        if (SoundFX.enabled) SoundFX.playFlip();
                    });
                }

                // Save Rating Result Button
                const btnSaveResult = document.getElementById('btnSaveResult');
                if (btnSaveResult) {
                    btnSaveResult.addEventListener('click', () => this.submitGameResult());
                }

                // Restart from Modal Button
                const btnRestartGame = document.getElementById('btnRestartGame');
                if (btnRestartGame) {
                    btnRestartGame.addEventListener('click', () => {
                        const modalEl = document.getElementById('memoResultModal');
                        const modal = bootstrap.Modal.getInstance(modalEl);
                        if (modal) modal.hide();
                        this.startNewGameSession();
                    });
                }
            },

            updateSoundButtonUI() {
                const icon = document.getElementById('soundIcon');
                const text = document.getElementById('soundText');
                if (!icon || !text) return;
                if (SoundFX.enabled) {
                    icon.className = 'ti ti-volume';
                    text.textContent = 'Bật';
                    document.getElementById('btnToggleSound').classList.replace('btn-outline-secondary', 'btn-outline-success');
                } else {
                    icon.className = 'ti ti-volume-off';
                    text.textContent = 'Tắt';
                    document.getElementById('btnToggleSound').classList.replace('btn-outline-success', 'btn-outline-secondary');
                }
            },

            loadGameData() {
                const board = document.getElementById('memoGameBoard');
                const loadingEl = document.getElementById('memoLoadingState');
                const errorEl = document.getElementById('memoErrorState');
                const noticeEl = document.getElementById('memoFallbackNotice');

                if (loadingEl) loadingEl.style.display = 'flex';
                if (errorEl) errorEl.style.display = 'none';
                if (board) board.style.display = 'none';
                if (noticeEl) noticeEl.style.display = 'none';

                fetch(`${this.routes.data}?theme_id=${this.themeId}&age_config_id=${this.ageConfigId}`, {
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                    .then(res => {
                        if (!res.ok) {
                            throw new Error(`HTTP ${res.status}: ${res.statusText}`);
                        }
                        return res.json();
                    })
                    .then(data => {
                        const payload = data.data || data;
                        if (!payload.success) {
                            throw new Error(payload.message || 'Lỗi không xác định khi tải dữ liệu thẻ bài');
                        }
                        this.gameData = payload;
                        this.updateConfigPreviewUI();
                        this.renderInitialBoard();

                        // Reveal board
                        if (loadingEl) loadingEl.style.display = 'none';
                        if (errorEl) errorEl.style.display = 'none';
                        if (board) board.style.display = 'grid';
                    })
                    .catch(err => {
                        if (loadingEl) loadingEl.style.display = 'none';
                        if (board) board.style.display = 'none';
                        if (errorEl) {
                            errorEl.style.display = 'flex';
                            const msgEl = document.getElementById('memoErrorMessage');
                            if (msgEl) msgEl.textContent = `Không thể kết nối máy chủ: ${err.message}`;
                        }
                    });
            },

            updateConfigPreviewUI() {
                const data = this.gameData;
                if (!data) return;

                // Update Fallback notice
                const noticeEl = document.getElementById('memoFallbackNotice');
                const countBadge = document.getElementById('availableCardsCount');
                const neededBadge = document.getElementById('neededPairsCount');

                if (countBadge) countBadge.textContent = data.available_cards_count;
                if (neededBadge) neededBadge.textContent = data.age_config.pairs_count;

                if (noticeEl) {
                    if (data.is_fallback) {
                        noticeEl.style.display = 'flex';
                    } else {
                        noticeEl.style.display = 'none';
                    }
                }

                // Update HUD placeholders
                document.getElementById('hudPairsTotal').textContent = data.age_config.pairs_count;
                document.getElementById('hudPairsMatched').textContent = '0';
                document.getElementById('hudMistakes').textContent = '0';
                document.getElementById('hudTimer').textContent = this.formatTime(data.age_config.total_duration);
                document.getElementById('hudRound').textContent = `1/${this.gameMode === 'multi' ? (data.age_config.total_rounds || 3) : 1}`;

                // Update Upload link
                const uploadLink = document.getElementById('linkUploadThemeCards');
                if (uploadLink) {
                    uploadLink.href = `${this.routes.bulkAdd}?theme_id=${data.theme.id}`;
                }
            },

            renderInitialBoard() {
                const data = this.gameData;
                const board = document.getElementById('memoGameBoard');
                if (!board || !data) return;

                board.className = `memo-grid memo-grid-${data.age_config.rows}x${data.age_config.columns}`;
                board.innerHTML = '';

                // Get array of cards safely
                const rawCards = Array.isArray(data.cards) ? data.cards : (data.cards?.data || []);

                // Generate pairs of cards
                const rawPairs = [];
                rawCards.forEach(card => {
                    rawPairs.push({ ...card, uid: `${card.id}_a` });
                    rawPairs.push({ ...card, uid: `${card.id}_b` });
                });

                // Shuffle with Fisher-Yates
                this.cards = this.shuffleArray(rawPairs);

                this.cards.forEach((card, index) => {
                    const cardEl = this.createCardElement(card, index);
                    board.appendChild(cardEl);
                });
            },

            createCardElement(card, index) {
                const wrap = document.createElement('div');
                wrap.className = 'memo-card-wrapper';
                wrap.dataset.index = index;
                wrap.dataset.key = card.key;
                wrap.dataset.id = card.id;
                if (card.audio) {
                    wrap.dataset.audio = card.audio;
                }

                let frontContentHtml = '';
                if (card.image) {
                    frontContentHtml = `
                        <img src="${card.image}" alt="${card.name}" onerror="this.style.display='none'; this.nextElementSibling.style.display='block';">
                        <div class="memo-card-front-icon" style="display:none; color: #0284c7;"><i class="ti ti-photo"></i></div>
                    `;
                } else {
                    frontContentHtml = `
                        <div class="memo-card-front-icon" style="color: ${card.color || '#0284c7'};">
                            <i class="${card.icon || 'ti ti-star'}"></i>
                        </div>
                    `;
                }

                wrap.innerHTML = `
                    <div class="memo-card-inner">
                        <div class="memo-card-face memo-card-back">
                            <div class="memo-card-back-pattern">
                                <i class="ti ti-help memo-card-back-icon"></i>
                            </div>
                        </div>
                        <div class="memo-card-face memo-card-front">
                            ${frontContentHtml}
                            <div class="memo-card-name" title="${card.name}">${card.name}</div>
                        </div>
                    </div>
                `;

                wrap.addEventListener('click', () => this.handleCardClick(wrap));
                return wrap;
            },

            startNewGameSession() {
                if (!this.gameData) return;

                // Set up session rounds
                this.totalRounds = this.gameMode === 'multi' ? (this.gameData.age_config.total_rounds || 3) : 1;
                this.currentRound = 1;
                this.roundsHistory = [];
                this.totalDurationSpent = 0;
                this.isGameRunning = true;

                // Hide start overlay
                const startOverlay = document.getElementById('memoStartOverlay');
                if (startOverlay) startOverlay.style.display = 'none';

                this.startRound();
            },

            startRound() {
                clearInterval(this.timerInterval);

                this.flippedCards = [];
                this.matchedPairs = 0;
                this.mistakes = 0;
                this.moves = 0;
                this.roundDurationSpent = 0;
                this.isBoardLocked = true;

                // Update HUD
                document.getElementById('hudRound').textContent = `${this.currentRound}/${this.totalRounds}`;
                document.getElementById('hudPairsMatched').textContent = '0';
                document.getElementById('hudMistakes').textContent = '0';

                // Re-shuffle for this round
                const data = this.gameData;
                const rawCards = Array.isArray(data.cards) ? data.cards : (data.cards?.data || []);
                const rawPairs = [];
                rawCards.forEach(card => {
                    rawPairs.push({ ...card, uid: `${card.id}_a_${Date.now()}` });
                    rawPairs.push({ ...card, uid: `${card.id}_b_${Date.now()}` });
                });
                this.cards = this.shuffleArray(rawPairs);

                const board = document.getElementById('memoGameBoard');
                board.innerHTML = '';
                this.cards.forEach((card, index) => {
                    const cardEl = this.createCardElement(card, index);
                    board.appendChild(cardEl);
                });

                // Start Peek Time
                this.runPeekPhase(data.age_config.peek_time || 3);
            },

            runPeekPhase(peekSeconds) {
                const overlay = document.getElementById('memoPeekOverlay');
                const counterEl = document.getElementById('memoPeekCounter');
                const cardEls = document.querySelectorAll('.memo-card-wrapper');

                if (overlay) overlay.style.display = 'flex';
                if (counterEl) counterEl.textContent = peekSeconds;

                // Flip all cards face up
                cardEls.forEach(el => el.classList.add('flipped'));
                SoundFX.playFlip();

                let currentPeek = peekSeconds;
                const peekInterval = setInterval(() => {
                    currentPeek--;
                    if (counterEl) counterEl.textContent = currentPeek;
                    SoundFX.playPeekTick();

                    if (currentPeek <= 0) {
                        clearInterval(peekInterval);
                        if (overlay) overlay.style.display = 'none';

                        // Flip all cards back face down
                        cardEls.forEach(el => el.classList.remove('flipped'));
                        SoundFX.playFlip();

                        // Start main game timer & unlock board
                        setTimeout(() => {
                            this.isBoardLocked = false;
                            this.startTimer();
                        }, 500);
                    }
                }, 1000);
            },

            startTimer() {
                const totalDuration = this.gameData.age_config.total_duration || 180;
                this.timeRemaining = totalDuration;
                this.roundDurationSpent = 0;

                const timerEl = document.getElementById('hudTimer');
                const timerBar = document.getElementById('memoTimerBar');

                this.timerInterval = setInterval(() => {
                    this.timeRemaining--;
                    this.roundDurationSpent++;

                    if (timerEl) timerEl.textContent = this.formatTime(this.timeRemaining);

                    if (timerBar) {
                        const pct = Math.max(0, (this.timeRemaining / totalDuration) * 100);
                        timerBar.style.width = `${pct}%`;
                        if (pct < 20) {
                            timerBar.className = 'memo-timer-bar danger';
                        } else if (pct < 45) {
                            timerBar.className = 'memo-timer-bar warning';
                        } else {
                            timerBar.className = 'memo-timer-bar';
                        }
                    }

                    if (this.timeRemaining <= 0) {
                        clearInterval(this.timerInterval);
                        this.handleTimeUp();
                    }
                }, 1000);
            },

            handleCardClick(cardEl) {
                if (!this.isGameRunning) {
                    this.startNewGameSession();
                    return;
                }
                if (this.isBoardLocked) return;
                if (cardEl.classList.contains('flipped') || cardEl.classList.contains('matched')) return;
                if (this.flippedCards.length >= 2) return;

                cardEl.classList.add('flipped');
                SoundFX.playFlip();
                if (cardEl.dataset.audio) {
                    SoundFX.playVoice(cardEl.dataset.audio);
                }
                this.flippedCards.push(cardEl);

                if (this.flippedCards.length === 2) {
                    this.isBoardLocked = true;
                    this.moves++;
                    this.checkMatch();
                }
            },

            checkMatch() {
                const [card1, card2] = this.flippedCards;
                const isMatch = card1.dataset.key === card2.dataset.key;

                if (isMatch) {
                    setTimeout(() => {
                        card1.classList.add('matched');
                        card2.classList.add('matched');

                        // Add matched badges
                        card1.querySelector('.memo-card-front').insertAdjacentHTML('beforeend', '<div class="memo-matched-badge"><i class="ti ti-check"></i></div>');
                        card2.querySelector('.memo-card-front').insertAdjacentHTML('beforeend', '<div class="memo-matched-badge"><i class="ti ti-check"></i></div>');

                        SoundFX.playMatch();
                        this.matchedPairs++;
                        document.getElementById('hudPairsMatched').textContent = this.matchedPairs;

                        this.flippedCards = [];
                        this.isBoardLocked = false;

                        // Check if all pairs matched in this round
                        if (this.matchedPairs >= this.gameData.age_config.pairs_count) {
                            this.handleRoundWon();
                        }
                    }, 400);
                } else {
                    this.mistakes++;
                    document.getElementById('hudMistakes').textContent = this.mistakes;

                    setTimeout(() => {
                        card1.classList.add('wrong');
                        card2.classList.add('wrong');
                        SoundFX.playWrong();
                    }, 350);

                    setTimeout(() => {
                        card1.classList.remove('flipped', 'wrong');
                        card2.classList.remove('flipped', 'wrong');
                        this.flippedCards = [];
                        this.isBoardLocked = false;
                    }, 950);
                }
            },

            handleRoundWon() {
                clearInterval(this.timerInterval);

                // Compute round score
                const maxDuration = this.gameData.age_config.total_duration || 180;
                const accuracyScore = Math.max(0, 100 - (this.mistakes * 6));
                const speedScore = Math.max(20, Math.round(100 - (this.roundDurationSpent / maxDuration) * 50));
                const roundScore = Math.min(100, Math.max(0, Math.round(accuracyScore * 0.65 + speedScore * 0.35)));

                this.roundsHistory.push({
                    round_number: this.currentRound,
                    duration_spent: this.roundDurationSpent,
                    pairs_matched: this.matchedPairs,
                    mistakes: this.mistakes,
                    score: roundScore,
                });

                this.totalDurationSpent += this.roundDurationSpent;

                if (this.currentRound < this.totalRounds) {
                    // Proceed to next round with mini modal
                    SoundFX.playVictory();
                    setTimeout(() => {
                        if (confirm(`🎉 Hoàn thành Lượt ${this.currentRound}/${this.totalRounds}!\n- Điểm lượt: ${roundScore} điểm\n- Thời gian: ${this.roundDurationSpent}s\n- Lỗi: ${this.mistakes}\n\nNhấn OK để tiếp tục Lượt ${this.currentRound + 1}!`)) {
                            this.currentRound++;
                            this.startRound();
                        } else {
                            this.finishGameSession();
                        }
                    }, 450);
                } else {
                    // Final Game Completed!
                    this.finishGameSession();
                }
            },

            handleTimeUp() {
                this.isBoardLocked = true;
                SoundFX.playWrong();

                const roundScore = Math.round((this.matchedPairs / this.gameData.age_config.pairs_count) * 60);
                this.roundsHistory.push({
                    round_number: this.currentRound,
                    duration_spent: this.roundDurationSpent,
                    pairs_matched: this.matchedPairs,
                    mistakes: this.mistakes,
                    score: roundScore,
                });
                this.totalDurationSpent += this.roundDurationSpent;

                alert('⏰ Đã hết thời gian làm bài của lượt này!');
                this.finishGameSession();
            },

            finishGameSession() {
                this.isGameRunning = false;
                clearInterval(this.timerInterval);

                // Calculate average score
                let totalScore = 0;
                let totalMistakes = 0;
                let totalPairsMatched = 0;

                this.roundsHistory.forEach(r => {
                    totalScore += r.score;
                    totalMistakes += r.mistakes;
                    totalPairsMatched += r.pairs_matched;
                });

                const finalScore = this.roundsHistory.length ? Math.round(totalScore / this.roundsHistory.length) : 0;

                let evalLabel = 'Trung bình';
                let badgeColor = 'bg-warning text-dark';
                let circleGradient = 'linear-gradient(135deg, #f59e0b 0%, #d97706 100%)';

                if (finalScore >= 85) {
                    evalLabel = 'Xuất sắc';
                    badgeColor = 'bg-success text-white';
                    circleGradient = 'linear-gradient(135deg, #10b981 0%, #059669 100%)';
                } else if (finalScore >= 70) {
                    evalLabel = 'Tốt';
                    badgeColor = 'bg-primary text-white';
                    circleGradient = 'linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%)';
                } else if (finalScore >= 50) {
                    evalLabel = 'Khá';
                    badgeColor = 'bg-info text-white';
                    circleGradient = 'linear-gradient(135deg, #06b6d4 0%, #0891b2 100%)';
                }

                // Show modal & celebrate
                SoundFX.playVictory();
                launchConfetti();

                // Populate Modal UI
                document.getElementById('modalScoreNumber').textContent = finalScore;
                document.getElementById('modalScoreCircle').style.background = circleGradient;

                const badgeEl = document.getElementById('modalEvaluationBadge');
                badgeEl.className = `badge px-3 py-2 fs-14 ${badgeColor}`;
                badgeEl.textContent = evalLabel;

                document.getElementById('modalTotalDuration').textContent = `${this.totalDurationSpent}s`;
                document.getElementById('modalTotalPairs').textContent = totalPairsMatched;
                document.getElementById('modalTotalMistakes').textContent = totalMistakes;

                // Rounds table
                const roundsTbody = document.getElementById('modalRoundsTableBody');
                roundsTbody.innerHTML = '';
                this.roundsHistory.forEach(r => {
                    roundsTbody.innerHTML += `
                        <tr>
                            <td class="fw-bold">Lượt ${r.round_number}</td>
                            <td>${r.duration_spent}s</td>
                            <td><span class="text-success fw-bold">${r.pairs_matched}</span></td>
                            <td><span class="text-danger fw-bold">${r.mistakes}</span></td>
                            <td><span class="badge bg-light text-dark fw-bold">${r.score}đ</span></td>
                        </tr>
                    `;
                });

                // Auto feedback text
                let feedbackText = '';
                if (finalScore >= 85) {
                    feedbackText = 'Bé có trí nhớ hình ảnh và độ tập trung xuất sắc! Tốc độ lật mở các cặp thẻ rất nhanh và ít mắc lỗi.';
                } else if (finalScore >= 70) {
                    feedbackText = 'Khả năng quan sát và ghi nhớ của bé rất tốt. Bé liên kết các hình ảnh chuẩn xác.';
                } else if (finalScore >= 50) {
                    feedbackText = 'Bé hoàn thành bài test ở mức Khá. Có thể rèn luyện thêm qua việc chơi thường xuyên.';
                } else {
                    feedbackText = 'Bé cần thêm thời gian để rèn luyện kỹ năng định vị hình ảnh và tăng sự tập trung.';
                }
                document.getElementById('modalFeedbackText').textContent = feedbackText;

                // Cache current session result for submit
                this.latestSession = {
                    theme_id: this.themeId,
                    age_config_id: this.ageConfigId,
                    total_duration_spent: this.totalDurationSpent,
                    total_pairs_matched: totalPairsMatched,
                    total_mistakes: totalMistakes,
                    score: finalScore,
                    evaluation_label: evalLabel,
                    rounds: this.roundsHistory,
                };

                const modal = new bootstrap.Modal(document.getElementById('memoResultModal'));
                modal.show();
            },

            submitGameResult() {
                if (!this.latestSession) return;
                const btn = document.getElementById('btnSaveResult');
                btn.disabled = true;
                btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Đang lưu...';

                fetch(this.routes.submit, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify(this.latestSession)
                })
                .then(res => res.json())
                .then(data => {
                    const payload = data.data || data;
                    btn.disabled = false;
                    btn.innerHTML = '<i class="ti ti-device-floppy me-1"></i> Lưu vào Lịch sử bài test';

                    if (payload.success) {
                        alert('✅ Đã lưu kết quả thành công vào Lịch sử bài test!');
                        if (payload.view_url && confirm('Bạn có muốn mở xem chi tiết bài test vừa lưu không?')) {
                            window.location.href = payload.view_url;
                        }
                    } else {
                        alert('❌ Lỗi: ' + (payload.message || 'Lỗi không xác định'));
                    }
                })
                .catch(err => {
                    btn.disabled = false;
                    btn.innerHTML = '<i class="ti ti-device-floppy me-1"></i> Lưu vào Lịch sử bài test';
                    alert('Lỗi gửi kết quả: ' + err.message);
                });
            },

            shuffleArray(array) {
                const arr = [...array];
                for (let i = arr.length - 1; i > 0; i--) {
                    const j = Math.floor(Math.random() * (i + 1));
                    [arr[i], arr[j]] = [arr[j], arr[i]];
                }
                return arr;
            },

            formatTime(seconds) {
                const s = Math.max(0, parseInt(seconds, 10));
                const mins = Math.floor(s / 60);
                const secs = s % 60;
                return `${mins}:${secs < 10 ? '0' : ''}${secs}`;
            }
        };

        // Initialize on DOM Ready
        document.addEventListener('DOMContentLoaded', () => {
            window.MemoGameApp.init();
        });
    })();
</script>
