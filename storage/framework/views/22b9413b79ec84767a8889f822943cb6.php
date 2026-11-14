<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex, nofollow">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
    <title><?php echo $__env->yieldContent('title', 'Dashboard'); ?> &middot; <?php echo e($schoolConfig['name']); ?></title>
    <?php echo app('Illuminate\Foundation\Vite')(['resources/css/app.css', 'resources/js/app.js']); ?>
</head>
<body>
<?php ($user = auth()->user()); ?>
<div class="sidebar-overlay" id="sidebar-overlay"></div>
<div class="admin-shell">
    <aside class="sidebar" id="admin-sidebar">
        <div class="identity">
            <img src="<?php echo e(asset($schoolConfig['images']['logo'])); ?>" alt="<?php echo e($schoolConfig['name']); ?>">
            <div>
                <div class="name"><?php echo e($schoolConfig['name']); ?></div>
                <div class="role"><?php echo e($user?->isSuperAdmin() ? 'Super Admin' : 'Staff'); ?></div>
            </div>
        </div>

        <div class="section-label">Workspace</div>
        <nav>
            <a href="<?php echo e(route('admin.dashboard')); ?>" class="<?php echo e(request()->routeIs('admin.dashboard') ? 'active' : ''); ?>">
                <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <rect width="7" height="9" x="3" y="3" rx="1"/>
                    <rect width="7" height="5" x="14" y="3" rx="1"/>
                    <rect width="7" height="9" x="14" y="12" rx="1"/>
                    <rect width="7" height="5" x="3" y="16" rx="1"/>
                </svg>
                <span>Dashboard</span>
            </a>
            <a href="<?php echo e(route('admin.certificates.index')); ?>" class="<?php echo e(request()->routeIs('admin.certificates.*') ? 'active' : ''); ?>">
                <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="m15.477 12.89 1.515 8.526a.5.5 0 0 1-.81.47l-3.58-2.687a1 1 0 0 0-1.197 0l-3.586 2.686a.5.5 0 0 1-.81-.469l1.514-8.526"/>
                    <circle cx="12" cy="8" r="6"/>
                </svg>
                <span>Certificates</span>
            </a>
        </nav>

        <div class="section-label">Reference Data</div>
        <nav>
            <a href="<?php echo e(route('admin.courses.index')); ?>" class="<?php echo e(request()->routeIs('admin.courses.*') ? 'active' : ''); ?>">
                <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M4 19.5v-15A2.5 2.5 0 0 1 6.5 2H20v20H6.5a2.5 2.5 0 0 1-2.5-2.5Z"/>
                    <path d="M6 6h10"/>
                    <path d="M6 10h10"/>
                </svg>
                <span>Courses</span>
            </a>
            <a href="<?php echo e(route('admin.instructors.index')); ?>" class="<?php echo e(request()->routeIs('admin.instructors.*') ? 'active' : ''); ?>">
                <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/>
                    <circle cx="9" cy="7" r="4"/>
                    <polyline points="16 11 18 13 22 9"/>
                </svg>
                <span>Instructors</span>
            </a>
        </nav>

        <div class="section-label">Audit & Security</div>
        <nav>
            <a href="<?php echo e(route('admin.logs.index')); ?>" class="<?php echo e(request()->routeIs('admin.logs.*') ? 'active' : ''); ?>">
                <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10"/>
                    <circle cx="12" cy="11" r="3"/>
                </svg>
                <span>Verification Logs</span>
            </a>
            <a href="<?php echo e(route('admin.activity-logs.index')); ?>" class="<?php echo e(request()->routeIs('admin.activity-logs.*') ? 'active' : ''); ?>">
                <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M3 12a9 9 0 1 0 9-9 9.75 9.75 0 0 0-6.74 2.74L3 8"/>
                    <path d="M3 3v5h5"/>
                    <path d="M12 7v5l4 2"/>
                </svg>
                <span>Admin Activity Log</span>
            </a>
            <a href="<?php echo e(route('admin.security.show')); ?>" class="<?php echo e(request()->routeIs('admin.security.*') ? 'active' : ''); ?>">
                <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <rect width="18" height="11" x="3" y="11" rx="2" ry="2"/>
                    <path d="M7 11V7a5 5 0 0 1 10 0v4"/>
                </svg>
                <span>Two-Factor Security</span>
            </a>
            <?php if($user?->isSuperAdmin()): ?>
                <a href="<?php echo e(route('admin.users.index')); ?>" class="<?php echo e(request()->routeIs('admin.users.*') ? 'active' : ''); ?>">
                    <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/>
                        <circle cx="9" cy="7" r="4"/>
                        <path d="M22 21v-2a4 4 0 0 0-3-3.87"/>
                        <path d="M16 3.13a4 4 0 0 1 0 7.75"/>
                    </svg>
                    <span>Staff Accounts</span>
                </a>
                <a href="<?php echo e(route('admin.settings.edit')); ?>" class="<?php echo e(request()->routeIs('admin.settings.*') ? 'active' : ''); ?>">
                    <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="4" x2="20" y1="21" y2="21"/>
                        <line x1="4" x2="20" y1="3" y2="3"/>
                        <line x1="4" x2="20" y1="12" y2="12"/>
                        <circle cx="16" cy="12" r="3"/>
                        <circle cx="8" cy="3" r="3"/>
                        <circle cx="12" cy="21" r="3"/>
                    </svg>
                    <span>System Settings</span>
                </a>
            <?php endif; ?>
        </nav>

        <div class="spacer"></div>

        <div class="footer">
            <a href="<?php echo e(route('verify.form')); ?>" target="_blank" rel="noopener">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M15 3h6v6"/>
                    <path d="M10 14 21 3"/>
                    <path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"/>
                </svg>
                <span>Public verify portal &nearr;</span>
            </a>
            <form method="post" action="<?php echo e(route('logout')); ?>">
                <?php echo csrf_field(); ?>
                <button type="submit">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/>
                        <polyline points="16 17 21 12 16 7"/>
                        <line x1="21" x2="9" y1="12" y2="12"/>
                    </svg>
                    <span>Sign out</span>
                </button>
            </form>
        </div>
    </aside>

    <div class="main">
        <header class="topbar">
            <div class="topbar-left">
                <button class="sidebar-toggle-btn" id="sidebar-toggle" aria-label="Toggle navigation">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="4" x2="20" y1="12" y2="12"/>
                        <line x1="4" x2="20" y1="6" y2="6"/>
                        <line x1="4" x2="20" y1="18" y2="18"/>
                    </svg>
                </button>
                <div>
                    <div class="crumb"><?php echo $__env->yieldContent('eyebrow', 'Administration'); ?></div>
                    <h1><?php echo $__env->yieldContent('title', 'Dashboard'); ?></h1>
                </div>
            </div>
            <div class="actions">
                <?php echo $__env->yieldContent('topbar_actions'); ?>
                <div class="who">
                    <div class="who-avatar">
                        <?php echo e(strtoupper(substr($user?->name ?? 'U', 0, 2))); ?>

                    </div>
                    <div class="who-details">
                        <strong><?php echo e($user?->name); ?></strong>
                        <span><?php echo e($user?->email); ?></span>
                    </div>
                </div>
            </div>
        </header>

        <main class="content">
            <?php if(session('status')): ?>
                <div class="alert alert-success">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="flex-shrink:0; margin-top:1px;">
                        <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/>
                        <polyline points="22 4 12 14.01 9 11.01"/>
                    </svg>
                    <div><?php echo e(session('status')); ?></div>
                </div>
            <?php endif; ?>

            <?php if(session('error')): ?>
                <div class="alert alert-danger">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="flex-shrink:0; margin-top:1px;">
                        <circle cx="12" cy="12" r="10"/>
                        <line x1="12" y1="8" x2="12" y2="12"/>
                        <line x1="12" y1="16" x2="12.01" y2="16"/>
                    </svg>
                    <div><?php echo e(session('error')); ?></div>
                </div>
            <?php endif; ?>

            <?php if($errors->any() && ! View::hasSection('skip_error_summary')): ?>
                <div class="alert alert-danger">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="flex-shrink:0; margin-top:1px;">
                        <circle cx="12" cy="12" r="10"/>
                        <line x1="12" y1="8" x2="12" y2="12"/>
                        <line x1="12" y1="16" x2="12.01" y2="16"/>
                    </svg>
                    <div>
                        <strong>Please correct the following:</strong>
                        <ul class="mb-0 mt-1" style="padding-left: 20px;">
                            <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <li><?php echo e($error); ?></li>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </ul>
                    </div>
                </div>
            <?php endif; ?>

            <?php echo $__env->yieldContent('content'); ?>
        </main>
    </div>
</div>

<script>
    (function() {
        const toggleBtn = document.getElementById('sidebar-toggle');
        const sidebar = document.getElementById('admin-sidebar');
        const overlay = document.getElementById('sidebar-overlay');

        if (toggleBtn && sidebar && overlay) {
            toggleBtn.addEventListener('click', function() {
                sidebar.classList.toggle('open');
                overlay.classList.toggle('open');
            });

            overlay.addEventListener('click', function() {
                sidebar.classList.remove('open');
                overlay.classList.remove('open');
            });
        }
    })();
</script>

<?php echo $__env->yieldPushContent('scripts'); ?>
</body>
</html>
<?php /**PATH C:\xampp\htdocs\2026 New Project\Eminent Overseas Certificate\resources\views/layouts/admin.blade.php ENDPATH**/ ?>