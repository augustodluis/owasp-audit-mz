<?php $__env->startSection('title', 'Nova auditoria'); ?>
<?php $__env->startSection('content'); ?>
<div class="row justify-content-center">
    <div class="col-lg-7">
        <h2 class="mb-3">Iniciar nova auditoria</h2>
        <div class="card shadow-sm">
            <div class="card-body p-4">
                <form method="POST" action="<?php echo e(route('audits.store')); ?>" id="auditForm">
                    <?php echo csrf_field(); ?>
                    <div class="mb-3">
                        <label class="form-label">URL alvo</label>
                        <input type="url" name="target_url" value="<?php echo e(old('target_url', 'http://localhost:3000')); ?>"
                               required placeholder="http://localhost:3000"
                               class="form-control <?php $__errorArgs = ['target_url'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
                        <?php $__errorArgs = ['target_url'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="invalid-feedback"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        <div class="form-text">
                            Use http://localhost:3000 para auditar o OWASP Juice Shop em Docker.
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Destinatarios do relatorio (opcional)</label>
                        <textarea name="recipients" rows="2" class="form-control <?php $__errorArgs = ['recipients'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                  placeholder="gerente@empresa.mz, manutencao@empresa.mz"><?php echo e(old('recipients')); ?></textarea>
                        <?php $__errorArgs = ['recipients'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="invalid-feedback"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        <div class="form-text">
                            Separados por virgula, ponto-e-virgula ou espaco. Receberao o relatorio PDF por e-mail no fim da auditoria.
                        </div>
                    </div>

                    <div class="form-check mb-4">
                        <input type="checkbox" name="authorised" value="1" id="authorised" required
                               class="form-check-input <?php $__errorArgs = ['authorised'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
                        <label for="authorised" class="form-check-label">
                            Confirmo que tenho autorizacao expressa para auditar este alvo.
                        </label>
                    </div>

                    <button class="btn" style="background:#C8102E; color:#fff;">
                        Iniciar auditoria
                    </button>
                    <a href="<?php echo e(route('dashboard')); ?>" class="btn btn-link">Cancelar</a>
                </form>
            </div>
        </div>
    </div>
</div>

<?php $__env->startPush('scripts'); ?>
<script src="<?php echo e(asset('js/audit.js')); ?>"></script>
<?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\Monografia UnISCED\owasp-audit-mz\resources\views/audits/create.blade.php ENDPATH**/ ?>