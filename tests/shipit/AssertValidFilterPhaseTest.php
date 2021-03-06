<?hh // strict
/**
 * Copyright (c) Facebook, Inc. and its affiliates.
 *
 * This source code is licensed under the MIT license found in the
 * LICENSE file in the root directory of this source tree.
 */

/**
 * This file was moved from fbsource to www. View old history in diffusion:
 * https://fburl.com/beb4xz2n
 */
namespace Facebook\ShipIt;

use namespace HH\Lib\{Str, Vec};
use type Facebook\HackTest\DataProvider; // @oss-enable
// @oss-disable: use type DataProvider;


<<\Oncalls('open_source')>>
final class AssertValidFilterPhaseTest extends BaseTest {
  public function testAllowsValidCombination(): void {
    $phase = new ShipItAssertValidFilterPhase(
      $changeset ==> $changeset->withDiffs(
        Vec\filter(
          $changeset->getDiffs(),
          $diff ==> Str\slice($diff['path'], 0, 4) === 'foo/',
        ),
      ),
    );
    $phase->assertValid(keyset['foo/']);
    // no exception thrown :)
  }

  public static function exampleEmptyRoots(
  ): dict<string, vec<keyset<string>>> {
    return dict[
      'empty set' => vec[keyset[]],
      'empty string' => vec[keyset['']],
      '.' => vec[keyset['.']],
      './' => vec[keyset['./']],
    ];
  }

  <<DataProvider('exampleEmptyRoots')>>
  public function testAllowsIdentityFunctionForEmptyRoots(
    keyset<string> $roots,
  ): void {
    $phase = new ShipItAssertValidFilterPhase($changeset ==> $changeset);
    $phase->assertValid($roots);
    // no exception thrown :)
  }

  public function testThrowsForIdentityFunctionWithRoots(): void {
    \expect(() ==> {
      $phase = new ShipItAssertValidFilterPhase(
        $changeset ==> $changeset, // stuff outside of 'foo' should be removed
      );
      $phase->assertValid(keyset['foo/']);
    })
      // @oss-disable: ->toThrow(\InvariantViolationException::class);
    ->toThrow(InvariantException::class); // @oss-enable
  }

  public function testThrowsForEmptyChangeset(): void {
    \expect(() ==> {
      $phase = new ShipItAssertValidFilterPhase(
        $_changeset ==> (new ShipItChangeset()),
      );
      $phase->assertValid(keyset['foo/']);
    })
      // @oss-disable: ->toThrow(\InvariantViolationException::class);
    ->toThrow(InvariantException::class); // @oss-enable
  }

  public function testThrowsForPartialMatch(): void {
    \expect(() ==> {
      $phase = new ShipItAssertValidFilterPhase(
        $changeset ==> $changeset->withDiffs(
          Vec\filter(
            $changeset->getDiffs(),
            $diff ==> Str\slice($diff['path'], 0, 3) === 'foo',
          ),
        ),
      );
      $phase->assertValid(keyset['foo/', 'herp/']);
    })
      // @oss-disable: ->toThrow(\InvariantViolationException::class);
    ->toThrow(InvariantException::class); // @oss-enable
  }

  public function testAllowsForStrippedPaths(): void {
    $phase = new ShipItAssertValidFilterPhase(
      $changeset ==> $changeset->withDiffs(
        Vec\filter(
          $changeset->getDiffs(),
          $diff ==> Str\slice($diff['path'], 0, 4) === 'foo/',
        ),
      ),
      vec['@bar@'],
    );
    $phase->assertValid(keyset['foo/', 'bar/']);
  }
}
