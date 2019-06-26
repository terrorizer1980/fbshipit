
  /**
   * Like ShipItSubmoduleFilter, but produces a plain file instead of a
   * real submodule.
   */
   public static function makeSubmoduleDiff(
    ?string $old_rev,
    ?string $new_rev,
    if ($old_rev === null && $new_rev !== null) {
      return Str\format(
        'new file mode 100644
--- /dev/null
+++ b/%s
@@ -0,0 +1 @@
+Subproject commit %s
',
        $path,
        $new_rev,
      );
    } else if ($new_rev === null && $old_rev !== null) {
      return Str\format(
        'deleted file mode 100644
--- a/%s
+++ /dev/null
@@ -1 +0,0 @@
-Subproject commit %s
',
        $path,
        $old_rev,
      );
    } else {
      return Str\format(
        '--- a/%s
+++ b/%s
@@ -1 +1 @@
-Subproject commit %s
+Subproject commit %s
',
        $path,
        $path,
        $old_rev ?? '',
        $new_rev ?? '',
      );
    }
      $new_rev = null;
      $old_rev = null;
      foreach (\explode("\n", $body) as $line) {
          $new_rev ?? 'null',
          $old_rev ?? 'null',