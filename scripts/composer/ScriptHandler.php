<?php

/**
 * @file
 * Contains \WordPressProject\composer\ScriptHandler.
 */

namespace WordPressProject\composer;

use Composer\Script\Event;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;

class ScriptHandler
{

  protected static function getWordPressRoot($project_root)
  {
    return $project_root .  '/web';
  }

  public static function createRequiredFiles(Event $event)
  {
    $fs = new Filesystem();
    $root = static::getWordPressRoot(getcwd());

    $dirs = [
      'wp-content/plugins',
      'wp-content/themes',
      'wp',
      'private/scripts/quicksilver',
    ];

    // Required for unit testing
    foreach ($dirs as $dir) {
      if (!$fs->exists($root . '/'. $dir)) {
        $fs->mkdir($root . '/'. $dir);
        $fs->touch($root . '/'. $dir . '/.gitkeep');
      }
    }

    // Create the files directory with chmod 0777
    if (!$fs->exists($root . '/wp-content/uploads')) {
      $oldmask = umask(0);
      $fs->mkdir($root . '/wp-content/uploads', 0777);
      umask($oldmask);
      $event->getIO()->write("Create a wp-content/uploads directory with chmod 0777");
    }

    static::writeMuPluginStubs($event, $fs, $root);
  }

  /**
   * Download the mu-plugin entrypoint stubs that Composer packages don't ship:
   *
   *   - loader.php             — boots pantheon-systems/pantheon-mu-plugin
   *                              (sourced from pantheon-systems/WordPress)
   *   - bedrock-autoloader.php — boots roots/bedrock-autoloader's Autoloader
   *                              (sourced from roots/bedrock)
   *
   * Both packages provide library code only; the mu-plugin entrypoint that
   * loads them is conventionally committed by hand. We fetch them from
   * upstream so the tree is reproducible from `composer install` alone.
   *
   * Sources are configured under `extra.mu-plugin-stubs` in composer.json.
   * Files are only downloaded when missing — to refresh against upstream,
   * run `composer mu-stubs:update`.
   */
  protected static function writeMuPluginStubs(Event $event, Filesystem $fs, $root)
  {
    static::syncMuPluginStubs($event, $fs, $root, false);
  }

  /**
   * Force a re-download of every mu-plugin stub, replacing local copies.
   * Bound to the `mu-stubs:update` composer script.
   */
  public static function updateMuPluginStubs(Event $event)
  {
    $fs = new Filesystem();
    $root = static::getWordPressRoot(getcwd());
    static::syncMuPluginStubs($event, $fs, $root, true);
  }

  protected static function syncMuPluginStubs(Event $event, Filesystem $fs, $root, $force)
  {
    $stubs = $event->getComposer()->getPackage()->getExtra()['mu-plugin-stubs'] ?? [];
    if (empty($stubs)) {
      return;
    }

    $muPluginsDir = $root . '/wp-content/mu-plugins';
    if (!$fs->exists($muPluginsDir)) {
      $fs->mkdir($muPluginsDir);
    }

    foreach ($stubs as $filename => $url) {
      $path = $muPluginsDir . '/' . $filename;

      if (!$force && $fs->exists($path)) {
        continue;
      }

      $event->getIO()->write(sprintf('  - Downloading mu-plugin stub <info>%s</info> from %s', $filename, $url));

      $contents = @file_get_contents($url);
      if ($contents === false) {
        $error = error_get_last();
        $message = sprintf(
          'Failed to download mu-plugin stub "%s" from %s%s',
          $filename,
          $url,
          isset($error['message']) ? ': ' . $error['message'] : ''
        );
        if ($force || !$fs->exists($path)) {
          throw new \RuntimeException($message);
        }
        $event->getIO()->writeError('<warning>' . $message . ' (keeping existing local copy)</warning>');
        continue;
      }

      $fs->dumpFile($path, $contents);
    }
  }

  // This is called by the QuickSilver deploy hook to convert from
  // a 'lean' repository to a 'fat' repository. This should only be
  // called when using this repository as a custom upstream, and
  // updating it with `terminus composer <site>.<env> update`. This
  // is not used in the GitHub PR workflow.
  public static function prepareForPantheon()
  {
    // Get rid of any .git directories that Composer may have added.
    // n.b. Ideally, there are none of these, as removing them may
    // impair Composer's ability to update them later. However, leaving
    // them in place prevents us from pushing to Pantheon.
    $dirsToDelete = [];
    $finder = new Finder();
    foreach (
      $finder
        ->directories()
        ->in(getcwd())
        ->ignoreDotFiles(false)
        ->ignoreVCS(false)
        ->depth('> 0')
        ->name('.git')
      as $dir) {
      $dirsToDelete[] = $dir;
    }
    $fs = new Filesystem();
    $fs->remove($dirsToDelete);

    // Fix up .gitignore: remove everything above the "::: cut :::" line
    $gitignoreFile = getcwd() . '/.gitignore';
    $gitignoreContents = file_get_contents($gitignoreFile);
    $gitignoreContents = preg_replace('/.*::: cut :::*/s', '', $gitignoreContents);
    file_put_contents($gitignoreFile, $gitignoreContents);
  }
}
