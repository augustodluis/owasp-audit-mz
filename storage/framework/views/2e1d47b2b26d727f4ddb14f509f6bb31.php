<nav class="navbar navbar-expand-lg shadow-sm" style="background:#fff; border-bottom: 3px solid #C8102E;">
    <div class="container-fluid">
        <a class="navbar-brand d-flex align-items-center" href="<?php echo e(route('dashboard')); ?>">
            <img src="<?php echo e(asset('img/icon-256.png')); ?>" alt="" height="32" class="me-2">
            <strong>OWASP-AUDIT-MZ</strong>
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarMain">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarMain">
            <ul class="navbar-nav me-auto">
                <li class="nav-item"><a class="nav-link" href="<?php echo e(route('dashboard')); ?>">Painel</a></li>
                <li class="nav-item"><a class="nav-link" href="<?php echo e(route('audits.create')); ?>">Nova auditoria</a></li>
                <?php if(auth()->user()->isAdmin()): ?>
                    <li class="nav-item"><a class="nav-link" href="<?php echo e(route('admin.errors')); ?>">Erros</a></li>
                    <li class="nav-item"><a class="nav-link" href="<?php echo e(route('admin.users')); ?>">Utilizadores</a></li>
                <?php endif; ?>
            </ul>
            <ul class="navbar-nav">
                <li class="nav-item">
                    <span class="navbar-text me-3"><?php echo e(auth()->user()->name); ?></span>
                </li>
                <li class="nav-item">
                    <form method="POST" action="<?php echo e(route('logout')); ?>" class="d-inline">
                        <?php echo csrf_field(); ?>
                        <button class="btn btn-sm btn-outline-dark">Terminar sessao</button>
                    </form>
                </li>
            </ul>
        </div>
    </div>
</nav>
<?php /**PATH D:\Monografia UnISCED\owasp-audit-mz\resources\views/partials/navbar.blade.php ENDPATH**/ ?>