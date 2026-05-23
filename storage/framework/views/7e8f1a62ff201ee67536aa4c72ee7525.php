<?php $__env->startSection('title', 'Relatorio #' . $audit->id); ?>
<?php $__env->startSection('content'); ?>
<?php
    $groups = ['High'=>[], 'Medium'=>[], 'Low'=>[], 'Informational'=>[]];
    foreach ($audit->pages as $page) {
        foreach ($page->endpoints as $endpoint) {
            foreach ($endpoint->vulnerabilities as $vulnerability) {
                $groups[$vulnerability->risk][] = $vulnerability;
            }
        }
    }

    $byCategory = [];
    foreach ($groups as $list) {
        foreach ($list as $v) {
            $cat = $v->owasp_category ?: 'Outras';
            $byCategory[$cat][] = $v;
        }
    }
    ksort($byCategory);
    $totals = $audit->totals();

    $riskBadge = [
        'High'          => 'danger',
        'Medium'        => 'warning',
        'Low'           => 'info',
        'Informational' => 'secondary',
    ];
?>

<div class="d-flex justify-content-between align-items-center mb-3">
    <div>
        <h2 class="mb-1">Relatorio de Auditoria #<?php echo e($audit->id); ?></h2>
        <small class="text-muted">Alvo: <code><?php echo e($audit->target_url); ?></code></small>
    </div>
    <div>
        <a href="<?php echo e(route('audits.show', $audit)); ?>" class="btn btn-outline-dark">Voltar</a>
        <a href="<?php echo e(route('audits.report.pdf', $audit)); ?>" class="btn btn-outline-dark">Exportar PDF</a>
    </div>
</div>

<div class="row g-3 mb-4">
    <?php $__currentLoopData = ['High'=>'Alto','Medium'=>'Medio','Low'=>'Baixo','Informational'=>'Informativo']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $k => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <div class="col-md-3">
            <div class="card border-<?php echo e($riskBadge[$k]); ?>">
                <div class="card-body">
                    <small class="text-muted"><?php echo e($label); ?></small>
                    <h3 class="text-<?php echo e($riskBadge[$k]); ?>"><?php echo e($totals[$k]); ?></h3>
                </div>
            </div>
        </div>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
</div>

<dl class="row mb-4 small">
    <dt class="col-sm-3">Estado</dt><dd class="col-sm-9"><?php echo e($audit->status); ?></dd>
    <dt class="col-sm-3">Inicio</dt><dd class="col-sm-9"><?php echo e($audit->started_at?->format('Y-m-d H:i:s')); ?></dd>
    <dt class="col-sm-3">Fim</dt><dd class="col-sm-9"><?php echo e($audit->finished_at?->format('Y-m-d H:i:s')); ?></dd>
    <dt class="col-sm-3">Paginas descobertas</dt><dd class="col-sm-9"><?php echo e($audit->pages->count()); ?></dd>
    <dt class="col-sm-3">Total de vulnerabilidades</dt>
    <dd class="col-sm-9"><?php echo e(array_sum($totals)); ?></dd>
</dl>

<h4 class="mt-4 mb-3">Detalhe por categoria OWASP Top 10</h4>

<?php $__currentLoopData = $byCategory; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $category => $items): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
    <div class="card mb-4">
        <div class="card-header bg-dark text-white">
            <strong><?php echo e($category); ?></strong>
            <span class="badge text-bg-light ms-2"><?php echo e(count($items)); ?></span>
        </div>
        <div class="card-body p-0">
            <?php $__currentLoopData = $items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $v): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="border-bottom p-3">
                    <div class="d-flex justify-content-between">
                        <div>
                            <span class="badge text-bg-<?php echo e($riskBadge[$v->risk]); ?> me-2"><?php echo e($v->risk); ?></span>
                            <strong><?php echo e($v->name); ?></strong>
                        </div>
                        <small class="text-muted"><?php echo e($v->check_code); ?> | CWE-<?php echo e($v->cwe_id); ?> | confianca: <?php echo e($v->confidence); ?></small>
                    </div>

                    <p class="mt-2 mb-2"><?php echo e($v->description); ?></p>

                    <form method="POST" action="<?php echo e(route('vulnerabilities.update', $v)); ?>" class="row g-2 mb-2">
                        <?php echo csrf_field(); ?> <?php echo method_field('PATCH'); ?>
                        <div class="col-md-3">
                            <select name="status" class="form-select form-select-sm">
                                <option value="open"           <?php if($v->status === 'open'): echo 'selected'; endif; ?>>Em aberto</option>
                                <option value="accepted"       <?php if($v->status === 'accepted'): echo 'selected'; endif; ?>>Aceite (risco assumido)</option>
                                <option value="false_positive" <?php if($v->status === 'false_positive'): echo 'selected'; endif; ?>>Falso positivo</option>
                                <option value="fixed"          <?php if($v->status === 'fixed'): echo 'selected'; endif; ?>>Corrigido</option>
                            </select>
                        </div>
                        <div class="col-md-7">
                            <input type="text" name="notes" value="<?php echo e($v->notes); ?>"
                                   placeholder="Notas internas (opcional)"
                                   class="form-control form-control-sm">
                        </div>
                        <div class="col-md-2">
                            <button class="btn btn-sm btn-outline-dark w-100">Guardar</button>
                        </div>
                    </form>

                    <?php if($v->evidence): ?>
                        <div class="mb-2">
                            <small class="text-muted d-block">Evidencia tecnica</small>
                            <pre class="bg-light p-2 small mb-0" style="white-space:pre-wrap;"><?php echo e($v->evidence); ?></pre>
                        </div>
                    <?php endif; ?>

                    <?php if($v->bad_example || $v->good_example): ?>
                        <div class="row g-2 mt-2">
                            <?php if($v->bad_example): ?>
                                <div class="col-md-6">
                                    <small class="text-danger d-block">Codigo vulneravel</small>
                                    <pre class="bg-light p-2 small mb-0" style="white-space:pre-wrap; border-left:3px solid #C8102E;"><?php echo e($v->bad_example); ?></pre>
                                </div>
                            <?php endif; ?>
                            <?php if($v->good_example): ?>
                                <div class="col-md-6">
                                    <small class="text-success d-block">Correccao recomendada</small>
                                    <pre class="bg-light p-2 small mb-0" style="white-space:pre-wrap; border-left:3px solid #198754;"><?php echo e($v->good_example); ?></pre>
                                </div>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>

                    <?php if($v->solution): ?>
                        <div class="mt-2">
                            <small class="text-muted d-block">Como mitigar</small>
                            <p class="mb-1 small"><?php echo e($v->solution); ?></p>
                        </div>
                    <?php endif; ?>

                    <small>
                        <?php if($v->reference): ?>
                            <a href="<?php echo e($v->reference); ?>" target="_blank" rel="noopener"><?php echo e($v->reference); ?></a>
                        <?php endif; ?>
                    </small>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
    </div>
<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\Monografia UnISCED\owasp-audit-mz\resources\views/reports/show.blade.php ENDPATH**/ ?>