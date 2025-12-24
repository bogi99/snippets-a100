#!/usr/bin/php
<?php 

function readFilenamesFromDirectory($directory) {
    $filenames = [];
    
    if (is_dir($directory)) {
        if ($dh = opendir($directory)) {
            while (($file = readdir($dh)) !== false) {
                if ($file != '.' && $file != '..') {
                    $dotPosition = strrpos($file, '.');
                    if ($dotPosition !== false) {
                        $filenames[] = [
                            'name' => substr($file, 0, $dotPosition),
                            'extension' => substr($file, $dotPosition + 1)
                        ];
                    } else {
                        $filenames[] = [
                            'name' => $file,
                            'extension' => ''
                        ];
                    }
                }
            }
            closedir($dh);
        }
    }
    
    return $filenames;
}


function generate_uuid4() {
    $data = random_bytes(16);
    $data[6] = chr((ord($data[6]) & 0x0f) | 0x40); // set version to 0100
    $data[8] = chr((ord($data[8]) & 0x3f) | 0x80); // set bits 6-7 to 10
    return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
}

// for($i = 0; $i < 10; $i++) {
//     echo generate_uuid4() . "\n";
// }

$path = $argv[1] ?? './';

// Ensure trailing slash
if (substr($path, -1) !== '/') {
    $path .= '/';
}

if (!is_dir($path)) {
    echo "Error: '$path' is not a valid directory.\n";
    exit(1);
}

echo "This will rename all files in: $path\n";
echo "Are you sure? (yes/no): ";
flush();
$confirmation = trim(fgets(STDIN));

if (strtolower($confirmation) !== 'yes') {
    echo "Operation cancelled.\n";
    exit(0);
}





foreach (readFilenamesFromDirectory($path) as $file) {
    echo $file['name'] . '.' . $file['extension'] . "\n";

    $cmd = sprintf("mv %s %s", 
        escapeshellarg($path . $file['name'] . '.' . $file['extension']),
        escapeshellarg($path . generate_uuid4() . '.' . $file['extension'])
    );

    echo $cmd . "\n";
    exec($cmd);
}   



