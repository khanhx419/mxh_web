<div class="admin-header">
    <h1><i class="fas fa-box-open"></i> Tài khoản: <?= e($bag['name']) ?></h1>
    <div style="display:flex;gap:8px">
        <a href="<?= url('/admin/mystery-bag/' . $bag['id'] . '/items/add') ?>" class="btn btn-primary"><i class="fas fa-plus"></i> Thêm tài khoản</a>
        <a href="<?= url('/admin/mystery-bag') ?>" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Quay lại</a>
    </div>
</div>

<div style="margin-bottom:16px;padding:14px 18px;background:var(--bg-card);border:1px solid var(--border-color);border-radius:12px;display:flex;align-items:center;gap:14px;flex-wrap:wrap">
    <div style="font-size:.85rem;color:var(--text-secondary)">
        <i class="fas fa-info-circle" style="color:var(--accent-primary);margin-right:4px"></i>
        <strong>Túi:</strong> <?= e($bag['name']) ?> &bull;
        <strong>Giá:</strong> <?= formatMoney($bag['price']) ?> &bull;
        <strong>Tổng items:</strong> <?= count($items) ?> &bull;
        <strong>Tổng xác suất:</strong> <?= array_sum(array_column($items, 'probability')) ?>
    </div>
</div>

<!-- Quick Probability Editor -->
<?php if (!empty($items)): ?>
<div style="margin-bottom:20px;padding:20px;background:var(--bg-card);border:1px solid var(--border-color);border-radius:14px">
    <h3 style="margin-bottom:14px;font-size:.95rem"><i class="fas fa-sliders-h" style="color:var(--accent-primary)"></i> Chỉnh Xác Suất Nhanh</h3>
    
    <!-- Visual Bar -->
    <div style="height:12px;border-radius:6px;overflow:hidden;display:flex;background:var(--bg-input);margin-bottom:14px">
        <?php 
        $colors = ['#6c63ff','#e94560','#00d4aa','#ffa726','#29b6f6','#ab47bc','#ef5350','#66bb6a','#42a5f5','#ff7043'];
        foreach ($items as $i => $item): 
            $c = $colors[$i % count($colors)];
        ?>
            <div style="width:<?= $item['percentage'] ?>%;background:<?= $c ?>;transition:width .3s" title="<?= e($item['name']) ?>: <?= $item['percentage'] ?>%"></div>
        <?php endforeach; ?>
    </div>

    <form action="<?= url('/admin/mystery-bag/' . $bag['id'] . '/probabilities') ?>" method="POST">
        <?= csrfField() ?>
        <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(200px,1fr));gap:10px">
            <?php foreach ($items as $i => $item): 
                $c = $colors[$i % count($colors)];
                $pctColor = $item['percentage'] >= 40 ? 'var(--accent-success)' : ($item['percentage'] >= 15 ? 'var(--accent-warning)' : 'var(--accent-danger)');
            ?>
                <div style="padding:12px;background:var(--bg-body);border-radius:10px;border:1px solid var(--border-color)">
                    <div style="display:flex;justify-content:space-between;margin-bottom:6px">
                        <span style="font-size:.82rem;font-weight:600;color:<?= $c ?>"><?= e($item['name']) ?></span>
                        <span style="font-size:.78rem;font-weight:700;color:<?= $pctColor ?>"><?= $item['percentage'] ?>%</span>
                    </div>
                    <input type="range" name="probability[<?= $item['id'] ?>]" value="<?= $item['probability'] ?>" min="0" max="100" 
                        style="width:100%;accent-color:<?= $c ?>" 
                        oninput="this.nextElementSibling.value=this.value">
                    <input type="number" value="<?= $item['probability'] ?>" min="0" class="form-control" 
                        style="margin-top:4px;font-size:.82rem;padding:4px 8px"
                        oninput="this.previousElementSibling.value=this.value"
                        name="probability[<?= $item['id'] ?>]">
                </div>
            <?php endforeach; ?>
        </div>
        <button type="submit" class="btn btn-primary" style="margin-top:14px"><i class="fas fa-save"></i> Lưu xác suất</button>
    </form>
