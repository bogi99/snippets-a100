package main

import (
	"flag"
	"fmt"
	"io"
	"os"
	"path/filepath"
	"strings"
)

func backupFile(src, dst string) error {
	sourceFile, err := os.Stat(src)
	if err != nil {
		return err
	}
	if !sourceFile.Mode().IsRegular() {
		return fmt.Errorf("%s is not a regular file", src)
	}
	source, err := os.Open(src)
	if err != nil {
		return err
	}
	defer source.Close()

	destination, err := os.Create(dst)
	if err != nil {
		return err
	}

	defer destination.Close()
	_, err = io.Copy(destination, source)
	return err
}

func backupDir(src, dst string) error {
	// obtain information about the source directory
	sourceInfo, err := os.Stat(src)
	if err != nil {
		return err
	}

	err = os.MkdirAll(dst, sourceInfo.Mode())
	if err != nil {
		return err
	}

	// Walk through the source directory and copy files/dirs
	err = filepath.Walk(src, func(path string, info os.FileInfo, err error) error {
		if err != nil {
			return err
		}

		// Skip the source directory itself
		if path == src {
			return nil
		}

		// Calculate the relative path from source
		relPath, err := filepath.Rel(src, path)
		if err != nil {
			return err
		}

		// Determine the destination path
		destPath := filepath.Join(dst, relPath)

		if info.IsDir() {
			// Create directory
			return os.MkdirAll(destPath, info.Mode())
		}

		// Copy file
		return backupFile(path, destPath)
	})

	return err
}

func main() {
	// parse command‑line flags for source and destination
	src := flag.String("src", "", "source path to back up")
	dst := flag.String("dst", "", "destination path for backup")
	flag.Parse()

	if strings.TrimSpace(*src) == "" || strings.TrimSpace(*dst) == "" {
		fmt.Fprintln(os.Stderr, "Usage: dbackup -src=<source> -dst=<destination>")
		os.Exit(1)
	}

	fmt.Printf("Starting backup from %s to %s\n", *src, *dst)

	err := backupDir(*src, *dst)
	if err != nil {
		fmt.Fprintf(os.Stderr, "Backup failed: %v\n", err)
		os.Exit(1)
	}

	fmt.Println("Backup completed successfully!")
}
