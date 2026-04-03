<link rel="stylesheet" href="https://unpkg.com/@chrisoakman/chessboardjs@1.0.0/dist/chessboard-1.0.0.min.css">
<style>
    .chess-page { padding: 0 0 40px; }
    .chess-header { text-align: center; padding: 25px 0 10px; }
    .chess-header h1 { font-size: 1.6rem; font-weight: 800; }
    .chess-header h1 i { color: var(--accent-primary); }
    .chess-header p { color: var(--text-secondary); font-size: 0.9rem; margin-top: 6px; }

    .chess-score-banner {
        display: flex; align-items: center; justify-content: center; gap: 12px;
        background: linear-gradient(135deg, rgba(99,102,241,0.1), rgba(168,85,247,0.1));
        border: 1px solid rgba(99,102,241,0.2); border-radius: 12px;
        padding: 12px 24px; margin: 0 auto 20px; max-width: 400px;
    }
    .chess-score-banner i { font-size: 1.4rem; color: var(--accent-warning); }
    .chess-score-banner .score-label { font-size: 0.85rem; color: var(--text-secondary); }
    .chess-score-banner .score-value { font-size: 1.5rem; font-weight: 800; color: var(--accent-primary); }

    /* User Stats Grid */
    .chess-user-stats {
        display: grid; grid-template-columns: repeat(4, 1fr); gap: 10px;
        max-width: 600px; margin: 0 auto 24px;
    }
    .chess-stat-card {
        background: var(--bg-card); border: 1px solid var(--border-color);
        border-radius: 10px; padding: 12px; text-align: center;
    }
    .chess-stat-card .stat-diff { font-size: 0.75rem; font-weight: 700; margin-bottom: 4px; text-transform: uppercase; }
    .chess-stat-card .stat-wins { font-size: 1.2rem; font-weight: 800; color: var(--accent-primary); }
    .chess-stat-card .stat-pts { font-size: 0.75rem; color: var(--text-muted); }

    .chess-layout {
        display: grid; grid-template-columns: minmax(280px, 560px) 1fr;
        gap: 24px; align-items: start;
    }
    .board-wrap {
        border-radius: 8px; overflow: hidden;
        box-shadow: 0 8px 30px rgba(0,0,0,0.25);
        border: 2px solid var(--border-color);
    }
    .side-panel {
        background: var(--bg-card); border-radius: 14px;
        padding: 20px; border: 1px solid var(--border-color);
    }
    .game-status {
        text-align: center; padding: 14px; margin-bottom: 18px;
        border-radius: 10px; font-weight: 700; font-size: 1rem;
        background: rgba(99,102,241,0.08); color: var(--accent-primary);
    }
    .diff-select { margin-bottom: 18px; }
    .diff-select label { font-weight: 600; display: block; margin-bottom: 6px; font-size: 0.9rem; }
    .diff-select select {
        width: 100%; padding: 10px 14px; border-radius: 8px;
        background: var(--bg-input); color: var(--text-primary);
        border: 1px solid var(--border-color); font-size: 0.95rem;
    }
    .ctrl-btn { width: 100%; margin-bottom: 10px; padding: 11px; font-weight: 600; border-radius: 8px; }
    .move-log {
        max-height: 220px; overflow-y: auto;
        background: var(--bg-body); padding: 10px;
        border-radius: 8px; font-family: 'Courier New', monospace; font-size: 0.9rem;
    }
    .move-log table { width: 100%; }
    .move-log td { padding: 3px 8px; color: var(--text-primary); }
    .move-log td:first-child { color: var(--text-muted); text-align: right; width: 36px; }
    .points-map {
        display: grid; grid-template-columns: 1fr 1fr; gap: 6px;
        margin-top: 14px; font-size: 0.82rem;
    }
    .points-map span {
        background: var(--bg-input); padding: 6px 10px; border-radius: 6px;
        display: flex; justify-content: space-between;
    }
    .points-map .pts { font-weight: 700; color: var(--accent-success); }
    .loading-overlay {
        text-align: center; padding: 60px 20px;
        background: var(--bg-card); border-radius: 14px; border: 1px solid var(--border-color);
    }
    .loading-overlay i { color: var(--accent-primary); }

    .win-toast {
        position: fixed; top: 20px; left: 50%; transform: translateX(-50%);
        background: linear-gradient(135deg, #10b981, #059669);
        color: #fff; padding: 16px 28px; border-radius: 12px;
        font-weight: 700; font-size: 1.1rem; z-index: 99999;
        box-shadow: 0 8px 30px rgba(16,185,129,0.4);
        animation: slideDown 0.4s ease; display: none;
    }
    @keyframes slideDown { from { opacity: 0; top: -40px; } to { opacity: 1; top: 20px; } }

    /* === Valid Move Highlighting === */
    .highlight-selected {
        background: rgba(255, 255, 92, 0.45) !important;
        box-shadow: inset 0 0 0 3px rgba(255, 215, 0, 0.5);
    }
    .highlight-valid-move {
        background: radial-gradient(
            circle at center,
            rgba(0, 180, 80, 0.4) 22%,
            transparent 23%
        ) !important;
    }
    .highlight-capture {
        background: radial-gradient(
            circle at center,
            transparent 50%,
            rgba(0, 180, 80, 0.4) 51%,
            rgba(0, 180, 80, 0.4) 68%,
            transparent 69%
        ) !important;
    }
    .highlight-check {
        background: radial-gradient(
            circle at center,
            rgba(255, 0, 0, 0.6) 20%,
            rgba(255, 0, 0, 0.2) 60%,
            transparent 70%
        ) !important;
    }

    /* Mini Leaderboard */
    .chess-mini-lb {
        margin-top: 30px; padding-top: 24px;
        border-top: 1px solid var(--border-color);
    }
    .chess-mini-lb h3 {
        font-size: 1.1rem; font-weight: 700; margin-bottom: 16px; text-align: center;
    }
    .chess-mini-lb h3 i { color: var(--accent-warning); margin-right: 6px; }
    .chess-lb-tabs {
        display: flex; gap: 6px; justify-content: center; flex-wrap: wrap; margin-bottom: 16px;
    }
    .chess-lb-tab {
        padding: 6px 14px; border: 1px solid var(--border-color); border-radius: 8px;
        background: var(--bg-input); color: var(--text-secondary); font-size: 0.78rem;
        font-weight: 600; cursor: pointer; transition: all 0.2s;
    }
    .chess-lb-tab:hover { border-color: var(--accent-primary); color: var(--accent-primary); }
    .chess-lb-tab.active {
        background: rgba(99,102,241,0.12); color: var(--accent-primary);
        border-color: var(--accent-primary);
    }
    .chess-lb-panel { display: none; }
    .chess-lb-panel.active { display: block; }
    .chess-lb-row {
        display: flex; align-items: center; gap: 10px;
        padding: 8px 12px; border-radius: 8px; margin-bottom: 4px;
        background: var(--bg-input);
    }
    .chess-lb-row .rank { font-weight: 800; color: var(--text-muted); width: 24px; text-align: center; }
    .chess-lb-row .rank.top-1 { color: #fbbf24; }
    .chess-lb-row .rank.top-2 { color: #94a3b8; }
    .chess-lb-row .rank.top-3 { color: #cd7f32; }
    .chess-lb-row .name { flex: 1; font-weight: 600; font-size: 0.88rem; }
    .chess-lb-row .wins-info { font-size: 0.78rem; color: var(--accent-info); font-weight: 700; }
    .chess-lb-row .pts-info { font-size: 0.78rem; color: var(--accent-warning); font-weight: 700; margin-left: 8px; }

    @media (max-width: 860px) {
        .chess-layout { grid-template-columns: 1fr; }
        .chess-user-stats { grid-template-columns: repeat(2, 1fr); }
    }
</style>

<div class="container chess-page">
    <div class="chess-header">
        <h1><i class="fas fa-chess-knight"></i> Cờ Vua AI — Stockfish</h1>
        <p>Đấu trí cùng Stockfish Engine. Thắng để nhận điểm!</p>
    </div>

    <div class="chess-score-banner">
        <i class="fas fa-trophy"></i>
        <div>
            <div class="score-label">Tổng điểm cờ vua</div>
            <div class="score-value" id="total-score"><?= $chessScore ?? 0 ?></div>
        </div>
    </div>

    <!-- User Stats Per Difficulty -->
    <div class="chess-user-stats">
        <?php
        $diffLabels = ['easy' => '🟢 Easy', 'medium' => '🟡 Medium', 'hard' => '🟠 Hard', 'hell' => '🔴 Hell'];
        foreach ($diffLabels as $dk => $dl):
            $s = $userStats[$dk] ?? 0;
            $wins = is_array($s) ? ($s['wins'] ?? 0) : 0;
            $pts = is_array($s) ? ($s['points'] ?? 0) : 0;
        ?>
        <div class="chess-stat-card">
            <div class="stat-diff"><?= $dl ?></div>
            <div class="stat-wins"><?= $wins ?></div>
            <div class="stat-pts"><?= $pts ?> điểm</div>
        </div>
        <?php endforeach; ?>
    </div>

    <!-- Loading -->
    <div class="loading-overlay" id="loading-engine">
        <i class="fas fa-spinner fa-spin fa-3x"></i>
        <h4 style="margin-top: 16px;">Đang tải Stockfish AI Engine...</h4>
        <p style="color: var(--text-secondary); font-size: 0.85rem;">Lần đầu có thể mất vài giây</p>
    </div>

    <!-- Game area -->
    <div class="chess-layout" id="game-area" style="display:none;">
        <div class="board-wrap">
            <div id="board"></div>
        </div>
        <div class="side-panel">
            <div class="game-status" id="status">Chọn độ khó & bấm Ván mới</div>

            <div class="diff-select">
                <label><i class="fas fa-signal"></i> Mức độ khó:</label>
                <select id="ai-difficulty">
                    <option value="easy">🟢 Easy — 500 ELO (+1 điểm)</option>
                    <option value="medium" selected>🟡 Medium — 1000 ELO (+3 điểm)</option>
                    <option value="hard">🟠 Hard — 1500 ELO (+5 điểm)</option>
                    <option value="hell">🔴 Hell — 2000 ELO (+10 điểm)</option>
                </select>
            </div>

            <button id="btn-new" class="btn btn-primary ctrl-btn"><i class="fas fa-play"></i> Ván mới</button>
            <button id="btn-undo" class="btn btn-secondary ctrl-btn"><i class="fas fa-undo"></i> Đi lại</button>
            <button id="btn-resign" class="btn ctrl-btn" style="background:var(--accent-danger);color:#fff;"><i class="fas fa-flag"></i> Chịu thua</button>

            <div style="margin-top: 16px;">
                <h4 style="font-size: 0.9rem; margin-bottom: 8px; color: var(--text-secondary);"><i class="fas fa-list-ol"></i> Lịch sử nước đi</h4>
                <div class="move-log" id="move-log">
                    <table><tbody id="moves-body"></tbody></table>
                </div>
            </div>

            <div class="points-map">
                <span>Easy <em class="pts">+1</em></span>
                <span>Medium <em class="pts">+3</em></span>
                <span>Hard <em class="pts">+5</em></span>
                <span>Hell <em class="pts">+10</em></span>
            </div>
        </div>
    </div>

    <!-- Mini Leaderboard -->
    <div class="chess-mini-lb">
        <h3><i class="fas fa-medal"></i> Bảng Xếp Hạng Cờ Vua</h3>
        <div class="chess-lb-tabs">
            <button class="chess-lb-tab active" data-chess-tab="clb-easy">🟢 Easy</button>
            <button class="chess-lb-tab" data-chess-tab="clb-medium">🟡 Medium</button>
            <button class="chess-lb-tab" data-chess-tab="clb-hard">🟠 Hard</button>
            <button class="chess-lb-tab" data-chess-tab="clb-hell">🔴 Hell</button>
        </div>
        <?php foreach (['easy', 'medium', 'hard', 'hell'] as $idx => $diff):
            $lb = $miniLeaderboard[$diff] ?? [];
        ?>
        <div class="chess-lb-panel <?= $idx === 0 ? 'active' : '' ?>" id="clb-<?= $diff ?>">
            <?php if (empty($lb)): ?>
                <div style="text-align:center;padding:20px;color:var(--text-muted);">
                    <i class="fas fa-chess-pawn" style="font-size:1.5rem;"></i>
                    <p style="margin-top:8px;">Chưa có ai thắng ở mức này</p>
                </div>
            <?php else: ?>
                <?php foreach ($lb as $ri => $row): ?>
                <div class="chess-lb-row">
                    <span class="rank <?= $ri < 3 ? 'top-'.($ri+1) : '' ?>"><?= $ri + 1 ?></span>
                    <span class="name"><i class="fas fa-user-circle" style="color:var(--text-muted);margin-right:4px;"></i><?= e($row['username']) ?></span>
                    <span class="wins-info"><?= $row['wins'] ?> trận</span>
                    <span class="pts-info">⭐ <?= $row['total_points'] ?></span>
                </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
        <?php endforeach; ?>
    </div>

    <!-- Win toast -->
    <div class="win-toast" id="win-toast"></div>
</div>

<script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/chess.js/0.10.3/chess.min.js"></script>
<script src="https://unpkg.com/@chrisoakman/chessboardjs@1.0.0/dist/chessboard-1.0.0.min.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const CSRF = '<?= e($csrfToken) ?>';
    const RECORD_URL = '<?= url("/chess/record-win") ?>';

    const DIFFICULTIES = {
        easy:   { skill: 0,  depth: 2,  time: 150  },
        medium: { skill: 5,  depth: 5,  time: 400  },
        hard:   { skill: 12, depth: 10, time: 1000 },
        hell:   { skill: 20, depth: 15, time: 2000 }
    };

    let stockfish = null, board = null, game = new Chess();
    let gameActive = false, aiThinking = false;
    let selectedSquare = null;
    const $status = $('#status'), $body = $('#moves-body');

    // Load Stockfish via blob worker (bypass CORS)
    const sfUrl = 'https://cdnjs.cloudflare.com/ajax/libs/stockfish.js/10.0.2/stockfish.js';
    fetch(sfUrl).then(r => r.text()).then(code => {
        const blob = new Blob([code], { type: 'application/javascript' });
        stockfish = new Worker(URL.createObjectURL(blob));

        stockfish.onmessage = function (e) {
            const line = e.data;
            if (line === 'uciok') {
                $('#loading-engine').hide();
                $('#game-area').show();
                initBoard();
            } else if (typeof line === 'string' && line.startsWith('bestmove')) {
                const m = line.match(/^bestmove ([a-h][1-8])([a-h][1-8])([qrbn])?/);
                if (m && gameActive) {
                    game.move({ from: m[1], to: m[2], promotion: m[3] || 'q' });
                    board.position(game.fen());
                    aiThinking = false;
                    refreshUI();
                }
            }
        };
        stockfish.postMessage('uci');
    }).catch(() => {
        $('#loading-engine').html('<div style="color:var(--accent-danger);"><i class="fas fa-exclamation-triangle fa-2x"></i><h4 style="margin-top:10px;">Lỗi tải Stockfish. Kiểm tra kết nối mạng.</h4></div>');
    });

    function initBoard() {
        board = Chessboard('board', {
            draggable: true, position: 'start',
            onDragStart, onDrop, onSnapEnd,
            pieceTheme: 'https://chessboardjs.com/img/chesspieces/wikipedia/{piece}.png'
        });
        $(window).resize(() => board && board.resize());

        // Two-click move handler
        $('#board').on('click', '.square-55d63', function () {
            if (!gameActive || game.game_over() || aiThinking) return;

            const classes = $(this).attr('class').split(/\s+/);
            let clickedSquare = null;
            for (let i = 0; i < classes.length; i++) {
                const match = classes[i].match(/^square-([a-h][1-8])$/);
                if (match) { clickedSquare = match[1]; break; }
            }
            if (!clickedSquare) return;

            const clickedPiece = game.get(clickedSquare);

            if (!selectedSquare) {
                if (clickedPiece && clickedPiece.color === 'w') {
                    selectedSquare = clickedSquare;
                    highlightMoves(clickedSquare);
                }
                return;
            }

            if (selectedSquare === clickedSquare) {
                selectedSquare = null;
                clearHighlights();
                return;
            }

            if (clickedPiece && clickedPiece.color === 'w') {
                selectedSquare = clickedSquare;
                highlightMoves(clickedSquare);
                return;
            }

            const move = game.move({
                from: selectedSquare,
                to: clickedSquare,
                promotion: 'q'
            });

            if (move) {
                board.position(game.fen());
                selectedSquare = null;
                clearHighlights();
                refreshUI();
                window.setTimeout(aiMove, 200);
            } else {
                selectedSquare = null;
                clearHighlights();
            }
        });
    }

    function highlightMoves(square) {
        clearHighlights();
        $('#board .square-' + square).addClass('highlight-selected');
        const moves = game.moves({ square: square, verbose: true });
        if (moves.length === 0) return;
        moves.forEach(function (move) {
            const $sq = $('#board .square-' + move.to);
            if (move.captured || move.flags.indexOf('e') !== -1) {
                $sq.addClass('highlight-capture');
            } else {
                $sq.addClass('highlight-valid-move');
            }
        });
    }

    function clearHighlights() {
        $('#board .square-55d63').removeClass('highlight-selected highlight-valid-move highlight-capture highlight-check');
    }

    function highlightKingInCheck() {
        if (!game.in_check()) return;
        const turn = game.turn();
        const board_state = game.board();
        for (let r = 0; r < 8; r++) {
            for (let c = 0; c < 8; c++) {
                const piece = board_state[r][c];
                if (piece && piece.type === 'k' && piece.color === turn) {
                    const file = 'abcdefgh'[c];
                    const rank = 8 - r;
                    $('#board .square-' + file + rank).addClass('highlight-check');
                    return;
                }
            }
        }
    }

    function onDragStart(src, piece) {
        if (!gameActive || game.game_over() || aiThinking || piece.search(/^b/) !== -1) return false;
        selectedSquare = null;
        highlightMoves(src);
    }
    function onDrop(src, tgt) {
        clearHighlights();
        selectedSquare = null;
        const move = game.move({ from: src, to: tgt, promotion: 'q' });
        if (!move) return 'snapback';
        refreshUI();
        window.setTimeout(aiMove, 200);
    }
    function onSnapEnd() {
        board.position(game.fen());
        clearHighlights();
        highlightKingInCheck();
    }

    function aiMove() {
        if (game.game_over() || !gameActive) return;
        aiThinking = true;
        $status.html('<i class="fas fa-spinner fa-spin"></i> AI đang suy nghĩ...');
        const d = DIFFICULTIES[$('#ai-difficulty').val()];
        stockfish.postMessage('setoption name Skill Level value ' + d.skill);
        stockfish.postMessage('position fen ' + game.fen());
        stockfish.postMessage('go depth ' + d.depth + ' movetime ' + d.time);
    }

    function refreshUI() {
        const hist = game.history();
        $body.empty();
        for (let i = 0; i < hist.length; i += 2) {
            $body.append('<tr><td>' + ((i/2)+1) + '.</td><td>' + hist[i] + '</td><td>' + (hist[i+1]||'') + '</td></tr>');
        }
        const log = document.getElementById('move-log');
        log.scrollTop = log.scrollHeight;

        const color = game.turn() === 'w' ? 'Trắng' : 'Đen';
        if (game.in_checkmate()) {
            const winner = game.turn() === 'w' ? 'Đen (AI)' : 'Trắng (Bạn)';
            $status.html('♚ Chiếu hết! ' + winner + ' thắng!');
            gameActive = false;
            if (game.turn() === 'b') recordWin();
        } else if (game.in_draw() || game.in_stalemate() || game.in_threefold_repetition()) {
            $status.html('🤝 Hòa!');
            gameActive = false;
        } else {
            let s = 'Lượt: ' + color;
            if (game.in_check()) s += ' — <strong style="color:var(--accent-danger);">Chiếu!</strong>';
            $status.html(s);
        }
        clearHighlights();
        highlightKingInCheck();
    }

    function recordWin() {
        const diff = $('#ai-difficulty').val();
        $.post(RECORD_URL, { csrf_token: CSRF, difficulty: diff }, function (res) {
            if (res.status === 'success') {
                $('#total-score').text(res.total);
                const toast = document.getElementById('win-toast');
                toast.innerHTML = '<i class="fas fa-trophy"></i> ' + res.message;
                toast.style.display = 'block';
                setTimeout(() => toast.style.display = 'none', 4000);
            }
        }, 'json');
    }

    // Buttons
    $('#btn-new').click(function () {
        game.reset(); board.start();
        $body.empty(); gameActive = true; aiThinking = false;
        selectedSquare = null; clearHighlights();
        $status.html('Lượt: Trắng (Bạn)');
    });

    $('#btn-undo').click(function () {
        if (!gameActive || aiThinking) return;
        game.undo(); game.undo();
        board.position(game.fen());
        refreshUI();
    });

    $('#btn-resign').click(function () {
        if (!gameActive) return;
        gameActive = false;
        $status.html('<span style="color:var(--accent-danger);">Bạn đã chịu thua.</span>');
    });

    // Mini leaderboard tabs
    document.querySelectorAll('.chess-lb-tab').forEach(function(tab) {
        tab.addEventListener('click', function() {
            document.querySelectorAll('.chess-lb-tab').forEach(t => t.classList.remove('active'));
            document.querySelectorAll('.chess-lb-panel').forEach(p => p.classList.remove('active'));
            this.classList.add('active');
            const panel = document.getElementById(this.dataset.chessTab);
            if (panel) panel.classList.add('active');
        });
    });
});
</script>
