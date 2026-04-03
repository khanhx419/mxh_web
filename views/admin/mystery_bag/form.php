<?php
    $isEdit = !empty($bag);
    $items = $items ?? [];
    $available = $available ?? 0;
    $sold = $sold ?? 0;
?>

<div class="admin-header">
    <h1><i class="fas fa-box-open"></i> <?= $isEdit ? 'Quản Lý Túi Mù: ' . e($bag['name']) : 'Thêm Túi Mù Mới' ?></h1>
    <a href="<?= url('/admin/mystery-bag') ?>" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Quay lại</a>
</div>

<?php if ($isEdit): ?>
<!-- ========== STATS BAR (Edit mode only) ========== -->
<div style="margin-bottom:20px;padding:14px 18px;background:var(--bg-card);border:1px solid var(--border-color);border-radius:12px;display:flex;align-items:center;gap:16px;flex-wrap:wrap">
    <div style="display:flex;align-items:center;gap:8px">
        <div style="width:40px;height:40px;border-radius:10px;background:linear-gradient(135deg,#6c63ff,#a78bfa);display:flex;align-items:center;justify-content:center;color:#fff;font-size:1.1rem"><i class="fas fa-box-open"></i></div>
        <div>
            <div style="font-size:.78rem;color:var(--text-muted)">Tổng tài khoản</div>
            <div style="font-size:1.1rem;font-weight:700;color:var(--text-primary)"><?= count($items) ?></div>
        </div>
    </div>
    <div style="width:1px;height:30px;background:var(--border-color)"></div>
    <div style="display:flex;align-items:center;gap:8px">
        <div style="width:40px;height:40px;border-radius:10px;background:linear-gradient(135deg,#00d4aa,#34d399);display:flex;align-items:center;justify-content:center;color:#fff;font-size:1.1rem"><i class="fas fa-check-circle"></i></div>
        <div>
            <div style="font-size:.78rem;color:var(--text-muted)">Còn hàng</div>
            <div style="font-size:1.1rem;font-weight:700;color:var(--accent-success)"><?= $available ?></div>
        </div>
    </div>
    <div style="width:1px;height:30px;background:var(--border-color)"></div>
    <div style="display:flex;align-items:center;gap:8px">
        <div style="width:40px;height:40px;border-radius:10px;background:linear-gradient(135deg,#ef4444,#f87171);display:flex;align-items:center;justify-content:center;color:#fff;font-size:1.1rem"><i class="fas fa-times-circle"></i></div>
        <div>
            <div style="font-size:.78rem;color:var(--text-muted)">Đã bán</div>
            <div style="font-size:1.1rem;font-weight:700;color:var(--accent-danger)"><?= $sold ?></div>
        </div>
    </div>
    <div style="width:1px;height:30px;background:var(--border-color)"></div>
    <div style="display:flex;align-items:center;gap:8px">
        <div style="width:40px;height:40px;border-radius:10px;background:linear-gradient(135deg,#fbbf24,#f59e0b);display:flex;align-items:center;justify-content:center;color:#fff;font-size:1.1rem"><i class="fas fa-tag"></i></div>
        <div>
            <div style="font-size:.78rem;color:var(--text-muted)">Giá bán</div>
            <div style="font-size:1.1rem;font-weight:700;color:var(--accent-warning)"><?= formatMoney($bag['price']) ?></div>
        </div>
    </div>
</div>
<?php endif; ?>

