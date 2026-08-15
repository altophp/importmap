# Alto Importmap

Alto Importmap builds, loads, combines, renders, and resolves JavaScript import maps in PHP. It supports top-level imports, scopes, integrity metadata, null mappings, and module preloads.

## Introduction

- [Installation](installation.md) installs the package and lists its requirements.
- [Getting started](getting-started.md) builds and renders a first map.

## Import maps

- [Maps](maps.md) covers entries, scopes, integrity metadata, JSON, and merging.
- [Rendering](rendering.md) generates safe import-map and module-preload tags.
- [Resolution](resolution.md) resolves exact, prefix, scoped, and URL-like specifiers.

The package represents import maps and returns HTML or resolution data. It does not download JavaScript modules or write configuration files.
