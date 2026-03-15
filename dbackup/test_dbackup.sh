#!/usr/bin/env bash

# simple functional test for the dbackup tool
# creates a temporary source tree, runs the program, and verifies files copied

set -euo pipefail

# ensure we have a linux binary available
./build.sh
bin=build/dbackup-linux-amd64

if [[ ! -x "$bin" ]]; then
    echo "binary not found: $bin" >&2
    exit 1
fi

src=$(mktemp -d)
dst=$(mktemp -d)
trap 'rm -rf "$src" "$dst"' EXIT

# populate source tree with a couple of files and a subdirectory
echo "first file" > "$src/a.txt"
mkdir -p "$src/sub"
echo "second file" > "$src/sub/b.txt"

# run backup
"$bin" -src="$src" -dst="$dst"

# verify the two trees are identical
if ! diff -r "$src" "$dst" >/dev/null; then
    echo "backup failed: contents differ" >&2
    diff -r "$src" "$dst" || true
    exit 1
fi

echo "dbackup functional test passed"