<!-- ========== SECTION 1: BAG SETTINGS ========== -->
<div class="mbadmin-card">
    <div class="mbadmin-card-header">
        <i class="fas fa-cog"></i>
        <h3><?= $isEdit ? 'Cài Đặt Túi Mù' : 'Thông Tin Túi Mù' ?></h3>
        <?php if ($isEdit): ?>
            <span class="mbadmin-badge"><?= $bag['status'] ? 'Đang bật' : 'Đã tắt' ?></span>
        <?php endif; ?>
    </div>
    <form action="<?= $isEdit ? url('/admin/mystery-bag/update/'.$bag['id']) : url('/admin/mystery-bag/store') ?>" method="POST" enctype="multipart/form-data">
        <?= csrfField() ?>
        <div class="mbadmin-card-body">
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px">
                <div class="form-group">
                    <label>Tên túi mù *</label>
                    <input type="text" name="name" class="form-control" value="<?= e($bag['name'] ?? '') ?>" required placeholder="VD: Túi Acc Liên Quân 20K">
                </div>
                <div class="form-group">
                    <label>Giá (VNĐ) *</label>
                    <input type="number" name="price" class="form-control" value="<?= e($bag['price'] ?? '') ?>" required placeholder="VD: 20000">
                    <small class="form-hint"><i class="fas fa-info-circle"></i> Khi user mua, hệ thống random 1 tài khoản từ kho và gửi cho khách.</small>
                </div>
            </div>
            <div class="form-group">
                <label>Mô tả</label>
                <textarea name="description" class="form-control" rows="2" placeholder="Mô tả ngắn về túi mù này..."><?= e($bag['description'] ?? '') ?></textarea>
            </div>
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;align-items:end">
                <div class="form-group">
                    <label>Hình ảnh túi</label>
                    <?php if ($isEdit && !empty($bag['image'])): ?>
                        <div style="margin-bottom:8px">
                            <img src="<?= url('/uploads/mystery_bags/'.$bag['image']) ?>" style="width:80px;height:80px;object-fit:cover;border-radius:10px;border:1px solid var(--border-color)">
                        </div>
                    <?php endif; ?>
                    <input type="file" name="image" class="form-control" accept="image/*">
                </div>
                <div class="form-group">
                    <label>
                        <input type="checkbox" name="status" <?= ($bag['status'] ?? 1) ? 'checked' : '' ?>> Kích hoạt túi mù này
                    </label>
                    <small class="form-hint"><i class="fas fa-info-circle"></i> Tắt = không hiển thị cho user</small>
                </div>
            </div>
            <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> <?= $isEdit ? 'Lưu Cài Đặt' : 'Tạo Túi Mù' ?></button>
        </div>
    </form>
</div>


<?php if ($isEdit): ?>
<!-- ========== SECTION 2: ADD SINGLE ACCOUNT ========== -->
<div class="mbadmin-card">
    <div class="mbadmin-card-header">
        <i class="fas fa-user-plus"></i>
        <h3>Thêm 1 Tài Khoản Thủ Công</h3>
    </div>
    <form action="<?= url('/admin/mystery-bag/' . $bag['id'] . '/items/store') ?>" method="POST">
        <?= csrfField() ?>
        <div class="mbadmin-card-body">
            <div class="form-group">
                <label>Tên hiển thị *</label>
                <input type="text" name="name" class="form-control" required placeholder="VD: Acc VIP Rank Kim Cương">
                <small class="form-hint"><i class="fas fa-info-circle"></i> Tên hiện cho user khi nhận tài khoản.</small>
            </div>
            <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:12px">
                <div class="form-group">
                    <label><i class="fas fa-user" style="color:var(--accent-primary);margin-right:4px"></i> Username *</label>
                    <input type="text" name="acct_username" id="add_username" class="form-control" required placeholder="player123">
                </div>
                <div class="form-group">
                    <label><i class="fas fa-lock" style="color:var(--accent-warning);margin-right:4px"></i> Password</label>
                    <input type="text" name="acct_password" id="add_password" class="form-control" placeholder="abc@123">
                </div>
                <div class="form-group">
                    <label><i class="fas fa-envelope" style="color:var(--accent-success);margin-right:4px"></i> Email</label>
                    <input type="text" name="acct_email" id="add_email" class="form-control" placeholder="player@gmail.com">
                </div>
            </div>
            <div class="form-group">
                <label>Thông tin bổ sung (tuỳ chọn)</label>
                <textarea name="acct_extra" id="add_extra" class="form-control" rows="2" placeholder="VD: Server: Việt Nam / Rank: Kim Cương / 50+ skins"></textarea>
            </div>
            <input type="hidden" name="content" id="add_content_combined">
            <input type="hidden" name="value" value="0">
            <button type="submit" class="btn btn-primary"><i class="fas fa-plus"></i> Thêm Tài Khoản</button>
        </div>
    </form>
</div>

