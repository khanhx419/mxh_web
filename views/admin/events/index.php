<div class="admin-header">
    <h1><i class="fas fa-calendar-star"></i> Quản lý Sự Kiện</h1>
    <a href="<?= url('/admin/events/create') ?>" class="btn btn-primary"><i class="fas fa-plus"></i> Thêm sự kiện</a>
</div>

<div class="table-wrapper">
    <table class="table">
        <thead>
            <tr>
                <th>Hình</th>
                <th>Tiêu đề</th>
                <th>Thời gian</th>
                <th>Phần thưởng</th>
                <th>Trạng thái</th>
                <th>Thao tác</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($events)): ?>
                <tr><td colspan="6" class="text-center text-muted">Chưa có sự kiện nào.</td></tr>
            <?php else: ?>
                <?php foreach ($events as $event): ?>
                    <?php
                        $now = date('Y-m-d H:i:s');
                        $isActive = $event['status'] == 1 && $event['start_date'] <= $now && $event['end_date'] >= $now;
                        $isUpcoming = $event['status'] == 1 && $event['start_date'] > $now;
                        $isExpired = $event['end_date'] < $now;
                    ?>
                    <tr>
                        <td>
                            <?php if (!empty($event['image'])): ?>
                                <img src="<?= asset('uploads/events/' . $event['image']) ?>" alt="" style="width:60px;height:40px;object-fit:cover;border-radius:8px;border:1px solid var(--border-color)">
                            <?php else: ?>
                                <div style="width:60px;height:40px;background:var(--bg-input);border-radius:8px;display:flex;align-items:center;justify-content:center;color:var(--text-muted);font-size:.8rem"><i class="fas fa-image"></i></div>
                            <?php endif; ?>
                        </td>
                        <td><strong><?= e($event['title']) ?></strong></td>
                        <td style="font-size:.82rem">
                            <div><?= formatDate($event['start_date']) ?></div>
                            <div style="color:var(--text-muted)">→ <?= formatDate($event['end_date']) ?></div>
                        </td>
                        <td>
                            <?php
                                switch ($event['reward_type']) {
                                    case 'money': echo '<span class="badge badge-success">' . formatMoney($event['reward_value']) . '</span>'; break;
                                    case 'points': echo '<span class="badge badge-info">x' . intval($event['reward_value']) . ' Điểm</span>'; break;
                                    case 'discount': echo '<span class="badge badge-warning">-' . intval($event['reward_value']) . '%</span>'; break;
                                    default: echo '<span class="badge badge-primary">Đặc biệt</span>'; break;
                                }
                            ?>
                        </td>
                        <td>
                            <?php if (!$event['status']): ?>
                                <span class="badge badge-danger">Tắt</span>
                            <?php elseif ($isActive): ?>
                                <span class="badge badge-success"><i class="fas fa-circle" style="font-size:.5rem"></i> Đang diễn ra</span>
                            <?php elseif ($isUpcoming): ?>
                                <span class="badge badge-info">Sắp tới</span>
                            <?php elseif ($isExpired): ?>
                                <span class="badge badge-warning">Đã kết thúc</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <a href="<?= url('/admin/events/edit/' . $event['id']) ?>" class="btn btn-sm btn-secondary"><i class="fas fa-edit"></i></a>
                            <a href="<?= url('/admin/events/delete/' . $event['id']) ?>" class="btn btn-sm btn-danger" onclick="return confirm('Xoá sự kiện này?')"><i class="fas fa-trash"></i></a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>
