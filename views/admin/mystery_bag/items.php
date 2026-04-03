<div class="admin-header">
    <h1><i class="fas fa-box-open"></i> Tài khoản: <?= e($bag['name']) ?></h1>
    <div style="display:flex;gap:8px">
        <a href="<?= url('/admin/mystery-bag/' . $bag['id'] . '/items/add') ?>" class="btn btn-primary"><i class="fas fa-plus"></i> Thêm tài khoản</a>
        <a href="<?= url('/admin/mystery-bag') ?>" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Quay lại</a>
    </div>
</div>

<!-- Stats Bar -->
<div style="margin-bottom:16px;padding:14px 18px;background:var(--bg-card);border:1px solid var(--border-color);border-radius:12px;display:flex;align-items:center;gap:14px;flex-wrap:wrap">
    <div style="font-size:.85rem;color:var(--text-secondary)">
        <i class="fas fa-info-circle" style="color:var(--accent-primary);margin-right:4px"></i>
        <strong>Túi:</strong> <?= e($bag['name']) ?> &bull;
        <strong>Giá:</strong> <?= formatMoney($bag['price']) ?> &bull;
        <strong>Tổng:</strong> <?= count($items) ?> tài khoản &bull;
        <span style="color:var(--accent-success);font-weight:600"><i class="fas fa-check-circle"></i> Còn lại: <?= $available ?></span> &bull;
        <span style="color:var(--accent-danger);font-weight:600"><i class="fas fa-times-circle"></i> Đã phát: <?= $sold ?></span>
    </div>
</div>

<!-- Bulk Import Accounts -->
<div style="margin-bottom:20px;padding:20px;background:var(--bg-card);border:1px solid var(--border-color);border-radius:14px">
    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:16px">
        <h3 style="font-size:.95rem;font-weight:700;margin:0;color:var(--text-primary)">
            <i class="fas fa-file-import" style="color:var(--accent-info);margin-right:6px"></i> Import Tài Khoản Hàng Loạt
        </h3>
        <span id="import-count-badge" style="display:none;font-size:.78rem;padding:4px 10px;border-radius:20px;background:rgba(99,102,241,.12);color:var(--accent-primary);font-weight:600">
            <i class="fas fa-list"></i> <span id="import-line-count">0</span> tài khoản
        </span>
    </div>

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
                    Mỗi dòng 1 tài khoản. Format: <code style="background:rgba(99,102,241,.12);padding:1px 5px;border-radius:3px">tài khoản|mật khẩu|email</code> — Khi user mua sẽ random 1 acc từ danh sách.
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
                    <strong>TXT:</strong> Mỗi dòng 1 tài khoản: <code style="background:rgba(99,102,241,.12);padding:1px 5px;border-radius:3px">tài khoản|mật khẩu|email</code>
                </small>
            </div>
        </div>

        <div style="margin-top:12px;display:flex;align-items:center;gap:12px;flex-wrap:wrap">
            <button type="submit" class="btn btn-primary" style="margin-left:auto">
                <i class="fas fa-file-import"></i> Import Tài Khoản
            </button>
        </div>
    </form>
</div>

<!-- Items Table -->
<div class="table-wrapper">
    <table class="table">
        <thead>
            <tr>
                <th>#</th>
                <th>Tên / Username</th>
                <th>Nội dung tài khoản</th>
                <th>Trạng thái</th>
                <th>Thao tác</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($items)): ?>
                <tr><td colspan="5" class="text-center text-muted">Chưa có tài khoản nào. Hãy thêm mới!</td></tr>
            <?php else: ?>
                <?php foreach ($items as $i => $item): ?>
                    <tr>
                        <td><?= $i + 1 ?></td>
                        <td><strong><?= e($item['name']) ?></strong></td>
                        <td>
                            <div style="max-width:350px;font-size:.82rem;color:var(--text-secondary);white-space:pre-wrap;word-break:break-all"><?= e(mb_substr($item['content'], 0, 150)) ?><?= mb_strlen($item['content']) > 150 ? '...' : '' ?></div>
                        </td>
                        <td>
                            <?php $st = $item['status'] ?? 1; ?>
                            <?= $st ? '<span class="badge badge-success"><i class="fas fa-check"></i> Còn</span>' : '<span class="badge badge-danger"><i class="fas fa-times"></i> Đã phát</span>' ?>
                        </td>
                        <td>
                            <a href="<?= url('/admin/mystery-bag/items/edit/' . $item['id']) ?>" class="btn btn-sm btn-secondary"><i class="fas fa-edit"></i></a>
                            <a href="<?= url('/admin/mystery-bag/items/delete/' . $item['id']) ?>" class="btn btn-sm btn-danger" onclick="return confirm('Xoá tài khoản này?')"><i class="fas fa-trash"></i></a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
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

    // Live line counter for textarea
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
        fileRemove.addEventListener('click', function() {
            fileInput.value = '';
            fileInfo.style.display = 'none';
            dropZone.style.display = 'block';
            badge.style.display = 'none';
        });
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
            countEl.textContent = count;
            badge.style.display = count > 0 ? 'inline-block' : 'none';
        };
        reader.readAsText(file);
    }
});
</script>
