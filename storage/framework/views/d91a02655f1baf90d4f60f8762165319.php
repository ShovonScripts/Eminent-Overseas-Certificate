<?php $__env->startSection('title', 'Admin Activity Log'); ?>
<?php $__env->startSection('eyebrow', 'Security & Audit Trail'); ?>

<?php $__env->startSection('topbar_actions'); ?>
    <a class="btn btn-ghost" href="<?php echo e(route('admin.dashboard')); ?>">&larr; Back to dashboard</a>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
    <div class="card">
        <div class="card-head">
            <div>
                <h2>Admin Activity Log</h2>
                <p>Immutable audit trail of all staff and system administrator operations</p>
            </div>
            <span class="badge badge-muted"><?php echo e($logs->total()); ?> recorded events</span>
        </div>

        <?php if($logs->isEmpty()): ?>
            <div class="empty">
                <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="#CBD5E1" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" style="margin: 0 auto 12px;">
                    <path d="M3 12a9 9 0 1 0 9-9 9.75 9.75 0 0 0-6.74 2.74L3 8"/>
                    <path d="M3 3v5h5"/>
                    <path d="M12 7v5l4 2"/>
                </svg>
                <strong>No administrative activity recorded yet</strong>
                <p class="text-muted">Staff actions such as creating, updating, issuing and revoking certificates will be tracked here.</p>
            </div>
        <?php else: ?>
            <div class="table-wrap" style="border: none; border-radius: 0;">
                <table class="table">
                    <thead>
                    <tr>
                        <th>Timestamp</th>
                        <th>User</th>
                        <th>Operation</th>
                        <th>Target Model</th>
                        <th class="actions">Details</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php $__currentLoopData = $logs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $log): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <tr>
                            <td class="text-muted" style="font-size: var(--text-xs); white-space: nowrap;">
                                <?php echo e($log->created_at->diffForHumans()); ?>

                                <div class="cell-sub"><?php echo e($log->created_at->format('Y-m-d H:i:s')); ?></div>
                            </td>
                            <td>
                                <strong style="color: #0F172A;"><?php echo e($log->user?->name ?? 'System'); ?></strong>
                                <div class="cell-sub"><?php echo e($log->user?->email ?? '—'); ?></div>
                            </td>
                            <td>
                                <span class="badge badge-<?php echo e($log->actionColor()); ?>"><?php echo e($log->action); ?></span>
                            </td>
                            <td>
                                <?php if($log->model_type): ?>
                                    <div class="mono" style="font-weight: 600; color: #1E3A8A;"><?php echo e(class_basename($log->model_type)); ?></div>
                                    <?php if($log->model_id): ?>
                                        <div class="cell-sub mono">#<?php echo e($log->model_id); ?></div>
                                    <?php endif; ?>
                                <?php else: ?>
                                    <span class="text-muted">—</span>
                                <?php endif; ?>
                            </td>
                            <td class="actions">
                                <a class="btn btn-secondary btn-sm" href="#" onclick="showDetails(<?php echo e($log->id); ?>); return false;">
                                    View Payload
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tbody>
                </table>
            </div>

            <div class="pagination-wrap">
                <?php echo e($logs->links()); ?>

            </div>
        <?php endif; ?>
    </div>

    <!-- Modal for details -->
    <div id="details-modal" class="modal-overlay" style="display: none;">
        <div class="modal-content">
            <div class="modal-header">
                <h3>Activity Event Details</h3>
                <button class="modal-close" onclick="closeDetailsModal();" aria-label="Close modal">&times;</button>
            </div>
            <div class="modal-body" id="modal-body"></div>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
    function showDetails(logId) {
        fetch(`/admin/activity-logs/${logId}`)
            .then(response => response.json())
            .then(data => {
                const body = document.getElementById('modal-body');
                let html = `
                    <dl class="detail-grid">
                        <dt>Action</dt>
                        <dd><span class="badge badge-${data.action_color}">${data.action}</span></dd>
                        <dt>User</dt>
                        <dd><strong>${data.user?.name ?? 'System'}</strong> <span class="text-muted">(${data.user?.email ?? '—'})</span></dd>
                        <dt>Timestamp</dt>
                        <dd>${data.created_at}</dd>
                        <dt>IP Address</dt>
                        <dd class="mono">${data.ip_address ?? '—'}</dd>
                        <dt>User Agent</dt>
                        <dd class="text-muted" style="font-size: 11px; word-break: break-all;">${data.user_agent ?? '—'}</dd>
                `;
                
                if (data.model_type) {
                    html += `
                        <dt>Target</dt>
                        <dd><strong>${data.model_type}</strong> #${data.model_id ?? '—'}</dd>
                    `;
                }
                
                if (data.old_values) {
                    html += `
                        <dt>Previous State</dt>
                        <dd><pre style="font-size: 11px; max-height: 180px; overflow: auto; border-radius: 8px;">${JSON.stringify(data.old_values, null, 2)}</pre></dd>
                    `;
                }
                
                if (data.new_values) {
                    html += `
                        <dt>New State</dt>
                        <dd><pre style="font-size: 11px; max-height: 180px; overflow: auto; border-radius: 8px;">${JSON.stringify(data.new_values, null, 2)}</pre></dd>
                    `;
                }
                
                html += '</dl>';
                body.innerHTML = html;
                
                const modal = document.getElementById('details-modal');
                modal.style.display = 'flex';
                requestAnimationFrame(() => modal.classList.add('open'));
            });
    }
    
    function closeDetailsModal() {
        const modal = document.getElementById('details-modal');
        modal.classList.remove('open');
        setTimeout(() => modal.style.display = 'none', 200);
    }
    
    document.getElementById('details-modal').addEventListener('click', function(e) {
        if (e.target === this) closeDetailsModal();
    });
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\2026 New Project\Eminent Overseas Certificate\resources\views/admin/activity-logs/index.blade.php ENDPATH**/ ?>