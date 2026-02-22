# XSD Test Schemas

This directory contains XML Schema Definition (`.xsd`) files.
- `common.xsd` contains shared schema definitions.
- `v2/*.xsd` contains endpoint-specific schemas for the BoardGameGeek XML API2.

The XSD files were originally used to automatically generate the entity PHP classes.  
They may be used to automatically generate test fixtures in the future.

The validity of the XSD files can be tested with the `validate-xsd.sh` script.    
It validates the XSD files by linting the XML test fixtures recorded from the BGG API (`tests/fixtures`).

## Credit

The XSD files were taken from [tnaskali/bgg-api](https://github.com/tnaskali/bgg-api).
