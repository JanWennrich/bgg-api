#!/usr/bin/env bash
# Validates the XSD files by linting the fixtures recorded from the BGG API - there should be no errors
for dir in ../fixtures/*; do
  name="$(basename "$dir")"
  schema="v2/${name}.xsd"
  echo $dir
  if [[ -f "$schema" ]]; then
    echo "Validating $dir with $schema"
    xmllint --noout --schema "$schema" "$dir"/*.xml
  else
    echo "Skipping $dir (no schema: $schema)"
  fi
done
