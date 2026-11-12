<?php
// Run AFTER Composer, syntax checks, view compilation and PHPUnit pass.
$root=dirname(__DIR__);
if (!is_file($root.'/vendor/autoload.php')) { fwrite(STDERR,"Missing vendor. Run Composer first.\n"); exit(1); }
if (!class_exists(ZipArchive::class)) { fwrite(STDERR,"Enable PHP's zip extension.\n"); exit(1); }
$dist=$root.'/dist'; if (!is_dir($dist)) mkdir($dist,0755,true);
$stage=$dist.'/htdocs';
function eraseTree(string $path): void {
    if (!is_dir($path)) return;
    foreach (new FilesystemIterator($path) as $item) { $item->isDir() ? eraseTree($item->getPathname()) : unlink($item->getPathname()); }
    rmdir($path);
}
eraseTree($stage);mkdir($stage,0755,true);
function copyTree(string $source,string $target): void {
    if (!is_dir($target)) mkdir($target,0755,true);
    foreach (new FilesystemIterator($source) as $file) {
        if ($file->isDir()) copyTree($file->getPathname(),$target.'/'.$file->getFilename());
        else copy($file->getPathname(),$target.'/'.$file->getFilename());
    }
}
copyTree($root.'/public',$stage);
$app=$stage.'/_app';mkdir($app,0755,true);
foreach (['app','bootstrap','config','database','lang','resources','routes','storage','vendor'] as $dir) copyTree($root.'/'.$dir,$app.'/'.$dir);
foreach (['artisan','composer.json','composer.lock'] as $file) if (is_file($root.'/'.$file)) copy($root.'/'.$file,$app.'/'.$file);
// Generated machine paths, sessions and logs must never be shipped.
foreach (['bootstrap/cache','storage/framework/views','storage/framework/sessions','storage/framework/cache/data','storage/logs'] as $dir) {
    eraseTree($app.'/'.$dir);mkdir($app.'/'.$dir,0755,true);file_put_contents($app.'/'.$dir.'/.gitignore',"*\n!.gitignore\n");
}
$env=is_file($root.'/.env') ? file_get_contents($root.'/.env') : file_get_contents($root.'/.env.example');
$env=preg_replace('/^APP_KEY=.*$/m','APP_KEY=base64:'.base64_encode(random_bytes(32)),$env);
$env=preg_replace('/^APP_ENV=.*$/m','APP_ENV=production',$env);
$env=preg_replace('/^APP_DEBUG=.*$/m','APP_DEBUG=false',$env);
file_put_contents($app.'/.env',$env);
file_put_contents($app.'/.htaccess',"<IfModule mod_authz_core.c>\nRequire all denied\n</IfModule>\n<IfModule !mod_authz_core.c>\nDeny from all\n</IfModule>\n");
$archive=$dist.'/TFA8-InfinityFree-Upload.zip';
$zip=new ZipArchive();
if ($zip->open($archive,ZipArchive::CREATE|ZipArchive::OVERWRITE)!==true) throw new RuntimeException('Cannot create ZIP.');
$files=new RecursiveIteratorIterator(new RecursiveDirectoryIterator($stage,FilesystemIterator::SKIP_DOTS),RecursiveIteratorIterator::SELF_FIRST);
foreach ($files as $file) {
    $relative='htdocs/'.substr($file->getPathname(),strlen($stage)+1);
    if ($file->isDir()) $zip->addEmptyDir($relative); else $zip->addFile($file->getPathname(),$relative);
}
$zip->addFile($root.'/README.md','START-HERE.md');
$zip->addFile($root.'/database/pos.sql','database-fresh.sql');
$zip->addFile($root.'/database/upgrade_tfa7.sql','database-upgrade-tfa7.sql');
$zip->close();
echo "Built: ".$archive.PHP_EOL;
