#!/usr/bin/env bash

# quick sanity test for build.sh
# run from repo root

set -euo pipefail

./build.sh

for f in build/dbackup-linux-amd64 build/dbackup-windows-amd64.exe build/dbackup-darwin-arm64; do
    if [[ ! -f "$f" ]]; then
        echo "ERROR: expected $f to exist" >&2
        exit 1
    fi
    echo -n "$f: "
    file "$f"
done

echo "build.sh appears to have produced all three binaries successfully"