<!-- ========== SECTION 3: BULK IMPORT ========== -->
<div class="mbadmin-card">
    <div class="mbadmin-card-header">
        <i class="fas fa-file-import"></i>
        <h3>Import Hàng Loạt</h3>
        <span id="import-count-badge" style="display:none;font-size:.78rem;padding:4px 10px;border-radius:20px;background:rgba(99,102,241,.12);color:var(--accent-primary);font-weight:600;margin-left:auto">
            <i class="fas fa-list"></i> <span id="import-line-count">0</span> tài khoản
        </span>
    </div>
    <div class="mbadmin-card-body">
        <!-- Tabs -->
        <div style="display:flex;gap:4px;margin-bottom:16px;background:var(--bg-body);border-radius:10px;padding:4px">
            <button type="button" class="import-tab active" data-tab="paste" style="flex:1;padding:8px 12px;border:none;border-radius:8px;font-size:.84rem;font-weight:600;cursor:pointer;transition:all .2s;background:var(--accent-primary);color:#fff">
                <i class="fas fa-paste"></i> Dán Text
            </button>
            <button type="button" class="import-tab" data-tab="file" style="flex:1;padding:8px 12px;border:none;border-radius:8px;font-size:.84rem;font-weight:600;cursor:pointer;transition:all .2s;background:transparent;color:var(--text-secondary)">
                <i class="fas fa-file-upload"></i> Upload File
            </button>
        </div>

        <form action="<?= url('/admin/mystery-bag/' . $bag['id'] . '/items/bulk-import') ?>" method="POST" enctype="multipart/form-data">
            <?= csrfField() ?>

            <!-- Tab: Paste Text -->
            <div id="tab-paste" class="import-tab-content">
                <textarea name="bulk_accounts" id="bulk-accounts-text" class="form-control" rows="6" placeholder="username1|password1|email1@gmail.com&#10;username2|password2|email2@gmail.com&#10;username3|password3|email3@gmail.com" style="font-family:'Courier New',monospace;font-size:.84rem;line-height:1.6"></textarea>
                <div style="margin-top:6px;padding:8px 12px;background:rgba(99,102,241,.05);border-radius:8px;border-left:3px solid var(--accent-primary)">
                    <small style="font-size:.78rem;color:var(--text-secondary)">
                        <i class="fas fa-info-circle" style="color:var(--accent-primary);margin-right:4px"></i>
                        Mỗi dòng 1 tài khoản. Format: <code style="background:rgba(99,102,241,.12);padding:1px 5px;border-radius:3px">tài khoản|mật khẩu|email</code>
                    </small>
                </div>
            </div>

            <!-- Tab: File Upload -->
            <div id="tab-file" class="import-tab-content" style="display:none">
                <div id="file-drop-zone" style="border:2px dashed var(--border-color);border-radius:12px;padding:30px;text-align:center;cursor:pointer;transition:all .2s;background:var(--bg-body)">
                    <i class="fas fa-cloud-upload-alt" style="font-size:2rem;color:var(--accent-primary);margin-bottom:8px;display:block"></i>
                    <p style="font-size:.88rem;color:var(--text-primary);margin-bottom:4px;font-weight:600">Kéo thả file hoặc click để chọn</p>
                    <p style="font-size:.78rem;color:var(--text-muted);margin:0">Hỗ trợ: .json, .txt — tối đa 5MB</p>
                    <input type="file" name="bulk_file" id="bulk-file-input" accept=".json,.txt" style="display:none">
                </div>
                <div id="file-info" style="display:none;margin-top:10px;padding:10px 14px;background:rgba(0,212,170,.08);border-radius:8px;border:1px solid rgba(0,212,170,.2)">
                    <i class="fas fa-file-alt" style="color:var(--accent-success);margin-right:6px"></i>
                    <span id="file-name" style="font-size:.84rem;font-weight:600;color:var(--text-primary)"></span>
                    <span id="file-size" style="font-size:.78rem;color:var(--text-muted);margin-left:8px"></span>
                    <button type="button" id="file-remove" style="float:right;background:none;border:none;color:var(--accent-danger);cursor:pointer;font-size:.9rem"><i class="fas fa-times"></i></button>
                </div>
                <div style="margin-top:8px;padding:8px 12px;background:rgba(99,102,241,.05);border-radius:8px;border-left:3px solid var(--accent-primary)">
                    <small style="font-size:.78rem;color:var(--text-secondary)">
                        <i class="fas fa-info-circle" style="color:var(--accent-primary);margin-right:4px"></i>
                        <strong>JSON:</strong> <code style="background:rgba(99,102,241,.12);padding:1px 5px;border-radius:3px">["user|pass|email", ...]</code> hoặc
                        <code style="background:rgba(99,102,241,.12);padding:1px 5px;border-radius:3px">[{"username":"..","password":"..","email":".."}]</code><br>
                        <i class="fas fa-info-circle" style="color:var(--accent-primary);margin-right:4px;visibility:hidden"></i>
                        <strong>TXT:</strong> Mỗi dòng: <code style="background:rgba(99,102,241,.12);padding:1px 5px;border-radius:3px">tài khoản|mật khẩu|email</code>
                    </small>
                </div>
            </div>

            <div style="margin-top:12px">
                <button type="submit" class="btn btn-primary"><i class="fas fa-file-import"></i> Import Tất Cả</button>
            </div>
        </form>
    </div>
