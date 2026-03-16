<div class="container">
    <div class="page-header">
        <h1><i class="fas fa-calendar-star" style="color: var(--accent-warning);"></i> Sự Kiện</h1>
        <p class="text-secondary">Các sự kiện đang diễn ra và sắp tới</p>
    </div>

    <?php if (empty($activeEvents) && empty($upcomingEvents)): ?>
        <div class="empty-state">
            <i class="fas fa-calendar-xmark"></i>
            <h3>Chưa có sự kiện nào</h3>
            <p>Hãy quay lại sau để xem các sự kiện hấp dẫn!</p>
        </div>
    <?php else: ?>
        <!-- Active Events -->
        <?php if (!empty($activeEvents)): ?>
            <div class="section-title">
                <i class="fas fa-fire" style="color: var(--accent-secondary);"></i>
                <h2>Đang Diễn Ra</h2>
            </div>
            <div class="product-grid mb-3">
                <?php foreach ($activeEvents as $event): ?>
                    <div class="event-card">
                        <div class="event-banner">
                            <?php if ($event['image']): ?>
                                <img src="<?= asset('uploads/' . $event['image']) ?>" alt="<?= e($event['title']) ?>" style="width:100%; height:100%; object-fit:cover;">
                            <?php else: ?>
                                <i class="fas fa-gift"></i>
                            <?php endif; ?>
                        </div>
                        <div class="event-body">
                            <div class="event-title"><?= e($event['title']) ?></div>
                            <p class="card-text"><?= e($event['description']) ?></p>
                            <div class="event-date">
                                <i class="fas fa-clock"></i>
                                <?= formatDate($event['start_date']) ?> - <?= formatDate($event['end_date']) ?>
                            </div>
                            <div class="event-reward mt-1">
                                <i class="fas fa-gift"></i>
                                <?php
                                    switch ($event['reward_type']) {
                                        case 'money': echo 'Thưởng: ' . formatMoney($event['reward_value']); break;
                                        case 'points': echo 'x' . intval($event['reward_value']) . ' Điểm xanh'; break;
                                        case 'discount': echo 'Giảm ' . intval($event['reward_value']) . '%'; break;
                                        default: echo 'Phần thưởng đặc biệt'; break;
                                    }
                                ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <!-- Upcoming Events -->
        <?php if (!empty($upcomingEvents)): ?>
            <div class="section-title mt-3">
                <i class="fas fa-clock"></i>
                <h2>Sắp Diễn Ra</h2>
            </div>
            <div class="product-grid">
                <?php foreach ($upcomingEvents as $event): ?>
                    <div class="event-card" style="opacity: 0.8;">
                        <div class="event-banner" style="background: linear-gradient(135deg, var(--bg-secondary), var(--bg-card));">
                            <i class="fas fa-hourglass-half"></i>
                        </div>
                        <div class="event-body">
                            <div class="event-title"><?= e($event['title']) ?></div>
                            <p class="card-text"><?= e($event['description']) ?></p>
                            <div class="event-date">
                                <i class="fas fa-calendar"></i>
                                Bắt đầu: <?= formatDate($event['start_date']) ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    <?php endif; ?>
</div>
