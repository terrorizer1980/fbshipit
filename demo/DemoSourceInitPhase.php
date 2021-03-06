<?hh // strict
/**
 * Copyright (c) Facebook, Inc. and its affiliates.
 *
 * This source code is licensed under the MIT license found in the
 * LICENSE file in the root directory of this source tree.
 */

namespace Facebook\ShipIt;

final class DemoSourceRepoInitPhase extends ShipItPhase {
  private bool $allowNuke = false;
  private string $name = "fbshipit-demo";

  <<__Override>>
  public function isProjectSpecific(): bool {
    return false;
  }

  <<__Override>>
  public function getReadableName(): string {
    return 'Initialize source '.$this->name.' repository';
  }

  <<__Override>>
  public function getCLIArguments(): vec<ShipItCLIArgument> {
    return vec[
      shape(
        'long_name' => 'skip-source-init',
        'description' => "Don't initialize the repository",
        'write' => $_ ==> $this->skip(),
      ),
    ];
  }

  <<__Override>>
  public function runImpl(ShipItBaseConfig $config): void {
    $local_path = $config->getSourcePath();

    /* HH_FIXME[2049] __PHPStdLib */
    /* HH_FIXME[4107] __PHPStdLib */
    if (\is_dir($local_path)) {
      return;
    }

    $command = vec['git', 'clone', 'https://github.com/facebook/fbshipit.git'];
    /* HH_FIXME[2049] __PHPStdLib */
    /* HH_FIXME[4107] __PHPStdLib */
    $local_parent_path = \dirname($local_path);
    /* HH_FIXME[2049] __PHPStdLib */
    /* HH_FIXME[4107] __PHPStdLib */
    if (!\is_dir($local_parent_path)) {
      /* HH_FIXME[2049] __PHPStdLib */
      /* HH_FIXME[4107] __PHPStdLib */
      \mkdir($local_parent_path, 0755, /* recursive = */ true);
    }
    // Make sure that "remove stale temp file" jobs don't clean this up
    /* HH_FIXME[2049] __PHPStdLib */
    /* HH_FIXME[4107] __PHPStdLib */
    \touch($local_parent_path);

    (new ShipItShellCommand($local_parent_path, ...$command))
      ->setRetries(2)
      ->setFailureHandler(
        $_ ==> (
          new ShipItShellCommand($local_parent_path, 'rm', '-rf', $local_path)
        )->runSynchronously(),
      )
      ->runSynchronously();
  }

  public static function isMonorepo(string $_name): bool {
    return true;
  }
}
