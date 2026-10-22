<!-- Create Fault Modal -->
<div class="modal custom-modal fade" id="createFaultModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="createFaultModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <div class="fault-modal-header-copy">
                    <h5 class="modal-title" id="createFaultModalLabel">
                        <i class="fas fa-tools me-2"></i>Log Fault
                    </h5>
                    <div class="text-muted small mt-1">Capture the affected service, contact details, and first technical update.</div>
                    <div class="fault-modal-meta">
                        <span class="fault-modal-meta-item"><i class="fas fa-circle-plus"></i> New Intake</span>
                        <span class="fault-modal-meta-item"><i class="fas fa-image"></i> Attachment Optional</span>
                        <span class="fault-modal-meta-item"><i class="fas fa-bolt"></i> Quick Capture</span>
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body">
                <form id="UF" action="<?php echo e(route('faults.store')); ?>" method="POST" enctype="multipart/form-data">
                    <?php echo e(csrf_field()); ?>


                    <div class="fault-modal-note mb-3">
                        <i class="fas fa-circle-info"></i>
                        <div>Use this form to log the first customer report, link the affected service, and capture the initial technical notes in one place.</div>
                    </div>

                    <div class="fault-modal-section mb-3">
                        <div class="fault-modal-section-header">
                            <span class="fault-modal-section-icon"><i class="fas fa-network-wired"></i></span>
                            <div>
                                <div class="fault-modal-section-title">Customer & Link</div>
                                <div class="fault-modal-section-subtitle">Select the customer account and the affected service link.</div>
                            </div>
                        </div>
                        <div class="fault-modal-section-body">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label for="customer" class="form-label">Customer Name</label>
                                    <select id="customer" class="form-select select2 customer-select <?php $__errorArgs = ['customer_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" name="customer_id" data-selected="<?php echo e(old('customer_id')); ?>">
                                        <option value=""></option>
                                        <?php $__currentLoopData = $customer; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $customerItem): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <option value="<?php echo e($customerItem->id); ?>" <?php echo e(old('customer_id') == $customerItem->id ? 'selected' : ''); ?>>
                                                <?php echo e($customerItem->customer); ?>

                                            </option>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </select>
                                    <?php $__errorArgs = ['customer_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                        <div class="invalid-feedback d-block"><strong><?php echo e($message); ?></strong></div>
                                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                </div>
                                <div class="col-md-6">
                                    <label for="link" class="form-label">Link</label>
                                    <select id="link" class="form-select select2 link-select <?php $__errorArgs = ['link_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" name="link_id" data-selected="<?php echo e(old('link_id')); ?>">
                                        <option value=""></option>
                                        <?php $__currentLoopData = $link; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $linkItem): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <?php if(old('link_id') == $linkItem->id): ?>
                                                <option value="<?php echo e($linkItem->id); ?>" selected><?php echo e($linkItem->link); ?></option>
                                            <?php endif; ?>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </select>
                                    <?php $__errorArgs = ['link_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                        <div class="invalid-feedback d-block"><strong><?php echo e($message); ?></strong></div>
                                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="fault-modal-section mb-3">
                        <div class="fault-modal-section-header">
                            <span class="fault-modal-section-icon"><i class="fas fa-address-book"></i></span>
                            <div>
                                <div class="fault-modal-section-title">Contact & Address</div>
                                <div class="fault-modal-section-subtitle">Add the caller details and suspected reason for outage.</div>
                            </div>
                        </div>
                        <div class="fault-modal-section-body">
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <label for="contactName" class="form-label">Contact Name</label>
                                    <input id="contactName" type="text" class="form-control <?php $__errorArgs = ['contactName'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" name="contactName" value="<?php echo e(old('contactName')); ?>" placeholder="Contact Name">
                                    <?php $__errorArgs = ['contactName'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                        <div class="invalid-feedback d-block"><strong><?php echo e($message); ?></strong></div>
                                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                </div>
                                <div class="col-md-4">
                                    <label for="phoneNumber" class="form-label">Phone Number</label>
                                    <input id="phoneNumber" type="tel" class="form-control <?php $__errorArgs = ['phoneNumber'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" name="phoneNumber" value="<?php echo e(old('phoneNumber')); ?>" required pattern="^2637\d{8}$" minlength="12" maxlength="12" inputmode="numeric" title="Phone number must be 12 digits starting with 2637" placeholder="e.g. 263776123456">
                                    <?php $__errorArgs = ['phoneNumber'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                        <div class="invalid-feedback d-block"><strong><?php echo e($message); ?></strong></div>
                                    <?php else: ?>
                                        <div class="invalid-feedback">Phone number must be 12 digits starting with 2637</div>
                                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                </div>
                                <div class="col-md-4">
                                    <label for="contactEmail" class="form-label">Contact Email (optional)</label>
                                    <input id="contactEmail" type="email" class="form-control <?php $__errorArgs = ['contactEmail'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" name="contactEmail" value="<?php echo e(old('contactEmail')); ?>" placeholder="e.g. name@example.com">
                                    <?php $__errorArgs = ['contactEmail'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                        <div class="invalid-feedback d-block"><strong><?php echo e($message); ?></strong></div>
                                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                </div>
                                <div class="col-md-6">
                                    <label for="suspectedRFO" class="form-label">Suspected Reason For Outage</label>
                                    <select id="suspectedRFO" class="form-select select2 <?php $__errorArgs = ['suspectedRfo_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" name="suspectedRfo_id">
                                        <option value=""></option>
                                        <?php $__currentLoopData = $suspectedRFO; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $suspectedRfoItem): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <option value="<?php echo e($suspectedRfoItem->id); ?>" <?php echo e(old('suspectedRfo_id') == $suspectedRfoItem->id ? 'selected' : ''); ?>>
                                                <?php echo e($suspectedRfoItem->RFO); ?>

                                            </option>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </select>
                                    <?php $__errorArgs = ['suspectedRfo_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                        <div class="invalid-feedback d-block"><strong><?php echo e($message); ?></strong></div>
                                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                </div>
                                <div class="col-md-6">
                                    <label for="address" class="form-label">Fault Address</label>
                                    <input id="address" type="text" class="form-control <?php $__errorArgs = ['address'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" name="address" value="<?php echo e(old('address')); ?>" placeholder="Address">
                                    <?php $__errorArgs = ['address'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                        <div class="invalid-feedback d-block"><strong><?php echo e($message); ?></strong></div>
                                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="fault-modal-section">
                        <div class="fault-modal-section-header">
                            <span class="fault-modal-section-icon"><i class="fas fa-clipboard-check"></i></span>
                            <div>
                                <div class="fault-modal-section-title">Remarks & Resolution</div>
                                <div class="fault-modal-section-subtitle">Describe the issue clearly and attach any image evidence if available.</div>
                            </div>
                        </div>
                        <div class="fault-modal-section-body">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label for="switch_name" class="form-label">Switch</label>
                                    <input id="switch_name" type="text" class="form-control <?php $__errorArgs = ['switch_name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" name="switch_name" value="<?php echo e(old('switch_name')); ?>" placeholder="Enter switch name or identifier">
                                    <?php $__errorArgs = ['switch_name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                        <div class="invalid-feedback d-block"><strong><?php echo e($message); ?></strong></div>
                                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                </div>
                                <div class="col-md-6">
                                    <label for="port" class="form-label">Port</label>
                                    <input id="port" type="text" class="form-control <?php $__errorArgs = ['port'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" name="port" value="<?php echo e(old('port')); ?>" placeholder="Enter port number or label">
                                    <?php $__errorArgs = ['port'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                        <div class="invalid-feedback d-block"><strong><?php echo e($message); ?></strong></div>
                                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                </div>
                                <div class="col-md-12">
                                    <label for="remark" class="form-label">Remarks</label>
                                    <textarea id="remark" name="remark" required class="form-control <?php $__errorArgs = ['remark'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" rows="4" placeholder="Enter the current issue and troubleshooting notes"><?php echo e(old('remark')); ?></textarea>
                                    <?php $__errorArgs = ['remark'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                        <div class="invalid-feedback d-block"><strong><?php echo e($message); ?></strong></div>
                                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                    <input type="hidden" name="activity" value="ON LOGGING">
                                </div>
                                <div class="col-md-12">
                                    <label for="attachment" class="form-label">Image attachment (optional)</label>
                                    <div class="impaza-dropzone" data-impaza-dropzone>
                                        <input type="file" class="form-control <?php $__errorArgs = ['attachment'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" id="attachment" name="attachment" accept="image/*">
                                        <div class="dz-inner">
                                            <div class="dz-icon"><i class="fas fa-cloud-upload-alt"></i></div>
                                            <div class="dz-text">
                                                <div class="a">Drag and drop an image here</div>
                                                <div class="b">Or click to browse. JPG/PNG supported. Max size follows server settings.</div>
                                            </div>
                                        </div>
                                    </div>
                                    <?php $__errorArgs = ['attachment'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                        <div class="invalid-feedback d-block"><strong><?php echo e($message); ?></strong></div>
                                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                    <div class="form-text">You can also paste an image directly into the remarks field.</div>
                                    <div class="mt-2" id="attachmentPreviewContainer" style="display:none;">
                                        <img id="attachmentPreview" src="" class="img-thumbnail" style="max-height:200px;">
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="fault-modal-toggle">
                                        <div class="form-check mt-2">
                                        <input class="form-check-input me-1" type="checkbox" id="resolvedOnCall" name="resolved_on_call" value="1" <?php echo e(old('resolved_on_call') ? 'checked' : ''); ?>>
                                        <label class="form-check-label fw-semibold" for="resolvedOnCall">Resolved on call</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>

            <div class="modal-footer fault-modal-footer">
                <button type="button" class="btn btn-outline-secondary btn-sm rounded-pill" data-bs-dismiss="modal">
                    <i class="fas fa-times me-1"></i> Cancel
                </button>
                <button type="submit" form="UF" class="btn btn-primary btn-sm rounded-pill">
                    <i class="fas fa-save me-1"></i> Log Fault
                </button>
            </div>
        </div>
    </div>
</div>

<?php /**PATH /var/www/html/resources/views/faults/create.blade.php ENDPATH**/ ?>