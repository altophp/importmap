# Errors

Package exceptions implement `ExceptionInterface`. Catch a precise exception
when the application can recover, or catch the interface at a request, job, or
command boundary.

| Failure | Cause | Recovery |
| --- | --- | --- |
| `InvalidEntryException` | Empty specifier, or a prefix specifier whose non-null address does not end in `/` | Supply a non-empty key and keep both sides of prefix mappings slash-terminated |
| `SpecifierNotFoundException` | No scoped, top-level, or URL-like match exists | Check spelling and add the intended mapping |
| `ResolutionFailedException` | An exact mapping deliberately contains `null` | Remove or replace the block only when the application should permit that dependency |
| `JsonException` | `fromJson()` receives invalid JSON, or output contains bytes JSON cannot encode | Correct the input and preserve valid UTF-8 data |
| `RuntimeException` | `fromFile()` cannot find or read its path | Check the application-authorized path and permissions |

`SpecifierNotFoundException` extends `ResolutionFailedException`, so catching
the parent handles both missing and blocked imports. When scoped resolution is
unexpected, verify the referencing URL: the longest matching scope takes
priority, then resolution falls back through shorter scopes and top-level
imports.

The package does not verify that a resolved address exists or that a browser
can fetch it. For browser failures, inspect the rendered map, document base URL,
network response, and content security policy in the consuming application.
