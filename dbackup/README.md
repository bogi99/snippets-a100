# dbackup

A small, simple **recursive backup utility** written in Go.

It copies a source directory tree into a destination directory, preserving the file structure and file contents.

---

## ✅ Features

- Recursively copies directories and files
- Preserves directory modes/permissions
- Works cross-platform (Linux, macOS, Windows)
- Includes convenient build and validation scripts

---

## 🚀 Quick Start

### Build (all platforms)

From the `dbackup/` directory:

```sh
./build.sh
```

Binaries are produced under `build/`:

- `build/dbackup-linux-amd64`
- `build/dbackup-windows-amd64.exe`
- `build/dbackup-darwin-arm64`

### Run

```sh
./build/dbackup-linux-amd64 -src=/path/to/source -dst=/path/to/destination
```

Example:

```sh
./build/dbackup-linux-amd64 -src="${HOME}/my-project" -dst="${HOME}/backups/my-project"
```

---

## 🧪 Tests

### Build sanity check

```sh
./test_build.sh
```

### Functional test

```sh
./test_dbackup.sh
```

---

## 🛠️ How it works

The tool walks the source directory tree and:

1. Creates matching directories under the destination
2. Copies each regular file from source to destination

It is intentionally small and dependency-free, making it easy to inspect, modify, and extend.

---

## 🧩 Notes

- If the destination directory already exists, files will be overwritten.
- Symlinks and non-regular files (devices, pipes, etc.) are not currently handled.

---

## 📄 Related

See the repository root `README.md` for an overview of this workspace and other projects.
