<?php

// This file has been auto-generated by the Symfony Dependency Injection Component for internal use.

if (\class_exists(\Container2RW9iej\srcApp_KernelDevDebugContainer::class, false)) {
    // no-op
} elseif (!include __DIR__.'/Container2RW9iej/srcApp_KernelDevDebugContainer.php') {
    touch(__DIR__.'/Container2RW9iej.legacy');

    return;
}

if (!\class_exists(srcApp_KernelDevDebugContainer::class, false)) {
    \class_alias(\Container2RW9iej\srcApp_KernelDevDebugContainer::class, srcApp_KernelDevDebugContainer::class, false);
}

return new \Container2RW9iej\srcApp_KernelDevDebugContainer([
    'container.build_hash' => '2RW9iej',
    'container.build_id' => '3ffd6edd',
    'container.build_time' => 1581102936,
], __DIR__.\DIRECTORY_SEPARATOR.'Container2RW9iej');