</div>

<!-- ========== SECTION 4: INVENTORY TABLE ========== -->
<div class="mbadmin-card">
    <div class="mbadmin-card-header">
        <i class="fas fa-warehouse"></i>
        <h3>Kho Tài Khoản (<?= count($items) ?>)</h3>
    </div>
    <div style="overflow-x:auto">
        <table class="table" style="margin:0">
            <thead>
                <tr>
                    <th style="width:40px">#</th>
                    <th>Username</th>
                    <th>Mật khẩu</th>
                    <th>Email</th>
                    <th style="width:90px">Trạng thái</th>
                    <th style="width:90px">Thao tác</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($items)): ?>
                    <tr><td colspan="6" class="text-center text-muted" style="padding:24px"><i class="fas fa-inbox" style="font-size:1.2rem;display:block;margin-bottom:6px;opacity:.4"></i>Chưa có tài khoản nào. Hãy thêm bằng form ở trên!</td></tr>
                <?php else: ?>
                    <?php foreach ($items as $i => $item):
                        // Parse content to show individual fields
                        $u = ''; $p = ''; $em = '';
                        if (!empty($item['content'])) {
                            foreach (explode("\n", $item['content']) as $line) {
                                $line = trim($line);
                                if (stripos($line, 'Tài khoản:') === 0) $u = trim(substr($line, strlen('Tài khoản:')));
                                elseif (stripos($line, 'Mật khẩu:') === 0) $p = trim(substr($line, strlen('Mật khẩu:')));
                                elseif (stripos($line, 'Email:') === 0) $em = trim(substr($line, strlen('Email:')));
                            }
                        }
                        $st = $item['status'] ?? 1;
                    ?>
                        <tr style="<?= !$st ? 'opacity:.55' : '' ?>">
                            <td style="font-size:.78rem;color:var(--text-muted)"><?= $i + 1 ?></td>
                            <td><code style="font-size:.82rem"><?= e($u ?: $item['name']) ?></code></td>
                            <td><code style="font-size:.82rem"><?= e($p ?: '—') ?></code></td>
                            <td style="font-size:.82rem;color:var(--text-secondary)"><?= e($em ?: '—') ?></td>
                            <td>
                                <?= $st
                                    ? '<span class="badge badge-success" style="font-size:.72rem"><i class="fas fa-check"></i> Còn</span>'
                                    : '<span class="badge badge-danger" style="font-size:.72rem"><i class="fas fa-times"></i> Đã bán</span>'
                                ?>
                            </td>
                            <td>
                                <a href="<?= url('/admin/mystery-bag/items/edit/' . $item['id']) ?>" class="btn btn-sm btn-secondary" title="Sửa"><i class="fas fa-edit"></i></a>
                                <a href="<?= url('/admin/mystery-bag/items/delete/' . $item['id']) ?>" class="btn btn-sm btn-danger" onclick="return confirm('Xoá tài khoản này?')" title="Xoá"><i class="fas fa-trash"></i></a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
<?php endif; ?>


