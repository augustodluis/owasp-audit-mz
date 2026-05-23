<?php $__env->startSection('title', 'Auditoria #' . $audit->id); ?>
<?php $__env->startSection('content'); ?>
<?php $totals = $audit->totals(); ?>

<div class="d-flex justify-content-between align-items-center mb-3">
    <h2 class="mb-0">Auditoria #<?php echo e($audit->id); ?></h2>
    <div>
        <a href="<?php echo e(route('audits.report', $audit)); ?>" class="btn btn-outline-dark">Ver relatorio</a>
        <a href="<?php echo e(route('audits.report.pdf', $audit)); ?>" class="btn btn-outline-dark">Exportar PDF</a>
        <a href="<?php echo e(route('audits.report.csv', $audit)); ?>" class="btn btn-outline-dark">Exportar CSV</a>
        <?php if($previous): ?>
            <a href="<?php echo e(route('audits.compare', $audit)); ?>" class="btn btn-outline-secondary">Comparar com #<?php echo e($previous->id); ?></a>
        <?php endif; ?>
        <?php if(! empty($audit->recipients)): ?>
            <form method="POST" action="<?php echo e(route('audits.resend', $audit)); ?>" class="d-inline">
                <?php echo csrf_field(); ?>
                <button class="btn btn-outline-primary">Reenviar relatorio</button>
            </form>
        <?php endif; ?>
        <form method="POST" action="<?php echo e(route('audits.destroy', $audit)); ?>" class="d-inline"
              onsubmit="return confirm('Eliminar definitivamente?');">
            <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
            <button class="btn btn-outline-danger">Eliminar</button>
        </form>
    </div>
</div>

<div class="row g-3 mb-4">
    <div class="col-md-3"><div class="card border-danger"><div class="card-body">
        <small class="text-muted">Alto</small><h3 class="text-danger"><?php echo e($totals['High']); ?></h3>
    </div></div></div>
    <div class="col-md-3"><div class="card border-warning"><div class="card-body">
        <small class="text-muted">Medio</small><h3 class="text-warning"><?php echo e($totals['Medium']); ?></h3>
    </div></div></div>
    <div class="col-md-3"><div class="card border-info"><div class="card-body">
        <small class="text-muted">Baixo</small><h3 class="text-info"><?php echo e($totals['Low']); ?></h3>
    </div></div></div>
    <div class="col-md-3"><div class="card border-secondary"><div class="card-body">
        <small class="text-muted">Informativo</small><h3><?php echo e($totals['Informational']); ?></h3>
    </div></div></div>
</div>

<dl class="row mb-4">
    <dt class="col-sm-3">Alvo</dt><dd class="col-sm-9"><code><?php echo e($audit->target_url); ?></code></dd>
    <dt class="col-sm-3">Estado</dt><dd class="col-sm-9"><?php echo e($audit->status); ?></dd>
    <dt class="col-sm-3">Inicio</dt><dd class="col-sm-9"><?php echo e($audit->started_at?->format('Y-m-d H:i:s')); ?></dd>
    <dt class="col-sm-3">Fim</dt><dd class="col-sm-9"><?php echo e($audit->finished_at?->format('Y-m-d H:i:s')); ?></dd>
    <dt class="col-sm-3">Paginas descobertas</dt><dd class="col-sm-9"><?php echo e($audit->pages->count()); ?></dd>
    <dt class="col-sm-3">Destinatarios</dt>
    <dd class="col-sm-9">
        <?php if(! empty($audit->recipients)): ?>
            <?php echo e(implode(', ', $audit->recipients)); ?>

            <?php if($audit->email_sent): ?>
                <span class="badge text-bg-success ms-2">enviado</span>
            <?php else: ?>
                <span class="badge text-bg-secondary ms-2">por enviar</span>
            <?php endif; ?>
        <?php else: ?>
            <span class="text-muted">nenhum</span>
        <?php endif; ?>
    </dd>
</dl>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\Monografia UnISCED\owasp-audit-mz\resources\views/audits/show.blade.php ENDPATH**/ ?>