<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag; ?>
<?php foreach($attributes->onlyProps([
    'sticky' => false,
]) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
} ?>
<?php $attributes = $attributes->exceptProps([
    'sticky' => false,
]); ?>
<?php foreach (array_filter(([
    'sticky' => false,
]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
} ?>
<?php $__defined_vars = get_defined_vars(); ?>
<?php foreach ($attributes as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
} ?>
<?php unset($__defined_vars); ?>
<div <?php echo e($attributes->merge(['class' => 'impaza-filter-bar' . ($sticky ? ' is-sticky' : '')])); ?>>
    <?php echo e($slot); ?>

    <?php if(isset($actions)): ?>
        <div class="filter-actions"><?php echo e($actions); ?></div>
    <?php endif; ?>
</div>
<?php /**PATH /var/www/html/resources/views/components/filter-bar.blade.php ENDPATH**/ ?>