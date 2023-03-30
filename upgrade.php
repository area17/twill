<?php

echo 'Staring Twill 2 to Twill 3 upgrade' . PHP_EOL;

$arrayRename = [
    'routes/admin.php' => 'routes/twill.php',
    'resources/views/admin' => 'resources/views/twill',
    'app/Http/Controllers/Admin' => 'app/Http/Controllers/Twill',
    'app/Http/Requests/Admin' => 'app/Http/Requests/Twill',
];

$arrayRenameInFile = [
    'config/twill-navigation.php' => ['admin.', 'twill.'],
    'config/twill.php' => ['admin.', 'twill.'],
    'routes/twill.php' => ['Route::module', 'TwillRoutes::module'],
    'routes/twill.php+1' => ['Route::singleton', 'TwillRoutes::singleton'],
];

foreach ($arrayRename as $original => $new) {
    if (file_exists(getcwd() . '/' . $original)) {
        echo "Copying $original to $new" . PHP_EOL;
        rename(getcwd() . '/' . $original, getcwd() . '/' . $new);
    }
}

foreach ($arrayRenameInFile as $file => $replace) {
    $file = explode('+', $file)[0];
    if (file_exists(getcwd() . '/' . $file)) {
        echo "Replacing legacy routes in $file" . PHP_EOL;
        $contents = file_get_contents(getcwd() . '/' . $file);

        $contents = str_replace($replace[0], $replace[1], $contents);

        file_put_contents(getcwd() . '/' . $file, $contents);
    }
}

echo "Replacing admin. with twill. in views:" . PHP_EOL;
// Loop over all views and replace admin. with twill.
$directories = new RecursiveDirectoryIterator(getcwd() . '/resources/views');
foreach (new RecursiveIteratorIterator($directories) as $filename => $file) {
    if (!str_ends_with($filename, '.')) {
        $contents = file_get_contents($filename);
        if (str_contains($contents, 'admin.')) {
            echo '--' . $filename . PHP_EOL;
            $contents = str_replace('admin.', 'twill.', $contents);
            file_put_contents($filename, $contents);
        }
    }
}

echo "Dumping composer autoloader" . PHP_EOL;

shell_exec('composer dump');

echo "Installing rector as dev dependency:" . PHP_EOL . PHP_EOL;
shell_exec("composer require rector/rector:^0.14 --dev");

echo "Running rector upgrade:";
shell_exec("./vendor/bin/rector process --clear-cache --config=vendor/area17/twill/rector-upgrade-compatibility.php");
shell_exec("./vendor/bin/rector process --clear-cache --config=vendor/area17/twill/rector-upgrade-routes-views.php");
shell_exec("./vendor/bin/rector process --clear-cache --config=vendor/area17/twill/rector-upgrade-twill-config.php");
echo PHP_EOL;

exit(0);
