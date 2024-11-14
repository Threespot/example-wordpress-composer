<?php

// Define the directory to search in and an array of replacements
$directory = dirname(__DIR__, 2);
$replacements = [
    'fixme-app-name' => 'lfc-wp', // ex: app-name
    'fixme-project-name' => 'Lever For Change', // ex: Client Name
    'fixme-domain' => 'www.leverforchange.org' // ex: www.clientdomain.org    (do not include the protocal)
    // Add more pairs as needed
];

$exclusions = [
  'scripts/composer/ReplaceFixme.php', // Relative path from project root
  '.env.example',
  '.lando.example.yml'
];

$excludedDirectories = [
  ".git",
  ".vscode",
  "scripts",
  "web/wp-content",
  "tests"
];

// Function to recursively get all files in a directory, skipping excluded directories
function getFiles($dir, $excludedDirectories) {
  $files = [];
  $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dir));
  foreach ($iterator as $file) {
      $filePath = $file->getPathname();

      // Check if file is in an excluded directory
      foreach ($excludedDirectories as $excludedDir) {
          $excludedPath = dirname(__DIR__, 2) . '/' . $excludedDir;
          if (strpos($filePath, $excludedPath) === 0) {
              continue 2; // Skip files in this directory
          }
      }

      if ($file->isFile()) {
          $files[] = $filePath;
      }
  }
  return $files;
}

// Function to perform replacements
function replaceTextInFiles($files, $replacements, $exclusions) {
    foreach ($files as $file) {
        // Convert file path to a format relative to the project root for comparison
        $relativePath = str_replace(dirname(__DIR__, 2) . '/', '', $file);

        // Skip files listed in exclusions
        if (in_array($relativePath, $exclusions)) {
            continue;
        }
        $content = file_get_contents($file);
        $updatedContent = str_replace(array_keys($replacements), array_values($replacements), $content);
        
        // Only write if changes were made
        if ($content !== $updatedContent) {
            file_put_contents($file, $updatedContent);
            echo "Updated: $file\n";
        }
    }
}

// Get all files and perform replacements 
$files = getFiles($directory, $excludedDirectories);
echo implode("\n", $files) . "\n";
replaceTextInFiles($files, $replacements, $exclusions);

echo "Replacement complete.\n";