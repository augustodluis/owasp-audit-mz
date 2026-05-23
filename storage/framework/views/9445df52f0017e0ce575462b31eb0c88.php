<?php $__env->startSection('title', 'Painel'); ?>
<?php $__env->startSection('content'); ?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="mb-0">As suas auditorias</h2>
    <a href="<?php echo e(route('audits.create')); ?>" class="btn" style="background:#C8102E; color:#fff;">
        Nova auditoria
    </a>
</div>

<?php if($audits->isEmpty()): ?>
    <div class="alert alert-info">
        Ainda nao iniciou nenhuma auditoria. Clique em "Nova auditoria" para comecar.
    </div>
<?php else: ?>
    <div class="table-responsive">
        <table class="table align-middle">
            <thead class="table-light">
                <tr>
                    <th>#</th>
                    <th>Alvo</th>
                    <th>Estado</th>
                    <th>Iniciada</th>
                    <th>Concluida</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                <?php $__currentLoopData = $audits; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $audit): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <tr>
                        <td><?php echo e($audit->id); ?></td>
                        <td><code><?php echo e($audit->target_url); ?></code></td>
                        <td>
                            <?php $badge = match($audit->status) {
                                'completed' => 'success',
                                'running'   => 'primary',
                                'failed'    => 'danger',
                                'cancelled' => 'secondary',
                                default     => 'warning',
                            }; ?>
                            <span class="badge text-bg-<?php echo e($badge); ?>"><?php echo e($audit->status); ?></span>
                        </td>
                        <td><?php echo e($audit->started_at?->format('Y-m-d H:i')); ?></td>
                        <td><?php echo e($audit->finished_at?->format('Y-m-d H:i')); ?></td>
                        <td class="text-end">
                            <a href="<?php echo e(route('audits.show', $audit)); ?>" class="btn btn-sm btn-outline-dark">Abrir</a>
                        </td>
                    </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </tbody>
        </table>
    </div>
    <?php echo e($audits->links()); ?>

<?php endif; ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\Monografia UnISCED\owasp-audit-mz\resources\views/audits/index.blade.php ENDPATH**/ ?>