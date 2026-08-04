<?php $__currentLoopData = $audits; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $audit): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
  <tr>
    <td class="text-nowrap">
      <?php echo e(\Illuminate\Support\Carbon::parse($audit->created_at)->format('d M Y, H:i')); ?>

    </td>
    <td>
      <div class="text-muted small"><?php echo e($audit->notes); ?></div>
    </td>
  </tr>
<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

<?php /**PATH /var/www/html/resources/views/users/login_history_rows.blade.php ENDPATH**/ ?>