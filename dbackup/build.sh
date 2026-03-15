#!/usr/bin/env bash

# simple build script for dbackup
# compiles three platform binaries in one shot.

set -euo pipefail

pkg=./
out=./build

mkdir -p "$out"

echo "Building linux/amd64..."
GOOS=linux GOARCH=amd64 go build -o "$out/dbackup-linux-amd64" "$pkg"

echo "Building windows/amd64..."
GOOS=windows GOARCH=amd64 go build -o "$out/dbackup-windows-amd64.exe" "$pkg"

echo "Building darwin/arm64..."
GOOS=darwin GOARCH=arm64 go build -o "$out/dbackup-darwin-arm64" "$pkg"

echo "All builds complete. Artifacts in $out/"