<style>
.mbadmin-card {
    background: var(--bg-card);
    border: 1px solid var(--border-color);
    border-radius: 14px;
    margin-bottom: 20px;
    overflow: hidden;
}
.mbadmin-card-header {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 14px 20px;
    border-bottom: 1px solid var(--border-color);
    background: rgba(99,102,241,.03);
}
.mbadmin-card-header i:first-child {
    color: var(--accent-primary);
    font-size: .95rem;
}
.mbadmin-card-header h3 {
    font-size: .92rem;
    font-weight: 700;
    margin: 0;
    color: var(--text-primary);
}
.mbadmin-card-body {
    padding: 20px;
}
.mbadmin-badge {
    margin-left: auto;
    font-size: .72rem;
    font-weight: 600;
    padding: 3px 10px;
    border-radius: 20px;
    background: rgba(0,212,170,.12);
    color: var(--accent-success);
}
.form-hint { display: block; margin-top: 4px; font-size: .78rem; color: var(--text-muted); }
.form-hint i { margin-right: 4px; color: var(--accent-primary); }
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Build content field for single add form
    var addForm = document.querySelector('form[action*="items/store"]');
    if (addForm) {
        addForm.addEventListener('submit', function() {
            var u = document.getElementById('add_username').value.trim();
            var p = document.getElementById('add_password').value.trim();
            var em = document.getElementById('add_email').value.trim();
            var x = document.getElementById('add_extra').value.trim();
            var content = '';
            if (u) content += 'Tài khoản: ' + u + '\n';
            if (p) content += 'Mật khẩu: ' + p + '\n';
            if (em) content += 'Email: ' + em + '\n';
            if (x) content += x;
            document.getElementById('add_content_combined').value = content.trim();
        });
    }

    // Tab switching
    document.querySelectorAll('.import-tab').forEach(function(tab) {
        tab.addEventListener('click', function() {
            document.querySelectorAll('.import-tab').forEach(function(t) {
                t.style.background = 'transparent';
                t.style.color = 'var(--text-secondary)';
                t.classList.remove('active');
            });
            this.style.background = 'var(--accent-primary)';
            this.style.color = '#fff';
            this.classList.add('active');
            document.querySelectorAll('.import-tab-content').forEach(function(c) { c.style.display = 'none'; });
            document.getElementById('tab-' + this.dataset.tab).style.display = 'block';
        });
    });

    // Live line counter
    var textarea = document.getElementById('bulk-accounts-text');
    var badge = document.getElementById('import-count-badge');
    var countEl = document.getElementById('import-line-count');
    if (textarea) {
        textarea.addEventListener('input', function() {
            var lines = this.value.split('\n').filter(function(l) { return l.trim().length > 0; });
            countEl.textContent = lines.length;
            badge.style.display = lines.length > 0 ? 'inline-block' : 'none';
        });
    }

    // File drop zone
    var dropZone = document.getElementById('file-drop-zone');
    var fileInput = document.getElementById('bulk-file-input');
    var fileInfo = document.getElementById('file-info');
    var fileName = document.getElementById('file-name');
    var fileSize = document.getElementById('file-size');
    var fileRemove = document.getElementById('file-remove');

    if (dropZone) {
        dropZone.addEventListener('click', function() { fileInput.click(); });
        dropZone.addEventListener('dragover', function(e) {
            e.preventDefault();
            this.style.borderColor = 'var(--accent-primary)';
            this.style.background = 'rgba(99,102,241,.06)';
        });
        dropZone.addEventListener('dragleave', function(e) {
            e.preventDefault();
            this.style.borderColor = 'var(--border-color)';
            this.style.background = 'var(--bg-body)';
        });
        dropZone.addEventListener('drop', function(e) {
            e.preventDefault();
            this.style.borderColor = 'var(--border-color)';
            this.style.background = 'var(--bg-body)';
            if (e.dataTransfer.files.length) {
                fileInput.files = e.dataTransfer.files;
                showFileInfo(e.dataTransfer.files[0]);
            }
        });
        fileInput.addEventListener('change', function() {
            if (this.files.length) showFileInfo(this.files[0]);
        });
        if (fileRemove) {
            fileRemove.addEventListener('click', function() {
                fileInput.value = '';
                fileInfo.style.display = 'none';
                dropZone.style.display = 'block';
                if (badge) badge.style.display = 'none';
            });
        }
    }

    function showFileInfo(file) {
        fileName.textContent = file.name;
        var kb = (file.size / 1024).toFixed(1);
        fileSize.textContent = '(' + kb + ' KB)';
        fileInfo.style.display = 'block';
        dropZone.style.display = 'none';
        var reader = new FileReader();
        reader.onload = function(e) {
            var content = e.target.result.trim();
            var count = 0;
            try {
                var json = JSON.parse(content);
                count = Array.isArray(json) ? json.length : 0;
            } catch(ex) {
                count = content.split('\n').filter(function(l) { return l.trim().length > 0; }).length;
            }
            if (countEl) countEl.textContent = count;
            if (badge) badge.style.display = count > 0 ? 'inline-block' : 'none';
        };
        reader.readAsText(file);
    }
});
</script>
