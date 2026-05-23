<?php $__env->startSection('title', 'Iniciar sessao'); ?>
<?php $__env->startSection('content'); ?>
<div class="row justify-content-center mt-5">
    <div class="col-md-5 col-lg-4">
        <div class="text-center mb-4">
            <img src="<?php echo e(asset('img/logo.png')); ?>" alt="OWASP-AUDIT-MZ" style="max-width: 280px;">
        </div>
        <div class="card shadow-sm">
            <div class="card-body p-4">
                <h5 class="mb-3">Iniciar sessao</h5>
                <form method="POST" action="<?php echo e(route('login')); ?>">
                    <?php echo csrf_field(); ?>
                    <div class="mb-3">
                        <label class="form-label">Correio electronico</label>
                        <input type="email" name="email" value="<?php echo e(old('email')); ?>" required autofocus
                               class="form-control <?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
                        <?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="invalid-feedback"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Palavra-passe</label>
                        <input type="password" name="password" required class="form-control">
                    </div>
                    <div class="form-check mb-3">
                        <input type="checkbox" name="remember" id="remember" class="form-check-input">
                        <label for="remember" class="form-check-label">Manter sessao iniciada</label>
                    </div>
                    <button class="btn w-100" style="background:#C8102E; color:#fff;">Entrar</button>
                </form>
                <hr>
                <p class="mb-0 text-center">
                    <a href="<?php echo e(route('register')); ?>">Criar conta</a>
                </p>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\Monografia UnISCED\owasp-audit-mz\resources\views/auth/login.blade.php ENDPATH**/ ?>