</div>
<?php endif; ?>

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
                    Mỗi dòng 1 tài khoản. Format: <code style="background:rgba(99,102,241,.12);padding:1px 5px;border-radius:3px">username|password|email</code>
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
                    <strong>TXT:</strong> Mỗi dòng 1 tài khoản: <code style="background:rgba(99,102,241,.12);padding:1px 5px;border-radius:3px">username|password|email</code>
                </small>
            </div>
        </div>

        <!-- Default Probability -->
        <div style="margin-top:12px;display:flex;align-items:center;gap:12px;flex-wrap:wrap">
            <div style="display:flex;align-items:center;gap:6px">
                <label style="font-size:.82rem;color:var(--text-secondary);white-space:nowrap;margin:0">Xác suất mặc định:</label>
                <input type="number" name="default_probability" value="10" min="1" max="100" class="form-control" style="width:80px;padding:6px 10px;font-size:.84rem">
            </div>
            <button type="submit" class="btn btn-primary" style="margin-left:auto">
                <i class="fas fa-file-import"></i> Import Tài Khoản
            </button>
        </div>
    </form>
</div>

<!-- Advanced: Old format bulk add -->
<div style="margin-bottom:20px;padding:16px;background:var(--bg-card);border:1px solid var(--border-color);border-radius:12px">
    <details>
        <summary style="cursor:pointer;font-weight:600;font-size:.85rem;color:var(--text-secondary)">
            <i class="fas fa-layer-group" style="color:var(--text-muted)"></i> Thêm nâng cao (format cũ: Tên|Giá trị|Nội dung|Xác suất)
        </summary>
        <form action="<?= url('/admin/mystery-bag/' . $bag['id'] . '/items/bulk-add') ?>" method="POST" style="margin-top:12px">
            <?= csrfField() ?>
            <textarea name="bulk_items" class="form-control" rows="4" placeholder="Tên|Giá trị|Nội dung|Xác suất&#10;VD: Acc VIP|150000|Tài khoản rank cao|20&#10;Acc Thường|30000|Tài khoản cơ bản|50" style="font-family:'Courier New',monospace;font-size:.82rem"></textarea>
            <small style="display:block;margin-top:4px;font-size:.78rem;color:var(--text-muted)">Mỗi dòng 1 item. Format: Tên|Giá trị|Nội dung|Xác suất</small>
            <button type="submit" class="btn btn-secondary" style="margin-top:8px"><i class="fas fa-plus"></i> Thêm tất cả</button>
        </form>
    </details>
</div>

<!-- Items Table -->
<div class="table-wrapper">
    <table class="table">
        <thead>
            <tr>
                <th>#</th>
                <th>Tên item</th>
                <th>Giá trị</th>
                <th>Nội dung / Tài khoản</th>
                <th>Xác suất</th>
                <th>Trạng thái</th>
                <th>Thao tác</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($items)): ?>
                <tr><td colspan="7" class="text-center text-muted">Chưa có tài khoản nào. Hãy thêm mới!</td></tr>
            <?php else: ?>
                <?php $totalProb = array_sum(array_column($items, 'probability')); ?>
                <?php foreach ($items as $i => $item): ?>
                    <tr>
                        <td><?= $i + 1 ?></td>
                        <td><strong><?= e($item['name']) ?></strong></td>
                        <td class="text-success"><?= formatMoney($item['value']) ?></td>
                        <td>
                            <div style="max-width:300px;font-size:.82rem;color:var(--text-secondary);white-space:pre-wrap;word-break:break-all"><?= e(mb_substr($item['content'], 0, 120)) ?><?= mb_strlen($item['content']) > 120 ? '...' : '' ?></div>
                        </td>
                        <td>
                            <span class="badge badge-info"><?= $item['probability'] ?></span>
                            <?php if ($totalProb > 0): ?>
                                <small style="color:var(--text-muted);margin-left:4px">(<?= $item['percentage'] ?>%)</small>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php $st = $item['status'] ?? 1; ?>
                            <?= $st ? '<span class="badge badge-success">Bật</span>' : '<span class="badge badge-danger">Tắt</span>' ?>
                        </td>
                        <td>
                            <a href="<?= url('/admin/mystery-bag/items/edit/' . $item['id']) ?>" class="btn btn-sm btn-secondary"><i class="fas fa-edit"></i></a>
                            <a href="<?= url('/admin/mystery-bag/items/delete/' . $item['id']) ?>" class="btn btn-sm btn-danger" onclick="return confirm('Xoá item này?')"><i class="fas fa-trash"></i></a>
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
        // Try to count lines
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
