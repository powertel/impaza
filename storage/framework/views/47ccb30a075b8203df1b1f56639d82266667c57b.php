<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag; ?>
<?php foreach($attributes->onlyProps([
    'title' => '',
    'value' => '',
    'icon' => 'fa-chart-bar',
    'variant' => 'primary',      // primary | success | warning | danger | info | muted
    'href' => null,
    'sub' => null,               // descriptive sub-text (shown when no trend given)
    'trend' => null,             // signed integer percent, or null to hide
    'trendLabel' => 'vs last month',
]) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
} ?>
<?php $attributes = $attributes->exceptProps([
    'title' => '',
    'value' => '',
    'icon' => 'fa-chart-bar',
    'variant' => 'primary',      // primary | success | warning | danger | info | muted
    'href' => null,
    'sub' => null,               // descriptive sub-text (shown when no trend given)
    'trend' => null,             // signed integer percent, or null to hide
    'trendLabel' => 'vs last month',
]); ?>
<?php foreach (array_filter(([
    'title' => '',
    'value' => '',
    'icon' => 'fa-chart-bar',
    'variant' => 'primary',      // primary | success | warning | danger | info | muted
    'href' => null,
    'sub' => null,               // descriptive sub-text (shown when no trend given)
    'trend' => null,             // signed integer percent, or null to hide
    'trendLabel' => 'vs last month',
]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
} ?>
<?php $__defined_vars = get_defined_vars(); ?>
<?php foreach ($attributes as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
} ?>
<?php unset($__defined_vars); ?>
<?php
    $variants = [
        'primary' => 'var(--impaza-primary)',
        'success' => 'var(--impaza-success)',
        'warning' => 'var(--impaza-warning)',
        'danger'  => 'var(--impaza-danger)',
        'info'    => 'var(--impaza-info)',
        'muted'   => 'var(--impaza-muted)',
    ];
    $accent = $variants[$variant] ?? $variants['primary'];
    $hasTrend = $trend !== null && $trend !== '';
    $trendDir = $hasTrend ? ((int) $trend > 0 ? 'up' : ((int) $trend < 0 ? 'down' : 'flat')) : 'flat';
    $trendIcon = $trendDir === 'up' ? 'fa-arrow-trend-up' : ($trendDir === 'down' ? 'fa-arrow-trend-down' : 'fa-minus');
    $innerAttributes = $attributes->merge(['class' => 'impaza-stat']);
    $hasSpark = trim((string) $slot) !== '';
?>
<?php if($href): ?>
    <a href="<?php echo e($href); ?>" <?php echo e($innerAttributes); ?> style="--impaza-stat-accent: <?php echo e($accent); ?>;">
<?php else: ?>
    <div <?php echo e($innerAttributes); ?> style="--impaza-stat-accent: <?php echo e($accent); ?>;">
<?php endif; ?>
        <div class="impaza-stat-head">
            <span class="impaza-stat-icon"><i class="fas <?php echo e($icon); ?>"></i></span>
            <span class="impaza-stat-title"><?php echo e($title); ?></span>
        </div>
        <div class="impaza-stat-body">
            <div class="impaza-stat-metric">
                <div class="impaza-stat-value"><?php echo e($value); ?></div>
                <?php if($hasTrend): ?>
                    <div class="impaza-stat-subline">
                        <span class="impaza-stat-trend impaza-stat-trend--<?php echo e($trendDir); ?>">
                            <i class="fas <?php echo e($trendIcon); ?>"></i><?php echo e((int) $trend > 0 ? '+' : ''); ?><?php echo e((int) $trend); ?>%
                        </span>
                        <span class="impaza-stat-sublabel"><?php echo e($trendLabel); ?></span>
                    </div>
                <?php elseif($sub !== null): ?>
                    <div class="impaza-stat-sub"><?php echo e($sub); ?></div>
                <?php endif; ?>
            </div>
            <?php if($hasSpark): ?>
                <div class="impaza-stat-spark"><?php echo e($slot); ?></div>
            <?php endif; ?>
        </div>
<?php if($href): ?>
    </a>
<?php else: ?>
    </div>
<?php endif; ?>
<?php /**PATH /var/www/html/resources/views/components/stat-card.blade.php ENDPATH**/ ?>