<?php
// Run the real Breeze installer, then reapply this project's customized source.
// This intentionally restores the GTA-specific fields, views, resource routes and tests.
$root=dirname(__DIR__);
$backup=sys_get_temp_dir().'/tfa8-overlay-'.bin2hex(random_bytes(8));
mkdir($backup,0700,true);
$dirs=['app','resources','routes','tests'];
$files=['composer.json','composer.lock','phpunit.xml','package.json','package-lock.json','vite.config.js','tailwind.config.js','postcss.config.js'];
function cpTree(string $from,string $to): void {
    if (!is_dir($to)) mkdir($to,0755,true);
    foreach (new FilesystemIterator($from) as $file) {
        if ($file->isDir()) cpTree($file->getPathname(),$to.'/'.$file->getFilename());
        else copy($file->getPathname(),$to.'/'.$file->getFilename());
    }
}
function rmTree(string $dir): void {
    if (!is_dir($dir)) return;
    foreach (new FilesystemIterator($dir) as $file) $file->isDir() ? rmTree($file->getPathname()) : unlink($file->getPathname());
    rmdir($dir);
}
foreach ($dirs as $dir) cpTree($root.'/'.$dir,$backup.'/'.$dir);
foreach ($files as $file) if (is_file($root.'/'.$file)) copy($root.'/'.$file,$backup.'/'.$file);
chdir($root);
$exit=1;
try { passthru(escapeshellarg(PHP_BINARY).' artisan breeze:install blade --no-interaction',$exit); }
finally {
    foreach ($dirs as $dir) { rmTree($root.'/'.$dir); cpTree($backup.'/'.$dir,$root.'/'.$dir); }
    foreach ($files as $file) {
        if (is_file($backup.'/'.$file)) copy($backup.'/'.$file,$root.'/'.$file);
        else if (is_file($root.'/'.$file)) unlink($root.'/'.$file);
    }
    rmTree($backup);
}
if ($exit!==0) { fwrite(STDERR,"Breeze installation failed; project source restored.\n");exit($exit); }
echo "Breeze installed; TFA8 customizations reapplied.\n";
